<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marks extends CI_Model {

    /**
     * Creates a mark.
     * 
     * @param $controlId
     * @param $studentId
     * @param $value
     * @param bool
     */
    public function create($controlId, $studentId, $value) {
        $sql =
            'INSERT INTO Mark
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE value = ?';

        return $this->db->simple_query($sql, array($studentId, $controlId, $value, $value));
    }

    /**
     * Creates all marks
     * 
     * @param $studentsMarks
     * @param $controlId
     */
    public function createAll($studentsMarks, $controlId) {
        foreach ($studentsMarks as $student => $note) {
            if ($note !== '') {
                $this->create($controlId, $student, $note);
            } else {
                $this->delete($controlId, $student);
            }
        }
    }

    /**
     * Updates a mark.
     * 
     * @param $controlId
     * @param $studentId
     * @param $newValue
     * @return bool
     */
    public function update($controlId, $studentId, $newValue) {
        return $this->db->set('value', $newValue)
            ->where('idControl', $controlId)
            ->where('idStudent', $studentId)
            ->update('Mark');
    }

    /**
     * Deletes a mark.
     * 
     * @param $controlId
     * @param $studentId
     * @return bool
     */
    public function delete($controlId, $studentId) {
        return $this->db->where('idControl', $controlId)
            ->where('idStudent', $studentId)
            ->delete('Mark');
    }

}
