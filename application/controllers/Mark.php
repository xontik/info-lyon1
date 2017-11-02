<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mark extends TM_Controller
{
    public function student_index($semester = '')
    {
        if (!preg_match('/^S[1-4]$/', $semester)) {
            $semester = '';
        }

        $this->load->model('mark_model');
        $this->load->model('semester_model');

        // Loads the max semester type the student went to
        $max_semester = intval(
            substr($this->semester_model->getSemesterTypeFromId(
                $this->semester_model->getCurrentSemesterId($_SESSION['id'])
            ), 1)
        );

        if ($semester > 'S' . $max_semester) {
            addPageNotification('Vous essayez d\'accéder à un semestre futur !<br>Redirection vers votre semestre courant');
            $semester = '';
        }

        $semesterId = $this->semester_model->getSemesterId($semester, $_SESSION['id']);
        $semester = $this->semester_model->getSemesterTypeFromId($semesterId);

        $marks = $this->mark_model->getMarksFromSemester($_SESSION['id'], $semesterId);

        $this->data = array(
            'maxSemester' => $max_semester,
            'semesterType' => $semester,
            'basePage' => 'Mark',
            'marks' => $marks
        );

        $this->show('Notes');
    }

    public function teacher_add($controlId)
    {
        $controlId = intval(htmlspecialchars($controlId[0]));
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');
        $this->load->model('mark_model', 'markMod');

        $control = $this->ctrlMod->getControl($controlId);
        if (empty($control)) {
            addPageNotification('Contrôle introuvable', 'danger');
            redirect('/Control');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $controlId)) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('/Control');
        }

        $marks = $this->markMod->getMarks($control, $_SESSION['id']);
        $matiere = $this->ctrlMod->getMatiere($controlId);

        $this->data = array(
            'control' => $control,
            'marks' => $marks,
            'matiere' => $matiere
        );

        $this->show('Ajout de notes');
    }
}
