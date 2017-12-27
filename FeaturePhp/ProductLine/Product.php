<?php

/**
 * The FeaturePhp\ProductLine\Product class.
 */

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Product class.
 */
class ProductException extends \Exception {}

/**
 * A product of a software product line.
 * A product is a concrete variant of a {@see ProductLine}.
 * Its {@see \FeaturePhp\Model\Configuration} defines which features
 * are selected and deselected.
 * A product can be analyzed, generated (or derived) and exported automatically.
 */
class Product {
    /**
     * @var ProductLine $productLine the product line that this product derives
     */
    private $productLine;

    /**
     * @var \FeaturePhp\Model\Configuration $configuration the product's configuration
     */
    private $configuration;

    /**
     * Creates a product.
     * Throws {@see \FeaturePhp\ProductLine\ProductException} if the configuration is invalid.
     * @param ProductLine $productLine
     * @param \FeaturePhp\Model\Configuration $configuration
     * @param bool $allowInvalid whether to throw an exception if the configuration is invalid
     */
    public function __construct($productLine, $configuration, $allowInvalid = false) {
        $this->productLine = $productLine;
        $this->configuration = $configuration;
        
        if (!$allowInvalid && !$this->configuration->isValid())
            throw new ProductException("the given configuration is not valid");
    }

    /**
     * Returns the product's product line.
     * @return ProductLine
     */
    public function getProductLine() {
        return $this->productLine;
    }

    /**
     * Returns the product's configuration.
     * @return \FeaturePhp\Model\Configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Returns all generators.
     * The generators are instantiated with their respective generator settings.
     * @return \FeaturePhp\Generator\Generator[]
     */
    private function getAllGenerators() {
        $allGenerators = array();
        foreach (fphp\Generator\Generator::getGeneratorMap() as $key => $klass)            
            $allGenerators[$key] = new $klass($this->productLine->getGeneratorSettings($key));
        return $allGenerators;
    }

    /**
     * Adds a feature's artifact to all generators it specifies.
     * This is done independently from whether the corresponding feature is
     * selected or deselected to support generating code for both cases.
     * @param \FeaturePhp\Generator\Generator[] $allGenerators
     * @param \FeaturePhp\Model\Feature $feature
     * @param callable $func whether to add a selected or deselected artifact
     */
    private function addArtifactToUsedGenerators($allGenerators, $feature, $func) {
        $artifact = $this->productLine->getArtifact($feature);
        foreach ($artifact->getGenerators() as $key => $cfg) {
            if (!array_key_exists($key, $allGenerators))
                throw new ProductException("\"$key\" is not a valid generator");
            call_user_func(array($allGenerators[$key], $func), $artifact);
        }
    }

    /**
     * Returns elements generated for the product.
     * To do this, every artifact is registered with the generators it specifies.
     * Then every generator generates some elements. Finally all the elements are merged.
     * @param callable $func
     * @return mixed[]
     */
    private function getGeneratorElements($func) {
        $allGenerators = $this->getAllGenerators();

        foreach ($this->configuration->getSelectedFeatures() as $feature)
            $this->addArtifactToUsedGenerators($allGenerators, $feature, "addSelectedArtifact");

        foreach ($this->configuration->getDeselectedFeatures() as $feature)
            $this->addArtifactToUsedGenerators($allGenerators, $feature, "addDeselectedArtifact");

        $elements = array();
        foreach ($allGenerators as $generator)
            if ($generator->hasArtifacts())
                $elements = array_merge($elements, call_user_func(array($generator, $func)));
        return $elements;
    }

    /**
     * Generates the product's files.
     * @return \FeaturePhp\File\File[]
     */
    public function generateFiles() {
        $files = $this->getGeneratorElements("generateFiles");
        $files = fphp\Helper\_Array::assertNoDuplicates($files, "getTarget");
        $files = fphp\Helper\_Array::sortByKey($files, "getTarget");
        return $files;
    }

    /**
     * Returns tracing links for the product.
     * @return \FeaturePhp\Artifact\TracingLink[]
     */
    public function trace() {
        $tracingLinks = array_merge(
            $this->getGeneratorElements("trace"),
            $this->getAllGenerators()["runtime"]->traceRuntimeCalls($this->generateFiles(), $this->productLine)
        );
        $tracingLinks = fphp\Helper\_Array::sortByKey($tracingLinks, "getFeatureName");
        return $tracingLinks;
    }

    /**
     * Exports the product using an exporter.
     * Depending on the exporter, this has a side effect, e.g. downloading a file.
     * @param \FeaturePhp\Exporter\Exporter $exporter
     */
    public function export($exporter) {
        $exporter->export($this);
    }

    /**
     * Analyzes the product by returning a web page.
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    public function renderAnalysis($textOnly = false) {
        return (new ProductRenderer($this))->render($textOnly);
    }
}

?>