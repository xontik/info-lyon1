<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class semester_model extends CI_Model {

    /**
     * Return the semester corresponding to the string passed in parameter.
     *
     * @param string $semester Can be empty or S1-4
     * @return bool|int The id of the semester,
     * FALSE if $semester is not a correct value
     */
    public function getSemesterId($semester) {
        $semesterId = FALSE;
        if ($semester === '') {
            if ($_SESSION['user_type'] === 'student') {
                $semesterId = $this->getCurrentSemesterId($_SESSION['id']);
            }
        }
        else if ( in_array($semester, array('S1', 'S2', 'S3', 'S4') ) ) {
            $semesterId = $this->getLastSemesterOfType($semester, $_SESSION['id']);
        }

        return $semesterId;
    }

    /**
     * @param int $semesterId The semester id
     * @return string The type (S1-4) of the semester
     */
    public function getSemesterTypeFromId($semesterId) {
        $semesterType = $this->db->select('Parcours.type')
            ->from('Semestres')
            ->join('Parcours', 'idParcours')
            ->where('idSemestre', $semesterId)
            ->get()
            ->row();

        if (empty($semesterType)) {
            return FALSE;
        }
        return $semesterType->type;
    }

    /**
     * @param int $semesterId The id of the semester
     * @return Period|bool The period of the semester,
     * FALSE if the semester doesn't exists
     */
    public function getSemesterPeriod($semesterId) {
        require_once(APPPATH . 'libraries/Period.php');

        $semester = $this->db->select('Parcours.type, anneeScolaire, differe')
            ->from('Semestres')
            ->join('Parcours', 'idParcours')
            ->where('idSemestre', $semesterId)
            ->get()
            ->row();

        if (empty($semester)) {
            return FALSE;
        }

        if (   ( ($semester->type === 'S1' || $semester->type === 'S3') && !$semester->differe )
            || ( ($semester->type === 'S2' || $semester->type === 'S4') && $semester->differe )
        ) {
            return new Period(
                new DateTime($semester->anneeScolaire . '-09-01'),
                new DateTime((intval($semester->anneeScolaire) + 1) . '-01-31')
            );
        } else {
            return new Period(
                new DateTime(($semester->anneeScolaire + 1) . '-02-01'),
                new DateTime(($semester->anneeScolaire + 1) . '-08-31')
            );
        }
    }

    /**
     * @param string $studentId The id of the student
     * @return int The current semester for the student
     */
    public function getCurrentSemesterId($studentId = '') {
        if ($studentId !== '') {
            $this->db->where('numEtudiant', $studentId);
        }
        $semester = $this->db->select('idSemestre')
            ->from('EtudiantGroupe')
            ->join('Groupes', 'idGroupe')
            ->join('Semestres', 'idSemestre')
            ->where('actif', '1')
            ->order_by('idSemestre', 'desc')
            ->get()
            ->row();

        if (empty($semester)) {
            return FALSE;
        }
        return $semester->idSemestre;
    }

    /**
     * Returns the id of the student's `type` semester
     * @param string $semesterType A type of semester (S1-4)
     * @param string $studentId The student id
     * @return int|bool The id of the corresponding semester, FALSE if it doesn't exists
     */
    public function getLastSemesterOfType($semesterType, $studentId)
    {
        $compatibleSemesters = $this->db->select('idSemestre')
            ->from('Semestres')
            ->join('Parcours', 'idParcours')
            ->where('typeSemestre', $semesterType)
            ->get_compiled_select();

        $groupId = $this->db->select_max('Groupes.idGroupe')
            ->from('Groupes')
            ->join('EtudiantGroupe', 'idGroupe')
            ->where('EtudiantGroupe.numEtudiant', $studentId)
            ->where('Groupes.idSemestre IN (' . $compatibleSemesters . ')')
            ->get()
            ->row()->idGroupe;

        $semester = $this->db->select('idSemestre')
            ->from('Groupes')
            ->where('idGroupe', $groupId)
            ->get()
            ->row();

        if ( empty($semester) ) {
            return FALSE;
        }

        return $semester->idSemestre;
    }

}
