<?

namespace FeaturePhp\Artifact;

class Settings extends \FeaturePhp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        if (array_key_exists("generators", $this->cfg)) {
            $generators = $this->cfg["generators"];
            if (!is_array($generators))
                throw new \FeaturePhp\InvalidSettingsException($generators, "generators");
        } else
            $this->cfg["generators"] = array();

        if (count($this->cfg["generators"]) === 0)
            $this->cfg["generators"]["empty"] = \FeaturePhp\Generator\Settings::emptyInstance();
    }
}

?>