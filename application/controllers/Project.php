<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends TM_Controller
{
    public function student_index()
    {
        $this->load->model('Students');
        $this->load->model('Projects');
        $this->load->model('DateProposals');

        $this->load->helper('time');

        $project = $this->Students->getProject($_SESSION['id']);
        if ($project === FALSE) {
            addPageNotification('Vous ne faites pas parti d\'un groupe de projet');
            redirect('/');
        }

        $members = $this->Projects->getMembers($project->idProject);
        $lastAppointment = $this->Projects->getLastAppointment($project->idProject);
        $nextAppointment = $this->Projects->getNextAppointment($project->idProject);
        $proposals = $this->DateProposals->getAll($nextAppointment->idAppointment);

        $this->data = array(
            'project' => $project,
            'members' => $members,
            'lastAppointment' => $lastAppointment,
            'nextAppointment' => $nextAppointment,
            'proposals' => $proposals
        );

        $this->show('Projets tuteurés');
    }

    public function teacher_index()
    {
        $this->load->model('Teachers');

        $projects = $this->Teachers->getProjects($_SESSION['id']);

        $this->data = array(
            'projects' => $projects
        );

        $this->show('Projets tuteurés');
    }

    public function teacher_detail($projectId)
    {
        $projectId = (int) htmlspecialchars($projectId);

        if ($projectId === 0) {
            show_404();
        }

        $this->load->model('Teachers');
        $this->load->model('Projects');
        $this->load->model('DateProposals');

        $this->load->helper('time');

        $project = $this->Projects->get($projectId);
        if ($project === FALSE) {
            addPageNotification('Projet introuvable', 'warning');
            redirect('Project');
        }
        if (!$this->Teachers->isTutor($projectId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas accès à ce projet tuteuré', 'danger');
            redirect('Project');
        }

        $members = $this->Projects->getMembers($projectId);
        $lastAppointment = $this->Projects->getLastAppointment($projectId);
        $nextAppointment = $this->Projects->getNextAppointment($projectId);
        $proposals = $this->DateProposals->getAll($nextAppointment->idAppointment);

        $this->data = array(
            'project' => $project,
            'members' => $members,
            'lastAppointment' => $lastAppointment,
            'nextAppointment' => $nextAppointment,
            'proposals' => $proposals
        );

        $this->show('Projets tuteurés');
    }
}
