<?php

/**
 * The FeaturePhp\Artifact\Place class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * A place is a locatable part of an artifact.
 * A place describes part of an {@see Artifact} and is included
 * in a {@see TracingLink} to provide feature traceability.
 */
class Place {
    /**
     * @var string $filePath the described file
     */
    protected $filePath;
    
    /**
     * Creates a place.
     * @param string $filePath
     */
    public function __construct($filePath) {
        $this->filePath = $filePath;
    }
    
    /**
     * Returns the place's file path.
     * @return string
     */
    public function getFilePath() {
        return $this->filePath;
    }

    /**
     * Returns a summary of the place.
     * @return string
     */
    public function getSummary() {
        return $this->filePath;
    }
}

?>