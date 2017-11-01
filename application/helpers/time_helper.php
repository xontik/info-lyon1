<?php

/**
 * Makes a readable string from an interval of time.
 *
 * @param DateInterval $diff The interval of time to be converted
 * @return string The difference in a readable format
 */
function readableTimeDifference($diff) {
    $read = $diff->invert
        ? 'Dans '
        : 'Il y a ';

    if ($diff->y >= 1) {
        $read .= 'plus d\'un an';
    } else if ($diff->m >= 1) {
        $read .= 'plus de ' . $diff->m . ' mois';
    } else if ($diff->d > 14) {
        $read .= 'plus de ' . ((int) ($diff->d / 7)) . ' semaines';
    } else if ($diff->d === 7) {
        $read .= 'une semaine';
    } else if ($diff->d > 1) {
        $read .= $diff->d . ' jours';
    } else if ($diff->d === 1) {
        $read = $diff->invert ? 'Hier' : 'Demain';
    } else if ($diff->h >= 1) {
        $read .= $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
    } else if ($diff->i >= 1) {
        $read .= $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    } else {
        $read .= 'moins d\'une minute';
    }

    return $read;
}

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
