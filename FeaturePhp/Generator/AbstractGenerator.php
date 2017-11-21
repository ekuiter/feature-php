<?

namespace FeaturePhp\Generator;

abstract class AbstractGenerator {
    private $settings;
    protected $artifacts;
    
    public function __construct($settings) {
        $this->settings = $settings;
        $this->artifacts = array();
    }

    public function getSettings() {
        return $this->settings;
    }
    
    public function addArtifact($artifact) {
        $this->artifacts[] = $artifact;
    }

    abstract public static function getKey();
    abstract public function generateFiles();
}

?>