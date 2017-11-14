<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teachers extends CI_Model
{

    /**
     * Get details about a teacher.
     *
     * @param int $teacherId
     * @return object
     */
    public function get($teacherId)
    {
        return $this->db->select('name, surname, email')
            ->from('Teacher')
            ->where('idTeacher', $teacherId)
            ->get()
            ->row();
    }

    /**
     * Get the controls a teacher has rights on.
     *
     * @param int $teacherId
     * @return array
     */
    public function getControls($teacherId)
    {
        $sql =
            'SELECT foo.subjectCode, foo.idSubject, foo.subjectName, foo.idControl, foo.controlName,
            foo.coefficient, foo.divisor, foo.controlTypeName, foo.idControlType, foo.median, foo.average,
            foo.controlDate, foo.subjectCoefficient, foo.groupName, foo.idGroup
            FROM (
                    SELECT subjectCode, idSubject, subjectName, idControl, controlName,
                    coefficient, divisor, idControlType, controlTypeName, median, average,
                    controlDate,subjectCoefficient,groupName,idGroup
                    FROM Control
                    JOIN ControlType USING (idControlType)
                    JOIN Education USING (idEducation)
                    JOIN Subject USING (idSubject)
                    JOIN `Group` USING (idGroup)
                    JOIN Semester USING (idSemester)
                    WHERE idTeacher = ? AND active = 1
                UNION
                    SELECT DISTINCT subjectCode, idSubject, subjectName, idControl, controlName,
                    coefficient, divisor, idControlType, controlTypeName, median, average,
                    controlDate, subjectCoefficient, NULL AS groupName, NULL AS idGroup
                    FROM Control
                    JOIN ControlType USING (idControlType)
                    JOIN Promo USING (idPromo)
                    JOIN Subject USING (idSubject)
                    JOIN Education USING (idSubject)
                    JOIN Semester USING (idSemester)
                    WHERE idTeacher = ? AND active = 1
                UNION
                    SELECT subjectCode, idSubject, subjectName, idControl, controlName,
                    coefficient, divisor, idControlType, controlTypeName, median, average,
                    controlDate, subjectCoefficient, groupName, idGroup
                    FROM Control
                    JOIN ControlType USING (idControlType)
                    JOIN Education USING (idEducation)
                    JOIN Subject USING (idSubject)
                    JOIN `Group` USING (idGroup)
                    JOIN SubjectOfModule USING (idSubject)
                    JOIN Referent USING (idModule, idSemester)
                    JOIN Semester USING (idSemester)
                    WHERE Referent.idTeacher = ? AND active = 1
                UNION
                    SELECT subjectCode, idSubject, subjectName, idControl, controlName,
                    Control.coefficient, divisor, idControlType, controlTypeName, median, average,
                    controlDate, subjectCoefficient, NULL AS groupName, NULL AS idGroup
                    FROM Control
                    JOIN ControlType USING (idControlType)
                    JOIN Promo USING (idPromo)
                    JOIN Subject USING (idSubject)
                    JOIN SubjectOfModule USING (idSubject)
                    JOIN Module USING (idModule)
                    JOIN Referent USING (idModule,idSemester)
                    JOIN Semester USING (idSemester)
                    WHERE idTeacher = ? AND active = 1
            ) AS foo ';

        //TODO continuer de verifier les different cas pour les ds surtout via Referent
        return $this->db->query($sql, array_fill(0, 4, $teacherId))
            ->result();
    }

    /**
     * Checks if the teacher has right on the control.
     *
     * @param int $controlId
     * @param int $teacherId
     * @return bool
     */
    public function hasRightOn($controlId, $teacherId)
    {
        $ids = array_merge(
            $this->_getControlIdsAsTeacher($teacherId),

            //TODO Remplacer par $this->isReferent ?
            $this->_getControlIdsAsReferent($teacherId)
        );

        foreach ($ids as $id) {
            if ($id->idControl == $controlId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $teacherId
     * @return array
     */
    private function _getControlIdsAsTeacher($teacherId)
    {
        $sql =
            'SELECT idControl
                FROM Control
                JOIN Education USING (idEducation)
                JOIN `Group` USING (idGroup)
                JOIN Semester USING (idSemester)
                WHERE idTeacher = ? AND active = 1
            UNION
                SELECT idControl
                FROM Control
                JOIN Promo USING (idPromo)
                JOIN Semester USING (idSemester)
                JOIN Education USING (idSubject)
                WHERE idTeacher = ? AND active = 1';

        return $this->db->query($sql, array_fill(0, 2, $teacherId))
            ->result();
    }

    /**
     * @param int $teacherId
     * @return array
     */
    private function _getControlIdsAsReferent($teacherId)
    {
        $sql =
            'SELECT idControl
                FROM Referent
                JOIN SubjectOfModule USING (idModule)
                JOIN Education USING (idSubject)
                JOIN Semester USING (idSemester)
                JOIN Control USING (idEducation)
                WHERE Referent.idTeacher = ? AND active = 1
            UNION
                SELECT idControl
                FROM Referent
                JOIN SubjectOfModule USING (idModule)
                JOIN Promo USING (idSubject,idSemester)
                JOIN Semester USING (idSemester)
                JOIN Control USING (idPromo)
                WHERE Referent.idTeacher = ? AND active = 1';

        return $this->db->query($sql, array_fill(0, 2, $teacherId))
            ->result();
    }

    /**
     * Checks if the teacher is referent on the control.
     *
     * @param int $controlId
     * @param int $teacherId
     * @return bool
     */
    public function isReferent($controlId, $teacherId)
    {
        return $this->db
                ->select('COUNT(*) AS count')
                ->from('Referent')
                ->join('SubjectOfModule', 'idModule')
                ->join('Promo', 'idSubject', 'left')
                ->join('Control', 'idPromo')
                ->where('idControl', $controlId)
                ->where('idTeacher', $teacherId)
                ->get()
                ->row()
                ->count > 0;
    }

    /**
     * Checks if teacher if tutor of a project.
     *
     * @param int $projectId
     * @param int $teacherId
     * @return bool
     */
    public function isTutor($projectId, $teacherId)
    {
        return $this->db->where('idProject', $projectId)
            ->where('idTeacher', $teacherId)
            ->get('Project')
            ->num_rows() > 0;
    }

    /**
     * Get the subjects the teacher teaches.
     *
     * @param int $teacherId
     * @return array
     */
    public function getSubjects($teacherId)
    {
        $sql =
            'SELECT DISTINCT idSubject, subjectCode, subjectName
                FROM Education
                JOIN `Group` USING (idGroup)
                JOIN Subject USING (idSubject)
                JOIN Semester USING (idSemester)
                WHERE idTeacher = ? AND active = 1
            UNION
                SELECT DISTINCT idSubject,subjectCode,subjectName FROM Referent
                JOIN SubjectOfModule USING ( idModule)
                JOIN Subject USING (idSubject)
                JOIN Semester USING (idSemester)
                WHERE idTeacher = ? AND active = 1';

        return $this->db->query($sql, array($teacherId, $teacherId))
            ->result();
    }

    /**
     * Return the groups of the teacher.
     *
     * @param int $teacherId
     * @return array
     */
    public function getGroups($teacherId)
    {
        $sql =
            'SELECT DISTINCT * FROM (
                (
                    SELECT groupName, courseType, idGroup
                    FROM Education
                    JOIN `Group` USING (idGroup)
                    JOIN Semester USING (idSemester)
                    JOIN Course USING (idCourse)
                    WHERE idTeacher = ? AND active = 1
                )
                UNION
                (
                    SELECT groupName, courseType, idGroup
                    FROM Referent
                    JOIN SubjectOfModule USING (idModule)
                    JOIN Education USING (idSubject)
                    JOIN `Group` USING (idGroup,idSemester)
                    JOIN Semester USING (idSemester)
                    JOIN Course USING (idCourse)
                    WHERE Referent.idTeacher = ? AND active = 1
                )
            ) AS foo
            ORDER BY courseType';

        return $this->db->query($sql, array_fill(0, 2, $teacherId))
            ->result();
    }

    /**
     * Returns the educations of the teacher.
     *
     * @param int $teacherId
     * @return array
     */
    public function getEducations($teacherId)
    {
        $sql =
            'SELECT DISTINCT * FROM (
                    SELECT groupName, subjectName, idEducation
                    FROM Education
                    JOIN `Group` USING (idGroup)
                    JOIN Subject USING (idSubject)
                    JOIN Semester USING (idSemester)
                    WHERE idTeacher = ? AND active = 1
                UNION
                    SELECT groupName, subjectName, idEducation
                    FROM Referent
                    JOIN SubjectOfModule USING (idModule)
                    JOIN Subject USING (idSubject)
                    JOIN Education USING (idSubject)
                    JOIN Semester USING (idSemester)
                    JOIN `Group` USING (idGroup, idSemester)
                    WHERE Referent.idTeacher = ?
                    AND active = 1
            ) AS foo ';

        return $this->db->query($sql, array_fill(0, 2, $teacherId))
            ->result();
    }

    /**
     * Checks if teacher as access to the education.
     *
     * @param int $educationId
     * @param int $teacherId
     * @return bool
     */
    public function hasEducation($educationId, $teacherId)
    {
        $sql =
            'SELECT idEducation
                FROM Education
                JOIN `Group` USING (idGroup)
                JOIN Semester USING (idSemester)
                WHERE  idEducation = ? AND active = 1 AND idTeacher = ?
            UNION
                SELECT idEducation
                FROM Referent
                JOIN SubjectOfModule USING (idModule)
                JOIN Education USING (idSubject)
                JOIN Semester USING (idSemester)
                WHERE  idEducation = ?
                AND Referent.idTeacher = ?
                AND active = 1';

        return $this->db->query($sql, array($educationId, $teacherId, $educationId, $teacherId))
                ->num_rows() > 0;
    }

    /**
     * Return the projects where the teacher is referent.
     *
     * @param int $teacherId
     * @return array
     */
    public function getProjects($teacherId)
    {
        return $this->db
            ->select('idProject, projectName')
            ->from('Project')
            ->where('idTeacher', $teacherId)
            ->get()
            ->result();
    }

    public function countQuestions($teacherId, $search = '')
    {
        return $this->db
            ->select('idQuestion, title, content, questionDate, public, CONCAT(name, \' \', surname) as name')
            ->from('Question')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idTeacher', $teacherId)
            ->like('title', $search, 'both')
            ->or_like('name', $search, 'both')
            ->or_like('content', $search, 'both')
            ->or_like('questionDate', $search, 'both')    
            ->count_all_results();
    }

    /**
     * Returns the questions addressed to the teacher,
     * and the student that asked it.
     *
     * @param int $teacherId
     * @param int $currentPage
     * @param int $nbQuestionsPerPage
     * @return array
     */
    public function getQuestionsPerPage($teacherId, $currentPage, $nbQuestionsPerPage, $search = '')
    {
        return $this->db
            ->select('idQuestion, title, content, questionDate, public, CONCAT(name, \' \', surname) as name')
            ->from('Question')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idTeacher', $teacherId)
            ->like('title', $search, 'both')
            ->or_like('name', $search, 'both')
            ->or_like('content', $search, 'both')  
            ->order_by('questionDate', 'DESC')
            ->limit($nbQuestionsPerPage, (($currentPage - 1) * $nbQuestionsPerPage))
            ->get()
            ->result();
    }

    /**
     * Get the page of a question.
     *
     * @param int $questionId
     * @param string $teacherId
     * @param int $nbQuestionsPerPage
     * @return int
     */
    public function getPage($questionId, $teacherId, $nbQuestionsPerPage)
    {
        $questions = $this->db
            ->select('idQuestion')
            ->from('Question')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idTeacher', $teacherId)
            ->order_by('questionDate', 'DESC')
            ->get()
            ->result_array();

        $questionsId = array_column($questions, 'idQuestion');
        $index = array_search($questionId, $questionsId);
        if ($index === FALSE) {
            return FALSE;
        }

        return ceil(($index+1)/$nbQuestionsPerPage);
    }

    /**
     * Returns the answers to the questions the teacher was asked.
     *
     * @param $teacherId
     * @return array
     */
    public function getAnswers($teacherId)
    {
        return $this->db
            ->select('idQuestion, idAnswer, Answer.content, teacher')
            ->from('Answer')
            ->join('Question', 'idQuestion')
            ->where('idTeacher', $teacherId)
            ->order_by('Question.questionDate', 'DESC')
            ->order_by('Answer.answerDate', 'DESC')
            ->get()
            ->result();
    }


    /**
     * Gets the resource for the timetable.
     *
     * @param int $teacherId
     * @return int|bool FALSE if no ressource is associated
     */
    public function getADEresource($teacherId)
    {
        $res = $this->db
            ->select('resource')
            ->from('TeacherTimetable')
            ->where('idTeacher', $teacherId)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return (int) $res->resource;
    }

}
