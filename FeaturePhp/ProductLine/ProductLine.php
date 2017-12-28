<?php

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
     * Returns a feature in the product line's model with a given name.
     * @param string $featureName
     * @param bool $permissive
     * @return \FeaturePhp\Model\Feature
     */
    public function getFeature($featureName, $permissive = false) {
        return $this->getModel()->getFeature($featureName, $permissive);
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
            $configuration = $this->getDefaultConfiguration();
        return new Product($this, $configuration);
    }

    /**
     * Returns tracing links for an artifact.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @return \FeaturePhp\Artifact\TracingLink[]
     */
    public function trace($artifact) {
        $featureName = str_replace("\"", "&quot;", $artifact->getFeature()->getName());
        $xmlConfiguration = fphp\Model\XmlConfiguration::fromString(
            '<configuration><feature name="' . $featureName . '" automatic="undefined" manual="selected"/></configuration>');
        $configuration = new fphp\Model\Configuration($this->getModel(), $xmlConfiguration);
        $product = new Product($this, $configuration, true);
        return $product->trace();
    }

    /**
     * Analyzes an artifact's tracing links by returning a web page.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    public function renderTracingLinkAnalysis($artifact, $textOnly = false) {
        return (new fphp\Artifact\TracingLinkRenderer($this->trace($artifact)))->render($textOnly);
    }
}

?>