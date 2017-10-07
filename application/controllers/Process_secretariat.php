<?php
/**
* Created by PhpStorm.
* User: xontik
* Date: 24/04/2017
* Time: 01:22
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Process_secretariat extends CI_Controller {

  public function __construct() {
      parent::__construct();
      if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
          redirect('/');
  }

  public function getUEs(){

    $this->load->model("administration_model",'adminMod');
    $UEsIn = $this->adminMod->getUEInParcours($_GET['idParcours']);
    $UEsOut = $this->adminMod->getUENotInParcours($_GET['idParcours']);
    $output = array('in' => $UEsIn,'out' => $UEsOut);

    header('Content-Type: application/json');
    echo json_encode( $output );
  }

  public function addUEtoParcours(){
    //TODO verification des droit sur le liens UE - PArcours
    $this->load->model("administration_model",'adminMod');
    $ids = array();
    foreach ($_GET['idUEs'] as $idUE) {

      if($this->adminMod->addUEtoParcours($_GET['idParcours'],$idUE)){
        $ids[] = $idUE;
      }
    }
    header('Content-Type: application/json');

    echo json_encode($ids);
  }

  public function removeUEtoParcours(){
    //TODO verification des droit sur le liens UE - PArcours

    $this->load->model("administration_model",'adminMod');
    $ids = array();
    foreach ($_GET['idUEs'] as $idUE) {
      if($this->adminMod->removeUEtoParcours($_GET['idParcours'],$idUE)){
        $ids[] = $idUE;
      }
    }
    header('Content-Type: application/json');

    echo json_encode($ids);
  }

  public function addParcours(){
    $this->load->model("administration_model",'adminMod');

    if(isset($_POST['send']) && isset($_POST['year']) && isset($_POST['type'])){
      if(is_numeric($_POST['year']) && strlen($_POST['type']) == 2){
        if($this->adminMod->addParcours($_POST['year'],$_POST['type'])){
          $this->session->set_flashdata("notif", array("Parcours créé !"));
        }else{
          $this->session->set_flashdata("notif", array("Erreur d'ajout bdd !"));
        }
      }else{
        $this->session->set_flashdata("notif", array("Données entrées corrompues !"));
      }
    }else{
      $this->session->set_flashdata("notif", array("Données manquantes !"));
    }
    redirect('Secretariat/administration');
  }

  public function deleteParcours(){
    $this->load->model("administration_model",'adminMod');
    //TODO verification des droit sur le liens UE - PArcours
    //TODO gerer le cas des semestre deja relier en plus du js I mean :)
    if(isset($_POST['parcours'])){
      if($this->adminMod->deleteCascadeParcours($_POST['parcours'])){
        $this->session->set_flashdata("notif", array("Parcours supprimé !"));
      }else{
        $this->session->set_flashdata("notif", array("Erreur de suppression bdd !"));
      }
    }else{
      $this->session->set_flashdata("notif", array("Données manquantes !"));
    }
    redirect('Secretariat/administration');
  }
}
