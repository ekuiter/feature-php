<?

namespace FeaturePhp\Generator;

class FileException extends \Exception {}

class File {
    private $fileName;
    private $contents;
    
    public function __construct($fileName, $contents = null) {
        $this->fileName = $this->resolveRelativePath($fileName);
        $this->contents = $contents ? $contents : "";
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getContents() {
        return $this->contents;
    }

    public function append($contents) {
        $this->contents .= $contents;
    }

    public static function findByFileName($files, $fileName) {
        foreach ($files as $file)
            if ($file->getFileName() === $fileName)
                return $file;
        return null;
    }

    public static function checkForDuplicates($_files) {
        $files = $_files;
        while (count($files) > 0) {
            $file = $files[0];
            $files = array_slice($files, 1);
            if (self::findByFileName($files, $file->getFileName()))
                throw new FileException("\"{$file->getFileName()}\" has been generated twice");
        }
        return $_files;
    }

    // https://stackoverflow.com/q/20522605
    private function resolveRelativePath($fileName) {
        $path = array();
        foreach(explode('/', $fileName) as $part) {
            if (empty($part) || $part === '.') continue;
            if ($part !== '..')
                array_push($path, $part);
            else if (count($path) > 0)
                array_pop($path);
            else
                throw new FileException("invalid path \"$fileName\"");
        }
        return implode('/', $path);
    }
}

?>