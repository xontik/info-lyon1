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
	
	public function getAbsencesFromSemester($etuId, $semestreId) {
        $CI =& get_instance();
        $CI->load->model("semester_model");

        $semDates = $CI->semester_model->getSemesterBounds($semestreId);

        return $this->db->select('*')
                        ->from('Absences')
                        ->where('numEtudiant', $etuId)
                        ->where('dateDebut BETWEEN "' . $semDates[0] . '" AND "' . $semDates[1] . '"')
                        ->get()
                        ->result();
    }
}