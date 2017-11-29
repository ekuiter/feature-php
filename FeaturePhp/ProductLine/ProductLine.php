<?

/**
 * The FeaturePhp\ProductLine\ProductLine class.
 */

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

/**
 * A feature-oriented software product line.
 * A product line consists of a feature model (see {@see \FeaturePhp\Model\Model}),
 * a set of artifacts (see {@see \FeaturePhp\Artifact\Artifact})
 * and instructions on how to generate a product from a configuration
 * (see {@see \FeaturePhp\Generator\Generator}).
 * All of these are provided using {@see Settings}.
 */
class ProductLine {
    /**
     * @var Settings $settings the settings object for the product line
     */
    private $settings;

    /**
     * Creates a product line.
     * @param Settings $settings
     */
    public function __construct($settings) {
        $this->settings = $settings;
    }

    /**
     * Returns the product line's settings.
     * @return Settings
     */
    public function getSettings() {
        return $this->settings;
    }

    /**
     * Returns the product line's model.
     * @return \FeaturePhp\Model\Model
     */
    public function getModel() {
        return $this->settings->get("model");
    }

    /**
     * Returns the product line's name.
     * @return string
     */
    public function getName() {
        return $this->settings->get("name");
    }

    /**
     * Returns the product line's default configuration.
     * @return \FeaturePhp\Model\Configuration
     */
    public function getDefaultConfiguration() {
        return $this->settings->get("defaultConfiguration");
    }

    /**
     * Returns the product line's artifact for a feature.
     * @param \FeaturePhp\Model\Feature $feature
     * @return \FeaturePhp\Artifact\Artifact
     */
    public function getArtifact($feature) {
        return $this->settings->get("artifacts", $feature->getName());
    }

    /**
     * Returns the product line's settings for a generator.
     * @param string $generator
     * @return \FeaturePhp\Generator\Settings
     */
    public function getGeneratorSettings($generator) {
        return $this->settings->getOptional("generators", $generator, fphp\Generator\Settings::emptyInstance());
    }

    /**
     * Returns a product of the product line for a configuration.
     * If the configuration is omitted, the default configuration is used.
     * @param \FeaturePhp\Model\Configuration $configuration
     * @return Product
     */
    public function getProduct($configuration = null) {
        if (!$configuration)
            $configuration = $this->defaultConfiguration;
        return new Product($this, $configuration);
    }
}

?>