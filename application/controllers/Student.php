<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends TM_Controller
{
    private function _index()
    {
        $this->setData(array(
            'view' => 'Common/student',
            'js' => 'Common/student',
            'css' => 'Common/student'
        ));
        $this->show('Liste des élèves');
    }

    private function _profile($studentId)
    {
        if (is_null($studentId)) {
            show_404();
        }
        $this->setData(array(
            'view' => 'Common/student_profile.php',
        ));

        $this->show('Cursus élève');
    }

    public function teacher_index()
    {
        $this->_index();
    }

    public function secretariat_index()
    {
        $this->_index();
    }

    public function teacher_profile($studentId = null)
    {
        $this->_profile($studentId);
    }

    public function secretariat_profile($studentId = null)
    {
        $this->_profile($studentId);
    }

}
