<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_DateProposal extends CI_Controller
{

    public function add() {
        $this->load->model('ptut_model');

        if (isset($_POST['groupId'])
            && isset($_POST['date'])
            && isset($_POST['time'])
        ) {
            $groupId = intval(htmlspecialchars($_POST['groupId']));
            $date = htmlspecialchars($_POST['date']);
            $time = htmlspecialchars($_POST['time']);
            $appointementId = $this->ptut_model->getNextAppointement($groupId)->idAppointement;

            $datetime = new DateTime($date . ' ' . $time);

            $this->ptut_model->createProposal($appointementId, $datetime, $_SESSION['userId']);
        }

        redirect('Project' . (isset($groupId) ? '/detail/' . $groupId : ''));
    }

    public function choose() {
        $this->load->model('ptut_model');

        if (isset($_POST['proposalId'])) {
            $proposalId = intval(htmlspecialchars($_POST['proposalId']));

            if (isset($_POST['accept'])) {
                $accept = true;
            } else if ($_POST['decline']) {
                $accept = false;
            } else {
                redirect('/Professeur/Ptut');
            }

            $this->ptut_model->setProposalAccept($proposalId, $_SESSION['userId'], $accept);

            if ($accept) {
                //TODO If everyone accepted, set project reunion final date
            } else {
                $groupId = $this->ptut_model->getGroupId('DateProposal', $proposalId);
                $this->ptut_model->sendGroupMessage($groupId, 'Une proposition de date à été refusée', 'warning');
            }
        }

        redirect('Project');
    }
}