<?

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

class Settings extends fphp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        if (array_key_exists("generators", $this->cfg)) {
            $generators = $this->cfg["generators"];
            if (!is_array($generators))
                throw new fphp\InvalidSettingsException($generators, "generators");

            foreach ($generators as $key => $generator) {                
                $this->cfg["generators"][$key] = self::getInstance(
                    $key, "\FeaturePhp\Generator\Settings", $generators);
            }
        } else
            $this->cfg["generators"] = array();

        if (count($this->cfg["generators"]) === 0)
            $this->cfg["generators"]["empty"] = fphp\Generator\Settings::emptyInstance();
    }
}

?>