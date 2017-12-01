<?

/**
 * The FeaturePhp\File\ChunkFile class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A text file generated from multiple chunks.
 * A chunked file is a {@see TextFile} generated by a {@see \FeaturePhp\Generator\ChunkGenerator}.
 * It contains some chunks of text in an undefined order. Optionally, a header and a footer can be provided.
 */
class ChunkFile extends TextFile implements ExtendFile {
    /**
     * @var string $header optional header before all chunks
     */
    private $header;

    /**
     * @var string $footer optional footer before all chunks
     */
    private $footer;

    /**
     * @var bool $newline whether to include new line characters after the header, footer and each chunk
     */
    private $newline;

    /**
     * Creates a chunked file.
     * @param string $fileTarget
     * @param string $header
     * @param string $footer
     * @param bool $newline
     */
    public function __construct($fileTarget, $header = "", $footer = "", $newline = true) {
        $this->header = $header;
        $this->footer = $footer;
        $this->newline = $newline ? "\n" : "";
        parent::__construct($fileTarget, $header === "" ? "" : $header . $this->newline);
    }

    /**
     * Creates a chunked file from a chunk specification.
     * See {@see \FeaturePhp\Specification\ChunkSpecification} for details.
     * @param \FeaturePhp\Specification\ChunkSpecification $chunkSpecification
     * @return ChunkFile
     */
    public static function fromSpecification($chunkSpecification) {
        return new self($chunkSpecification->getTarget(),
                        $chunkSpecification->getHeader(),
                        $chunkSpecification->getFooter(),
                        $chunkSpecification->getNewline());
    }

    /**
     * Adds a chunk to the chunked file.
     * This is expected to be called only be a {@see \FeaturePhp\Generator\ChunkGenerator}.
     * Only uses the text of the chunk specification.
     * @param \FeaturePhp\Specification\ChunkSpecification $chunkSpecification
     */
    public function extend($chunkSpecification) {
        $this->append($chunkSpecification->getText() . $this->newline);
    }

    /**
     * Returns the chunked file's content.
     * The content consists of the header, then every chunk and then the footer.
     * @return TextFileContent
     */
    public function getContent() {
        return new TextFileContent(
            $this->content . ($this->footer === "" ? "" : $this->footer . $this->newline));
    }
}

?>