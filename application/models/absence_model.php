<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Absence_model extends CI_Model {

    public function __construct()
    {

    }
	
	public function add($absenceId, $absenceType, $numStudent, $justify, $startDate, $endDate) {
		$this->db->set('idAbsence', $absenceId)
		    	->set('typeAbsence', $absenceType)
			    ->set('numEtudiant', $numStudent)
				->set('justifiee', $justify)
				->set('dateDebut', $startDate)
				->set('dateFin', $endDate);

		return $this->db->insert('Absences');
    }
	
	public function getAbsencesFromSemester($etuId,$semestre){

		$selectIdG = $this->db->select('idGroupe')
							->from('EtudiantGroupe')
							->join('Groupes', 'EtudiantGroupe.idGroupe = Groupes.idGroupe')
							->join('Semestres', 'Groupes.idSemestre = Semestres.idSemestre')
							->where('typeSemestre', $semestre)
							->where('numEtudiant', $etuId)
							->get()
							->result();

		return $this->db->select('*')
						->from('Absences')
						->join('Etudiants', 'Absences.numEtudiant = Etudiants.numEtudiant')
						->join('EtudiantGroupe', 'Etudiants.numEtudiant = EtudiantGroupe.numEtudiant')
						->where('numEtudiant', $etuId)
						->where('idGroupe', $selectIdG)
						->get()
						->result();
    }
}