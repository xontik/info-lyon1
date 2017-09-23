<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secretariat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
            redirect('/');
    }

    public function index() {
        $this->absence();
    }

    public function absence($semester = '') {
        $this->load->model('absence_model');
        $this->load->model('semester_model');
        $this->load->model('students_model');

        $this->load->helper('year');

        $period = $this->semester_model->getSemesterPeriod(
            $this->semester_model->getSemesterId($semester)
        );

        $students = $this->students_model->getStudents();
        $absences =  $this->absence_model->getAbsencesInPeriod($period);

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
                $assoc[$student->numEtudiant] = array (
                    'numEtudiant' => $student->numEtudiant,
                    'nom' => $student->nom,
                    'prenom' => $student->prenom,
                    'mail' => $student->mail,
                    'groupe' => $student->nomGroupe,
                    'absences' => array()
                );

                if (isset($groups[$student->nomGroupe])) {
                    $groups[$student->nomGroupe] += 1;
                } else {
                    $groups[$student->nomGroupe] = 1;
                }
            }

            if (isset($abs_assoc[$student->numEtudiant])) {
                foreach ($abs_assoc[$student->numEtudiant] as $absence) {
                    $index = $period->getDays(new DateTime($absence->dateDebut));
                    $assoc[$student->numEtudiant]['absences'][$index] = $absence;
                }
            }
        }

        $data = array(
            'css' => array('Secretariat/absences'),
            'js' => array('debug', 'absences_table'),
            'title' => 'Absences',
            'data' => array(
                'absences' => $assoc,
                'groups' => $groups,
                'begin_date' => $period->getBeginDate(),
                'day_number' => $period->getDays()
            )
        );
        show("Secretariat/absences", $data);
    }

}
