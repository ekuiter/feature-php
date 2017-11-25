<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class TextFile extends File {
    private $contents;
    
    public function __construct($fileTarget, $contents = null) {
        parent::__construct($fileTarget);
        $this->contents = $contents ? $contents : "";
    }

    public function getContents() {
        return $this->contents;
    }

    public function append($contents) {
        $this->contents .= $contents;
    }
}

?>