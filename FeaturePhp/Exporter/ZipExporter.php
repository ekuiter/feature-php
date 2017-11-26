<?

namespace FeaturePhp\Exporter;
use \FeaturePhp as fphp;

class ZipExporterException extends \Exception {}

class ZipExporter extends Exporter {
    protected $target;
    
    public function __construct($target) {
        $this->target = $target;
        
        if (!extension_loaded("zip"))
            throw new ZipExporterException("zip extension not loaded, can not use ZipExporter");
    }

    private function open() {
        if (file_exists($this->target) && !unlink($this->target))
            throw new ZipExporterException("could not remove existing zip archive at \"$this->target\"");
        
        $zip = new \ZipArchive();
        if (!$zip->open($this->target, \ZipArchive::CREATE))
            throw new ZipExporterException("could not create zip archive at \"$this->target\"");
        return $zip;
    }

    private function close($zip) {
        if (!$zip->close())
            throw new ZipExporterException("could not save zip archive at \"$this->target\"");
    }
    
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