<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        static $DAYS = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
        static $MONTHS = array(
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre','Décembre');

        $this->load->helper('calendar');

        $date = '2017-06-07 18:00:00';

        $calendar = getNextCalendar(9306, 'day', $date);

        $time = strtotime($date);
        $dateformat = $DAYS[ date('w', $time) ] . ' ' . date('j', $time) . ' ' . $MONTHS[ date('n', $time) - 1 ];
        $edt_view = $this->load->view('includes/edt_day', array('date' => $dateformat, 'calendar' => $calendar), TRUE);

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
