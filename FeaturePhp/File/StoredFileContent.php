<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class StoredFileContent extends FileContent {
    private $fileSource;
    
    public function __construct($fileSource) {
        $this->fileSource = $fileSource;
    }

    public function getSummary() {
        return "stored file at \"$this->fileSource\"";
    }

    public function addToZip($zip, $target) {
        return $zip->addFile($this->fileSource, $target);
    }
}

?>