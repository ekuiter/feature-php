<?

namespace FeaturePhp;

class ConstraintSolverException extends \Exception {}

class ConstraintSolver {
    private $model;
    private $constraints;
    private $solver;
    
    public function __construct($model) {
        $this->model = $model;
        $this->constraints = [];
        
        $featureConstraintSemantics = array(
            array("self", "root"), array("self", "mandatory"), array("self", "optional"),
            array("self", "alternative"), array("self", "_or")
        );

        foreach ($model->getFeatures() as $feature)
            foreach ($featureConstraintSemantics as $semantics) {
                $constraint = call_user_func($semantics, $feature);
                if ($constraint)
                    $this->constraints[] = $constraint;
            }

        foreach ($model->getXmlModel()->getRules() as $rule)
            $this->constraints[] = $this->crossTreeConstraint($rule);

        $this->solver = call_user_func_array(__NAMESPACE__ . "\Logic::_and", $this->constraints);
    }

    public function getModel() {
        return $this->model;
    }

    public function getConstraints() {
        return $this->constraints;
    }

    public function getSolver() {
        return $this->solver;
    }

    private static function root($feature) {
        if (!$feature->getParent())
            return Logic::is($feature);
    }

    private static function mandatory($feature) {
        if ($feature->getParent() && $feature->getMandatory())
            return Logic::equiv(Logic::is($feature), Logic::is($feature->getParent()));
    }

    private static function optional($feature) {
        if ($feature->getParent())
            return Logic::implies(Logic::is($feature), Logic::is($feature->getParent()));
    }

    private static function alternative($feature) {
        if ($feature->getAlternative()) {
            $children = [];
            foreach ($feature->getChildren() as $child)
                $children[] = Logic::is($child);

            $alternativeConstraints = [];
            for ($i = 0; $i < count($children); $i++)
                for ($j = 0; $j < $i; $j++)
                    $alternativeConstraints[] = Logic::not(Logic::_and($children[$i], $children[$j]));

            return Logic::_and(Logic::equiv(Logic::is($feature), call_user_func_array(__NAMESPACE__ . "\Logic::_or", $children)),
                               call_user_func_array(__NAMESPACE__ . "\Logic::_and", $alternativeConstraints));
        }
    }

    private static function _or($feature) {
        if ($feature->getOr()) {
            $children = [];
            foreach ($feature->getChildren() as $child)
                $children[] = Logic::is($child);
            return Logic::equiv(Logic::is($feature), call_user_func_array(__NAMESPACE__ . "\Logic::_or", $children));
        }
    }

    private function crossTreeConstraint($rule) {
        $op = $rule->getName();
        $num = $rule->count();

        if ($op === "imp" && $num === 2)
            return Logic::implies($this->crossTreeConstraint($rule->children()[0]), $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "conj" && $num === 2)
            return Logic::_and($this->crossTreeConstraint($rule->children()[0]), $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "disj" && $num === 2)
            return Logic::_or($this->crossTreeConstraint($rule->children()[0]), $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "not" && $num === 1)
            return Logic::not($this->crossTreeConstraint($rule->children()[0]));
        if ($op === "var" && $num === 0)
            return Logic::is($this->model->getFeature((string) $rule));

        throw new ConstraintSolverException("unknown operation $op with $num arguments encountered");
    }

    private function solve($features) {
        return call_user_func($this->solver, $features);
    }

    public function isValid($configuration) {
        return $this->solve($configuration->getSelectedFeatures());
    }
}