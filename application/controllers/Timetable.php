<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends TM_Controller
{
    private function _index($adeResource, $weekNum)
    {
        $this->load->helper('timetable');

        if ($adeResource === FALSE) {
            $this->data = array('date' => new DateTime(), 'timetable' => false);
        } else {
            $timetableDate = is_numeric($weekNum) ? $weekNum : new DateTime();
            $this->data = getNextTimetable($adeResource, 'week', $timetableDate);
        }

        $this->data['resource'] = $adeResource;

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
