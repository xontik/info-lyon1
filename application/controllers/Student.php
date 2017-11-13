<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends TM_Controller
{
    private function _index()
    {
        $this->load->model('Students');
        $students = $this->Students->getAllOrganized();



        $this->data = array('students' => $students);
        $this->setData(array(
            'view' => 'Common/student.php',
        ));
        $this->show('Liste des élèves');
    }

    private function _profile($studentId)
    {
        if (is_null($studentId)) {
            show_404();
        }

        $this->load->model('Students');
        $this->load->model('Semesters');

        $student = $this->Students->get($studentId);

        $semesters = $this->Students->getSemesters($studentId);

        $averageBySemester = array();
        $averageTUBySemester = array();
        $absences = array();
        foreach ($semesters as $semester) {
            $averageBySemester[$semester->idSemester] = $this->Students->getSubjectsAverage($studentId, $semester->idSemester);
            $absences[$semester->idSemester] = $this->Semesters->getStudentAbsence($semester->idSemester,$studentId);
            $averageTUBySemester[$semester->idSemester] = array();
            $tus = $this->Students->getSubjectsTUAverage($studentId, $semester->idSemester);
            foreach ($tus as $tu) {
                $averageTUBySemester[$semester->idSemester][$tu->idTeachingUnit] = $tu;
            }

        }



        $this->data = array(    'semesters' => $semesters,
                                'student' => $student,
                                'averageBySemester' => $averageBySemester,
                                'absences' => $absences,
                                'averageTUBySemester' => $averageTUBySemester
                                );
        $this->setData(array(
            'view' => 'Common/student_profile.php',
            'js' => 'Common/student_profile'
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
