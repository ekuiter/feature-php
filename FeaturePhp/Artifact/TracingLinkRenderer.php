<?php

/**
 * The FeaturePhp\Artifact\TracingLinkRenderer class.
 */

namespace FeaturePhp\Artifact;
use \FeaturePhp as fphp;

/**
 * A renderer for some tracing links.
 * This renders a {@see TracingLink} analysis as a web page.
 */
class TracingLinkRenderer extends fphp\Renderer {
    /**
     * @var TracingLink[] $tracingLinks the tracing links that will be analyzed
     */
    private $tracingLinks;

    /**
     * Creates a product renderer.
     * @param TracingLink[] $tracingLinks
     */
    public function __construct($tracingLinks) {            
        $this->tracingLinks = $tracingLinks;
    }

    /**
     * Returns the tracing link analysis.
     * @param bool $textOnly whether to render text or HTML
     * @return string
     */
    public function _render($textOnly) {
        $tracingLinkNum = count($this->tracingLinks);
        $str = "";

        if ($textOnly) {
            $str .= "\nFeature Traceability\n====================\n" .
                 "The following $tracingLinkNum tracing links were found:\n\n";
            $maxLen = fphp\Helper\_String::getMaxLength($this->tracingLinks, "getFeatureName")
                    + fphp\Helper\_String::getMaxLength($this->tracingLinks, "getType");
            foreach ($this->tracingLinks as $tracingLink)
                $str .= $this->analyzeTracingLink($tracingLink, true, $maxLen);
        } else {
            $str .= "<h2>Feature Traceability</h2>";
            $str .= "<p>The following $tracingLinkNum tracing links were found:</p>";
            $str .= "<table cellpadding='2'>";
            $str .= "<tr align='left'><th>Feature</th><th>Type</th><th>Source</th><th>Target</th></tr>";
            foreach ($this->tracingLinks as $tracingLink)
                $str .= $this->analyzeTracingLink($tracingLink);
            $str .= "</table>";
        }
        
        return $str;
    }

    /**
     * Analyzes a single tracing link.
     * @param TracingLink $tracingLink
     * @param bool $textOnly whether to render text or HTML
     * @param int $maxLen
     * @return string
     */
    private function analyzeTracingLink($tracingLink, $textOnly = false, $maxLen = 0) {
        $maxLen += strlen($this->defaultColor);
        
        if ($textOnly)
            return sprintf("$this->accentColor%-{$maxLen}s %s\n%{$maxLen}s %s\n",
                           $tracingLink->getFeatureName() . "$this->defaultColor " . $tracingLink->getType(),
                           $tracingLink->getSourcePlace()->getSummary(),
                           $this->defaultColor, $tracingLink->getTargetPlace()->getSummary());
        else
            return "<tr><td><span class='feature'>"
                . $tracingLink->getFeatureName()
                . "</span></td><td>" . $tracingLink->getType()
                . "</td><td>" . $tracingLink->getSourcePlace()->getSummary()
                . "</td><td>" . $tracingLink->getTargetPlace()->getSummary() . "</td></tr>";
    }
}