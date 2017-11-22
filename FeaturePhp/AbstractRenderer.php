<?

namespace FeaturePhp;

abstract class AbstractRenderer {
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