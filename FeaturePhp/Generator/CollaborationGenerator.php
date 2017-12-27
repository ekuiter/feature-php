<?php

/**
 * The FeaturePhp\Generator\CollaborationGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the CollaborationGenerator class.
 */
class CollaborationGeneratorException extends \Exception {}

/**
 * Composes classes from roles inside collaborations.
 * This generator implements the idea of collaboration-based design outlined
 * in Chapter 6 of "Feature-Oriented Software Product Lines" (see 
 * {@see http://www.springer.com/de/book/9783642375200}).
 * Features are mapped to collaborations (see {@see \FeaturePhp\Collaboration\Collaboration})
 * which are in turn a set of roles. A {@see \FeaturePhp\Collaboration\Role} is
 * the base code for a class or a class refinement. Depending on the feature selection,
 * corresponding roles in the collaborations are composed into a set of refined files
 * (see {@see \FeaturePhp\File\RefinedFile}) implementing the selected features.
 * The concrete composition mechanism may differ from language to language
 * (see {@see \FeaturePhp\Collaboration\Composer}).
 */
class CollaborationGenerator extends FileGenerator {
    /**
     * @var FeaturePhp\Collaboration\Collaboration[] $collaborations all collaborations from selected artifacts
     */
    private $collaborations;

    /**
     * @var string[] $featureOrder ordered feature names to determine composition order
     */
    private $featureOrder;

    /**
     * Creates a collaboration generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
        $this->collaborations = array();
        $this->featureOrder = $settings->getOptional("featureOrder", array());
    }
    
    /**
     * Returns the collaboration generator's key.
     * @return string
     */
    public static function getKey() {
        return "collaboration";
    }

    /**
     * Adds a role from a file to a collaboration.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    protected function processFileSpecification($artifact, $fileSpecification) {
        $collaboration = fphp\Collaboration\Collaboration::findByArtifact($this->collaborations, $artifact);
        if (!$collaboration)
            $this->collaborations[] = $collaboration = new fphp\Collaboration\Collaboration($artifact);
        $collaboration->addRoleFromFileSpecification($fileSpecification);
        $this->tracingLinks[] = new fphp\Artifact\TracingLink(
            "role", $artifact, $fileSpecification->getSourcePlace(), $fileSpecification->getTargetPlace());
        $this->logFile->log($artifact, "using role at \"{$fileSpecification->getTarget()}\"");
    }

    /**
     * Generates the refined files.
     */
    protected function _generateFiles() {
        foreach ($this->getRegisteredArtifacts() as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            if (array_search($featureName, $this->featureOrder) === false)
                throw new CollaborationGeneratorException("no feature order supplied for \"$featureName\"");
        }
        
        parent::_generateFiles();

        $rolePartition = fphp\Helper\Partition::fromObjects($this->collaborations, "getRoles")->partitionBy("correspondsTo");
        foreach ($rolePartition as $roleEquivalenceClass) {
            // sort the roles by their feature order (because role composition is not commutative)
            $roleEquivalenceClass = fphp\Helper\_Array::schwartzianTransform($roleEquivalenceClass, function($role) {
                return array_search($role->getCollaboration()->getArtifact()->getFeature()->getName(), $this->featureOrder);
            });
            
            // take any representative from the equivalence class to extract the file target
            $fileTarget = $roleEquivalenceClass[0]->getFileSpecification()->getTarget();
            $this->files[] = new fphp\File\RefinedFile($fileTarget, $roleEquivalenceClass);
            $this->logFile->log(null, "added file \"$fileTarget\"");
        }
    }
}

?>