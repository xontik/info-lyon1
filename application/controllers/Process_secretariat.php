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
    if(isset($_POST['year']) && isset($_POST['type'])){
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

  public function addSemestre(){
      $this->load->model("semester_model",'semMod');
      $this->load->model("administration_model",'adminMod');

      if(isset($_POST['anneeScolaire']) &&isset($_POST['parcours'])){
          if($this->adminMod->isThisParcoursExist($_POST['parcours'])){
              $semester = (object)array('differe' => isset($_POST['chkDiffere'])?1:0,'anneeScolaire' => $_POST['anneeScolaire'], 'idParcours' => $_POST['parcours'], 'type' => $this->adminMod->getParcoursType($_POST['parcours']));
              $now = new DateTime();
              $dateStart = $this->semMod->getSemesterObjectPeriod($semester)->getBeginDate();
              if($now < $dateStart){
                  if($this->semMod->addSemester($semester->idParcours,$semester->differe,$semester->anneeScolaire)){
                      $this->session->set_flashdata("notif", array("Semestre créé"));
                  }else{
                      $this->session->set_flashdata("notif", array("Impossible d'ajouter car ce semestre existe deja !"));
                  }
              }else{
                  $this->session->set_flashdata("notif", array("Impossible de creer ce semestre car il aurait deja du commencer !"));
              }
          }else{
              $this->session->set_flashdata("notif", array("Parcours inconnu"));
          }
      }else{
          $this->session->set_flashdata("notif", array("Données manquantes !"));
      }
      redirect('Secretariat/administration');

  }

  public function deleteSemestre($id){
      $this->load->model("semester_model",'semMod');
      if($this->semMod->isSemesterDeletable($id)){
          if($this->semMod->deleteSemestre($id)){
              $this->session->set_flashdata("notif", array("Semestre supprimé !"));
          }else{
              $this->session->set_flashdata("notif", array("Erreur de suppression bdd !"));
          }
      }
      else{
          $this->session->set_flashdata("notif", array("Ce semestre ne peut etre supprimé !"));
      }
      redirect('Secretariat/administration');

  }

  public function getCSVGroupeSemestre($id){
      $this->load->model('Students_model','studentMod');
      $this->load->model('Semester_model','semMod');

      $semestre = $this->semMod->getSemesterById($id);

      $groups = $this->studentMod->getStudentsBySemestre($id);
      header('Content-Type: text/csv');
      header('Content-Encoding: UTF-8');
      header('Content-disposition: attachment; filename='.$semestre->anneeScolaire.'-'.$semestre->type.'.csv');



      echo 'SEMESTRE;'.$semestre->idSemestre.';<--Donnees non modifiable;;;'.PHP_EOL;
      echo 'Type du semestre;'.$semestre->type.';Annee scolaire;'.$semestre->anneeScolaire.'-'.(((int)$semestre->anneeScolaire)+1).';<--Donnees non modifiable;'.PHP_EOL;
      $idgroupe = 0;
      foreach ($groups as $group) {
          if($idgroupe != $group->idGroupe){
             $idgroupe = $group->idGroupe;
             echo PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
             echo 'GROUPE;'.$group->idGroupe.';Nom du groupe;'.$group->nomGroupe.';<--Donnees non modifiable;'.PHP_EOL;


          }
          echo $group->numEtudiant.';'.$group->nom.';'.$group->prenom.';;;'.PHP_EOL;
      }
      //

  }

  public function addGroupe($idSemestre){
      $this->load->model("administration_model",'adminMod');
      $this->load->model("semester_model",'semMod');

      if(isset($_POST['nomGroupe'])){
          //TODO add preg_match
        if($this->semMod->isSemesterEditable($idSemestre)) {
          if($this->adminMod->addGroupe($idSemestre,$_POST['nomGroupe'])){
            $this->session->set_flashdata("notif", array("Groupe ".$_POST['nomGroupe']." ajouté!"));
          }else{
            $this->session->set_flashdata("notif", array("Erreur de création de groupe !"));
          }
        }else{
          $this->session->set_flashdata("notif", array("Ce semestre ne peut etre modifié !"));
        }
      }else{
        $this->session->set_flashdata("notif", array("Données manquantes !"));
      }
      redirect('Secretariat/gestionSemestre/'.$idSemestre);

  }

  public function deleteGroupe($idGroupe,$idSemestre){
      $this->load->model("administration_model",'adminMod');
      $this->load->model("semester_model",'semMod');


      if($this->adminMod->isGroupeEditable($idGroupe)) {
        if($this->adminMod->deleteGroupe($idGroupe)){
          $this->session->set_flashdata("notif", array("Groupe supprimé !"));
        }else{
          $this->session->set_flashdata("notif", array("Erreur de suppression du groupe !"));
        }
      }else{
        $this->session->set_flashdata("notif", array("Ce semestre ne peut etre modifié !"));
      }
      redirect('Secretariat/gestionSemestre/'.$idSemestre);

  }

  public function importCSV(){
      $this->load->model("administration_model",'adminMod');
      $this->load->model("semester_model",'semMod');
      $this->load->model("students_model",'studentMod');

      if(isset($_FILES['import']) && $_FILES['import']['size'] > 0){
          if($_FILES['import']['type'] == 'text/csv'){
              $csv = array();
              ini_set('auto_detect_line_endings',TRUE);
              $file = fopen($_FILES['import']['tmp_name'],'r');
              while($line = fgetcsv($file,0,';')){
                  if($line[0]){
                      $csv[] = $line;
                  }
              }
              fclose($file);
              ini_set('auto_detect_line_endings',FALSE);

              /*
              echo '<pre>';
              print_r($csv);
              echo '</pre>';
              /**/
              $idSemestre = $csv[0][1];
              $ajout = array();
              $refus = array();
              $dejaIn = array();
              if($this->semMod->isSemesterEditable($idSemestre)){
                  //echo 'sem ok' ;
                  $semestreDuringSamePeriod = $this->semMod->getSemesterIdsSamePeriod($idSemestre);
                  /*
                  echo '<pre>';
                  print_r($semestreDuringSamePeriod);
                  echo '</pre>';
                  /**/
                  $groupeId = 0;
                  foreach ($csv as $line) {
                      if($line[0]=="GROUPE"){
                          $groupeId = $line[1];
                          $ajout[$groupeId] = array();
                          $refus[$groupeId] = array();
                          $dejaIn[$groupeId] = array();
                      }else{
                          if($groupeId != 0){

                              $student = array('numEtudiant' => $line[0],'nom' => $line[1], 'prenom' => $line[2]);

                              //1 verifier si il es pas deja dans le groupe cible
                              if($this->studentMod->isStudentInGroup($student['numEtudiant'],$groupeId)){
                                  //echo $student['numEtudiant'].' DEJA DANS LE GROUPE<br>';
                                  //mais ajout car on wipe le groupe
                                  $ajout[$groupeId][] = $student;
                              }else{
                                  //2 verifier si il n'est pas deja dans un autre groupe ?
                                  if($this->studentMod->isStudentInGroupsOfSemesters($student['numEtudiant'],$semestreDuringSamePeriod)){
                                      echo $student['numEtudiant'];
                                      print_r($semestreDuringSamePeriod);
                                      $refus[$groupeId][] = $student;
                                  }else{ // sinon whatever car on wipe chaque groupe TODO A CHANGER
                                      $ajout[$groupeId][] = $student;
                                  }
                              }
                          }
                      }

                  }


                  //TODO maybe delete all relation pour le semestre ?

                  foreach ($ajout as $group => $students) {
                      $this->studentMod->deleteAllRelationForGroup($group);


                      foreach ($students as $student) {
                          $this->studentMod->addToGroupe($student['numEtudiant'],$group);
                      }
                  }
                  $error = array();
                  foreach ($refus as $group => $students) {
                      foreach ($students as $student) {
                          $error[] = $student;
                      }
                  }

                  if(count($error)){
                      //TODO differencier les erreurs
                      $this->session->set_flashdata("notif",array("Erreur : ".count($error)));
                  }else{
                      $this->session->set_flashdata("notif",array("Alright"));
                  }

                  redirect('Secretariat/gestionSemestre/'.$idSemestre);
                  exit(0);


              }else{
                  $this->session->set_flashdata("notif", array("Identifiant semestre incorrect"));
              }
          }else{
              $this->session->set_flashdata("notif", array("Fichier format incorrect"));
          }
      }else{
          $this->session->set_flashdata("notif", array("Aucun fichier recu"));
      }
      redirect('Secretariat/administration');



  }
}
