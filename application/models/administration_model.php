<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration_model extends CI_Model {

    public function getAllAdministration(){
        $sql =
        'SELECT * from Parcours
        JOIN UEdePArcours using(idParcours)
        join UE using(idue)
        join modulesdeue using(idue)
        join modules using(idmodule)
        join matieresdemodules using(idmodule)
        join matieres using(idmatiere)
        ORDER BY idParcours DESC,idUe,idModule,idmatiere';

        return $this->db->query($sql)->result();
    }

    public function getDeadlineEditable($parcours){
      $annee = $parcours['anneeCreation'];
      $cond = array(
        'S1' => $annee.'-08-31',
        'S2' => $annee.'-12-31',
        'S3' => $annee.'-08-31',
        'S4' =>  $annee.'-12-31'
      );
      return $cond[$parcours['type']];
    }

    public function getUENotInParcours($idParcours){
      $sql = 'SELECT idUE,codeUE,nomUE,anneeCreation from UE
      where idUe NOT in
        (SELECT idUE  from  UEdePArcours where idParcours = ? )
      group by idUE order by anneeCreation desc';

      return $this->db->query($sql,array($idParcours))->result();


    }
    public function getUEInParcours($idParcours){
      $sql = 'SELECT * from UE join UEdePArcours using(idUE) where idParcours = ? order by idUE';

      return $this->db->query($sql,array($idParcours))->result();


    }
    //TODO distinction de ceux que l'on peut editer
    public function getAllParcoursEditable(){
      $sql =
      'SELECT * from Parcours where DATE(CONCAT(anneeCreation,\'-08-31\')) > CURDATE()';

      return $this->db->query($sql)->result();
    }
    public function getAllUEParcours(){
        $sql =
        'SELECT distinct(idUE), nomUE,codeUE  from Parcours
        JOIN UEdePArcours using(idParcours)
        join UE using(idue)
        ORDER BY idParcours DESC';

        return $this->db->query($sql)->result();
    }

    public function addUEtoParcours($idParcours,$idUE){
      return $this->db->query("INSERT INTO UEdeparcours VALUES ('',?,?)",array($idUE,$idParcours));
    }
    public function removeUEtoParcours($idParcours,$idUE){
      return $this->db->query("DELETE FROM UEdeparcours WHERE idUE = ? and idParcours = ?",array($idUE,$idParcours));
    }
}
