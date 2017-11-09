<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends CI_Model
{

    /**
     * Gets a student.
     *
     * @param string $studentId
     * @return array
     */
    public function get($studentId)
    {
        return $this->db
            ->from('Student')
            ->select('idStudent, surname, name, email')
            ->join('User','idUser')
            ->where('idStudent', $studentId)
            ->get()
            ->row();
    }

    /**
     * Get all students in active semester,
     * ordered by course, group and name.
     *
     * @return array
     */
    public function getAllOrganized()
    {
        //TODO Check results
        return $this->db
            ->select('idStudent,'
                . 'CONCAT(name, \' \', surname) as name,'
                . 'CONCAT(groupName, courseType) as groupName'
            )
            ->from('Student')
            ->join('User', 'idUser')
            ->join('StudentGroup', 'idStudent')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->join('Course', 'idCourse')
            ->where('active', '1')
            ->order_by('courseType', 'ASC')
            ->order_by('idGroup', 'ASC')
            ->order_by('surname', 'ASC')
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Return the project to which the student currently or most lastly belongs.
     *
     * @param string $studentId
     * @return object|bool FALSE if student has no project.
     */
    public function getProject($studentId)
    {
        $res = $this->db
            ->from('ProjectMember')
            ->join('Project', 'idProject')
            ->where('idStudent', $studentId)
            ->order_by('idProject', 'DESC')
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Returns the questions asked by the student,
     * and the teacher to who they were asked.
     *
     * @param string $studentId
     * @return array
     */
    public function getQuestions($studentId)
    {
        return $this->db
            ->select(
                'idQuestion, title, content, questionDate, idStudent, idTeacher,'
                . 'CONCAT(name, \' \', surname) as teacherName'
            )
            ->from('Question')
            ->join('Teacher', 'idTeacher')
            ->join('User', 'idUser')
            ->where('idStudent', $studentId)
            ->order_by('questionDate', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Returns the answers to the questions the student asked.
     *
     * @param $studentId
     * @return array
     */
    public function getAnswers($studentId) {
        return $this->db
            ->select(
                'idQuestion, idAnswer, Answer.content, teacher,'
                . 'CONCAT(name, \' \', surname) as studentName'
            )
            ->from('Answer')
            ->join('Question', 'idQuestion')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idStudent', $studentId)
            ->order_by('Question.questionDate', 'DESC')
            ->order_by('Answer.answerDate', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Gets the teachers of a student.
     *
     * @param string $studentId
     * @return array
     */
    public function getTeachers($studentId)
    {
        $groupId = $this->db
            ->select('idGroup')
            ->from('StudentGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->where('idStudent', $studentId)
            ->where('active', '1')
            ->get_compiled_select();

        return $this->db->select('idTeacher, CONCAT(name, \' \', surname) as name')
            ->from('Teacher')
            ->join('User', 'idUser')
            ->join('Education', 'idTeacher')
            ->join('Group', 'idGroup')
            ->where('idGroup = (' . $groupId . ')')
            ->order_by('surname', 'ASC')
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Gets the resource for the timetable.
     *
     * @param string $studentId
     * @return int|bool FALSE if no ressource is associated
     */
    public function getADEResource($studentId)
    {
        $res = $this->db
            ->select('resource')
            ->from('GroupTimetable')
            ->join('StudentGroup', 'idGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->where('idStudent', $studentId)
            ->where('active', '1')
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return (int) $res->resource;
    }
}
