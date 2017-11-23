<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class TextFile extends File {
    private $contents;
    
    public function __construct($fileName, $contents = null) {
        parent::__construct($fileName);
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