<?

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

class Settings extends fphp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);

        // instantiate model
        $this->cfg["model"] = $model = new fphp\Model\Model(self::getInstance("model", "\FeaturePhp\Model\XmlModel"));

        // instantiate default configuration
        $this->cfg["defaultConfiguration"] = new fphp\Model\Configuration(
            $this->cfg["model"],
            array_key_exists("defaultConfiguration", $this->cfg) ?
            self::getInstance("defaultConfiguration", "\FeaturePhp\Model\XmlConfiguration") :
            fphp\Model\XmlConfiguration::emptyInstance()
        );

        // instantiate artifacts
        if (array_key_exists("artifacts", $this->cfg)) {
            $artifacts = $this->cfg["artifacts"];
            if (!is_array($artifacts))
                throw new fphp\InvalidSettingsException($artifacts, "artifacts");

            foreach ($artifacts as $key => $artifact) {
                $feature = $model->getFeature($key);
                if (!$feature)
                    throw new fphp\SettingsException("the model has no feature named \"$key\"");
                
                $this->cfg["artifacts"][$key] = new fphp\Artifact\Artifact(
                    $feature, self::getInstance($key, "\FeaturePhp\Artifact\Settings", $artifacts));
            }
        } else
            $this->cfg["artifacts"] = array();

        if (array_key_exists("artifactFile", $this->cfg)) {
            $artifactFile = $this->cfg["artifactFile"];
            if (!is_string($artifactFile))
                throw new fphp\InvalidSettingsException($artifactFile, "artifactFile");
        } else
            $this->cfg["artifactFile"] = "artifact.json";
        
        if (array_key_exists("artifactDirectory", $this->cfg)) {
            $artifactDirectory = $this->cfg["artifactDirectory"];
            if (!is_string($artifactDirectory) || !is_dir($artifactDirectory = $this->getPath($artifactDirectory)))
                throw new fphp\InvalidSettingsException($artifactDirectory, "artifactDirectory");

            foreach (scandir($artifactDirectory) as $entry) {
                if (is_dir($directory = fphp\Helper\Path::join($artifactDirectory, $entry)) && $entry !== "." && $entry !== "..")
                    if (in_array($this->cfg["artifactFile"], scandir($directory))) {
                        $feature = $model->getFeature($entry);
                        if (!$feature)
                            throw new fphp\SettingsException("the model has no feature named \"$entry\"");
                        if (array_key_exists($entry, $this->cfg["artifacts"]))
                            throw new fphp\SettingsException("there are multiple settings for \"$entry\"");
                        
                        $this->cfg["artifacts"][$entry] = new fphp\Artifact\Artifact(
                            $feature, fphp\Artifact\Settings::fromFile(fphp\Helper\Path::join($directory, $this->cfg["artifactFile"])));
                    }
            }
        }

        foreach ($model->getFeatures() as $feature) {
            $key = $feature->getName();
            if (!array_key_exists($key, $this->cfg["artifacts"]))
                $this->cfg["artifacts"][$key] = new fphp\Artifact\Artifact(
                    $feature, new fphp\Artifact\Settings(array(), $directory));
        }

        // instantiate generator settings
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
    }
}

?>