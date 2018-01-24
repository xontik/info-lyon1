<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Education extends CI_Controller
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

    /*
     * AJAX
     */
    public function set_teacher($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        if (isset($_POST['teacherId'])
            && isset($_POST['groupId'])
            && isset($_POST['subjectId'])
        ) {
            $this->load->model('Semesters');
            $this->load->model('Educations');

            $teacherId = (int) htmlspecialchars($_POST['teacherId']);
            $groupId = (int) htmlspecialchars($_POST['groupId']);
            $subjectId = (int) htmlspecialchars($_POST['subjectId']);

            if ($this->Semesters->isEditable($semesterId)) {
                if ($this->Educations->setTeacher($subjectId, $groupId, $teacherId)) {
                    header('Content-Length: 0', true, 200);
                    return;
                }
            }
        }

        header('Content-Lenght: 0', true, 400);
    }

    /*
     * AJAX
     */
    public function set_teacher_all($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        if (isset($_POST['teacherId'])
            && isset($_POST['subjectId'])
        ) {
            $this->load->model('Semesters');
            $this->load->model('Educations');

            $teacherId = (int) htmlspecialchars($_POST['teacherId']);
            $subjectId = (int) htmlspecialchars($_POST['subjectId']);

            if ($this->Semesters->isEditable($semesterId)) {
                if ($this->Educations->setAllTeacher($subjectId, $semesterId, $teacherId)) {
                    header('Content-Length: 0', true, 200);
                    return;
                }
            }
        }

        header('Content-Lenght: 0', true, 400);
    }

}
