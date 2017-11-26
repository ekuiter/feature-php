<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

abstract class FileContent {
    abstract public function getSummary();
    abstract public function addToZip($zip, $target);
}

?>