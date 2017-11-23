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
        $logFile = new File("logs/empty.log");

        foreach ($this->selectedArtifacts as $artifact)
            $logFile->append("nothing generated for \"{$artifact->getFeature()->getName()}\"\n");

        return array($logFile);
    }
}

?>