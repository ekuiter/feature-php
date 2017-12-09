<?

/**
 * The FeaturePhp\Collaboration\Composition class.
 */

namespace FeaturePhp\Collaboration;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Composition class.
 */
class CompositionException extends \Exception {}

/**
 * Composes roles of the same kind.
 * This implements a non-commutative composition operator for the {@see Role} class.
 * A composition is only applied to roles of the composition's kind.
 * Arbitrary refinements are possible, though the typical use case is implemented
 * by {@see ClassComposition}.
 */
abstract class Composition {
    /**
     * Returns the class names of all compositions.
     * @return string[]
     */
    private static function getCompositions() {
        return array(
            "\FeaturePhp\Collaboration\ClassComposition"
        );
    }

    /**
     * Returns a map from all composition kinds to class names.
     * @return string[]
     */
    public static function getCompositionMap() {
        $compositionMap = array();
        foreach (self::getCompositions() as $composition)
            $compositionMap[call_user_func(array($composition, "getKind"))] = $composition;
        return $compositionMap;
    }

    /**
     * Creates a composition from a kind.
     * @param string $kind
     * @return Composition
     */
    public static function fromKind($kind) {
        $compositionMap = self::getCompositionMap();
        if (!array_key_exists($kind, $compositionMap))
            throw new CompositionException("no composition found for \"$kind\"");
        $class = $compositionMap[$kind];
        return new $class();
    }

    /**
     * Returns the composition's kind.
     * @return string
     */
    abstract public function getKind();

    /**
     * Returns a new composition containing a role's refinements.
     * @param Role $role
     * @return Composition
     */
    abstract public function refine($role);

    /**
     * Returns the refined file's content.
     * @return \FeaturePhp\File\FileContent
     */
    abstract public function getContent();
}

?>