<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends TM_Controller
{
    public function student_index()
    {
        $this->load->helper('timetable');

        $date = new DateTime();
        $timetable = getNextTimetable(9311, 'day', $date);

        $side_edt = $this->load->view(
            'includes/side-edt',
            array('date' => $date, 'timetable' => $timetable),
            TRUE
        );

        $this->data = array(
            'side-edt' => $side_edt
        );

        $this->show('Tableau de bord');
    }

}
