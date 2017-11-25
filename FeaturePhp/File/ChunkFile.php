<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class ChunkFile extends TextFile {
    private $header;
    private $footer;

    public function __construct($fileTarget, $header = "", $footer = "", $newline = true) {
        $this->header = $header;
        $this->footer = $footer;
        $this->newline = $newline ? "\n" : "";
        parent::__construct($fileTarget, $header === "" ? "" : $header . $this->newline);
    }

    public static function fromSpecification($chunkSpecification) {
        return new self($chunkSpecification->getTarget(),
                        $chunkSpecification->getHeader(),
                        $chunkSpecification->getFooter(),
                        $chunkSpecification->getNewline());
    }

    public function addChunk($chunkSpecification) {
        $this->append($chunkSpecification->getText() . $this->newline);
    }

    public function getContents() {
        return parent::getContents() . ($this->footer === "" ? "" : $this->footer . $this->newline);
    }
}

?>