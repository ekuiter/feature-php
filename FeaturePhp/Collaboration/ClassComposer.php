<?php

/**
 * The FeaturePhp\Collaboration\ClassComposer class.
 */

namespace FeaturePhp\Collaboration;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the ClassComposer class.
 */
class ClassComposerException extends \Exception {}

/**
 * Composes roles that refine PHP classes.
 * There are two sensible ways to implement this kind of composer:
 * mixin-based inheritance and superimposition. Here, we implement the former
 * because of its relative simplicity (though it has runtime overhead).
 */
class ClassComposer extends Composer {
    /**
     * @var string $targetClass the name of the target class (defined by the first role)
     */
    private $targetClass;

    /**
     * @var string $parentClass the name of the parent class that is being refined
     */
    private $parentClass;

    /**
     * @var array[] $ast the abstract syntax tree so far
     */
    private $ast;

    /**
     * Creates a class composer.
     * @param string $targetClass
     * @param string $parentClass
     * @param array[] $ast
     */
    public function __construct($targetClass = null, $parentClass = null, $ast = array()) {
        $this->targetClass = $targetClass;
        $this->parentClass = $parentClass;
        $this->ast = $ast;
    }
    
    /**
     * Returns the class composer's kind.
     * @return string
     */
    public function getKind() {
        return "php";
    }

    /**
     * Returns the mangled class name for a role.
     * The mangled class name encodes the corresponding feature identifier,
     * this facilitates debugging.
     * @param Role $role
     * @return string
     */
    private function getMangledClassName($role) {
        return $this->targetClass . '__' . $role->getCollaboration()->getArtifact()->getFeature()->getIdentifier();
    }

    /**
     * Mangles the class name for a role.
     * @param \PhpParser\Node\Stmt\Class_ $class
     * @param Role $role
     */
    private function mangleClassName(&$class, $role) {
        $class->name = $this->getMangledClassName($role);
    }

    /**
     * Extends a class with the parent class.
     * @param \PhpParser\Node\Stmt\Class_ $class
     */
    private function extendClass(&$class) {
        $class->extends = new \PhpParser\Node\Name($this->parentClass);
    }

    /**
     * Returns a new class composer containing a role's refinements.
     * @param Role $role
     * @return ClassComposer
     */
    public function refine($role) {
        $fileSource = $role->getFileSpecification()->getSource();
        $parser = (new fphp\Helper\PhpParser())->parseFile($fileSource);
        $ast = $parser->getAst();
        $class = $parser->getExactlyOneClass($fileSource);

        if (!$this->targetClass) { // not refining, but defining the target class
            $this->targetClass = $class->name;
            $this->mangleClassName($class, $role);
        } else { // refining
            if ($class->name !== $this->targetClass)
                throw new ClassComposerException("\"$fileSource\" refines \"$class->name\" (expected \"$this->targetClass\")");
            if ($class->extends)
                throw new ClassComposerException("refining role at \"$fileSource\" may not extend a class");
            
            $this->mangleClassName($class, $role);
            $this->extendClass($class);
        }
        
        return new ClassComposer($this->targetClass, $class->name, array_merge($this->ast, $ast));
    }

    /**
     * Returns the refined file's content.
     * @return \FeaturePhp\File\TextFileContent
     */
    public function getContent() {
        $class = new \PhpParser\Node\Stmt\Class_($this->targetClass);
        $this->extendClass($class);
        $ast = array_merge($this->ast, array($class));
        $code = fphp\Helper\PhpParser::astToString($ast);
        return new fphp\File\TextFileContent($code);
    }
}

?>