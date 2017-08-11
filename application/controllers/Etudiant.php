<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 27/04/2017
 * Time: 10:06
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiant extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $newdata = array(
            'username'  => 'johndoe',
            'email'     => 'johndoe@some-site.com',
            'id' => 'p1600006'
        );

        $this->session->set_userdata($newdata);
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Tableau de bord'
        );
        show('Etudiant/dashboard', $data);
    }

    public function absence($semestre = '') {
		
		$this->load->model('absence_model', 'absenceMod');
        $this->load->model('semester_model', 'semesterMod');

        if ($semestre === '') {
            $semestreId = $this->semesterMod->getCurrentSemesterId($_SESSION['id']);
        } else {
            $semestreId = $this->semesterMod->getLastSemesterOfType($semestre, $_SESSION['id']);
            if ( empty($semestreId) )
                $semestreId = $this->semesterMod->getCurrentSemesterId($_SESSION['id']);
        }

        $absences = $semestreId !== FALSE ?
            $this->absenceMod->getAbsencesFromSemester($_SESSION['id'], $semestreId) :
            array();

		$var = array(
            'css' => array('absences_page'),
            'js' => array('debug'),
            'title' => 'Absences',
			'data' => array('absences' => $absences)
        );
		
        show('Etudiant/absences', $var);
    }

    public function note($semestre = '') {

        $this->load->model('mark_model','markMod');
        $this->load->model('semester_model', 'semesterMod');

        if ($semestre === '') {
            $semestreId = $this->semesterMod->getCurrentSemesterId($_SESSION['id']);
        } else {
            $semestreId = $this->semesterMod->getLastSemesterOfType($semestre, $_SESSION['id']);
            if ( empty($semestreId) )
                $semestreId = $this->semesterMod->getCurrentSemesterId($_SESSION['id']);
        }

        $marks = $this->markMod->getMarksFromSemester($_SESSION['id'], $semestreId);

        $css = array('test');
        $js = array('debug');
        $title = 'Notes';
        $data = array('marks' => $marks);

        $var = array(
            'css' => $css,
            'js' => $js,
            'title' => $title,
            'data' => $data);

        show('Etudiant/notes', $var);
    }

    public function ptut() {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Projets tuteurés'
        );
        show('Etudiant/ptut', $data);
    }

    public function edt() {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Emploi du temps'
        );
        show('Etudiant/edt', $data);
    }

    public function question() {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Questions / Réponses'
        );
        show('Etudiant/questions', $data);
    }
}
