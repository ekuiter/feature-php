<?php

/**
 * The FeaturePhp\Model\ConfigurationRenderer class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * A renderer for a configuration and its model.
 * This renders a {@see Configuration} and its {@see Model} analysis as a web page.
 * The model analysis includes a list of features and descriptions.
 * The configuration analysis lists selected and deselected features and evaluates
 * the configuration's validity.
 */
class ConfigurationRenderer extends fphp\Renderer {
    /**
     * @var Configuration $configuration the configuration that will be analyzed
     */
    private $configuration;

    /**
     * @var \FeaturePhp\ProductLine\ProductLine|null $productLine the associated product line
     */
    private $productLine;

    /**
     * Creates a configuration renderer.
     * @param Configuration $configuration
     * @param \FeaturePhp\ProductLine\ProductLine $productLine
     */
    public function __construct($configuration, $productLine = null) {            
        $this->configuration = $configuration;
        $this->productLine = $productLine;
    }

    /**
     * Returns the model and configuration analysis.
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    public function _render($textOnly) {
        $str = "";
        if ($textOnly)
            $str .= "\nModel Analysis\n==============\n";
        else
            $str .= "<h2>Model Analysis</h2>";
        $str .= $this->analyzeModel($this->configuration->getModel(), $textOnly);
        if ($textOnly)
            $str .= "\nConfiguration Analysis\n======================\n";
        else
            $str .= "</td><td valign='top'><h2>Configuration Analysis</h2>";
        $str .= $this->analyzeConfiguration($this->configuration, $textOnly);
        return $str;
    }

    /**
     * Returns a model analysis.
     * @param Model $model
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    private function analyzeModel($model, $textOnly) {
        $featureNum = count($model->getFeatures());
        $rootFeatureName = $model->getRootFeature()->getName();
        $constraintNum = count($model->getConstraintSolver()->getConstraints());
        $ruleNum = count($model->getXmlModel()->getRules());

        $str = "";
        $maxLen = fphp\Helper\_String::getMaxLength($model->getFeatures(), "getName");
        
        if ($textOnly)
            $str .= "The given feature model with the root feature " .
                 "$this->accentColor$rootFeatureName$this->defaultColor has the following $featureNum features:\n\n";
        else {
            $str .= "<div>";
            $str .= "<p>The given feature model with the root feature <span class='feature'>$rootFeatureName</span> "
                 . "has the following $featureNum features:</p>";
            $str .= "<ul>";
        }

        foreach ($model->getFeatures() as $feature) {
            $description = $feature->getDescription();
            if ($this->productLine)
                $class = $this->productLine->getArtifact($feature)->isGenerated() ? "" : "unimplemented";
            else
                $class = "";

            if ($textOnly) {
                $color = $class === "unimplemented" ? "" : $this->accentColor;
                $str .= sprintf("%s%-{$maxLen}s$this->defaultColor %s\n", $color, $feature->getName(),
                                fphp\Helper\_String::truncate($description));
            } else
                $str .= "<li><span class='feature $class'>"
                     . $feature->getName()
                     . ($description ? "</span><br /><span style='font-size: 0.8em'>"
                        . str_replace("\n", "<br />", $description) . "</span>" : "")
                     . "</li>";
        }

        if ($textOnly)
            $str .= "\nThere are $constraintNum feature constraints ($ruleNum of them cross-tree constraints).\n";
        else {
            $str .= "</ul>";
            $str .= "<p>There are $constraintNum feature constraints ($ruleNum of them cross-tree constraints).</p>";
            $str .= "</div>";
        }
        return $str;
    }

    /**
     * Returns a configuration analysis.
     * @param Configuration $configuration
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    private function analyzeConfiguration($configuration, $textOnly) {
        $validity = $configuration->isValid() ? "valid" : "invalid";

        $str = "";
        $selectedColor = "\033[1;32m";
        $deselectedColor = "\033[1;31m";
        
        if ($textOnly)
            $str .= "The given configuration has the following feature selection:\n\n";
        else {
            $str .= "<div style='font-family: monospace'>";
            $str .= "<p>The given configuration has the following feature selection:</p>";
            $str .= "<ul>";
        }

        foreach ($configuration->getModel()->getFeatures() as $feature) {
            $isSelected = Feature::has($configuration->getSelectedFeatures(), $feature);
            $mark = $isSelected ? "x" : "&nbsp;";
            $class = $isSelected ? "selected" : "deselected";

            if ($textOnly)
                $str .= "[" . ($isSelected ? "x" : " ") . "] " .
                     ($isSelected ? $selectedColor : $deselectedColor) . $feature->getName() . "$this->defaultColor\n";
            else
                $str .= "<li>[$mark] <span class='feature $class'>" . $feature->getName() . "</span></li>";
        }

        if ($textOnly)
            $str .= "\nThis configuration is $validity.\n";
        else {
            $str .= "</ul>";
            $str .= "<p>This configuration is $validity.</p>";
            $str .= "</div>";
        }
        return $str;
    }
}