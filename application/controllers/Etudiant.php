<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiant extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student')
            redirect('/');

    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Tableau de bord'
        );
        show('Etudiant/dashboard', $data);
    }

    public function absence($semester = '')
    {

        $this->load->model('absence_model', 'absenceMod');
        $this->load->model('semester_model', 'semesterMod');

        $semesterId = $this->semesterMod->getSemesterId($semester);
        if ($semesterId === FALSE) {
            $semesterId = $this->semesterMod->getSemesterId();
        }

        $absences = $this->absenceMod->getAbsencesFromSemester($_SESSION['id'], $semesterId);

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

    public function note($semester = '')
    {

        $this->load->model('mark_model', 'markMod');
        $this->load->model('semester_model', 'semesterMod');

        $semesterId = $this->semesterMod->getSemesterId($semester);
        if ($semesterId === FALSE) {
            redirect('/Etudiant/Note/');
            return;
        }

        $marks = $this->markMod->getMarksFromSemester($_SESSION['id'], $semesterId);

        $var = array(
            'css' => array('Etudiants/notes'),
            'js' => array('debug'),
            'title' => 'Notes',
            'data' => array('marks' => $marks)
        );

        show('Etudiant/notes', $var);
    }

    public function ptut()
    {
        $this->load->model('ptut_model');
        $this->load->helper('time');

        $group = $this->ptut_model->getStudentGroup($_SESSION['id']);
        if (empty($group)) {
            addPageNotification('Vous ne faites pas parti d\'un groupe de projet');
            redirect('/');
        }

        $members = $this->ptut_model->getGroupMembers($group->idGroupe);
        $lastAppointement = $this->ptut_model->getLastAppointement($group->idGroupe);
        $nextAppointement = $this->ptut_model->getNextAppointement($group->idGroupe);
        $proposals = $this->ptut_model->getDateProposals($nextAppointement->idRDV);

        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Projet tuteuré',
            'data' => array(
                'group' => $group,
                'members' => $members,
                'lastAppointement' => $lastAppointement,
                'nextAppointement' => $nextAppointement,
                'proposals' => $proposals
            )
        );
        show('Etudiant/ptut', $data);
    }

    public function edt()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Emploi du temps'
        );
        show('Etudiant/edt', $data);
    }

    public function question()
    {
        $data = array(
            'css' => array(),
            'js' => array(),
            'title' => 'Questions / Réponses'
        );
        show('Etudiant/questions', $data);
    }
}
