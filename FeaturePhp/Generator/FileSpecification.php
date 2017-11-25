<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class FileSpecificationException extends \Exception {}

class FileSpecification extends Specification {
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
    }

    public static function fromArray($cfg, $settings) {
        $fileSpecification = new self($cfg, $settings->getDirectory());
        $fileSpecification->set("source", $settings->getPath($fileSpecification->getSource()));
        $fileSpecification->set("target", fphp\Helper\Path::join(
            $settings->getOptional("target", null), $fileSpecification->getTarget()));

        if (!file_exists($fileSpecification->getSource()))
            throw new FileSpecificationException("file \"{$fileSpecification->getSource()}\" does not exist");

        return $fileSpecification;
    }
}

?>