<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends TM_Controller
{
    public function student_index()
    {
        $this->load->model('ptut_model');
        $this->load->helper('time');

        $group = $this->ptut_model->getStudentGroup($_SESSION['id']);
        if (empty($group)) {
            addPageNotification('Vous ne faites pas parti d\'un groupe de projet');
            redirect('/');
        }

        $members = $this->ptut_model->getGroupMembers($group->idGroupe);
        $lastAppointement = $this->ptut_model->getLastAppointement($group->idGroupe);
        $nextAppointement = $this->ptut_model->getNextAppointement($group->idGroupe);
        $proposals = $this->ptut_model->getDateProposals($nextAppointement->idRDV);

        $this->data = array(
            'group' => $group,
            'members' => $members,
            'lastAppointement' => $lastAppointement,
            'nextAppointement' => $nextAppointement,
            'proposals' => $proposals
        );

        $this->show('Projets tuteurés');
    }

    public function teacher_index()
    {
        $this->load->model('ptut_model');

        $ptuts = $this->ptut_model->getPtutsOfProf($_SESSION['id']);

        $this->data = array(
            'ptuts' => $ptuts
        );

        $this->show('Projets tuteurés');
    }

    public function teacher_detail($projectId)
    {
        $projectId = intval(htmlspecialchars($projectId));

        if ($projectId === 0) {
            show_404();
        }

        $this->load->model('ptut_model');
        $this->load->helper('time');

        $group = $this->ptut_model->getGroup($projectId, $_SESSION['id']);
        if (empty($group)) {
            addPageNotification('Projet introuvable', 'warning');
            redirect('/Project');
        }


        $members = $this->ptut_model->getGroupMembers($projectId);
        $lastAppointement = $this->ptut_model->getLastAppointement($projectId);
        $nextAppointement = $this->ptut_model->getNextAppointement($projectId);
        $proposals = $this->ptut_model->getDateProposals($nextAppointement->idRDV);

        $this->data = array(
            'group' => $group,
            'members' => $members,
            'lastAppointement' => $lastAppointement,
            'nextAppointement' => $nextAppointement,
            'proposals' => $proposals
        );

        $this->show('Projets tuteurés');
    }
}
