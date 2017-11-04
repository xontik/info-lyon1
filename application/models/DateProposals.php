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
     * Get all date proposals referenced to the appointement.
     *
     * @param int $appointementId The appointement id
     * @return mixed
     */
    public function getAll($appointementId)
    {
        return $this->db->where('idAppointment', $appointementId)
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
            ->where('accept IS NULL')
            ->or_where('accept', '0')
            ->get()
            ->num_rows() === 0;
    }

    /**
     * Creates a date proposal refering to an appointement.
     *
     * @param int $appointementId The appointement
     * @param DateTime $datetime The time of the proposal
     * @param int $userId The user who makes the proposal
     * @return bool
     */
    public function create($appointementId, $datetime, $userId)
    {
        // Check is user belongs to the project
        $projectId = $this->getGroupId('Appointement', $appointementId);

        if (!$this->isUserInProject($userId, $projectId)) {
            redirect('/Project/' . $projectId);
        }

        $proposalId = $this->db->trans_start()
            ->insert('DateProposal',
                array(
                    'date' => $datetime->format('Y-m-d H:i:s'),
                    'idAppointement' => $appointementId
                )
            )
            ->insert_id();

        $this->db->insert('DateAccept',
            array(
                'idProposal' => $proposalId,
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
            ->where('idProposal', $proposalId)
            ->where('idUser', $userId)
            ->update('DateAccept');
    }
}