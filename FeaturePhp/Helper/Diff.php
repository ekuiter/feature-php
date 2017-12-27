<?php

/**
 * The FeaturePhp\Helper\Diff class.
 */

namespace FeaturePhp\Helper;
use \FeaturePhp as fphp;

/**
 * A class containing functions for computing diffs and formatting the output.
 * Created by Stephen Morley - http://stephenmorley.org/ - and released under the
 * terms of the CC0 1.0 Universal legal code:
 * http://creativecommons.org/publicdomain/zero/1.0/legalcode
 */
class Diff{

    // define the constants
    const UNMODIFIED = 0;
    const DELETED    = 1;
    const INSERTED   = 2;

    /**
     * Returns the diff for two strings.
     * The return value is an array, each of
     * whose values is an array containing two values: a line (or character, if
     * $compareCharacters is true), and one of the constants DIFF::UNMODIFIED (the
     * line or character is in both strings), DIFF::DELETED (the line or character
     * is only in the first string), and DIFF::INSERTED (the line or character is
     * only in the second string).
     * @param string $string1 the first string
     * @param string $string2 the second string
     * @param bool $compareCharacters true to compare characters, and false to compare
     * lines; this optional parameter defaults to false
     */
    public static function compare(
        $string1, $string2, $compareCharacters = false){

        // initialise the sequences and comparison start and end positions
        $start = 0;
        if ($compareCharacters){
            $sequence1 = $string1;
            $sequence2 = $string2;
            $end1 = strlen($string1) - 1;
            $end2 = strlen($string2) - 1;
        }else{
            $sequence1 = preg_split('/\R/', $string1);
            $sequence2 = preg_split('/\R/', $string2);
            $end1 = count($sequence1) - 1;
            $end2 = count($sequence2) - 1;
        }

        // skip any common prefix
        while ($start <= $end1 && $start <= $end2
               && $sequence1[$start] == $sequence2[$start]){
            $start ++;
        }

        // skip any common suffix
        while ($end1 >= $start && $end2 >= $start
               && $sequence1[$end1] == $sequence2[$end2]){
            $end1 --;
            $end2 --;
        }

        // compute the table of longest common subsequence lengths
        $table = self::computeTable($sequence1, $sequence2, $start, $end1, $end2);

        // generate the partial diff
        $partialDiff =
                     self::generatePartialDiff($table, $sequence1, $sequence2, $start);

        // generate the full diff
        $diff = array();
        for ($index = 0; $index < $start; $index ++){
            $diff[] = array($sequence1[$index], self::UNMODIFIED);
        }
        while (count($partialDiff) > 0) $diff[] = array_pop($partialDiff);
        for ($index = $end1 + 1;
             $index < ($compareCharacters ? strlen($sequence1) : count($sequence1));
             $index ++){
            $diff[] = array($sequence1[$index], self::UNMODIFIED);
        }

        // return the diff
        return $diff;

    }

    /**
     * Returns the table of longest common subsequence lengths for the specified
     * sequences.
     * @param mixed $sequence1 the first sequence
     * @param mixed $sequence2 the second sequence
     * @param mixed $start     the starting index
     * @param mixed $end1      the ending index for the first sequence
     * @param mixed $end2      the ending index for the second sequence
     */
    private static function computeTable(
        $sequence1, $sequence2, $start, $end1, $end2){

        // determine the lengths to be compared
        $length1 = $end1 - $start + 1;
        $length2 = $end2 - $start + 1;

        // initialise the table
        $table = array(array_fill(0, $length2 + 1, 0));

        // loop over the rows
        for ($index1 = 1; $index1 <= $length1; $index1 ++){

            // create the new row
            $table[$index1] = array(0);

            // loop over the columns
            for ($index2 = 1; $index2 <= $length2; $index2 ++){

                // store the longest common subsequence length
                if ($sequence1[$index1 + $start - 1]
                    == $sequence2[$index2 + $start - 1]){
                    $table[$index1][$index2] = $table[$index1 - 1][$index2 - 1] + 1;
                }else{
                    $table[$index1][$index2] =
                                             max($table[$index1 - 1][$index2], $table[$index1][$index2 - 1]);
                }

            }
        }

        // return the table
        return $table;

    }

    /**
     * Returns the partial diff for the specificed sequences, in reverse order.
     * @param mixed $table     the table returned by the computeTable function
     * @param mixed $sequence1 the first sequence
     * @param mixed $sequence2 the second sequence
     * @param mixed $start     the starting index
     */
    private static function generatePartialDiff(
        $table, $sequence1, $sequence2, $start){

        //  initialise the diff
        $diff = array();

        // initialise the indices
        $index1 = count($table) - 1;
        $index2 = count($table[0]) - 1;

        // loop until there are no items remaining in either sequence
        while ($index1 > 0 || $index2 > 0){

            // check what has happened to the items at these indices
            if ($index1 > 0 && $index2 > 0
                && $sequence1[$index1 + $start - 1]
                == $sequence2[$index2 + $start - 1]){

                // update the diff and the indices
                $diff[] = array($sequence1[$index1 + $start - 1], self::UNMODIFIED);
                $index1 --;
                $index2 --;

            }elseif ($index2 > 0
                     && $table[$index1][$index2] == $table[$index1][$index2 - 1]){

                // update the diff and the indices
                $diff[] = array($sequence2[$index2 + $start - 1], self::INSERTED);
                $index2 --;

            }else{

                // update the diff and the indices
                $diff[] = array($sequence1[$index1 + $start - 1], self::DELETED);
                $index1 --;

            }

        }

        // return the diff
        return $diff;

    }
}

?>