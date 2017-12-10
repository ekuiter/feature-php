<?php

/**
 * The FeaturePhp\Aspect\Aspect class.
 */

namespace FeaturePhp\Aspect;
use \FeaturePhp as fphp;

/**
 * An aspect kernel manages a set of aspects.
 * The aspect kernel is used to tell Go! AOP about every {@see Aspect} from selected artifacts
 * (see {@see \FeaturePhp\Generator\AspectGenerator}).
 * To use it, include the generated aspect kernel into the application and call its
 * `init` method (see {@see https://github.com/goaop/framework}).
 */
class AspectKernel {
    /**
     * @var \FeaturePhp\Aspect\Aspect[] $aspects all aspects registered in the aspect kernel
     */
    private $aspects;

    /**
     * Creates an aspect kernel.
     */
    public function __construct() {
        $this->aspects = array();
    }

    /**
     * Adds an aspect to the aspect kernel.
     * @param Aspect $aspect
     */
    public function addAspect($aspect) {
        $this->aspects[] = $aspect;
    }

    /**
     * Generates the aspect kernel's files.
     * This includes all aspect files and the aspect kernel itself.
     * @param string $class
     * @param string $target
     */
    public function generateFiles($class, $target) {
        $files = array();
        $includes = "";
        $registers = "";
        
        foreach ($this->aspects as $aspect) {
            $files[] = $aspect->getStoredFile();
            $includes .= "require_once __DIR__ . '/" . str_replace("'", "\'", $aspect->getRelativeFileTarget($target)) . "';\n";
            $registers .= '        $container->registerAspect(new ' . $aspect->getClassName() . "());\n";
        }

        $files[] = fphp\File\TemplateFile::fromSpecification(
            fphp\Specification\TemplateSpecification::fromArrayAndSettings(
                array(
                    "source" => "AspectKernel.php.template",
                    "target" => $target,
                    "rules" => array(
                        array("assign" => "class", "to" => $class),
                        array("assign" => "includes", "to" => trim($includes)),
                        array("assign" => "registers", "to" => trim($registers))
                    )
                ), fphp\Settings::inDirectory(__DIR__))
        );
        
        return $files;
    }
}

?>