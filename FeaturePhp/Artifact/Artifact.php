<?

namespace FeaturePhp\Artifact;

class Artifact {
    private $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }

    public function getSettings() {
        return $this->settings;
    }
}

?>