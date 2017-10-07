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
    public function getAllParcoursEditable(){
      $sql =
      'SELECT * from Parcours left join Semestres using(idParcours) where DATE(CONCAT(anneeCreation,\'-08-31\')) > CURDATE() group by idParcours /*having count(idSemestre) < 1*/';

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
    public function isThisParcoursEditable($id){
      return count($this->db->query('SELECT * from Parcours where DATE(CONCAT(anneeCreation,\'-08-31\')) > CURDATE() and idParcours = ?',array($id))->result()) > 0;
    }

    public function addUEtoParcours($idParcours,$idUE){
      return $this->db->query("INSERT INTO UEdeparcours VALUES ('',?,?)",array($idUE,$idParcours));
    }
    public function removeUEtoParcours($idParcours,$idUE){
      return $this->db->query("DELETE FROM UEdeparcours WHERE idUE = ? and idParcours = ?",array($idUE,$idParcours));
    }

    public function addParcours($date,$type){
      return $this->db->query('INSERT INTO Parcours VALUES(\'\',?,?)',array($type,$date));
    }

    public function deleteCascadeParcours($id){
      $this->db->query('DELETE FROM UEdeParcours where idParcours =?',array($id));
      return $this->db->query('DELETE FROM Parcours where idParcours = ?',array($id));

    }
}
