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

    public function _generateFiles() {
        foreach ($this->selectedArtifacts as $artifact)
            $this->logFile->log($artifact, "nothing generated");
    }
}

?>