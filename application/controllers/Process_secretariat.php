<?php
class Process_secretariat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
            redirect('/');
    }

    public function ajout_absence()
    {
        if (!isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $data = array(
            'numEtudiant' => htmlspecialchars($_POST['studentId']),
            'dateDebut' => htmlspecialchars($_POST['beginDate']),
            'dateFin' => htmlspecialchars($_POST['endDate']),
            'idTypeAbsence' => htmlspecialchars($_POST['absenceTypeId']),
            'justifiee' => htmlspecialchars($_POST['justified'])
        );

        $errors = $this->_checkAbsenceData($data);

        if (!empty($errors)) {
            echo join(',', $errors);
            return;
        }

        try {
            $this->db->insert('Absences', $data);
            $absenceId = $this->db->select_max('idAbsence')
                ->get('Absences')
                ->row()->idAbsence;
            echo 'success ' . $absenceId;
        } catch(PDOException $e) {
            echo 'exception : ' . $e->getMessage();
        }

    }

    public function modifier_absence()
    {
        header('Content-Type: text/plain');

        if (!isset($_POST['absenceId'])
            ||!isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $absenceId = htmlspecialchars($_POST['absenceId']);
        $data = array(
            'numEtudiant' => htmlspecialchars($_POST['studentId']),
            'dateDebut' => htmlspecialchars($_POST['beginDate']),
            'dateFin' => htmlspecialchars($_POST['endDate']),
            'idTypeAbsence' => htmlspecialchars($_POST['absenceTypeId']),
            'justifiee' => htmlspecialchars($_POST['justified'])
        );

        $errors = $this->_checkAbsenceData($data);
        if ($errors === FALSE) {
            echo 'cancel';
            return;
        }

        if (!empty($errors)) {
            echo join(',', $errors);
            return;
        }

        try {
            $this->db->set($data)
                ->where('idAbsence', $absenceId)
                ->update('Absences', $data);
            echo 'success ' . $absenceId;
        } catch(Exception $e) {
            echo 'exception : ' . $e->getMessage();
        }
    }

    private function _checkAbsenceData($data)
    {
        $errors = array();

        if (empty($data['numEtudiant'])) {
            return false;
        }

        if (empty($data['dateDebut'])) {
            $errors[] = 'beginDate';
        }

        if (empty($data['dateFin'])) {
            $errors[] = 'endDate';
        }

        if ($data['dateDebut'] === $data['dateFin']) {
            $errors[] = 'sameDates';
        }

        if ($data['justifiee'] != 0 && $data['justifiee'] != 1) {
            return false;
        }

        return $errors;
    }
}
