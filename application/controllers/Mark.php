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

        $this->load->helper('tabs');

        // Highest semester the student was it
        $maxSemester = (int) substr(
            $this->Students->getCurrentSemester($_SESSION['id'])->courseType,
            1
        );

        // If above max semester
        if ($semester !== '' && $semester > 'S' . $maxSemester) {
            addPageNotification('Vous essayez d\'accéder à un semestre futur<br>Redirection vers le semestre courant');
            $semester = '';
        }

        // Tabs content
        $tabs = array();
        for ($i = 1; $i <= $maxSemester; $i++) {
            $tabs["S$i"] = createTab("Semester $i", "Mark/S$i");
        }

        $semesterId = $this->Semesters->getSemesterId($semester, $_SESSION['id']);

        $semesterType = $this->Semesters->getType($semesterId);
        $tabs[$semesterType]->active = true;

        $marks = $this->Semesters->getStudentMarks($_SESSION['id'], $semesterId);

        $this->data = array(
            'tabs' => $tabs,
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
            addPageNotification('Vous n\'avez pas les droits sur ce contrôle', 'danger');
            redirect('/Control');
        }

        $marks = $this->Controls->getMarks($control, $_SESSION['id']);
        $subject = $this->Controls->getSubject($controlId);
        
        if (empty($subject->subjectName)) {
          $subject->subjectName = $subject->moduleName;
        }

        $this->data = array(
            'control' => $control,
            'marks' => $marks,
            'subject' => $subject
        );

        $this->show('Ajout de notes');
    }
}
