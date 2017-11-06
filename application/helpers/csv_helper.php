<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function csvToArray($filepath, $_s = ';') {
    $csv = array();
    try {
        ini_set('auto_detect_line_endings', TRUE);
        $file = fopen($filepath, 'r');

        $line = fgetcsv($file, 0, $_s);
        if (substr($line[0],0,3) !== 'sep' ) {
            $csv[] = $line;
        }
        while ($line = fgetcsv($file, 0, $_s)) {
            if ($line[0]) {
                $csv[] = $line;
            }
        }

        fclose($file);
        ini_set('auto_detect_line_endings', FALSE);
        if($_s == ';' && $csv[0][0] != 'IUT' ) {
            return csvToArray($filepath,',');
        }
        return $csv;
    } catch (Exception $e) {
        return false;
    }

}

function isCSVFile($name) {
    try {
        $format = strtolower(array_slice(
            explode('.', $name), -1)[0]);
        return $format === 'csv';
    } catch (Exception $e) {
        return false;
    }
}

function arrayToCsv($array, $_s = ','){
    $out = "sep=".$_s.PHP_EOL;
    foreach ($array as $line) {
        foreach ($line as $key => $value) {
            if ($key === 'newline') {
                for ($i = 0; $i < $value; $i++) {
                    $out .= PHP_EOL;
                }
            }
            else if (!is_numeric($key)) {
                if ($key == 'editable' && $value == false) {
                    $out .= '<--Donnees non modifiables';
                } else {
                    $out .= $key . $_s . $value . $_s;
                }
            } else {
                $out .= $value . $_s;
            }
        }
        $out .= PHP_EOL;
    }
    return $out;

}
