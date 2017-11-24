<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class FileGeneratorException extends \Exception {}

class SpecificationSettings extends fphp\Settings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
    }

    public function getSource() {
        return $this->get("source");
    }

    public function getTarget() {
        return $this->get("target");
    }

    public function setTarget($target) {
        $this->set("target", $target);
    }

    public function getExclude() {
        $this->setOptional("exclude", array());
        return $this->getWith("exclude", "is_array");
    }
}

class FileGenerator extends Generator {    
    public function __construct($settings) {
        parent::__construct($settings);
    }

    public static function getKey() {
        return "file";
    }

    private function getSpecificationSettings($settings, $spec, $type) {
        if (is_string($spec))
            $spec = array("source" => $spec);
        if (!is_array($spec))
            throw new FileGeneratorException("invalid $type specification");
        if (!array_key_exists("target", $spec))
            $spec["target"] = $spec["source"];
        $spec["source"] = $settings->getPath($spec["source"]);
        if (($type === "file" && !file_exists($spec["source"])) ||
            ($type === "directory" && !is_dir($spec["source"])))
            throw new FileGeneratorException("$type \"$spec[source]\" does not exist");
        return new SpecificationSettings($spec, $settings->getDirectory());
    }

    public function generateFiles() {
        $logFile = new LogFile("file");
        $files = array($logFile);

        foreach ($this->selectedArtifacts as $artifact) {
            $featureName = $artifact->getFeature()->getName();
            $settings = $artifact->getGeneratorSettings(self::getKey());
            $target = $settings->getOptional("target", null);

            foreach ($settings->getOptional("files", array()) as $file) {
                $fileSettings = $this->getSpecificationSettings($settings, $file, "file");
                $fileSettings->setTarget(fphp\Helper\Path::join($target, $fileSettings->getTarget()));
                $files[] = new StoredFile($fileSettings->getTarget(), $fileSettings->getSource());
                $logFile->log($artifact, "added file \"{$fileSettings->getTarget()}\"");
            }

            foreach ($settings->getOptional("directories", array()) as $directory) {
                $directorySettings = $this->getSpecificationSettings($settings, $directory, "directory");
                
                foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directorySettings->getSource())) as $entry)
                    if (!fphp\Helper\Path::isDot($entry)) {
                        $fileSource = $entry->getPathName();
                        $relativeFileTarget = fphp\Helper\Path::stripBase(
                            realpath($fileSource), realpath($directorySettings->getSource()));
                        if (in_array($relativeFileTarget, $directorySettings->getExclude()))
                            continue;
                        
                        $fileTarget = fphp\Helper\Path::join(
                            $target, fphp\Helper\Path::join($directorySettings->getTarget(), $relativeFileTarget));
                        $files[] = new StoredFile($fileTarget, $fileSource);
                        $logFile->log($artifact, "added file \"$fileTarget\"");
                    }
            }
        }

        return $files;
    }
}

?>