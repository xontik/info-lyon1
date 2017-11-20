<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DateProposals extends CI_Model
{

    /**
     * Get details about a date proposal.
     *
     * @param $dateProposalId
     * @return object|bool FALSE if the id doesn't exist
     */
    public function get($dateProposalId)
    {
        $res = $this->db
            ->where('idDateProposal', $dateProposalId)
            ->get('DateProposal')
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Returns the next date proposal in time.
     *
     * @param $appointmentId
     * @return object|bool FALSE if there's no date proposal
     */
    public function getNext($appointmentId)
    {
        $res = $this->db
            ->from('DateProposal')
            ->where('idAppointment', $appointmentId)
            ->where('date >= CURDATE()')
            ->order_by('date', 'ASC')
            ->limit(1)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Get all date proposals referenced to the appointment.
     *
     * @param int $appointmentId The appointment id
     * @return mixed
     */
    public function getAll($appointmentId)
    {
        return $this->db
            ->where('idAppointment', $appointmentId)
            ->get('DateProposal')
            ->result();
    }

    /**
     * Checks if a date proposal has been accepted by everyone
     *
     * @param int $dateProposalId
     * @return bool
     */
    public function isAccepted($dateProposalId) {
        return $this->db
            ->from('DateProposal')
            ->join('DateAccept', 'idDateProposal')
            ->where('idDateProposal', $dateProposalId)
            ->where('accepted IS NULL')
            ->or_where('accepted', 0)
            ->get()
            ->num_rows() === 0;
    }

    /**
     * Creates a date proposal refering to an appointment.
     *
     * @param int appointmentId
     * @param DateTime $datetime The time of the proposal
     * @param int $userId The user who makes the proposal
     * @return bool
     */
    public function create($appointmentId, $datetime, $userId)
    {
        // Check is user belongs to the project
        $projectId = $this->getGroupId('Appointment', $appointmentId);

        if (!$this->isUserInProject($userId, $projectId)) {
            redirect('/Project/' . $projectId);
        }

        $proposalId = $this->db->trans_start()
            ->insert('DateProposal',
                array(
                    'date' => $datetime->format('Y-m-d H:i:s'),
                    'idAppointment' => $appointmentId
                )
            )
            ->insert_id();

        $this->db->insert('DateAccept',
            array(
                'idDateProposal' => $proposalId,
                'idUser' => $userId,
                'accepted' => '1'
            )
        )
            ->trans_end();

        return true;
    }

    /**
     * Set whether an user accepted a proposition or not.
     *
     * @param int $proposalId The proposition id
     * @param int $userId The user id
     * @param boolean $accept Whether he accepted it or not
     */
    public function setAccept($proposalId, $userId, $accept)
    {
        $this->db->set('accepted', $accept)
            ->where('idDateProposal', $proposalId)
            ->where('idUser', $userId)
            ->update('DateAccept');
    }
}
