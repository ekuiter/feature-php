<?php

/**
 * The FeaturePhp\Model\XmlModel class.
 */

namespace FeaturePhp\Model;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the XmlModel class.
 */
class XmlModelException extends \Exception {}

/**
 * A feature model represented in XML.
 * The XML syntax of FeatureIDE feature models is used.
 * A short valid model might look like this:
 * ```
 * <featureModel>
 *	   <struct>
 *		<and abstract="true" mandatory="true" name="root feature"></and>
 *	   </struct>
 *	 <constraints></constraints>
 * </featureModel>
 * ```
 * It is recommended to generate a model using FeatureIDE's graphical
 * feature model editor.
 */
class XmlModel {
    /**
     * @var \FeaturePhp\Helper\XmlParser $xmlParser the underlying XML parser
     */
    private $xmlParser;

    /**
     * @var \SimpleXMLElement $xml the underlying XML document
     */
    private $xml;

    /**
     * @var \SimpleXMLElement $root the XML element containing the root feature
     */
    private $root;

    /**
     * @var \SimpleXMLElement[] $rules XML elements containing cross-tree constraints
     */
    private $rules;

    /**
     * Creates an XML feature model.
     * @param \FeaturePhp\Helper\XmlParser $xmlParser
     */
    public function __construct($xmlParser) {
        $this->xmlParser = $xmlParser;
        $this->xml = $xml = $xmlParser
                   ->validate("vendor/ekuiter/feature-schema/model.xsd")
                   ->getXml();
        
        $struct = fphp\Helper\XmlParser::get($xml, "struct");
        
        if ($struct->count() !== 1)
            throw new XmlModelException("model does not have exactly one root");
        $this->root = $struct->children()[0];

        $this->rules = array();
        foreach (fphp\Helper\XmlParser::get($xml, "constraints") as $constraint) {
            if ($constraint->getName() !== "rule" || $constraint->count() !== 1)
                throw new XmlModelException("model has invalid constraint");
            $this->rules[] = $constraint->children()[0];
        }
    }

    /**
     * Creates an XML feature model from an XML file.
     * @param string $fileName
     * @return XmlModel
     */
    public static function fromFile($fileName) {        
        return new self((new fphp\Helper\XmlParser())->parseFile($fileName));
    }

    /**
     * Creates an XML feature model from an XML string.
     * @param string $str
     * @param string $directory ignored
     * @return XmlModel
     */
    public static function fromString($str, $directory = null) {        
        return new self((new fphp\Helper\XmlParser())->parseString($str));
    }

    /**
     * Traverses a part of the XML feature model's tree.
     * This is a preorder traversal (in particular, the root is visited first).
     * @param \SimpleXMLElement $node
     * @param \SimpleXMLElement $parent
     * @param callable callback called with the current node and its parent
     */
    private static function _traverse($node, $parent, $callback) {
        if (in_array($node->getName(), array("feature", "and", "or", "alt")))
            call_user_func($callback, $node, $parent);

        foreach ($node->children() as $child)
            self::_traverse($child, $node, $callback);
    }

    /**
     * Traverses the whole XML feature model's tree.
     * @param callable $callback called with the current node and its parent
     */
    public function traverse($callback) {
        self::_traverse($this->root, null, $callback);
    }

    /**
     * Returns the XML feature model's underlying XML parser.
     * @return \FeaturePhp\Helper\XmlParser
     */
    public function getXmlParser() {
        return $this->xmlParser;
    }

    /**
     * Returns the XML feature model's underlying XML document.
     * @return \SimpleXMLElement
     */
    public function getXml() {
        return $this->xml;
    }

    /**
     * Returns the XML feature model's root feature element.
     * @return \SimpleXMLElement
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * Returns the XML feature model's cross-tree constraint elements.
     * @return \SimpleXMLElement[]
     */
    public function getRules() {
        return $this->rules;
    }
}