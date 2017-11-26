<?

namespace FeaturePhp;
use \FeaturePhp as fphp;

abstract class Renderer {
    protected function getStyle() {
        return "<style>
                    body { font-family: monospace; }
                    .feature, .fileName { color: blue; font-weight: bold; }
                    .feature.selected { color: darkgreen; }
                    .feature.deselected { color: darkred; }
                    .fileName { cursor: pointer; }
                </style>";
    }

    public function render() {
        echo $this->getStyle();
        echo "<table><tr><td valign='top'>";
        $this->_render();
        echo "</td></tr></table>";
    }

    abstract protected function _render();
}

?>