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
        return $this->db->select('numEtudiant, nom, prenom, mail')
            ->order_by('nom', 'asc')
            ->order_by('prenom', 'asc')
            ->get('Etudiants')
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
