<?

namespace FeaturePhp\Exporter;
use \FeaturePhp as fphp;

class DownloadZipExporterException extends \Exception {}

class DownloadZipExporter extends ZipExporter {
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

    private function remove() {
        if (file_exists($this->target) && !unlink($this->target))
            throw new DownloadZipExporterException("could not remove temporary zip archive at \"$this->target\"");
    }

    private function download($productLineName) {
        $productLineName = str_replace("\"", "", $productLineName);
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