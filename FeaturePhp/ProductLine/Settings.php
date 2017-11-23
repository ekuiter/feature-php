<?

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

class Settings extends fphp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
        
        // instantiate model
        $this->set("model", new fphp\Model\Model(self::getInstance($this->get("model"), "\FeaturePhp\Model\XmlModel")));

        // instantiate default configuration
        $this->set("defaultConfiguration", new fphp\Model\Configuration(
            $this->get("model"),
            $this->has("defaultConfiguration") ?
            self::getInstance($this->get("defaultConfiguration"), "\FeaturePhp\Model\XmlConfiguration") :
            fphp\Model\XmlConfiguration::emptyInstance()
        ));

        // instantiate artifacts
        $this->setOptional("artifacts", array());
        $artifacts = $this->getWith("artifacts", "is_array");

        foreach ($artifacts as $key => $artifact)                
            $this->set("artifacts", $key, new fphp\Artifact\Artifact(
                $this->get("model")->getFeature($key),
                self::getInstance($this->getIn($artifacts, $key), "\FeaturePhp\Artifact\Settings")
            ));

        $this->setOptional("artifactFile", "artifact.json");
        $this->getWith("artifactFile", "is_string");
        
        if ($this->has("artifactDirectory")) {
            $artifactDirectory = $this->getPath(
                $this->getWith("artifactDirectory", function($dir) {
                    return is_string($dir) && is_dir($this->getPath($dir));
                }));

            foreach (scandir($artifactDirectory) as $entry) {
                $directory = fphp\Helper\Path::join($artifactDirectory, $entry);
                if (is_dir($directory) && !fphp\Helper\Path::isDot($entry) && in_array($this->get("artifactFile"), scandir($directory))) {
                        if ($this->has($entry, $this->get("artifacts")))
                            throw new fphp\SettingsException("there are multiple settings for \"$entry\"");
                        
                        $this->set("artifacts", $entry, new fphp\Artifact\Artifact(
                            $this->get("model")->getFeature($entry),
                            fphp\Artifact\Settings::fromFile(
                                fphp\Helper\Path::join($directory, $this->get("artifactFile")))
                        ));
                    }
            }
        }

        foreach ($this->get("model")->getFeatures() as $feature) {
            $key = $feature->getName();
            if (!$this->has($key, $this->get("artifacts")))
                $this->set("artifacts", $key, new fphp\Artifact\Artifact(
                    $feature, new fphp\Artifact\Settings(array(), $this->getDirectory())));
        }

        // instantiate generator settings
        $this->setOptional("generators", array());
        $generators = $this->getWith("generators", "is_array");
        
        foreach ($generators as $key => $generator)
            $this->set("generators", $key, self::getInstance(
                $this->getIn($generators, $key), "\FeaturePhp\Generator\Settings"));
    }
}

?>