<?php

/**
 * The FeaturePhp\Artifact\ChunkPlace class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * A chunk place describes a chunk of an artifact's file.
 * It can provide feature traceability for chunks of files.
 */
class ChunkPlace extends Place {
    /**
     * @var int $startLine the first line of the chunk
     */
    private $startLine;

    /**
     * @var int $endLine the last line of the chunk
     */
    private $endLine;

    /**
     * Creates a chunk place.
     * @param string $filePath
     * @param int $startLine
     * @param int $endLine
     */
    public function __construct($filePath, $startLine, $endLine) {
        parent::__construct($filePath);
        $this->startLine = $startLine;
        $this->endLine = $endLine;
    }
    
    /**
     * Returns a summary of the chunk place.
     * @return string
     */
    public function getSummary() {
        return parent::getSummary() . ":$this->startLine-$this->endLine";
    }
}

?>