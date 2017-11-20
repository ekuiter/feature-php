<?

namespace FeaturePhp\ProductLine;

use \FeaturePhp\Model;

class SettingsException extends \Exception {}

class Settings {
    private $cfg;
    
    public function __construct($cfg, $directory = ".") {
        if (!is_array($cfg))
            throw new SettingsException("not a valid configuration array");
        $this->cfg = $cfg;

        if (!array_key_exists("directory", $cfg))
            $this->cfg["directory"] = $directory;

        $this->cfg["model"] = new Model\Model(self::getInstance("model", "\FeaturePhp\Model\XmlModel"));

        $this->cfg["defaultConfiguration"] = new Model\Configuration(
            $this->cfg["model"],
            array_key_exists("defaultConfiguration", $cfg) ?
            self::getInstance("defaultConfiguration", "\FeaturePhp\Model\XmlConfiguration") :
            Model\XmlConfiguration::emptyInstance()
        );
    }

    public static function fromString($json, $directory = ".") {
        return new self(json_decode($json, true), $directory);
    }

    public static function fromFile($fileName) {
        if (!file_exists($fileName))
            throw new XmlParserException("file $fileName does not exist");
        return self::fromString(file_get_contents($fileName), dirname($fileName));
    }

    public static function fromArray($cfg, $directory = ".") {
        return new self($cfg, $directory);
    }

    // https://stackoverflow.com/q/1091107
    private static function joinPaths($leftHandSide, $rightHandSide) { 
        return rtrim($leftHandSide, '/') .'/'. ltrim($rightHandSide, '/'); 
    }

    private function getInstance($key, $klass) {
        if (!array_key_exists($key, $this->cfg))
            throw new SettingsException("no configuration found for \"$key\"");
        $object = $this->cfg[$key];
        if (is_string($object) && method_exists($klass, "fromFile"))
            return $klass::fromFile(self::joinPaths($this->cfg["directory"], $object));
        else if (is_array($object) && array_key_exists("data", $object) && method_exists($klass, "fromString"))
            return $klass::fromString($object["data"]);
        else if (is_array($object) && method_exists($klass, "fromArray"))
            return $klass::fromArray($object);
        else
            throw new SettingsException("\"" . json_encode($object) . "\" is not a valid configuration for \"$klass\"");
    }

    private function _get($cfg) {
        $args = array_slice(func_get_args(), 1);
        if (count($args) === 0)
            return $cfg;
        else {
            if (!is_array($cfg) || !array_key_exists($args[0], $cfg))
                throw new SettingsException("no configuration found for \"$args[0]\"");
            $args[0] = $cfg[$args[0]];
            return call_user_func_array(array($this, "_get"), $args);
        }
    }

    public function get() {
        $args = func_get_args();
        array_unshift($args, $this->cfg);
        return call_user_func_array(array($this, "_get"), $args);
    }
}

?>