<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class FileException extends \Exception {}

class File {
    private $fileName;
    private $contents;
    
    public function __construct($fileName, $contents = null) {
        $this->fileName = fphp\Helper\Path::resolve($fileName);
        $this->contents = $contents ? $contents : "";
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getContents() {
        return $this->contents;
    }

    public function append($contents) {
        $this->contents .= $contents;
    }
}

?>