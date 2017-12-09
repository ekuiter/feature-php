<?php

/**
 * The FeaturePhp\Exporter\Exporter class.
 */

namespace FeaturePhp\Exporter;
use \FeaturePhp as fphp;

/**
 * An exporter for a product.
 * An exporter is responsible for providing a {@see \FeaturePhp\ProductLine\Product}
 * to the end user. For this it takes a product and its generated files
 * ({@see \FeaturePhp\File\File}) and bundles them.
 */
abstract class Exporter {
    /**
     * Exports a product.
     * The implementation is expected to have some side effect, e.g. downloading a file.
     * @param \FeaturePhp\ProductLine\Product $product
     */
    abstract public function export($product);
}

?>