<?php

/**
 * The FeaturePhp\File\StoredFileContent class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A stored file's content.
 * Because stored files may contain binary data, they are not read with
 * the likes of file_get_contents.
 */
class StoredFileContent extends FileContent {
    /**
     * @var string $fileSource a (relative) path to the file on the server
     */
    private $fileSource;

    /**
     * Creates a stored file's content.
     * @param string $fileSource
     */
    public function __construct($fileSource) {
        $this->fileSource = $fileSource;
    }

    /**
     * Returns the stored file's file source.
     * @return string
     */
    public function getFileSource() {
        return $this->fileSource;
    }

    /**
     * Returns the stored file's path as a summary.
     * This returns no content of the stored file.
     * @return string
     */
    public function getSummary() {
        return "stored file at \"$this->fileSource\"";
    }

    /**
     * Adds the stored file's content to a ZIP archive.
     * @param \ZipArchive $zip
     * @param string $target the file target in the ZIP archive
     */
    public function addToZip($zip, $target) {
        return $zip->addFile($this->fileSource, $target);
    }

    /**
     * Copies the stored file's content to the local filesystem.
     * @param string $target the file target in the filesystem
     */
    public function copy($target) {
        if (!parent::copy($target))
            return false;
        return copy($this->fileSource, $target);
    }
}

?>