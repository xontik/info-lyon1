<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Project extends CI_Controller
{

    public function create() {


        $this->load->model('Projects');
        $this->load->model('Teachers');

        $teacher = $this->Teachers->get($_SESSION['id']);

        if(is_null($teacher)) {
            addPageNotification('Seul un professeur peut créer un projet');
        }


        $lastProject = $this->Teachers->getLastProject($_SESSION['id']);


        if (is_null($lastProject) || !is_null($lastProject->projectName) ) {
            if (!$this->Projects->create($_SESSION['id'])) {
                addPageNotification('Erreur de la création du project', 'danger');
            }
        } else {
            addPageNotification('Merci de completer le dernier project avant d\'en creer un  nouveau', 'warning');
        }

        redirect('/Project');


    }

    /**
     * AJAX
     */

    public function get_student_available($projectId) {

        $this->load->model('Projects');

        
        header('Content-Type: application/json');
        echo json_encode($output);
    }
}
