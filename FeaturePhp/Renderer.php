<?php

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
     * @var string $accentColor accent color for text only rendering
     */
    protected $accentColor = "\033[1;33m";

    /**
     * @var string $defaultColor default color for text only rendering
     */
    protected $defaultColor = "\033[0m";
    
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
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    public function render($textOnly = false) {
        if ($textOnly)
            return $this->renderText();
        
        $str = $this->getStyle();
        $str .= "<table><tr><td valign='top'>";
        $str .= $this->_render(false);
        $str .= "</td></tr></table>";
        return $str;
    }

    /**
     * Returns the renderer's web page as text only.
     * This is used by the command-line interface.
     * @return string
     */
    public function renderText() {
        return $this->_render(true);
    }

    /**
     * Internal function for returning the renderer's web page.
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    abstract protected function _render($textOnly);
}

?>