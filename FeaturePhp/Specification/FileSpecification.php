<?php

/**
 * The FeaturePhp\Specification\FileSpecification class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the FileSpecification class.
 */
class FileSpecificationException extends \Exception {}

/**
 * Settings for specifying a single file.
 * File specifications are used by a {@see \FeaturePhp\Generator\CopyGenerator}
 * to specify a single file (in contrast to a {@see DirectorySpecification}).
 * The file specification settings follow the structure:
 * - root (object)
 *   - source (string) - a (relative) path to a file on the server
 *   - target (string) - the file target in the generated product
 *
 * or, alternatively:
 * - root (string) - specifies source as well as target
 *
 * If either source or target is omitted, they share the same value.
 */
class FileSpecification extends Specification {
    /**
     * Creates a file specification.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
    }

    /**
     * Returns the entity's source relative to its settings.
     * @return string
     */
    public function getRelativeSource() {
        return $this->get("relativeSource");
    }

    /**
     * Creates a file specification from a plain settings array.
     * The settings context is taken into consideration to generate paths
     * relative to the settings.
     * @param array $cfg a plain settings array
     * @param \FeaturePhp\Settings $settings the settings context
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @return FileSpecification
     */
    public static function fromArrayAndSettings($cfg, $settings, $artifact = null) {
        $fileSpecification = new static($cfg, $settings->getDirectory(), $artifact);
        $fileSpecification->set("relativeSource", $fileSpecification->getSource());
        $fileSpecification->set("source", $settings->getPath($fileSpecification->getSource()));
        $fileSpecification->set("target", fphp\Helper\Path::join(
            $settings->getOptional("target", null), $fileSpecification->getTarget()));

        if (!file_exists($fileSpecification->getSource()))
            throw new FileSpecificationException("file \"{$fileSpecification->getSource()}\" does not exist");

        return $fileSpecification;
    }
}

?>