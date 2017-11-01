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
        $this->load->helper('timetable');

        $date = new DateTime();
        $timetable = getNextTimetable(9311, 'day', $date);

        $side_edt = $this->load->view(
            'includes/side-edt',
            array('date' => $date, 'timetable' => $timetable),
            TRUE
        );

        /* Notifications */
        // To be added on each page that use notifications (aka every page)
        if (isset($_SESSION['pageNotif'])) {
            $this->session->keep_flashdata('pageNotif');
        }
        $notifications = array_merge(
            isset($_SESSION['sessionNotif']) ? $_SESSION['sessionNotif'] : array()
            //$this->notifications->getAll($_SESSION['userId'])
        );
        /* /Notifications */

        $data = array(
            'css' => array('Etudiant/dashboard'),
            'js' => array('debug'),
            'title' => 'Tableau de bord',
            'notifications' => $notifications,
            'data' => array(
                'side-edt' => $side_edt
            )
        );
        show('Etudiant/dashboard', $data);
    }

    public function absence($semester = '')
    {
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

    public function note($semester = '')
    {
        $this->load->model('mark_model');
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

        $semesterId = $this->semester_model->getSemesterId($semester);
        $semester = $this->semester_model->getSemesterTypeFromId($semesterId);

        $marks = $this->mark_model->getMarksFromSemester($_SESSION['id'], $semesterId);

        $data = array(
            'css' => array(),
            'js' => array('debug'),
            'page' => 'notes',
            'title' => 'Notes',
            'data' => array(
                'max_semester' => $max_semester,
                'semester' => $semester,
                'marks' => $marks
            )
        );
        show('Etudiant/notes', $data);
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
        $this->load->model('question_model', 'questionsMod');
        $this->load->model('students_model', 'studentsMod');
        $this->load->model('teacher_model', 'teacherMod');

        // Get questions and answers
        $questions = $this->questionsMod->getStudentQuestions($_SESSION['id']);
        $answers = array();

        foreach($questions as $question) {
            $answers[$question->idQuestion] = $this->questionsMod->getAnswers($question->idQuestion);
        }

        // Get teachers
        $unsortedTeachers = $this->studentsMod->getProfesseursByStudent($_SESSION['id']);
        $teachers = array();

        foreach($unsortedTeachers as $teacher) {
            $teachers[$teacher->idProfesseur] = $teacher;
        }

        $data = array(
            'css' => array('Etudiant/questions'),
            'js' => array('debug'),
            'page' => 'question',
            'title' => 'Questions / Réponses',
            'data' => array(
                'questions' => $questions,
                'answers' => $answers,
                'teachers' => $teachers
            )
        );
        show('Etudiant/questions', $data);
    }
}
