<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class StoredFile extends File {
    private $fileSource;
    
    public function __construct($fileName, $fileSource) {
        parent::__construct($fileName);
        $this->fileSource = $fileSource;
    }

    public function getContents() {
        return "stored file at \"$this->fileSource\"";
    }

    public static function fromFileSpecification($fileSpecification) {        
        return new self($fileSpecification->getTarget(), $fileSpecification->getSource());
    }
}

?>