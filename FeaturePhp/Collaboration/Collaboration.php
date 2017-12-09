<?

/**
 * The FeaturePhp\Collaboration\Collaboration class.
 */

namespace FeaturePhp\Collaboration;
use \FeaturePhp as fphp;

/**
 * A collaboration is a set of cooperating roles.
 * A collaboration consists of all roles ({@see Role}) needed to implement a feature.
 * As such, it corresponds to a {@see \FeaturePhp\Artifact\Artifact}
 * (every artifact may have exactly one collaboration).
 */
class Collaboration {
    /**
     * @var \FeaturePhp\Artifact\Artifact $artifact the collaboration's corresponding artifact
     */
    private $artifact;

    /**
     * @var Role[] $roles the collaboration's roles
     */
    private $roles;

    /**
     * Creates a collaboration.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     */
    public function __construct($artifact) {
        $this->artifact = $artifact;
        $this->roles = array();
    }

    /**
     * Returns the collaboration's corresponding artifact.
     * @return \FeaturePhp\Artifact\Artifact
     */
    public function getArtifact() {
        return $this->artifact;
    }

    /**
     * Returns the collaboration's roles.
     * @return Role[]
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * Adds a role to a collaboration.
     * This is expected to be called only be a {@see \FeaturePhp\Generator\CollaborationGenerator}.
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    public function addRoleFromFileSpecification($fileSpecification) {
        $this->roles[] = new Role($fileSpecification, $this);
    }

    /**
     * Finds a collaboration by its artifact in a list of collaborations.
     * @param Collaboration[] $collaborations
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @return Collaboration
     */
    public static function findByArtifact($collaborations, $artifact) {
        return fphp\Helper\_Array::findByKey($collaborations, "getArtifact", $artifact);
    }
}

?>