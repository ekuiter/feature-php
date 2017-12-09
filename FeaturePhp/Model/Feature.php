<?php

/**
 * The FeaturePhp\Model\Feature class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Feature class.
 */
class FeatureException extends \Exception {}

/**
 * A feature of a feature model.
 * > A feature is a characteristic or end-user-visible behavior of a software system.

 * (from "Feature-Oriented Software Product Lines", see {@see http://www.springer.com/de/book/9783642375200})
 *
 * Here, it is essentially a name for some functionality that should be either included in
 * (*selected* feature) or omitted from (*deselected* feature) a {@see \FeaturePhp\ProductLine\Product}.
 * Features are usually related to other features through feature constraints
 * (see {@see ConstraintSolver}).
 * Every feature has a corresponding {@see \FeaturePhp\Artifact\Artifact} that handles
 * the details of product generation.
 */
class Feature {
    /**
     * @var string $name the feature's name
     */
    private $name;

    /**
     * @var string $description the feature's description, if any
     */
    private $description;

    /**
     * @var bool $mandatory whether the feature is mandatory
     */
    private $mandatory;

    /**
     * @var bool $alternative whether the feature provides an alternative choice
     */
    private $alternative;

    /**
     * @var bool $or whether the feature provides a choice using an inclusive or
     */
    private $or;

    /**
     * @var \SimpleXMLElement the feature's parent feature, if any
     */
    private $parent;

    /**
     * @var \SimpleXMLElement[] the feature's child features, if any
     */
    private $children;

    /**
     * Creates a feature.
     * For the attributes "mandatory", "alternative" and "or" see {@see \FeaturePhp\Model\ConstraintSolver}.
     * @param \SimpleXMLElement $node
     * @param \SimpleXMLElement $parent
     * @param \SimpleXMLElement[] $children
     */
    public function __construct($node, $parent = null, $children = null) {
        $this->name = (string) $node["name"];
        $this->description = $node->description ? trim((string) $node->description) : null;
        $this->mandatory = (string) $node["mandatory"] === "true";
        $this->alternative = $node->getName() === "alt";
        $this->or = $node->getName() === "or";
        $this->parent = $parent ? new self($parent) : null;

        if ($children && ($this->alternative || $this->or)) {
            $this->children = array();
            foreach ($children as $child) {
                if (in_array($child->getName(), array("feature", "and", "or", "alt")))
                    $this->children[] = new self($child);
            }
        }
    }

    /**
     * Creates a feature from an XML node.
     * @param \SimpleXMLElement $node
     * @param \SimpleXMLElement $parent
     * @return Feature
     */
    public static function fromNode($node, $parent) {
        if (is_null($node["value"]))
            return new Feature($node, $parent, $node->children());
        else
            return new ValueFeature($node, $parent, $node->children());
    }

    /**
     * Returns the feature's name.
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Transforms a feature's name to a permissive name.
     * @param string $name
     * @return string
     */
    private static function _getPermissiveName($name) {
        return str_replace(" ", "-", strtolower($name));
    }

    /**
     * Returns the feature's permissive name.
     * @return string
     */
    public function getPermissiveName() {
        return self::_getPermissiveName($this->name);
    }

    /**
     * Returns the feature's identifier.
     * @return string
     */
    public function getIdentifier() {
        return str_replace(" ", "", strtolower($this->name));
    }

    /**
     * Returns the feature's description.
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Returns whether the feature is mandatory.
     * @return bool
     */
    public function getMandatory() {
        return $this->mandatory;
    }

    /**
     * Returns whether the feature provides an alternative choice.
     * @return bool
     */
    public function getAlternative() {
        return $this->alternative;
    }

    /**
     * Returns whether the feature provides a choice using an inclusive or.
     * @return bool
     */
    public function getOr() {
        return $this->or;
    }

    /**
     * Returns the feature's default value.
     * @return string
     */
    public function getDefaultValue() {
        throw new FeatureException("not a value feature");
    }

    /**
     * Returns the feature's parent feature.
     * @return \SimpleXMLElement
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Returns the feature's child features.
     * @return \SimpleXMLElement[]
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Finds a feature by its name in a list of features.
     * Permissive search ignores case and substitutes hyphens.
     * @param Feature[] $features
     * @param string $featureName
     * @param bool $permissive
     * @return Feature
     */
    public static function findByName($features, $featureName, $permissive = false) {
        if ($permissive)
            $featureName = self::_getPermissiveName($featureName);
        return fphp\Helper\_Array::findByKey($features, $permissive ? "getPermissiveName" : "getName", $featureName);
    }

    /**
     * Returns whether a feature is included in a list of features.
     * @param Features[] $features
     * @param Feature $feature
     * @return bool
     */
    public static function has($features, $feature) {
        return !!self::findByName($features, $feature->getName());
    }
}