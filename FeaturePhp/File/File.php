<?

/**
 * The FeaturePhp\File\File class.
 */

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

/**
 * A file generated for a product.
 * A file can be generated by a {@see \FeaturePhp\Generator\Generator} to be included
 * by a {@see \FeaturePhp\Exporter\Exporter} in a {@see \FeaturePhp\ProductLine\Product}.
 */
abstract class File {
    /**
     * @var string $fileTarget the file target in the generated product
     */
    protected $fileTarget;

    /**
     * Creates a file.
     * @param string $fileTarget
     */
    public function __construct($fileTarget) {
        $this->fileTarget = fphp\Helper\Path::resolve($fileTarget);
    }

    /**
     * Returns the file's target.
     * @return string
     */
    public function getTarget() {
        return $this->fileTarget;
    }

    /**
     * Returns the file's content.
     * For details see {@see \FeaturePhp\File\FileContent}.
     * @return FileContent
     */
    abstract public function getContent();
}

?>