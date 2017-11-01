<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mark_model extends CI_Model {
    
    /**
    * Returns all the marks of a student in a semester
    * @param $studentId String The student id
    * @param $semestreId int The semester id
    * @return mixed The marks of the student during a semester
    */
    public function getMarksFromSemester($studentId, $semestreId)
    {
        $sql = 'SELECT codeMatiere, nomMatiere, nomControle,
                coefficient, diviseur, nomTypeControle, median, average,
                dateControle, coefficientMatiere, valeur, idDSPromo
                FROM Notes
                JOIN Controles USING (idControle)
                JOIN TypeControle USING (idTypeControle)
                JOIN Enseignements USING (idEnseignement)
                JOIN Matieres USING (idMatiere)
                JOIN Groupes USING  (idGroupe)
                WHERE numEtudiant = ? AND idSemestre = ?
            UNION
                SELECT DISTINCT codeMatiere, nomMatiere, nomControle,
                coefficient, diviseur, nomTypeControle, median, average,
                dateControle, coefficientMatiere, valeur, idDSPromo
                FROM Notes
                JOIN Controles USING (idControle)
                JOIN TypeControle USING (idTypeControle)
                JOIN DsPromo USING (idDSPromo)
                JOIN Matieres USING (idMatiere)
                JOIN Enseignements USING (idMatiere)
                WHERE numEtudiant = ? AND idSemestre = ?';
        
        return $this->db->query($sql, array($studentId, $semestreId, $studentId, $semestreId))
            ->result();
    }
    
    /**
    * Add a note for a student to a specified test
    * @param $controlId int The id of the test
    * @param $studentId String The id of the student
    * @param $value float The note
    */
    public function add($controlId, $studentId, $value) {
        $data = array(
            'numEtudiant' => $studentId,
            'idControle' => $controlId,
            'valeur' => $value
        );
        
        $this->db->insert('Notes', $data);
    }
    
    /**
    * Add the notes of the students to the specific test
    * @param $controlId int The test id
    * @param $studentsNotes array An associative array of the following shape :
    * { studentId1 => note1, studentId2 => note2, ... }
    * with "studentId" being a string, AND "note" a float
    */
    public function addMarks($controlId, $studentsNotes) {
        foreach ($studentsNotes as $student => $note) {
            if ($note !== '') {
                $this->add($controlId, $student, $note);
            } else {
                $this->deleteMark($controlId, $student);
            }
        }
    }
    
    /**
    * Modify the note of a student to a test
    * @param $controlId int The ID of the test
    * @param $studentId String The student id
    * @param $newValue float The new note
    */
    public function editMark($controlId, $studentId, $newValue) {
        $this->db->set('valeur', $newValue)
            ->where('idControle', $controlId)
            ->where('numEtudiant', $studentId);
        
        $this->db->update('Notes');
    }
    
    /**
    * Deletes the note of a student in a test
    * @param $controlId int The test id
    * @param $studentId String The student id
    */
    public function deleteMark($controlId, $studentId) {
        $this->db->where('idControle', $controlId)
            ->where('numEtudiant', $studentId)
            ->delete('Notes');
    }
    
    
    public function getControlMarks($controlId)
    {
        $sql = 'SELECT nom, prenom, Etudiants.numEtudiant, valeur
            FROM Controles
            JOIN Enseignements USING (idEnseignement)
            JOIN EtudiantGroupe USING (idGroupe)
            JOIN Etudiants USING (numEtudiant)
            JOIN groupes USING (idGroupe)
            JOIN Semestres USING (idSemestre)
            LEFT JOIN Notes USING (numEtudiant, idControle)
            WHERE Controles.idControle = ? AND actif = 1';
        
            return $this->db->query($sql, array($controlId))
                ->result();
    }
    
    public function getDsPromoAllMarks($controlId) {
        $sql = 'SELECT nom, prenom, Etudiants.numEtudiant, valeur
            FROM Controles
            JOIN DsPromo USING (idDSPromo)
            JOIN Semestres USING (idSemestre)
            JOIN Groupes USING (idSemestre)
            JOIN Etudiantgroupe USING (idGroupe)
            JOIN Etudiants USING (numEtudiant)
            LEFT JOIN Notes USING (numEtudiant, idControle)
            WHERE Controles.idControle = ? AND actif = 1';
        
        return $this->db->query($sql, array($controlId))
            ->result();
    }
    
    public function getDsPromoMarks($profId,$controlId) {
        $sql = 'SELECT nom, prenom, Etudiants.numEtudiant, valeur
            FROM Controles
            JOIN DsPromo USING (idDSPromo)
            JOIN Semestres USING (idSemestre)
            JOIN Groupes USING (idSemestre)
            JOIN Etudiantgroupe USING (idGroupe)
            JOIN Etudiants USING (numEtudiant)
            JOIN Enseignements USING (idGroupe, idMatiere)
            LEFT JOIN Notes USING (numEtudiant, idControle)
            WHERE Controles.idControle = ? AND actif = 1 AND idProfesseur = ?';
        
        return $this->db->query($sql, array($controlId, $profId))
            ->result();
    }
        
    public function getMarks($control,$profId) {
        $CI =& get_instance();
        $CI->load->model('control_model');
        
        if (!is_null($control->idDSPromo)) {
            if ($CI->control_model->isReferent($profId, $control->idControle)) {
                $marks = $this->getDsPromoAllMarks($control->idControle);
            } else {
                $marks = $this->getDsPromoMarks($profId, $control->idControle);
            }
        } else {
            $marks = $this->getControlMarks($control->idControle);
        }
        return $marks;
    }

}
