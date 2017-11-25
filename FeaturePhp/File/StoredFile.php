<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class StoredFile extends File {
    protected $fileSource;
    
    public function __construct($fileTarget, $fileSource) {
        parent::__construct($fileTarget);
        $this->fileSource = $fileSource;
    }

    public function getContents() {
        return "stored file at \"$this->fileSource\"";
    }

    public static function fromSpecification($fileSpecification) {        
        return new self($fileSpecification->getTarget(), $fileSpecification->getSource());
    }
}

?>