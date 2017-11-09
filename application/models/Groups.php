<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends CI_Model
{
    /**
     * Get the details of a group.
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
            ->from('StudentGroup')
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
        return $this->db->where('idGroup', $groupId)
            ->where('idStudent', $studentId)
            ->delete('StudentGroup');
    }

    /**
     * Remove all students from a group.
     *
     * @param $groupId
     * @return bool
     */
    public function removeAllStudents($groupId)
    {
        return $this->db->delete('StudentGroup', array('idGroup' => $groupId));
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
        $i = 1;
        //TODO si differe start G6
        while(in_array('G'.$i,$groups)){
            $i++;
        }
        $newName = 'G'.$i;

        $data = array(
            'idSemester' => $semesterId,
            'groupName' => $newName
        );
        return $this->db->insert('Group', $data);

    }

    /**
     * Deletes a group.
     *
     * @param int $groupId
     * @return bool
     */
    public function delete($groupId)
    {
        return $this->db->delete('Group', array('idGroup' => $groupId));
    }
}
