<?php

/**
 * The FeaturePhp\Exporter\DownloadZipExporter class.
 */

namespace FeaturePhp\Exporter;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the DownloadZipExporter class.
 */
class DownloadZipExporterException extends \Exception {}

/**
 * Exports products as ZIP archives and offers them for downloading.
 * This {@see ZipExporter} exports the product to a temporary ZIP archive, offers it for downloading
 * and finally removes the archive. It is suited to implement a "self-service" product generation
 * service for the end user.
 */
class DownloadZipExporter extends ZipExporter {
    /**
     * Creates a ZIP exporter for downloading.
     * @param string|null $directory A directory suited for storing temporary files,
     * if NULL, the global temporary directory is used. Writing permissions are required.
     * @param string $prefix A prefix to assign to temporary files. Omitted in usual cases.
     */
    public function __construct($directory = null, $prefix = "feature-php-") {
        if (!$directory)
            $directory = sys_get_temp_dir();
        if (!is_dir($directory))
            throw new DownloadZipExporterException("directory \"$directory\" does not exist");
        
        $target = tempnam($directory, $prefix);
        if (!$target)
            throw new DownloadZipExporterException("could not create temporary zip archive in \"$directory\"");
        
        parent::__construct($target);
    }

    /**
     * Removes the temporary ZIP archive.
     */
    private function remove() {
        if (file_exists($this->target) && !unlink($this->target))
            throw new DownloadZipExporterException("could not remove temporary zip archive at \"$this->target\"");
    }

    /**
     * Downloads the temporary ZIP archive.
     * This only works when no headers and no output have been sent yet, otherwise users receive
     * corrupted ZIP files.
     * @param string $productLineName the name of the product line, used as the ZIP archive file name
     */
    private function download($productLineName) {
        $productLineName = str_replace("\"", "", $productLineName);
        if (headers_sent())
            throw new DownloadZipExporterException("could download zip archive");
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"$productLineName.zip\"");
        set_time_limit(0);
        $file = @fopen($this->target, "rb");
        while(!feof($file)) {
            print(@fread($file, 1024*8));
            ob_flush();
            flush();
        }
    }

    /**
     * Exports a product as a ZIP archive and offers it for downloading.
     * This only works when no headers and no output have been sent yet.
     * Note that any occurring errors are ignored so that the downloaded archive will not be
     * corrupted. There is currently no way to obtain error information in this case because
     * feature-php does not have an external log file.
     * @param \FeaturePhp\ProductLine\Product $product
     */
    public function export($product) {
        try {
            parent::export($product);
            $this->download($product->getProductLine()->getName());
        } catch (\Exception $e) {}

        try {
            $this->remove();
        } catch (\Exception $e) {}
    }
}

?>