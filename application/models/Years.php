<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Years extends CI_Model
{

    /**
     * Returns all years and their students.
     *
     * @return array
     */
    public function getAll() {
        return $this->db
            ->select(
                'idSemester, CONCAT(schoolYear,\'-\', schoolYear+1) as schoolYear,
                idStudent, name, surname'
            )
            ->from('Semester')
            ->join('Group', 'idSemester')
            ->join('StudentGroup', 'idGroup')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->get()
            ->result();
    }

}