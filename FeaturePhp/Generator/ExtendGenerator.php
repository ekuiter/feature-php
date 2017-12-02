<?

/**
 * The FeaturePhp\Generator\ExtendGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates text files that can be extended by different artifacts.
 * In contrast, generators like {@see CopyGenerator} allow no file modifications.
 * For concrete implementations, see {@see ChunkGenerator} or {@see TemplateGenerator}.
 */
abstract class ExtendGenerator extends Generator {
    /**
     * @var \FeaturePhp\File\ExtendableFile[] $extendableFiles cached extendable files
     */
    private $extendableFiles;

    /**
     * Creates an extendable generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
        $this->extendableFiles = array();

        foreach ($this->getFileSettings($settings) as $file)
            $this->getExtendableFile($this->getSpecification($file, $settings));
    }

    /**
     * Creates an extendable file from a specification.
     * If the extendable file was already created, returns the cached extendable file.
     * If the extendable file does not yet exist, create it if it may be created from
     * this specification.
     * @param \FeaturePhp\Specification\Specification $specification
     * @return \FeaturePhp\File\ExtendableFile
     */
    private function getExtendableFile($specification) {
        $target = $specification->getTarget();
        if (!array_key_exists($target, $this->extendableFiles)) {
            if (!$specification->mayCreate())
                return null;
            $this->extendableFiles[$target] = $this->getExtendableFileFromSpecification($specification);
            $this->logFile->log(null, "added file \"{$specification->getTarget()}\"");
        }
        return $this->extendableFiles[$target];
    }
    
    /**
     * Generates the extendable files for a set of artifacts and file settings.
     * @param \FeaturePhp\Artifact\Artifact[] $artifacts
     * @param callable $fileSettingsGetter
     * @param bool $extend
     */
    private function generateFilesForArtifacts($artifacts, $fileSettingsGetter, $extend) {
        foreach ($artifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(static::getKey());

            foreach (call_user_func(array($this, $fileSettingsGetter), $settings) as $file) {
                $specification = $this->getSpecification($file, $settings);
                $extendableFile = $this->getExtendableFile($specification);
                if ($extendableFile && $extend) {
                    $extendableFile->extend($specification);
                    $this->logFile->log($artifact, "extended file \"{$specification->getTarget()}\"");
                }
            }
        }
    }

    /**
     * Generates the extendable files.
     */
    public function _generateFiles() {            
        $this->generateFilesForArtifacts($this->selectedArtifacts, "getFileSettingsForSelected", false);
        $this->generateFilesForArtifacts($this->deselectedArtifacts, "getFileSettingsForDeselected", false);
        
        $this->generateFilesForArtifacts($this->selectedArtifacts, "getFileSettingsForSelected", true);
        $this->generateFilesForArtifacts($this->deselectedArtifacts, "getFileSettingsForDeselected", true);

        foreach ($this->extendableFiles as $extendableFile)
            $this->files[] = $extendableFile;
    }

    /**
     * Returns plain settings arrays with file specifications for selected artifacts.
     * @param Settings $settings
     * @return array[]
     */
    private function getFileSettingsForSelected($settings) {
        return $settings->getOptional("filesIfSelected", $settings->getOptional("files", array()));
    }

    /**
     * Returns plain settings arrays with file specifications for deselected artifacts.
     * @param Settings $settings
     * @return array[]
     */
    private function getFileSettingsForDeselected($settings) {
        return $settings->getOptional("filesIfDeselected", array());
    }

    /**
     * Returns plain settings arrays with file specifications for all registered artifacts.
     * @param Settings $settings
     * @return array[]
     */
    private function getFileSettings($settings) {
        return array_merge($this->getFileSettingsForSelected($settings),
                           $this->getFileSettingsForDeselected($settings));
    }

    /**
     * Returns a specification from a plain settings array.
     * @param $file a plain settings array
     * @param $settings the generator's settings
     * @return \FeaturePhp\Specification\Specification
     */
    abstract protected function getSpecification($file, $settings);

    /**
     * Returns an extendable file from a specification.
     * @param \FeaturePhp\Specification\Specification $specification
     * @return \FeaturePhp\File\ExtendableFile
     */
    abstract protected function getExtendableFileFromSpecification($specification);
}

?>