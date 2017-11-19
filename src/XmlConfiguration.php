<?

namespace FeaturePhp;

class XmlConfigurationException extends \Exception {}

class XmlConfiguration {
    private $xml;
    private $selectedFeatureNames;

    public function __construct($xml) {
        $this->xml = $xml;
        $this->selectedFeatureNames = [];

        foreach ($xml->children() as $child)
            if ((string) $child["automatic"] === "selected" || (string) $child["manual"] === "selected")
                $this->selectedFeatureNames[] = (string) $child["name"];
    }

    public static function fromFile($fileName) {        
        return new self(XmlParser::parseFile($fileName));
    }

    public static function fromString($str) {        
        return new self(XmlParser::parseString($str));
    }

    public static function fromRequest($key, $allowEmpty = false) {
        if (empty($_REQUEST[$key]) && !$allowEmpty)
            throw new XmlConfigurationException("no configuration in request");
        else if (empty($_REQUEST[$key]))
            $str = "<configuration></configuration>";
        else
            $str = $_REQUEST[$key];
        return new self(XmlParser::parseString($str));
    }

    public function getXml() {
        return $this->xml;
    }

    public function getSelectedFeatureNames() {
        return $this->selectedFeatureNames;
    }
}