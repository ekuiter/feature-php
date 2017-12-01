<?

/**
 * The FeaturePhp\Generator\CopyGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Copies files and directories.
 * A selected artifact can specify files and directories that should be copied
 * into the product unchanged. This enables file-level granularity.
 * Directories are copied recursively, specific files can be excluded
 * (see {@see \FeaturePhp\Specification\FileSpecification} and
 * {@see \FeaturePhp\Specification\DirectorySpecification}).
 * File contents are not inspected, so binary files can be copied as well.
 */
class CopyGenerator extends Generator {
    /**
     * @var string[] $exclude list of globally excluded files
     */
    private $exclude;
    
    /**
     * Creates a copy generator.
     * @param Settings $settings
     */
    public function __construct($settings) {
        parent::__construct($settings);
        $this->exclude = $settings->getOptional("exclude", array());
    }

    /**
     * Returns the copy generator's key.
     * @return string
     */
    public static function getKey() {
        return "copy";
    }

    /**
     * Adds a stored file from a specification.
     * Considers globally excluded files. Only exact file names are supported.
     * @param \FeaturePhp\Specification\FileSpecification $fileSpecification
     */
    private function addFileFromSpecification($fileSpecification) {
        if (!in_array(basename($fileSpecification->getSource()), $this->exclude))
            $this->files[] = fphp\File\StoredFile::fromSpecification($fileSpecification);
    }

    /**
     * Copies the files and directories.
     * Internally, this generates stored files (see {@see \FeaturePhp\File\StoredFile})
     * pointing to the original files on the server.
     */
    public function _generateFiles() {        
        foreach ($this->selectedArtifacts as $artifact) {
            $settings = $artifact->getGeneratorSettings(self::getKey());

            foreach ($settings->getOptional("files", array()) as $file) {
                $fileSpecification = fphp\Specification\FileSpecification::fromArray($file, $settings);
                $this->addFileFromSpecification($fileSpecification);
                $this->logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
            }

            foreach ($settings->getOptional("directories", array()) as $directory) {
                $directorySpecification = fphp\Specification\DirectorySpecification::fromArray($directory, $settings);
                
                foreach ($directorySpecification->getFileSpecifications() as $fileSpecification) {
                    $this->addFileFromSpecification($fileSpecification);
                    $this->logFile->log($artifact, "added file \"{$fileSpecification->getTarget()}\"");
                }
            }
        }
    }
}

?>