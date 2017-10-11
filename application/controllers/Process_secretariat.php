<?php
/**
 * Created by PhpStorm.
 * User: enzob
 * Date: 30/09/2017
 * Time: 19:53
 */

class Process_secretariat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
            redirect('/');
    }

    public function ajout_absence()
    {
        if (!isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $data = array(
            'numEtudiant' => htmlspecialchars($_POST['studentId']),
            'dateDebut' => htmlspecialchars($_POST['beginDate']),
            'dateFin' => htmlspecialchars($_POST['endDate']),
            'idTypeAbsence' => htmlspecialchars($_POST['absenceTypeId']),
            'justifiee' => htmlspecialchars($_POST['justified'])
        );

        $errors = $this->_checkAbsenceData($data);

        if (!empty($errors)) {
            echo join(',', $errors);
            return;
        }

        try {
            $this->db->insert('Absences', $data);
            $absenceId = $this->db->select_max('idAbsence')
                ->get('Absences')
                ->row()->idAbsence;
            echo 'success ' . $absenceId;
        } catch(PDOException $e) {
            echo 'exception : ' . $e->getMessage();
        }

    }

    public function modifier_absence()
    {
        header('Content-Type: text/plain');

        if (!isset($_POST['absenceId'])
            ||!isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $absenceId = htmlspecialchars($_POST['absenceId']);
        $data = array(
            'numEtudiant' => htmlspecialchars($_POST['studentId']),
            'dateDebut' => htmlspecialchars($_POST['beginDate']),
            'dateFin' => htmlspecialchars($_POST['endDate']),
            'idTypeAbsence' => htmlspecialchars($_POST['absenceTypeId']),
            'justifiee' => htmlspecialchars($_POST['justified'])
        );

        $errors = $this->_checkAbsenceData($data);
        if ($errors === FALSE) {
            echo 'cancel';
            return;
        }

        if (!empty($errors)) {
            echo join(',', $errors);
            return;
        }

        try {
            $this->db->set($data)
                ->where('idAbsence', $absenceId)
                ->update('Absences', $data);
            echo 'success ' . $absenceId;
        } catch(Exception $e) {
            echo 'exception : ' . $e->getMessage();
        }
    }

    private function _checkAbsenceData($data)
    {
        $errors = array();

        if (empty($data['numEtudiant'])) {
            return false;
        }

        if (empty($data['dateDebut'])) {
            $errors[] = 'beginDate';
        }

        if (empty($data['dateFin'])) {
            $errors[] = 'endDate';
        }

        if ($data['dateDebut'] === $data['dateFin']) {
            $errors[] = 'sameDates';
        }

        if ($data['justifiee'] != 0 && $data['justifiee'] != 1) {
            return false;
        }

        return $errors;
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
