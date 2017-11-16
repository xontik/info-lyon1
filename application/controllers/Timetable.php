<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends TM_Controller
{
    private function _index($adeResource, $weekNum)
    {
        $this->load->helper('timetable');

        if ($adeResource === FALSE) {
            $this->data = array(
                'date' => new DateTime(),
                'timetable' => false,
                'minTime' => '00:00',
                'maxTime' => '01:00'
            );
            $this->data['loaded'] = false;
        } else {
            $timetableDate = is_numeric($weekNum) ? $weekNum : new DateTime();
            $this->data = getNextTimetable($adeResource, 'week', $timetableDate);
            $this->data['loaded'] = true;
        }
        $this->data['weekNum'] = $this->data['date']->format('W');
        $this->data['resource'] = $adeResource;
        $this->data['pageUrl'] = 'Timetable/';

        $this->data['menu'] = array(
            'Revenir à aujourd\'hui' => $this->data['pageUrl'],
            'Mettre à jour' => 'Process_Timetable/update/' . $adeResource . '/' . $weekNum,
            'Modifier' => 'Timetable/edit',
            'Salles' => 'Timetable/room'
        );

        $this->setData(array(
            'view' => 'Common/timetable.php',
            'css' => 'Common/timetable',
            'js' => 'Common/timetable'
        ));
        $this->show('Emploi du temps');
    }

    private function _edit($type, $who)
    {
        $type = htmlspecialchars($type);
        switch ($type) {
            case '':
                $type = null;
                $who = null;
                break;
            case 'group':
            case 'teacher':
                $who = (int) htmlspecialchars($who);
                break;
            case 'room':
                $who = htmlspecialchars($who);
                break;
            default:
                addPageNotification('Données corrompues', 'danger');
                redirect('Timetable');
        }

        $this->data = array(
            'type' => $type,
            'who' => $who
        );

        $this->setData('view', 'Common/timetable_edit.php');
        $this->show('Modification de l\'emploi du temps');
    }

    private function _room($roomName, $weekNum) {
        if ($roomName === '') {
            $this->_roomDashboard();
        } else {
            $this->_roomDetails($roomName, $weekNum);
        }
    }

    private function _roomDashboard() {
        $this->load->model('Rooms');

        $this->data['rooms'] = $this->Rooms->getAll();

        $this->setData('view', 'Common/timetable_dashboard');
        $this->show('Emploi du temps de salles');
    }

    private function _roomDetails($roomName, $weekNum) {
        $this->load->model('Rooms');

        $roomName = htmlspecialchars($roomName);
        $weekNum = (int) htmlspecialchars($weekNum);

        $adeResource = $this->Rooms->getAdeResource($roomName);

        if ($adeResource === FALSE) {
            addPageNotification('Salle inconnue', 'warning');
            redirect('Timetable/room');
        }

        $this->load->helper('timetable');

        if ($adeResource === FALSE) {
            $this->data = array(
                'date' => new DateTime(),
                'timetable' => false,
                'minTime' => '00:00',
                'maxTime' => '01:00'
            );
            $this->data['loaded'] = false;
        } else {
            $timetableDate = $weekNum ? $weekNum : new DateTime();
            $this->data = getNextTimetable($adeResource, 'week', $timetableDate);
            $this->data['loaded'] = true;
        }
        $this->data['weekNum'] = $this->data['date']->format('W');
        $this->data['resource'] = $adeResource;
        $this->data['pageUrl'] = "Timetable/room/$roomName/";

        $this->data['menu'] = array(
            'Revenir à aujourd\'hui' => $this->data['pageUrl'],
            'Mettre à jour' => "Process_Timetable/update/$adeResource/$weekNum/$roomName",
            'Modifier' => "Timetable/edit/room/$roomName",
            'Retour' => 'Timetable/room'
        );

        $this->setData(array(
            'view' => 'Common/timetable.php',
            'css' => 'Common/timetable',
            'js' => 'Common/timetable'
        ));
        $this->show('Salle ' . $roomName);

    }

    public function student_index($weekNum = '')
    {
        $this->load->model('Students');

        $adeResource = $this->Students->getADEResource($_SESSION['id']);

        $this->_index($adeResource, $weekNum);
    }

    public function student_edit($type = '', $who = '')
    {
       $this->_edit($type, $who);
    }

    public function student_room($roomName = '', $weekNum = 0) {
        $this->_room($roomName, $weekNum);
    }

    public function teacher_index($weekNum = '')
    {
        $this->load->model('Teachers');

        $adeResource = $this->Teachers->getADEResource($_SESSION['id']);

        $this->_index($adeResource, $weekNum);
    }

    public function teacher_edit($type = '', $who = '')
    {
        $this->_edit($type, $who);
    }

    public function teacher_room($roomName = '', $weekNum = 0) {
        $this->_room($roomName, $weekNum);
    }
}
