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
      $sql = "SELECT type FROM Semestres JOIN Parcours using (idParcours) where idSemestre=?";
      return $this->db->query($sql,array($semesterId))->row()->type;

    }

    /**
     * @param $semesterId int The id of the semester
     * @return mixed An array of two dates, the beginning and the end of the semester
     */
    public function getSemesterBounds($semesterId) {
        $this->db->select('type, anneeScolaire, differe')
            ->from('Semestres')
            ->where('idSemestre', $semesterId);
        $sql = 'SELECT type,anneeScolaire,differe FROM Semestres JOIN Parcours USING (idParcours) where idSemestre = ?';
        $row = $this->db->query($sql,array($semesterId))->row();

        if ( empty($row) ) {
            return FALSE;
        }


        if (( ($row->type === 'S1' || $row->type === 'S3') && !$row->differe ) ||
            ( ($row->type === 'S2' || $row->type === 'S4') && $row->differe ))
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
        $sql = "SELECT idSemestre from EtudiantGroupe
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


        $sql = 'SELECT idSemestre FROM EtudiantGroupe
        join Groupes using(idGroupe)
        join Semestres using(idSemestre)
        join Parcours using(idParcours)
        where type = ? and numEtudiant = ? order by idGroupe DESC'

        $row = $this->db->query($sql,array($semesterType, $studentId))->row();


        if ( empty($row) ) {
            return FALSE;
        }

        return $row->idSemestre;
    }

}
