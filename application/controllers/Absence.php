<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absence extends TM_Controller
{
    public function student_index($semester = '')
    {
        if (!preg_match('/^S[1-4]$/', $semester)) {
            $semester = '';
        }
        $this->load->model('Semesters');

        // Loads the max semester type the student went to
        $max_semester = (int) substr($this->Semesters->getType(
                $this->Semesters->getStudentCurrent($_SESSION['id'])
            ), 1
        );

        if ($semester !== '' && $semester > 'S' . $max_semester) {
            addPageNotification('Vous essayez d\'accéder à un semestre futur !<br>Redirection vers votre semestre courant');
            $semester = '';
        }

        $semesterId = $this->Semesters->getSemesterId($semester, $_SESSION['id']);
        $semesterType = $this->Semesters->getType($semesterId);

        $absences = $this->Semesters->getStudentAbsence($_SESSION['id'], $semesterId);

        $this->data = array(
            'semesterTabs' => array(
                'max' => $max_semester,
                'semester' => $semesterType,
                'basePage' => 'Absence',
            ),
            'absences' => $absences
        );

        $this->show('Absences');
    }

    public function teacher_index()
    {
        $this->show('Absences');
    }

    public function secretariat_index()
    {
        $this->load->model('Absences');
        $this->load->model('Semesters');
        $this->load->model('Students');

        $this->load->helper('time');

        $period = $this->Semesters->getCurrentPeriod();
        $students = $this->Students->getAllOrganized();
        $unsortedAbsences = $this->Absences->getInPeriod($period);

        // Associate absence to the student
        $absences = array();
        foreach ($unsortedAbsences as $absence) {
            $absences[$absence->idStudent][] = $absence;
        }

        // Associate students absences to the day it happened
        $groups = array();
        $assoc = array();

        foreach ($students as $student) {

            if (!isset($assoc[$student->idStudent])) {
                $student->absences = array(
                    'total' => 0,
                    'totalDays' => 0,
                    'justified' => 0
                );

                $assoc[$student->idStudent] = $student;

                if (isset($groups[$student->groupName])) {
                    $groups[$student->groupName] += 1;
                } else {
                    $groups[$student->groupName] = 1;
                }
            }

            if (isset($absences[$student->idStudent])) {

                foreach ($absences[$student->idStudent] as $absence) {
                    $index = $period->getDays(new DateTime($absence->beginDate));
                    $assoc[$student->idStudent]->absences[$index][] = $absence;

                    if ($absence->justified) {
                        $assoc[$student->idStudent]->absences['justified'] += 1;
                    }
                }

                $assoc[$student->idStudent]->absences['total'] =
                    count($absences[$student->idStudent]);
                $assoc[$student->idStudent]->absences['totalDays'] =
                    count($assoc[$student->idStudent]->absences) - 3;
            }
        }

        $this->data = array(
            'absences' => $assoc,
            'groups' => $groups,
            'beginDate' => $period->getBeginDate(),
            'dayNumber' => $period->getDays(),
            'absenceTypes' => $this->Absences->getTypes()
        );

        $this->show('Absences');
    }
}