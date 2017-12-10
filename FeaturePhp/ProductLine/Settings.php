<?php

/**
 * The FeaturePhp\ProductLine\Settings class.
 */

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

/**
 * Settings for a product line.
 * The {@see ProductLine} settings follow the structure:
 * - root (object)
 *   - model (mixed*) - a FeatureIDE feature model (see {@see \FeaturePhp\Model\XmlModel})
 *   - name (string) - the name of the product line
 *   - defaultConfiguration (mixed*) - a FeatureIDE configuration used by default (see {@see \FeaturePhp\Model\XmlConfiguration})
 *   - artifactDirectory (string) - a directory with a) directories that are named
 *     after features and contain an artifact file and b) files that are named after features
 *   - artifactFile (string) - artifact file used when an artifact directory is specified
 *   - artifacts (mixed*) - an object from artifact keys to {@see \FeaturePhp\Artifact\Settings}
 *   - generators (mixed*) - an object from generator keys to {@see \FeaturePhp\Generator\Settings}
 *
 * mixed* means that a string, object or bool can be given according
 * to {@see \FeaturePhp\Settings::getInstance()}.
 */
class Settings extends fphp\Settings {
    /**
     * Creates settings.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
        
        // instantiate model
        $this->set("model", new fphp\Model\Model(self::getInstance($this->get("model"), "\FeaturePhp\Model\XmlModel")));

        $this->setOptional("name", $this->get("model")->getRootFeature()->getName());
        $this->getWith("name", "is_string");

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
                self::getInstance($artifact, "\FeaturePhp\Artifact\Settings")
            ));

        $this->setOptional("artifactFile", "artifact.json");
        $this->getWith("artifactFile", "is_string");
        
        if ($this->has("artifactDirectory")) {
            $artifactDirectory = $this->getPath(
                $this->getWith("artifactDirectory", function($dir) {
                    return is_string($dir) && is_dir($this->getPath($dir));
                }));

            foreach (scandir($artifactDirectory) as $entry) {
                $entity = fphp\Helper\Path::join($artifactDirectory, $entry);
                if (is_dir($entity) && !fphp\Helper\Path::isDot($entry) && in_array($this->get("artifactFile"), scandir($entity))) {
                    $feature = $this->get("model")->getFeature($entry, true);
                    if ($this->has($feature->getName(), $this->get("artifacts")))
                        throw new fphp\SettingsException("there are multiple settings for \"{$feature->getName()}\"");

                    $this->set("artifacts", $feature->getName(), new fphp\Artifact\Artifact(
                        $feature,
                        fphp\Artifact\Settings::fromFile(
                            fphp\Helper\Path::join($entity, $this->get("artifactFile")))
                    ));
                }
                if (is_file($entity))
                    try {
                        $feature = $this->get("model")->getFeature(pathinfo($entity)["filename"], true);
                        if ($this->has($feature->getName(), $this->get("artifacts")))
                            throw new fphp\SettingsException("there are multiple settings for \"{$feature->getName()}\"");

                        $this->set("artifacts", $feature->getName(), new fphp\Artifact\Artifact(
                            $feature, fphp\Artifact\Settings::fromFile($entity)
                        ));
                    } catch (fphp\Model\ModelException $e) {}
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
            $this->set("generators", $key, self::getInstance($generator, "\FeaturePhp\Generator\Settings"));

        // always exclude the artifact file
        foreach (array("copy", "collaboration", "aspect") as $fileGenerator) {
            if (is_null($this->getOptional("generators", $fileGenerator, null)))
                $this->set("generators", $fileGenerator, fphp\Generator\Settings::emptyInstance());
            
            $this->get("generators", $fileGenerator)->set(
                "exclude", array_merge(
                    $this->get("generators", $fileGenerator)->getOptional("exclude", array()),
                    array($this->get("artifactFile"))));
        }
    }
}

?>