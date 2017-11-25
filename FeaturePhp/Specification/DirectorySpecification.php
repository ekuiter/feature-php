<?

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

class DirectorySpecificationException extends \Exception {}

class DirectorySpecification extends Specification {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);        
    }

    public static function fromArray($cfg, $settings) {
        $directorySpecification = new self($cfg, $settings->getDirectory());
        $directorySpecification->set("source", $settings->getPath($directorySpecification->getSource()));
        $directorySpecification->set("baseTarget", $settings->getOptional("target", null));

        if (!is_dir($directorySpecification->getSource()))
            throw new DirectorySpecificationException("directory \"{$directorySpecification->getSource()}\" does not exist");

        return $directorySpecification;
    }
    
    public function getExclude() {
        $this->setOptional("exclude", array());
        return $this->getWith("exclude", "is_array");
    }

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