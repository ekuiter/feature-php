<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class TemplateFile extends StoredFile {
    private $rules;
    
    public function __construct($fileTarget, $fileSource, $rules) {
        parent::__construct($fileTarget, $fileSource);
        $this->rules = $rules;
    }

    public function getContent() {
        $content = file_get_contents($this->fileSource);
        foreach ($this->rules as $rule)
            $content = $rule->apply($content);
        return new TextFileContent($content);
    }

    public static function fromSpecification($templateSpecification) {        
        return new self($templateSpecification->getTarget(),
                        $templateSpecification->getSource(),
                        $templateSpecification->getRules());
    }
}

?>