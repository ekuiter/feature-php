<?

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

class ProductLine {
    private $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getModel() {
        return $this->settings->get("model");
    }

    public function getName() {
        return $this->settings->get("name");
    }

    public function getDefaultConfiguration() {
        return $this->settings->get("defaultConfiguration");
    }

    public function getArtifact($feature) {
        return $this->settings->get("artifacts", $feature->getName());
    }

    public function getGeneratorSettings($generator) {
        return $this->settings->getOptional("generators", $generator, fphp\Generator\Settings::emptyInstance());
    }

    public function getProduct($configuration = null) {
        if (!$configuration)
            $configuration = $this->defaultConfiguration;
        return new Product($this, $configuration);
    }
}

?>