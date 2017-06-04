<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Tableau de bord"
        );
        show("Professeur/dashboard", $data);
    }

    public function absence() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Absences"
        );
        show("Professeur/absences", $data);
    }

    public function note() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Notes"
        );
        show("Professeur/notes", $data);
    }

    public function ptut() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Projets tuteurés"
        );
        show("Professeur/ptut", $data);
    }

    public function edt() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Emploi du temps"
        );
        show("Professeur/edt", $data);
    }

    public function question() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Questions / Réponses"
        );
        show("Professeur/questions", $data);
    }

}
