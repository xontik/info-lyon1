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
        $this->load->model('Teachers');

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('Control');
        }

        $control = $this->Controls->get($controlId);
        $marks = $this->Controls->getMarks($control, $_SESSION['id']);

        $i = 0;
        $correctData = true;

        foreach ($_POST as $key => $value) {
            if ($key !== $marks[$i]->idStudent) {
                $correctData = false;
                break;
            }
            $i++;
        }

        if (!$correctData) {
            addPageNotification('Données corrompues', 'danger');
            redirect('Mark/add/' . $controlId);
        }

        //TODO Ajouter verification sur value
        if (!$this->Marks->createAll($_POST, $controlId)) {
            addPageNotification('Erreur lors de la modification des notes', 'danger');
            redirect('Mark/add/' . $controlId);
        }

        addPageNotification('Note modifiées avec succès', 'success');
        redirect('Control');
    }
}