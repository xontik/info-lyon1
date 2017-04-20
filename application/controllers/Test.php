<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $this->load->helper('calendar');

        $css = array("test");
        $js = array("debug");
        $title = "Premier essai !";
        $data = array( getCalendar(9306, '2017-03-27', '2017-03-31') );

        $var = array(   "css" => $css,
                        "js" => $js,
                        "title" => $title,
                        "data" => $data);

        show("testv", $var);

    }
}
