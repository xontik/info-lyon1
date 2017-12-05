<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Question extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!(isset($_SESSION['userType'])
            && in_array($_SESSION['userType'], $this->config->item('userTypes')))
        ) {
            header('Content-Length: 0', TRUE, 403);
            exit(0);
        }
    }

    public function send()
    {
        $this->load->model('Questions');

        if (isset($_POST['teacherId'])
            && isset($_POST['title'])
            && isset($_POST['text'])
            && is_numeric($_POST['teacherId'])
        ) {
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['text']);
            $teacherId = (int) htmlspecialchars($_POST['teacherId']);
            $studentId = $_SESSION['id'];

            if (!$this->Questions->ask($title, $text, $teacherId, $studentId)) {
                addPageNotification('Erreur model lors de l\'envoi de la question', 'danger');
            }
        } else {
            addPageNotification('Erreur lors de l\'envoi de la question', 'danger');
        }

        redirect('Question');
    }

    public function answer($questionId)
    {
        $questionId = (int) htmlspecialchars($questionId);

        $this->load->model('Questions');

        if ($questionId !== 0
            && isset($_POST['text'])
        ) {
            $text = htmlspecialchars($_POST['text']);
            $isTeacher = $_SESSION['userType'] === 'teacher';

            $this->Questions->answer($questionId, $text, $isTeacher);
        } else {
            addPageNotification('Erreur lors de l\'envoi de la réponse', 'danger');
        }

        redirect('Question');
    }

    public function set_public($questionId)
    {
        $this->load->model('Questions');

        $idPublicQuestion = (int) htmlspecialchars($questionId);
        $this->Questions->setPublic($idPublicQuestion, isset($_POST['checkPublic']));

        redirect('Question/detail/' . $questionId);
    }

}
