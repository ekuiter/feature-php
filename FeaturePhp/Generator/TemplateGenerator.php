<?php

/**
 * The FeaturePhp\Generator\TemplateGenerator class.
 */

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

/**
 * Generates text files by replacing parts of template files.
 * A selected artifact can specify template files and corresponding replacement rules
 * (see {@see \FeaturePhp\Specification\TemplateSpecification} and
 * {@see \FeaturePhp\Specification\ReplacementRule}).
 * The rules are applied to the template files to generate new, configuration-specific
 * files.
 */
class TemplateGenerator extends ExtendGenerator {
    /**
     * Returns the template generator's key.
     * @return string
     */
    public static function getKey() {
        return "template";
    }

    /**
     * Returns a template specification from a plain settings array.
     * @param array $file a plain settings array
     * @param Settings $settings the generator's settings
     * @param \FeaturePhp\Artifact\Artifact $artifact the currently processed artifact
     * @return \FeaturePhp\Specification\TemplateSpecification
     */
    protected function getSpecification($file, $settings, $artifact) {
        return fphp\Specification\TemplateSpecification::fromArrayAndSettings($file, $settings, $artifact);
    }

    /**
     * Returns a template file from a template specification.
     * @param \FeaturePhp\Specification\TemplateSpecification $templateSpecification
     * @return \FeaturePhp\File\TemplateFile
     */
    protected function getExtendableFileFromSpecification($templateSpecification) {
        return fphp\File\TemplateFile::fromSpecification($templateSpecification);
    }
}

?>