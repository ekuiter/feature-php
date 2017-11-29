<?

/**
 * The FeaturePhp\File\TemplateFile class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A text file in which specific parts may be replaced.
 * A template file is a {@see StoredFile} which is assumed to have text contents.
 * It has rules (see {@see \FeaturePhp\Specification\ReplacementRule}) which specify
 * parts of the file to be replaced, enabling simple feature-based templating systems.
 */
class TemplateFile extends StoredFile {
    /**
     * @var \FeaturePhp\Specification\ReplacementRule[] $rules rules to apply to the file content
     */
    private $rules;
    
    /**
     * Creates a template file.
     * @param string $fileTarget
     * @param string $fileSource
     * @param \FeaturePhp\Specification\ReplacementRule[] $rules
     */
    public function __construct($fileTarget, $fileSource, $rules) {
        parent::__construct($fileTarget, $fileSource);
        $this->rules = $rules;
    }

    /**
     * Returns the template file's content.
     * The content consists of the file content with every rule applied.
     * @return TextFileContent
     */
    public function getContent() {
        $content = file_get_contents($this->fileSource);
        foreach ($this->rules as $rule)
            $content = $rule->apply($content);
        return new TextFileContent($content);
    }

    /**
     * Creates a template file from a template specification.
     * See {@see \FeaturePhp\Specification\TemplateSpecification} for details.
     * @param \FeaturePhp\Specification\TemplateSpecification $templateSpecification
     * @return TemplateFile
     */
    public static function fromSpecification($templateSpecification) {        
        return new self($templateSpecification->getTarget(),
                        $templateSpecification->getSource(),
                        $templateSpecification->getRules());
    }
}

?>