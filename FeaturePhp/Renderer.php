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
                    .feature.unimplemented { color: black; }
                    .fileName { cursor: pointer; }
                </style>";
    }

    /**
     * Returns the renderer's web page.
     */
    public function render() {
        $str = $this->getStyle();
        $str .= "<table><tr><td valign='top'>";
        $str .= $this->_render();
        $str .= "</td></tr></table>";
        return $str;
    }

    /**
     * Internal function for returning the renderer's web page.
     */
    abstract protected function _render();
}

?>