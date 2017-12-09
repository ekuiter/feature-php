<?php

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
     * @var \SimpleXMLElement $xml the underlying XML document
     */
    private $xml = null;

    /**
     * @var string $str the underlying XML string
     */
    private $xmlString = null;

    /**
     * Returns the XML parser's underlying XML document.
     * @return \SimpleXMLElement
     */
    public function getXml() {
        return $this->xml;
    }

    /**
     * Returns the XML parser's underlying XML string.
     * @return string
     */
    public function getXmlString() {
        return $this->xmlString;
    }

    /**
     * Parses an XML string.
     * @param string $str
     * @return \SimpleXMLElement
     */
    public function parseString($str) {
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

        $this->xml = $xml;
        $this->xmlString = $str;
        return $this;
    }

    /**
     * Parses an XML file.
     * @param string $fileName
     * @return \SimpleXMLElement
     */
    public function parseFile($fileName) {
        if (!file_exists($fileName))
            throw new XmlParserException("file $fileName does not exist");
        
        return $this->parseString(file_get_contents($fileName));
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