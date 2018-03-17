<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absences extends CI_Model
{

    /**
     * Get the absences of all students during a time period.
     *
     * @param Period $period
     * @return array
     */
    public function getInPeriod($period)
    {
        return $this->db->select('idStudent, surname, name, email,
                idAbsence, beginDate, endDate, absenceTypeName, justified')
            ->from('Absence')
            ->join('AbsenceType', 'idAbsenceType')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('beginDate BETWEEN "' . $period->getBeginDate()->format('Y-m-d')
                . '" AND "' . $period->getEndDate()->format('Y-m-d') . '"')
            ->order_by('surname', 'ASC')
            ->order_by('name', 'ASC')
            ->order_by('beginDate', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Get the absences of the students that start at the time.
     *
     * @param DateTime $time
     * @param array $students
     * @return array
     */
    public function getAtTime($time, $students)
    {
        $this->load->config('date_format');
        $studentsId = array_map(function($item) {
            return $item->idStudent;
        }, $students);

        return $this->db
            ->from('Absence')
            ->where('beginDate', $time->format($this->config->item('datetimeSystemFormat')))
            ->where_in('idStudent', $studentsId)
            ->get()
            ->result();
    }

    /**
     * Returns the possible absences types.
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->db
            ->order_by('idAbsenceType')
            ->get('AbsenceType')
            ->result();
    }

    /**
     * Creates an absence.
     *
     * @param int $studentId
     * @param string $beginDate
     * @param string $endDate
     * @param int $idAbsenceType
     * @param bool $justified
     * @return int|bool Created id or FALSE if an error happened
     */
	public function create($studentId, $beginDate, $endDate, $idAbsenceType, $justified)
    {
        $data = array(
            'idStudent' => $studentId,
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'idAbsenceType' => $idAbsenceType,
            'justified' => $justified
        );

        if ($this->db->insert('Absence', $data)) {
		    return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    /**
     * Updates an absence.
     *
     * @param int $absenceId
     * @param string $startDate
     * @param string $endDate
     * @param int $idAbsenceType
     * @param bool $justified
     * @return bool
     */
    public function update($absenceId, $startDate, $endDate, $idAbsenceType, $justified)
    {
        $data = array(
            'beginDate' => $startDate,
            'endDate' => $endDate,
            'idAbsenceType' => $idAbsenceType,
            'justified' => $justified
        );

        return $this->db
            ->where('idAbsence', $absenceId)
            ->update('Absence', $data);
    }

    /**
     * Deletes an absence.
     *
     * @param int $absenceId
     * @return bool
     */
    public function delete($absenceId)
    {
        $this->db->delete('Absence', array('idAbsence' => $absenceId));
        return $this->db->affected_rows();
        
    }

}
