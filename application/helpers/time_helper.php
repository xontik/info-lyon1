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
