<?php

/**
 * The FeaturePhp\Generator\AspectGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Registers aspects in an aspect kernel.
 * This generator implements the idea of aspect-oriented programming outlined
 * in Chapter 6 of "Feature-Oriented Software Product Lines" (see 
 * {@see http://www.springer.com/de/book/9783642375200}).
 * A feature can supply some aspects (see {@see \FeaturePhp\Aspect\Aspect}) which
 * serve the modularization of crosscutting concerns. An aspect can intercept
 * different kinds of events in the control flow (called pointcuts).
 * Aspects are implemented using the Go! AOP framework (see {@see https://github.com/goaop/framework})
 * which is a mature AOP framework for PHP development.
 * All aspects from selected features are registered in a {@see \FeaturePhp\Aspect\AspectKernel}
 * which is then available for use in the generated product.
 */
class AspectGenerator extends FileGenerator {
    /**
     * @var \FeaturePhp\Aspect\AspectKernel $aspectKernel the application aspect kernel
     */
    private $aspectKernel;

    /**
     * @var string $class the aspect kernel name, defaults to "ApplicationAspectKernel"
     */
    private $class;

    /**
     * @var string $class the aspect kernel file target in the generated product,
     * defaults to "AspectKernel.php"
     */
    private $target;

    /**
     * @var string|null $feature a feature that has to be selected to generate the aspect kernel
     */
    private $feature;

    /**
     * Creates an aspect generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
        $this->aspectKernel = new fphp\Aspect\AspectKernel();
        $this->class = $settings->getOptional("class", "ApplicationAspectKernel");
        $this->target = $settings->getOptional("target", "AspectKernel.php");
        $this->feature = $settings->getOptional("feature", null);
    }
    
    /**
     * Returns the aspect generator's key.
     * @return string
     */
    public static function getKey() {
        return "aspect";
    }

    /**
     * Adds an aspect from a file to the aspect kernel.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    protected function processFileSpecification($artifact, $fileSpecification) {
        $this->aspectKernel->addAspect(new fphp\Aspect\Aspect($artifact, $fileSpecification));
        $this->tracingLinks[] = new fphp\Artifact\TracingLink(
            "aspect", $artifact, $fileSpecification->getSourcePlace(), $fileSpecification->getTargetPlace());
        $this->logFile->log($artifact, "added aspect at \"{$fileSpecification->getTarget()}\"");
    }

    /**
     * Generates the aspect files and the aspect kernel.
     */
    protected function _generateFiles() {
        if ($this->feature && !$this->isSelectedFeature($this->feature)) {
            $this->logFile->log(null, "did not add aspect kernel because \"$this->feature\" is not selected");
            return;
        }

        parent::_generateFiles();
        $this->files = array_merge($this->files,
                                   $this->aspectKernel->generateFiles($this->class, $this->target));
    }
}

?>