<?php

class Appointments extends CI_Model
{

    /**
     * Set the final to the date of a date proposal.
     *
     * @param $dateProposalId
     * @return bool
     */
    public function setFinalDate($dateProposalId)
    {
        $this->load->model('DateProposals');
        $dateProposal = $this->DateProposals->get($dateProposalId);

        $this->db->set('finalDate', $dateProposal->date)
            ->where('idAppointment', $dateProposal->idAppointment)
            ->update('Appointment');
        return $this->db->affected_rows();

    }

    public function setComment($comment, $appointmentId)
    {
        $this->db->set('comment', $comment)
            ->where('idAppointment', $idAppointment)
            ->update('Appointment');
        return $this->db->affected_rows();

    }

    public function create($projectId) {
        $this->db
            ->insert('Appointment',array('idProject' => $projectId));
        return $this->db->affected_rows();
    }


    public function get($appointmentId) {
        return $this->db
            ->from('Appointment')
            ->where('idAppointment', $appointmentId)
            ->get()
            ->row();

    }

    public function delete($appointmentId) {
        $this->db
            ->delete('Appointment', array('idAppointment' => $appointmentId));
        return $this->db->affected_rows();
    }


}
