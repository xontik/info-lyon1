<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_DateProposal extends CI_Controller
{

    public function add($groupId)
    {
        $groupId = (int) htmlspecialchars($groupId);

        $this->load->model('DateProposals');
        $this->load->model('Projects');

        if ($groupId !== 0
            && isset($_POST['date'])
            && isset($_POST['time'])
        ) {
            $datetime = new DateTime(
                htmlspecialchars($_POST['date'])
                . ' ' . htmlspecialchars($_POST['time'])
            );
            $appointmentId = $this->Projects->getNextAppointment($groupId)->idAppointment;

            if ($this->DateProposals->create($appointmentId, $datetime, $_SESSION['userId'])) {
                addPageNotification('Proposition ajoutée avec succès', 'success');
            } else {
                addPageNotification('Impossible de créer la proposition de rendez-vous', 'danger');
            }
        } else {
            addPageNotification('Données reçu corrompues', 'danger');
        }

        redirect('Project/detail/' . $groupId);
    }

    public function choose($dateProposalId)
    {
        $dateProposalId = (int) htmlspecialchars($dateProposalId);

        $this->load->model('Projects');
        $this->load->model('Appointments');
        $this->load->model('DateProposals');

        $redirectUrl = 'Project';
        if ($dateProposalId !== 0) {

            $projectId = $this->Projects->getProjectId('DateProposal', $dateProposalId);
            if ($projectId === false) {
                redirect('/Project')
            }

            if ($_SESSION['userType'] === 'teacher') {
                $redirectUrl .= '/' . $projectId;
            }

            if (isset($_POST['accept'])) {
                $accept = true;
            } else if (isset($_POST['decline'])) {
                $accept = false;
            } else {
                addPageNotification('Données corrompues', 'danger');
                redirect($redirectUrl);
            }

            $this->DateProposals->setAccept($dateProposalId, $_SESSION['userId'], $accept);

            if ($accept) {
                if ($this->DateProposals->isAccepted($dateProposalId)) {

                    $this->Appointments->setFinalDate($dateProposalId);
                    $this->Projects->sendProjectMessage(
                        $projectId,
                        'Une proposition de date à été acceptée',
                        'success'
                    );
                }
            } else {
                $this->Projects->sendProjectMessage(
                    $projectId,
                    'Une proposition de date à été refusée',
                    'warning'
                );
            }

        }

        redirect($redirectUrl);
    }
}
