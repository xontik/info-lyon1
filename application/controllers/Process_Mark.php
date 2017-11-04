<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Mark extends CI_Controller
{

    public function add($controlId)
    {
        $controlId = (int) htmlspecialchars($controlId);
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('Marks');
        $this->load->model('Controls');
        $this->load->model('Teacher');

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('Control');
        }

        $control = $this->Controls->get($controlId);
        $marks = $this->Controls->getMarks($control, $_SESSION['id']);

        $i = 0;
        $correctData = true;
        foreach ($_POST as $key => $value) {
            if ($key !== $marks[$i]->numEtudiant) {
                $correctData = false;
                break;
            }
            $i++;
        }

        if (!$correctData) {
            addPageNotification('Données corrompues', 'danger');
            redirect('Control');
        }

        //TODO Ajouter verification sur value
        $this->Marks->createAll($_POST, $controlId);

        addPageNotification('Note modifiées avec succès', 'success');
        redirect('Control');
    }
}