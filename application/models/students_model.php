<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class students_model extends CI_Model {

    /**
     * @return array The id, name, surname and email of all students in the database,
     * ordered by semester type, then group
     */
    public function getStudentsOrganized() {
        //TODO Check results
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

    public function getProfesseursByStudent($numEtudiant) {
        $query = "SELECT idProfesseur, nom, prenom FROM professeurs
                  JOIN enseignements USING (idProfesseur)
                  JOIN groupes USING (idGroupe)
                  WHERE idGroupe = (SELECT idGroupe FROM etudiantgroupe 
                                    JOIN groupes USING (idGroupe)
                                    JOIN semestres USING (idSemestre)
                                    WHERE numEtudiant = ? AND actif = 1
                                    )
                  ORDER BY nom ASC";

        return $this->db->query($query, array($numEtudiant))
                        ->result();
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

    /**
     * Returns the students that are in a semester
     *
     * @param int $semesterId
     * @return array The students
     */
    public function getStudentsBySemestre($semesterId)
    {
        $sql = 'SELECT idGroupe, nomGroupe, nom, prenom, numEtudiant
            FROM Groupes
            LEFT JOIN EtudiantGroupe USING (idGroupe)
            LEFT JOIN Etudiants USING (numEtudiant)
            WHERE idSemestre = ?
            ORDER BY nomGroupe ';
        
        return $this->db->query($sql, array($semesterId))
            ->result();
    }

    /**
     * Checks if a student is an a group.
     *
     * @param string $numEtu
     * @param int $groupId
     * @return bool
     */
    public function isStudentInGroup($numEtu, $groupId)
    {
        return $this->db->where('numEtudiant', $numEtu)
            ->where('idGroupe', $groupId)
            ->get('EtudiantGroupe')
            ->num_rows() > 0;
    }

    /**
     * @param string $numEtudiant
     * @param array $semesterIds
     * @return bool|array
     */
    public function isStudentInGroupsOfSemesters($numEtudiant, $semesterIds)
    {
        if (empty($semesterIds)) {
            return false;
        }

        $sql = 'SELECT *
            FROM EtudiantGroupe
            JOIN Groupes USING (idGroupe)
            JOIN Semestres USING (idSemestre)
            JOIN Parcours USING (idParcours)
            WHERE numEtudiant = ? AND idSemestre IN ?';

        $row = $this->db->query($sql, array($numEtudiant, $semesterIds))
            ->row();

        if (empty($row)) {
            return false;
        }
        return $row;
    }

    /**
     * Remove a student from a group.
     *
     * @param $groupId
     * @param $numEtudiant
     * @return bool
     */
    public function deleteRelationGroupStudent($groupId, $numEtudiant)
    {
        return $this->db->where('idGroupe', $groupId)
            ->where('numEtudiant', $numEtudiant)
            ->delete('EtudiantGroupe');
    }

    /**
     * Remove all students from a group.
     *
     * @param $groupId
     * @return bool
     */
    public function deleteAllRelationForGroup($groupId)
    {
        return $this->db->delete('EtudiantGroupe', array('idGroupe' => $groupId));
    }

    /**
     * Add a student to a group.
     *
     * @param $numEtudiant
     * @param $groupId
     * @return bool
     */
    public function addToGroupe($numEtudiant, $groupId)
    {
        $data = array(
            'numEtudiant' => $numEtudiant,
            'idGroupe' => $groupId
        );
        return $this->db->insert('EtudiantGroupe', $data);
    }

    /**
     * Get the ids of the students in a group.
     *
     * @param $idGroup
     * @return array
     */
    public function getIdsFROMGroup($idGroup)
    {
        $res = $this->db->select('numEtudiant')
            ->from('EtudiantGroupe')
            ->where('idGroupe', $idGroup)
            ->get()
            ->result_array();
        return array_column($res, 'numEtudiant');
    }

}
