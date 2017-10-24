<?php
/**
* Created by PhpStorm.
* User: xontik
* Date: 27/04/2017
* Time: 10:06
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiant extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student')
            redirect('/');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $this->load->helper('timetable');

        $date = new DateTime();
        $timetable = getNextTimetable(9311, 'day', $date);

        $side_edt = $this->load->view(
            'includes/side-edt',
            array('date' => $date, 'timetable' => $timetable),
            TRUE
        );

        $data = array(
            'css' => array('Etudiant/dashboard'),
            'js' => array('debug'),
            'title' => 'Tableau de bord',
            'data' => array(
                'side-edt' => $side_edt
            )
        );
        show('Etudiant/dashboard', $data);
    }

    public function absence($semester = '') {
        $this->load->model('absence_model');
        $this->load->model('semester_model');

        // Loads the max semester type the student went to
        $max_semester = intval(
            substr($this->semester_model->getSemesterTypeFromId(
                $this->semester_model->getCurrentSemesterId($_SESSION['id'])
            ), 1)
        );

        $semesterId = $this->semester_model->getSemesterId($semester);
        $semester = $this->semester_model->getSemesterTypeFromId($semesterId);

        $absences = $this->absence_model->getStudentSemesterAbsence($_SESSION['id'], $semesterId);

        $data = array(
            'css' => array('Etudiant/absences'),
            'js' => array('debug'),
            'page' => 'absences',
            'title' => 'Absences',
            'data' => array(
                'max_semester' => $max_semester,
                'semester' => $semester,
                'absences' => $absences
            )
        );

        show('Etudiant/absences', $data);
    }

    public function note($semester = '') {

        $this->load->model('mark_model','markMod');
        $this->load->model('semester_model', 'semesterMod');

        $semesterId = $this->semesterMod->getSemesterId($semester);

        if ($semesterId === FALSE) {
            $semesterId = $this->semesterMod->getSemesterId();
        }

        $marks = $this->markMod->getMarksFromSemester($_SESSION['id'], $semesterId);

        $data = array(
            'css' => array('Etudiants/notes'),
            'js' => array('debug'),
            'page' => 'notes',
            'title' => 'Notes',
            'data' => array(
                'marks' => $marks
            )
        );
        show('Etudiant/notes', $data);
    }

    public function ptut() {
        $data = array(
            'css' => array('Etudiant/ptut'),
            'js' => array('debug'),
            'page' => 'ptut',
            'title' => 'Projets tuteurés'
        );
        show('Etudiant/ptut', $data);
    }

    public function edt() {
        $data = array(
            'css' => array('Etudiant/edt'),
            'js' => array('debug'),
            'page' => 'edt',
            'title' => 'Emploi du temps'
        );
        show('Etudiant/edt', $data);
    }

    public function question() {
        $data = array(
            'css' => array('Etudiant/questions'),
            'js' => array('debug'),
            'page' => 'questions',
            'title' => 'Questions / Réponses'
        );
        show('Etudiant/questions', $data);
    }
}
