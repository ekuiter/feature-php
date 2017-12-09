<?php

/**
 * The FeaturePhp\Collaboration\Role class.
 */

namespace FeaturePhp\Collaboration;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Role class.
 */
class RoleException extends \Exception {}

/**
 * A role defines a class' responsibilities in a collaboration.
 * As part of a {@see Collaboration}, a role implements part of a feature's functionality.
 * A role may be the base code for a class or a class refinement. Roles are composed using
 * a {@see Composer}.
 */
class Role {
    /**
     * @var \FeaturePhp\Specification\FileSpecification $fileSpecification which file the role refers to
     */
    private $fileSpecification;

    /**
     * @var Collaboration $collaboration which collaboration the role belongs to
     */
    private $collaboration;

    /**
     * Creates a role.
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     * @param Collaboration $collaboration
     */
    public function __construct($fileSpecification, $collaboration) {
        $this->fileSpecification = $fileSpecification;
        $this->collaboration = $collaboration;
    }

    /**
     * Returns which file the role refers to.
     * @return \FeaturePhp\Specification\FileSpecification
     */
    public function getFileSpecification() {
        return $this->fileSpecification;
    }
    
    /**
     * Returns which collaboration the role belongs to.
     * @return Collaboration
     */
    public function getCollaboration() {
        return $this->collaboration;
    }

    /**
     * Returns the kind of the role.
     * This is used to determine the role's {@see Composer}.
     * As of now, the kind is simply the file extension.
     * @return string
     */
    public function getKind() {
        return pathinfo($this->fileSpecification->getSource(), PATHINFO_EXTENSION);
    }

    /**
     * Returns whether two roles are refining the same class.
     * It requires the roles' files to reside in the same containment hierarchies
     * (i.e. their source paths match).
     * This is an equivalence relation and can be used by {@see \FeaturePhp\Helper\Partition}.
     * @param Role $role
     * @return bool
     */
    public function correspondsTo($role) {
        if ($this->fileSpecification->getRelativeSource() === $role->fileSpecification->getRelativeSource()) {
            if ($this->fileSpecification->getTarget() !== $role->fileSpecification->getTarget())
                throw new RoleException("mismatching targets for \"{$this->fileSpecification->getSource()}\"" .
                                        " and \"{$role->fileSpecification->getSource()}\"");
            if ($this->getKind() !== $role->getKind())
                throw new RoleException("mismatching kinds for \"{$this->fileSpecification->getSource()}\"" .
                                        " and \"{$role->fileSpecification->getSource()}\"");
            return true;
        }
        return false;
    }
}

?>