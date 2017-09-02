<?php

/**
 * Created by PhpStorm.
 * User: Enzo
 * Date: 08/08/2017
 * Time: 19:05
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class semester_model extends CI_Model {

    public function getSemesterId($semester) {

        $semesterId = FALSE;
        if ($semester === '') {
            $semesterId = $this->getCurrentSemesterId($_SESSION['id']);
        }
        else if ( in_array($semester, array('S1', 'S2', 'S3', 'S4') ) ) {
            $semesterId = $this->getLastSemesterOfType($semester, $_SESSION['id']);
        }

        return $semesterId;
    }

    /**
     * @param $semesterId int The semester id
     * @return String The type (S1-4) of the semester
     */
    public function getSemesterTypeFromId($semesterId) {
         $semesterType = $this->db->select('typeSemestre')
            ->from('Semestres')
            ->where('idSemestre', $semesterId)
            ->get()
            ->result();

        if ( empty($semesterType) ) {
            return FALSE;
        }
        return $semesterType[0]->typeSemestre;
    }

    /**
     * @param $semesterId int The id of the semester
     * @return mixed An array of two dates, the beginning and the end of the semester
     */
    public function getSemesterBounds($semesterId) {
        $this->db->select('typeSemestre, anneeScolaire, differe')
            ->from('Semestres')
            ->where('idSemestre', $semesterId);

        $row = $this->db->get()->result()[0];

        if ( empty($row) ) {
            return FALSE;
        }


        if (( ($row->typeSemestre === 'S1' || $row->typeSemestre === 'S3') && !$row->differe ) ||
            ( ($row->typeSemestre === 'S2' || $row->typeSemestre === 'S4') && $row->differe ))
        {
            return array(
                $row->anneeScolaire . '-09-01',
                (intval($row->anneeScolaire) + 1) . '-01-31'
            );
        } else {
            return array(
                $row->anneeScolaire . '-02-01',
                $row->anneeScolaire . '-08-31'
            );
        }
    }

    /**
     * @param $studentId String The id of the student
     * @return int current activ semestre
     */
    public function getCurrentSemesterId($studentId) {
        $sql = "SELECT idSemestre from Etudiantgroupe
          join Groupes USING (idGroupe)
          join Semestres USING (idSemestre)
          where numEtudiant=? and actif = 1
          ORDER BY idSemestre DESC";
        $semestre = $this->db->query($sql,array($studentId))->row();
        if (empty($semestre) ) {
            return FALSE;
        }
        return $semestre->idSemestre;
    }

    /**
     * Returns the id of the student's [type] semester
     * @param $semesterType String A type of semester (S1-4)
     * @param $studentId String The student id
     * @return int The id of the corresponding semester, FALSE if it doesn't exists
     */
    public function getLastSemesterOfType($semesterType, $studentId) {

        $compatibleSemesters = $this->db->select('idSemestre')
            ->from('Semestres')
            ->where('typeSemestre', $semesterType)
            ->get_compiled_select();

        $groupId = $this->db->select_max('Groupes.idGroupe')
            ->from('Groupes')
            ->join('EtudiantGroupe', 'Groupes.idGroupe = EtudiantGroupe.idGroupe')
            ->where('EtudiantGroupe.numEtudiant', $studentId)
            ->where('Groupes.idSemestre IN (' . $compatibleSemesters . ')')
            ->get()
            ->result()[0]->idGroupe;

        $semester = $this->db->select('idSemestre')
            ->from('Groupes')
            ->where('idGroupe', $groupId)
            ->get()
            ->result();

        if ( empty($semester) ) {
            return FALSE;
        }

        return $semester[0]->idSemestre;
    }

}
