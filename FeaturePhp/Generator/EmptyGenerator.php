<?

namespace FeaturePhp\Generator;

class EmptyGenerator extends AbstractGenerator {        
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "empty";
    }

    public function generateFiles() {
        $file = new File("empty.log");

        foreach ($this->artifacts as $artifact)
            $file->append("nothing generated for \"{$artifact->getFeature()->getName()}\"\n");

        return array($file);
    }
}

?>