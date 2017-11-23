<?

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

class PathException extends \Exception {}

class Path {
    public static function stripBase($path, $base = null) {
        if ($base === null)
            $base = getcwd();
        if (substr($path, 0, strlen($base)) === $base)
            return ltrim(substr($path, strlen($base) + 1), "/");
        else
            throw new SettingsException("\"$path\" does not contain base path \"$base\"");
    }
    
    // https://stackoverflow.com/q/1091107
    public static function join($lhs, $rhs) {
        if ($lhs === null)
            return $rhs;
        return rtrim($lhs, '/') .'/'. ltrim($rhs, '/');
    }

    // https://stackoverflow.com/q/20522605
    public static function resolve($fileName) {
        $path = array();
        foreach(explode('/', $fileName) as $part) {
            if (empty($part) || $part === '.') continue;
            if ($part !== '..')
                array_push($path, $part);
            else if (count($path) > 0)
                array_pop($path);
            else
                throw new PathException("invalid path \"$fileName\"");
        }
        return implode('/', $path);
    }
}

?>