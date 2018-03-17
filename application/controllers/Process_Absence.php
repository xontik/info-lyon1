<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Absence extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!(isset($_SESSION['userType'])
            && in_array($_SESSION['userType'], $this->config->item('userTypes')))
        ) {
            header('Content-Length: 0', TRUE, 403);
            exit(0);
        }
    }

    /*
     * AJAX
     */
    public function add()
    {
        header('Content-Type: text/plain');

        if (!(isset($_POST['studentId'])
            && isset($_POST['beginDate'])
            && isset($_POST['endDate'])
            && isset($_POST['absenceTypeId'])
            && isset($_POST['justified']))
        ) {
            echo 'missing_data';
            return;
        }

        $studentId = htmlspecialchars($_POST['studentId']);
        $beginDate = htmlspecialchars($_POST['beginDate']);
        $endDate = htmlspecialchars($_POST['endDate']);
        $idAbsenceType = (int) htmlspecialchars($_POST['absenceTypeId']);
        $justified = htmlspecialchars($_POST['justified']) === "true";

        if (!$this->_checkAbsenceData($beginDate, $endDate, $idAbsenceType, $justified)) {
            echo 'wrong_data';
            return;
        }

        $this->load->model('Absences');
        $absenceId = $this->Absences->create($studentId, $beginDate, $endDate, $idAbsenceType, $justified);
        if ($absenceId === FALSE) {
            echo 'fail';
        }

        echo 'success ' . $absenceId;
    }

    /*
     * AJAX
     */
    public function update()
    {
        $this->load->model('Absences');

        header('Content-Type: text/plain');

        if (!isset($_POST['absenceId'])
            || !isset($_POST['studentId'])
            || !isset($_POST['beginDate'])
            || !isset($_POST['endDate'])
            || !isset($_POST['absenceTypeId'])
            || !isset($_POST['justified'])
        ) {
            echo 'missing_data';
            return;
        }

        $absenceId = (int) htmlspecialchars($_POST['absenceId']);
        $beginDate = htmlspecialchars($_POST['beginDate']);
        $endDate = htmlspecialchars($_POST['endDate']);
        $idAbsenceType = (int) htmlspecialchars($_POST['absenceTypeId']);
        $justified = htmlspecialchars($_POST['justified']) === "true";

        if (!$this->_checkAbsenceData($beginDate, $endDate, $idAbsenceType, $justified)) {
            echo 'wrong_data';
            return;
        }

        if ($this->Absences->update($absenceId, $beginDate, $endDate, $idAbsenceType, $justified)) {
            echo 'success ' . $absenceId;
        } else {
            echo 'fail';
        }
    }

    /*
     * AJAX
     */
    public function delete()
    {
        $this->load->model('Absences');

        header('Content-Type: text/plain');

        if (!isset($_POST['absenceId'])) {
            echo 'missing_data';
            return;
        }

        $absenceId = (int) htmlspecialchars($_POST['absenceId']);

        if ($this->Absences->delete($absenceId)) {
            echo 'success';
        } else {
            echo 'fail';
        }
    }

    private function _checkAbsenceData($beginDate, $endDate, $idAbsenceType, $justified)
    {
        return !empty($beginDate)
            && !empty($endDate)
            && $beginDate !== $endDate
            && $idAbsenceType !== 0
            && ($justified == 0 || $justified == 1);
    }

}