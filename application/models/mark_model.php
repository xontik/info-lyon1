<?php

/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 13/04/2017
 * Time: 15:42
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Mark_model extends CI_Model {


    public function __construct()
    {

    }
    public function getMarksFromSemester($etuId,$semestre){
        $sql = "SELECT * FROM Notes JOIN Etudiants using(numEtudiant)
			 join Controles using (idControle)
			 join Enseignements using (idEnseignement)
			 join Matieres using(codeMatiere)
			 where numEtudiant = ?  and idGroupe = (
			   Select idGroupe from Etudiantgroupe
			   join Groupes USING (idGroupe)
			   join Semestres USING (idSemestre)
			   where numEtudiant = ? and typeSemestre = ?
			 ) ORDER BY idEnseignement";
        return $this->db->query($sql, array($etuId,$etuId,$semestre))->result();


    }
    public function add($controlId = null,$numStudent = null,$value = null){
		if($controlId != null AND $numStudent != null AND $value != null){
			$this->db->set('idControle', $controlId)
					->set('numEtudiant', $numStudent)
					->set('valeur', $value);
		}
		return $this->db->insert('Notes');
    }
	
    public function addMarks($controlId,$a_numStudent,$a_value){
		$i = 0;
		foreach ($a_numStudent as $numStudent) {
			add($controlId,$numStudent,$a_value[$i]);
			$i++;
		}
    }
	
    public function editMark($controlId = null,$numStudent = null,$newValue = null){
		if($controlId != null AND $numStudent != null AND $newValue != null){
			$this->db->set('valeur', $newValue)
					->where('idControle', $controlId)
					->where('numEtudiant', $numStudent);
		}
		return $this->db->update('Notes');
    }
	
    public function deleteMark($controlId = null,$numStudent = null){
		if($controlId != null AND $numStudent != null){
			return $this->db->where('idControle', $controlId)
						->where('numEtudiant', $numStudent)
						->delete('Notes');
		}
		else{
			return false;
		}
    }


}