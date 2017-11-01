<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absence_model extends CI_Model
{

    /**
     * @return array The absence types
     */
    public function getAbsenceTypes() {
        return $this->db->order_by('idTypeAbsence', 'asc')
            ->get('TypeAbsence')
            ->result();
    }

    /**
     * Creates a new absence entry.
     * @param string $studentId The student id
     * @param string $startDate The time at which absence started
     * @param string $endDate The time at which absence ended
     * @param String|int $absenceType The type of the absence
     * @param boolean $justify Whether the absence is justified or not
     * @return boolean Whether the insert was successful or not
     */
	public function add($studentId, $startDate, $endDate, $absenceType, $justify)
    {
        if (is_string($absenceType)) {
            $absenceType = $this->db->select('idTypeAbsence')
                ->where('nomTypeAbsence', $absenceType)
                ->get('TypeAbsence')
                ->row();

            if ($absenceType === FALSE) {
                return FALSE;
            }

            $idAbsenceType = $absenceType->idTypeAbsence;
        } else {
            $idAbsenceType = $absenceType;
        }

	    $data = array(
	        'dateDebut' => $startDate,
	        'dateFin' => $endDate,
            'typeAbsence' => $idAbsenceType,
            'numEtudiant' => $studentId,
            'justifiee' => $justify
        );

		return $this->db->insert('Absences', $data);
    }

    /**
     * Get the absences of all the students during the semester
     * @param $semester mixed The id of the semester
     * @return mixed The absences of all students in the period between
     * the beginning and the end of the semester,
     * or FALSE if an error happened with the semester's id
     */
    public function getSemesterAbsences($semester)
    {
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
     * @param DateTime|Period $beginDate A period object or the datetime of the beginning of the period
     * @param DateTime $endDate The datetime of the end of the period (optionnal)
     * @return array The absences of the students
     */
    public function getAbsencesInPeriod($beginDate, $endDate = null)
    {
        if (is_null($endDate)) {
            // $beginDate must be a period
            $endDate = $beginDate->getEndDate();
            $beginDate = $beginDate->getBeginDate();
        }

        return $this->db->select('numEtudiant, nom, prenom, mail,
                idAbsence, dateDebut, dateFin, nomTypeAbsence as typeAbsence, justifiee')
            ->from('Absences')
            ->join('TypeAbsence', 'idTypeAbsence')
            ->join('Etudiants', 'numEtudiant')
            ->where('dateDebut BETWEEN "' . $beginDate->format('Y-m-d')
                . '" AND "' . $endDate->format('Y-m-d') . '"')
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
	public function getStudentSemesterAbsence($studentId, $semesterId)
    {
        $CI =& get_instance();
        $CI->load->model('semester_model');

        $bounds = $CI->semester_model->getSemesterPeriod($semesterId);
        if ($bounds === FALSE) {
            return FALSE;
        }

        return $this->db->select('numEtudiant, idAbsence, dateDebut, dateFin,
                nomTypeAbsence as typeAbsence, justifiee')
            ->from('Absences')
            ->join('TypeAbsence', 'idTypeAbsence')
            ->where('numEtudiant', $studentId)
            ->where('dateDebut BETWEEN "' . $bounds->getBeginDate()->format('Y-m-d')
            . '" AND "' . $bounds->getEndDate()->format('Y-m-d') . '"')
            ->order_by('dateDebut')
            ->get()
            ->result();
    }

}
