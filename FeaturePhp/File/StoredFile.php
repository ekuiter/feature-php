<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class StoredFile extends File {
    protected $fileSource;
    
    public function __construct($fileTarget, $fileSource) {
        parent::__construct($fileTarget);
        $this->fileSource = $fileSource;
    }

    public function getContent() {
        return new StoredFileContent($this->fileSource);
    }

    public static function fromSpecification($fileSpecification) {        
        return new self($fileSpecification->getTarget(), $fileSpecification->getSource());
    }
}

?>