<?

namespace FeaturePhp\Artifact;

class Artifact {
    private $feature;
    private $settings;
    
    public function __construct($feature, $settings) {
        $this->feature = $feature;
        $this->settings = $settings;
    }

    public function getFeature() {
        return $this->feature;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getGenerators() {
        return $this->settings->get("generators");
    }

    public function getGeneratorSettings($generator) {
        return $this->settings->getOptional("generators", $generator, \FeaturePhp\Generator\Settings::emptyInstance());
    }
}

?>