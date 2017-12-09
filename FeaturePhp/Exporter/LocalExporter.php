<?php

/**
 * The FeaturePhp\Exporter\LocalExporter class.
 */

namespace FeaturePhp\Exporter;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the LocalExporter class.
 */
class LocalExporterException extends \Exception {}

/**
 * Exports products to the local filesystem.
 * This {@see Exporter} takes a product's generated files and copies them to
 * a directory on the server.
 */
class LocalExporter extends Exporter {
    /**
     * @var string $target the target directory on the server
     */
    protected $target;

    /**
     * Creates a local exporter.
     * @param string $target
     * @param bool $overwrite whether an existing target may be discarded
     */
    public function __construct($target, $overwrite = false) {
        if (file_exists($target) && $overwrite)
            fphp\Helper\Path::removeDirectory($target);
        if (file_exists($target))
            throw new LocalExporterException("target directory already exists");
        $this->target = $target;
    }

    /**
     * Exports a product in the local filesystem.
     * Every generated file is copied to the filesystem at its target path.
     * @param \FeaturePhp\ProductLine\Product $product
     */
    public function export($product) {
        $files = $product->generateFiles();
        mkdir($this->target, 0777, true);
        
        foreach ($files as $file)
            if (!$file->getContent()->copy(fphp\Helper\Path::join($this->target, $file->getTarget())))
                throw new LocalExporterException("could not copy file \"{$file->getTarget()}\"");
    }
}

?>