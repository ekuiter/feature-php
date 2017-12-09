<?php

/**
 * The FeaturePhp\Helper\PhpParser class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * Exception thrown from the PhpParser class.
 */
class PhpParserException extends \Exception {}

/**
 * Helper class for parsing PHP files.
 */
class PhpParser {
    /**
     * @var array[] $ast the code's abstract syntax tree
     */
    private $ast = null;

    /**
     * Parses a PHP string.
     * @param string $str
     * @return PhpParser
     */
    public function parseString($str) {
        $parser = (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
        $this->ast = $parser->parse($str);
        return $this;
    }

    /**
     * Parses a PHP file.
     * @param string $fileName
     * @return PhpParser
     */
    public function parseFile($fileName) {
        if (!file_exists($fileName))
            throw new PhpParserException("file $fileName does not exist");
        
        return $this->parseString(file_get_contents($fileName));
    }

    /**
     * Returns the code's abstract syntax tree.
     * @return array[]
     */
    public function getAst() {
        return $this->ast;
    }

    /**
     * Returns an abstract syntax tree's code.
     * @param array[] $ast
     * @return string
     */
    public static function astToString($ast) {
        return (new \PhpParser\PrettyPrinter\Standard())->prettyPrintFile($ast);
    }
}