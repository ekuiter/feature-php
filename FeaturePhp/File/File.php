<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

abstract class File {
    protected $fileTarget;
    
    public function __construct($fileTarget) {
        $this->fileTarget = fphp\Helper\Path::resolve($fileTarget);
    }

    public function getFileTarget() {
        return $this->fileTarget;
    }

    abstract public function getContents();
}

?>