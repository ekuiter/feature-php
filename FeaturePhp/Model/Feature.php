<?

namespace FeaturePhp\Model;

class Feature {
    private $name;
    private $description;
    private $mandatory;
    private $alternative;
    private $or;
    private $parent;

    public function __construct($node, $parent = null, $children = null) {
        $this->name = (string) $node["name"];
        $this->description = $node->description ? trim((string) $node->description) : null;
        $this->mandatory = (string) $node["mandatory"] === "true";
        $this->alternative = $node->getName() === "alt";
        $this->or = $node->getName() === "or";
        $this->parent = $parent ? new self($parent) : null;

        if ($children && ($this->alternative || $this->or)) {
            $this->children = array();
            foreach ($children as $child) {
                if (in_array($child->getName(), array("feature", "and", "or", "alt")))
                    $this->children[] = new self($child);
            }
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getMandatory() {
        return $this->mandatory;
    }

    public function getAlternative() {
        return $this->alternative;
    }

    public function getOr() {
        return $this->or;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getChildren() {
        return $this->children;
    }

    public static function findByName($features, $featureName) {
        foreach ($features as $feature)
            if ($feature->getName() === $featureName)
                return $feature;
        return null;
    }

    public static function has($features, $feature) {
        return !!self::findByName($features, $feature->getName());
    }
}