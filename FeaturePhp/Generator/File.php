<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

abstract class File {
    private $fileName;
    
    public function __construct($fileName) {
        $this->fileName = fphp\Helper\Path::resolve($fileName);
    }

    public function getFileName() {
        return $this->fileName;
    }

    abstract public function getContents();
}

?>