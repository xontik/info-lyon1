<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends TM_Controller
{
    public function student_index()
    {
        $this->load->model('Students');
        $this->load->model('Projects');
        $this->load->model('DateProposals');

        $this->load->helper('timetable');

        // Timetable
        $adeResource = $this->Students->getADEResource($_SESSION['id']);

        if ($adeResource === FALSE) {
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                array(
                    'date' => new DateTime(),
                    'timetable' => false,
                    'minTime' => '00:00',
                    'maxTime' => '01:00'
                ),
                TRUE
            );
        } else {
            $result = getNextTimetable($adeResource, 'day');
            $sideTimetable = $this->load->view(
                'includes/side-timetable',
                $result,
                TRUE
            );
        }
        $this->data['side-timetable'] = $sideTimetable;

        // Absence
        $this->data['absence'] = $this->Students->getLastAbsence($_SESSION['id']);

        // Mark
        $this->data['mark'] = $this->Students->getLastMark($_SESSION['id']);

        // Project
        $project = $this->Students->getProject($_SESSION['id']);
        if ($project === FALSE) {
            $this->data['appointment'] = false;
        } else {
            $appointment = $this->Projects->getNextAppointment($project->idProject);
            $nextDateProposal = null;

            if (is_null($appointment->finalDate)) {
                $nextDateProposal = $this->DateProposals->getNext($appointment->idAppointment);
            }

            $this->data['appointment'] = $appointment;
            $this->data['nextDateProposal'] = $nextDateProposal;
        }

        // Question
        $this->data['question'] = $this->Students->getLastAnswer($_SESSION['id']);

        $this->show('Tableau de bord');
    }

}
