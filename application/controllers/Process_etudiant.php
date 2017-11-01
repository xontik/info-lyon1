<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Process_etudiant extends CI_Controller {

    public function envoyerQuestion() {
        $this->load->model('question_model', 'questionsMod');
        if (isset($_POST['q_titre']) AND isset($_POST['q_texte']) AND isset($_POST['q_idProfesseur']) AND is_numeric($_POST['q_idProfesseur'])) {
            $titre = htmlspecialchars($_POST['q_titre']);
            $texte = htmlspecialchars($_POST['q_texte']);
            $idProf = (int) htmlspecialchars($_POST['q_idProfesseur']);
            $numEtu = $_SESSION['id'];
            $this->questionsMod->ask($titre, $texte, $idProf, $numEtu);
        }
        redirect('Etudiant/Question');
    }

    public function repondreQuestion() {
        $this->load->model('question_model', 'questionsMod');
        if (isset($_POST['r_texte']) AND isset($_POST['r_idQuestion']) AND is_numeric($_POST['r_idQuestion'])) {
            $idQuestion = (int) htmlspecialchars($_POST['r_idQuestion']);
            $texte = htmlspecialchars($_POST['r_texte']);
            $isProf = ($_SESSION['user_type'] == 'teacher') ? 1 : 0;
            $this->questionsMod->answer($idQuestion, $texte, $isProf);
        }
        redirect('Etudiant/Question');
    }

}
