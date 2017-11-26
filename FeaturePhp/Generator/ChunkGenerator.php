<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class ChunkGenerator extends Generator {
    private $chunkFiles;
    
    public function __construct($settings) {
        parent::__construct($settings);
        $this->chunkFiles = array();

        foreach ($settings->getOptional("files", array()) as $file)
            $this->getChunkFile(fphp\Specification\ChunkSpecification::fromArray($file, $settings));
    }

    public static function getKey() {
        return "chunk";
    }

    private function getChunkFile($chunkSpecification) {
        $target = $chunkSpecification->getTarget();
        if (!array_key_exists($target, $this->chunkFiles)) {
            $this->chunkFiles[$target] = fphp\File\ChunkFile::fromSpecification($chunkSpecification);
            $this->logFile->log(null, "added file \"{$chunkSpecification->getTarget()}\"");
        }
        return $this->chunkFiles[$target];
    }

    public function _generateFiles() {
        foreach ($this->selectedArtifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(self::getKey());

            foreach ($settings->getOptional("files", array()) as $file) {
                $chunkSpecification = fphp\Specification\ChunkSpecification::fromArray($file, $settings);
                $this->getChunkFile($chunkSpecification)->addChunk($chunkSpecification);
                $this->logFile->log($artifact, "added chunk in file \"{$chunkSpecification->getTarget()}\"");
            }
        }

        foreach ($this->chunkFiles as $chunkFile)
            $this->files[] = $chunkFile;
    }
}

?>