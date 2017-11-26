<?

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

class ConfigurationRenderer extends fphp\Renderer {
    private $configuration;
    
    public function __construct($configuration) {            
        $this->configuration = $configuration;
    }

    public function _render() {
        echo "<h2>Model Analysis</h2>";
        $this->analyzeModel($this->configuration->getModel());
        echo "</td><td valign='top'>";
        echo "<h2>Configuration Analysis</h2>";
        $this->analyzeConfiguration($this->configuration);
    }

    private function analyzeModel($model) {
        $featureNum = count($model->getFeatures());
        $rootFeatureName = $model->getRootFeature()->getName();
        $constraintNum = count($model->getConstraintSolver()->getConstraints());
        $ruleNum = count($model->getXmlModel()->getRules());

        echo "<div>";
        echo "<p>The given feature model with the root feature <span class='feature'>$rootFeatureName</span> "
            . "has the following $featureNum features:</p>";
        echo "<ul>";

        foreach ($model->getFeatures() as $feature) {
            $description = $feature->getDescription();
            echo "<li><span class='feature'>"
                . $feature->getName()
                . ($description ? "</span><br /><span style='font-size: 0.8em'>"
                   . str_replace("\n", "<br />", $description) . "</span>" : "")
                . "</li>";
        }

        echo "</ul>";
        echo "<p>There are $constraintNum feature constraints ($ruleNum of them cross-tree constraints).</p>";
        echo "</div>";
    }

    private function analyzeConfiguration($configuration) {
        $validity = $configuration->isValid() ? "valid" : "invalid";
        
        echo "<div style='font-family: monospace'>";
        echo "<p>The given configuration has the following feature selection:</p>";
        echo "<ul>";

        foreach ($configuration->getModel()->getFeatures() as $feature) {
            $isSelected = Feature::has($configuration->getSelectedFeatures(), $feature);
            $mark = $isSelected ? "x" : "&nbsp;";
            $class = $isSelected ? "selected" : "deselected";
            echo "<li>[$mark] <span class='feature $class'>" . $feature->getName() . "</span></li>";
        }

        echo "</ul>";
        echo "<p>This configuration is $validity.</p>";
        echo "</div>";
    }
}