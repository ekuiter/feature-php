<?php

/**
 * The FeaturePhp\Helper\Logic class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Helper class for propositional logic.
 * For simplicity, the logic is not general, but operates directly on lists of features
 * (see {@see \FeaturePhp\Model\Feature} and {@see \FeaturePhp\Model\ConstraintSolver}).
 * A formula is expressed by nested calls to the connectives, e.g.
 * `Logic::equiv(Logic::is(...), Logic::is(...))`.
 * Every formula (a feature constraint) is a closure expecting a variable assignment
 * (a list of features).
 * This way a complex formula can be built, stored and later evaluated for a given
 * variable assignment.
 */
class Logic {
    /**
     * Returns a formula that tests for the presence of a feature.
     * @param Feature $feature
     * @return callable
     */
    public static function is($feature) {
        return function ($features) use ($feature) {
            return fphp\Model\Feature::has($features, $feature);
        };
    }

    /**
     * Returns a formula that is the negation of another formula.
     * @param callable $constraint
     * @return callable
     */
    public static function not($constraint) {
        return function ($features) use ($constraint) {
            return !$constraint($features);
        };
    }

    /**
     * Returns a formula that is the conjunction of other formulas.
     * Formulas can be supplied variadically.
     * @return callable
     */
    public static function _and(/* ... */) {
        $args = func_get_args();
        return function ($features) use ($args) {
            $acc = true;
            foreach ($args as $arg)
                $acc = $acc && $arg($features);
            return $acc;
        };
    }

    /**
     * Returns a formula that is the disjunction of other formulas.
     * Formulas can be supplied variadically.
     * @return callable
     */
    public static function _or(/* ... */) {
        $args = func_get_args();
        return function ($features) use ($args) {
            $acc = false;
            foreach ($args as $arg)
                $acc = $acc || $arg($features);
            return $acc;
        };
    }

    /**
     * Returns a formula that is the biconditional of two formulas.
     * @param callable $constraintA
     * @param callable $constraintB
     * @return callable
     */
    public static function equiv($constraintA, $constraintB) {
        return function ($features) use ($constraintA, $constraintB) {
            return $constraintA($features) === $constraintB($features);
        };
    }

    /**
     * Returns a formula that is the material conditional of two formulas.
     * @param callable $constraintA
     * @param callable $constraintB
     * @return callable
     */
    public static function implies($constraintA, $constraintB) {
        return function ($features) use ($constraintA, $constraintB) {
            return !$constraintA($features) || $constraintB($features);
        };
    }
}