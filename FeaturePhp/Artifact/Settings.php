<?php

/**
 * The FeaturePhp\Artifact\Settings class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * Settings for an artifact.
 * The {@see Artifact} settings follow the structure:
 * - root (object)
 *   - generators (mixed*) - an object from generator keys to {@see \FeaturePhp\Generator\Settings}
 *
 * or, alternatively:
 * - root (object) - an object from generator keys to {@see \FeaturePhp\Generator\Settings}
 *
 * If no generator is specified, the empty generator is added automatically.
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

        $this->setOptional("generators", $cfg);
        $generators = $this->getWith("generators", "is_array");

        foreach ($generators as $key => $generator)
            $this->set("generators", $key, self::getInstance(
                $generator, "\FeaturePhp\Generator\Settings"));

        if (count($this->get("generators")) === 0)
            $this->set("generators", "empty", fphp\Generator\Settings::emptyInstance());
    }
}

?>