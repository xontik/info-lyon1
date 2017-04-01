<?php defined('BASEPATH') OR exit('No direct script access allowed');

function getCalendar($resources, $firstDate = NULL, $lastDate = NULL)
{
	global $DATE_FORMAT;
	$DATE_FORMAT = 'Y-m-d';
	
	// Test the optionnal parameters
	// (res, 'day' [, date])
	if ( strcasecmp($firstDate, 'day') === 0 )
	{
		_test_date($lastDate);
		$firstDate = $lastDate;
	}
	// (res, date, 'day')
	else if ( strcasecmp($lastDate, 'day') === 0 )
	{
		_test_date($firstDate);
		$lastDate = $firstDate;
	}
	// (res, 'week' [, date])
	else if ( strcasecmp($firstDate, 'week') === 0 )
	{
		_test_date($lastDate);
		$time = strtotime($lastDate);
		$dayofweek = date('N', $time);
		
		$firstDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) - ($dayofweek - 1), date('Y', $time)));
		$lastDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) + (7 - $dayofweek), date('Y', $time)));
	}
	// (res, date, 'week')
	else if ( strcasecmp($lastDate, 'week') === 0 )
	{
		_test_date($firstDate);
		$time = strtotime($firstDate);
		$dayofweek = date('N', $time);
		
		$firstDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) - ($dayofweek - 1), date('Y', $time)));
		$lastDate = date($DATE_FORMAT, mktime(0, 0, 0, date('m', $time), date('d', $time) + (7 - $dayofweek), date('Y', $time)));
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
	
	$calendar = array();
	$updated = false;
	
	//TODO Dev: Check if exists in database
	if ( file_exists('assets/calendar' . $resources . '.json')) {
		//TODO Dev: Load from database
		$calendar = file_get_contents('assets/calendar' . $resources . '.json');
		
		if ($calendar === FALSE) {
			trigger_error('Could not read json file "assets/calendar' . $resources . '.json"', E_USER_WARNING);
			unlink('assets/calendar' . $resources . '.json');
			return array();
		} else {
			$calendar = json_decode($calendar, true);
		}
		
		$year = date('Y', strtotime($firstDate));
		$week = date('W', strtotime($firstDate));
		
		// Make sure datas exists
		if ( !array_key_exists($year, $calendar)
			|| !array_key_exists($week, $calendar[$year]) )
		{
			$calendar = _icsToArray(
					'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
					. 'calType=ical'
					. '&resources=' . $resources
					. '&projectId=3'
					. '&firstDate=' . $firstDate
					. '&lastDate=' . $lastDate
				) + $calendar;
			$updated = true;
		} else {
			
			//Make sure datas are up-to-date (2 days old max)
			if ( $firstDate == $lastDate ) {
				$dayinweek = date('N', strtotime($firstDate));
				// If content was updated before two days ago
				if ( array_key_exists($dayinweek, $calendar[$year][$week]) &&
					strtotime($calendar[$year][$week][$dayinweek]['updated'])
					< mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 2, date('y')))
				{
					$calendar = _icsToArray(
							'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
							. 'calType=ical'
							. '&resources=' . $resources
							. '&projectId=3'
							. '&firstDate=' . $firstDate
							. '&lastDate=' . $lastDate
						) + $calendar;
					$updated = true;
				}
			} else {
				$temp = $firstDate;
				
				while ( $temp != $lastDate ) {
					$dayinweek = date('N', strtotime($temp));
					// If one day isn't up-to-date, update entire period
					if ( !array_key_exists($dayinweek, $calendar[$year][$week]) ||
						$calendar[$year][$week][$dayinweek]['updated']
						< mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 2, date('y')) )
					{
						$calendar = _icsToArray(
								'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
								. 'calType=ical'
								. '&resources=' . $resources
								. '&projectId=3'
								. '&firstDate=' . $firstDate
								. '&lastDate=' . $lastDate
							) + $calendar;
						$updated = true;
						break;
					}
					
					$temp = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('y')));
				}
			}
		}
	}
	// If no preexisting data was found
	else {
		$calendar = _icsToArray(
				'http://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?'
				. 'calType=ical'
				. '&resources=' . $resources
				. '&projectId=3'
				. '&firstDate=' . $firstDate
				. '&lastDate=' . $lastDate
			);
		$updated = true;
	}
	
	if ( $updated && 
		! file_put_contents('assets/calendar' . $resources . '.json', json_encode($calendar, JSON_PRETTY_PRINT)))
	{
		trigger_error('Could not write json file "assets/calendar' . $resources . '.json"', E_USER_WARNING);
		unlink('assets/calendar' . $resources . '.json');
	}

	return $calendar;
}

function _icsToArray($ics_filepath) {
	
	// Remove whitespace chars if there are
	$content = trim( file_get_contents($ics_filepath) );
	
	// Check if file is valid
	if (startsWith($content, 'BEGIN:VCALENDAR')
		&& endsWith($content, 'END:VCALENDAR'))
	{
		$VERSION_SUPPORTED = array('2.0');
		
		$ics = _strToIcs($content);
		$array = array();
		
		// Check if file version is supported
		if ( !in_array( trim($ics['VERSION']), $VERSION_SUPPORTED) ) {
			trigger_error('ICS File: Unsupported calendar version', E_USER_WARNING);
			return array();
		}
		
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
						'description' => $event['DESCRIPTION'],
					);
			}
		}
		
		return $array;
	}
	else
	{
		trigger_error('ICS File: Not a valid ics pathfile "' . $ics_filepath . '"');
		return array();
	}
}

function _strToIcs($str) {
	$ics = array();
	
	$str = explode(PHP_EOL, trim($str));
	$len = count($str) - 1;
	
	if ( !( startsWith($str[0], 'BEGIN:' )
		&& startsWith($str[$len], 'END:' ) ))
	{
		return array();
	}
	
	// Skip first and last lines, they're BEGIN and END of ics element
	for ($i = 1; $i < $len; $i++) {
		$curr_line = explode(':', $str[$i], 2);
		if (count($curr_line) == 2) {
			if ($curr_line[0] == 'BEGIN') {
				// Create new ics element
				$element_type = $curr_line[1];
				$element_lines = "";
				
				// Read the whole element
				do {
					$element_curr_line = $str[$i];
					$element_lines .= $element_curr_line . PHP_EOL;
				} while ($element_curr_line !== 'END:'.$element_type && $i++);
				
				$element_type = trim($element_type);
				if ( !array_key_exists($element_type, $ics))
					$ics[$element_type] = array();
				// Compute it
				$ics[$element_type][] = _strToIcs($element_lines);
				
			} else {
				// Add attribute value
				if ( array_key_exists($curr_line[0], $ics))
					trigger_error('ICS File: Content "' . $curr_line[0] . '" overriden');
				$ics[$curr_line[0]] = $curr_line[1];
			}
		} else {
			trigger_error('ICS File: Line ' . ($i+1) . ' invalid');
		}
	}
	return $ics;
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

if ( !function_exists('swap') )
{
	function swap(&$x, &$y) {
		$tmp = $x;
		$x = $y;
		$y = $tmp;
	}
}

if ( !function_exists('startsWith') )
{
	function startsWith($str, $sub) {
		return substr($str, 0, strlen($sub)) === $sub;
	}
}

if ( !function_exists('endsWith') )
{
	function endsWith($str, $sub) {
		return substr($str, strlen($str) - strlen($sub)) === $sub;
	}
}