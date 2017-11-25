<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class TemplateFile extends StoredFile {
    private $rules;
    
    public function __construct($fileTarget, $fileSource, $rules) {
        parent::__construct($fileTarget, $fileSource);
        $this->rules = $rules;
    }

    public function getContents() {
        $contents = file_get_contents($this->fileSource);
        foreach ($this->rules as $rule)
            $contents = $rule->apply($contents);
        return $contents;
    }

    public static function fromFileSpecification($templateSpecification) {        
        return new self($templateSpecification->getTarget(),
                        $templateSpecification->getSource(),
                        $templateSpecification->getRules());
    }
}

?>