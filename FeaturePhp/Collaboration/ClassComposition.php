<?

/**
 * The FeaturePhp\Collaboration\ClassComposition class.
 */

namespace FeaturePhp\Collaboration;
use \FeaturePhp as fphp;

/**
 * Composes roles that refine PHP classes.
 * There are two sensible ways to implement this kind of composition:
 * mixin-based inheritance and superimposition. Here, we implement the former
 * because of its relative simplicity (though it has runtime overhead).
 */
class ClassComposition extends Composition {
    /**
     * Returns the class composition's kind.
     * @return string
     */
    public function getKind() {
        return "php";
    }

    /**
     * Returns a new class composition containing a role's refinements.
     * @param Role $role
     * @return ClassComposition
     */
    public function refine($role) {
        return new ClassComposition();
    }

    /**
     * Returns the refined file's content.
     * @return \FeaturePhp\File\TextFileContent
     */
    public function getContent() {
        return new fphp\File\TextFileContent("");
    }
}

?>