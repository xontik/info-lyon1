<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

    /**
     * Return the students name and surname from a group
     *
     * @param $idGroupe
     * @return array The students
     */
    public function getStudentNameSurnameFromGroup($idGroupe)
    {
        return $this->db->select('nom, prenom')
            ->from('Etudiants')
            ->join('EtudiantGroupe', 'Etudiants.numEtudiant = EtudiantGroupe.numEtudiant')
            ->join('Groupes', 'Groupes.idGroupe = EtudiantGroupe.idGroupe')
            ->where('Groupes.idGroupe', $idGroupe)
            ->get()
            ->result();
    }

}
