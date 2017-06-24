<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $this->load->helper('calendar');

        $var = array(   "css" => array(),
                        "js" => array('debug'),
                        "title" => 'Page de test',
                        "data" => array(
                            "edt" => getCalendar(9306, '2017-06-14', 'week')
                        ) );

        show("testv", $var);

    }

}
