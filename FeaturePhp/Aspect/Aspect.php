<?php

/**
 * The FeaturePhp\Aspect\Aspect class.
 */

namespace FeaturePhp\Aspect;
use \FeaturePhp as fphp;

/**
 * An aspect implements a crosscutting concern.
 * Similar to a {@see \FeaturePhp\Collaboration\Role} in collaboration-based design,
 * an aspect implements part of a feature's functionality. Typically, this functionality
 * concerns many other features (crosscutting concerns, e.g. Logging, Cache, Encryption ...).
 * An aspect encapsulates this behaviour using pointcuts and advices.
 * All aspects are registered in a {@see AspectKernel}.
 */
class Aspect {
    /**
     * @var \FeaturePhp\Artifact\Artifact $artifact the aspect's corresponding artifact
     */
    private $artifact;

    /**
     * @var \FeaturePhp\Specification\FileSpecification $fileSpecification which file the aspect refers to
     */
    private $fileSpecification;

    /**
     * Creates an aspect.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    public function __construct($artifact, $fileSpecification) {
        $this->artifact = $artifact;
        $this->fileSpecification = $fileSpecification;
    }

    /**
     * Returns the aspect's corresponding artifact.
     * @return \FeaturePhp\Artifact\Artifact
     */
    public function getArtifact() {
        return $this->artifact;
    }

    /**
     * Returns which file the aspect refers to.
     * @return \FeaturePhp\Specification\FileSpecification
     */
    public function getFileSpecification() {
        return $this->fileSpecification;
    }

    /**
     * Returns the aspect's stored file.
     * @return \FeaturePhp\File\StoredFile
     */
    public function getStoredFile() {
        return fphp\File\StoredFile::fromSpecification($this->fileSpecification);
    }

    /**
     * Returns the path of the aspect's target file relative to the aspect kernel's target file.
     * @param string $aspectKernelTarget
     * @return string
     */
    public function getRelativeFileTarget($aspectKernelTarget) {
        return fphp\Helper\Path::join(fphp\Helper\Path::rootPath(dirname($aspectKernelTarget)),
                                      $this->fileSpecification->getTarget());
    }

    /**
     * Returns the aspect's class name.
     * For this, the aspect's file has to be parsed.
     * @return string
     */
    public function getClassName() {
        $fileSource = $this->fileSpecification->getSource();
        return (new fphp\Helper\PhpParser())->parseFile($fileSource)->getExactlyOneClass($fileSource)->name;
    }
}

?>