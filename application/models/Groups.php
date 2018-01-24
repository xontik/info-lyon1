<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends CI_Model
{
    /**
     * Get the appointments of a group.
     *
     * @param int $groupId
     * @return object
     */
    public function get($groupId)
    {
        return $this->db
            ->from('Group')
            ->join('Semester', 'idSemester')
            ->join('Courses', 'idCourse')
            ->where('idGroup', $groupId)
            ->get()
            ->row();
    }

    /**
     * Get the group id from it's name.
     *
     * @param string $name
     * @return int
     */
    public function getIdFromName($name)
    {
        $this->load->model('Semesters');
        $semesterIndex = strpos($name, 'S');

        $semesterName = substr($name, $semesterIndex);
        $groupName = substr($name, 0, $semesterIndex);

        $semesterId = $this->Semesters->getIdFromName($semesterName);

        $res = $this->db
            ->select('idGroup')
            ->from('Group')
            ->where('idSemester', $semesterId)
            ->where('groupName', $groupName)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return (int) $res->idGroup;
    }

    /**
     * Returns all groups in database, and the linked students.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db
            ->select(
                'idGroup,
                CONCAT(groupName, courseType) as groupName,
                CONCAT(schoolYear, \'-\', schoolYear+1) as schoolYear,
                idStudent, name, surname'
            )
            ->from('Group')
            ->join('StudentGroup', 'idGroup')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->join('Semester', 'idSemester')
            ->join('Course', 'idCourse')
            ->get()
            ->result();
    }

    /**
     * Checks if a group name already exists in a semester.
     *
     * @param int $semesterId
     * @param string $groupName
     * @return bool
     */
    public function exists($semesterId, $groupName)
    {
        return $this->db
                ->where('idSemester', $semesterId)
                ->where('groupName', $groupName)
                ->get('Group')
                ->num_rows() > 0;
    }

    /**
     * Checks if a group is editable.
     *
     * @param int $groupId
     * @return bool
     */
    public function isEditable($groupId)
    {
        $this->load->model('Semesters');

        $group = $this->db
            ->select('idSemester')
            ->where('idGroup', $groupId)
            ->get('Group')
            ->row();

        if (is_null($group)) {
            return false;
        }
        return $this->Semesters->isEditable($group->idSemester);
    }

    /**
     * Get the students that are in a group.
     *
     * @param $idGroup
     * @return array
     */
    public function getStudents($idGroup)
    {
        return $this->db
            ->select('idStudent, surname, name, idGroup, groupName')
            ->from('StudentGroup')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->join('Group', 'idGroup')
            ->where('idGroup', $idGroup)
            ->get()
            ->result();
    }

    /**
     * Add a student to a group.
     *
     * @param $studentId
     * @param $groupId
     * @return bool
     */
    public function addStudent($studentId, $groupId)
    {
        $data = array(
            'idStudent' => $studentId,
            'idGroup' => $groupId
        );
        return $this->db->insert('StudentGroup', $data);
    }

    /**
     * Checks if a student is an a group.
     *
     * @param string $studentId
     * @param int $groupId
     * @return bool
     */
    public function hasStudent($studentId, $groupId)
    {
        return $this->db->where('idStudent', $studentId)
                ->where('idGroup', $groupId)
                ->get('StudentGroup')
                ->num_rows() > 0;
    }

    /**
     * Remove a student from a group.
     *
     * @param $studentId
     * @param $groupId
     * @return bool
     */
    public function removeStudent($studentId, $groupId)
    {


        $this->db->where('idGroup', $groupId)
            ->where('idStudent', $studentId)
            ->delete('StudentGroup');

        return $this->db->affected_rows();
    }

    /**
     * Remove all students from a group.
     *
     * @param $groupId
     * @return bool
     */
    public function removeAllStudents($groupId)
    {
        $this->db->delete('StudentGroup', array('idGroup' => $groupId));
        return $this->db->affected_rows();

    }

    /**
     * Creates a group.
     *
     * @param int $semesterId
     * @return bool
     */
    public function create($semesterId)
    {
        //TODO add all aduction related to this group and semester
        $groups = array_column($this->db
                    ->from('group')
                    ->select('groupName')
                    ->where('idSemester',$semesterId)
                    ->get()
                    ->result_array(),'groupName');
        $delayed = $this->db
                        ->select('delayed')
                        ->from('semester')
                        ->where('idSemester',$semesterId)
                        ->get()
                        ->row()->delayed;
        if($delayed) {
            $i = 6;
        } else {
            $i = 1;
        }

        while(in_array('G'.$i,$groups)) {
            $i++;
        }
        $newName = 'G'.$i;

        $data = array(
            'idSemester' => $semesterId,
            'groupName' => $newName
        );
        return $this->db->insert('Group', $data) ? $newName : false;

    }

    /**
     * Deletes a group.
     *
     * @param int $groupId
     * @return bool
     */
    public function delete($groupId)
    {
        $this->db->delete('Group', array('idGroup' => $groupId));
        return $this->db->affected_rows();
        
    }
}
