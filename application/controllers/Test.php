<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $this->load->helper('calendar');

        $date = new DateTime('2017-06-09');
        $calendar = getNextCalendar(9306, 'day', $date);

        $edt_view = $this->load->view('includes/edt_day',
            array('date' => translateAndFormat($date), 'calendar' => $calendar), TRUE);

        $var = array(   'css' => array('edt_day'),
                        'js' => array('debug'),
                        'title' => 'Page de test',
                        'data' => array(
                            'calendar' => $calendar,
                            'edt_view' => $edt_view
                        ) );

        show('testv', $var);

    }

}
