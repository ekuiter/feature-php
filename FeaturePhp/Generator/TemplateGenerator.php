<?

/**
 * The FeaturePhp\Generator\TemplateGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates text files by replacing parts of template files.
 * A selected artifact can specify template files and corresponding replacement rules
 * (see {@see \FeaturePhp\Specification\TemplateSpecification} and
 * {@see \FeaturePhp\Specification\ReplacementRule}).
 * The rules are applied to the template files to generate new, configuration-specific
 * files.
 */
class TemplateGenerator extends Generator {
    /**
     * Creates a template generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
    }

    /**
     * Returns the template generator's key.
     * @return string
     */
    public static function getKey() {
        return "template";
    }

    /**
     * Generates the template files.
     * Only template specifications from selected artifacts are considered.
     */
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