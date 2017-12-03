<?

/**
 * The FeaturePhp\Model\XmlConfiguration class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the XmlConfiguration class.
 */
class XmlConfigurationException extends \Exception {}

/**
 * A configuration represented in XML.
 * The XML syntax of FeatureIDE configurations is used.
 * A short valid configuration (for the example model at {@see XmlModel})
 * might look like this:
 * ```
 * <configuration>
 *   <feature automatic="selected" manual="undefined" name="root feature"/>
 * </configuration>
 * ```
 * It is recommended to generate a configuration using FeatureIDE's graphical
 * configuration editor.
 */
class XmlConfiguration {
    /**
     * @var \FeaturePhp\Helper\XmlParser $xmlParser the underlying XML parser
     */
    private $xmlParser;
    
    /**
     * @var \SimpleXMLElement $xml the underlying XML document
     */
    private $xml;

    /**
     * @var string[] $selectedFeatureNames the names of all selected features in the configuration
     */
    private $selectedFeatureNames;

    /**
     * @var string[] $values names of value features associated with their values
     */
    private $values;

    /**
     * Creates an XML configuration.
     * @param \FeaturePhp\Helper\XmlParser $xmlParser
     */
    public function __construct($xmlParser) {
        $this->xmlParser = $xmlParser;
        $this->xml = $xml = $xmlParser->getXml();
        $this->selectedFeatureNames = array();
        $this->values = array();

        foreach ($xml->children() as $child) {
            $featureName = (string) $child["name"];
            if ((string) $child["automatic"] === "selected" || (string) $child["manual"] === "selected")
                $this->selectedFeatureNames[] = $featureName;
            if (!is_null($child["value"]))
                $this->values[$featureName] = (string) $child["value"];
        }
    }

    /**
     * Creates an XML configuration from an XML file.
     * @param string $fileName
     * @return XmlConfiguration
     */
    public static function fromFile($fileName) {        
        return new self((new fphp\Helper\XmlParser())->parseFile($fileName));
    }

    /**
     * Creates an XML configuration from an XML string.
     * @param string $str
     * @param string $directory ignored
     * @return XmlConfiguration
     */
    public static function fromString($str, $directory = null) {        
        return new self((new fphp\Helper\XmlParser())->parseString($str));
    }

    /**
     * Creates an XML configuration from a request variable.
     * For simple usage, the configuration can be read from GET, POST or cookie.
     * A note on security: This function is safe to use in production scenarios
     * (assuming the safety of the SimpleXML parser) because a configuration is
     * always validated against the feature model.
     * @param string $key the variable in the request
     * @param bool $allowEmpty whether to throw an exception when no configuration is present
     * @return XmlConfiguration
     */
    public static function fromRequest($key, $allowEmpty = false) {
        if (empty($_REQUEST[$key]) && !$allowEmpty)
            throw new XmlConfigurationException("no configuration in request");
        else if (empty($_REQUEST[$key]))
            return self::emptyInstance();
        else
            $str = $_REQUEST[$key];
        return new self((new fphp\Helper\XmlParser())->parseString($str));
    }

    /**
     * Creates an empty XML configuration.
     * @return XmlConfiguration
     */
    public static function emptyInstance() {
        return new self((new fphp\Helper\XmlParser())->parseString("<configuration></configuration>"));
    }

    /**
     * Returns the XML configuration's underlying XML parser.
     * @return \FeaturePhp\Helper\XmlParser
     */
    public function getXmlParser() {
        return $this->xmlParser;
    }
    
    /**
     * Returns the XML configuration's underlying XML document.
     * @return \SimpleXMLElement
     */
    public function getXml() {
        return $this->xml;
    }

    /**
     * Returns the XML configuration's selected feature names.
     * @return string[]
     */
    public function getSelectedFeatureNames() {
        return $this->selectedFeatureNames;
    }

    /**
     * Returns the XML configuration's values.
     * @return string[]
     */
    public function getValues() {
        return $this->values;
    }
}