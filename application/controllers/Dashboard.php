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
            $sideEDT = $this->load->view(
                'includes/side-edt',
                array('date' => $now, 'timetable' => false),
                TRUE
            );
        } else {
            $timetable = getNextTimetable($adeResource, 'day', $date);
            $sideEDT = $this->load->view(
                'includes/side-edt',
                array('date' => $now, 'timetable' => $timetable),
                TRUE
            );
        }

        $this->data = array(
            'side-edt' => $sideEDT
        );

        $this->show('Tableau de bord');
    }

}
