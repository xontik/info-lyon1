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
        $this->load->model('Teachers');

        $deletable = $this->Semesters->isDeletable($semesterId);
        $editable = $this->Semesters->isEditable($semesterId);

        $semester = $this->Semesters->get($semesterId);
        $unsortedGroups = $this->Semesters->getGroups($semesterId);
        $unsortedStudent = $this->Semesters->getStudents($semesterId);

        $freeStudents = $deletable ? null : $this->Semesters->getStudentsWithoutGroup($semesterId, false);

        $groupsWithStudent = array();
        foreach ($unsortedGroups as $group) {
            $group->students = array();
            $groupsWithStudent[$group->idGroup] = $group;
        }

        foreach ($unsortedStudent as $student) {
            $groupsWithStudent[$student->idGroup]->students[] = $student;
        }

        $subjects = $this->Semesters->getSubjects($semesterId);
        $allEducations = $this->Semesters->getEducations($semesterId);

        $educations = array();
        foreach ($unsortedGroups as $group) {
            $educations[$group->idGroup] = array();
        }
        foreach ($allEducations as $education) {
            $educations[$education->idGroup][$education->idSubject] = $education;
        }



        $this->data = array(
            'semester' => $semester,
            'groupsWithStudent' => $groupsWithStudent,
            'groups' => $unsortedGroups,
            'subjects' => $subjects,
            'educations' => $educations,
            'freeStudents' => $freeStudents,
            'deletable' => $deletable,
            'editable' => $editable
        );

        $this->setData('js');

        $this->setData('js', 'jquery-ui/jquery-ui.min');
        $this->setData('js', 'Secretariat/administration_semester');
        $this->setData('css', 'jquery-ui/jquery-ui.structure.min');
        $this->setData('css', 'jquery-ui/jquery-ui.min');

        $this->show('Gestion de semestre');
    }
}
