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

    public function generateFiles() {
        $logFile = new fphp\File\LogFile("template");
        $files = array($logFile);

        foreach ($this->selectedArtifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(self::getKey());

            foreach ($settings->getOptional("files", array()) as $file) {
                $fileSpecification = fphp\Specification\FileSpecification::fromArray($file, $settings);
                $files[] = fphp\File\TemplateFile::fromFileSpecification($fileSpecification);
                $logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
            }
        }

        return $files;
    }
}

?>