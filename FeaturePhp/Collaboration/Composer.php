<?php

/**
 * The FeaturePhp\Collaboration\Composer class.
 */

namespace FeaturePhp\Collaboration;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Composer class.
 */
class ComposerException extends \Exception {}

/**
 * Composes roles of the same kind.
 * This implements a non-commutative composer operator for the {@see Role} class.
 * A composer is only applied to roles of the composer's kind.
 * Arbitrary refinements are possible, though the typical use case is implemented
 * by {@see ClassComposer}.
 */
abstract class Composer {
    /**
     * Returns the class names of all composers.
     * @return string[]
     */
    private static function getComposers() {
        return array(
            "\FeaturePhp\Collaboration\ClassComposer"
        );
    }

    /**
     * Returns a map from all composer kinds to class names.
     * @return string[]
     */
    public static function getComposerMap() {
        $composerMap = array();
        foreach (self::getComposers() as $composer)
            $composerMap[call_user_func(array($composer, "getKind"))] = $composer;
        return $composerMap;
    }

    /**
     * Creates a composer from a kind.
     * @param string $kind
     * @return Composer
     */
    public static function fromKind($kind) {
        $composerMap = self::getComposerMap();
        if (!array_key_exists($kind, $composerMap))
            throw new ComposerException("no composer found for \"$kind\"");
        $class = $composerMap[$kind];
        return new $class();
    }

    /**
     * Returns the composer's kind.
     * @return string
     */
    abstract public function getKind();

    /**
     * Returns a new composer containing a role's refinements.
     * @param Role $role
     * @return Composer
     */
    abstract public function refine($role);

    /**
     * Returns the refined file's content.
     * @return \FeaturePhp\File\FileContent
     */
    abstract public function getContent();
}

?>