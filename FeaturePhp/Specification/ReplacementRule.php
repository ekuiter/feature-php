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
 * Exception thrown from the ReplacementRule class regarding invalid settings arrays.
 */
class InvalidReplacementRuleException extends ReplacementRuleException {
    /**
     * Creates an invalid replacement rule exception.
     * @param mixed $cfg
     */
    public function __construct($cfg) {
        parent::__construct("invalid replacement rule \"" . json_encode($cfg) . "\"");
    }
}

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

 * Instead of "to":
 * - "eval" can be specified to evaluate arbitrary PHP code that returns a replacement string
 * - "value" can be specified to use the value of a {@see \FeaturePhp\Model\ValueFeature}
 *   as the replacement string. To use this, you need to set the configuration
 *   with {@see setConfiguration()}. If "value" is true, just uses the supplied value.
 *   If it is "integer", an integer value is expected.
 *   If it is any other string, it is evaluated as PHP code with $value defined (returning true
 *   uses the value, false indicates an invalid value and a string overrides the value).
 */
class ReplacementRule extends fphp\Settings {
    /**
     * @var array $cfg a plain settings array
     */
    private $cfg;

    /**
     * @var \FeaturePhp\Model\Configuration $configuration the configuration used to get feature values
     */
    private static $configuration = null;

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
     * Returns the configuration used to get feature values.
     * @return \FeaturePhp\Model\Configuration
     */
    private static function getConfiguration() {
        if (is_null(self::$configuration))
            throw new ReplacementRuleException("to use value rules, set replacement rule configuration");
        return self::$configuration;
    }

    /**
     * Sets the configuration used to get feature values.
     * @param \FeaturePhp\Model\Configuration $configuration
     */
    public static function setConfiguration($configuration) {
        self::$configuration = $configuration;
    }

    /**
     * Returns the rule's replacement string.
     * This may call eval which seems evil, but only the product line
     * administrator can use this in a configuration file.
     * @return string
     */
    private function getReplacementString() {
        if ($this->has("to"))
            return $this->get("to");
        else if ($this->has("eval"))
            return eval($this->get("eval"));
        else if ($this->has("value")) {
            $feature = $this->get("artifact")->getFeature();
            $value = self::getConfiguration()->getValue($feature);
            $code = $this->get("value");
            if ($code === true)
                $code = 'return $value;';
            if ($code === "integer")
                $code = 'if (is_numeric($value)) return intval($value); else return false;';
            $replacementString = eval($code);
            if ($replacementString === false)
                throw new ReplacementRuleException("invalid value for \"{$feature->getName()}\"");
            if ($replacementString === true)
                $replacementString = $value;
            return $replacementString;
        } else
            throw new InvalidReplacementRuleException($this->cfg);
    }

    /**
     * Applies a replacement rule to a string.
     * Possible matching mechanisms include exact matches, Handlebars-style
     * template variables and regular expressions.
     * @param string $string
     * @return string
     */
    public function apply($string) {
        if ($this->has("from"))
            return str_replace($this->get("from"), $this->getReplacementString(), $string);
        else if ($this->has("assign"))
            return str_replace("{{{$this->get("assign")}}}", $this->getReplacementString(), $string);
        else if ($this->has("regex"))
            return preg_replace($this->get("regex"), $this->getReplacementString(), $string);
        else
            throw new InvalidReplacementRuleException($this->cfg);
    }
}

?>