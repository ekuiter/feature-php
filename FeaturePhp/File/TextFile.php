<?php

/**
 * The FeaturePhp\File\TextFile class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A file containing arbitrary text.
 * A text file may be manipulated, in contrast to a {@see StoredFile}.
 * In particular, it may not be used for binary data such as images.
 */
class TextFile extends File {
    /**
     * @var string $content the content as text (usually UTF-8)
     */
    protected $content;

    /**
     * Creates a text file.
     * @param string $fileTarget
     * @param string $content initial content of the file
     */
    public function __construct($fileTarget, $content = null) {
        parent::__construct($fileTarget);
        $this->content = $content ? $content : "";
    }

    /**
     * Returns the text file's content.
     * @return TextFileContent
     */
    public function getContent() {
        return new TextFileContent($this->content);
    }

    /**
     * Appends text content to the file.
     * @param string $content
     */
    public function append($content) {
        $this->content .= $content;
    }
}

?>