<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $this->load->helper('calendar');

        $var = array(   'css' => array('edt_day'),
                        'js' => array('debug'),
                        'title' => 'Page de test',
                        'data' => array(
                            'calendar' => getCalendar(9306, '2017-06-14', 'day'),
                            'edt_view' => $this->load->view('includes/edt_day', NULL, TRUE)
                        ) );

        show('testv', $var);

    }

}
