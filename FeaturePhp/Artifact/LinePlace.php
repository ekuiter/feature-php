<?php

/**
 * The FeaturePhp\Artifact\LinePlace class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * A line place describes a line of an artifact's file.
 * It can provide feature traceability for lines of files.
 */
class LinePlace extends Place {
    /**
     * @var int $line the line of the file
     */
    private $line;

    /**
     * Creates a line place.
     * @param string $filePath
     * @param int $line
     */
    public function __construct($filePath, $line) {
        parent::__construct($filePath);
        $this->line = $line;
    }
    
    /**
     * Returns a summary of the line place.
     * @return string
     */
    public function getSummary() {
        return parent::getSummary() . ":$this->line";
    }
}

?>