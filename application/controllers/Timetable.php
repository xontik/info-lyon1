<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends TM_Controller
{
    public function student_index($args)
    {
        $this->show('Emploi du temps');
    }

    public function teacher_index($args)
    {
        $this->show('Emploi du temps');
    }
}
