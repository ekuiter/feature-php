<?php

/**
 * The FeaturePhp\Settings class.
 */

namespace FeaturePhp;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Settings class.
 */
class SettingsException extends \Exception {}

/**
 * Exception thrown from the Settings class regarding invalid settings arrays.
 */
class InvalidSettingsException extends SettingsException {
    /**
     * Creates an invalid settings exception.
     * @param mixed $object
     * @param string $key
     */
    public function __construct($object, $key) {
        parent::__construct("\"" . json_encode($object) . "\" is not a valid setting for \"$key\"");
    }
}

/**
 * Exception thrown from the Settings class regarding settings that are not present.
 */
class NotFoundSettingsException extends SettingsException {
    /**
     * Creates a not found settings exception.
     * @param string $key
     */
    public function __construct($key) {
        parent::__construct("no settings found for \"$key\"");
    }
}

/**
 * A general key-value store for user-supplied settings.
 * A {@see \FeaturePhp\ProductLine\ProductLine} and its dependencies can be
 * configured in many ways by the user. The settings class provides a uniform
 * interface for checking, reading and writing individual settings.
 * For a description of the actual settings, see {@see \FeaturePhp\ProductLine\Settings}.
 */
class Settings {
    /**
     * @var array $cfg the internal plain settings array
     */
    private $cfg;

    /**
     * Creates settings.
     * The directory may be used to resolve relative paths in the settings.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        if (!is_array($cfg))
            throw new SettingsException("not a valid settings array");
        $this->cfg = $cfg;
        $this->setOptional("directory", $directory);
    }

    /**
     * Creates settings from a JSON-encoded string.
     * @param string $json
     * @param string $directory the directory the settings apply to
     * @return Settings
     */
    public static function fromString($json, $directory = ".") {
        $json = json_decode($json, true);
        if (is_null($json))
            throw new SettingsException("invalid json");
        return new static($json, $directory);
    }

    /**
     * Creates settings from a JSON-encoded file.
     * @param string $fileName
     * @return Settings
     */
    public static function fromFile($fileName) {
        if (!file_exists($fileName))
            throw new SettingsException("file $fileName does not exist");
        return static::fromString(file_get_contents($fileName), dirname($fileName));
    }

    /**
     * Creates settings.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public static function fromArray($cfg, $directory = ".") {
        return new static($cfg, $directory);
    }

    /**
     * Creates empty settings in the context of a directory.
     * @param string $directory the directory the settings apply to
     */
    public static function inDirectory($directory) {
        return new static(array(), $directory);
    }

    /**
     * Returns a path in the context of the settings' directory.
     * @param string $path
     * @return string
     */
    public function getPath($path) {
        return fphp\Helper\Path::join($this->cfg["directory"], $path);
    }

    /**
     * Returns the settings' directory.
     * @return string
     */
    public function getDirectory() {
        return $this->cfg["directory"];
    }

    /**
     * Creates an instance of a class from a plain settings array.
     * If a string is given, the class is instantiated from a file.
     * If an array("data" => ...) is given, the class is instantiated
     * from a string.
     * If another array is given, the class is instantiated from that
     * array.
     * If true is given, the class is instantiated from an empty array.
     * @param string|array|bool $object
     * @param string $klass
     * @return $class
     */
    public function getInstance($object, $klass) {
        if ($object === true)
            $object = array();
        
        if (is_string($object) && method_exists($klass, "fromFile"))
            return $klass::fromFile($this->getPath($object));
        else if (is_array($object) && $this->has("data", $object) && method_exists($klass, "fromString"))
            return $klass::fromString($object["data"], $this->cfg["directory"]);
        else if (is_array($object) && method_exists($klass, "fromArray"))
            return $klass::fromArray($object, $this->cfg["directory"]);
        else
            throw new InvalidSettingsException($object, $klass);
    }

    /**
     * Returns whether a plain settings array has a key.
     * If no settings array is given, the internal settings array
     * is assumed.
     * @param string $key
     * @param array $cfg
     * @return bool
     */
    protected function has($key, $cfg = null) {
        if (!$cfg)
            $cfg = $this->cfg;
        return array_key_exists($key, $cfg);
    }

    /**
     * Returns a setting in a plain settings array.
     * A setting path can be supplied variadically.
     * @param array $cfg
     * @return mixed
     */
    private function _get($cfg/*, ... */) {
        $args = array_slice(func_get_args(), 1);
        if (count($args) === 0)
            return $cfg;
        else {
            if (!is_array($cfg) || !$this->has($args[0], $cfg))
                throw new NotFoundSettingsException($args[0]);
            $args[0] = $cfg[$args[0]];
            return call_user_func_array(array($this, "_get"), $args);
        }
    }

    /**
     * Returns a setting.
     * A setting path can be supplied variadically.
     * @return mixed
     */
    public function get(/* ... */) {
        $args = func_get_args();
        array_unshift($args, $this->cfg);
        return call_user_func_array(array($this, "_get"), $args);
    }

    /**
     * Returns a setting in a plain settings array.
     * A setting path can be supplied variadically.
     * @param array $cfg
     * @return mixed
     */
    public function getIn($cfg/*, ... */) {
        $args = func_get_args();
        return call_user_func_array(array($this, "_get"), $args);
    }

    /**
     * Returns a setting if a predicate is satisfied.
     * Throws {@see \FeaturePhp\InvalidSettingsException} if the predicate fails.
     * @param string $key
     * @param callable $predicate
     * @return mixed
     */
    public function getWith($key, $predicate) {
        $object = $this->get($key);
        if (!call_user_func($predicate, $object))
            throw new fphp\InvalidSettingsException($object, $key);
        return $object;
    }

    /**
     * Returns an optional setting, defaulting to a value.
     * A setting path can be supplied variadically.
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getOptional(/* ..., */$defaultValue) {
        $args = func_get_args();
        try {
            return call_user_func_array(array($this, "get"), array_slice($args, 0, -1));
        } catch (fphp\NotFoundSettingsException $e) {
            return $args[count($args) - 1];
        }
    }

    /**
     * Sets a setting in a plain settings array.
     * @param array $cfg
     * @param array $args a setting path followed by the setting's new value
     */
    private function _set(&$cfg, $args) {
        if (count($args) === 2) {
            $key = $args[count($args) - 2];
            $value = $args[count($args) - 1];
            $cfg[$key] = $value;
        } else {
            if (!is_array($cfg) || !$this->has($args[0], $cfg))
                throw new NotFoundSettingsException($args[0]);
            // Evil, but I found no other way to pass $cfg as reference via call_user_func_array.
            eval('$this->_set($cfg[$args[0]], array_slice($args, 1));');
        }
    }

    /**
     * Sets a setting.
     * A setting path can be supplied variadically.
     * @param string $key
     * @param mixed $value
     */
    protected function set(/* ..., */$key, $value) {
        $args = func_get_args();
        eval('$this->_set($this->cfg, $args);');
    }

    /**
     * Sets a setting if it is not already set.
     * @param string $key
     * @param mixed $value
     */
    protected function setOptional($key, $value) {
        if (!$this->has($key))
            $this->set($key, $value);
    }
}

?>