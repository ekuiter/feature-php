<?php

/**
 * The FeaturePhp\Specification\DirectorySpecification class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the DirectorySpecification class.
 */
class DirectorySpecificationException extends \Exception {}

/**
 * Settings for specifying a directory recursively.
 * Directory specifications are used by a {@see \FeaturePhp\Generator\CopyGenerator}
 * to specify all files in a directory tree.
 * The directory specification settings follow the structure:
 * - root (object)
 *   - source (string) - a (relative) path to a directory on the server
 *   - target (string) - the directory target in the generated product
 *   - exclude (string[]) - files to exclude from the specification
 *
 * or, alternatively:
 * - root (string) - specifies source as well as target
 *
 * If either source or target is omitted, they share the same value.
 */
class DirectorySpecification extends Specification {
    /**
     * Creates a directory specification.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);        
    }

    /**
     * Creates a directory specification from a plain settings array.
     * The settings context is taken into consideration to generate paths
     * relative to the settings.
     * @param array $cfg a plain settings array
     * @param \FeaturePhp\Settings $settings the settings context
     * @return DirectorySpecification
     */
    public static function fromArrayAndSettings($cfg, $settings) {
        $directorySpecification = new self($cfg, $settings->getDirectory());
        $directorySpecification->set("source", $settings->getPath($directorySpecification->getSource()));
        $directorySpecification->set("baseTarget", $settings->getOptional("target", null));

        if (!is_dir($directorySpecification->getSource()))
            throw new DirectorySpecificationException("directory \"{$directorySpecification->getSource()}\" does not exist");

        return $directorySpecification;
    }

    /**
     * Returns the list of excluded files.
     * @return string[]
     */
    public function getExclude() {
        $this->setOptional("exclude", array());
        return $this->getWith("exclude", "is_array");
    }

    /**
     * Returns the file specifications this directory specification applies to.
     * The entire source directory tree is considered, except for excluded files.
     * @return FileSpecification[]
     */
    public function getFileSpecifications() {
        $fileSpecifications = array();
        
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getSource())) as $entry)
            if (!fphp\Helper\Path::isDot($entry)) {
                $fileSource = $entry->getPathName();
                $relativeFileTarget = fphp\Helper\Path::stripBase(
                    realpath($fileSource), realpath($this->getSource()));
                if (in_array($relativeFileTarget, $this->getExclude()))
                    continue;
                        
                $fileTarget = fphp\Helper\Path::join(
                    $this->get("baseTarget"), fphp\Helper\Path::join($this->getTarget(), $relativeFileTarget));
                $fileSpecifications[] = new FileSpecification(
                    array("source" => $fileSource, "target" => $fileTarget), $this->getDirectory());
            }
        
        return $fileSpecifications;
    }
}

?>