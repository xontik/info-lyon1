<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DateProposals extends CI_Model
{

    /**
     * Get appointments about a date proposal.
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
     * Get all date proposals referenced to the appointment.
     *
     * @param int $appointmentId The appointment id
     * @return mixed
     */
    public function getAll($appointmentId)
    {
        return $this->db
            ->from('DateProposal')
            ->where('idAppointment', $appointmentId)
            ->get()
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
            ->where('(accepted IS NULL OR accepted = 0)')
            ->get()
            ->num_rows() === 0;

    }

    /**
     * Creates a date proposal refering to an appointment.
     *
     * @param int       $appointmentId
     * @param DateTime  $datetime
     * @param int       $userId
     * @return bool
     */
    public function create($appointmentId, $datetime, $userId)
    {
        $this->load->model('Projects');

        // Check is user belongs to the project
        $projectId = $this->Projects->getProjectId('Appointment', $appointmentId);
        $members = $this->Projects->getMembers($projectId);
        $members[] = $this->Projects->getTutor($projectId);

        if (!$this->Projects->isUserInProject($userId, $projectId)) {
            redirect('/Project/' . $projectId);
        }

        $this->db->trans_start();

        $data = array(
            'date' => $datetime->format('Y-m-d H:i:s'),
            'idAppointment' => $appointmentId
        );

        if (!$this->db->insert('DateProposal', $data)) {
            return false;
        }
        $dateProposalId = $this->db->insert_id();

        $data = array();
        foreach ($members as $member) {
            $data[] = array(
                'idDateProposal' => $dateProposalId,
                'idUser' => $member->idUser,
                'accepted' => $member->idUser === $_SESSION['userId'] ? '1' : null
            );
        }

        if (!$this->db->insert_batch('DateAccept', $data)) {
            return false;
        }

        $this->db->trans_complete();

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
