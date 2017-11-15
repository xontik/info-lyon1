<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Appointment extends CI_Controller
{


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
            redirect('Project/detail/'.$projectId);
        } else {
            if ($this->Appointments->create($projectId)){
                addPageNotification('Un rendez-vous a été créé', 'success');
            } else {
                addPageNotification('Impossible de creer ce rendez-vous', 'danger');
            }


            redirect('Project/detail/'.$projectId);
        }



    }
}
