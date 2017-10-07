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
    foreach ($_GET['idUEs'] as $idUE) {
      if($this->adminMod->addUEtoParcours($_GET['idParcours'],$idUE)){
        $ids[] = $idUE;
      }
    }
    header('Content-Type: application/json');

    echo json_encode($ids);
  }

  public function removeUEtoParcours(){
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
}
