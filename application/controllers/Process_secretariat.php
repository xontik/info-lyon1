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
    $this->load->model("administration_model",'adminMod');
    $ids = array();
    if($this->adminMod->isThisParcoursEditable($_GET['idParcours'])) {

      foreach ($_GET['idUEs'] as $idUE) {

        if($this->adminMod->addUEtoParcours($_GET['idParcours'],$idUE)){
          $ids[] = $idUE;
        }
      }

    }
    header('Content-Type: application/json');

    echo json_encode($ids);
  }

  public function removeUEtoParcours(){
    $this->load->model("administration_model",'adminMod');
    $ids = array();
    if($this->adminMod->isThisParcoursEditable($_GET['idParcours'])) {

      foreach ($_GET['idUEs'] as $idUE) {
        if($this->adminMod->removeUEtoParcours($_GET['idParcours'],$idUE)){
          $ids[] = $idUE;
        }
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
    if(isset($_POST['parcours'])){
      if($this->adminMod->isThisParcoursEditable($_POST['parcours'])) {
        if($this->adminMod->deleteCascadeParcours($_POST['parcours'])){
          $this->session->set_flashdata("notif", array("Parcours supprimé !"));
        }else{
          $this->session->set_flashdata("notif", array("Erreur de suppression bdd !"));
        }
      }else{
        $this->session->set_flashdata("notif", array("Ce parcours ne peut etre supprimé !"));
      }
    }else{
      $this->session->set_flashdata("notif", array("Données manquantes !"));
    }
    redirect('Secretariat/administration');
  }
}
