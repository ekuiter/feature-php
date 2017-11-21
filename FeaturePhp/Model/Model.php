<?

namespace FeaturePhp\Model;

class Model {
    private $xmlModel;
    private $features;
    private $rootFeature;
    private $constraintSolver;

    public function __construct($xmlModel) {
        $this->xmlModel = $xmlModel;
        $this->features = array();
        $xmlModel->traverse(array($this, "addFeature"));
        $this->rootFeature = $this->features[0];
        $this->constraintSolver = new ConstraintSolver($this);
    }

    public function addFeature($node, $parent) {
        $this->features[] = new Feature($node, $parent, $node->children());
    }

    public function getFeature($featureName) {
        return Feature::findByName($this->features, $featureName);
    }

    public function getXmlModel() {
        return $this->xmlModel;
    }
    
    public function getFeatures() {
        return $this->features;
    }

    public function getRootFeature() {
        return $this->rootFeature;
    }

    public function getConstraintSolver() {
        return $this->constraintSolver;
    }
}