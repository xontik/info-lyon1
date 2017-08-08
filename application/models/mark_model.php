<?php

/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 13/04/2017
 * Time: 15:42
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Mark_model extends CI_Model {

    public function __construct()
    {

    }

    /**
     * Returns all the marks of a student in a semester
     * @param $etuId int The student id
     * @param $semestreId int The semester id
     * @return mixed The marks of the student during a semester
     */
    public function getMarksFromSemester($etuId, $semestreId) {
        $CI =& get_instance();
        $CI->load->model("semester_model");

        $semDates = $CI->semester_model->getSemesterBounds($semestreId);

        $this->db->from('Notes')
            ->join('Controles', 'Notes.idControle = Controles.idControle')
            ->where('numEtudiant', $etuId)
            ->where('dateControle BETWEEN "' . $semDates[0] . '" AND "' . $semDates[1] . '"');

        return $this->db->get()->result();

    }

    /**
     * Add a note for a student to a specified test
     * @param $controlId int The id of the test
     * @param $numStudent int The id of the student
     * @param $value float The note
     */
    public function add($controlId, $numStudent, $value) {
        $data = array(
            'idContole' => $controlId,
            'numEtudiant' => $numStudent,
            'valeur' => $value
        );

		$this->db->insert('Notes', $data);
    }

    /**
     * Add the notes of the students to the specific test
     * @param $controlId int The test id
     * @param $studentsNotes array An associative array of the following shape :
     * { studentId1 => note1, studentId2 => note2, ... }
     */
    public function addMarks($controlId, $studentsNotes) {
        foreach ($studentsNotes as $student => $note)
			add($controlId, $student, $note);
    }

    /**
     * Modify the note of a student to a test
     * @param $controlId int The ID of the test
     * @param $numStudent int The student id
     * @param $newValue float The new note
     */
    public function editMark($controlId, $numStudent, $newValue){
        $this->db->set('valeur', $newValue)
                ->where('idControle', $controlId)
                ->where('numEtudiant', $numStudent);

		$this->db->update('Notes');
    }

    /**
     * Deletes the note of a student in a test
     * @param $controlId int The test id
     * @param $numStudent int The student id
     */
    public function deleteMark($controlId, $numStudent){
        $this->db->where('idControle', $controlId)
                ->where('numEtudiant', $numStudent)
                ->delete('Notes');
    }

}
