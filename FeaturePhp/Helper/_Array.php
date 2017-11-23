<?

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

class ArrayException extends \Exception {}

class _Array {
    public static function findByKey($array, $key, $value) {
        foreach ($array as $element)
            if (call_user_func(array($element, $key)) === $value)
                return $element;
        return null;
    }
    
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