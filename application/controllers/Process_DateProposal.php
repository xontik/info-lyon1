<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_DateProposal extends CI_Controller
{

    public function add($projectId)
    {
        $projectId = (int) htmlspecialchars($projectId);

        $this->load->model('DateProposals');
        $this->load->model('Projects');
        $project = $this->Projects->get($projectId);
        if ($project === FALSE) {
            addPageNotification('Projet introuvable', 'warning');
            redirect('Project');
        }

        $members = $this->Projects->getMembers($projectId);
        if (!count($members)) {
            addPageNotification('Projet ne comportant aucun étudiant', 'warning');
            redirect('Project');
        }
        if (
            && isset($_POST['date'])
            && isset($_POST['time'])
        ) {
            $datetime = new DateTime(
                htmlspecialchars($_POST['date'])
                . ' ' . htmlspecialchars($_POST['time'])
            );
            $now = new DateTime();
            if ($datetime > $now) {
                if ($this->Projects->hasAppointmentSheduled($projectId)) {
                    $appointmentId = $this->Projects->getNextAppointment($projectId)->idAppointment;
                    if ($this->DateProposals->create($appointmentId, $datetime, $_SESSION['userId'])) {
                        addPageNotification('Proposition ajoutée avec succès', 'success');
                        $this->Projects->sendProjectMessage($projectId, 'Nouvelle proposition de date pour un projet');
                    } else {
                        addPageNotification('Impossible de créer la proposition de rendez-vous', 'danger');
                    }
                } else {
                    addPageNotification('Impossible de créer la proposition de rendez-vous, car un rendez est déja prevu', 'warning');
                }
            } else {
                addPageNotification('Impossible de créer la proposition de rendez-vous : date dans le passé', 'danger');
            }
        } else {
            addPageNotification('Données reçu corrompues', 'danger');
        }

        redirect('Project/appointment/' . $projectId);
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
                redirect($redirectUrl);
            }

            if ($_SESSION['userType'] === 'teacher') {
                $redirectUrl .= '/appointment/' . $projectId;
            }

            if (isset($_POST['accept'])) {
                $accept = true;
            } else if (isset($_POST['decline'])) {
                $accept = false;
            } else {
                addPageNotification('Données corrompues', 'danger');
                redirect($redirectUrl);
            }
            //TODO cascade refuse
            $this->DateProposals->setAccept($dateProposalId, $_SESSION['userId'], $accept);

            if ($accept) {
                if ($this->DateProposals->isAccepted($dateProposalId)) {

                    $this->Appointments->setFinalDate($dateProposalId);

                    $this->Projects->sendProjectMessage($projectId
                        ,
                        'Un rdv a été validé !',
                        'success'
                    );

                }

            } else {
                $this->Projects->sendProjectMessage($projectId
                    ,
                    'Une proposition de date à été refusée',
                    'warning'
                );
            }

        }

        redirect($redirectUrl);
    }
}
