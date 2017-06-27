<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @param $resources int The resources number in ADE
 * @param null $firstDate mixed The first limit date or 'DAY' or 'WEEK'
 * @param null $lastDate mixed The second limit date or 'DAY' or 'WEEK'
 * @return array The formatted calendar
 */
function getCalendar($resources, $firstDate = NULL, $lastDate = NULL)
{
    $CI = get_instance();
    $CI->load->model('calendar_model');

	global $DATE_FORMAT;
	$DATE_FORMAT = 'Y-m-d';

	$period = "UNDEFINED";
	
	// Test the optional parameters
	// (res, 'day' [, date])
	if ( strcasecmp($firstDate, 'day') === 0 )
	{
		_test_date($lastDate);
		$firstDate = $lastDate;
		$period = 'DAY';
	}
	// (res, date, 'day')
	else if ( strcasecmp($lastDate, 'day') === 0 )
	{
		_test_date($firstDate);
		$lastDate = $firstDate;
		$period = 'DAY';
	}
	// (res, 'week' [, date])
	else if ( strcasecmp($firstDate, 'week') === 0 )
	{
		_test_date($lastDate);
		$time = strtotime($lastDate);
		$dayOfWeek = date('N', $time);
		$period = 'WEEK';
		
		$firstDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) - ($dayOfWeek - 1), date('Y', $time)));
		$lastDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) + (7 - $dayOfWeek), date('Y', $time)));
	}
	// (res, date, 'week')
	else if ( strcasecmp($lastDate, 'week') === 0 )
	{
		_test_date($firstDate);
		$time = strtotime($firstDate);
		$dayOfWeek = date('N', $time);
        $period = 'WEEK';
		
		$firstDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) - ($dayOfWeek - 1), date('Y', $time)));
		$lastDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) + (7 - $dayOfWeek), date('Y', $time)));
	}
	// (res [, beginDate [, endDate]])
	else
	{
		_test_date($firstDate);
		_test_date($lastDate);
		
		// Make dates in right order
		// To give ADE a correct url
		$firstTime = strtotime($firstDate);
		$lastTime = strtotime($lastDate);
		
		if ( $firstTime > $lastTime )
			swap($firstDate, $lastDate);
	}
	
	
	$updated = false;
	$existedInDB = true;
	$calendar = $CI->calendar_model->getCalendarJSON($resources);

	if (!empty($calendar)) {

        $calendar = json_decode($calendar, true);

        // Make sure the data exists in the file
        $exists = true;

        $beginYear = date('Y', strtotime($firstDate));
        $endYear = date('Y', strtotime($lastDate));

        for ($i = $beginYear; $i <= $endYear && $exists; $i++) {
            if (!array_key_exists($i, $calendar)) {
                $exists = false;
            } else {
                // if first year, take week of first date, else begin to first week of year
                $beginWeek = ($i == $beginYear) ? date('W', strtotime($firstDate)) : 0;
                // if last year, take week of last date, else end to last week of year
                $endWeek = ($i == $endYear) ? date('W', strtotime($lastDate)) : 51;

                for ($j = $beginWeek; $j <= $endWeek && $exists; $j++) {
                    if (!array_key_exists($j, $calendar[$i])) {
                        $exists = false;
                    } else {
                        $beginDayOfWeek = ($j == $beginWeek ? date('N', strtotime($firstDate)) : 0);
                        $endDayOfWeek = ($j == $endWeek ? date('N', strtotime($lastDate)) : 6);

                        for ($k = $beginDayOfWeek; $k <= $endDayOfWeek && $exists; $k++) {
                            if (!array_key_exists($k, $calendar[$i][$j]))
                                $exists = false;
                        }

                    }
                }

                if (!$exists)
                    break;
            }
        }

        if (!$exists) {

            $calendar = merge_arrays(_icsToCalendar(_getAdeRequest($resources, $firstDate, $lastDate)), $calendar);

            $updated = true;

        } else {

            //Make sure data is up-to-date
            $temp = $firstDate;
            $validTimeLimit = mktime(
                date('H'),
                date('i'),
                date('s'),
                date('m'),
                date('d') - 1,
                date('y')
            );

            do {
                $year = date('Y', strtotime($temp));
                $week = date('W', strtotime($temp));
                $dayOfWeek = date('N', strtotime($temp));

                // If one day isn't up-to-date, update entire period
                if (array_key_exists($year, $calendar) &&
                    array_key_exists($week, $calendar[$year]) &&
                    array_key_exists($dayOfWeek, $calendar[$year][$week]) &&
                    $validTimeLimit > $calendar[$year][$week][$dayOfWeek]['updated']
                ) {
                    $calendar = _icsToCalendar(_getAdeRequest($resources, $firstDate, $lastDate)) + $calendar;
                    $updated = true;
                    break;
                }

                $temp = date('Y-m-d', strtotime($temp . '+1 day'));
            } while ($temp < $lastDate);

        }

    }
	// If no preexisting data was found
	else {
		$calendar = _icsToCalendar(_getAdeRequest($resources, $firstDate, $lastDate));
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
				
				$array[$year][$week][$day][] = array(
						'name' => $event['SUMMARY'],
						'time_start' => date('H:i', $start_time),
						'time_end' => date('H:i', strtotime($event['DTEND'])),
						'location' => $event['LOCATION'],
						'description' => $event['DESCRIPTION']
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

/**
 * Reduce the array from the beginning date to the end date
 * @param $array array The complete array, to be restrained
 * @param $begin string The first limit date
 * @param $end string The second limit date
 * @param $period string What period you want ('DAY', 'WEEK', 'UNDEFINED')
 * @return array Only the array element that are in the time limit.
 * If $begin and $end don't correspond to $period, $period has priority and
 * the result the period applied to $begin
 */
function _narrow($array, $begin, $end, $period = 'UNDEFINED') {
	$final = array();
	
	$beginTime = strtotime($begin);
	$endTime = strtotime($end);
	
	if ($endTime < $beginTime)
		swap($beginTime, $endTime);
	
	$beginYear = date('Y', $beginTime);
	$beginWeek = date('W', $beginTime) % 52;
	$beginDay = date('N', $beginTime);
	
	$endYear = date('Y', $endTime);
	$endWeek = date('W', $endTime) % 52;
	$endDay = date('N', $endTime);
	
	for ($i = $beginYear; $i <= $endYear; $i++) {
		$beginWeekInYear = ($i == $beginYear) ? $beginWeek : 0;
		$endWeekInYear = ($i == $endYear) ? $endWeek : 51;
		
		for ($j = $beginWeekInYear; $j <= $endWeekInYear; $j++) {
			$beginDayInWeek = ($i == $beginYear && $j == $beginWeek) ? $beginDay : 0;
			$endDayInWeek = ($i == $endYear && $j == $endWeek) ? $endDay : 6;
			
			for ($k = $beginDayInWeek; $k <= $endDayInWeek; $k++) {
				if ( array_key_exists($i, $array) &&
					array_key_exists($j, $array[$i]) && 
					array_key_exists($k, $array[$i][$j]) )
				{
					if ( !array_key_exists($i, $final) )
						$final[$i] = array();
					if ( !array_key_exists($j, $final[$i]) )
						$final[$i][$j] = array();
					$final[$i][$j][$k] = $array[$i][$j][$k];
				}
			}
		}
	}

	if (in_array($period, array('DAY', 'WEEK')))
	    $final = $final[$beginYear][$beginWeek];

	if ($period === 'DAY')
        $final = $final[$beginDay];

    return $final;
}

function _test_date(&$date) {
	static $REGEX_DATE_FORMAT = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/';
	global $DATE_FORMAT;
	
	if ($date === NULL) {
		// By default, $date is today
		$date = date($DATE_FORMAT);
	}
	else if ( !preg_match($REGEX_DATE_FORMAT, $date) )
	{
		trigger_error('ICS ERROR: Date is wrongly formatted ( ' . $date . ' )', E_USER_NOTICE);
		$date = date($DATE_FORMAT);
	}
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
