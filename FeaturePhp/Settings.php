<?

namespace FeaturePhp;
use \FeaturePhp as fphp;

class SettingsException extends \Exception {}

class InvalidSettingsException extends SettingsException {
    public function __construct($object, $key) {
        parent::__construct("\"" . json_encode($object) . "\" is not a valid setting for \"$key\"");
    }
}

class NotFoundSettingsException extends SettingsException {
    public function __construct($key) {
        parent::__construct("no settings found for \"$key\"");
    }
}

class Settings {
    private $cfg;
    
    public function __construct($cfg, $directory = ".") {
        if (!is_array($cfg))
            throw new SettingsException("not a valid settings array");
        $this->cfg = $cfg;
        $this->setOptional("directory", $directory);
    }

    public static function fromString($json, $directory = ".") {
        return new static(json_decode($json, true), $directory);
    }

    public static function fromFile($fileName) {
        if (!file_exists($fileName))
            throw new SettingsException("file $fileName does not exist");
        return static::fromString(file_get_contents($fileName), dirname($fileName));
    }

    public static function fromArray($cfg, $directory = ".") {
        return new static($cfg, $directory);
    }
    
    public function getPath($fileName) {
        return fphp\Helper\Path::join($this->cfg["directory"], $fileName);
    }

    public function getDirectory() {
        return $this->cfg["directory"];
    }

    protected function getInstance($object, $klass) {
        if ($object === true)
            $object = array();
        
        if (is_string($object) && method_exists($klass, "fromFile"))
            return $klass::fromFile(fphp\Helper\Path::join($this->cfg["directory"], $object));
        else if (is_array($object) && $this->has("data", $object) && method_exists($klass, "fromString"))
            return $klass::fromString($object["data"], $this->cfg["directory"]);
        else if (is_array($object) && method_exists($klass, "fromArray"))
            return $klass::fromArray($object, $this->cfg["directory"]);
        else
            throw new InvalidSettingsException($object, $klass);
    }

    protected function has($key, $cfg = null) {
        if (!$cfg)
            $cfg = $this->cfg;
        return array_key_exists($key, $cfg);
    }

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

    public function get(/* ... */) {
        $args = func_get_args();
        array_unshift($args, $this->cfg);
        return call_user_func_array(array($this, "_get"), $args);
    }

    public function getIn($cfg/*, ... */) {
        $args = func_get_args();
        return call_user_func_array(array($this, "_get"), $args);
    }

    public function getWith($key, $predicate) {
        $object = $this->get($key);
        if (!call_user_func($predicate, $object))
            throw new fphp\InvalidSettingsException($object, $key);
        return $object;
    }

    public function getOptional(/* ..., */$defaultValue) {
        $args = func_get_args();
        try {
            return call_user_func_array(array($this, "get"), array_slice($args, 0, -1));
        } catch (fphp\NotFoundSettingsException $e) {
            return $args[count($args) - 1];
        }
    }

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

    protected function set(/* ..., */$key, $value) {
        $args = func_get_args();
        eval('$this->_set($this->cfg, $args);');
    }

    protected function setOptional($key, $value) {
        if (!$this->has($key))
            $this->set($key, $value);
    }
}

?>