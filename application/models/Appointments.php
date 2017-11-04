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

        return $this->db->set('finalDate', $dateProposal->date)
            ->where('idAppointment', $dateProposal->idAppointment)
            ->update('Appointment');
    }

    public function setComment($comment, $appointmentId)
    {
        return $this->db->set('comment', $comment)
            ->where('idAppointment', $idAppointment)
            ->update('Appointment');
    }

}