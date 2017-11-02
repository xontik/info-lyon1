<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class semester_model extends CI_Model {

    /**
     * Return the semester corresponding to the string passed in parameter.
     *
     * @param string $semester Can be empty or S1-4
     * @param string $studentId
     * @return bool|int FALSE if $semester is not a correct value
     */
    public function getSemesterId($semester, $studentId) {
        $semesterId = FALSE;
        if ($semester === '') {
            $semesterId = $this->getCurrentSemesterId($studentId);
        }
        else if ( in_array($semester, array('S1', 'S2', 'S3', 'S4') ) ) {
            $semesterId = $this->getLastSemesterOfType($semester, $studentId);
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

        return $this->getSemesterObjectPeriod($semester);

    }
    /**
     * @param $semester object with type,anneeScolaire et differe
     * @return mixed An array of two dates, the beginning and the end of the semester
     */

    public function getSemesterObjectPeriod($semester) {
        require_once(APPPATH . 'libraries/Period.php');



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
     * Computes the period of the current semester.
     * @return Period The period of the current semester.
     * Returns FALSE if there is no current semester
     */
    public function getCurrentPeriod() {
        $semesterId = $this->getLastActiveSemesterId();
        return $semesterId !== FALSE ? $this->getSemesterPeriod($semesterId) : FALSE;
    }

    /**
     * @return int The id of the most recent active semester.
     * WARNING : There are several semester at the same time,
     * this function only returns the last one created !
     */
    public function getLastActiveSemesterId() {
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
     * @param string $studentId The id of the student
     * @return int The current semester for the student
     */
    public function getCurrentSemesterId($studentId) {
        $semester = $this->db->select('idSemestre')
            ->from('EtudiantGroupe')
            ->join('Groupes', 'idGroupe')
            ->join('Semestres', 'idSemestre')
            ->where('actif', '1')
            ->where('numEtudiant', $studentId)
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
     * 
     * @param string $semesterType A type of semester (S1-4)
     * @param string $studentId The student id
     * @return int|bool The id of the corresponding semester, FALSE if it doesn't exists
     */
    public function getLastSemesterOfType($semesterType, $studentId) {
        if (!in_array($semesterType, array('S1', 'S2', 'S3', 'S4'))) {
            return FALSE;
        }

        $compatibleSemesters = $this->db->select('idSemestre')
            ->from('Semestres')
            ->join('Parcours', 'idParcours')
            ->where('type', $semesterType)
            ->get_compiled_select();

        $semester = $this->db->select_max('Groupes.idSemestre')
            ->from('Groupes')
            ->join('EtudiantGroupe', 'idGroupe')
            ->where('EtudiantGroupe.numEtudiant', $studentId)
            ->where('Groupes.idSemestre IN (' . $compatibleSemesters . ')')
            ->get()
            ->row();

        if (empty($semester)) {
            return FALSE;
        }
        return $semester->idSemestre;
    }


    public function getAllSemesters() {
      $sql = 'SELECT *
          FROM Semestres
          JOIN parcours USING (idparcours)
          LEFT JOIN Groupes USING (idSemestre)
          ORDER BY idSemestre DESC,
          anneeScolaire DESC,
          nomGroupe ASC';
      return $this->db->query($sql)->result();
    }
    
    public function getSemesterById($semesterId) {
        return $this->db->from('Semestres')
            ->join('Parcours', 'idParcours')
            ->where('idSemestre', $semesterId)
            ->get()
            ->row();
    }
    
    public function isSemesterEditable($id) {
        if (is_null($this->getSemesterById($id))) {
            return false;
        }
        $dateSem = $this->getSemesterPeriod($id);
        
        $now = new DateTime();
        $dateEnd = $dateSem->getEndDate();
        
        if ($now > $dateEnd) {
            return false;
        }
        return true;
    }
    
    public function isSemesterDeletable($id) {
        if (is_null($this->getSemesterById($id))) {
            return false;
        }

        $now = new DateTime();
        if ($now > $this->getSemesterPeriod($id)->getBeginDate()) {
          return false;
        }
        return true;
    }

    public function deleteSemestre($semesterId) {
        return $this->db->delete('Semestres', array('idSemestre' => $semesterId));
    }
    
    public function addSemester($idParcours, $differe, $anneeScolaire) {
        if ($this->isThisSemesterAlreadyExist($idParcours, $differe, $anneeScolaire)) {
            return FALSE;
        } else {
            $data = array(
                'idParcours' => $idParcours,
                'anneeScolaire' => $anneeScolaire,
                'differe' => $differe
            );
            return $this->db->insert('Semestres', $data);
        }
    }
    
    public function isThisSemesterAlreadyExist($idParcours, $differe, $anneeScolaire) {
        $sql = 'SELECT * FROM Semestres WHERE idParcours = ? AND differe = ? AND anneeScolaire = ?';
        return $this->db->query($sql, array($idParcours, $differe, $anneeScolaire))->num_rows() > 0;
    }

    public function getSemesterIdsSamePeriod($idSemestre, $strict = true) {//$strict true si on exclu le semstre passer en parametre
        $semesters = $this->getAllSemesters();
        $beginDate = $this->getSemesterPeriod($idSemestre)->getBeginDate();
        $outSem = array();

        foreach ($semesters as $semester) {
            if ($beginDate == $this->getSemesterObjectPeriod($semester)->getBeginDate()) {
                if (!in_array($semester->idSemestre, $outSem) && (!$strict OR $semester->idSemestre!=$idSemestre)) {
                    $outSem[] = $semester->idSemestre;
                }
            }
        }
        return $outSem;
    }
    
    public function getStudentWithoutGroup($semestreId, $strict = true) {//$strict true si on exclu le semstre passer en parametre
        //TODO a retravailler
        $sql = 'SELECT *
            FROM Etudiants
            LEFT JOIN EtudiantGroupe USING (numEtudiant)
            LEFT JOIN Groupes USING (idGroupe)
            WHERE numEtudiant NOT IN (
                SELECT numEtudiant
                FROM EtudiantGroupe
                JOIN Groupes USING (idGroupe)
                WHERE idSemestre in ?)
            ORDER BY idGroupe, nom';

        return $this->db->query($sql, array($this->getSemesterIdsSamePeriod($semestreId, false)))
            ->result();

    }
    
    public function isThisGroupInSemester($groupId, $semId) {
        return $this->db->where('idGroupe', $groupId)
            ->where('idSemestre', $semId)
            ->get('Groupes')
            ->num_rows() > 0;
    }

    public function getOtherGroups($groupeId) {
        $sql = 'SELECT idGroupe
            FROM groupes
            WHERE idSemestre = (
                SELECT idSemestre
                FROM groupes
                WHERE idGroupe = ?)
            AND idGroupe != ?';

        $res = $this->db->query($sql, array($groupeId, $groupeId))->result_array();
        return array_column($res, 'idGroupe');
    }

}
