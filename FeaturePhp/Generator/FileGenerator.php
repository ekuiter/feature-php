<?

namespace FeaturePhp\Generator;

class FileGeneratorException extends \Exception {}

class FileGenerator extends AbstractGenerator {    
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "file";
    }

    public function generateFiles() {
        $logFile = new File("logs/file.log");
        $files = array($logFile);

        foreach ($this->artifacts as $artifact) {
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

                $fileTarget = \FeaturePhp\AbstractSettings::joinPaths($target, $file["target"]);
                $files[] = new File($fileTarget, file_get_contents($fileSource));
                $logFile->append("added file \"$fileTarget\" for \"$featureName\"\n");
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
                    if ($entry->getFileName() !== "." && $entry->getFileName() !== "..") {
                        $fileSource = $entry->getPathName();
                        $relativeFileTarget = \FeaturePhp\AbstractSettings::stripBasePath(
                            realpath($fileSource), realpath($directorySource));
                        if (in_array($relativeFileTarget, $directory["exclude"]))
                            continue;
                        
                        $fileTarget = \FeaturePhp\AbstractSettings::joinPaths(
                            $target, \FeaturePhp\AbstractSettings::joinPaths($directory["target"], $relativeFileTarget));
                        $files[] = new File($fileTarget, file_get_contents($fileSource));
                        $logFile->append("added file \"$fileTarget\" for \"$featureName\"\n");
                    }
            }
        }

        return $files;
    }
}

?>