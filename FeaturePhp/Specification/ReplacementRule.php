<?

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

class ReplacementRuleException extends \Exception {}

class ReplacementRule extends fphp\Settings {
    private $cfg;
    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
        $this->cfg = $cfg;
    }

    public function apply($string) {
        if ($this->has("from") && $this->has("to"))
            return str_replace($this->get("from"), $this->get("to"), $string);
        else if ($this->has("assign") && $this->has("to"))
            return str_replace("{{{$this->get("assign")}}}", $this->get("to"), $string);
        else if ($this->has("regex") && $this->has("to"))
            return preg_replace($this->get("regex"), $this->get("to"), $string);
        else
            throw new ReplacementRuleException("invalid replacement rule \"" . json_encode($this->cfg) . "\"");
    }
}

?>