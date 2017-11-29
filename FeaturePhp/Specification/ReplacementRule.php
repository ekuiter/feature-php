<?

/**
 * The FeaturePhp\Specification\ReplacementRule class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the ReplacementRule class.
 */
class ReplacementRuleException extends \Exception {}

/**
 * Settings for specifying a replacement rule in a template specification.
 * Replacement rules are used inside a {@see TemplateSpecification} to specify
 * rules for replacing parts of a {@see \FeaturePhp\File\TemplateFile}.
 * The replacement rules settings follow the structure:
 * - root (object)
 *   - from (string) - a string in the template file (exact match)
 *   - to (string) - a replacement string
 *
 * or, alternatively:
 * - root (object)
 *   - assign (string) - a variable in the template file with the form {{assign}}
 *   - to (string) - a replacement string

 * or, alternatively:
 * - root (object)
 *   - regex (string) - a regular expression
 *   - to (string) - a replacement string
 */
class ReplacementRule extends fphp\Settings {
    /**
     * @var array $cfg a plain settings array
     */
    private $cfg;

    /**
     * Creates a replacement rule.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     */
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
        $this->cfg = $cfg;
    }

    /**
     * Applies a replacement rule to a string.
     * Possible matching mechanisms include exact matches, Handlebars-style
     * template variables and regular expressions.
     * @param string $string
     * @return string
     */
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