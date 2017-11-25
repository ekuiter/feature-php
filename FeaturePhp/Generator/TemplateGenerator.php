<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class TemplateGenerator extends Generator {    
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "template";
    }

    public function _generateFiles() {
        $files = array();

        foreach ($this->selectedArtifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(self::getKey());

            foreach ($settings->getOptional("files", array()) as $file) {
                $fileSpecification = fphp\Specification\FileSpecification::fromArray($file, $settings);
                $files[] = fphp\File\TemplateFile::fromFileSpecification($fileSpecification);
                $this->logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
            }
        }

        return $files;
    }
}

?>