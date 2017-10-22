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
      'css' => array(),
      'js' => array(),
      'title' => 'Tableau de bord'
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
      'css' => array(),
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
    $this->load->model('question_model', 'questionsMod');
    $this->load->model('students_model', 'studentsMod');
    $this->load->model('teacher_model', 'teacherMod');
    
    //Poser une question
    if (isset($_POST['q_titre']) AND isset($_POST['q_texte']) AND isset($_POST['q_idProfesseur']) AND is_numeric($_POST['q_idProfesseur'])) {
        $titre = htmlspecialchars($_POST['q_titre']);
        $texte = htmlspecialchars($_POST['q_texte']);
        $idProf = (int) htmlspecialchars($_POST['q_idProfesseur']);
        $numEtu = $_SESSION['id'];
        $this->questionsMod->ask($titre, $texte, $idProf, $numEtu);
    }
    
    //Répondre à une question
    if (isset($_POST['r_texte']) AND isset($_POST['r_idQuestion']) AND is_numeric($_POST['r_idQuestion'])){
        $idQuestion = (int) $_POST['r_idQuestion'];
        $texte = htmlspecialchars($_POST['r_texte']);
        $isProf = ($_SESSION['user_type'] == 'teacher') ? 1 : 0;
        $this->questionsMod->answer($idQuestion, $texte, $isProf);
    }
    
    //Récupérer les questions posées
    $etuQuestions = $this->questionsMod->getStudentQuestions($_SESSION['id']);
    
    //Récupérer les profs
    $etuTeachers = $this->studentsMod->getProfesseursByStudent($_SESSION['id']);

    $data = array(
      'css' => array(),
      'js' => array('debug'),
      'title' => 'Questions / Réponses',
      'data' => array('etuQuestions' => $etuQuestions,
                      'etuTeachers' => $etuTeachers)
    );
    show('Etudiant/questions', $data);
  }
  
  
  
}
