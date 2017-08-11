<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Absence_model extends CI_Model {

    /**
     * Creates a new absence entry.
     * @param $studentId String The student id
     * @param $startDate String The time at which absence started
     * @param $endDate String The time at which absence ended
     * @param $absenceType String The type of the absence
     * @param $justify boolean Whether the absence is justified or not
     */
	public function add($studentId, $startDate, $endDate, $absenceType, $justify) {
	    $data = array(
	        'dateDebut' => $startDate,
	        'dateFin' => $endDate,
	        'typeAbsence' => $absenceType,
            'numEtudiant' => $studentId,
            'justifiee' => $justify
        );

		$this->db->insert('Absences', $data);
    }

    /**
     * Get the absence of a student during a semester
     * @param $studentId String The id of the student
     * @param $semestreId int The id of the semester
     * @return array An array of absences
     */
	public function getAbsencesFromSemester($studentId, $semestreId) {
        $CI =& get_instance();
        $CI->load->model('semester_model');

        $semDates = $CI->semester_model->getSemesterBounds($semestreId);

        if ($semDates === FALSE) {
            return FALSE;
        }

        return $this->db->select('*')
                        ->from('Absences')
                        ->where('numEtudiant', $studentId)
                        ->where('dateDebut BETWEEN "' . $semDates[0] . '" AND "' . $semDates[1] . '"')
                        ->get()
                        ->result();
    }
}