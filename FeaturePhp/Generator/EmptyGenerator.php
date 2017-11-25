<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class EmptyGenerator extends Generator {        
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "empty";
    }

    public function generateFiles() {
        $logFile = new fphp\File\LogFile("empty");

        foreach ($this->selectedArtifacts as $artifact)
            $logFile->log($artifact, "nothing generated");

        return array($logFile);
    }
}

?>