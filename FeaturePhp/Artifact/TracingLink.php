<?php

/**
 * The FeaturePhp\Artifact\TracingLink class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * A tracing link provides feature traceability.
 * A tracing link connects a {@see Artifact} with a source and target 
 * {@see Place} to provide feature traceability. This implements ideas
 * from Chapter 7 of "Feature-Oriented Software Product Lines" (see 
 * {@see http://www.springer.com/de/book/9783642375200}).
 */
class TracingLink {
    /**
     * @var string $type a string describing the type of tracing link
     */
    private $type;

    /**
     * @var Artifact $artifact the artifact associated with the tracing link
     */
    private $artifact;

    /**
     * @var Place $place the source place associated with the tracing link
     */
    private $sourcePlace;

    /**
     * @var Place $place the target place associated with the tracing link
     */
    private $targetPlace;

    /**
     * Creates a tracing link.
     * @param string $type 
     * @param Artifact $artifact 
     * @param Place $sourcePlace 
     * @param Place $targetPlace 
     */
    public function __construct($type, $artifact, $sourcePlace, $targetPlace) {
        $this->type = $type;
        $this->artifact = $artifact;
        $this->sourcePlace = $sourcePlace;
        $this->targetPlace = $targetPlace;
    }

    /**
     * Returns the tracing link's artifact.
     * @return Artifact
     */
    public function getArtifact() {
        return $this->artifact;
    }

    /**
     * Returns the tracing link artifact feature's name.
     * @return Artifact
     */
    public function getFeatureName() {
        return $this->artifact ? $this->artifact->getFeature()->getName() : "(unknown)";
    }

    /**
     * Returns the tracing link's type.
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the tracing link's source place.
     * @return Place
     */
    public function getSourcePlace() {
        return $this->sourcePlace;
    }

    /**
     * Returns the tracing link's target place.
     * @return Place
     */
    public function getTargetPlace() {
        return $this->targetPlace;
    }
}

?>