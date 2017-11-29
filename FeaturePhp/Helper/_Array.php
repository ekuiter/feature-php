<?

/**
 * The FeaturePhp\Helper\_Array class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the _Array class.
 */
class ArrayException extends \Exception {}

/**
 * Helper class for working with arrays.
 */
class _Array {
    /**
     * Finds an array element by comparing a member with the given value.
     * @param array $array
     * @param callable $key
     * @param string $value
     * @return object
     */
    public static function findByKey($array, $key, $value) {
        foreach ($array as $element)
            if (call_user_func(array($element, $key)) === $value)
                return $element;
        return null;
    }

    /**
     * Asserts that an array does not contain duplicates regarding a member.
     * Throws {@see \FeaturePhp\Helper\ArrayException} if duplicates are found.
     * @param array $_array
     * @param callable $key
     * @return array
     */
    public static function assertNoDuplicates($_array, $key) {
        $array = $_array;
        while (count($array) > 0) {
            $element = $array[0];
            $array = array_slice($array, 1);
            $value = call_user_func(array($element, $key));
            if (self::findByKey($array, $key, $value))
                throw new ArrayException("duplicate found for \"$value\"");
        }
        return $_array;
    }

    /**
     * Sorts an array in ascending order regarding a member.
     * @param array $_array
     * @param callable $key
     * @return array
     */
    public static function sortByKey($_array, $key) {
        $array = $_array;
        $result = usort($array, function($a, $b) use ($key) {
            return strcmp(call_user_func(array($a, $key)),
                          call_user_func(array($b, $key)));
        });
        if (!$result)
            throw new ArrayException("sorting by \"$key\" failed");
        return $array;
    }
}

?>