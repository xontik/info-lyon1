<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends TM_Controller
{
    public function student_index()
    {
        $this->show('Emploi du temps');
    }

    public function student_edit()
    {
        $this->setData('view', 'Common/timetable_edit.php');
        $this->show('Modification de l\'emploi du temps');
    }

    public function teacher_index()
    {
        $this->show('Emploi du temps');
    }

    public function teacher_edit()
    {
        $this->setData('view', 'Common/timetable_edit.php');
        $this->show('Modification de l\'emploi du temps');
    }
}
