<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 21/04/2017
 * Time: 07:47
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Control_model extends CI_Model
{

    public function __construct()
    {

    }

    public function getControl($controlId)
    {
        $sql = "SELECT * FROM Controles WHERE idControle = ? ";
        return $this->db->query($sql, array($controlId))->row();
    }

    public function getControls($professorId)
    {
        $sql = "SELECT * FROM Controles join enseignements using(idEnseignement) join matieres using(codeMatiere) join groupes USING(idGroupe) WHERE idProfesseur = ? and actif = 1 and idDsPromo IS NULL ORDER BY codeMatiere, dateControle DESC";
        return $this->db->query($sql, array($professorId))->result();
    }

    public function getDsPromoForTeacherBoard($professorId)
    {
        $sql = "SELECT distinct codeMatiere,nomMatiere,idDsPromo,idControle,nomControle,coefficient,diviseur,typeControle,median,average,dateControle,coefficientMatiere FROM Controles join DsPromo using(idDsPromo) join matieres using(codeMatiere) 
    join enseignements using(codeMatiere) join groupes using(idGroupe) WHERE idProfesseur = ? and actif = 1 ORDER BY codeMatiere, dateControle DESC";
        return $this->db->query($sql, array($professorId))->result();
    }

    public function getDsPromo($professorId)
    {
        $sql = "SELECT * FROM Controles join DsPromo using(idDsPromo) join matieres using(codeMatiere) 
join enseignements using(codeMatiere) join groupes using(idGroupe) WHERE idProfesseur = ? and actif = 1 ORDER BY codeMatiere, dateControle DESC";
        return $this->db->query($sql, array($professorId))->result();
    }


    public function getControlWithMarks($controlId)
    {

    }

    public function checkProfessorRightOnControl($profId, $controlId)
    {
        $sql = "select distinct idControle from ( 
SELECT idControle FROM Controles JOIN DsPromo USING(idDsPromo) JOIN enseignements USING(codeMatiere) join groupes using (idgroupe)WHERE idProfesseur = ? && idControle = ? and actif = 1 
union  
SELECT idControle FROM Controles JOIN enseignements USING(idEnseignement) JOIN matieres USING(codeMatiere) JOIN groupes USING(idGroupe) WHERE idProfesseur = ? && idControle = ? ) foo";
        return (count($this->db->query($sql, array($profId, $controlId,$profId, $controlId))->row()) > 0);
    }

    public function addDsPromo($nom, $coeff, $div,$type, $date, $codeMat,$idEnseignement)
    {

        $sql = "INSERT INTO DsPromo VALUES ('', ?) ";
        $this->db->query($sql, array($codeMat));
        $sql = " SELECT * FROM DsPromo order by idDsPromo desc limit 1";
        $id = $this->db->query($sql)->row()->idDsPromo;

        $sql = "INSERT INTO Controles VALUES ('',?,?,?,?,null,null,?,?,?)";
        return $this->db->query($sql, array($nom, $coeff, $div, $type, $date, $id,$idEnseignement));
    }

    public function addControl($nom, $coeff, $div, $type, $date, $idEnseignement)
    {

        $sql = "INSERT INTO Controles VALUES ('',?,?,?,?,null,null,?,null,?)";
        return $this->db->query($sql, array($nom, $coeff, $div, $type, $date, $idEnseignement));

    }

    public function editControl($nom, $coeff, $div, $type, $date, $controlId)
    {

        $sql = "UPDATE Controles SET nomControle = ? , coefficient = ?, diviseur = ?, typeControle= ?,dateControle = ?  WHERE idControle = ?";
        return $this->db->query($sql, array($nom, $coeff, $div, $type, $date, $controlId));


    }

    public function deleteControl($controlId)
    {

        $sql = "DELETE FROM Controles WHERE idControle = ? ";
        return $this->db->query($sql, array($controlId));
    }

    public function getEnseignements($profId)
    {
        $sql = "SELECT * FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere)  WHERE idProfesseur = ? and actif = 1 ";
        return $this->db->query($sql, array($profId))->result();
    }
    public function getEnseignementWithMatiere($profId,$codeMatiere)
    {
        $sql = "SELECT * FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere)  WHERE idProfesseur = ? and actif = 1 and codeMatiere = ? ";
        return $this->db->query($sql, array($profId,$codeMatiere))->row();
    }
    public function checkEnseignementProf($ens,$prof){
        $sql = "SELECT * from Enseignements where  idEnseignement = ? and idProfesseur = ?";
        return (count($this->db->query($sql, array($ens, $prof))->row()) > 0);
    }

    public function getMatieres($profId)
    {
        $sql = "SELECT distinct * FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere) WHERE idProfesseur = ? and actif = 1 ";
        return $this->db->query($sql, array($profId))->result();

    }

}