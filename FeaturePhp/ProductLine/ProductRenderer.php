<?php

/**
 * The FeaturePhp\ProductLine\ProductRenderer class.
 */

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

/**
 * A renderer for a product.
 * This renders a {@see Product} analysis as a web page.
 * In particular, a list of generated files and file content
 * summaries is included.
 */
class ProductRenderer extends fphp\Renderer {
    /**
     * @var Product $product the product that will be analyzed
     */
    private $product;

    /**
     * Creates a product renderer.
     * @param Product $product
     */
    public function __construct($product) {            
        $this->product = $product;
        $this->files = $this->product->generateFiles();
    }

    /**
     * Returns the product analysis.
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    public function _render($textOnly) {
        $featureNum = count($this->product->getConfiguration()->getSelectedFeatures());
        $fileNum = count($this->files);

        $str = "";
        $accentColor = "\033[1;33m";
        $colorOff = "\033[0m";

        if ($textOnly)
            $str .= "\nProduct Analysis\n================\n" .
                 "For the given product, $featureNum features were selected and the following $fileNum files were generated:\n\n";
        else {
            $str .= "<h2>Product Analysis</h2>";
            $str .= "<div>";
            $str .= "<p>For the given product, $featureNum features were selected and the following $fileNum files were generated:</p>";
            $str .= "<ul>";
        }

        $maxLen = 0;
        foreach ($this->files as $file)
            $maxLen = strlen($file->getTarget()) > $maxLen ? strlen($file->getTarget()) : $maxLen;

        foreach ($this->files as $file) {
            $summary = $file->getContent()->getSummary();
            if ($textOnly) {
                $str .= sprintf("$accentColor%-{$maxLen}s$colorOff %s\n", $file->getTarget(),
                                fphp\Helper\_String::truncate($summary));
            } else
                $str .= "<li><span class='fileName' onclick='var style = this.parentElement.children[1].style;
                                                      style.display = style.display === \"block\" ? \"none\" : \"block\";'>"
                     . $file->getTarget()
                     . "</span><pre style='font-size: 0.8em; display: none'>"
                     . str_replace("\n", "<br />", htmlspecialchars($summary)) . "</pre>"
                     . "</li>";
        }

        if (!$textOnly) {
            $str .= "</ul>";
            $str .= "</div>";
        }
        return $str;
    }
}