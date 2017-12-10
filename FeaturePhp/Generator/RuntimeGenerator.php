<?php

/**
 * The FeaturePhp\Generator\RuntimeGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates a file with runtime information.
 * An artifact can register a runtime generator to provide information about
 * its corresponding feature (selected or deselected) at runtime. If used,
 * a suitable PHP class is generated according to the product line's generator
 * settings (see {@see Settings}). (Only PHP is supported as of now.)
 */
class RuntimeGenerator extends Generator {
    /**
     * @var string $class the runtime class name, defaults to "Runtime"
     */
    private $class;

    /**
     * @var string $class the runtime class file target in the generated product,
     * defaults to "Runtime.php"
     */
    private $target;

    /**
     * @var string $class the runtime class method for getting feature information,
     * default to "hasFeature"
     */
    private $getter;

    /**
     * @var string|null $feature a feature that has to be selected to generate the runtime class
     */
    private $feature;

    /**
     * Creates a runtime generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
        $this->class = $settings->getOptional("class", "Runtime");
        $this->target = $settings->getOptional("target", "{$this->class}.php");
        $this->getter = $settings->getOptional("getter", "hasFeature");
        $this->feature = $settings->getOptional("feature", null);
    }

    /**
     * Returns the runtime generator's key.
     * @return string
     */
    public static function getKey() {
        return "runtime";
    }

    /**
     * Returns the JSON-encoded names of the corresponding features of some artifacts.
     * The JSON is assumed to then be enclosed in single quotes (').
     * @param \FeaturePhp\Artifact\Artifact[]
     * @return string
     */
    private function encodeFeatureNames($artifacts) {
        $featureNames = array();
        foreach ($artifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $featureNames[] = $featureName;
            $this->logFile->log($artifact, "added runtime information in \"$this->target\"");
        }
        return str_replace("'", "\'", json_encode($featureNames));
    }

    /**
     * Generates the runtime file.
     * Internally, this uses a template file and assigns the given variables.
     * You can override this to add runtime information for other languages.
     * If a feature was supplied, only generates if that feature is selected.
     */
    public function _generateFiles() {
        if ($this->feature && !$this->isSelectedFeature($this->feature)) {
            $this->logFile->log(null, "did not add runtime information because \"$this->feature\" is not selected");
            return;
        }
        
        $this->files[] = fphp\File\TemplateFile::fromSpecification(
            fphp\Specification\TemplateSpecification::fromArrayAndSettings(
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