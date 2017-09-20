<?php
/**
 * Created by PhpStorm.
 * User: enzob
 * Date: 17/09/2017
 * Time: 12:06
 */

class students_model extends CI_Model {

    /**
     * @return array The id, name, surname and email of all students in the database
     */
    public function getStudents() {
        return $this->db->select('numEtudiant, nom, prenom, mail, CONCAT(Groupes.nomGroupe, Semestres.typeSemestre) as nomGroupe')
            ->from('Etudiants')
            ->join('EtudiantGroupe', 'numEtudiant')
            ->join('Groupes', 'idGroupe')
            ->join('Semestres', 'idSemestre')
            ->where('Semestres.actif', '1')
            ->order_by('Semestres.typeSemestre', 'asc')
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

}
