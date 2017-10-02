<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secretariat extends CI_Controller {

  public function __construct() {
      parent::__construct();
      if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
          redirect('/');
  }

  public function index() {
      $this->administration();
  }

  public function administration(){
    $this->load->model('Administration_model','adminMod');
    $admins = $this->adminMod->getAllAdministration();
    $parcours = array();
    $idParcours = 0;



    foreach ($admins as $admin) {

      if($admin->idParcours != $idParcours){

        $idParcours = $admin->idParcours;
        $parcours[$idParcours]['type'] = $admin->type;
        $parcours[$idParcours]['anneeCreation'] = $admin->anneeCreation;
        $parcours[$idParcours]['UEs'] = array();

        $idUE = 0;
        $now = date('Y-m-d');

        $parcours[$idParcours]['editable']  = $this->adminMod->getDeadlineEditable($parcours[$idParcours]) > $now;


      }

      if($idUE != $admin->idUE){
        $idUE = $admin->idUE;
        $parcours[$idParcours]['UEs'][$idUE]['codeUE'] = $admin->codeUE;
        $parcours[$idParcours]['UEs'][$idUE]['nomUE'] = $admin->nomUE;
        $parcours[$idParcours]['UEs'][$idUE]['coefficientUE'] = 0;
        $parcours[$idParcours]['UEs'][$idUE]['Modules'] = array();
        
        $idModule = 0;



      }

      if($idModule != $admin->idModule){


        $idModule = $admin->idModule;
        $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['codeModule'] = $admin->codeModule;
        $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['nomModule'] = $admin->nomModule;
        $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['matieres'] = array();
        $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['coefficientModule'] = 0;

        $idMatiere = 0;

      }

      if($idMatiere != $admin->idMatiere){
        $idMatiere = $admin->idMatiere;
      }

      $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['matieres'][$idMatiere]['codeMatiere'] = $admin->codeMatiere;
      $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['matieres'][$idMatiere]['nomMatiere'] = $admin->nomMatiere;
      $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['matieres'][$idMatiere]['coefficientMatiere'] = $admin->coefficientMatiere;


      $parcours[$idParcours]['UEs'][$idUE]['coefficientUE']+= $admin->coefficientMatiere;
      $parcours[$idParcours]['UEs'][$idUE]['Modules'][$idModule]['coefficientModule']+= $admin->coefficientMatiere;
    }

    //TODO calcul coeff des modules ue


    $data = array(
      "css" => array('Secretariats/administration'),
      "js" => array('debug','administration'),
      "title" => "Administration",
      'data' => array('parcours' => $parcours, 'admins' => $admins)
    );
    show("Secretariat/administration", $data);

  }


}
