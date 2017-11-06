<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends TM_Controller
{
    public function student_index()
    {
        $this->load->model('Students');

        $this->load->helper('timetable');

        $now = new DateTime();
        $adeResource = $this->Students->getADEResource($_SESSION['id']);

        if ($adeResource === FALSE) {
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                array('date' => $now, 'timetable' => false),
                TRUE
            );
        } else {
            $timetable = getNextTimetable($adeResource, 'day', $now);
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                array('date' => $now, 'timetable' => $timetable),
                TRUE
            );
        }

        $this->data = array(
            'side-timetable' => $sideTimetable
        );

        $this->show('Tableau de bord');
    }

}
