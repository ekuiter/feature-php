<?

namespace FeaturePhp\ProductLine;

use \FeaturePhp\Model;
use \FeaturePhp\Artifact;

class Settings extends \FeaturePhp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        // instantiate model
        $this->cfg["model"] = $model = new Model\Model(self::getInstance("model", "\FeaturePhp\Model\XmlModel"));

        // instantiate default configuration
        $this->cfg["defaultConfiguration"] = new Model\Configuration(
            $this->cfg["model"],
            array_key_exists("defaultConfiguration", $this->cfg) ?
            self::getInstance("defaultConfiguration", "\FeaturePhp\Model\XmlConfiguration") :
            Model\XmlConfiguration::emptyInstance()
        );

        // instantiate artifacts
        if (array_key_exists("artifacts", $this->cfg)) {
            $artifacts = $this->cfg["artifacts"];
            if (!is_array($artifacts))
                throw new \FeaturePhp\InvalidSettingsException($artifacts, "artifacts");

            foreach ($artifacts as $key => $artifact) {
                $feature = $model->getFeature($key);
                if (!$feature)
                    throw new \FeaturePhp\SettingsException("the model has no feature named \"$key\"");
                
                $this->cfg["artifacts"][$key] = new Artifact\Artifact(
                    $feature, self::getInstance($key, "\FeaturePhp\Artifact\Settings", $artifacts));
            }
        } else
            $this->cfg["artifacts"] = array();

        foreach ($model->getFeatures() as $feature) {
            $key = $feature->getName();
            if (!array_key_exists($key, $this->cfg["artifacts"]))
                $this->cfg["artifacts"][$key] = new Artifact\Artifact(
                    $feature, new Artifact\Settings(array(), $directory));
        }

        // instantiate generator settings
        if (array_key_exists("generators", $this->cfg)) {
            $generators = $this->cfg["generators"];
            if (!is_array($generators))
                throw new \FeaturePhp\InvalidSettingsException($generators, "generators");

            foreach ($generators as $key => $generator) {                
                $this->cfg["generators"][$key] = self::getInstance(
                    $key, "\FeaturePhp\Generator\Settings", $generators);
            }
        } else
            $this->cfg["generators"] = array();
    }
}

?>