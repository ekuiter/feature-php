<?

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
 * The {@see CopyGenerator} settings inside an artifact's generator settings
 * follow the structure:
 * - root (object)
 *   - files (array) - see {@see \FeaturePhp\Specification\FileSpecification}
 *   - directories (array) - see {@see \FeaturePhp\Specification\DirectorySpecification}
 *
 * The {@see RuntimeGenerator} settings in the product line's generator settings
 * follow the structure:
 * - root (object)
 *   - class (string) - the runtime class name
 *   - target (string) - the runtime class file target in the generated product
 *   - getter (string) - the runtime class method for getting feature information
 *
 * The {@see TemplateGenerator} settings inside an artifact's generator settings
 * follow the structure:
 * - root (object)
 *   - files (array) - see {@see \FeaturePhp\Specification\TemplateSpecification}
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