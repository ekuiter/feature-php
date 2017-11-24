<?

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

class Settings extends fphp\Settings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        $this->setOptional("generators", array());
        $generators = $this->getWith("generators", "is_array");

        foreach ($generators as $key => $generator)
            $this->set("generators", $key, self::getInstance(
                $this->getIn($generators, $key), "\FeaturePhp\Generator\Settings"));

        if (count($this->get("generators")) === 0)
            $this->set("generators", "empty", fphp\Generator\Settings::emptyInstance());
    }
}

?>