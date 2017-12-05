<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Teacher extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!(isset($_SESSION['userType'])
            && in_array($_SESSION['userType'], $this->config->item('userTypes')))
        ) {
            header('Content-Length: 0', TRUE, 403);
            exit(0);
        }
    }

    public function get_teachers_subjects($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        $this->load->model('Teachers');

        header('Content-Type: application/json');

        $teachersUnsorted = $this->Teachers->getAll();
        $subjectsUnsorted = $this->Teachers->getAllSubjects($semesterId);

        $teachers = array();
        foreach ($teachersUnsorted as $teacher) {
            $teachers[$teacher->idTeacher] = $teacher;
        }

        $teachersNoSubject = $teachers;

        $subjects = array();
        foreach ($subjectsUnsorted as $subject) {
            if (!array_key_exists($subject->idSubject, $subjects)) {
                $subj = new stdClass();
                $subj->idSubject = $subject->idSubject;
                $subj->subjectCode = $subject->subjectCode;
                $subj->moduleName = $subject->moduleName;
                $subj->subjectName = empty($subject->subjectName)
                    ? $subject->moduleName : $subject->subjectName;
                $subj->teachers = array();
                $subjects[$subject->idSubject] = $subj;
            }

            $subjects[$subject->idSubject]->teachers[] = $subject->idTeacher;
            unset($teachersNoSubject[$subject->idTeacher]);
        }

        echo json_encode(array(
            'teachers' => $teachers,
            'teacherNoSubject' => $teachersNoSubject,
            'subjects' => $subjects
        ));
    }
}