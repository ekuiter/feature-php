<?

namespace FeaturePhp\ProductLine;

use \FeaturePhp\Model;

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

    public function renderAnalysis($configuration = null) {
        if (!$configuration)
            $configuration = $this->defaultConfiguration;
        (new Model\AnalysisRenderer($configuration))->render();
    }
}

?>