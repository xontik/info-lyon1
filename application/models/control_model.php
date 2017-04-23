<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 21/04/2017
 * Time: 07:47
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Control_model extends CI_Model {

    public function __construct(){

    }
    public function getControl($controlId){
        $sql = "SELECT * FROM Controles WHERE idControle = ? ";
        return $this->db->query($sql,array($controlId))->row();
    }
    public function getControls($professorId){
        $sql = "SELECT * FROM Controles join enseignements using(idEnseignement) join matieres using(codeMatière) join groupes USING(idGroupe) WHERE idProfesseur = ? ORDER BY codeMatière, dateControle DESC";
        return $this->db->query($sql,array($professorId))->result();
    }
    public function getControlWithMarks($controlId){

    }
    public function addControl($coeff,$div,$type,$idDSPromo,$idEnseignement){

            $sql = "INSERT INTO Controles VALUES ('',?,?,?,null,null,?,?,?)";
            return $this->db->query($sql,array($coeff,$div,$type,date('Y-m-d'),$idDSPromo,$idEnseignement));

    }

    public function editControl($controlId,$coeff,$div,$type,$idDSPromo,$idProf,$group,$codeMat){
        $sql = "SELECT * from enseignements where idprofesseur = ? and codeMatière = ? and idGroupe = ?";
        if($this->db->query($sql,array($idProf,$codeMat,$group))->num_rows()){

            $sql = "UPDATE Controles SET 'coefficient' = ?, 'diviseur' = ?, 'type'= ?, 'idDSPromo' = ?, 'idProfesseur' = ?, 'nomGroupe' = ?, 'codeMatière' = ? WHERE idControle = ?";
            $this->db->query($sql,array($coeff,$div,$type,$idDSPromo,$idProf,$group,$codeMat,$controlId));
            return $controlId;
        }
        return false;

    }
    public function deleteControl($controlId){
        $sql = "DELETE FROM Controles WHERE idControle = ? ";
        return $this->db->query($sql,array($controlId));
    }
}