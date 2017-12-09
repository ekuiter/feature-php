<?

/**
 * The FeaturePhp\Helper\Partition class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Helper class for partitioning an array with an equivalence relation.
 */
class Partition {
    /**
     * @var array $elements the set that should be partitioned
     */
    private $elements;

    /**
     * Creates a partition.
     * @param array $elements
     */
    public function __construct($elements) {
        $this->elements = $elements;
    }

    /**
     * Creates a partition from a set of objects.
     * @param array $array
     * @param callable $key
     * @return Partition
     */
    public static function fromObjects($array, $key) {
        $elements = array();
        foreach ($array as $element) {
            $result = call_user_func(array($element, $key));
            $elements = array_merge($elements, is_array($result) ? $result : array($result));
        }
        return new self($elements);
    }

    /**
     * Partitions the elements using an equivalence relation.
     * @param callable $key a binary relation on the elements
     * @return array[]
     */
    public function partitionBy($key) {
        $partition = array();
        foreach ($this->elements as $element) {
            $newEquivalenceClass = true;
            for ($i = 0; $i < count($partition); $i++) // partition[$i] is an equivalence class
                // an equivalence class is guaranteed to be non-empty
                if (call_user_func(array($element, $key), $partition[$i][0])) {
                    $partition[$i][] = $element;
                    $newEquivalenceClass = false;
                }
            if ($newEquivalenceClass)
                $partition[] = array($element);
        }
        return $partition;
    }
}

?>