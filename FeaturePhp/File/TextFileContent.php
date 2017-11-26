<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class TextFileContent extends FileContent {
    private $content;
    
    public function __construct($content) {
        $this->content = $content;
    }

    public function getSummary() {
        return $this->content;
    }

    public function addToZip($zip, $target) {
        return $zip->addFromString($target, $this->content);
    }
}

?>