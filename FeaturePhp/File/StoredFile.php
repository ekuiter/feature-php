<?php

/**
 * The FeaturePhp\File\StoredFile class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A file stored on the server.
 * A stored file may contain arbitrary data (text files, images, ...)
 * In contrast to a {@see TextFile}, it can not be manipulated and will be
 * included in a product as is.
 */
class StoredFile extends File {
    /**
     * @var string $fileSource a (relative) path to the file on the server
     */
    protected $fileSource;

    /**
     * Creates a stored file.
     * @param string $fileTarget
     * @param string $fileSource
     */
    public function __construct($fileTarget, $fileSource) {
        parent::__construct($fileTarget);
        $this->fileSource = $fileSource;
    }

    /**
     * Returns the stored file's content.
     * @return StoredFileContent
     */
    public function getContent() {
        return new StoredFileContent($this->fileSource);
    }

    /**
     * Creates a stored file from a file specification.
     * See {@see \FeaturePhp\Specification\FileSpecification} for details.
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     * @return StoredFile
     */
    public static function fromSpecification($fileSpecification) {        
        return new self($fileSpecification->getTarget(), $fileSpecification->getSource());
    }
}

?>