<?

/**
 * The FeaturePhp\File\FileContent class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A file's content.
 * Because different types of files are supported ({@see TextFile} and {@see StoredFile}
 * in particular), file content is represented in its own class, not as a string.
 */
abstract class FileContent {
    /**
     * Returns a summary of the file's content.
     * The summary should be suitable for an overview of generated files, but does not
     * necessarily contain the complete file content.
     * @return string
     */
    abstract public function getSummary();

    /**
     * Adds the file's content to a ZIP archive.
     * This is expected to be called only be a {@see \FeaturePhp\Exporter\ZipExporter}.
     * @param \ZipArchive $zip
     * @param string $target the file target in the ZIP archive
     */
    abstract public function addToZip($zip, $target);
}

?>