<?

/**
 * The FeaturePhp\File\TextFileContent class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A text file's content.
 * For text files we can store the whole file in a string.
 */
class TextFileContent extends FileContent {
    /**
     * @var string $content the entire content as a string (usually UTF-8)
     */
    private $content;

    /**
     * Creates a text file's content.
     * @param string $content
     */
    public function __construct($content) {
        $this->content = $content;
    }

    /**
     * Returns the entire content as a summary.
     * This returns the whole text file.
     * @return string
     */
    public function getSummary() {
        return $this->content;
    }

    /**
     * Adds the text file's content to a ZIP archive.
     * @param \ZipArchive $zip
     * @param string $target the file target in the ZIP archive
     */
    public function addToZip($zip, $target) {
        return $zip->addFromString($target, $this->content);
    }

    /**
     * Copies the text file's content to the local filesystem.
     * @param string $target the file target in the filesystem
     */
    public function copy($target) {
        if (!parent::copy($target))
            return false;
        return file_put_contents($target, $this->content) !== false;
    }
}

?>