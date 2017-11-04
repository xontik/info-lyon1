<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Model
{

    /**
     * Return the project corresponding to the id.
     *
     * @param int $projectId
     * @return object|bool FALSE if project if doesn't exist
     */
    public function get($projectId)
    {
        $res = $this->db->where('idProject', $projectId)
            ->get('Project')
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Return the members of the project.
     *
     * @param int $projectId
     * @return array
     */
    public function getMembers($projectId)
    {
        return $this->db->select('CONCAT(name, " ", surname) as name')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idProject', $projectId)
            ->get()
            ->result();
    }

    /**
     * Computes the last appointement the project had.
     *
     * @param int $projectId
     * @return object
     */
    public function getLastAppointment($projectId)
    {
        return $this->db->from('Appointment')
            ->where('idProject', $projectId)
            ->where('finalDate IS NOT NULL')
            ->where('finalDate <= CURDATE()')
            ->order_by('finalDate', 'DESC')
            ->get()
            ->row();
    }

    /**
     * Computes the next appointement the project will have.
     *
     * @param int $projectId
     * @return object
     */
    public function getNextAppointment($projectId)
    {
        return $this->db->from('Appointment')
            ->where('idProject', $projectId)
            ->group_start()
            ->where('finalDate IS NULL')
            ->or_where('finalDate >= CURDATE()')
            ->group_end()
            ->order_by('finalDate', 'ASC')
            ->get()
            ->row();
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
     * Send a message to all the members of the project,
     * except the user who is currently connected.
     *
     * @param int $projectId The project id
     * @param string $message The content of the message
     * @param string $type The type of the notification
     * @param string $icon The icon of the notification (optionnal)
     */
    public function sendProjectMessage($projectId, $message, $type = 'info', $icon = '')
    {
        $this->load->model('Notifications');

        // Student members
        $this->db->select('idUser')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idProject', $projectId);

        if (isset($_SESSION['idUser'])) {
            $this->db->where('idUser !=', $_SESSION['idUser']);
        }

        $students = $this->db->get()
            ->result();

        foreach ($students as $student) {
            $this->Notifications->create($message, '/Project', $student->idUser, $type, $icon);
        }

        // Tutor
        $this->db->select('idUser')
            ->from('Project')
            ->join('Teacher', 'idTeacher')
            ->join('User', 'idUser')
            ->where('idProject', $projectId);

        if (isset($_SESSION['idUser'])) {
            $this->db->where('idUser !=', $_SESSION['idUser']);
        }

        $teacher = $this->db->get()
            ->row();

        if (!is_null($teacher)) {
            $this->Notifications->create($message, '/Project/detail/' . $projectId, $teacher, $type, $icon);
        }
    }

    /**
     * Get project id from another table, related to projects
     *
     * @param string $table The related table ('DateAccept', 'DateProposal', 'Appointement')
     * @param mixed $idInTable The primary key content
     * @return int|bool FALSE if the table doesn't exist or if id doesn't exist in the table
     */
    public function getProjectId($table, $idInTable)
    {
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
                return FALSE;
        }


        $res = $this->db->get()
            ->row();

        if (empty($res)) {
            return FALSE;
        }
        return (int) $res->idProject;
    }

}
