<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Semesters extends CI_Model
{

    /**
     * Get appointments about a semester.
     *
     * @param int $semesterId
     * @return object
     */
    public function get($semesterId)
    {
        return $this->db->from('Semester')
            ->join('Course', 'idCourse')
            ->where('idSemester', $semesterId)
            ->get()
            ->row();
    }

    /**
     * Get semester id from its name.
     *
     * @param string $name
     * @return int
     */
    public function getIdFromName($name)
    {
        $res = $this->db
            ->select('idSemester')
            ->from('Semester')
            ->join('Course', 'idCourse')
            ->where('active', 1)
            ->where('courseType', $name)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return (int) $res->idSemester;
    }

    /**
     * Checks if a semester exists.
     *
     * @param int $courseId
     * @param bool $delayed
     * @param int $schoolYear
     * @return bool
     */
    public function exists($courseId, $delayed, $schoolYear)
    {
        return $this->db
                ->where('idCourse', $courseId)
                ->where('delayed', $delayed)
                ->where('schoolYear', $schoolYear)
                ->get('Semester')
                ->num_rows() > 0;
    }

    /**
     * Checks if a semester is editable.
     *
     * @param int $semesterId
     * @return bool
     */
    public function isEditable($semesterId)
    {
        $period = $this->getPeriod($semesterId);
        if ($period === FALSE) {
            return FALSE;
        }

        $now = new DateTime();
        return !$now->diff($period->getEndDate())->invert;
    }

    /**
     * Checks if a semester is deletable.
     *
     * @param int $semesterId
     * @return bool
     */
    public function isDeletable($semesterId)
    {
        $period = $this->getPeriod($semesterId);
        if ($period === FALSE) {
            return false;
        }

        $now = new DateTime();
        return $now < $period->getBeginDate();
    }

    /**
     * Gets all semesters
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db
            ->from('Semester')
            ->join('Course', 'idCourse')
            ->join('Group', 'idSemester', 'left')
            ->order_by('idSemester', 'DESC')
            ->order_by('schoolYear', 'DESC')
            ->order_by('groupName', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Return the semester corresponding to the string passed in parameter.
     *
     * @param string $semester Can be empty or S1-4
     * @param string $studentId
     * @return int|bool FALSE if $semester is not a correct value
     */
    public function getSemesterId($semester, $studentId)
    {
        $semesterId = FALSE;
        if ($semester === '') {
            $this->load->model('Students');
            $semesterId = $this->Students->getCurrentSemester($studentId)->idSemester;
        } else if (preg_match('/^S[1-4]$/', $semester)) {
            $semesterId = $this->getLastSemesterOfType($semester, $studentId);
        }

        if ($semesterId === FALSE || $semesterId === 0) {
            return FALSE;
        }
        return (int) $semesterId;
    }

    /**
     * Returns the id of the student's `type` semester
     *
     * @param string $semesterType
     * @param string $studentId
     * @return int|bool FALSE if there's no semester.
     */
    public function getLastSemesterOfType($semesterType, $studentId)
    {
        if (!preg_match('/^S[1-4]$/', $semesterType)) {
            return FALSE;
        }

        $compatibleSemesters = $this->db->select('idSemester')
            ->from('Semester')
            ->join('Course', 'idCourse')
            ->where('courseType', $semesterType)
            ->get_compiled_select();
        
        $semester = $this->db->select_max('idSemester')
            ->from('Group')
            ->join('StudentGroup', 'idGroup')
            ->where('idStudent', $studentId)
            ->where('idSemester IN (' . $compatibleSemesters . ')')
            ->get()
            ->row();

        if (empty($semester)) {
            return FALSE;
        }
        return (int) $semester->idSemester;
    }

    /**
     * Get the type of a semester.
     *
     * @param int $semesterId
     * @return string S[1-4]
     */
    public function getType($semesterId)
    {
        $semesterType = $this->db->select('courseType')
            ->from('Semester')
            ->join('Course', 'idCourse')
            ->where('idSemester', $semesterId)
            ->get()
            ->row();

        if (empty($semesterType)) {
            return FALSE;
        }
        return $semesterType->courseType;
    }

    /**
     * Returns the active semester with the highest id.
     *
     * @return int
     */
    public function getLastActiveSemesterId()
    {
        $semester = $this->db->select('idSemester')
            ->from('StudentGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->where('active', '1')
            ->order_by('idSemester', 'DESC')
            ->get()
            ->row();

        if (empty($semester)) {
            return FALSE;
        }
        return $semester->idSemester;
    }

    /**
     * Computes the period of the current semester.
     *
     * @return Period|bool FALSE if there is no current semester
     */
    public function getCurrentPeriod()
    {
        $semesterId = $this->getLastActiveSemesterId();

        if ($semesterId === FALSE) {
            return FALSE;
        }
        return $this->getPeriod($semesterId);
    }

    /**
     * Get semesters that are at the same time.
     *
     * @param int $semesterId
     * @param bool $strict <i>true</i> if $semesterId must be excluded
     * @return array
     */
    public function getConcurrent($semesterId, $strict = true)
    {
        $semesters = $this->getAll();
        $beginDate = $this->getPeriod($semesterId)->getBeginDate();
        $outSem = array();

        foreach ($semesters as $semester) {
            if ($beginDate->diff($this->getPeriodObject($semester)->getBeginDate(), true)->days === 0
                && !in_array($semester->idSemester, $outSem)
                && (!$strict || $semester->idSemester !== $semesterId)
            ) {
                $outSem[] = $semester->idSemester;
            }
        }

        return $outSem;
    }


    /**
     * Gets the period of a semester.
     *
     * @param int $semesterId
     * @return Period|bool FALSE if the semester doesn't exists
     */
    public function getPeriod($semesterId)
    {
        $semester = $this->db->select('courseType, schoolYear, delayed')
            ->from('Semester')
            ->join('Course', 'idCourse')
            ->where('idSemester', $semesterId)
            ->get()
            ->row();

        return $this->getPeriodObject($semester);

    }

    /**
     * Computes a Period from the fields of a semester.
     *
     * @param object $semester
     * @return Period|bool FALSE if semester if empty
     */
    public function getPeriodObject($semester)
    {
        require_once(APPPATH . 'libraries/Period.php');

        if (empty($semester)) {
            return FALSE;
        }

        if ((($semester->courseType === 'S1' || $semester->courseType === 'S3') && !$semester->delayed)
            || (($semester->courseType === 'S2' || $semester->courseType === 'S4') && $semester->delayed)
        ) {
            return new Period(
                new DateTime($semester->schoolYear . '-09-01'),
                new DateTime((((int) $semester->schoolYear) + 1) . '-01-31')
            );
        } else {
            return new Period(
                new DateTime(($semester->schoolYear + 1) . '-02-01'),
                new DateTime(($semester->schoolYear + 1) . '-08-31')
            );
        }
    }

    /**
     * Get all the absences students during a semester
     *
     * @param int $semesterId
     * @return array
     */
    public function getAbsences($semesterId)
    {
        $CI =& get_instance();
        $CI->load->model('Absences');

        $period = $this->getPeriod($semesterId);
        if ($period === FALSE) {
            return array();
        }

        return $CI->Absences->getInPeriod($period);
    }

    /**
     * Checks if a student is in a semester.
     *
     * @param string $studentId
     * @param array $semesterId
     * @return bool
     */
    public function hasStudent($studentId, $semesterId)
    {
        return $this->db
                ->from('Semester')
                ->join('Group', 'idSemester')
                ->join('StudentGroup', 'idGroup')
                ->where('idStudent', $studentId)
                ->where('idSemester', $semesterId)
                ->get()
                ->num_rows() > 0;
    }

    /**
     * Return the semester that contains the student.
     *
     * @param string $studentId
     * @param array $semesterIds
     * @return object|bool FALSE if student is not in any of the semesters
     */
    public function anyHasStudent($studentId, $semesterIds)
    {
        if (empty($semesterIds)) {
            return FALSE;
        }

        $row = $this->db
            ->from('StudentGroup')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->join('Course', 'idCourse')
            ->where('idStudent', $studentId)
            ->where_in('idSemester', $semesterIds)
            ->get()
            ->row();

        if (empty($row)) {
            return false;
        }
        return $row;
    }

    /**
     * Returns the students that are in a semester
     *
     * @param int $semesterId
     * @return array The students
     */
    public function getStudents($semesterId)
    {
        return $this->db
            ->select('idStudent, surname, name, idGroup, groupName')
            ->from('Group')
            ->join('StudentGroup', 'idGroup', 'left')
            ->join('Student', 'idStudent', 'left')
            ->join('User', 'idUser')
            ->where('idSemester', $semesterId)
            ->order_by('groupName', 'ASC')
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Gets students that don't have group in the semester.
     *
     * @param int $semesterId
     * @param bool $strict <i>true</i> if $semesterId must be excluded
     * @return array
     */
    public function getStudentsWithoutGroup($semesterId, $strict = true)
    {
        $concurrentSemesters = $this->getConcurrent($semesterId, $strict);

        $sql =
            'SELECT distinct name, surname, idStudent
            FROM Student
            JOIN User USING (idUser)
            JOIN StudentGroup USING (idStudent)
            JOIN `Group` USING (idGroup) ';

        if (!empty($concurrentSemesters)) {

            $sql .=
                'WHERE idStudent NOT IN (
                    SELECT idStudent
                    FROM StudentGroup
                    JOIN `Group` USING (idGroup)
                    WHERE idSemester IN ?
                ) ';
        }
        $sql .= 'ORDER BY idGroup, surname';

        return $this->db->query($sql, array($concurrentSemesters))
            ->result();
    }

    /**
     * Get the absence of a student during a semester.
     *
     * @param string $studentId
     * @param int $semesterId
     * @return array
     */
    public function getStudentAbsence($studentId, $semesterId)
    {
        $bounds = $this->getPeriod($semesterId);
        if ($bounds === FALSE) {
            return array();
        }

        return $this->db
            ->select('idStudent, idAbsence, beginDate, endDate,
                absenceTypeName, justified')
            ->from('Absence')
            ->join('AbsenceType', 'idAbsenceType')
            ->where('idStudent', $studentId)
            ->where('beginDate BETWEEN "' . $bounds->getBeginDate()->format('Y-m-d')
                . '" AND "' . $bounds->getEndDate()->format('Y-m-d') . '"')
            ->order_by('beginDate', 'ASC')
            ->get()
            ->result();
    }


    /**
     * Returns all the marks of a student in a semester.
     *
     * @param string $studentId
     * @param int $semesterId
     * @return array
     */
    public function getStudentMarks($studentId, $semesterId)
    {
        $sql =
            'SELECT *
            FROM (
                SELECT subjectCode, moduleName, subjectName, controlName,
                coefficient, divisor, controlTypeName, median, average,
                controlDate, idSubject, subjectCoefficient, value, idPromo
                FROM Mark
                JOIN Control USING (idControl)
                JOIN ControlType USING (idControlType)
                JOIN Education USING (idEducation)
                JOIN Subject USING (idSubject)
                JOIN SubjectOfModule USING (idSubject)
                JOIN Module USING (idModule)
                JOIN `Group` USING  (idGroup)
                WHERE idStudent = ? AND idSemester = ?
            UNION
                SELECT DISTINCT subjectCode, moduleName, subjectName, controlName,
                coefficient, divisor, controlTypeName, median, average,
                controlDate, idSubject, subjectCoefficient, value, idPromo
                FROM Mark
                JOIN Control USING (idControl)
                JOIN ControlType USING (idControlType)
                JOIN Promo USING (idPromo)
                JOIN Subject USING (idSubject)
                JOIN SubjectOfModule USING (idSubject)
                JOIN Module USING (idModule)
                JOIN Education USING (idSubject)
                WHERE idStudent = ? AND idSemester = ?
            ) AS foo
            ORDER BY idSubject';

        return $this->db->query($sql, array($studentId, $semesterId, $studentId, $semesterId))
            ->result();
    }

    /**
     * Returns all subjects in the semester
     *
     * @param int $semestreId
     * @return array
     */
    public function getSubjects($semesterId) {
        return $this->db
            ->from('subject')
            ->select('subjectName, subjectCode, idSubject, moduleName')
            ->join('subjectofmodule', 'idSubject')
            ->join('module', 'idModule')
            ->join('moduleofteachingunit', 'idModule')
            ->join('teachingunitofcourse', 'idTeachingUnit')
            ->join('semester', 'idCourse')
            ->where('idSemester', $semesterId)
            ->get()
            ->result();
    }
    /**
     * Returns all education related to the semester with the educationId as key in array
     *
     * @param int $semestreId
     * @return array
     */
    public function getEducations($semesterId) {
        $educations = $this->db
            ->from('education')
            ->join('group', 'idGroup')
            ->join('teacher','idTeacher')
            ->join('user','idUser')
            ->where('idSemester', $semesterId)
            ->get()
            ->result();
        $out = array();
        foreach ($educations as $education) {
            $out[$education->idEducation] = $education;
        }

        return $out;
    }
    /**
     * Get all groups in a semester.
     *
     * @param int $semesterId
     * @return array
     */
    public function getGroups($semesterId)
    {
        return $this->db
            ->from('Group')
            ->where('idSemester', $semesterId)
            ->get()
            ->result();
    }

    /**
     * Get groups that are the same semester as a group,
     * but are not the group.
     *
     * @param int $groupId
     * @return array
     */
    public function getOtherGroups($groupId)
    {
        $semesterId = $this->db->select('idSemester')
            ->from('Group')
            ->where('idGroup', $groupId)
            ->get_compiled_select();

        return $this->db->select('idGroup')
            ->from('Group')
            ->where('idSemester = (' . $semesterId . ')')
            ->where('idGroup !=', $groupId)
            ->get()
            ->result();
    }

    /**
     * Checks a group is in semester.
     *
     * @param int $groupId
     * @param int $semesterId
     * @return bool
     */
    public function hasGroup($groupId, $semesterId)
    {
        return $this->db->where('idGroup', $groupId)
                ->where('idSemester', $semesterId)
                ->get('Group')
                ->num_rows() > 0;
    }

    /**
     * Creates a semester.
     *
     * @param int $courseId
     * @param bool $delayed
     * @param int $schoolYear
     * @return int|bool The id of the semester, FALSE if there was a problem.
     */
    public function create($courseId, $delayed, $schoolYear)
    {
        if ($this->exists($courseId, $delayed, $schoolYear)) {
            return FALSE;
        } else {
            $data = array(
                'idCourse' => $courseId,
                'schoolYear' => $schoolYear,
                'delayed' => $delayed
            );
            if ($this->db->insert('Semester', $data)) {
                return $this->db->insert_id();
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Deletes a semester.
     *
     * @param int $semesterId
     * @return bool
     */
    public function delete($semesterId)
    {
        $this->db->delete('Semester', array('idSemester' => $semesterId));
        return $this->db->affected_rows();
        
    }

}
