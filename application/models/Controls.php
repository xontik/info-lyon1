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
        $res = $this->db->where('idControl', $controlId)
            ->get('Control')
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

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
        return $this->db->select('surname, name, idStudent, value')
            ->from('Control')
            ->join('Education', 'idEducation')
            ->join('StudentGroup', 'idGroup')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->join('Group', 'idGroup')
            ->join('Semester', 'idSemester')
            ->join('Mark', 'idStudent, idControl', 'left')
            ->where('idControl', $controlId)
            ->where('active', '1')
            ->get()
            ->result();
    }

    private function _getPromoMarks($teacherId, $controlId)
    {
        $CI = &get_instance();
        $CI->load->model('Teachers');

        $this->db
            ->select('surname, name, idStudent, value')
            ->from('Control')
            ->join('Promo', 'idPromo')
            ->join('Semester', 'idSemester')
            ->join('Group', 'idSemester')
            ->join('StudentGroup', 'idGroup')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->join('Mark', 'idStudent, idControl', 'left')
            ->where('idControl', $controlId)
            ->where('active', '1');

        if (!$CI->Teachers->isReferent($teacherId, $controlId))
        {
            $this->db
                ->join('Education', 'idGroup, idSubject')
                ->where('idTeacher', $teacherId);
        }

        return $this->db->get()
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
                    SELECT subjectCode, subjectName
                    FROM Control
                    JOIN Promo USING (idPromo)
                    JOIN Subject USING (idSubject)
                    WHERE idControl = ?
                UNION
                    SELECT subjectCode, subjectName
                    FROM Control
                    JOIN Education USING (idEducation)
                    JOIN Subject USING (idSubject)
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
     * @param $div
     * @param $typeId
     * @param $date
     * @return bool
     */
    public function update($controlId, $name, $coeff, $div, $typeId, $date)
    {
        $data = array(
            'controlName' => $name,
            'coefficient' => $coeff,
            'divisor' => $div,
            'idControlType' => $typeId,
            'controlDate' => $date,
        );

        return $this->db->set($data)
            ->where('idControl', $controlId)
            ->update('Control');
    }

    /**
     * Deletes a control.
     *
     * @param $controlId
     * @return bool
     */
    public function delete($controlId)
    {
        return $this->db->delete('Control', array('idControl' => $controlId));
    }

}
