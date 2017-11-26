<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class RuntimeGenerator extends Generator {
    private $class;
    private $target;
    private $getter;
    
    public function __construct($settings) {
        parent::__construct($settings);
        $this->class = $settings->getOptional("class", "Runtime");
        $this->target = $settings->getOptional("target", "{$this->class}.php");
        $this->getter = $settings->getOptional("getter", "hasFeature");
    }

    public static function getKey() {
        return "runtime";
    }

    private function encodeFeatureNames($artifacts) {
        $featureNames = array();
        foreach ($artifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $featureNames[] = $featureName;
            $this->logFile->log($artifact, "added runtime information in \"$this->target\"");
        }
        return str_replace("'", "\'", json_encode($featureNames));
    }

    public function _generateFiles() {
        $this->files[] = fphp\File\TemplateFile::fromSpecification(
            fphp\Specification\TemplateSpecification::fromArray(
                array(
                    "source" => "Runtime.php.template",
                    "target" => $this->target,
                    "rules" => array(
                        array("assign" => "class", "to" => $this->class),
                        array("assign" => "getter", "to" => $this->getter),
                        array("assign" => "selectedFeatures", "to" => $this->encodeFeatureNames($this->selectedArtifacts)),
                        array("assign" => "deselectedFeatures", "to" => $this->encodeFeatureNames($this->deselectedArtifacts))
                    )
                ), Settings::inDirectory(__DIR__))
        );
    }
}

?>