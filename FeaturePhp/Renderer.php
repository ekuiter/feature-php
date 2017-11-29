<?

/**
 * The FeaturePhp\Renderer class.
 */

namespace FeaturePhp;
use \FeaturePhp as fphp;

/**
 * A minimalistic renderer for a web page.
 * This serves for visualizing and demonstrating the feature-php library.
 * It is recommended to develop your own renderers in production.
 */
abstract class Renderer {
    /**
     * Returns a simple stylesheet.
     * @return string
     */
    protected function getStyle() {
        return "<style>
                    body { font-family: monospace; }
                    .feature, .fileName { color: blue; font-weight: bold; }
                    .feature.selected { color: darkgreen; }
                    .feature.deselected { color: darkred; }
                    .fileName { cursor: pointer; }
                </style>";
    }

    /**
     * Echoes the renderer's web page.
     */
    public function render() {
        echo $this->getStyle();
        echo "<table><tr><td valign='top'>";
        $this->_render();
        echo "</td></tr></table>";
    }

    /**
     * Internal function for echoing the renderer's web page.
     */
    abstract protected function _render();
}

?>