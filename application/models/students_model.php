<?php
/**
 * Created by PhpStorm.
 * User: enzob
 * Date: 17/09/2017
 * Time: 12:06
 */

class students_model extends CI_Model {

    /**
     * @return array The id, name, surname and email of all students in the database,
     * ordered by semester type, then group
     */
    public function getStudentsOrganized() {
        return $this->db->select('numEtudiant, nom, prenom, mail, CONCAT(Groupes.nomGroupe, Parcours.type) as nomGroupe')
            ->from('Etudiants')
            ->join('EtudiantGroupe', 'numEtudiant')
            ->join('Groupes', 'idGroupe')
            ->join('Semestres', 'idSemestre')
            ->join('Parcours', 'idParcours')
            ->where('Semestres.actif', '1')
            ->order_by('Parcours.type', 'asc')
            ->order_by('Groupes.idGroupe', 'asc')
            ->order_by('Etudiants.nom', 'asc')
            ->order_by('Etudiants.prenom', 'asc')
            ->get()
            ->result();
    }

    /**
     * @param $studentId String The id of the student you want the informations
     * @return array The id, name, surname et email of the student
     */
    public function getStudent($studentId) {
        return $this->db->select('numEtudiant, nom, prenom, mail')
            ->where('numEtudiant', $studentId)
            ->get('Etudiants')
            ->row();
    }

    public function getStudentsBySemestre($id){
        $sql = 'SELECT idGroupe, nomGroupe, nom, prenom, numEtudiant from Groupes left join EtudiantGroupe using(idGroupe) left join Etudiants using(numEtudiant) where idSemestre = ? order by nomGroupe ';
        return $this->db->query($sql,array($id))->result();
    }



    public function isStudentInGroup($numEtu,$groupId){
        $sql = 'SELECT  * from EtudiantGroupe where numEtudiant=? and idGroupe = ?';
        return $this->db->query($sql,array($numEtu,$groupId))->num_rows() > 0;
    }
    //semesterIds : les ids des semestre a verifier
    public function isStudentInGroupsOfSemesters($numEtudiant,$semesterIds){
        $sql = 'SELECT  * from EtudiantGroupe join groupes using(idGroupe) join Semestres using(idSemestre) join parcours using(idParcours)  where numEtudiant=? and idSemestre IN ?';
        $row = $this->db->query($sql,array($numEtudiant,$semesterIds))->row();
        if(empty($row)){
            return false;
        }
        else{
            return $row;
        }
    }


    public function deleteRelationGroupStudent($groupId,$numEtudiant){
        $sql = 'DELETE FROM EtudiantGroupe where idGroupe = ? and numEtudiant = ?';
        return $this->db->query($sql,array($groupId,$numEtudiant));
    }
    public function deleteAllRelationForGroup($groupeId){
        $sql = 'DELETE FROM EtudiantGroupe where idGroupe = ?';
        return $this->db->query($sql,array($groupeId));
    }

    public function addToGroupe($numEtudiant,$groupId){
        $sql = 'INSERT INTO EtudiantGroupe VALUES(\'\',?,?)';
        return $this->db->query($sql,array($numEtudiant,$groupId));
    }
    public function getIdsFromGroup($idGroup){
        return array_column($this->db->query('SELECT numEtudiant from EtudiantGroupe where idGroupe = ?',array($idGroup))->result_array(),'numEtudiant');
    }



}
