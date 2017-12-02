<?

/**
 * The FeaturePhp\Specification\TemplateSpecification class.
 */

namespace FeaturePhp\Specification;
use \FeaturePhp as fphp;

/**
 * Settings for specifying a text file in which specific parts may be replaced.
 * Template specifications are used by a {@see \FeaturePhp\Generator\TemplateGenerator}
 * to specify a {@see \FeaturePhp\File\TemplateFile} and its rules (see {@see ReplacementRule}).
 * The template specification settings follow the structure:
 * - root (object)
 *   - source (string) - a (relative) path to a template file on the server
 *   - target (string) - the file target in the generated product
 *   - rules (mixed*[]) - rules for replacing parts of the template (see {@see ReplacementRule})
 *
 * mixed* means that a string, object or bool can be given according
 * to {@see \FeaturePhp\Settings::getInstance()}.
 */
class TemplateSpecification extends ExtendSpecification {
    /**
     * Creates a template specification.
     * @param array $cfg a plain settings array
     * @param string $directory the directory the settings apply to
     * @param \FeaturePhp\Artifact\Artifact $artifact
     */
    public function __construct($cfg, $directory = ".", $artifact = null) {
        parent::__construct($cfg, $directory);

        $this->setOptional("rules", array());
        $rules = $this->getWith("rules", "is_array");
        $newRules = array();
        foreach ($rules as $rule) {
            $rule = self::getInstance($rule, "\FeaturePhp\Specification\ReplacementRule");
            $rule->set("artifact", $artifact);
            $newRules[] = $rule;
        }
        $this->set("rules", $newRules);
    }

    /**
     * Returns the template file's rules.
     * @return ReplacementRule[]
     */
    public function getRules() {
        return $this->get("rules");
    }
}

?>