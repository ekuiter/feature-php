<?

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

class ProductLine {
    private $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
        $this->model = $settings->get("model");
        $this->defaultConfiguration = $settings->get("defaultConfiguration");
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getModel() {
        return $this->model;
    }

    public function getDefaultConfiguration() {
        return $this->defaultConfiguration;
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