<?

/**
 * The FeaturePhp\Generator\ChunkGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates text files from multiple chunks.
 * A selected artifact can specify chunks of text to include in a
 * {@see \FeaturePhp\File\ChunkFile} (see {@see \FeaturePhp\Specification\ChunkSpecification}).
 * In the product line's generator settings, more details about chunked files
 * (such as header and footer) can be specified (see {@see Settings}).
 */
class ChunkGenerator extends ExtendGenerator {
    /**
     * Returns the chunk generator's key.
     * @return string
     */
    public static function getKey() {
        return "chunk";
    }

    /**
     * Returns a chunk specification from a plain settings array.
     * @param array $file a plain settings array
     * @param Settings $settings the generator's settings
     * @param \FeaturePhp\Artifact\Artifact $artifact the currently processed artifact
     * @return \FeaturePhp\Specification\ChunkSpecification
     */
    protected function getSpecification($file, $settings, $artifact) {
        return fphp\Specification\ChunkSpecification::fromArrayAndSettings($file, $settings);
    }

    /**
     * Returns a chunk file from a chunk specification.
     * @param \FeaturePhp\Specification\ChunkSpecification $chunkSpecification
     * @return \FeaturePhp\File\ChunkFile
     */
    protected function getExtendableFileFromSpecification($chunkSpecification) {
        return fphp\File\ChunkFile::fromSpecification($chunkSpecification);
    }
}

?>