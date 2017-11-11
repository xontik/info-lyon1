<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mark extends TM_Controller
{
    public function student_index($semester = '')
    {
        if (!preg_match('/^S[1-4]$/', $semester)) {
            $semester = '';
        }

        $this->load->model('Students');
        $this->load->model('Marks');
        $this->load->model('Semesters');

        // Loads the max semester type the student went to
        $max_semester = (int) substr($this->Semesters->getType(
                $this->Semesters->getStudentCurrent($_SESSION['id'])
            ), 1
        );

        if ($semester !== '' && $semester > 'S' . $max_semester) {
            addPageNotification('Vous essayez d\'accéder à un semestre futur !<br>Redirection vers le semestre courant');
            $semester = '';
        }

        $semesterId = $this->Semesters->getSemesterId($semester, $_SESSION['id']);
        $semester = $this->Semesters->getType($semesterId);

        $marks = $this->Semesters->getStudentMarks($_SESSION['id'], $semesterId);

        $this->data = array(
            'semesterTabs' => array(
                'max' => $max_semester,
                'semester' => $semester,
                'basePage' => 'Mark',
            ),
            'marks' => $marks
        );

        $this->show('Notes');
    }

    public function teacher_add($controlId)
    {
        $controlId = intval(htmlspecialchars($controlId));
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('Teachers');
        $this->load->model('Controls');
        $this->load->model('Marks');

        $control = $this->Controls->get($controlId);
        if ($control === FALSE) {
            addPageNotification('Contrôle introuvable', 'danger');
            redirect('/Control');
        }

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('/Control');
        }

        $marks = $this->Controls->getMarks($control, $_SESSION['id']);
        $subject = $this->Controls->getSubject($controlId);

        $this->data = array(
            'control' => $control,
            'marks' => $marks,
            'subject' => $subject
        );

        $this->show('Ajout de notes');
    }
}
