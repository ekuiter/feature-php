<?php

/**
 * The FeaturePhp\Model\ValueFeature class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the ValueFeature class.
 */
class ValueFeatureException extends \Exception {}

/**
 * A feature holding a value.
 * While most features are simple boolean features (see {@see Feature}),
 * value features have a string value that may further customize product generation.
 */
class ValueFeature extends Feature {
    /**
     * @var string $defaultValue the feature's default value, encoded as a string
     */
    private $defaultValue;
    
    /**
     * Creates a value feature.
     * @param \SimpleXMLElement $node
     * @param \SimpleXMLElement $parent
     * @param \SimpleXMLElement[] $children
     */
    public function __construct($node, $parent = null, $children = null) {
        parent::__construct($node, $parent, $children);
        if (is_null($node["value"]))
            throw new ValueFeatureException("not a value feature");
        $this->defaultValue = (string) $node["value"];
    }

    /**
     * Returns the feature's default value.
     * @return string
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }
}