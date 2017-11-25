<?

namespace FeaturePhp\ProductLine;
use \FeaturePhp as fphp;

class ProductRenderer extends fphp\Renderer {
    private $product;
    
    public function __construct($product) {            
        $this->product = $product;
        $this->files = $this->product->generateFiles();
    }

    public function render() {
        $featureNum = count($this->product->getConfiguration()->getSelectedFeatures());
        $fileNum = count($this->files);
        
        echo $this->getStyle();
        echo "<table><tr><td valign='top'>";
        echo "<h2>Product Analysis</h2>";
        echo "<div>";
        echo "<p>For the given product, $featureNum features were selected and the following $fileNum files were generated:</p>";
        echo "<ul>";

        foreach ($this->files as $file) {
            $contents = $file->getContents();
            echo "<li><span class='fileName' onclick='var style = this.parentElement.children[1].style;
                                                      style.display = style.display === \"block\" ? \"none\" : \"block\";'>"
                . $file->getFileTarget()
                . "</span><pre style='font-size: 0.8em; display: none'>"
                . str_replace("\n", "<br />", htmlspecialchars($contents)) . "</pre>"
                . "</li>";
        }

        echo "</ul>";
        echo "</div>";
        echo "</td></tr></table>";
    }
}