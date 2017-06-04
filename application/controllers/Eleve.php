<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Eleve extends CI_Controller {

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
        show("Eleve/dashboard", $data);
    }

    public function absence() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Absences"
        );
        show("Eleve/absences", $data);
    }

    public function note() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Notes"
        );
        show("Eleve/notes", $data);
    }

    public function ptut() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Projets tuteurés"
        );
        show("Eleve/ptut", $data);
    }

    public function edt() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Emploi du temps"
        );
        show("Eleve/edt", $data);
    }

    public function question() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Questions / Réponses"
        );
        show("Eleve/questions", $data);
    }

}
