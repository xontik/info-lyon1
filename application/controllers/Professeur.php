<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher')
    redirect('/');

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
    $this->load->model('ptut_model','ptutMod');

    $ptuts = $this->ptutMod->getPtutOfProf($_SESSION['id']);


    $data = array(
      'css' => array(),
      'js' => array('debug'),
      'title' => "Projets tuteurés",
      'var' => array('ptuts'=> $ptuts),

    );
    show('Professeur/ptut', $data);
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
    $matieres = $this->ctrlMod->getMatieres($_SESSION['id']);
    $groupes = $this->ctrlMod->getGroupes($_SESSION['id']);




    $restrict = array("groupes" => array(),"matieres" => array(), "DS" => array()); //le filtre
    /*
    echo "<pre>";
    var_dump($groupes);
    echo "</pre>";
    //*/
    if(isset($_POST["filter"])){


      $grp = array(); //from bd
      $mat = array(); //from bd

      foreach ($groupes as $groupe) {

          array_push($grp,$groupe->idGroupe);
      }
      foreach ($matieres as $matiere){
          array_push($mat,$matiere->idMatiere);
      }

      $restrict = array("groupes" => array(),"matieres" => array(), "DS" => array()); //le filtre
      foreach ($_POST as $key => $value) {
        if(in_array($key,$grp)){
          array_push($restrict["groupes"],$key);
        }
        else if(in_array($key,$mat)){
          array_push($restrict["matieres"],$key);
        }
      }
      //TODO verifier ds promo ou non
      if(isset($_POST["DSPROMO"])){
          array_push($restrict["DS"],"DSPROMO");
      }
      if(isset($_POST["CC"])){
        array_push($restrict["DS"],"CC");
      }

      /*
      echo "<pre>";
      var_dump($controls);
      echo "</pre>";
      //*/
      foreach ($controls as $key => $control) {
          if(!is_null($control->nomGroupe) && !empty($restrict["groupes"]) && !in_array($control->idGroupe, $restrict["groupes"]) ){
            //echo $control->nomGroupe;
            unset($controls[$key]);
          }
          if(!empty($restrict["matieres"] && !in_array($control->idMatiere,$restrict["matieres"]) ) ){
            //echo $control->codeMatiere;
            unset($controls[$key]);
          }
          //
          if(!empty($restrict["DS"]) && count($restrict['DS']) < 2){
            if(in_array("CC",$restrict["DS"]) && is_null($control->nomGroupe)){
              unset($controls[$key]);
            }
            if(in_array("DSPROMO",$restrict["DS"]) && !is_null($control->nomGroupe)){
              unset($controls[$key]);

            }
          }

      }
    }


    $css = array("Professeurs/notes");
    $js = array("debug");
    $title = "Controles";
    $data = array("controls" => $controls, "groupes" => $groupes, "matieres" => $matieres,"restrict" => $restrict);
    $var = array(
      "css" => $css,
      "js" => $js,
      "title" => $title,
      "data" => $data);

      show("Professeur/controles",$var);
    }

    public function addControle($promo = ""){
      //TODO verifier isreferent
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


      $css = array();
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
          redirect("professeur/controle");
        }
        if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'],$id)){
          $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
          redirect("professeur/controle");
        }



        $css = array();
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("control" => $control);
        $var = array(   "css" => $css,
        "js" => $js,
        "title" => $title,
        "data" => $data);

        show("Professeur/editControl",$var);
      }

      public function ajoutNotes($id = ""){
        if($id == ""){
          echo "d";

          show_404();

        }
        $this->load->model('control_model','ctrlMod');
        $this->load->model('mark_model','markMod');

        $control = $this->ctrlMod->getControl($id);
        if(empty($control)){
          $this->session->set_flashdata("notif", array("Controle Introuvable"));
          redirect("professeur/controle");
        }
        if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'],$id)){
          $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
          redirect("professeur/controle");
        }

        $marks = $this->markMod->getMarks($control,$_SESSION["id"]);


        $matiere = $this->ctrlMod->getMatiere($id);
        $js = array("debug");
        $title = "Ajout de notes";
        $data = array("control" => $control,"marks" => $marks,"matiere" => $matiere);
        $var = array(   "css" => array(),
        "js" => $js,
        "title" => $title,
        "data" => $data);

        show("Professeur/addMarks",$var);
      }

    }
