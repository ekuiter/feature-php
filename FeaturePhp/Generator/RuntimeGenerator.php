<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class RuntimeGenerator extends Generator {
    private $class;
    private $target;
    private $getter;
    
    public function __construct($settings) {
        parent::__construct($settings);
        $this->class = $settings->getOptional("class", "Runtime");
        $this->target = $settings->getOptional("target", "{$this->class}.php");
        $this->getter = $settings->getOptional("getter", "hasFeature");
    }

    public static function getKey() {
        return "runtime";
    }

    private function assign($template, $replace, $replacement) {
        return str_replace("{{{$replace}}}", $replacement, $template);
    }

    private function encodeFeatureNames($logFile, $artifacts) {
        $featureNames = array();
        foreach ($artifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $featureNames[] = $featureName;
            $logFile->log($artifact, "added runtime information in \"$this->target\"");
        }
        return str_replace("'", "\'", json_encode($featureNames));
    }

    public function generateFiles() {        
        $logFile = new fphp\File\LogFile("runtime");
        
        $template = file_get_contents(__DIR__ . "/Runtime.php.template");
        $template = $this->assign($template, "class", $this->class);
        $template = $this->assign($template, "getter", $this->getter);
        $template = $this->assign($template, "selectedFeatures",
                                  $this->encodeFeatureNames($logFile, $this->selectedArtifacts));
        $template = $this->assign($template, "deselectedFeatures",
                                  $this->encodeFeatureNames($logFile, $this->deselectedArtifacts));

        return array($logFile, new fphp\File\TextFile($this->target, $template));
    }
}

?>