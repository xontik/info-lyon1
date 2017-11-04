<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Control extends TM_Controller
{
    public function teacher_index()
    {
        $this->load->model('Teachers');
        $this->load->model('Controls');

        $controls = $this->Teachers->getControls($_SESSION['id']);
        $subjects = $this->Teachers->getSubjects($_SESSION['id']);
        $groups = $this->Teachers->getGroups($_SESSION['id']);
        $controlTypes = $this->Controls->getTypes();

        $restrict = array(
            'controlType' => isset($_POST['controlTypeId']) ? (int) htmlspecialchars($_POST['controlTypeId']) : 0,
            'group' => isset($_POST['groupId']) ? (int) htmlspecialchars($_POST['groupId']) : 0,
            'subject' => isset($_POST['subjectId']) ? (int) htmlspecialchars($_POST['subjectId']) : 0
        );

        foreach ($controls as $key => $control)
        {
            if ((!is_null($control->groupName)
                    && $restrict['group'] !== 0
                    && $control->idGroup != $restrict['group']
                )
                || ($restrict['subject'] !== 0
                    && $control->idSubject != $restrict['subject']
                )
                || ($restrict['controlType'] !== 0
                    && $restrict['controlType'] != $control->idControlType
                )
            ) {
                unset($controls[$key]);
            }
        }

        $this->data = array(
            'controls' => $controls,
            'groups' => $groups,
            'subjects' => $subjects,
            'restrict' => $restrict,
            'controlTypes' => $controlTypes
        );

        $this->show('Contrôles');
    }

    public function teacher_add($promo = '')
    {
        $this->load->model('Teachers');
        $this->load->model('Controls');

        $isPromo = strtolower($promo) === 'promo';

        if ($promo === '') {
            $select = $this->Teachers->getEducations($_SESSION['id']);
        } else if ($isPromo) {
            $select = $this->Teachers->getSubjects($_SESSION['id']);
        } else {
            show_404();
            return;
        }

        $controlTypes = $this->Controls->getTypes();

        $this->data = array(
            'select' => $select,
            'promo' => $isPromo,
            'controlTypes' => $controlTypes
        );

        $this->show('Ajout de controle');
    }

    public function teacher_edit($controlId)
    {
        $controlId = (int) htmlspecialchars($controlId[0]);
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('Teachers');
        $this->load->model('Controls');

        $control = $this->Controls->get($controlId);
        if (empty($control)) {
            addPageNotification('Controle introuvable', 'danger');
            redirect('Controle');
        }

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas les droit sur ce controle', 'danger');
            redirect('Controle');
        }

        $controlTypes = $this->Controls->getTypes();

        $this->data = array(
            'control' => $control,
            'controlTypes' => $controlTypes
        );

        $this->show('Edition de contrôle');
    }
}
