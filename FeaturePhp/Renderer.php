<?

namespace FeaturePhp;
use \FeaturePhp as fphp;

abstract class Renderer {
    abstract public function render();

    protected function getStyle() {
        return "<style>
                    body { font-family: monospace; }
                    .feature, .fileName { color: blue; font-weight: bold; }
                    .feature.selected { color: darkgreen; }
                    .feature.deselected { color: darkred; }
                    .fileName { cursor: pointer; }
                </style>";
    }
}

?>