<?

/**
 * The FeaturePhp\Model\ConstraintSolver class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;
use \FeaturePhp\Helper\Logic as Logic;

/**
 * Exception thrown from the ConstraintSolver class.
 */
class ConstraintSolverException extends \Exception {}

/**
 * A solver for feature constraints.
 * A {@see Model} can contain complex relationships between features
 * that a configuration needs to satisfy.
 * These relationships are represented as feature and cross-tree constraints,
 * which in turn are represented as propositional formulas.
 * A constraint solver extracts all constraints from a feature model
 * and uses them to build a solver (i.e. a complex formula, see
 * {@see \FeaturePhp\Helper\Logic}).
 * This formula can then be evaluated for a {@see Configuration} to determine
 * its validity.
 */
class ConstraintSolver {
    /**
     * @var Model $model the feature model the constraint solver relates to
     */
    private $model;

    /**
     * @var callable[] $constraints formulas for all constraints from the feature model
     */
    private $constraints;

    /**
     * @var callable one formula that validates all feature constraints
     */
    private $solver;

    /**
     * Creates a constraint solver.
     * Every feature constraint is translated into a formula using the semantics
     * defined in Chapter 2 of "Feature-Oriented Software Product Lines" (see 
     * {@see http://www.springer.com/de/book/9783642375200}).
     * Every cross-tree constraint is already a formula and only needs to be
     * transformed from an XML representation to a formula (i.e. callable).
     * Finally, all formulas are conjoined to build a solver.
     * @param Model $model
     */
    public function __construct($model) {
        $this->model = $model;
        $this->constraints = array();
        
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

        $this->solver = call_user_func_array("\FeaturePhp\Helper\Logic::_and", $this->constraints);
    }

    /**
     * Returns the feature model the constraint solver relates to.
     * @return Model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Returns formulas for all constraints from the feature model.
     * @return callable[]
     */
    public function getConstraints() {
        return $this->constraints;
    }

    /**
     * Returns one formula that validates all feature constraints.
     * @return callable
     */
    public function getSolver() {
        return $this->solver;
    }

    /**
     * Semantics for the root feature.
     * The root feature is always selected.
     * @param Feature $feature
     * @return callable
     */
    private static function root($feature) {
        if (!$feature->getParent())
            return Logic::is($feature);
    }

    /**
     * Semantics for a mandatory feature.
     * A mandatory feature is selected iff its parent is selected.
     * @param Feature $feature
     * @return callable
     */
    private static function mandatory($feature) {
        if ($feature->getParent() && $feature->getMandatory())
            return Logic::equiv(Logic::is($feature), Logic::is($feature->getParent()));
    }

    /**
     * Semantics for an optional feature.
     * If an optional feature is selected, its parent is selected.
     * @param Feature $feature
     * @return callable
     */
    private static function optional($feature) {
        if ($feature->getParent())
            return Logic::implies(Logic::is($feature), Logic::is($feature->getParent()));
    }

    /**
     * Semantics for a feature that provides an alternative choice.
     * Exactly one child of such a feature is selected.
     * @param Feature $feature
     * @return callable
     */
    private static function alternative($feature) {
        if ($feature->getAlternative()) {
            $children = array();
            foreach ($feature->getChildren() as $child)
                $children[] = Logic::is($child);

            $alternativeConstraints = array();
            for ($i = 0; $i < count($children); $i++)
                for ($j = 0; $j < $i; $j++)
                    $alternativeConstraints[] = Logic::not(Logic::_and($children[$i], $children[$j]));

            return Logic::_and(Logic::equiv(Logic::is($feature),
                                            call_user_func_array("\FeaturePhp\Helper\Logic::_or", $children)),
                               call_user_func_array("\FeaturePhp\Helper\Logic::_and", $alternativeConstraints));
        }
    }

    /**
     * Semantics for a feature that provides a choice using an inclusive or.
     * At least one child of such a feature is selected.
     * @param Feature $feature
     * @return callable
     */
    private static function _or($feature) {
        if ($feature->getOr()) {
            $children = array();
            foreach ($feature->getChildren() as $child)
                $children[] = Logic::is($child);
            return Logic::equiv(Logic::is($feature),
                                call_user_func_array("\FeaturePhp\Helper\Logic::_or", $children));
        }
    }

    /**
     * Transforms a cross-tree constraint from an XML rule to a formula.
     * @param \SimpleXMLElement $rule
     * @return callable
     */
    private function crossTreeConstraint($rule) {
        $op = $rule->getName();
        $num = $rule->count();

        if ($op === "eq" && $num === 2)
            return Logic::equiv($this->crossTreeConstraint($rule->children()[0]),
                                $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "imp" && $num === 2)
            return Logic::implies($this->crossTreeConstraint($rule->children()[0]),
                                  $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "conj" && $num === 2)
            return Logic::_and($this->crossTreeConstraint($rule->children()[0]),
                               $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "disj" && $num === 2)
            return Logic::_or($this->crossTreeConstraint($rule->children()[0]),
                              $this->crossTreeConstraint($rule->children()[1]));
        if ($op === "not" && $num === 1)
            return Logic::not($this->crossTreeConstraint($rule->children()[0]));
        if ($op === "var" && $num === 0)
            return Logic::is($this->model->getFeature((string) $rule));

        throw new ConstraintSolverException("unknown operation $op with $num arguments encountered");
    }

    /**
     * Evaluates the solver for a list of features.
     * @param Feature[] $features
     * @return bool
     */
    private function solve($features) {
        return call_user_func($this->solver, $features);
    }

    /**
     * Returns whether a configuration is valid.
     * This is done by evaluating the solver for the configuration's selected features.
     * @param Configuration $configuration
     * @return bool
     */
    public function isValid($configuration) {
        return $this->solve($configuration->getSelectedFeatures());
    }
}