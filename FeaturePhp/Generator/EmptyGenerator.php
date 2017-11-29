<?

/**
 * The FeaturePhp\Generator\EmptyGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates no files.
 * This generator is implicitly registered with any artifact that specifies no
 * generators at all, i.e. one that has has no influence on the generated product.
 * This way unused artifacts are logged and can be identified easily.
 */
class EmptyGenerator extends Generator {
    /**
     * Creates an empty generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
    }

    /**
     * Returns the empty generator's key.
     * @return string
     */
    public static function getKey() {
        return "empty";
    }

    /**
     * Generates no files, but logs all registered selected artifacts.
     */
    public function _generateFiles() {
        foreach ($this->selectedArtifacts as $artifact)
            $this->logFile->log($artifact, "nothing generated");
    }
}

?>