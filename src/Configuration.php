<?

namespace FeaturePhp;

class ConfigurationException extends \Exception {}

class Configuration {
    private $model;
    private $xmlConfiguration;
    private $selectedFeatures;

    public function __construct($model, $xmlConfiguration) {
        $this->model = $model;
        $this->xmlConfiguration = $xmlConfiguration;
        $this->selectedFeatures = [];

        foreach ($xmlConfiguration->getSelectedFeatureNames() as $featureName) {
            $feature = $this->model->getFeature($featureName);
            if (!$feature)
                throw new ConfigurationException("invalid feature $featureName");
            $this->selectedFeatures[] = $feature;
        }
    }

    public function getModel() {
        return $this->model;
    }

    public function getXmlConfiguration() {
        return $this->xmlConfiguration;
    }

    public function getSelectedFeatures() {
        return $this->selectedFeatures;
    }

    public function isValid() {
        return $this->model->getConstraintSolver()->isValid($this);
    }
}