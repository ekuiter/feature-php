<?

namespace FeaturePhp;

class Logic {
    public static function is($feature) {
        return function ($features) use ($feature) {
            return Feature::has($features, $feature);
        };
    }

    public static function not($constraint) {
        return function ($features) use ($constraint) {
            return !$constraint($features);
        };
    }

    public static function _and() {
        $args = func_get_args();
        return function ($features) use ($args) {
            $acc = true;
            foreach ($args as $arg)
                $acc = $acc && $arg($features);
            return $acc;
        };
    }

    public static function _or() {
        $args = func_get_args();
        return function ($features) use ($args) {
            $acc = false;
            foreach ($args as $arg)
                $acc = $acc || $arg($features);
            return $acc;
        };
    }

    public static function equiv($constraintA, $constraintB) {
        return function ($features) use ($constraintA, $constraintB) {
            return $constraintA($features) === $constraintB($features);
        };
    }

    public static function implies($constraintA, $constraintB) {
        return function ($features) use ($constraintA, $constraintB) {
            return !$constraintA($features) || $constraintB($features);
        };
    }
}