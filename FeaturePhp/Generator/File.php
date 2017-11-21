<?

namespace FeaturePhp\Generator;

class FileException extends \Exception {}

class File {
    private $fileName;
    private $contents;
    
    public function __construct($fileName, $contents = null) {
        $this->fileName = $fileName;
        $this->contents = $contents || "";
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
}

?>