<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class FileGeneratorException extends \Exception {}

class FileGenerator extends AbstractGenerator {    
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "file";
    }

    public function generateFiles() {
        $logFile = new LogFile("file");
        $files = array($logFile);

        foreach ($this->selectedArtifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $settings = $artifact->getGeneratorSettings(self::getKey());
            $target = $settings->getOptional("target", null);

            foreach ($settings->getOptional("files", array()) as $file) {
                if (is_string($file))
                    $file = array("source" => $file);
                if (!is_array($file))
                    throw new FileGeneratorException("invalid file specification");
                if (!array_key_exists("target", $file))
                    $file["target"] = $file["source"];
                $fileSource = $settings->getPath($file["source"]);
                if (!file_exists($fileSource))
                    throw new FileGeneratorException("file \"$fileSource\" does not exist");

                $fileTarget = fphp\Helper\Path::join($target, $file["target"]);
                $files[] = new StoredFile($fileTarget, $fileSource);
                $logFile->log($artifact, "added file \"$fileTarget\"");
            }

            foreach ($settings->getOptional("directories", array()) as $directory) {
                if (is_string($directory))
                    $directory = array("source" => $directory);
                if (!is_array($directory))
                    throw new FileGeneratorException("invalid directory specification");
                if (!array_key_exists("target", $directory))
                    $directory["target"] = $directory["source"];
                $directorySource = $settings->getPath($directory["source"]);
                if (!is_dir($directorySource))
                    throw new FileGeneratorException("directory \"$directorySource\" does not exist");
                if (array_key_exists("exclude", $directory)) {
                    if (!is_array($directory["exclude"]))
                        throw new FileGeneratorException("invalid exclude specification");
                } else
                    $directory["exclude"] = array();

                foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directorySource)) as $entry)
                    if (!fphp\Helper\Path::isDot($entry)) {
                        $fileSource = $entry->getPathName();
                        $relativeFileTarget = fphp\Helper\Path::stripBase(
                            realpath($fileSource), realpath($directorySource));
                        if (in_array($relativeFileTarget, $directory["exclude"]))
                            continue;
                        
                        $fileTarget = fphp\Helper\Path::join(
                            $target, fphp\Helper\Path::join($directory["target"], $relativeFileTarget));
                        $files[] = new StoredFile($fileTarget, $fileSource);
                        $logFile->log($artifact, "added file \"$fileTarget\"");
                    }
            }
        }

        return $files;
    }
}

?>