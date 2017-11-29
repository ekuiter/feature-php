<?

/**
 * The FeaturePhp\Exporter\ZipExporter class.
 */

namespace FeaturePhp\Exporter;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the ZipExporter class.
 */
class ZipExporterException extends \Exception {}

/**
 * Exports products as ZIP archives.
 * This {@see Exporter} takes a product's generated files and bundles them up in a ZIP archive
 * on the server. To download the file, see {@see DownloadZipExporter}.
 */
class ZipExporter extends Exporter {
    /**
     * @var string $target the ZIP archive target file on the server
     */
    protected $target;

    /**
     * Creates a ZIP exporter.
     * @param string $target
     */
    public function __construct($target) {
        $this->target = $target;
        
        if (!extension_loaded("zip"))
            throw new ZipExporterException("zip extension not loaded, can not use ZipExporter");
    }

    /**
     * Opens and returns the targeted ZIP archive.
     * @return \ZipArchive
     */
    private function open() {
        if (file_exists($this->target) && !unlink($this->target))
            throw new ZipExporterException("could not remove existing zip archive at \"$this->target\"");
        
        $zip = new \ZipArchive();
        if (!$zip->open($this->target, \ZipArchive::CREATE))
            throw new ZipExporterException("could not create zip archive at \"$this->target\"");
        return $zip;
    }

    /**
     * Closes a ZIP archive.
     * @param \ZipArchive $zip
     */
    private function close($zip) {
        if (!$zip->close())
            throw new ZipExporterException("could not save zip archive at \"$this->target\"");
    }

    /**
     * Exports a product as a ZIP archive.
     * Every generated file is added to the archive at its target path. If there are no files,
     * a NOTICE file is generated because \ZipArchive does not support saving an empty ZIP archive.
     * The archive's root directory has the product line's name.
     * @param \FeaturePhp\ProductLine\Product $product
     */
    public function export($product) {
        $productLineName = $product->getProductLine()->getName();
        $files = $product->generateFiles();
        $zip = $this->open();
        
        if (count($files) === 0)
            $files[] = new fphp\File\TextFile("NOTICE", "No files were generated.");
        
        foreach ($files as $file)
            if (!$file->getContent()->addToZip($zip, fphp\Helper\Path::join($productLineName, $file->getTarget())))
                throw new ZipExporterException("could not add file to zip archive at \"$this->target\"");
        
        $this->close($zip);
    }
}

?>