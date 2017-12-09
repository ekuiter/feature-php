<?

/**
 * The FeaturePhp\Generator\CopyGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Copies files and directories.
 * A selected artifact can specify files and directories that should be copied
 * into the product unchanged. This enables file-level granularity.
 * Directories are copied recursively, specific files can be excluded
 * (see {@see \FeaturePhp\Specification\FileSpecification} and
 * {@see \FeaturePhp\Specification\DirectorySpecification}).
 * File contents are not inspected, so binary files can be copied as well.
 */
class CopyGenerator extends FileGenerator {
    /**
     * Returns the copy generator's key.
     * @return string
     */
    public static function getKey() {
        return "copy";
    }

    /**
     * Adds a stored file from a specification.
     * Considers globally excluded files. Only exact file names are supported.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    protected function processFileSpecification($artifact, $fileSpecification) {
        $this->files[] = fphp\File\StoredFile::fromSpecification($fileSpecification);
        $this->logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
    }
}

?>