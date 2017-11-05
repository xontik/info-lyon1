<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration extends TM_Controller
{
    public function secretariat_index()
    {
        $this->load->model('Courses');
        $this->load->model('Semesters');

        $course = $this->Courses->getEditable();
        $courseTypes = $this->Courses->getCourseTypes();
        $semesters = $this->Semesters->getAll();

        $outSem = array();
        $idSemester = 0;

        foreach ($semesters as $semester)
        {
            if ($idSemester != $semester->idSemester) {
                $idSemester = $semester->idSemester;
                $period = $this->Semesters->getPeriod($idSemester);

                $now = new DateTime();

                if ($now > $period->getEndDate()) {
                    $state = 'after';
                } else if ($now >= $period->getBeginDate()) {
                    $state = 'now';
                } else {
                    $state = 'before';
                }

                $outSem[] = array(
                    'data' => $semester,
                    'state' => $state,
                    'period' => $period,
                    'groups' => array()
                );
            }

            if (!is_null($semester->idGroup)) {
                $outSem[count($outSem) - 1]['groups'][] = array(
                    'idGroup' => $semester->idGroup,
                    'groupName' => $semester->groupName
                );
            }
        }

        usort($outSem, function ($a, $b) {
            if ($a['period']->getBeginDate() < $b['period']->getEndDate()) {
                return 1;
            } else {
                return -1;
            }
        });

        $this->data = array(
            'courses' => $course,
            'semesters' => $outSem,
            'courseTypes' => $courseTypes
        );

        $this->show('Tableau de bord');
    }

    public function secretariat_semester($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        $this->load->model('Semesters');

        if (!$this->Semesters->isEditable($semesterId)) {
            addPageNotification('Impossible d\'éditer ce semestre', 'danger');
            redirect('Administration');
        }

        $semester = $this->Semesters->get($semesterId);
        $unsortedGroups = $this->Semesters->getGroups($semesterId);
        $unsortedStudent = $this->Semesters->getStudents($semesterId);

        // false pour récuperer ceux qui non pas du tout de groupe sachant qu'on a déjà ceux du semestre
        $freeStudents = $this->Semesters->getStudentsWithoutGroup($semesterId, false);

        $groups = array();
        foreach ($unsortedGroups as $group) {
            $group->students = array();
            $groups[$group->idGroup] = $group;
        }

        foreach ($unsortedStudent as $student) {
            $groups[$student->idGroup]->students[] = $student;
        }

        $this->data = array(
            'semester' => $semester,
            'groups' => $groups,
            'freeStudents' => $freeStudents
        );

        $this->show('Gestion de semestre');
    }
}
