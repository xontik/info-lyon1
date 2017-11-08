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
    public function create($controlId, $studentId, $value)
    {
        $sql =
            'INSERT INTO Mark (idStudent, idControl, `value`)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE `value` = ?';

        return $this->db->query($sql, array($studentId, $controlId, $value, $value));
    }

    /**
     * Creates all marks
     * 
     * @param $studentsMarks
     * @param $controlId
     * @return bool
     */
    public function createAll($studentsMarks, $controlId)
    {
        $noerror = true;

        $this->db->trans_start();
        foreach ($studentsMarks as $student => $mark) {
            if (empty($mark)) {
                $noerror = $noerror && $this->delete($controlId, $student);
            } else {
                $noerror = $noerror && $this->create($controlId, $student, (float) $mark);
            }
        }
        $this->db->trans_complete();

        return $noerror;
    }

    /**
     * Updates a mark.
     * 
     * @param $controlId
     * @param $studentId
     * @param $newValue
     * @return bool
     */
    public function update($controlId, $studentId, $newValue)
    {
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
    public function delete($controlId, $studentId)
    {
        return $this->db->where('idControl', $controlId)
            ->where('idStudent', $studentId)
            ->delete('Mark');
    }

}
