<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Educations extends CI_Model
{

    public function setTeacher($subjectId, $groupId, $teacherId)
    {
        if (!$teacherId) {
            $teacherId = NULL;
        }

        if ($this->exist($subjectId, $groupId)) {
            $this->db
                ->set('idTeacher',$teacherId)
                ->where('idSubject', $subjectId)
                ->where('idGroup',$groupId)
                ->update('education');

        } else {
            $data = array(
                'idSubject' => $subjectId,
                'idTeacher' => $teacherId,
                'idGroup' => $groupId
            );
            $this->db->insert('education', $data);
        }
        return $this->db->affected_rows();
    }

    /**
     * Set the teacher of all the educations in a semester.
     * 
     * @param $subjectId
     * @param $semesterId
     * @param $teacherId
     * @return int
     */
    public function setAllTeacher($subjectId, $semesterId, $teacherId)
    {
        $this->load->model('Semesters');

        if (!$teacherId) {
            $teacher = NULL;
        }

        $groups = $this->Semesters->getGroups($semesterId);
        $affectedRows = 0;

        foreach ($groups as $group) {
            $affectedRows += $this->setTeacher($subjectId, $group->idGroup, $teacherId);
        }
        return $affectedRows;
    }

    public function exist($subjectId, $groupId)
    {
        return $this->db
            ->where('idSubject', $subjectId)
            ->where('idGroup',$groupId)
            ->get('education')
            ->num_rows() > 0;
    }



}
