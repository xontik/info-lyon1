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

}
