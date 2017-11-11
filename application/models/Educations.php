<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Educations extends CI_Model
{


    public function create($subjectId, $groupId, $teacherId) {
        if ($teacherId == 0) {
            $teacherId = NULL;
        }

        if ($this->exist($subjectId, $groupId)) {
            $this->db
                ->set('idTeacher',$teacherId)
                ->where('idSubject', $subjectId)
                ->where('idGroup',$groupId)
                ->update('education');

        } else {
            $data = array(  'idSubject' => $subjectId,
                            'idTeacher' => $teacherId,
                            'idGroup' => $groupId);
            $this->db->insert('education', $data);
        }
        return $this->db->affected_rows();
    }

    public function exist($subjectId, $groupId) {
        return $this->db
            ->where('idSubject', $subjectId)
            ->where('idGroup',$groupId)
            ->get('education')
            ->num_rows() > 0;
    }



}
