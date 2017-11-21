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
        try {
            return $this->settings->get("generators", $generator);
        } catch (\FeaturePhp\NotFoundSettingsException $e) {
            return \FeaturePhp\Generator\Settings::emptyInstance();
        }
    }
}

?>