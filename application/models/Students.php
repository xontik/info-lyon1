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
     * Get all students unsorted.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db
            ->from('Student')
            ->join('User', 'idUser')
            ->get()
            ->result();
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
     * Get the group of the student.
     *
     * @param $studentId
     * @return object|bool FALSE if student has currently no group.
     */
    public function getGroup($studentId)
    {
        $res = $this->db->select('idGroup, groupName')
            ->from('StudentGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->where('idStudent', $studentId)
            ->where('active', '1')
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Returns the last absence of the student in a period.
     *
     * @param string    $studentId
     * @param Period    $period
     * @return object|bool FALSE if student has no absence.
     */
    public function getLastAbsence($studentId, $period)
    {
        $this->load->model('Semesters');

        $res = $this->db
            ->select('beginDate, endDate, absenceTypeName')
            ->from('Absence')
            ->join('AbsenceType', 'idAbsenceType')
            ->where('idStudent', $studentId)
            ->where('beginDate BETWEEN \'' . $period->getBeginDate()->format('Y-m-d')
                . '\' AND \'' . $period->getEndDate()->format('Y-m-d') . '\'')
            ->order_by('beginDate', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    public function getAbsencesCount($studentId, $period) {
        $justified = $this->db
            ->from('absence')
            ->where('idStudent', $studentId)
            ->where('beginDate BETWEEN "' . $period->getBeginDate()->format('Y-m-d')
                . '" AND "' . $period->getEndDate()->format('Y-m-d') . '"')
            ->where('justified','1')
            ->get()
            ->num_rows();
        $unjustified = $this->db
            ->from('absence')
            ->where('idStudent', $studentId)
            ->where('beginDate BETWEEN "' . $period->getBeginDate()->format('Y-m-d')
                . '" AND "' . $period->getEndDate()->format('Y-m-d') . '"')
            ->where('justified','0')
            ->get()
            ->num_rows();

        return array('justified' => $justified, 'unjustified' => $unjustified);

    }

    /**
     * Return the last mark the student got, with its control.
     *
     * @param string    $studentId
     * @param int       $semesterId
     * @return object|bool FALSE if student has no mark
     */
    public function getLastMark($studentId, $semesterId)
    {
        $res = $this->db
            ->query(
                'SELECT *
                FROM (
                        SELECT value, controlName, coefficient, divisor, controlDate
                        FROM Mark
                        JOIN Control USING (idControl)
                        JOIN Education USING (idEducation)
                        JOIN `Group` USING (idGroup) 
                        WHERE idStudent = \'p0000001\'
                        AND idSemester = 2
                    UNION
                        SELECT value, controlName, coefficient, divisor, controlDate
                        FROM Mark
                        JOIN Control USING (idControl)
                        JOIN Promo USING (idPromo)
                        WHERE idStudent = \'p0000001\'
                        AND idSemester = 2
                ) AS foo
                ORDER BY controlDate DESC
                LIMIT 1',
                array($studentId, $semesterId)
            )
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    public function getSubjectsAverage($studentId, $semesterId) {
        $sql = 'SELECT idSubject, subjectCode, subjectName, subjectCoefficient, moduleName, idTeachingUnit, teachingUnitName, teachingUnitCode, idSemester,
                        ROUND(SUM((value/divisor)*20*coefficient)/SUM(coefficient), 2) AS average,
                        ROUND(SUM(average*coefficient)/SUM(coefficient), 2) AS groupAverage
                FROM (
                SELECT idSubject, idControl, idStudent, idSemester FROM mark
                    JOIN control using (idControl)
                    JOIN education USING(idEducation)
                    JOIN `group` USING(idGroup)
                    JOIN studentgroup USING(idStudent,idGroup)
                    where idStudent = ? && idSemester = ?
                UNION
                SELECT idSubject, idControl, idStudent, idSemester  FROM mark
                    JOIN control using (idControl)
                    JOIN promo USING(idPromo)
                    JOIN education USING(idSubject)
                    JOIN `group` USING(idGroup, idSemester)
                    JOIN studentgroup USING(idStudent)
                    where idStudent = ? && idSemester = ?) AS c
                JOIN subject USING(idSubject)
                JOIN mark USING(idControl, idStudent)
                JOIN control USING(idControl)
                JOIN subjectofmodule USING(idSubject)
                JOIN moduleofteachingunit USING(idModule)
                JOIN module USING(idModule)
                JOIN teachingunit USING (idTeachingunit)
                GROUP BY idSubject, idSemester
                ORDER BY idTeachingunit';

        return $this->db->query($sql, array($studentId, $semesterId, $studentId, $semesterId))->result();
    }

    public function getSubjectsTUAverage($studentId, $semesterId) {
        $sql = 'SELECT idTeachingUnit, teachingUnitName, teachingUnitCode,
                        ROUND(SUM((value/divisor)*20*coefficient)/SUM(coefficient), 2) AS average,
                        ROUND(SUM(average*coefficient)/SUM(coefficient), 2) AS groupAverage,
                        SUM(subjectCoefficient) as coefficient

                FROM (
                SELECT idSubject, idControl, idStudent, idSemester FROM mark
                    JOIN control using (idControl)
                    JOIN education USING(idEducation)
                    JOIN `group` USING(idGroup)
                    JOIN studentgroup USING(idStudent,idGroup)
                    where idStudent = ? && idSemester = ?
                UNION
                SELECT idSubject, idControl, idStudent, idSemester  FROM mark
                    JOIN control using (idControl)
                    JOIN promo USING(idPromo)
                    JOIN education USING(idSubject)
                    JOIN `group` USING(idGroup, idSemester)
                    JOIN studentgroup USING(idStudent)
                    where idStudent = ? && idSemester = ?) AS c
                JOIN subject USING(idSubject)
                JOIN mark USING(idControl, idStudent)
                JOIN control USING(idControl)
                JOIN subjectofmodule USING(idSubject)
                JOIN moduleofteachingunit USING(idModule)
                JOIN module USING(idModule)
                JOIN teachingunit USING (idTeachingunit)
                GROUP BY idTeachingunit
                ORDER BY idTeachingunit';

        return $this->db->query($sql, array($studentId, $semesterId, $studentId, $semesterId))->result();
    }

    /**
     * Returns the last answer, with its question.
     *
     * @param string    $studentId
     * @param Period    $period
     * @return object|bool FALSE
     */
    public function getLastAnswer($studentId, $period) {
        $res = $this->db
            ->select(
                'idAnswer, Answer.content as answerContent, answerDate,'
                . 'idQuestion, title, Question.content as questionContent, questionDate'
            )
            ->from('Answer')
            ->join('Question', 'idQuestion')
            ->join('Teacher', 'idTeacher')
            ->where('idStudent', $studentId)
            ->where('answerDate BETWEEN \'' . $period->getBeginDate()->format('Y-m-d')
                . '\' AND \'' . $period->getEndDate()->format('Y-m-d') . '\'')
            ->order_by('answerDate', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
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

    /**
     * Computes in which semester is a student.
     *
     * @param string $studentId
     * @return int
     */
    public function getCurrentSemester($studentId)
    {
        $semester = $this->db
            ->from('StudentGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->join('Course', 'idCourse')
            ->where('active', '1')
            ->where('idStudent', $studentId)
            ->order_by('idSemester', 'DESC')
            ->get()
            ->row();

        if (empty($semester)) {
            return FALSE;
        }
        return $semester;
    }

    public function getSemesters($studentId) {
        return $this->db
            ->from('StudentGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->join('Course', 'idCourse')
            ->where('idStudent', $studentId)
            ->order_by('schoolYear', 'DESC')
            ->order_by('idSemester', 'DESC')
            ->get()
            ->result();
    }

}
