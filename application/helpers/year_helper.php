<?php
/**
 * Created by PhpStorm.
 * User: enzob
 * Date: 18/09/2017
 * Time: 21:46
 */

/**
* Returns the number of days in a given month and year, taking into account leap years.
* @param $month int numeric month (integers 1-12)
* @param $year int numeric year (any integer)
* @return int The number of days in the given month and year
*/
function days_in_month($month, $year) {
    return $month == 2
        // february
        ? ($year % 4
            ? 28 : ($year % 100
                ? 29 : ($year % 400
                    ? 28 : 29
                )))
        // rest of the year
        : (($month - 1) % 7 % 2
            ? 30 : 31);
}
