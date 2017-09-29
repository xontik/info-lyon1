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
    $sql = "SELECT foo.codeMatiere,foo.idMatiere,foo.nomMatiere,foo.idControle,foo.nomControle,
    foo.coefficient,foo.diviseur,foo.nomTypeControle,foo.idTypeControle,foo.median,foo.average,
    foo.dateControle,foo.coefficientMatiere,foo.nomGroupe,foo.idGroupe
    from (
      Select codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
      coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
      dateControle,coefficientMatiere,nomGroupe,idGroupe FROM Controles
        join TypeControle USING (idTypeControle)
        JOIN Enseignements USING (idEnseignement)
        JOIN Matieres USING (idMatiere)
        JOIN Groupes USING (idGroupe)
        join Semestres using (idSemestre)
        where idProfesseur = ? and actif = 1
      UNION
      Select distinct codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
      coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
      dateControle,coefficientMatiere,null as nomGroupe,null as idGroupe FROM Controles
        join TypeControle USING (idTypeControle)
        JOIN DsPromo USING (idDSPromo)
        join Matieres using (idMatiere)
        join Enseignements USING (idMatiere)
        join Semestres using (idSemestre)
        where idProfesseur = ? and actif = 1
      UNION
      Select codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
      coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
      dateControle,coefficientMatiere,nomGroupe,idGroupe FROM Controles
        join TypeControle USING (idTypeControle)
        JOIN Enseignements USING (idEnseignement)
        JOIN Matieres USING (idMatiere)
        JOIN Groupes USING (idGroupe)
        join MatieresDeModules using (idMatiere)
        JOIN Referents using (idModule,idSemestre)
        join Semestres using (idSemestre)
        where Referents.idProfesseur = ? and  actif = 1
      UNION
      Select codeMatiere,idMatiere,nomMatiere,idControle,nomControle,
      controles.coefficient,diviseur,idTypeControle,nomTypeControle,median,average,
      dateControle,coefficientMatiere,null as nomGroupe,null as idGroupe FROM Controles
        join TypeControle USING (idTypeControle)
        JOIN DsPromo USING (idDSPromo)
        join Matieres USING (idMatiere)
        join MatieresDeModules using (idMatiere)
        JOIN Modules using (idModule)
        JOIN Referents using (idModule,idSemestre)
        join Semestres using (idSemestre)

        where idProfesseur = ? and actif = 1
    ) as foo ";

    //TODO continuer de verifier les different cas pour les ds surtout via Referents
    return $this->db->query($sql, array($professorId,$professorId,$professorId,$professorId))->result();
  }



  public function isReferent($profId,$controlId){
    $sql = "SELECT count(*) as nb from Referents join MatieresDeModules USING (idModule)
    left join DsPromo USING (idMatiere)
    join Controles using (idDSPromo) where idControle = ? and idProfesseur = ?";
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
      join DsPromo USING (idDSPromo)
      join Semestres using (idSemestre)
      join Enseignements using (idMatiere)
      where idProfesseur = ? and actif = 1) as foo";
      return $this->db->query($sql, array($profId,$profId))->result();

    }
    public function getControlIdsFromReferentStatus($profId){
      $sql = "SELECT idControle FRom Referents
      join MatieresDeModules using(idModule)
      JOIN Enseignements using (idMatiere)
      join Semestres using (idSemestre)
      JOIN Controles USING (idEnseignement)
      where Referents.idProfesseur = ? and actif = 1
      UNION
      SELECT idControle FRom Referents
      join MatieresDeModules using(idModule)
      JOIN DsPromo using (idMatiere,idSemestre)
      join Semestres using (idSemestre)
      JOIN Controles USING (idDSPromo)
      where Referents.idProfesseur = ? and actif = 1

      ";

      return $this->db->query($sql, array($profId,$profId))->result();

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

    public function getCurrentSemestreFromMatiere($idMat){
      $sql = "SELECT idSemestre FROM Matieres JOIN MatieresDeModules USING (idMatiere) JOIN ModulesDeUE using(idModule)
      join UEdeParcours  USING (idUE)
      join Parcours using (idParcours)
      join Semestres using (idParcours) where idMatiere = ? and actif = 1 ";
      return $this->db->query($sql,array($idMat))->row()->idSemestre;
    }
    public function addDsPromo($nom, $coeff, $div,$type, $date, $idMat)
    {

      $sql = "INSERT INTO DsPromo VALUES ('', ?,?) ";

      $semestre = $this->ctrlMod->getCurrentSemestreFromMatiere($idMat);

      $this->db->query($sql, array($codeMat,$semestre));
      $sql = " SELECT * FROM DsPromo order by idDSPromo desc limit 1";
      $idpromo = $this->db->query($sql)->row()->idDSPromo;

      $sql = "INSERT INTO Controles VALUES ('',?,?,?,?,null,null,?,null,?)";
      return $this->db->query($sql, array($nom, $coeff, $div, $type, $date, $idpromo));
    }

    public function addControl($nom, $coeff, $div, $type, $date, $idEnseignement)
    {

      $sql = "INSERT INTO Controles VALUES ('',?,?,?,?,null,null,?,?,null)";
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

      $sql = "SELECT distinct * from (
      SELECT nomGroupe,nomMatiere,idEnseignement FROM Enseignements
  join Groupes using(idGroupe)
  join Matieres using(idMatiere)
  join Semestres using (idSemestre)
  WHERE idProfesseur = ? and actif = 1
UNION
  SELECT nomGroupe,nomMatiere,idEnseignement FROM Referents
  join MatieresDeModules using(idModule)
  JOIN Matieres using (idMatiere)
  join Enseignements using(idMatiere)
  join Semestres using (idSemestre)
  JOIN Groupes using (idGroupe,idSemestre)
  WHERE Referents.idProfesseur = ? and actif = 1) as foo ";

      return $this->db->query($sql, array($profId,$profId))->result();
    }

    public function getGroupes($profId){
      $sql = "SELECT distinct * from (
      SELECT nomGroupe,type,idGroupe FROM Enseignements
  join Groupes using(idGroupe)
  join Semestres using (idSemestre)
  join Parcours using(idParcours)
  WHERE idProfesseur = ? and actif = 1
  UNION
  SELECT nomGroupe,type,idGroupe FROM Referents
  join MatieresDeModules using(idModule)
  JOIN Enseignements using (idMatiere)
  join Groupes using(idGroupe,idSemestre)
  join Semestres using (idSemestre)
  join Parcours using (idParcours)
  WHERE Referents.idProfesseur = ? and actif = 1) as foo ORDER BY type ";

      return $this->db->query($sql, array($profId,$profId))->result();
    }
    /*public function getEnseignementWithMatiere($profId,$codeMatiere)
    {
    $sql = "SELECT nomGroupe,nomMatiere FROM Enseignements join Groupes using(idGroupe) join Matieres using(codeMatiere)  WHERE idProfesseur = ? and actif = 1 and codeMatiere = ? ";
    return $this->db->query($sql, array($profId,$codeMatiere))->row();
  }
  //*/

  public function checkEnseignementProf($ens,$prof){
    $sql = "SELECT idEnseignement from Enseignements
    join groupes using(idGroupe)
    join Semestres using (idSemestre)
    where  idEnseignement = ? and actif = 1 and idProfesseur = ?
    UNION
    select idEnseignement from Referents
    join MatieresDeModules using(idModule)
    join Enseignements using(idMatiere)
    join Semestres using(idSemestre)
    where  idEnseignement = ? and actif = 1 and Referents.idProfesseur = ? ";
    return (count($this->db->query($sql, array($ens, $prof,$ens, $prof))->row()) > 0);
  }
  //*/

  public function getMatieres($profId)
  {
    $sql = "SELECT distinct idMatiere,codeMatiere,nomMatiere FROM Enseignements
  join Groupes using(idGroupe)
  join Matieres using(idMatiere)
  join Semestres using (idSemestre)
  WHERE idProfesseur = ? and actif = 1
  UNION
  SELECT distinct idMatiere,codeMatiere,nomMatiere FROM Referents
  join MatieresDeModules using( idModule)
  JOIN Matieres using (idMatiere)
  join Semestres using (idSemestre)
  where idProfesseur = ? and actif = 1";

    return $this->db->query($sql, array($profId,$profId))->result();

  }
  public function getMatiere($idControle){
    $sql = "SELECT distinct * from (SELECT codeMatiere, nomMatiere FROM Controles JOIN DsPromo using (idDSPromo) join Matieres using (idMatiere)  WHERE idControle = ?
    UNION SELECT codeMatiere, nomMatiere FROM Controles JOIN Enseignements using (idEnseignement) join Matieres using (idMatiere)  WHERE idControle = ?) as foo ";
    return $this->db->query($sql, array($idControle,$idControle))->row();
  }

  public function getTypeControle(){
    $sql = "SELECT  idTypeControle,nomTypeControle FROM typeControle";

    return $this->db->query($sql)->result();
  }
}
