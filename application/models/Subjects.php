<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subjects extends CI_Model
{

    /**
     * Returns the id of the semester that contains the subject.
     *
     * @param int $subjectId
     * @return int|bool FALSE If no semester contains the subject
     */
    public function getSemester($subjectId)
    {
        $res = $this->db
            ->select('idSemester')
            ->from('Subject')
            ->join('SubjectOfModule', 'idSubject')
            ->join('ModuleOfTeachingUnit', 'idModule')
            ->join('TeachingUnitOfCourse', 'idTeachingUnit')
            ->join('Course', 'idCourse')
            ->join('Semester', 'idCourse')
            ->where('idSubject', $subjectId)
            ->where('active', '1')
            ->get()
            ->row();

        if (empty($res)) {
            return FALSE;
        }
        return (int) $res->idSemester;
    }
}