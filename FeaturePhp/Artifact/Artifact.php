<?

/**
 * The FeaturePhp\Artifact\Artifact class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * An artifact associated with a feature.
 * An artifact is a representation of a {@see \FeaturePhp\Model\Feature} with information
 * on how to generate a product for which the feature is selected or deselected. This
 * information is provided by a {@see Settings} object.
 */
class Artifact {
    /**
     * @var \FeaturePhp\Model\Feature $feature the feature associated with the artifact
     */
    private $feature;
    
    /**
     * @var Settings $settings the settings object with generator settings
     */
    private $settings;

    /**
     * Creates an artifact.
     * @param \FeaturePhp\Model\Feature $feature 
     * @param Settings $settings
     */
    public function __construct($feature, $settings) {
        $this->feature = $feature;
        $this->settings = $settings;
    }

    /**
     * Returns the artifact's feature.
     * @return \FeaturePhp\Model\Feature
     */
    public function getFeature() {
        return $this->feature;
    }

    /**
     * Returns the artifact's settings.
     * @return Settings
     */
    public function getSettings() {
        return $this->settings;
    }

    /**
     * Returns the artifact's settings for all generators.
     * @return \FeaturePhp\Generator\Settings[]
     */
    public function getGenerators() {
        return $this->settings->get("generators");
    }

    /**
     * Returns the artifact's settings for a given generator.
     * Returns empty settings if no settings are specified for the generator.
     * @param \FeaturePhp\Generator\Generator $generator
     * @return \FeaturePhp\Generator\Settings
     */
    public function getGeneratorSettings($generator) {
        return $this->settings->getOptional("generators", $generator, fphp\Generator\Settings::emptyInstance());
    }
}

?>