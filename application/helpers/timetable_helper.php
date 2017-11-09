<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('DATE_FORMAT', 'Y-m-d');

/**
 * Look for the next not-empty timetable in the 3 next days.
 * @param int $resources The resources number in ADE
 * @param string $period The period you want ('day' or 'week')
 * @param DateTime $datetime The date you want to starting searching timetable (optionnal)
 * @return array Formatted timetable
 * @see getTimetable()
 */
function getNextTimetable($resources, $period, &$datetime = NULL) {
    global $timezone;
    $timezone = new DateTimeZone('Europe/Paris');

    if ($datetime === NULL) {
        $datetime = new DateTime();
        $datetime->setTimezone($timezone);
    }

    $tempDate = clone $datetime;
    $limit = 0;

    // If hour >= 18h, take next day timetable
    if ($tempDate->format('H') >= 18) {
        $tempDate->modify('+1 day');
        $limit = 1;
    }

    $timetable = getTimetable($resources, $period, $tempDate);

    // Look at next not empty timetable within 3 days
    while ($limit < 3 && empty($timetable)) {
        $tempDate->modify('+1 day');
        $timetable = getTimetable($resources, $period, $tempDate);
        $limit++;
    }

    // If timetable still empty, reset date
    if (!empty($timetable) && strcasecmp($period, 'day') === 0) {
        $datetime = $tempDate;
    }

    return $timetable;
}

/**
 * Get the timetable of a period.
 * @param int $resources The resources number in ADE
 * @param string $period The period of the timetable ('day' or 'week')
 * @param DateTime $datetime The date of the timetable (optionnal)
 * @return array Formatted timetable
 */
function getTimetable($resources, $period, $datetime = NULL)
{
    global $timezone;

    $CI = get_instance();
    $CI->load->model('Timetables');

    // Default $datetime to today
	if ($datetime === NULL) {
        $datetime = new DateTime();
        $datetime->setTimezone($timezone);
    }

    // Generalize time period
	if (strcasecmp($period, 'day') === 0)
	{
        $firstDate = clone $datetime;
        $lastDate = clone $datetime;
    }
    else if (strcasecmp($period, 'week') === 0) {

	    $datetimeDay = $datetime->format('N');

	    $firstDate = clone $datetime;
	    $firstDate->modify('-' . ($datetimeDay - 1) . ' days');

	    $lastDate = clone $datetime;
	    $lastDate->modify('+' . abs(6 - $datetimeDay). ' days');
    }
    else {
	    trigger_error('Period isn\'t "day" nor "week"');
	    return array();
    }

    $updated = false;
	$existedInDB = true;
	$timetable = $CI->Timetables->getJSON($resources);

    if (!empty($timetable)) {
	    $timetable = json_decode($timetable, true);

        $temp = clone $firstDate;
        $validTimeLimit = new DateTime();
        $validTimeLimit->setTimezone($timezone);
        $validTimeLimit = $validTimeLimit->modify('-1 day')->getTimeStamp();

        do {
            $year = $temp->format('Y');
            $week = $temp->format('W');
            $dayOfWeek = $temp->format('N');

            // If one day isn't up-to-date, update entire period
            if (!(array_key_exists($year, $timetable)
                && array_key_exists($week, $timetable[$year])
                && ($period === 'week'
                    || (array_key_exists($dayOfWeek, $timetable[$year][$week])
                        && $validTimeLimit < $timetable[$year][$week][$dayOfWeek]['updated'])))
            ) {
                $timetable = mergeArrays(_icsToTimetable(_getAdeRequest($resources, $firstDate, $lastDate)), $timetable);
                $updated = true;
                break;
            }

            $temp->modify('+1 ' . $period);
            $diff = $temp->diff($lastDate);
        } while ($diff->days !== 0 && $diff->invert !== 1);
    }
	// If no preexisting data was found
	else {
	    // Create from scratch
		$timetable = _icsToTimetable(_getAdeRequest($resources, $firstDate, $lastDate));
		$existedInDB = $timetable !== FALSE;
		$updated = true;
	}

	if ($updated === true) {
        if ($existedInDB) {
            $CI->Timetables->setJSON($resources, json_encode($timetable, JSON_PRETTY_PRINT));
        } else {
            $CI->Timetables->create($resources, json_encode($timetable, JSON_PRETTY_PRINT));
        }
    }

	return _narrow($timetable, $firstDate, $lastDate, $period);
}

/**
 * Reduce the array from the beginning date to the end date.
 * @param array $timetable The timetable to be restrained
 * @param DateTime $begin The first limit date
 * @param DateTime $end The second limit date
 * @param string $period The period you want ('day', 'week')
 * @return array Only the array element that are in the time limit.
 * If $begin and $end don't correspond to $period, $period has priority and
 * $period applied to $begin
 */
function _narrow($timetable, $begin, $end, $period)
{
    $final = array();

    $temp = clone $begin;
    do {
        $year = $temp->format('Y');
        $week = $temp->format('W');
        $dayOfWeek = $temp->format('N');

        if (array_key_exists($year, $timetable)
            && array_key_exists($week, $timetable[$year])
            &&  array_key_exists($dayOfWeek, $timetable[$year][$week])
        ) {
            if (!array_key_exists($year, $final))
                $final[$year] = array();
            if (!array_key_exists($week, $final[$year]))
                $final[$year][$week] = array();

            unset($timetable[$year][$week][$dayOfWeek]['updated']);
            $final[$year][$week][$dayOfWeek] = $timetable[$year][$week][$dayOfWeek];
        }

        $temp->modify('+1 day');
        $diff = $temp->diff($end);
    } while ($diff->days != 0 && $diff->invert != 1);


    $year = $begin->format('Y');
    $week = $begin->format('W');
    $dayOfWeek = $begin->format('N');

    // If necessary, reduce period to week
    if (in_array(strtolower($period), array('day', 'week'))) {
        if (!isset($final[$year]) || !isset($final[$year][$week])) {
            return array();
        }
        $final = $final[$year][$week];
    }

    // Then, if necessary, reduce week to day
    if (strcasecmp($period, 'day') === 0) {
        if (!isset($final[$dayOfWeek])) {
            return array();
        }
        $final = $final[$dayOfWeek];
    }

    return $final;
}

/**
 * Converts a ICS Timetable file to an array that represents the file in PHP
 * @param string $ics_filepath path to the ICS file
 * @return array The timetable in an array
 */
function _icsToTimetable($ics_filepath)
{
    global $timezone;

	// Remove beginning and ending whitespace characters
	$content = trim(file_get_contents($ics_filepath));

	// Check if file is valid
	if (startsWith('BEGIN:VCALENDAR', $content)
		&& endsWith('END:VCALENDAR', $content))
	{
		$VERSION_SUPPORTED = array('2.0');
		
		$ics = _strToIcs($content);

		// Check if file version is supported
		if (!array_key_exists('VERSION', $ics)
            || !in_array($ics['VERSION'] , $VERSION_SUPPORTED)
        ) {
			trigger_error('ICS File: Unsupported file version', E_USER_WARNING);
			return array();
		}

        $timetable = array();

		if (array_key_exists('VEVENT', $ics)) {
			// Sort each event at it's place, week then day
			foreach ($ics['VEVENT'] as $event) {
				$start_time = new DateTime($event['DTSTART']);
				$start_time->setTimezone($timezone);

				$year = $start_time->format('Y');
				if (!array_key_exists($year, $timetable)) {
                    $timetable[$year] = array();
                }
				
				$week = $start_time->format('W');
				if (!array_key_exists($week, $timetable[$year])) {
                    $timetable[$year][$week] = array();
                }
				
				$day = $start_time->format('N');
				if (!array_key_exists($day, $timetable[$year][$week])) {
					$timetable[$year][$week][$day] = array();
				}

                $description = explode('\n', $event['DESCRIPTION']);

				$groupLimit = 1;
				while (preg_match('/^(G[1-9]+)?S[1-9]+$/i', $description[$groupLimit])) {
				    $groupLimit++;
				}
				
				$groups = implode(', ', array_slice($description, 1, $groupLimit - 1));
				$teachers = implode(', ', array_slice($description, $groupLimit, -1));

                $timetable[$year][$week][$day]['updated'] = time();
                $timetable[$year][$week][$day][] = array(
                    'name' => $event['SUMMARY'],
                    'time_start' => $start_time->format('H:i'),
                    'time_end' => (new DateTime($event['DTEND']))->setTimezone($timezone)->format('H:i'),
                    'location' => $event['LOCATION'],
                    'groups' => $groups,
                    'teachers' => $teachers
                );
			}
		}

		return $timetable;
	}
	else {
		trigger_error('ICS File: Not a valid ICS file "' . $ics_filepath . '"');
		return array();
	}
}


/**
 * Converts a string to an array that represents an ICS element.
 * The string must contain the "BEGIN:" and "END:" lines of the element
 * @param string $str The string to analyze
 * @return array The representation of the ISC element
 */
function _strToIcs($str)
{
	$ics = array();
	
	$str = explode(PHP_EOL, trim($str));
	$len = count($str) - 1;

	if (!(startsWith('BEGIN:', $str[0])
		&& startsWith('END:', $str[$len])))
	{
        trigger_error('Not an ICS element');
		return array();
	}

	// Skip first and last lines, they're BEGIN and END of ics element
	for ($i = 1; $i < $len; $i++) {

		$curr_line = explode(':', $str[$i], 2);

		// Line is 'correct'
		if (count($curr_line) == 2) {
			if ($curr_line[0] == 'BEGIN') {
				// Create new ics element
				$element_type = $curr_line[1];
				$element_lines = '';
				
				// Read the whole element
				do {
                    $element_curr_line = $str[$i];

					// ADE only uses 73-74 characters per line,
                    // So add a new line only if last line isn't 73*n characters long
                    $lenLastLine = strlen(getLastLines($element_lines));
                    if ($lenLastLine % 73 != 0 && $lenLastLine % 74 != 0)
                    {
                        $element_lines .= PHP_EOL;
                    }

					$element_lines .=  $element_curr_line;
				} while ($element_curr_line !== 'END:'.$element_type && $i++);

				// Make sure there's no white character
				$element_type = trim($element_type);
				if (!array_key_exists($element_type, $ics))
					$ics[$element_type] = array();
				// Compute it
				$ics[$element_type][] = _strToIcs($element_lines);
				
			} else {
				// Add attribute value
				if (array_key_exists($curr_line[0], $ics))
					trigger_error('ICS File: Content "' . $curr_line[0] . '" overriden');
				$ics[$curr_line[0]] = $curr_line[1];
			}
		
		}
		// Line is not correct
		else {
			trigger_error('ICS File: Line "' . $str[$i] . '" is invalid'); 
		}
	}
	return $ics;
}

/**
 * Return the URL of the request to ADE.
 * @param int $resources resource number
 * @param DateTime $firstDate begin date
 * @param DateTime $lastDate end date
 * @return string The request to be used to contact ADE
 */
function _getAdeRequest($resources, $firstDate, $lastDate)
{
	return 'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
        . 'resources=' . $resources
        . '&projectId=1&calType=ical'
        . '&firstDate=' . $firstDate->format(DATE_FORMAT)
        . '&lastDate=' . $lastDate->format(DATE_FORMAT);
}

/**
 * Compare two timetable items.
 * Should be use with function usort().
 *
 * @param $item1
 * @param $item2
 * @return int 1 if $item1 is greater, -1 otherwise
 */
function sortTimetable($item1, $item2)
{
    return $item1['time_start'] > $item2['time_start'] ? 1 : -1;
}

/**
 * @param DateTime $date The date to be formatted
 * @return string A readabble date, in french
 */
function translateAndFormat($date)
{
    static $days = array(
        'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'
    );
    static $months = array(
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
        'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    );

    return $days[$date->format('w')] . ' '
        . $date->format('j') . ' '
        . $months[$date->format('n') - 1];
}

/**
 * Compute the height (in percentage) a DOM element should take
 * comparing the hours it reprents to a day of 10h.
 * To be used in views.
 *
 * @param string $begin The beginning of time
 * @param string $end The end of time
 * @return string Percentage the DOM element should take
 */
function computeTimeToHeight($begin, $end)
{
    $interval = abs(strtotime($begin) - strtotime($end));
    return ($interval / 360) . '%';
}

/**
 * Fills time in timetables.
 * To be used in views.
 *
 * @param $from
 * @param $to
 */
function fillTime($from, $to) {
    ?>
    <div class="fill hide-on-med-and-down" style="height: <?= computeTimeToHeight($from, $to) ?>"></div>
    <?php
}

if (!function_exists('mergeArrays'))
{
    /**
     * Merges two arrays.
     * In case of keys matching, takes the values of $original.
     * @param array $original The timetable with more priority
     * @param array $added The timetable with more priority
     * @return array The fusion of both timetables
     */
    function mergeArrays($original, $added)
    {
        foreach ($added as $key => $value) {
            if (is_array($value)) {
                // Make key exists if not
                if (!array_key_exists($key, $original))
                    $original[$key] = array();

                // Merge the sub array
                $original[$key] = mergeArrays($original[$key], $added[$key]);
            } else {
                if (!array_key_exists($key, $original))
                    $original[$key] = $added[$key];
            }
        }
        return $original;
    }
}

if (!function_exists('getLastLine'))
{
    /**
     * @param string $string A string with multiple lines
     * @param int $n The number of lines
     * @return string The last $n lines
     */
    function getLastLines($string, $n = 1)
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_slice($lines, -$n);
        return implode(PHP_EOL, $lines);
    }
}

if (!function_exists('swap'))
{
    /**
     * Swap the values of the two variables.
     * @param $x mixed
     * @param $y mixed
     */
    function swap(&$x, &$y)
    {
		$tmp = $x;
		$x = $y;
		$y = $tmp;
	}
}

if (!function_exists('startsWith'))
{
    /**
     * @param $str string The complete string
     * @param $sub string The potential start of $str
     * @return bool Whether $sub is the start of $str
     */
    function startsWith($sub, $str)
    {
		return substr($str, 0, strlen($sub)) === $sub;
	}
}

if (!function_exists('endsWith'))
{
    /**
     * @param $str string The complete string
     * @param $sub string The potential end the $str
     * @return bool Whether $sub is the end of $str or not
     */
    function endsWith($sub, $str)
    {
		return substr($str, strlen($str) - strlen($sub)) === $sub;
	}
}
