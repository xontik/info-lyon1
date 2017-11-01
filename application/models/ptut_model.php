<?php
/**
 * Created by PhpStorm.
 * User: Fistouil
 * Date: 01/10/2017
 * Time: 09:35
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Ptut_model extends CI_Model
{

    /**
     * Return the group corresponding to the id,
     * with the teacher as referent.
     *
     * @param int $groupId The group id
     * @param int $teacherId The teacher id
     * @return stdClass The group
     */
    public function getGroup($groupId, $teacherId)
    {
        return $this->db->where('idGroupe', $groupId)
            ->where('idProfesseur', $teacherId)
            ->get('GroupesPTUT')
            ->row();
    }

    /**
     * Return the group to which the student belongs
     *
     * @param $studentId
     * @return stdClass The group
     */
    public function getStudentGroup($studentId) {
        return $this->db->select('*')
            ->from('MembrePTUT')
            ->join('GroupesPTUT', 'idGroupe')
            ->where('numEtudiant', $studentId)
            ->order_by('idGroupe', 'DESC')
            ->get()
            ->row();
    }

    /**
     * Return the members of the group.
     *
     * @param int $groupId The group id
     * @return array The members of the group
     */
    public function getGroupMembers($groupId)
    {
        return $this->db->select('CONCAT(prenom, " ", nom) as nom')
            ->from('GroupesPTUT')
            ->join('MembrePTUT', 'idGroupe')
            ->join('Etudiants', 'numEtudiant')
            ->where('idGroupe', $groupId)
            ->get()
            ->result();
    }

    /**
     * Return the projects where the teacher is referent.
     *
     * @param int $professorId The teacher id
     * @return array The projects
     */
    public function getPtutsOfProf($professorId)
    {
        return $this->db->select('idGroupe, nomGroupe')
            ->from('GroupesPtut')
            ->where('idProfesseur', $professorId)
            ->get()
            ->result();
    }

    /**
     * Computes the last appointement the group had.
     *
     * @param int $groupId The group id
     * @return mixed
     */
    public function getLastAppointement($groupId)
    {
        return $this->db->from('RDVPtut')
            ->where('idGroupe', $groupId)
            ->where('dateFinale IS NOT NULL')
            ->where('dateFinale <= CURDATE()')
            ->order_by('dateFinale', 'DESC')
            ->get()
            ->row();
    }

    /**
     * Computes the next appointement the group will have.
     *
     * @param int $groupId The group id
     * @return mixed
     */
    public function getNextAppointement($groupId)
    {
        return $this->db->from('RDVPtut')
            ->where('idGroupe', $groupId)
            ->group_start()
            ->where('dateFinale IS NULL')
            ->or_where('dateFinale >= CURDATE()')
            ->group_end()
            ->order_by('dateFinale', 'ASC')
            ->get()
            ->row();
    }

    /**
     * Get all date proposals referenced to the appointement.
     *
     * @param int $appointementId The appointement id
     * @return mixed
     */
    public function getDateProposals($appointementId)
    {
        return $this->db->where('idRDV', $appointementId)
            ->get('PropositionsDate')
            ->result();
    }

    /**
     * Creates a date proposal refering to an appointement.
     *
     * @param int $appointementId The appointement
     * @param DateTime $datetime The time of the proposal
     * @param int $userId The user who makes the proposal
     * @return bool
     */
    public function createProposal($appointementId, $datetime, $userId)
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
    public function setProposalAccept($proposalId, $userId, $accept)
    {
        $this->db->set('accepted', $accept)
            ->where('idProposal', $proposalId)
            ->where('idUser', $userId)
            ->update('DateAccept');
    }

    /**
     * Check if user is in a project.
     *
     * @param int $userId The user id
     * @param int $projectId The project id
     * @return bool Whether the user is in the project
     */
    public function isUserInProject($userId, $projectId)
    {
        $userInProject = $this->db->select('userId')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Teacher', 'idTeacher')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idProject', $projectId)
            ->where('userId', $userId)
            ->get()
            ->row();

        return !empty($userInProject);
    }

    /**
     * Send a message to all group members,
     * except the user who is currently connected.
     *
     * @param int $projectId The project id
     * @param string $message The content of the message
     * @param string $type The type of the notification
     * @param string $icon The icon of the notification (optionnal)
     * @param string $link Where notification links to (optionnal)
     */
    public function sendGroupMessage($projectId, $message, $type, $icon = '', $link = '') {
        get_instance()->load->helper('notification');

        $this->db->select('idUser')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Teacher', 'idTeacher')
            ->join('Student', 'studentId')
            ->where('idProject', $projectId);

        if (isset($_SESSION['idUser'])) {
            $this->db->where('idUser !=', $_SESSION['idUser']);
        }

        $users = $this->db->get()
            ->result();

        foreach ($users as $user) {
            addSeenNotification($message, $type, $icon, $link, $user->idUser);
        }
    }

    /**
     * Get group id from another table, related to projects
     *
     * @param string $table The related table ('DateAccept', 'DateProposal', 'Appointement')
     * @param int $idInTable The primary key content
     * @return int
     */
    public function getGroupId($table, $idInTable) {
        $this->db->select('idProject')
            ->from('Project');

        switch ($table) {
            case 'DateAccept':
                $this->db->join('DateAccept', 'idProposal')
                    ->where('idProposal', $idInTable);
            case 'DateProposal':
                $this->db->join('DateProposal', 'idAppointement');
                if ($table === 'DateProposal') {
                    $this->db->where('idProposal', $idInTable);
                }
            case 'Appointement':
                $this->db->join('Appointement', 'idProject');
                if ($table === 'Appointement') {
                    $this->db->where('idAppointement', $idInTable);
                }
                break;
            default:
                return -1;
        }

        return $this->db->get()
            ->row()
            ->idProject;
    }

}
