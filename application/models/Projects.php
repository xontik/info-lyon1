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
     * Returns all projects with a picture.
     *
     * @return array
     */
    public function getAllPictures()
    {
        return $this->db
            ->where('projectPicture IS NOT NULL')
            ->get('Project')
            ->result();
    }

    /**
     * Return the members of the project.
     *
     * @param int $projectId
     * @return array
     */
    public function getMembers($projectId)
    {
        return $this->db->select('idUser, CONCAT(name, " ", surname) as name, idStudent')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idProject', $projectId)
            ->get()
            ->result();
    }

    /**
     * Get the tutor of the project.
     *
     * @param int $projectId
     * @return object|bool FALSE if project doesn't exists
     */
    public function getTutor($projectId)
    {
        $res = $this->db
            ->select('idUser, idTeacher, CONCAT(name, \' \', surname) as name')
            ->from('Project')
            ->join('Teacher', 'idTeacher')
            ->join('User', 'idUser')
            ->where('idProject', $projectId)
            ->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return $res;
    }

    /**
     * Computes the last appointment the project had.
     *
     * @param int $projectId
     * @return object
     */
    public function getLastAppointment($projectId)
    {
        return $this->db->from('Appointment')
            ->where('idProject', $projectId)
            ->where('finalDate IS NOT NULL')
            ->where('finalDate <= NOW()')
            ->order_by('finalDate', 'DESC')
            ->get()
            ->row();
    }

    /**
     * Computes the next appointment the project will have.
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
            ->or_where('finalDate > NOW()')
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

        $isUserInStudent = $this->db
            ->select('idUser')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idProject', $projectId)
            ->where('idUser', $userId)
            ->get()
            ->row();

        if (!is_null($isUserInStudent)) {
            return true;
        }

        $isTeacher = $this->db
            ->select('idUser')
            ->from('Project')
            ->join('Teacher', 'idTeacher')
            ->join('User', 'idUser')
            ->where('idProject', $projectId)
            ->where('idUser', $userId)
            ->get()
            ->row();

        return !is_null($isTeacher);
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
        $this->load->model('Projects');

        $project = $this->Projects->get($projectId);
        if ($project === FALSE) {
            return;
        }

        // Student members
        $this->db->select('idUser')
            ->from('Project')
            ->join('ProjectMember', 'idProject')
            ->join('Student', 'idStudent')
            ->join('User', 'idUser')
            ->where('idProject', $projectId);

        if (isset($_SESSION['userId'])) {
            $this->db->where('idUser !=', $_SESSION['userId']);
        }

        $students = $this->db->get()
            ->result();

        foreach ($students as $student) {
            $this->Notifications->create($message, 'Project', $student->idUser, $type, $icon);
        }

        // Tutor
        $this->db->select('idUser')
            ->from('Project')
            ->join('Teacher', 'idTeacher')
            ->join('User', 'idUser')
            ->where('idProject', $projectId);

        if (isset($_SESSION['userId'])) {
            $this->db->where('idUser !=', $_SESSION['userId']);
        }

        $teacher = $this->db->get()
            ->row();

        if (!is_null($teacher)) {
            $this->Notifications->create('Projet ' . $project->projectName . ' : ' . $message,
                '/Project/appointment/' . $projectId, $teacher->idUser, $type, $icon);
        }
    }

    /**
     * Get project id from another table, related to projects
     *
     * @param string $table The related table ('DateAccept', 'DateProposal', 'Appointment')
     * @param mixed $idInTable The primary key content
     * @return int|bool FALSE if the table doesn't exist or if id doesn't exist in the table
     */
    public function getProjectId($table, $idInTable)
    {
        $this->db
            ->select('idProject')
            ->from('Project');

        switch ($table) {
            case 'DateAccept':
                $this->db
                    ->join('Appointment', 'idProject')
                    ->join('DateProposal', 'idAppointment')
                    ->join('DateAccept', 'idProposal')
                    ->where('idDateAccept', $idInTable);
                break;
            case 'DateProposal':
                $this->db
                    ->join('Appointment', 'idProject')
                    ->join('DateProposal', 'idAppointment')
                    ->join('DateAccept', 'idDateProposal')
                    ->where('idDateProposal', $idInTable);
                break;
            case 'Appointment':
                $this->db
                    ->join('Appointment', 'idProject')
                    ->where('idAppointment', $idInTable);
                break;
            default:
                trigger_error('Unknown table');
                return FALSE;
        }

        $res = $this->db->get()
            ->row();

        if (is_null($res)) {
            return FALSE;
        }
        return (int) $res->idProject;
    }

    public function hasAppointmentSheduled($projectId) {
        return $this->db
            ->from('Appointment')
            ->where('idProject', $projectId)
            ->where('finalDate IS NULL',NULL,false)
            ->get()
            ->num_rows();
    }


    public function create($teacherId) {

        $data = array('idTeacher' => $teacherId);
        $this->db
            ->insert('project', $data);

        return $this->db->affected_rows();
    }

    public function delete($projectId) {

        $this->db
            ->delete('project', array('idProject' => $projectId) );

        return $this->db->affected_rows();
    }

    public function getStudentsWithoutProject(){
        $sql = 'SELECT CONCAT(idStudent, " ",name, " ", surname) as name FROM student
                    JOIN user USING (idUser)
                    JOIN studentgroup USING (idStudent)
                    JOIN `group` USING (idGroup)
                    JOIN semester USING (idSemester)
                    WHERE active = 1 AND idStudent NOT IN (
                        SELECT idStudent FROM projectmember
                            JOIN studentgroup USING (idStudent)
                            JOIN `group` USING (idGroup)
                            JOIN semester USING (idSemester)
                            WHERE active = 1
                    )';

        return array_column($this->db->query($sql)->result_array(), 'name');

    }

    public function addMemeber($projectId, $studentId) {

        $this->db
            ->insert('projectmember', array('idStudent' => $studentId, 'idProject' => $projectId));
        return $this->db->affected_rows();
    }

    public function deleteMemeber($projectId, $studentId) {

        $this->db
            ->delete('projectmember', array('idStudent' => $studentId, 'idProject' => $projectId));
        return $this->db->affected_rows();
    }

    public function changeName($projectId, $projectName) {

        $this->db
            ->where('idProject', $projectId)
            ->update('project', array('projectName' => $projectName));
        return $this->db->affected_rows();

    }



}
