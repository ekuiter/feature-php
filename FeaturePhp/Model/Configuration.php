<?

/**
 * The FeaturePhp\Model\Configuration class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * A configuration of a software product line.
 * A configuration is a set of selected features of a feature model,
 * implying a set of deselected features as well ({@see Feature} and
 * {@see Model}). (Partial configurations are not needed on the server-side.)
 * A configuration may be valid or invalid, this is determined by a
 * the feature model's {@see ConstraintSolver}.
 * The underlying XML configuration has its own class, {@see XmlConfiguration}.
 */
class Configuration {
    /**
     * @var Model $model the feature model the configuration relates to
     */
    private $model;

    /**
     * @var XmlConfiguration $xmlConfiguration the underlying XML configuration
     */
    private $xmlConfiguration;

    /**
     * @var Feature[] all selected features in the configuration
     */
    private $selectedFeatures;

    /**
     * @var Feature[] all deselected features in the configuration
     */
    private $deselectedFeatures;

    /**
     * Creates a configuration.
     * @param Model $model
     * @param XmlConfiguration $xmlConfiguration
     */
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

    /**
     * Returns the feature model the configuration relates to.
     * @return Model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Returns the underlying XML configuration.
     * @return XmlConfiguration
     */
    public function getXmlConfiguration() {
        return $this->xmlConfiguration;
    }

    /**
     * Returns the configuration's selected features.
     * @return Feature[]
     */
    public function getSelectedFeatures() {
        return $this->selectedFeatures;
    }

    /**
     * Returns the configuration's deselected features.
     * @return Feature[]
     */
    public function getDeselectedFeatures() {
        return $this->deselectedFeatures;
    }

    /**
     * Returns whether the configuration is valid.
     * @return bool
     */
    public function isValid() {
        return $this->model->getConstraintSolver()->isValid($this);
    }

    /**
     * Analyzes the model and configuration by echoing a web page.
     */
    public function renderAnalysis() {
        (new ConfigurationRenderer($this))->render();
    }
}