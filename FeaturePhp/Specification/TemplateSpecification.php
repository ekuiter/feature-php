<?

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

class TemplateSpecification extends FileSpecification {
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        $this->setOptional("rules", array());
        $rules = $this->getWith("rules", "is_array");
        $newRules = array();
        foreach ($rules as $rule)
            $newRules[] = self::getInstance($rule, "\FeaturePhp\Specification\ReplacementRule");
        $this->set("rules", $newRules);
    }

    public function getRules() {
        return $this->get("rules");
    }
}

?>