<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @param $resources int The resources number in ADE
 * @param $period string The period you want (day or week)
 * @param null $datetime At which date you want the calendar. Default is today.
 * @return array Formatted calendar
 */
function getNextCalendar($resources, $period, &$datetime = NULL) {
    if ($datetime === NULL) {
        $datetime = new DateTime();
    }

    $originalDate = clone $datetime;
    $limit = 0;

    // If hour >= 18h, take next day calendar
    if ($datetime->format('H') >= 18) {
        $datetime->modify('+1 day');
        $limit = 1;
    }

    $calendar = getCalendar($resources, $period, $datetime);

    while ($limit < 3 && empty($calendar)) {
        $datetime->modify('+1 day');
        $calendar = getCalendar($resources, $period, $datetime);
        $limit++;
    }

    if (empty($calendar))
        $datetime = $originalDate;

    return $calendar;
}

/**
 * @param $resources int The resources number in ADE
 * @param $period string The period you want (day or week)
 * @param null $datetime DateTime At which date you want the calendar. Default is today.
 * @return array The formatted calendar
 */
function getCalendar($resources, $period, $datetime = NULL)
{
    global $DATE_FORMAT;
    $DATE_FORMAT = 'Y-m-d';

    $CI = get_instance();
    $CI->load->model('calendar_model');

	if ($datetime === NULL) {
        $datetime = new DateTime();
    }

	if (strcasecmp($period, 'day') === 0) {

        $firstDate = clone $datetime;
        $lastDate = clone $datetime;
    }
    else if (strcasecmp($period, 'week') === 0) {

	    $datetimeDay = $datetime->format('N');

	    $firstDate = clone $datetime;
	    $firstDate->modify('-' . ($datetimeDay - 1) . ' days');

	    $lastDate = clone $datetime;
	    $lastDate->modify('+' . abs(5 - $datetimeDay). ' days');

    } else {
	    trigger_error('Period isn\'t "day" nor "week"');
	    return array();
    }

    $updated = false;
	$existedInDB = true;
	$calendar = $CI->calendar_model->getCalendarJSON($resources);

	if (!empty($calendar)) {

        $calendar = json_decode($calendar, true);

        $temp = clone $firstDate;
        $validTimeLimit = new DateTime();
        $validTimeLimit = $validTimeLimit->modify('-1 day')->getTimeStamp();

        do {
            $year = $temp->format('Y');
            $week = $temp->format('W');
            $dayOfWeek = $temp->format('N');

            // If one day isn't up-to-date, update entire period
            if ( !( array_key_exists($year, $calendar) &&
                array_key_exists($week, $calendar[$year]) &&
                array_key_exists($dayOfWeek, $calendar[$year][$week]) &&
                $validTimeLimit < $calendar[$year][$week][$dayOfWeek]['updated'] ))
            {
                $firstDateFormat = $firstDate->format($DATE_FORMAT);
                $lastDateFormat = $lastDate->format($DATE_FORMAT);
                $calendar = merge_arrays(_icsToCalendar(_getAdeRequest($resources, $firstDateFormat, $lastDateFormat)), $calendar);
                $updated = true;
                break;
            }

            $temp->modify('+1 day');
            $diff = $temp->diff($lastDate);
        } while ($diff->days != 0 && $diff->invert != 1);

    }
	// If no preexisting data was found
	else {

		$calendar = _icsToCalendar(_getAdeRequest(
		    $resources,
            $firstDate->format($DATE_FORMAT),
            $lastDate->format($DATE_FORMAT))
        );
		$existedInDB = false;
		$updated = true;
	}

	if ($updated === true)
        if ($existedInDB)
            $CI->calendar_model->setCalendarJSON($resources, json_encode($calendar, JSON_PRETTY_PRINT));
        else
	        $CI->calendar_model->createCalendar($resources, json_encode($calendar, JSON_PRETTY_PRINT));

	return _narrow($calendar, $firstDate, $lastDate, $period);
}

/**
 * @param $date DateTime The date to be formatted
 * @return string A string showing date formatted for display, in french
 */
function translateAndFormat($date) {
    static $DAYS = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    static $MONTHS = array(
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre','Décembre');

    return $DAYS[ $date->format('w') ] . ' '
        . $date->format('j') . ' '
        . $MONTHS[ $date->format('n') - 1 ];
}

/**
 * Compute the height (in percentage) a DOM element should take
 * comparing the hours it reprents to a day of 10h.
 * @param $begin string The beginning of time
 * @param $end string The end of time
 * @return string Percentage the DOM element should take
 */
function computeTimeToHeight($begin, $end) {
    static $hoursNumber = 10 * 3600;
    $interval = abs(strtotime($begin) - strtotime($end));

    return ($interval / $hoursNumber * 100) . '%';
}

/**
 * Reduce the array from the beginning date to the end date
 * @param $array array The complete array, to be restrained
 * @param $begin DateTime The first limit date
 * @param $end DateTime The second limit date
 * @param $period string The period you want ('day', 'week')
 * @return array Only the array element that are in the time limit.
 * If $begin and $end don't correspond to $period, $period has priority and
 * the result the period applied to $begin
 */
function _narrow($array, $begin, $end, $period) {
    $final = array();

    $temp = clone $begin;
    do {
        $year = $temp->format('Y');
        $week = $temp->format('W');
        $dayOfWeek = $temp->format('N');

        if ( array_key_exists($year, $array) &&
            array_key_exists($week, $array[$year]) &&
            array_key_exists($dayOfWeek, $array[$year][$week]) )
        {
            if ( !array_key_exists($year, $final) )
                $final[$year] = array();
            if ( !array_key_exists($week, $final[$year]) )
                $final[$year][$week] = array();

            unset($array[$year][$week][$dayOfWeek]['updated']);
            $final[$year][$week][$dayOfWeek] = $array[$year][$week][$dayOfWeek];
        }

        $temp->modify('+1 day');
        $diff = $temp->diff($end);
    } while ($diff->days != 0 && $diff->invert != 1);

    $year = $begin->format('Y');
    $week = $begin->format('W');
    $dayOfWeek = $begin->format('N');


    if (in_array(strtolower($period), array('day', 'week'))) {
        if ( !isset($final[$year]) || !isset($final[$year][$week]) )
            return array();

        $final = $final[$year][$week];
    }

    if (strcasecmp($period, 'day') === 0) {
        if ( !isset($final[$dayOfWeek]) )
            return array();

        $final = $final[$dayOfWeek];
    }

    return $final;
}

/**
 * Converts a ICS Calendar file to an array that represents the file in PHP
 * @param $ics_filepath string path to the ICS file
 * @return array The calendar in an array
 */
function _icsToCalendar($ics_filepath) {
	
	// Remove beginning and ending whitespace characters
	$content = trim( file_get_contents($ics_filepath) );

	// Check if file is valid
	if (startsWith('BEGIN:VCALENDAR', $content)
		&& endsWith('END:VCALENDAR', $content))
	{
		$VERSION_SUPPORTED = array('2.0');
		
		$ics = _strToIcs($content);
		
		// Check if file version is supported
		if ( array_key_exists('VERSION', $ics) &&
            !in_array( $ics['VERSION'] , $VERSION_SUPPORTED )
        ) {
			trigger_error('ICS File: Unsupported calendar version', E_USER_WARNING);
			return array();
		}

        $array = array();

		if ( array_key_exists('VEVENT', $ics)) {
			// Sort each event at it's place, week then day
			foreach ($ics['VEVENT'] as $event) {
				$start_time = strtotime($event['DTSTART']);
				$year = date('Y', $start_time);
				if (!array_key_exists($year, $array))
					$array[$year] = array();
				
				$week = date('W', $start_time);
				if ( !array_key_exists($week, $array[$year]) )
					$array[$year][$week] = array();
				
				$day = date('N', $start_time);
				if ( !array_key_exists($day, $array[$year][$week]) ) {
					$array[$year][$week][$day] = array();
					$array[$year][$week][$day]['updated'] = time();
				}

				$description = explode("\\n", $event['DESCRIPTION']);

				$groupLimit = 1;
				while ( preg_match('/^(G[1-9])?S[1-9]$/i', $description[$groupLimit]) ) {
				    $groupLimit++;
				}
				
				$groups = implode(', ', array_slice($description, 1, $groupLimit - 1));
				$teachers = implode(', ', array_slice($description, $groupLimit, -1));

				$array[$year][$week][$day][] = array(
						'name' => $event['SUMMARY'],
						'time_start' => date('H:i', $start_time),
						'time_end' => date('H:i', strtotime($event['DTEND'])),
						'location' => $event['LOCATION'],
                        'groups' => $groups,
                        'teachers' => $teachers
					);
			}
		}
		
		return $array;
	}
	else
	{
		trigger_error('ICS File: Not a valid ICS file "' . $ics_filepath . '"');
		return array();
	}
}

/**
 * Converts a string to an array that represents an ICS element.
 * The string must contain the "BEGIN:" and "END:" lines of the element
 * @param $str string The string to analyze
 * @return array The representation of the ISC element
 */
function _strToIcs($str) {

	$ics = array();
	
	$str = explode(PHP_EOL, trim($str));
	$len = count($str) - 1;

	if ( !( startsWith( 'BEGIN:', $str[0] )
		&& startsWith( 'END:', $str[$len] ) ))
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
                    $lenLastLine = strlen( getLastLine( $element_lines ) );
                    if ( $lenLastLine % 73 != 0 && $lenLastLine % 74 != 0 )
                    {
                        $element_lines .= PHP_EOL;
                    }

					$element_lines .=  $element_curr_line;
				} while ($element_curr_line !== 'END:'.$element_type && $i++);

				// Make sure there's no white character
				$element_type = trim($element_type);
				if ( !array_key_exists($element_type, $ics) )
					$ics[$element_type] = array();
				// Compute it
				$ics[$element_type][] = _strToIcs($element_lines);
				
			} else {
				// Add attribute value
				if ( array_key_exists($curr_line[0], $ics))
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
 * @param $resources int resource number
 * @param $firstDate string begin date
 * @param $lastDate string end date
 * @return string The request to be used to contact ADE
 */
function _getAdeRequest($resources, $firstDate, $lastDate) {
	return 'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
				. 'calType=ical'
				. '&resources=' . $resources
				. '&projectId=3'
				. '&firstDate=' . $firstDate
				. '&lastDate=' . $lastDate;
}

if (! function_exists('merge_arrays')) {
    /**
     * Merges two arrays.
     * In case of keys matching, takes the values of $original.
     * @param $original array The calendar with more priority
     * @param $added array The calendar with more priority
     * @return array The fusion of both calendars
     */
    function merge_arrays($original, $added)
    {
        foreach ($added as $key => $value) {
            if (is_array($value)) {

                // Make key exists if not
                if (!array_key_exists($key, $original))
                    $original[$key] = array();

                // Merge the sub array
                $original[$key] = merge_arrays($original[$key], $added[$key]);
            } else {
                if (!array_key_exists($key, $original))
                    $original[$key] = $added[$key];
            }
        }

        return $original;
    }
}

if ( !function_exists('getLastLine') )
{
    /**
     * @param $string string A string with multiple lines
     * @param int $n int The number of line you want
     * @return string The last $n lines
     */
    function getLastLine($string, $n = 1) {
        $lines = explode(PHP_EOL, $string);
        $lines = array_slice($lines, -$n);
        return implode(PHP_EOL, $lines);
    }
}

if ( !function_exists('swap') )
{
    /**
     * Swap the values of the two variables.
     * @param $x mixed
     * @param $y mixed
     */
    function swap(&$x, &$y) {
		$tmp = $x;
		$x = $y;
		$y = $tmp;
	}
}

if ( !function_exists('startsWith') )
{
    /**
     * @param $str string The complete string
     * @param $sub string The potential start of $str
     * @return bool Whether $sub is the start of $str
     */
    function startsWith($sub, $str) {
		return substr( $str, 0, strlen($sub) ) === $sub;
	}
}

if ( !function_exists('endsWith') )
{
    /**
     * @param $str string The complete string
     * @param $sub string The potential end the $str
     * @return bool Whether $sub is the end of $str or not
     */
    function endsWith($sub, $str) {
		return substr($str, strlen($str) - strlen($sub)) === $sub;
	}
}
