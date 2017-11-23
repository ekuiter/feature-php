<?

namespace FeaturePhp\Generator;

abstract class AbstractGenerator {
    protected $settings;
    protected $selectedArtifacts;
    protected $deselectedArtifacts;
    
    public function __construct($settings) {
        $this->settings = $settings;
        $this->selectedArtifacts = array();
        $this->deselectedArtifacts = array();
    }

    public function getSettings() {
        return $this->settings;
    }
    
    public function addSelectedArtifact($artifact) {
        $this->selectedArtifacts[] = $artifact;
    }

    public function addDeselectedArtifact($artifact) {
        $this->deselectedArtifacts[] = $artifact;
    }

    public function hasArtifacts() {
        return count($this->selectedArtifacts) > 0 || count($this->deselectedArtifacts) > 0;
    }

    abstract public static function getKey();
    abstract public function generateFiles();
}

?>