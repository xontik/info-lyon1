<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Question extends CI_Controller
{

    public function send()
    {
        $this->load->model('question_model', 'questionsMod');

        if (isset($_POST['titre'])
            && isset($_POST['texte'])
            && isset($_POST['idProfesseur'])
            && is_numeric($_POST['idProfesseur'])
        ) {
            $titre = htmlspecialchars($_POST['titre']);
            $texte = htmlspecialchars($_POST['texte']);
            $idProf = intval(htmlspecialchars($_POST['idProfesseur']));
            $numEtu = $_SESSION['id'];

            if (!$this->questionsMod->ask($titre, $texte, $idProf, $numEtu)) {
                addPageNotification('Erreur model lors de l\'envoi de la question', 'danger');
            }
        } else {
            addPageNotification('Erreur lors de l\'envoi de la question', 'danger');
        }

        redirect('Question');
    }

    public function answer()
    {
        $this->load->model('question_model', 'questionsMod');

        if (isset($_POST['texte'])
            && isset($_POST['idQuestion'])
            && is_numeric($_POST['idQuestion'])
        ) {
            $idQuestion = intval(htmlspecialchars($_POST['idQuestion']));
            $texte = htmlspecialchars($_POST['texte']);
            $isProf = $_SESSION['userType'] == 'teacher' ? true : false;

            $this->questionsMod->answer($idQuestion, $texte, $isProf);
        } else {
            addPageNotification('Erreur lors de l\'envoi de la réponse', 'danger');
        }

        redirect('Question');

        $this->load->model('question_model', 'questionsMod');
    }

}