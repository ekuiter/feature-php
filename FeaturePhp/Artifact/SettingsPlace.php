<?php

/**
 * The FeaturePhp\Artifact\SettingsPlace class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * A settings place refers to an artifact's settings.
 * It can provide feature traceability for code that was generated from settings.
 */
class SettingsPlace extends Place {
    /**
     * @var Artifact $artifact the artifact associated with the place
     */
    private $artifact;
    
    /**
     * Creates a settings place.
     * @param Artifact $artifact
     */
    public function __construct($artifact) {
        $this->artifact = $artifact;
    }
    
    /**
     * Returns a summary of the settings place.
     * @return string
     */
    public function getSummary() {
        return "(settings for " . $this->artifact->getFeature()->getName() . ")";
    }
}

?>