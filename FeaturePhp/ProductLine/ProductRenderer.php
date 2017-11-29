<?

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
     * Echoes the product analysis.
     */
    public function _render() {
        $featureNum = count($this->product->getConfiguration()->getSelectedFeatures());
        $fileNum = count($this->files);
        
        echo "<h2>Product Analysis</h2>";
        echo "<div>";
        echo "<p>For the given product, $featureNum features were selected and the following $fileNum files were generated:</p>";
        echo "<ul>";

        foreach ($this->files as $file) {
            $summary = $file->getContent()->getSummary();
            echo "<li><span class='fileName' onclick='var style = this.parentElement.children[1].style;
                                                      style.display = style.display === \"block\" ? \"none\" : \"block\";'>"
                . $file->getTarget()
                . "</span><pre style='font-size: 0.8em; display: none'>"
                . str_replace("\n", "<br />", htmlspecialchars($summary)) . "</pre>"
                . "</li>";
        }

        echo "</ul>";
        echo "</div>";
    }
}