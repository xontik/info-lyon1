<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DateAccepts extends CI_Model
{

    /**
     * Get the DateAccept.
     *
     * @param $dateAcceptId
     * @return object|bool FALSE if id doesn't exist
     */
    public function get($dateAcceptId)
    {
        $res = $this->db
            ->from('DateAccept')
            ->where('idDateAccept', $dateAcceptId)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Get all the date accepts of an appointment.
     *
     * @param $appointmentId
     * @return array
     */
    public function getAll($appointmentId)
    {
        return $this->db
            ->from('DateAccept')
            ->join('DateProposal', 'idDateProposal')
            ->where('idAppointment', $appointmentId)
            ->get()
            ->result();
    }

}
