<?

/**
 * The FeaturePhp\Helper\Path class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the Path class.
 */
class PathException extends \Exception {}

/**
 * Helper class for handling file and directory paths.
 */
class Path {
    /**
     * Strips a base from a file or directory path.
     * If no base is supplied, the working directory is used.
     * @param string $path
     * @param string $base
     * @return string
     */
    public static function stripBase($path, $base = null) {
        if ($base === null)
            $base = getcwd();
        if (substr($path, 0, strlen($base)) === $base)
            return ltrim(substr($path, strlen($base) + 1), "/");
        else
            throw new PathException("\"$path\" does not contain base path \"$base\"");
    }
    
    /**
     * Joins two file or directory paths.
     * If the left path is null, returns the right path, making the
     * left path optional (see {@see https://stackoverflow.com/q/1091107}).
     * @param string|null $lhs
     * @param string $rhs
     * @return string
     */
    public static function join($lhs, $rhs) {
        if ($lhs === null)
            return $rhs;
        return rtrim($lhs, '/') . '/' . ltrim($rhs, '/');
    }

    /**
     * Resolves a relative file or directory path.
     * This only works for unambiguous paths not depending on the
     * working directory (e.g. ../test can not be resolved)
     * (see {@see https://stackoverflow.com/q/20522605}).
     * @param string $fileName
     * @return string
     */
    public static function resolve($fileName) {
        $path = array();
        foreach(explode('/', $fileName) as $part) {
            if (empty($part) || $part === '.') continue;
            if ($part !== '..')
                array_push($path, $part);
            else if (count($path) > 0)
                array_pop($path);
            else
                throw new PathException("can not resolve path \"$fileName\"");
        }
        return implode('/', $path);
    }

    /**
     * Returns whether a file path refers to the current or parent directory.
     * @param string|SplFileInfo $fileName
     * @return bool
     */
    public static function isDot($fileName) {
        if (!is_string($fileName))
            $fileName = $fileName->getFileName();
        return $fileName === "." || $fileName === "..";
    }

    /**
     * Removes a directory recursively.
     * (see {@see https://paulund.co.uk/php-delete-directory-and-files-in-directory})
     * @param string $path
     */
    public static function removeDirectory($path) {
        if (is_dir($path))
            $dir = opendir($path);
        if (!$dir)
            return false;
        while ($file = readdir($dir))
            if (!self::isDot($file)) {
	            if (!is_dir("$path/$file"))
                    unlink("$path/$file");
	            else
                    self::removeDirectory("$path/$file");
            }
        closedir($dir);
        rmdir($path);
        return true;
    }
}

?>