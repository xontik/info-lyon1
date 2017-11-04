<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TeachingUnits extends CI_Model
{

    /**
     * Get all teaching units.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db
            ->distinct()
            ->select('idTeachingUnit, teachingUnitName, teachingUnitCode, Courses.creationYear, idCourse')
            ->from('Courses')
            ->join('TeacherUnitOfCourse', 'idCourse')
            ->join('TeachingUnit', 'idTeachingUnit')
            ->order_by('idCourse', 'DESC')
            ->get()
            ->result();
    }

}