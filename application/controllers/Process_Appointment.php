<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Appointment extends CI_Controller
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

    public function create($projectId) {

        $projectId = (int) htmlspecialchars($projectId);

        if ($projectId === 0) {
            show_404();
        }

        $this->load->model('Projects');
        $this->load->model('Appointments');

        $project = $this->Projects->get($projectId);

        if ($project === FALSE) {
            addPageNotification('Projet introuvable', 'warning');
            redirect('Project');
        }

        if (!$this->Projects->isUserInProject($_SESSION['userId'], $projectId)) {
            addPageNotification('Vous ne faites pas parti du projet', 'warning');
            redirect('Project');
        }

        if ($this->Projects->hasAppointmentSheduled($projectId)) {
            addPageNotification('Un rendez-vous existe déjà', 'warning');
            redirect('Project/appointment/'.$projectId);
        } else {
            if ($this->Appointments->create($projectId)){
                addPageNotification('Un rendez-vous a été créé', 'success');
                $this->Projects->sendProjectMessage($projectId, 'Vous avez une demande de rendez-vous');
            } else {
                addPageNotification('Impossible de creer ce rendez-vous', 'danger');
            }
            redirect('Project/appointment/'.$projectId);
        }
    }

    public function delete($appointmentId) {

        $this->load->model('Appointments');
        $this->load->model('Projects');

        $appointment = $this->Appointments->get($appointmentId);
        if (is_null($appointment)) {
            addPageNotification('Ce rendez-vous n\'existe pas', 'danger');
            redirect('/Project');
        }

        if (!$this->Projects->isUserInProject($_SESSION['userId'], $appointment->idProject)) {
            addPageNotification('Vous ne faites pas parti de ce projet', 'danger');
            redirect('/Project');
        }

        $finalDate = new DateTime($appointment->finalDate);
        $now = new DateTime();

        if(!$now->diff($finalDate)->invert) {
            if ($this->Appointments->delete($appointmentId)) {
                addPageNotification('Rendez-vous supprimé', 'success');
                $this->Projects->sendProjectMessage($appointment->idProject, 'Le rendez-vous a été annulé', 'warning');
            } else {
                addPageNotification('Erreur de suppression du rendez-vous', 'danger');
            }
        } else {
            addPageNotification('Impossible de supprimer un rendez-vous terminé', 'danger');
        }

        redirect('/Project/appointment/'.$appointment->idProject);

    }
}
