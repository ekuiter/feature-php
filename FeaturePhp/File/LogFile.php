<?

namespace FeaturePhp\File;
use \FeaturePhp as fphp;

class LogFile extends TextFile {
    private $logs;
    
    public function __construct($fileTarget) {
        parent::__construct("logs/$fileTarget.log");
        $this->logs = array();
    }

    public function log($artifact, $content) {
        $this->logs[] = array($artifact, $content);
    }

    public function getContent() {
        $content = "";
        $maxLen = 0;

        foreach ($this->logs as $log)
            if ($log[0]) {
                $name = $log[0]->getFeature()->getName();
                $maxLen = strlen($name) > $maxLen ? strlen($name) : $maxLen;
            }
        
        foreach ($this->logs as $log)
            $content .= sprintf("%-{$maxLen}s | $log[1]\n", $log[0] ? $log[0]->getFeature()->getName() : "");
        
        return new TextFileContent($content);
    }
}

?>