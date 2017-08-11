<?php

/**
 * Created by PhpStorm.
 * User: Enzo
 * Date: 08/08/2017
 * Time: 19:05
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class semester_model extends CI_Model {

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
        $this->db->select('typeSemestre, anneeScolaire')
            ->from('Semestres')
            ->where('idSemestre', $semesterId);

        $row = $this->db->get()->result()[0];

        if ( empty($row) ) {
            return FALSE;
        }

        switch ($row->typeSemestre) {
            case 'S1':
            case 'S3':
                return array(
                    $row->anneeScolaire . '-09-01',
                    ( intval($row->anneeScolaire)+1 ) . '-01-31'
                );
            case 'S2':
            case 'S4':
                return array(
                    $row->anneeScolaire . '-02-01',
                    $row->anneeScolaire . '-08-31'
                );
            default:
                return FALSE;
        }

    }

    /**
     * @param $studentId String The id of the student
     * @return int The highest semester the student got to.
     * Should be the same as the current one.
     */
    public function getCurrentSemesterId($studentId) {
        $maxGroupId = $this->db->select_max('Groupes.idGroupe')
            ->from('Groupes')
            ->join('EtudiantGroupe', 'Groupes.idGroupe = EtudiantGroupe.idGroupe')
            ->where('EtudiantGroupe.numEtudiant', $studentId)
            ->get()
            ->result()[0]->idGroupe;


        $semesterId = $this->db->select('idSemestre')
            ->from('Groupes')
            ->where('idGroupe', $maxGroupId)
            ->get()
            ->result();

        if ( empty($semesterId) ) {
            return FALSE;
        }
        return $semesterId[0]->idSemestre;
    }

    /**
     * Returns the id of the student's [type] semester
     * @param $semesterType String A type of semester (S1-4)
     * @param $studentId String The student id
     * @return int The id of the corresponding semester
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

        $semesterId = $this->db->select('idSemestre')
            ->from('Groupes')
            ->where('idGroupe', $groupId)
            ->get()
            ->result();

        if ( empty($semesterId) ) {
            return FALSE;
        }

        return $semesterId[0]->idSemestre;
    }

}
