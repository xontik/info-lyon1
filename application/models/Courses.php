<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courses extends CI_Model
{

    /**
     * Checks if a course exists.
     *
     * @param int $courseId
     * @return bool
     */
    public function exists($courseId)
    {
        return $this->db
                ->where('idCourse', $courseId)
                ->get('Course')
                ->num_rows() > 0;
    }

    /**
     * Checks if a course is editable.
     *
     * @param int $courseId
     * @return bool
     */
    public function isEditable($courseId)
    {
        return $this->db
            ->where('DATE(CONCAT(creationYear, \'-08-31\')) > CURDATE()')
            ->where('idCourse', $courseId)
            ->get('Course')
            ->num_rows() > 0;
    }


    /**
     * Returns the course that are editable.
     *
     * @return array
     */
    public function getEditable()
    {
        //TODO LES DATEs A METTRE EN CONFIG
        return $this->db->select('idCourse, courseType, creationYear')
            ->from('Course')
            ->join('Semester', 'idCourse', 'left')
            ->where('DATE(CONCAT(creationYear, \'-08-31\')) > CURDATE()')
            ->group_by('idCourse')
            ->get()
            ->result();
    }

    /**
     * Returns the type of the course.
     *
     * @param $courseId
     * @return string
     */
    public function getType($courseId)
    {
        return $this->db
            ->select('courseType')
            ->from('Course')
            ->where('idCourse', $courseId)
            ->get()
            ->row()
            ->courseType;
    }

    /**
     * Returns the courses ordered by their type.
     *
     * @return array
     */
    public function getCourseTypes()
    {
        return $this->db
            ->order_by('courseType', 'ASC')
            ->order_by('creationYear', 'DESC')
            ->get('Course')
            ->result();
    }

    /**
     * Creates a course.
     *
     * @param string $date
     * @param string $type
     * @return bool
     */
    public function create($date, $type)
    {
        $data = array(
            'creationYear' => $date,
            'courseType' => $type
        );
        return $this->db->insert('Course', $data);
    }

    /**
     * Deletes a course.
     *
     * @param int $courseId
     * @return mixed
     */
    public function delete($courseId)
    {
        return $this->db->delete('Course', array('idCourse' => $courseId));
    }

    /**
     * Add a teaching unit to the course.
     *
     * @param int $teachingUnitId
     * @param int $courseId
     * @return mixed
     */
    public function linkTeachingUnit($teachingUnitId, $courseId)
    {
        $data = array(
            'idCourse' => $courseId,
            'idTeachingUnit' => $teachingUnitId
        );
        return $this->db->insert('TeachingUnitOfCourse', $data);
    }

    /**
     * Removes a teaching unit from the course.
     *
     * @param int $teachingUnitId
     * @param int $courseId
     * @return mixed
     */
    public function unlinkTeachingUnit($teachingUnitId, $courseId)
    {
        return $this->db->where('idTeachingUnit', $teachingUnitId)
            ->where('idCourse', $courseId)
            ->delete('TeachingUnitOfCourse');
    }

    /**
     * Returns the teachings that are in a course.
     *
     * @param int $courseId
     * @return array
     */
    public function getTeachingUnitsIn($courseId)
    {
        return $this->db
            ->from('TeachingUnit')
            ->join('TeachingUnitOfCourse', 'idTeachingUnit')
            ->where('idCourse', $courseId)
            ->order_by('idTeachingUnit')
            ->get()
            ->result();
    }

    /**
     * Returns the teaching units that are not in a course.
     *
     * @param int $courseId
     * @return array
     */
    public function getTeachingUnitsOut($courseId)
    {
        $sql =
            'SELECT idTeachingUnit, teachingUnitCode, teachingUnitName, creationYear
            FROM Teachingunit
            WHERE idTeachingUnit NOT IN (
              SELECT idTeachingUnit
              FROM TeachingUnitOfCourse
              WHERE idCourse = ?
            )
            GROUP BY idTeachingUnit
            ORDER BY creationYear DESC';

        return $this->db->query($sql, array($courseId))
            ->result();
    }

    /**
     * Return the course
     *
     * @param int $courseId
     * @return ArrayAccess
     */
    public function get($courseId){
        return $this->db
            ->from('Course')
            ->where('idCourse', $courseId)
            ->get()
            ->row();
    }

}
