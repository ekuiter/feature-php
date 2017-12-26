<?php

/**
 * The FeaturePhp\Helper\_String class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Helper class for working with strings.
 */
class _String {
    /**
     * Truncates a multi-line string.
     * Appends an ellipsis (see {@see https://stackoverflow.com/q/9219795}).
     * @param string $text
     * @param int $chars
     * @return string
     */
    public static function truncate($text, $chars = 60) {
        if (!$text)
            return $text;
        
        $lines = explode("\n", $text);
        foreach ($lines as &$line)
            $line = trim($line);
        $text = implode(" ", $lines);
        if (strlen($text) <= $chars)
            return $text;
        
        $text = $text . " ";
        $text = substr($text, 0, $chars);
        $text = substr($text, 0, strrpos($text, ' '));
        $text = $text . "...";
        return $text;
    }
}

?>