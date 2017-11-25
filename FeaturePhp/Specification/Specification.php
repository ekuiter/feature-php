<?

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

class SpecificationException extends \Exception {}

abstract class Specification extends fphp\Settings {    
    public function __construct($cfg, $directory = ".") {
        if (is_string($cfg))
            $cfg = array("source" => $cfg);
        if (!is_array($cfg))
            throw new SpecificationException("invalid specification \"" . json_encode($cfg) . "\"");
        if (!array_key_exists("target", $cfg))
            $cfg["target"] = $cfg["source"];

        parent::__construct($cfg, $directory);
    }

    public function getSource() {
        return $this->get("source");
    }

    public function getTarget() {
        return $this->get("target");
    }
}

?>