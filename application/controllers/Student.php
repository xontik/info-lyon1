<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends TM_Controller
{
    private function _index()
    {
        $this->load->model('Students');
        $students = $this->Students->getAllOrganized();

        $studentList = $this->load->view(
            'includes/student_list', array(), TRUE
        );

        $this->data = array(
            'studentList' => $studentList
        );

        $this->setData('js', 'Common/student_list');
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
