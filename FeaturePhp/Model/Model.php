<?php

/**
 * The FeaturePhp\Model\Model class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Model class.
 */
class ModelException extends \Exception {}

/**
 * A feature model for a software product line.
 * A feature model is a tree describing features and their relationships.
 * Here, it is represented as a list of features and feature constraints
 * ({@see Feature} and {@see ConstraintSolver}).
 * The underlying XML feature model has its own class, {@see XmlModel}.
 */
class Model {
    /**
     * @var XmlModel $xmlModel the underlying XML feature model
     */
    private $xmlModel;

    /**
     * @var Feature[] $features a flat list of all features in the model
     */
    private $features;

    /**
     * @var Feature $rootFeature the root of the feature model tree
     */
    private $rootFeature;

    /**
     * @var ConstraintSolver $constraintSolver a solver containing all feature constraints
     */
    private $constraintSolver;

    /**
     * Creates a feature model.
     * @param XmlModel $xmlModel
     */
    public function __construct($xmlModel) {
        $this->xmlModel = $xmlModel;
        $this->features = array();
        $xmlModel->traverse(array($this, "addFeature"));
        $this->rootFeature = $this->features[0];
        $this->constraintSolver = new ConstraintSolver($this);
    }

    /**
     * Adds a feature from an XML node.
     * This is expected to be called only from the model's constructor
     * through XmlModel::traverse().
     * @param \SimpleXMLElement $node
     * @param \SimpleXMLElement $parent
     */
    public function addFeature($node, $parent) {
        $this->features[] = Feature::fromNode($node, $parent);
    }

    /**
     * Returns a feature in the feature model with a given name.
     * @param string $featureName
     * @param bool $permissive
     * @return Feature
     */
    public function getFeature($featureName, $permissive = false) {
        $feature = Feature::findByName($this->features, $featureName, $permissive);
        if (!$feature)
            throw new ModelException("the model has no feature named \"$featureName\"");
        return $feature;
    }

    /**
     * Returns the feature model's underlying XML feature model.
     * @return XmlModel
     */
    public function getXmlModel() {
        return $this->xmlModel;
    }

    /**
     * Returns the feature model's list of features.
     * @return Feature[]
     */
    public function getFeatures() {
        return $this->features;
    }

    /**
     * Returns the feature model's root feature.
     * @return Feature
     */
    public function getRootFeature() {
        return $this->rootFeature;
    }

    /**
     * Returns the feature model's constraint solver.
     * @return ConstraintSolver
     */
    public function getConstraintSolver() {
        return $this->constraintSolver;
    }
}