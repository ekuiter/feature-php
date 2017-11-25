<?

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

class ProductException extends \Exception {}

class Product {
    private $productLine;
    private $configuration;
    
    public function __construct($productLine, $configuration) {
        $this->productLine = $productLine;
        $this->configuration = $configuration;
        
        if (!$this->configuration->isValid())
            throw new ProductException("the given configuration is not valid");
    }

    public function getProductLine() {
        return $this->productLine;
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    private function getAllGenerators() {
        $allGenerators = array();
        foreach (fphp\Generator\Generator::getGeneratorMap() as $key => $klass)            
            $allGenerators[$key] = new $klass($this->productLine->getGeneratorSettings($key));
        return $allGenerators;
    }

    private function addArtifactToUsedGenerators($allGenerators, $feature, $func) {
        $artifact = $this->productLine->getArtifact($feature);
        foreach ($artifact->getGenerators() as $key => $cfg) {
            if (!array_key_exists($key, $allGenerators))
                throw new ProductException("\"$key\" is not a valid generator");
            call_user_func(array($allGenerators[$key], $func), $artifact);
        }
    }

    public function generateFiles() {
        $allGenerators = $this->getAllGenerators();

        foreach ($this->configuration->getSelectedFeatures() as $feature)
            $this->addArtifactToUsedGenerators($allGenerators, $feature, "addSelectedArtifact");

        foreach ($this->configuration->getDeselectedFeatures() as $feature)
            $this->addArtifactToUsedGenerators($allGenerators, $feature, "addDeselectedArtifact");

        $files = array();
        foreach ($allGenerators as $generator)
            if ($generator->hasArtifacts())
                $files = array_merge($files, $generator->generateFiles());

        $files = fphp\Helper\_Array::assertNoDuplicates($files, "getFileTarget");
        $files = fphp\Helper\_Array::sortByKey($files, "getFileTarget");
        return $files;
    }

    public function renderAnalysis() {
        (new ProductRenderer($this))->render();
    }
}

?>