<?

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

class ChunkSpecification extends FileSpecification {
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        $this->setOptional("text", "");
        $this->getWith("text", "is_string");
        $this->setOptional("header", "");
        $this->getWith("header", "is_string");
        $this->setOptional("footer", "");
        $this->getWith("footer", "is_string");
        $this->setOptional("newline", true);
        $this->getWith("newline", "is_bool");
    }

    public static function fromArray($cfg, $settings) {
        $chunkSpecification = new static($cfg, $settings->getDirectory());
        $chunkSpecification->set("source", null);
        $chunkSpecification->set("target", fphp\Helper\Path::join(
            $settings->getOptional("target", null), $chunkSpecification->getTarget()));
        return $chunkSpecification;
    }

    public function getText() {
        return $this->get("text");
    }

    public function getHeader() {
        return $this->get("header");
    }

    public function getFooter() {
        return $this->get("footer");
    }

    public function getNewline() {
        return $this->get("newline");
    }
}

?>