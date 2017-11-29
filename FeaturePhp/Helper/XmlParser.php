<?

/**
 * The FeaturePhp\Helper\XmlParser class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the XmlParser class.
 */
class XmlParserException extends \Exception {}

/**
 * Helper class for parsing XML files.
 */
class XmlParser {
    /**
     * Parses an XML string.
     * @param string $str
     * @return \SimpleXMLElement
     */
    public static function parseString($str) {
        if (!extension_loaded("SimpleXML"))
            throw new XmlParserException("SimpleXML extension not loaded, can not use XmlParser");
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($str);
        
        if ($xml === false) {
            $msg = "The following XML errors occured:";
            foreach (libxml_get_errors() as $error)
                $msg .= "\n" . $error->message;
            throw new XmlParserException($msg);
        }
        
        return $xml;
    }

    /**
     * Parses an XML file.
     * @param string $fileName
     * @return \SimpleXMLElement
     */
    public static function parseFile($fileName) {
        if (!file_exists($fileName))
            throw new XmlParserException("file $fileName does not exist");
        
        return self::parseString(file_get_contents($fileName));
    }

    /**
     * Returns a child node for a tag name from an XML node.
     * @param \SimpleXMLElement $node
     * @param string $tagName
     * @param int $count how many child nodes for the tag name are allowed
     * @return \SimpleXMLElement
     */
    public static function get($node, $tagName, $count = 1) {
        $node = $node->{$tagName};
        if ($node->count() !== $count)
            throw new XmlParserException("xml does not have exactly $count $tagName's");
        return $node[0];
    }
}