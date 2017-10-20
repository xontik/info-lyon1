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
        $sql = 'SELECT idGroupe, nomGroupe, nom, prenom, numEtudiant from Groupes join EtudiantGroupe using(idGroupe) join Etudiants using(numEtudiant) where idSemestre = ? order by idGroupe ';
        return $this->db->query($sql,array($id))->result();
    }

    public function getStudentWithoutGroup(){
        $sql = 'SELECT * from Etudiants where numEtudiant not in (select numEtudiant from EtudiantGroupe join Groupes using(idGroupe) join semestres using(idSemestre) where actif = 1)';
        return $this->db->query($sql)->result();

    }

}
