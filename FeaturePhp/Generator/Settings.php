<?php

/**
 * The FeaturePhp\Generator\Settings class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Settings for a generator.
 * The {@see ChunkGenerator} settings follow the structure:
 * - root (object)
 *   - files (array) - see {@see \FeaturePhp\Specification\ChunkSpecification}
 *
 * or, alternatively:
 * - root (object)
 *   - filesIfSelected (array) - chunks to add if the artifact's feature is selected
 *   - filesIfDeselected (array) - chunks to add if the artifact's feature is deselected
 *
 * The {@see CopyGenerator}, {@see CollaborationGenerator} and {@see AspectGenerator}
 * settings inside an artifact's generator settings follow the structure:
 * - root (object)
 *   - files (array) - see {@see \FeaturePhp\Specification\FileSpecification}
 *   - directories (array) - see {@see \FeaturePhp\Specification\DirectorySpecification}
 *
 * The {@see CopyGenerator}, {@see CollaborationGenerator} and {@see AspectGenerator}
 * settings in the product line's generator settings follow the structure:
 * - root (object)
 *   - exclude (array) - file names to exclude globally, useful for files like .gitignore (to exclude a directory, use "&lt;directory&gt;/*")
 *   - featureOrder (array) - array of feature names to determine role ordering,
 *     for collaboration generator only
 *   - feature (string) - a feature that has to be selected to generate the aspect kernel,
 *     for aspect generator only
 *
 * The {@see RuntimeGenerator} settings inside an artifact's generator settings
 * follow the structure:
 * - root (bool) - true to generate runtime information
 *
 * The {@see RuntimeGenerator} settings in the product line's generator settings
 * follow the structure:
 * - root (object)
 *   - class (string) - the runtime class name
 *   - target (string) - the runtime class file target in the generated product
 *   - getter (string) - the runtime class method for getting feature information
 *   - feature (string) - a feature that has to be selected to generate the runtime class
 *
 * The {@see TemplateGenerator} settings follow the structure:
 * - root (object)
 *   - files (array) - see {@see \FeaturePhp\Specification\TemplateSpecification}
 *
 * or, alternatively:
 * - root (object)
 *   - filesIfSelected (array) - rules to add if the artifact's feature is selected
 *   - filesIfDeselected (array) - rules to add if the artifact's feature is deselected
 *
 * All generators additionally accept the following in the product line's generator settings:
 * - root (object)
 *   - logFile (bool) - whether to include a log file in the generated product
 */
class Settings extends fphp\Settings {
    /**
     * Creates settings.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
    }

    /**
     * Creates empty settings.
     * @return Settings
     */
    public static function emptyInstance() {
        return self::fromArray(array());
    }
}

?>