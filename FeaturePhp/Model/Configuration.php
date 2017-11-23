<?

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

class Configuration {
    private $model;
    private $xmlConfiguration;
    private $selectedFeatures;
    private $deselectedFeatures;

    public function __construct($model, $xmlConfiguration) {
        $this->model = $model;
        $this->xmlConfiguration = $xmlConfiguration;
        $this->selectedFeatures = array();
        $this->deselectedFeatures = array();

        foreach ($xmlConfiguration->getSelectedFeatureNames() as $featureName)
            $this->selectedFeatures[] = $this->model->getFeature($featureName);

        foreach ($this->model->getFeatures() as $feature)
            if (!Feature::has($this->selectedFeatures, $feature))
                $this->deselectedFeatures[] = $feature;
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

    public function getDeselectedFeatures() {
        return $this->deselectedFeatures;
    }

    public function isValid() {
        return $this->model->getConstraintSolver()->isValid($this);
    }
}