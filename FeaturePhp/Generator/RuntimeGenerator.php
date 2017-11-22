<?

namespace FeaturePhp\Generator;

class RuntimeGenerator extends AbstractGenerator {
    private $class;
    private $target;
    private $getter;
    
    public function __construct($settings) {
        parent::__construct($settings);
        $this->class = $this->getSettings()->getOptional("class", "RuntimeConfig");
        $this->target = $this->getSettings()->getOptional("target", "{$this->class}.php");
        $this->getter = $this->getSettings()->getOptional("getter", "hasFeature");
    }

    public static function getKey() {
        return "runtime";
    }

    private function assign($template, $replace, $replacement) {
        return str_replace("{{{$replace}}}", $replacement, $template);
    }

    public function generateFiles() {
        $logFile = new File("logs/runtime.log");
        $featureNames = array();

        foreach ($this->artifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $featureNames[] = $featureName;
            $logFile->append("added runtime information for \"$featureName\"\n");
        }

        $template = file_get_contents(__DIR__ . "/Runtime.php.template");
        $template = $this->assign($template, "class", $this->class);
        $template = $this->assign($template, "getter", $this->getter);
        $template = $this->assign($template, "features", str_replace("'", "\'", json_encode($featureNames)));

        return array($logFile, new File($this->target, $template));
    }
}

?>