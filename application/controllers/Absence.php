<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absence extends TM_Controller
{
    public function student_index($semester = '')
    {
        if (!preg_match('/^S[1-4]$/', $semester)) {
            $semester = '';
        }

        $this->load->model('absence_model');
        $this->load->model('semester_model');

        // Loads the max semester type the student went to
        $max_semester = intval(
            substr($this->semester_model->getSemesterTypeFromId(
                $this->semester_model->getCurrentSemesterId($_SESSION['id'])
            ), 1)
        );

        if ($semester > 'S' . $max_semester) {
            addPageNotification('Vous essayez d\'accéder à un semestre futur !<br>Redirection vers votre semestre courant');
            $semester = '';
        }

        $semesterId = $this->semester_model->getSemesterId($semester, $_SESSION['id']);
        $semesterType = $this->semester_model->getSemesterTypeFromId($semesterId);

        $absences = $this->absence_model->getStudentSemesterAbsence($_SESSION['id'], $semesterId);

        $this->data = array(
            'maxSemester' => $max_semester,
            'semesterType' => $semesterType,
            'basePage' => 'Absence',
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
        $this->load->model('absence_model');
        $this->load->model('semester_model');
        $this->load->model('students_model');

        $this->load->helper('time');

        $period = $this->semester_model->getCurrentPeriod();
        $students = $this->students_model->getStudentsOrganized();
        $absences = $this->absence_model->getAbsencesInPeriod($period);

        // Associate absence to the student
        $abs_assoc = array();
        foreach ($absences as $absence) {
            $abs_assoc[$absence->numEtudiant][] = $absence;
        }

        // Associate students absences to the day it happened
        $groups = array();
        $assoc = array();

        foreach ($students as $student) {
            if (!isset($assoc[$student->numEtudiant])) {
                $assoc[$student->numEtudiant] = array(
                    'numEtudiant' => $student->numEtudiant,
                    'nom' => $student->nom,
                    'prenom' => $student->prenom,
                    'mail' => $student->mail,
                    'groupe' => $student->nomGroupe,
                    'absences' => array(
                        'total' => 0,
                        'totalDays' => 0,
                        'justified' => 0
                    )
                );

                if (isset($groups[$student->nomGroupe])) {
                    $groups[$student->nomGroupe] += 1;
                } else {
                    $groups[$student->nomGroupe] = 1;
                }
            }

            if (isset($abs_assoc[$student->numEtudiant])) {

                $assoc[$student->numEtudiant]['absences']['justified'] = 0;

                foreach ($abs_assoc[$student->numEtudiant] as $absence) {
                    $index = $period->getDays(new DateTime($absence->dateDebut));
                    $assoc[$student->numEtudiant]['absences'][$index][] = $absence;

                    if ($absence->justifiee) {
                        $assoc[$student->numEtudiant]['absences']['justified'] += 1;
                    }
                }

                $assoc[$student->numEtudiant]['absences']['total'] =
                    count($abs_assoc[$student->numEtudiant]);
                $assoc[$student->numEtudiant]['absences']['totalDays'] =
                    count($assoc[$student->numEtudiant]['absences']) - 3;
            }
        }

        $this->data = array(
            'absences' => $assoc,
            'groups' => $groups,
            'beginDate' => $period->getBeginDate(),
            'dayNumber' => $period->getDays(),
            'absenceTypes' => $this->absence_model->getAbsenceTypes()
        );

        $this->show('Absences');
    }
}
