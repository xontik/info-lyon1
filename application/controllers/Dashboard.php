<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends TM_Controller
{
    public function student_index()
    {
        $this->load->model('Students');
        $this->load->model('Semesters');
        $this->load->model('Projects');
        $this->load->model('DateProposals');

        $this->load->helper('timetable');
        $this->load->helper('time');

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



        $semester = $this->Students->getCurrentSemester($_SESSION['id']);
        $period = $this->Semesters->getPeriodObject($semester);
        $now = new DateTime();

        if (!$now->diff($period->getEndDate())->invert) {
            $period->setEndDate($now);
        }

        // Absence
        $this->data['absence'] = $this->Students->getLastAbsence($_SESSION['id'], $period);
        $this->data['absenceCount'] = $this->Students->getAbsencesCount($_SESSION['id'], $period);

        // Mark
        $this->data['mark'] = $this->Students->getLastMark($_SESSION['id'], $semester->idSemester);
        $this->data['average'] = $this->Students->getSubjectsTUAverage($_SESSION['id'], $semester->idSemester);

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
        $this->data['project'] = $project;

        // Question
        $this->data['question'] = $this->Students->getLastAnswer($_SESSION['id']);

        $this->show('Tableau de bord');
    }

}
