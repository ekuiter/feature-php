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
        foreach ($this->selectedArtifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(self::getKey());

            foreach ($settings->getOptional("files", array()) as $file) {
                $templateSpecification = fphp\Specification\TemplateSpecification::fromArray($file, $settings);
                $this->files[] = fphp\File\TemplateFile::fromSpecification($templateSpecification);
                $this->logFile->log($artifact, "added file \"{$templateSpecification->getTarget()}\"");
            }
        }
    }
}

?>