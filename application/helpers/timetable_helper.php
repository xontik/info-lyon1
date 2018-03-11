<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('DATE_FORMAT', 'Y-m-d');

/**
 * Look for the next non-empty timetable in the 3 next days.
 *
 * @param int           $resource
 * @param string        $period     'day' or 'week'
 * @param DateTime|int  $datetime   Date or week number (default: today)
 * @return array Formatted timetable
 *
 * @see getTimetable()
 */
function getNextTimetable($resource, $period, $datetime = NULL) {
    global $timezone;
    $timezone = new DateTimeZone('Europe/Paris');
    $instance = &get_instance();

    $instance->load->config('timetable');

    if ($datetime === NULL) {
        $datetime = new DateTime();
        $datetime->setTimezone($timezone);
    }
    else if (is_numeric($datetime)) {
        $weekNum = (int) $datetime;
        $datetime = new DateTime();
        $datetime->setTimezone($timezone);
    }

    $dayOffset = 0;

    // If hour >= 18:00, take next day timetable
    if ($datetime->format('H') >= 18) {
        $datetime->modify('+1 day');
        $datetime->setTime(0, 0);
        $dayOffset = 1;
    }

    $dayNum = $datetime->format('N');

    // If strict week
    if (isset($weekNum)) {
        if (strcasecmp($period, 'week') !== 0) {
            trigger_error('Can\'t get the timetable of a day from a week number');
            return array();
        }

        $weekDiff = $weekNum - $datetime->format('W');
        $datetime->modify($weekDiff. ' week');

        $dayDiff = $weekDiff === ($dayNum >= 6 ? 1 : 0)
            ? -($datetime->format('N') - 1) // Difference to monday
            : 7 - $datetime->format('N');   // Difference to sunday

        $datetime->modify($dayDiff . ' day');
        $datetime->setTime(0, 0);

    } else {

        // If week-end and not strict week
        if ($dayNum >= 6) {
            // Goto monday
            $nextMondayDiff = 8 - $dayNum;
            $datetime->modify('+' . $nextMondayDiff . ' day');
            $datetime->setTime(0, 0);
            $dayOffset += $nextMondayDiff;
        }
    }

    $result = getTimetable($resource, $period, $datetime);

    if (strcasecmp($period, 'day') === 0) {
        $datetimeClone = clone $datetime;
        $daySearchLimit = $instance->config->item('daySearchLimit');

        // Look at next not empty timetable within 3 days
        while ($dayOffset < $daySearchLimit && empty($result['timetable'])) {
            $datetimeClone->modify('+1 day');
            $result = getTimetable($resource, $period, $datetimeClone);
            $dayOffset++;
        }

        if (!empty($result['timetable'])) {
            if ($dayOffset !== 0) {
                $datetimeClone->setTime(0, 0);
            }

            $datetime = $datetimeClone;
            usort($result['timetable'], 'sortTimetable');
        }
    } else {
        foreach ($result['timetable'] as $key => $day) {
            usort($result['timetable'][$key], 'sortTimetable');
        }
    }

    return $result + array('date' => $datetime);
}

/**
 * Get the timetable of a period.
 *
 * @param int       $resource
 * @param string    $period     'day' or 'week'
 * @param DateTime  $datetime   (default: today)
 * @return array Formatted timetable
 */
function getTimetable($resource, $period, $datetime = NULL)
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
        $beginDate = clone $datetime;
        $endDate = clone $datetime;
    }
    else if (strcasecmp($period, 'week') === 0) {

	    $datetimeDay = $datetime->format('N');

	    $beginDate = clone $datetime;
	    $beginDate->modify('-' . ($datetimeDay - 1) . ' days');

	    $endDate = clone $datetime;
	    $endDate->modify('+' . abs(6 - $datetimeDay). ' days');
    }
    else {
	    trigger_error('Period isn\'t "day" nor "week"');
	    return array();
    }

    $updated = false;
	$existedInDB = true;
	$timetable = $CI->Timetables->getJSON($resource);

    if (!empty($timetable)) {
	    $timetable = json_decode($timetable, true);

        $temp = clone $beginDate;
        $validTimeLimit = (new DateTime())
            ->setTimezone($timezone)
            ->modify('-1 day')
            ->getTimeStamp();

        do {
            $year = $temp->format('Y');
            $week = $temp->format('W');
            $dayOfWeek = $temp->format('N');

			// TODO This condition must cause problems, spread it !
            // If one day isn't up-to-date, update entire period
            if (!(array_key_exists($year, $timetable)
                && array_key_exists($week, $timetable[$year])
                && (($period === 'week'
                        && $validTimeLimit < $timetable[$year][$week]['updated'])
                    || (array_key_exists($dayOfWeek, $timetable[$year][$week])
                        && $validTimeLimit < $timetable[$year][$week][$dayOfWeek]['updated'])))
            ) {
                $timetable = mergeArrays(
                    _icsToTimetable(
                        _getAdeRequest($resource, $beginDate, $endDate),
                        $beginDate,
                        $endDate
                    ),
                    $timetable
                );
                $updated = true;
                break;
            }

            $temp->modify('+1 ' . $period);
            $diff = $temp->diff($endDate);
        } while ($diff->days !== 0 && $diff->invert !== 1);
    }
	// If no preexisting data was found
	else {
	    // Create from scratch
		$timetable = _icsToTimetable(
            _getAdeRequest($resource, $beginDate, $endDate),
            $beginDate,
            $endDate
        );
		$existedInDB = $timetable !== FALSE;
		$updated = true;
	}

	if ($updated === true) {
        if ($existedInDB) {
            $CI->Timetables->setJSON($resource, json_encode($timetable, JSON_PRETTY_PRINT));
        } else {
            $CI->Timetables->create($resource, json_encode($timetable, JSON_PRETTY_PRINT));
        }
    }
    
	return _narrow($timetable, $beginDate, $endDate, $period);
}

/**
 * Reduce the array to the time between begin date and end date.
 * If $begin and $end don't correspond to $period, $period has priority and
 * $period is applied to $begin
 *
 * @param array     $timetable
 * @param DateTime  $begin
 * @param DateTime  $end
 * @param string    $period     'day' or 'week'
 * @return array
 */
function _narrow($timetable, $begin, $end, $period)
{
    $final = array();

    $minTime = '24:00';
    $maxTime = '00:00';

    $temp = clone $begin;
    do {
        $year = $temp->format('Y');
        $week = $temp->format('W');
        $dayOfWeek = $temp->format('N');

        if (array_key_exists($year, $timetable)
            && array_key_exists($week, $timetable[$year])
            &&  array_key_exists($dayOfWeek, $timetable[$year][$week])
        ) {
            if (!array_key_exists($year, $final)) {
                $final[$year] = array();
            }
            if (!array_key_exists($week, $final[$year])) {
                $final[$year][$week] = array();
            }

            unset($timetable[$year][$week][$dayOfWeek]['updated']);

            if (!empty($timetable[$year][$week][$dayOfWeek])) {
                $day = $timetable[$year][$week][$dayOfWeek];

                if ($day['earliestTime'] < $minTime) {
                    $minTime = $day['earliestTime'];
                }
                if ($day['latestTime'] > $maxTime) {
                    $maxTime = $day['latestTime'];
                }

                unset($day['earliestTime']);
                unset($day['latestTime']);

                $final[$year][$week][$dayOfWeek] = $day;
            }
        }

        $temp->modify('+1 day');
        $diff = $temp->diff($end);
    } while ($diff->days != 0 && $diff->invert != 1);

    $year = $begin->format('Y');
    $week = $begin->format('W');
    $dayOfWeek = $begin->format('N');

    $final = $final[$year][$week];

    // If necessary, reduce week to day
    if (strcasecmp($period, 'day') === 0) {
        if (!isset($final[$dayOfWeek])) {
            $final = array();
        } else {
            $final = $final[$dayOfWeek];
        }
    }

    return array(
        'timetable' => $final,
        'minTime' => $minTime,
        'maxTime' => $maxTime
    );
}

/**
 * Converts a ICS Timetable file to an array that represents the file in PHP.
 *
 * @param string $icsFilepath
 * @param DateTime $beginDate
 * @param DateTime $endDate
 * @return array
 */
function _icsToTimetable($icsFilepath, $beginDate, $endDate)
{
    global $timezone;

	// Remove beginning and ending whitespace characters
	$content = trim(file_get_contents($icsFilepath));

	// Check if file is valid
	if (startsWith('BEGIN:VCALENDAR', $content)
		&& endsWith('END:VCALENDAR', $content))
	{
		$VERSION_SUPPORTED = array(2.0);
		
		$ics = _strToIcs($content);

		// Check if file version is supported
		if (!array_key_exists('VERSION', $ics)
            || !in_array($ics['VERSION'] , $VERSION_SUPPORTED)
        ) {
			trigger_error('ICS File: Unsupported file version', E_USER_WARNING);
			return array();
		}

        $timetable = array();
        $now = time();

        if (array_key_exists('VEVENT', $ics)) {
			// Sort each event at it's place, week then day

            foreach ($ics['VEVENT'] as $event) {
				$startTime = new DateTime($event['DTSTART']);
				$startTime->setTimezone($timezone);
				$endTime = new DateTime($event['DTEND']);
				$endTime->setTimezone($timezone);

				$year = $startTime->format('Y');
				if (!array_key_exists($year, $timetable)) {
                    $timetable[$year] = array();
                }
				
				$week = $startTime->format('W');
				if (!array_key_exists($week, $timetable[$year])) {
                    $timetable[$year][$week] = array(
                        'updated' => $now
                    );
                }
				
				$day = $startTime->format('N');
				if (!array_key_exists($day, $timetable[$year][$week])) {
					$timetable[$year][$week][$day] = array(
                        'updated' => $now,
                        'earliestTime' => 24,
                        'latestTime' => 0
                    );
				}

				// Earliest and latest time
                $hour = $startTime->format('H:i');
                if ($hour < $timetable[$year][$week][$day]['earliestTime']) {
                    $timetable[$year][$week][$day]['earliestTime'] = $hour;
                }

                $hour = $endTime->format('H:i');
                if ($hour > $timetable[$year][$week][$day]['latestTime']) {
                    $timetable[$year][$week][$day]['latestTime'] = $hour;
                }

                // Compute description
                $description = explode('\n', $event['DESCRIPTION']);

				$groupLimit = 1;
				while (preg_match('/^(G[1-9]+)?S[1-9]+$/i', $description[$groupLimit])) {
				    $groupLimit++;
				}
				
				$groups = implode(', ', array_slice($description, 1, $groupLimit - 1));
				$teachers = implode(', ', array_slice($description, $groupLimit, -1));

				// Creates event
                $timetable[$year][$week][$day][] = array(
                    'name' => $event['SUMMARY'],
                    'timeStart' => $startTime->format('H:i'),
                    'timeEnd' => $endTime->format('H:i'),
                    'location' => str_replace('\\', '', $event['LOCATION']),
                    'groups' => $groups,
                    'teachers' => $teachers
                );
            }
		}

		$tempDate = clone $beginDate;
		do {
            $year = $tempDate->format('Y');
            if (!array_key_exists($year, $timetable)) {
                $timetable[$year] = array();
            }

            $week = $tempDate->format('W');
            if (!array_key_exists($week, $timetable[$year])) {
                $timetable[$year][$week] = array(
                    'updated' => $now
                );
            }

            $day = $tempDate->format('N');
            if (!array_key_exists($day, $timetable[$year][$week])) {
                $timetable[$year][$week][$day] = array(
                    'updated' => $now
                );
            }

            $tempDate->modify('+1 day');
            $diff = $tempDate->diff($endDate);
        } while ($diff->days !== 0 && !$diff->invert);

		return $timetable;
	}
	else {
		trigger_error('ICS File: Not a valid ICS file "' . $icsFilepath . '"');
		return array();
	}
}


/**
 * Converts a string to an array that represents the ICS element.
 * The string must contain the "BEGIN:" and "END:" lines of the element
 *
 * @param string $str
 * @return array
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

		$currLine = explode(':', $str[$i], 2);

		// Line is 'correct'
		if (count($currLine) == 2) {
			if ($currLine[0] == 'BEGIN') {
				// Create new ics element
				$elementType = $currLine[1];
				$elementLines = '';
				
				// Read the whole element
				do {
                    $elementCurrLine = $str[$i];

					// ADE only uses 73-74 characters per line,
                    // So add a new line only if last line isn't 73*n characters long
                    $lenLastLine = strlen(getLastLines($elementLines));
                    if ($lenLastLine % 73 !== 0 && $lenLastLine % 74 !== 0) {
                        $elementLines .= PHP_EOL;
                    }

					$elementLines .=  $elementCurrLine;
				} while ($elementCurrLine !== 'END:'.$elementType && $i++);

				// Make sure there's no whitespace character
				$elementType = trim($elementType);
				if (!array_key_exists($elementType, $ics)) {
                    $ics[$elementType] = array();
                }
				// Compute it
				$ics[$elementType][] = _strToIcs($elementLines);
				
			} else {
				// Add attribute value
				if (array_key_exists($currLine[0], $ics))
					trigger_error('ICS File: Content "' . $currLine[0] . '" overriden');
				$ics[$currLine[0]] = $currLine[1];
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
 *
 * @param int       $resource
 * @param DateTime  $beginDate
 * @param DateTime  $endDate
 * @return string
 */
function _getAdeRequest($resource, $beginDate, $endDate)
{
	return 'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
        . 'resources=' . $resource
        . '&projectId=1&calType=ical'
        . '&firstDate=' . $beginDate->format(DATE_FORMAT)
        . '&lastDate=' . $endDate->format(DATE_FORMAT);
}

/**
 * Compare two timetable items.
 * Should be used with function usort().
 *
 * @param array $item1
 * @param array $item2
 * @return int As specified by function 'usort'
 */
function sortTimetable($item1, $item2)
{
    return $item1['timeStart'] > $item2['timeStart'] ? 1 : -1;
}

/**
 * Converts a time string to a float.
 *
 * @param $time
 * @return float
 */
function timeToFloat($time) {
    $time = DateTime::createFromFormat('H:i', $time);

    $float = (float) $time->format('H');
    $float += (float) $time->format('i') / 60;
    return $float;
}

/**
 * Converts a float to a time string
 *
 * @param $float
 * @return string
 */
function floatToTime($float) {
    $hours = floor($float);
    $minutes = ($float - $hours) * 60;

    return sprintf('%02d:%02d', $hours, $minutes);
}

/**
 * @param DateTime  $date   The date to be formatted
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
 * Compute the height (in percent) a DOM element should take
 * comparing the hours it represents to the hours in the day.
 * To be used in views.
 *
 * @param string    $begin
 * @param string    $end
 * @param int       $hoursInDay
 * @return string
 */
function computeTimeToHeight($begin, $end, $hoursInDay)
{
    return ((timeToFloat($end) - timeToFloat($begin)) / $hoursInDay) * 100 . '%';
}

/**
 * Fills time in timetables.
 * To be used in views.
 *
 * @param string    $from
 * @param string    $to
 * @param int       $hoursInDay
 */
function fillTime($from, $to, $hoursInDay) {
    ?>
    <div class="fill hide-on-small-and-down" style="height: <?= computeTimeToHeight($from, $to, $hoursInDay)
    ?>"></div>
    <?php
}

if (!function_exists('mergeArrays'))
{
    /**
     * Merges two arrays.
     * In case of keys matching, takes the values of $original.
     *
     * @param array $original
     * @param array $added
     * @return array
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
     * Returns the last lines of a string.
     *
     * @param string    $string
     * @param int       $n      The number of lines
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
     *
     * @param mixed $x
     * @param mixed $y
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
     * Checks if a string begins with another string.
     *
     * @param string    $subject    The subject string
     * @param string    $sub        The substring
     * @return bool
     */
    function startsWith($sub, $subject)
    {
		return substr($subject, 0, strlen($sub)) === $sub;
	}
}

if (!function_exists('endsWith'))
{
    /**
     * Checks if a string ends with another string.
     *
     * @param string    $subject    The subject string
     * @param string    $sub        The substring
     * @return bool
     */
    function endsWith($sub, $subject)
    {
		return substr($subject, strlen($subject) - strlen($sub)) === $sub;
	}
}
