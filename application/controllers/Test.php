<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $css = array("test");
        $js = array("debug");
        $title = "Premier essai !";
        $data = array("a" => "donnée a","b" => "donnée b","c" => "donnée c");
        $var = array(   "css" => $css,
                        "js" => $js,
                        "title" => $title,
                        "data" => $data);

		$this->calendar(9306);
        show("testv", $var);

    }

    public function calendar($ressource = 9305){
        $this->load->helper('calendar');
        $cal = getCalendar($ressource, '2017-03-27', '2017-03-31');
        echo '<pre>';
        print_r($cal);
        echo '</pre>';

    }
}
