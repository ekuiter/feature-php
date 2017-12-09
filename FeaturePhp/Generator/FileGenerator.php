<?

/**
 * The FeaturePhp\Generator\FileGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates files from specified files and directories.
 * For concrete implementations, see {@see FileGenerator} or {@see CollaborationGenerator}.
 */
abstract class FileGenerator extends Generator {
    /**
     * @var string[] $exclude list of globally excluded files
     */
    private $exclude;
    
    /**
     * Creates a file generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
        $this->exclude = $settings->getOptional("exclude", array());
    }

    /**
     * Returns plain settings arrays with file or directory specifications.
     * @param Settings $settings
     * @param string $key
     * @return array[]
     */
    private function getFileOrDirectorySettings($settings, $key) {
        $filesOrDirectories = $settings->getOptional($key, array());
        if (!is_array($filesOrDirectories))
            $filesOrDirectories = array($filesOrDirectories);
        return $filesOrDirectories;
    }

    /**
     * Processes the files and directories.
     */
    public function _generateFiles() {        
        foreach ($this->selectedArtifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(static::getKey());

            foreach ($this->getFileOrDirectorySettings($settings, "files") as $file)
                $this->_processFileSpecification(
                    $artifact, fphp\Specification\FileSpecification::fromArrayAndSettings($file, $settings));

            foreach ($this->getFileOrDirectorySettings($settings, "directories") as $directory) {
                $directorySpecification = fphp\Specification\DirectorySpecification::fromArrayAndSettings($directory, $settings);
                
                foreach ($directorySpecification->getFileSpecifications() as $fileSpecification)
                    $this->_processFileSpecification($artifact, $fileSpecification);
            }
        }
    }

    /**
     * Processes a file from a specification.
     * Considers globally excluded files. Only exact file names are supported.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    private function _processFileSpecification($artifact, $fileSpecification) {
        if (!in_array(basename($fileSpecification->getSource()), $this->exclude))
            $this->processFileSpecification($artifact, $fileSpecification);
    }

    /**
     * Processes a file from a specification.
     * @param \FeaturePhp\Artifact\Artifact $artifact
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    abstract protected function processFileSpecification($artifact, $fileSpecification);
}

?>