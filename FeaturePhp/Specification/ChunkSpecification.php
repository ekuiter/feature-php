<?

/**
 * The FeaturePhp\Specification\ChunkSpecification class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Settings for specifying a chunk of a text file.
 * Chunk specifications are used by a {@see \FeaturePhp\Generator\ChunkGenerator}
 * to specify a chunk of text for inclusion in a {@see \FeaturePhp\File\ChunkFile}.
 * The chunk specification settings inside an artifact's generator settings
 * hold a specific chunk of a chunk file and follow the structure:
 * - root (object)
 *   - target (string) - the file target in the generated product
 *   - text (string) - a chunk of text to include in the target
 *
 * The chunk specification settings in the product line's generator settings
 * hold general settings for a chunk file and follow the structure:
 * - root (object)
 *   - target (string) - the file target in the generated product
 *   - header (string) - header before all chunks
 *   - footer (string) - footer after all chunks
 *   - newline (bool) - whether to include new line characters after each chunk
 */
class ChunkSpecification extends ExtendSpecification {
    /**
     * Creates a chunk specification.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
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

    /**
     * Creates a chunk specification from a plain settings array.
     * The settings context is taken into consideration to generate paths
     * relative to the settings.
     * @param array $cfg a plain settings array
     * @param \FeaturePhp\Settings $settings the settings context
     * @return ChunkSpecification
     */
    public static function fromArray($cfg, $settings) {
        $chunkSpecification = new static($cfg, $settings->getDirectory());
        $chunkSpecification->set("source", null);
        $chunkSpecification->set("target", fphp\Helper\Path::join(
            $settings->getOptional("target", null), $chunkSpecification->getTarget()));
        return $chunkSpecification;
    }

    /**
     * Returns the chunk's text.
     * @return string
     */
    public function getText() {
        return $this->get("text");
    }

    /**
     * Returns the chunked file's header.
     * @return string
     */
    public function getHeader() {
        return $this->get("header");
    }

    /**
     * Returns the chunked file's footer.
     * @return string
     */
    public function getFooter() {
        return $this->get("footer");
    }

    /**
     * Returns whether to include new line characters after each chunk.
     * @return bool
     */
    public function getNewline() {
        return $this->get("newline");
    }
}

?>