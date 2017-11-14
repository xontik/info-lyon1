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
        return $this->db->select('idStudent, surname, name, email')
            ->where('idStudent', $studentId)
            ->get('Student')
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

    public function countQuestions($studentId)
    {
        return $this->db
            ->where('idStudent', $studentId)
            ->count_all_results('Question');
    }

    /**
     * Returns the questions asked by the student,
     * and the teacher to who it was told.
     *
     * @param int $studentId
     * @param int $currentPage
     * @param int $nbQuestionsPerPage
     * @return array
     */
    public function getQuestionsPerPage($studentId, $currentPage, $nbQuestionsPerPage)
    {
        return $this->db
            ->select(
                'idQuestion, title, content, questionDate, idStudent, idTeacher,'
                . 'CONCAT(name, \' \', surname) as name'
            )
            ->from('Question')
            ->join('Teacher', 'idTeacher')
            ->join('User', 'idUser')
            ->where('idStudent', $studentId)
            ->order_by('questionDate', 'DESC')
            ->limit($nbQuestionsPerPage, (($currentPage - 1) * $nbQuestionsPerPage))
            ->get()
            ->result();
    }

    /**
     * Get the page of a question.
     *
     * @param int $questionId
     * @param string $studentId
     * @param int $nbQuestionsPerPage
     * @return int
     */
    public function getPage($questionId, $studentId, $nbQuestionsPerPage)
    {
        $questions = $this->db
            ->select('idQuestion, title, content, questionDate, public, CONCAT(name, \' \', surname) as name')
            ->from('Question')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idStudent', $studentId)
            ->order_by('questionDate', 'DESC')
            ->get()
            ->result();

        $index = 0;
        foreach ($questions as $question) {
            $index++;
            if ($question->idQuestion === $questionId) {
                break;
            }
        }

        if ($index === 0) {
            return FALSE;
        }

        return floor(($index - 1)/$nbQuestionsPerPage);
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
