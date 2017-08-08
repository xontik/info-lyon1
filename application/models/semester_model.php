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
        $this->db->select('typeSemestre')
            ->from('Semestres')
            ->where('idSemestre', $semesterId);
        return $this->db->get()->result()[0];
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
                    '01-09-' . $row->anneeScolaire,
                    '31-01-' . (intval($row->anneeScolaire)+1)
                );
            case 'S2':
            case 'S4':
                return array(
                    '01-02-' . $row->anneeScolaire,
                    '31-08-' . $row->anneeScolaire
                );
            default:
                return FALSE;
        }

    }

    public function getCurrentSemesterId($numEtudiant) {
        $maxGroupId = $this->db->select_max('idGroupe')
            ->from('Groupes')
            ->join('GroupeEtudiant', 'Groupes.idGroupe = GroupeEtudiant.idGroupe')
            ->where('GroupeEtudiant.numEtudiant', $numEtudiant)
            ->get_compiled_query();

        return $this->db->select('idSemestre')
            ->from('Groupes')
            ->where('idGroupe', $maxGroupId)
            ->get()
            ->result()[0];
    }

    /**
     * Returns the id of the student's [type] semester
     * @param $semesterType String A type of semester (S1-4)
     * @param $numEtudiant int The student id
     * @return int The id of the corresponding semester
     */
    public function getLastSemesterOfType($semesterType, $numEtudiant) {

        $compatibleSemesters = $this->db->select('idSemestre')
            ->from('Semestres')
            ->where('typeSemestre', $semesterType)
            ->get_compiled_query();

        $groupId = $this->db->select_max('idGroupe')
            ->from('Groupes')
            ->join('GroupeEtudiant', 'Groupes.idGroupe = GroupeEtudiant.idGroupe')
            ->where('GroupeEtudiant.numEtudiant', $numEtudiant)
            ->where('Groupe.idSemestre IN', $compatibleSemesters)
            ->get_compiled_query();


        return $this->db->select('idSemestre')
            ->from('Groupes')
            ->where('idGroupe', $groupId)
            ->get()
            ->result()[0];
    }

}