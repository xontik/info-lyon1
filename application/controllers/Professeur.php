<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {


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


    public function __construct(){
        parent::__construct();
        $newdata = array(
            'username'  => 'johndoe',
            'email'     => 'johndoe@some-site.com',
            'profId' => 'e8888888'
        );

        $this->session->set_userdata($newdata);
    }


    public function control(){
        $this->load->model('control_model','ctrlMod');

        $controls = $this->ctrlMod->getControls($_SESSION['profId']);
        $dspromo = $this->ctrlMod->getDsPromo($_SESSION['profId']);



        $css = array("test");
        $js = array("debug");
        $title = "Controles";
        $data = array("controls" => $controls,"dspromo" => $dspromo);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("P_controls",$var);
    }
    public function addControl($promo = ""){
        $this->load->model('control_model','ctrlMod');
        $bool = false;
        if($promo == ""){
            $select =  $this->ctrlMod->getEnseignements($_SESSION['profId']);
        }else if($promo == "promo"){
            $bool = true;
            $select = $this->ctrlMod->getMatieres($_SESSION['profId']);
        }else{
            show_404();
        }


        $css = array("test");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("select" => $select,"promo" => $bool);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("P_addControl",$var);
    }
    public function editControl($id = ""){
        if($id == ""){
            show_404();
        }
        $this->load->model('control_model','ctrlMod');


        $control = $this->ctrlMod->getControl($id);
        if(empty($control)){
            $this->session->set_flashdata("notif", array("Controle Introuvable"));
            redirect("professeur/control");
        }
        if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['profId'],$id)){
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

        show("P_editControl",$var);
    }




}