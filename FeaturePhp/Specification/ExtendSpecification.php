<?php

/**
 * The FeaturePhp\Specification\ExtendSpecification class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Settings for specifying an extendable file.
 * Extended specifications are used by a {@see \FeaturePhp\Generator\ExtendGenerator}.
 * For concrete implementations, see {@see ChunkSpecification} or {@see TemplateSpecification}.
 * The extended specification settings follow the structure:
 * - root (object)
 *   - mayCreate (bool) - whether this specification may create the specified file,
 *     if this is false and the file does not exist, the specification is ignored
 */
class ExtendSpecification extends FileSpecification {
    /**
     * Creates an extended specification.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     * @param \FeaturePhp\Artifact\Artifact $artifact
     */
    public function __construct($cfg, $directory = ".", $artifact = null) {
        parent::__construct($cfg, $directory);

        $this->setOptional("mayCreate", false);
        $this->getWith("mayCreate", "is_bool");
    }

    /**
     * Returns whether the specification may create its specified file.
     * @return string
     */
    public function mayCreate() {
        return $this->get("mayCreate");
    }
}

?>