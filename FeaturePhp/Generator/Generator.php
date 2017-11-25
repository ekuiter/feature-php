<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

abstract class Generator {
    protected $settings;
    protected $selectedArtifacts;
    protected $deselectedArtifacts;
    protected $logFile;

    private static function getGenerators() {
        return array(
            "\FeaturePhp\Generator\EmptyGenerator",
            "\FeaturePhp\Generator\CopyGenerator",
            "\FeaturePhp\Generator\RuntimeGenerator",
            "\FeaturePhp\Generator\TemplateGenerator",
            "\FeaturePhp\Generator\ChunkGenerator"
        );
    }

    public static function getGeneratorMap() {
        $generatorMap = array();
        foreach (self::getGenerators() as $generator)
            $generatorMap[call_user_func(array($generator, "getKey"))] = $generator;
        return $generatorMap;
    }
    
    public function __construct($settings) {
        $this->settings = $settings;
        $this->selectedArtifacts = array();
        $this->deselectedArtifacts = array();
        $this->logFile = new fphp\File\LogFile(static::getKey());
    }

    public function getSettings() {
        return $this->settings;
    }
    
    public function addSelectedArtifact($artifact) {
        $this->selectedArtifacts[] = $artifact;
    }

    public function addDeselectedArtifact($artifact) {
        $this->deselectedArtifacts[] = $artifact;
    }

    public function hasArtifacts() {
        return count($this->selectedArtifacts) > 0 || count($this->deselectedArtifacts) > 0;
    }

    public function generateFiles() {
        return array_merge(array($this->logFile), $this->_generateFiles());
    }

    abstract public static function getKey();
    abstract protected function _generateFiles();
}

?>