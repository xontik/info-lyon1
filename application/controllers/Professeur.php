<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $newdata = array(
            'username'  => 'johndoe',
            'email'     => 'johndoe@some-site.com',
            'id' => 'e8888888'
        );

        $this->session->set_userdata($newdata);
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

    public function controle() {
        $this->load->model('control_model','ctrlMod');

        $controls = $this->ctrlMod->getControls($_SESSION['id']);
        $dspromo = $this->ctrlMod->getDsPromo($_SESSION['id']);


        $css = array("test");
        $js = array("debug");
        $title = "Controles";
        $data = array("controls" => $controls,"dspromo" => $dspromo);
        $var = array(
            "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/controles",$var);
    }

    public function addControle($promo = ""){
        $this->load->model('control_model', 'ctrlMod');
        $bool = false;
        if($promo == ""){
            $select =  $this->ctrlMod->getEnseignements($_SESSION['id']);
        }else if($promo == "promo"){
            $bool = true;
            $select = $this->ctrlMod->getMatieres($_SESSION['id']);
        }else{
            show_404();
            return;
        }


        $css = array("test");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("select" => $select,"promo" => $bool);
        $var = array(
            "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/addControl",$var);
    }
    public function editControle($id = ""){
        if($id == ""){
            show_404();
        }
        $this->load->model('control_model','ctrlMod');


        $control = $this->ctrlMod->getControl($id);
        if(empty($control)){
            $this->session->set_flashdata("notif", array("Controle Introuvable"));
            redirect("professeur/control");
        }
        if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'],$id)){
            $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
            redirect("professeur/control");
        }

        $css = array("test");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("control" => $control);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("Professeur/editControl",$var);
    }

}
