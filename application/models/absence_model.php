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
     * Get the absences of all the students during the semester
     * @param $semester mixed The id of the semester
     * @return mixed The absences of all students in the period between
     * the beginning and the end of the semester,
     * or FALSE if an error happened with the semester's id
     */
    public function getSemesterAbsences($semester) {
        $CI =& get_instance();
        $CI->load->model('semester_model');

        $period = $CI->semester_model->getSemesterPeriod($semester);
        if ($period === FALSE)
            return FALSE;
        return $this->getAbsencesInPeriod($period);
    }

    /**
     * Get the absences of all students during a time period.
     * The period can be either expressed by two DateTime objects,
     * corresponding to the beginning and the end of the period,
     * or by a Period object.
     * @param $begin_date mixed A period object or the datetime of the beginning of the period
     * @param null $end_date DateTime The datetime of the end of the period
     * @return array The absences of the students
     */
    public function getAbsencesInPeriod($begin_date, $end_date = null) {
        if (is_null($end_date)) {
            // $begin_date must be a period
            $end_date = $begin_date->getEndDate();
            $begin_date = $begin_date->getBeginDate();
        }
        return $this->db->select('numEtudiant, nom, prenom, mail,
                idAbsence, dateDebut, dateFin, typeAbsence, justifiee')
            ->from('absences')
            ->join('etudiants', 'numEtudiant')
            ->where('dateDebut BETWEEN "' . $begin_date->format('Y-m-d')
                . '" AND "' . $end_date->format('Y-m-d') . '"')
            ->order_by('etudiants.nom', 'asc')
            ->order_by('etudiants.prenom', 'asc')
            ->order_by('absences.dateDebut', 'asc')
            ->get()
            ->result();
    }

    /**
     * Get the absence of a student during a semester
     * @param $studentId String The id of the student
     * @param $semesterId int The id of the semester
     * @return mixed An array of absences,
     * FALSE if there's an error with semesterId
     */
	public function getStudentSemesterAbsence($studentId, $semesterId) {
        $CI =& get_instance();
        $CI->load->model('semester_model');

        $bounds = $CI->semester_model->getSemesterPeriod($semesterId);
        if ($bounds === FALSE)
            return FALSE;

        return $this->db->select('*')
                        ->from('Absences')
                        ->where('numEtudiant', $studentId)
                        ->where('dateDebut BETWEEN "' . $bounds->getBeginDate()->format('Y-m-d')
                            . '" AND "' . $bounds->getEndDate()->format('Y-m-d') . '"')
                        ->get()
                        ->result();
    }
}