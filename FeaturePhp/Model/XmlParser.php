<?

namespace FeaturePhp\Model;

class XmlParserException extends \Exception {}

class XmlParser {
    public static function parseString($str) {
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

    public static function parseFile($fileName) {
        if (!file_exists($fileName))
            throw new XmlParserException("file $fileName does not exist");
        
        return self::parseString(file_get_contents($fileName));
    }

    public static function get($node, $tagName, $count = 1) {
        $node = $node->{$tagName};
        if ($node->count() !== $count)
            throw new XmlParserException("xml does not have exactly $count $tagName's");
        return $node[0];
    }
}