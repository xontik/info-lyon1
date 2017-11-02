<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Mark extends CI_Controller
{

    public function add($id)
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');
        $this->load->model('mark_model', 'markMod');

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('Control');
        }

        $control = $this->ctrlMod->getControl($id);
        $marks = $this->markMod->getMarks($control, $_SESSION['id']);

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
            addPageNotification('Données reçues incohérentes', 'warning');
            redirect('Control');
        }

        //TODO Ajouter verification sur value
        $this->markMod->addMarks($id, $_POST);

        addPageNotification('Note modifiées avec succès', 'success');
        redirect('Control');
    }
}