<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Controls extends CI_Model
{

    /**
     * Get the appointments of a control.
     *
     * @param $controlId
     * @return object|bool FALSE if $controlId doesn't exists
     */
    public function get($controlId)
    {
        $res = $this->db
            ->where('idControl', $controlId)
            ->get('Control')
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Checks if at least one student was noted.
     *
     * @param $controlId
     * @return bool
     */
    public function hasMark($controlId) {
        return $this->db
            ->from('Control')
            ->join('Mark', 'idControl')
            ->where('idControl', $controlId)
            ->get()
            ->num_rows() > 0;
    }

    /**
     * Returns the marks of the students.
     *
     * @param $control
     * @param $teacherId
     * @return array
     */
    public function getMarks($control, $teacherId)
    {
        if (is_null($control->idPromo)) {
            $marks = $this->_getControlMarks($control->idControl);
        } else {
            $marks = $this->_getPromoMarks($teacherId, $control->idControl);
        }
        return $marks;
    }

    private function _getControlMarks($controlId)
    {
        return $this->db
            ->select('Student.idStudent, name, surname, value')
            ->from('Control')
            ->join('Education', 'idEducation')
            ->join('StudentGroup', 'idGroup')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->join('Mark', 'Mark.idStudent = Student.idStudent AND Mark.idControl = Control.idControl', 'left')
            ->where('Control.idControl', $controlId)
            ->get()
            ->result();
    }

    private function _getPromoMarks($teacherId, $controlId)
    {
        $this->load->model('Teachers');

        $data = array();

        $sql =
            'SELECT Student.idStudent, surname, name, value
            FROM Control
            JOIN Promo USING (idPromo)
            JOIN Subject USING (idSubject)
            JOIN `Group` USING (idSemester)
            JOIN StudentGroup USING (idGroup)
            JOIN Student USING (idStudent)
            JOIN `User` USING (idUser)
            LEFT JOIN Mark ON Mark.idStudent = Student.idStudent
                AND Mark.idControl = Control.idControl ';

        if (!$this->Teachers->isReferent($teacherId, $controlId))
        {
            $sql .= 'JOIN Education ON Education.idGroup = Group.idGroup
                AND Education.idSubject = Subject.idSubject
                WHERE idTeacher = ? AND ';
            $data[] = $teacherId;
        }
        else {
            $sql .= 'WHERE ';
        }

        $sql .= 'Control.idControl = ?';
        $data[] = $controlId;

        return $this->db->query($sql, $data)
            ->result();
    }


    /**
     * Returns the subject of a control.
     *
     * @param $controlId
     * @return stdClass
     */
    public function getSubject($controlId)
    {
        $sql = 'SELECT DISTINCT * FROM (
                    SELECT subjectCode, subjectName, moduleName
                    FROM Control
                    JOIN Promo USING (idPromo)
                    JOIN Subject USING (idSubject)
                    JOIN SubjectOfModule USING (idSubject)
                    JOIN Module USING (idModule)
                    WHERE idControl = ?
                UNION
                    SELECT subjectCode, subjectName, moduleName
                    FROM Control
                    JOIN Education USING (idEducation)
                    JOIN Subject USING (idSubject)
                    JOIN SubjectOfModule USING (idSubject)
                    JOIN Module USING (idModule)
                    WHERE idControl = ?
            ) AS foo';

        return $this->db->query($sql, array_fill(0, 2, $controlId))
            ->row();
    }

    /**
     * Get control types.
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->db->get('ControlType')
            ->result();
    }

    /**
     * Creates a control.
     *
     * @param $name
     * @param $coeff
     * @param $div
     * @param $typeId
     * @param $date
     * @param $educationId
     * @return bool
     */
    public function create($name, $coeff, $div, $typeId, $date, $educationId)
    {
        $data = array(
            'controlName' => $name,
            'coefficient' => $coeff,
            'divisor' => $div,
            'idControlType' => $typeId,
            'controlDate' => $date,
            'idEducation' => $educationId
        );
        return $this->db->insert('Control', $data);
    }

    /**
     * Creates a promo control.
     *
     * @param $name
     * @param $coeff
     * @param $div
     * @param $date
     * @param $subjectId
     * @return bool
     */
    public function createPromo($name, $coeff, $div, $date, $subjectId)
    {
        $this->load->model('Subjects');

        $data = array(
            'idSemester' => $this->Subjects->getSemester($subjectId),
            'idSubject' => $subjectId
        );
        $this->db->insert('Promo', $data);
        $promoId = $this->db->insert_id();

        $data = array(
            'controlName' => $name,
            'coefficient' => $coeff,
            'divisor' => $div,
            'idControlType' => 1,
            'controlDate' => $date,
            'idPromo' => $promoId
        );
        return $this->db->insert('Control', $data);
    }

    /**
     * Updates the control.
     *
     * @param $controlId
     * @param $name
     * @param $coeff
     * @param $div  (optionnal)
     * @param $date (optionnal)
     * @return bool
     */
    public function update($controlId, $name, $coeff, $div = null, $date = null)
    {
        $data = array(
            'controlName' => $name,
            'coefficient' => $coeff,
        );

        if ($div !== null) {
            $data['divisor'] = $div;
        }

        if ($date !== null) {
            $data['controlDate'] = $date;
        }

        $this->db->set($data)
            ->where('idControl', $controlId)
            ->update('Control');
        return $this->db->affected_rows();

    }

    /**
     * Deletes a control.
     *
     * @param $controlId
     * @return bool
     */
    public function delete($controlId)
    {
        $this->db->delete('Control', array('idControl' => $controlId));
        return $this->db->affected_rows();
        
    }

}
