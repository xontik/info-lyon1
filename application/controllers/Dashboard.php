<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends TM_Controller
{
    public function student_index()
    {
        $this->load->model('Students');

        $this->load->helper('timetable');

        $adeResource = $this->Students->getADEResource($_SESSION['id']);

        if ($adeResource === FALSE) {
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                array('date' => new DateTime(), 'timetable' => false),
                TRUE
            );
        } else {
            $result = getNextTimetable($adeResource, 'day');
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                array('date' => $result['date'], 'timetable' => $result['timetable']),
                TRUE
            );
        }

        $this->data = array(
            'side-timetable' => $sideTimetable
        );

        $this->show('Tableau de bord');
    }

}
