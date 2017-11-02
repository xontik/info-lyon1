<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Control extends TM_Controller
{
    public function teacher_index()
    {
        $this->load->model('control_model','ctrlMod');

        $controls = $this->ctrlMod->getControls($_SESSION['id']);
        $matieres = $this->ctrlMod->getMatieres($_SESSION['id']);
        $groupes = $this->ctrlMod->getGroupes($_SESSION['id']);
        $typeControle = $this->ctrlMod->getTypeControle();

        $restrict = array(
            'typeControle' => isset($_POST['typeControle']) ? intval(htmlspecialchars($_POST['typeControle'])) : 0,
            'groupes' => isset($_POST['groupes']) ? intval(htmlspecialchars($_POST['groupes'])) : 0,
            'matieres' => isset($_POST['matieres']) ? intval(htmlspecialchars($_POST['matieres'])) : 0
        );

        foreach ($controls as $key => $control) {
            if (!is_null($control->nomGroupe)
                && $restrict['groupes'] !== 0
                && $control->idGroupe != $restrict['groupes']
            ) {
                unset($controls[$key]);
            }

            if ($restrict['matieres'] !== 0
                && $control->idMatiere != $restrict['matieres']
            ) {
                unset($controls[$key]);
            }

            if ($restrict['typeControle'] !== 0
                && $restrict['typeControle'] != $control->idTypeControle
            ) {
                unset($controls[$key]);
            }
        }

        $this->data = array(
            'controls' => $controls,
            'groupes' => $groupes,
            'matieres' => $matieres,
            'restrict' => $restrict,
            'typeControle' => $typeControle
        );

        $this->show('Contrôles');
    }

    public function teacher_add($promo = '')
    {
        $this->load->model('control_model', 'ctrlMod');

        $isPromo = strtolower($promo) === 'promo';

        if ($promo === '') {
            $select = $this->ctrlMod->getEnseignements($_SESSION['id']);
        } else if ($isPromo) {
            $select = $this->ctrlMod->getMatieres($_SESSION['id']);
        } else {
            show_404();
            return;
        }

        $typeControle = $this->ctrlMod->getTypeControle();

        $this->data = array(
            'select' => $select,
            'promo' => $isPromo,
            'typeControle' => $typeControle
        );

        $this->show('title', 'Ajout de controle');
    }

    public function teacher_edit($controlId)
    {
        $controlId = intval(htmlspecialchars($controlId[0]));
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');

        $control = $this->ctrlMod->getControl($controlId);
        if (empty($control)) {
            addPageNotification('Controle introuvable', 'danger');
            redirect('Controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $controlId)) {
            addPageNotification('Vous n\'avez pas les droit sur ce controle', 'danger');
            redirect('Controle');
        }

        $typeControle = $this->ctrlMod->getTypeControle();

        $this->data = array(
            'control' => $control,
            'typeControle' => $typeControle
        );

        $this->show('Edition de contrôle');
    }
}
