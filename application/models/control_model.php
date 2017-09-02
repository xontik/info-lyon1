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
    $sql = "SELECT foo.codeMatiere,foo.nomMatiere,foo.idControle,foo.nomControle,
    foo.coefficient,foo.diviseur,foo.typeControle,foo.median,foo.average,
    foo.dateControle,foo.coefficientMatiere,foo.nomGroupe
    from (
      Select codeMatiere,nomMatiere,idControle,nomControle,
      coefficient,diviseur,typeControle,median,average,
      dateControle,coefficientMatiere,nomGroupe
      FROM Controles JOIN Enseignements USING (idEnseignement) JOIN Matieres USING (codeMatiere)
      JOIN Groupes USING (idGroupe) where idProfesseur = ?
      UNION
      Select distinct codeMatiere,nomMatiere,idControle,nomControle,
      coefficient,diviseur,typeControle,median,average,
      dateControle,coefficientMatiere,null as nomGroupe
      FROM Controles JOIN DsPromo USING (idDsPromo)  join Matieres using (codeMatiere) join Enseignements USING (codeMatiere) where idProfesseur = ?
      UNION
      Select codeMatiere,nomMatiere,idControle,nomControle,
      coefficient,diviseur,typeControle,median,average,
      dateControle,coefficientMatiere,nomGroupe
      FROM Controles JOIN Enseignements USING (idEnseignement) JOIN Matieres USING (codeMatiere)
      JOIN Groupes USING (idGroupe) JOIN Referents using (codeModule) where Referents.idProfesseur = ?
      UNION
      Select codeMatiere,nomMatiere,idControle,nomControle,
      controles.coefficient,diviseur,typeControle,median,average,
      dateControle,coefficientMatiere,null as nomGroupe
      FROM Controles JOIN DsPromo USING (idDsPromo) join Matieres USING (codeMatiere) JOIN Modules using (codeModule)
      JOIN Referents using (codeModule) where Referents.idProfesseur = ?
    ) as foo JOIN Matieres using (codeMatiere) JOIN Modules USING (codeModule) join UE USING (codeUE) join Semestres using (typeSemestre) where actif = 1";


    return $this->db->query($sql, array($professorId,$professorId,$professorId,$professorId))->result();
  }



  public function isReferent($profId,$controlId){
    $sql = "SELECT count(*) as nb from Referents join Matieres USING (codeModule)
    left join DsPromo USING (codeMatiere)
    join Controles using (idDsPromo) where idControle = ? and idProfesseur = ?";
    return $this->db->query($sql,array($controlId,$profId))->row()->nb > 0;


  }
  public function getControlIdsFromTeacherStatus($profId){
    $sql = "SELECT idControle from (Select idControle from Controles
      JOIN Enseignements using (idEnseignement)
      join groupes using (idGroupe)
      join Semestres using (idSemestre)

      where idProfesseur = ? and actif = 1
      UNION
      Select idControle from Controles
      join DsPromo USING (idDsPromo)
      join Enseignements USING (codeMatiere)
      JOIN Matieres using (codeMatiere)
      JOIN Modules USING (codeModule)
      join UE USING (codeUE)
      join Semestres using (typeSemestre)
      where idProfesseur = ? and actif = 1) as foo";
      return $this->db->query($sql, array($profId,$profId))->result();

    }
    public function getControlIdsFromReferentStatus($profId){
      $sql = "SELECT idControle FRom Referents
      JOIN Matieres USING (codeModule)
      JOIN Modules USING (codeModule)
      join UE USING (codeUE)
      join Semestres using (typeSemestre)
      JOIN Enseignements using (codeMatiere)
      JOIN Controles USING (idEnseignement)

      where Referents.idProfesseur = ? and actif = 1";

      return $this->db->query($sql, array($profId))->result();

    }
    public function checkProfessorRightOnControl($profId, $controlId)
    {
      $ids = array_merge($this->getControlIdsFromTeacherStatus($profId),
      $this->getControlIdsFromReferentStatus($profId));

      foreach ($ids as $id) {
        if($id->idControle == $controlId){
          return true;
        }
      }
      return false;
    }

    public function getCurrentSemestreFromMatiere($codeMat){
      $sql = "SELECT idSemestre FROM Matieres JOIN Modules USING (codeModule)
      join UE USING (codeUE)
      join Semestres using (typeSemestre) where codeMatiere = ? and actif = 1 ";
      return $this->db->query($sql,array($codeMat))->row()->idSemestre;
    }
    public function addDsPromo($nom, $coeff, $div,$type, $date, $codeMat,$idEnseignement)
    {

      $sql = "INSERT INTO DsPromo VALUES ('', ?,?) ";

      $semestre = $this->ctrlMod->getCurrentSemestreFromMatiere($codeMat);

      $this->db->query($sql, array($codeMat,$semestre));
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
      $sql = "SELECT nomGroupe,nomMatiere,idEnseignement FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere) join Semestres using (idSemestre) WHERE idProfesseur = ? and actif = 1 ";
      return $this->db->query($sql, array($profId))->result();
    }
    /*public function getEnseignementWithMatiere($profId,$codeMatiere)
    {
    $sql = "SELECT nomGroupe,nomMatiere FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere)  WHERE idProfesseur = ? and actif = 1 and codeMatiere = ? ";
    return $this->db->query($sql, array($profId,$codeMatiere))->row();
  }
  //*/

  public function checkEnseignementProf($ens,$prof){
    $sql = "SELECT * from Enseignements join groupes using(idGroupe) join Semestres using (idSemestre) where  idEnseignement = ? and actif = 1 and idProfesseur = ? ";
    return (count($this->db->query($sql, array($ens, $prof))->row()) > 0);
  }
  //*/

  public function getMatieres($profId)
  {
    $sql = "SELECT distinct codeMatiere,nomMatiere FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere) join Semestres using (idSemestre) WHERE idProfesseur = ? and actif = 1";
    return $this->db->query($sql, array($profId))->result();

  }
  public function getMatiere($idControle){
    $sql = "SELECT distinct * from (SELECT codeMatiere, nomMatiere FROM Controles JOIN DsPromo using (idDsPromo) join Matieres using (codeMatiere)  WHERE idControle = ?
    UNION SELECT codeMatiere, nomMatiere FROM Controles JOIN Enseignements using (idEnseignement) join Matieres using (codeMatiere)  WHERE idControle = ?) as foo ";
    return $this->db->query($sql, array($idControle,$idControle))->row();
  }
}
