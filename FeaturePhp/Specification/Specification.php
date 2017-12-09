<?php

/**
 * The FeaturePhp\Specification\Specification class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Specification class.
 */
class SpecificationException extends \Exception {}

/**
 * Settings for specifying an entity relevant to a generated file.
 * Specification settings are used by a {@see \FeaturePhp\Generator\Generator}.
 * The specification settings follow the structure:
 * - root (object)
 *   - source (string) - a (relative) path to an entity on the server
 *   - target (string) - the target entity in the generated product
 *
 * or, alternatively:
 * - root (string) - specifies source as well as target
 *
 * If either source or target is omitted, they share the same value.
 */
abstract class Specification extends fphp\Settings {
    /**
     * Creates a specification.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        if (is_string($cfg))
            $cfg = array("source" => $cfg);
        if (!is_array($cfg) || (!array_key_exists("source", $cfg) && !array_key_exists("target", $cfg)))
            throw new SpecificationException("invalid specification \"" . json_encode($cfg) . "\"");
        if (!array_key_exists("target", $cfg))
            $cfg["target"] = $cfg["source"];
        if (!array_key_exists("source", $cfg))
            $cfg["source"] = $cfg["target"];

        parent::__construct($cfg, $directory);
    }

    /**
     * Returns the entity's source.
     * @return string
     */
    public function getSource() {
        return $this->get("source");
    }

    /**
     * Returns the entity's target.
     * @return string
     */
    public function getTarget() {
        return $this->get("target");
    }
}

?>