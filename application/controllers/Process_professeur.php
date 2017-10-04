<?php
/**
* Created by PhpStorm.
* User: xontik
* Date: 24/04/2017
* Time: 01:22
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Process_professeur extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher')
    redirect('/');

  }

  public function addcontrole($promo = ""){
    $this->load->model('control_model','ctrlMod');


    if($promo=="") {
      if (isset($_POST['enseignement']) && isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
      && isset($_POST['date']) && isset($_POST['type'])
    ) {
      if ($_POST["enseignement"] != "" && $_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
      && $_POST["date"] != "" && $_POST["typeControle"] != ""
    ) {
      if(!$this->ctrlMod->checkEnseignementProf($_POST["enseignement"],$_SESSION['id'])){
        $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur cet enseignement"));
        redirect("professeur/controle");
      }
      if ($this->ctrlMod->addControl($_POST['nom'], $_POST['coeff'], $_POST['diviseur'], $_POST['typeControle'], $_POST['date'], $_POST['enseignement'])) {
        $this->session->set_flashdata("notif", array("Controle ajoutée avec succes"));
        redirect("professeur/controle");
      }else{
        $this->session->set_flashdata("notif", array("Erreur de requete base de données impossible d'ajouter le controle"));
        redirect("professeur/controle");

      }


    }
  }
  $this->session->set_flashdata("notif", array("Erreur controle pas add"));
  redirect("professeur/controle");
}else if ($promo=="promo"){

  if (isset($_POST['matiere']) && isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
  && isset($_POST['date'])
) {
  if ( $_POST["matiere"] != "" && $_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
  && $_POST["date"] != ""
) {

  if ($this->ctrlMod->addDsPromo($_POST['nom'], $_POST['coeff'], $_POST['diviseur'], 1, $_POST['date'],$_POST['matiere'])) {
    $this->session->set_flashdata("notif", array("Controle promo ajoutée avec succes"));
    redirect("professeur/controle");
  }
  else{
    $this->session->set_flashdata("notif", array("Erreur de requete base de données impossible d'ajouter le controle"));
    redirect("professeur/controle");
  }


}
}
$this->session->set_flashdata("notif", array("Erreur controle promo pas add"));
redirect("professeur/controle");
}else{
  show_404();
}

}
public function editcontrole($id = ""){
  if($id == ""){
    show_404();
  }
  $this->load->model('control_model','ctrlMod');
  if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'],$id)){
    $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
    redirect("professeur/controle");
  }

  $control = $this->ctrlMod->getControl($id);

  if(isset($_POST['nom']) && isset($_POST['coeff']) && isset($_POST['diviseur'])
  && isset($_POST['date'])) {

    if($_POST["nom"] != "" && $_POST["coeff"] != "" && $_POST["diviseur"] != ""
    && $_POST["date"] != ""){
      if(!is_null($control->idDSPromo)){
        $res = $this->ctrlMod->editControl($_POST['nom'],$_POST['coeff'],$_POST['diviseur'],1,$_POST['date'],$id);
      }else{
        if(isset($_POST['typeControle'])){
          $res = $this->ctrlMod->editControl($_POST['nom'],$_POST['coeff'],$_POST['diviseur'],$_POST['typeControle'],$_POST['date'],$id);
        }else{
          $res= false;
        }
      }

      if($res){
        $this->session->set_flashdata("notif",array("Controle ajoutée avec succes"));
        redirect("professeur/controle");
      }
      else{
        $this->session->set_flashdata("notif",array("Erreur requete bd"));
        redirect("professeur/controle");

      }
    }
  }
  $this->session->set_flashdata("notif",array("Erreur controle pas add"));
  redirect("professeur/controle");

}
public function deletecontrole($id = "")
{
  if($id == ""){
    show_404();
  }
  $this->load->model('control_model','ctrlMod');

  if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'],$id)){
    $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
    redirect("professeur/controle");
  }
  if ($this->ctrlMod->deleteControl($id)) {
    $this->session->set_flashdata("notif", array("Controle supprimé avec succes"));
    redirect("professeur/controle");
  }

  $this->session->set_flashdata("notif", array("Erreur controle pas delete"));
  redirect("professeur/controle");

}

public function addmarks($id){


  $this->load->model('control_model','ctrlMod');
  $this->load->model('mark_model','markMod');


  if(!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'],$id)){
    $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
    redirect("professeur/controle");
  }
  $control = $this->ctrlMod->getControl($id);
  $marks = $this->markMod->getMarks($control,$_SESSION["id"]);



  $i = 0;
  $ok = true;
  foreach ($_POST as $key => $value) {
    if($key != $marks[$i]->numEtudiant){
      $ok = false;
      break;
    }
    $i++;

    if($i==count($_POST)-1){
      break;
    }
  }
  if(!$ok){
    $this->session->set_flashdata("notif", array("Aucune modification, incoherence des données recues"));
    redirect("professeur/controle");
  }
  array_pop($_POST);
  //TODO ajoter verification sur value

  $this->markMod->addMarks($id,$_POST);

  $this->session->set_flashdata("notif", array("Note modifiées avec succes !"));
  redirect("professeur/controle");
}



}
