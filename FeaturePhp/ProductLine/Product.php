<?

namespace FeaturePhp\ProductLine;

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

    private static function getGeneratorMap() {
        return array(
            \FeaturePhp\Generator\EmptyGenerator::getKey() => "\FeaturePhp\Generator\EmptyGenerator",
            \FeaturePhp\Generator\FileGenerator::getKey() => "\FeaturePhp\Generator\FileGenerator",
            \FeaturePhp\Generator\RuntimeGenerator::getKey() => "\FeaturePhp\Generator\RuntimeGenerator"
        );
    }

    private function getInstance(&$generators, $key) {
        if (!array_key_exists($key, self::getGeneratorMap()))
            throw new ProductException("\"$key\" is not a valid generator");
        $klass = self::getGeneratorMap()[$key];
        
        if (!array_key_exists($key, $generators))
            $generators[$key] = new $klass(
                $this->productLine->getGeneratorSettings($key));

        return $generators[$key];
    }

    public function generateFiles() {
        $allGenerators = array();

        foreach ($this->configuration->getSelectedFeatures() as $feature) {
            $artifact = $this->productLine->getArtifact($feature);
            $usedGenerators = $artifact->getGenerators();
            foreach ($usedGenerators as $key => $cfg)
                $this->getInstance($allGenerators, $key)->addArtifact($artifact);
        }

        $files = array();
        foreach ($allGenerators as $generator)
            $files = array_merge($files, $generator->generateFiles());        

        return \FeaturePhp\Generator\File::checkForDuplicates($files);
    }

    public function renderAnalysis() {
        (new ProductRenderer($this))->render();
    }
}

?>