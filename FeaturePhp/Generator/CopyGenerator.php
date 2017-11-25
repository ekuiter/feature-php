<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class CopyGenerator extends Generator {    
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "copy";
    }

    public function generateFiles() {
        $logFile = new LogFile("file");
        $files = array($logFile);

        foreach ($this->selectedArtifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $settings = $artifact->getGeneratorSettings(self::getKey());

            foreach ($settings->getOptional("files", array()) as $file) {
                $fileSpecification = FileSpecification::fromArray($file, $settings);
                $files[] = StoredFile::fromFileSpecification($fileSpecification);
                $logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
            }

            foreach ($settings->getOptional("directories", array()) as $directory) {
                $directorySpecification = DirectorySpecification::fromArray($directory, $settings);
                
                foreach ($directorySpecification->getFileSpecifications() as $fileSpecification) {
                    $files[] = StoredFile::fromFileSpecification($fileSpecification);
                    $logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
                }
            }
        }

        return $files;
    }
}

?>