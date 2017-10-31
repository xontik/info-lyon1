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

    public function getGroup($groupId, $teacherId)
    {
        return $this->db->where('idGroupe', $groupId)
            ->where('idProfesseur', $teacherId)
            ->get('GroupesPTUT')
            ->row();
    }

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

    public function getPtutsOfProf($professorId)
    {
        return $this->db->select('idGroupe, nomGroupe')
            ->from('GroupesPtut')
            ->where('idProfesseur', $professorId)
            ->get()
            ->result();
    }

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

    public function setProposalAccept($proposalId, $userId, $accept)
    {
        // Check if user belongs to the project
        $projectId = $this->getGroupId('DateProposal', $proposalId);

        if (!$this->isUserInProject($userId, $projectId)) {
            redirect('/project/' . $projectId);
        }

        $this->db->set('accepted', $accept)
            ->where('idProposal', $proposalId)
            ->where('idUser', $userId)
            ->update('DateAccept');
    }

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

    public function sendGroupMessage($projectId, $message, $type) {
        get_instance()->load->helper('notification');

        $users = $this->db->select('idUser')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Teacher', 'idTeacher')
            ->join('Student', 'studentId')
            ->where('idProject', $projectId)
            ->get()
            ->result();

        foreach ($users as $user) {
            addSeenNotification($message, $type, '', $user->idUser);
        }
    }

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
