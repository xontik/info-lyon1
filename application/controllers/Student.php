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

        $this->load->model('Students');
        $this->load->model('Semesters');

        $student = $this->Students->get($studentId);
        $semesters = $this->Students->getSemesters($studentId);

        $averageBySemester = array();
        $averageTUBySemester = array();
        $absences = array();
        $totalAvgs = array();
        
        foreach ($semesters as $semester) {
            $averageBySemester[$semester->idSemester] = $this->Students->getSubjectsAverage($studentId, $semester->idSemester);

            $period = $this->Semesters->getPeriod($semester->idSemester);
            $absences[$semester->idSemester] = $this->Students->getAbsencesCount($studentId, $period);

            $averageTUBySemester[$semester->idSemester] = array();
            $tus = $this->Students->getSubjectsTUAverage($studentId, $semester->idSemester);

            $sumTU = 0;
            $sumTUGroup = 0;
            $sumCoeff = 0;
            foreach ($tus as $tu) {
                $averageTUBySemester[$semester->idSemester][$tu->idTeachingUnit] = $tu;
                $sumTU += $tu->average * $tu->coefficient;
                $sumTUGroup += $tu->groupAverage * $tu->coefficient;
                $sumCoeff += $tu->coefficient;
            }

            
            if ($sumCoeff > 0) {
                $totalAvg = $sumTU / $sumCoeff;
                $totalAvgGroup = $sumTUGroup / $sumCoeff;
            } else {
                $totalAvg = null;
                $totalAvgGroup = null;
            }
            
            $totalAvgs[$semester->idSemester] = array(
              'student' => $totalAvg,
              'group' => $totalAvgGroup
            );
        }

        $this->data = array(
            'semesters' => $semesters,
            'student' => $student,
            'averageBySemester' => $averageBySemester,
            'absences' => $absences,
            'averageTUBySemester' => $averageTUBySemester,
            'totalAvgs' => $totalAvgs
        );
        
        $this->setData(array(
            'view' => 'Common/student_profile.php',
            'js' => 'Common/student_profile',
            'css' => 'Common/student_profile',
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
