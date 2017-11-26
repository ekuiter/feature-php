<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class TextFile extends File {
    protected $content;
    
    public function __construct($fileTarget, $content = null) {
        parent::__construct($fileTarget);
        $this->content = $content ? $content : "";
    }

    public function getContent() {
        return new TextFileContent($this->content);
    }

    public function append($content) {
        $this->content .= $content;
    }
}

?>