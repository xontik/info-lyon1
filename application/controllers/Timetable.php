<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends TM_Controller
{
    private function _index($adeResource)
    {
        $this->load->helper('timetable');

        $now = new DateTime();

        if ($adeResource === FALSE) {
            $this->data['timetable'] = false;
        } else {
            $this->data['timetable'] = getNextTimetable($adeResource, 'week', $now);
        }
        $this->data['date'] = $now;

        $this->setData('view', 'Common/timetable.php');
        $this->setData('css', 'Common/timetable');
        $this->show('Emploi du temps');
    }

    private function _edit()
    {
        $this->setData('view', 'Common/timetable_edit.php');
        $this->show('Modification de l\'emploi du temps');
    }

    public function student_index()
    {
        $this->load->model('Students');

        $adeResource = $this->Students->getADEResource($_SESSION['id']);

        $this->_index($adeResource);
    }

    public function student_edit()
    {
       $this->_edit();
    }

    public function teacher_index()
    {
        $this->load->model('Teachers');

        $adeResource = $this->Teachers->getADEResource($_SESSION['id']);

        $this->_index($adeResource);
    }

    public function teacher_edit()
    {
        $this->_edit();
    }
}
