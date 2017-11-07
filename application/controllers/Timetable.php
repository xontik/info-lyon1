<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends TM_Controller
{
    private function _index($adeResource, $weekNum)
    {
        $this->load->helper('timetable');

        $date = new DateTime();

        if (is_numeric($weekNum)) {
            $weekDiff = $weekNum - $date->format('W');
            $date->modify($weekDiff . ' week');
        }

        if ($adeResource === FALSE) {
            $this->data['timetable'] = false;
        } else {
            $this->data['timetable'] = getNextTimetable($adeResource, 'week', $date);
        }
        $this->data['date'] = $date;

        $this->setData(array(
            'view' => 'Common/timetable.php',
            'css' => 'Common/timetable',
            'js' => 'Common/timetable'
        ));
        $this->show('Emploi du temps');
    }

    private function _edit()
    {
        $this->setData('view', 'Common/timetable_edit.php');
        $this->show('Modification de l\'emploi du temps');
    }

    public function student_index($weekNum = '')
    {
        $this->load->model('Students');

        $adeResource = $this->Students->getADEResource($_SESSION['id']);

        $this->_index($adeResource, $weekNum);
    }

    public function student_edit()
    {
       $this->_edit();
    }

    public function teacher_index($weekNum = '')
    {
        $this->load->model('Teachers');

        $adeResource = $this->Teachers->getADEResource($_SESSION['id']);

        $this->_index($adeResource, $weekNum);
    }

    public function teacher_edit()
    {
        $this->_edit();
    }
}
