<?

namespace FeaturePhp\Model;

class XmlModelException extends \Exception {}

class XmlModel {
    private $xml;
    private $root;
    private $rules;

    public function __construct($xml) {
        $this->xml = $xml;
        
        $struct = XmlParser::get($xml, "struct");
        
        if ($struct->count() !== 1)
            throw new XmlModelException("model does not have exactly one root");
        $this->root = $struct->children()[0];

        $this->rules = [];
        foreach (XmlParser::get($xml, "constraints") as $constraint) {
            if ($constraint->getName() !== "rule" || $constraint->count() !== 1)
                throw new XmlModelException("model has invalid constraint");
            $this->rules[] = $constraint->children()[0];
        }
    }

    public static function fromFile($fileName) {        
        return new self(XmlParser::parseFile($fileName));
    }

    public static function fromString($str) {        
        return new self(XmlParser::parseString($str));
    }

    private static function _traverse($node, $parent, $callback) {
        if (in_array($node->getName(), array("feature", "and", "or", "alt")))
            call_user_func($callback, $node, $parent);

        foreach ($node->children() as $child)
            self::_traverse($child, $node, $callback);
    }

    public function traverse($callback) {
        self::_traverse($this->root, null, $callback);
    }

    public function getXml() {
        return $this->xml;
    }

    public function getRoot() {
        return $this->root;
    }

    public function getRules() {
        return $this->rules;
    }
}