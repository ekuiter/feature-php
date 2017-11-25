<?

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

class FileSpecificationException extends \Exception {}

class FileSpecification extends Specification {
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        $this->setOptional("rules", array());
        $rules = $this->getWith("rules", "is_array");
        $newRules = array();
        foreach ($rules as $rule)
            $newRules[] = self::getInstance($rule, "\FeaturePhp\Specification\ReplacementRule");
        $this->set("rules", $newRules);
    }

    public static function fromArray($cfg, $settings) {
        $fileSpecification = new self($cfg, $settings->getDirectory());
        $fileSpecification->set("source", $settings->getPath($fileSpecification->getSource()));
        $fileSpecification->set("target", fphp\Helper\Path::join(
            $settings->getOptional("target", null), $fileSpecification->getTarget()));

        if (!file_exists($fileSpecification->getSource()))
            throw new FileSpecificationException("file \"{$fileSpecification->getSource()}\" does not exist");

        return $fileSpecification;
    }

    public function getRules() {
        return $this->get("rules");
    }
}

?>