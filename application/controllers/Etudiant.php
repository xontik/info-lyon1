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
    $data = array(
      'css' => array('Etudiant/dashboard'),
      'js' => array('debug'),
      'title' => 'Tableau de bord',
      'data' => array('hello world !')
    );
    show('Etudiant/dashboard', $data);
  }

  public function absence($semester = '') {
    $this->load->model('absence_model', 'absenceMod');
    $this->load->model('semester_model', 'semesterMod');

    $semesterId = $this->semesterMod->getSemesterId($semester);
    if ($semesterId === FALSE) {
        $semesterId = $this->semesterMod->getSemesterId();
    }

    $absences = $this->absenceMod->getStudentSemesterAbsence($_SESSION['id'], $semesterId);

    $var = array(
      'css' => array('Etudiant/absences'),
      'js' => array('debug'),
      'title' => 'Absences',
      'data' => array(
          'absences' => $absences
      )
    );

    show('Etudiant/absences', $var);
  }

  public function note($semester = '') {

    $this->load->model('mark_model','markMod');
    $this->load->model('semester_model', 'semesterMod');

    $semesterId = $this->semesterMod->getSemesterId($semester);
    if ($semesterId === FALSE)
      $semesterId = $this->semesterMod->getSemesterId();

    $marks = $this->markMod->getMarksFromSemester($_SESSION['id'], $semesterId);

    $var = array(
      'css' => array('Etudiants/notes'),
      'js' => array('debug'),
      'title' => 'Notes',
      'data' => array('marks' => $marks)
    );

    show('Etudiant/notes', $var);
  }

  public function ptut() {
    $data = array(
      'css' => array('Etudiant/ptut'),
      'js' => array(),
      'title' => 'Projets tuteurés'
    );
    show('Etudiant/ptut', $data);
  }

  public function edt() {
    $data = array(
      'css' => array('Etudiant/edt'),
      'js' => array(),
      'title' => 'Emploi du temps'
    );
    show('Etudiant/edt', $data);
  }

  public function question() {
    $data = array(
      'css' => array('Etudiant/questions'),
      'js' => array(),
      'title' => 'Questions / Réponses'
    );
    show('Etudiant/questions', $data);
  }
}
