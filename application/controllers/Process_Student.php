<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Student extends CI_Controller
{

    public function get_all()
    {
        $this->load->model('Students');
        $this->load->model('Groups');

        $students = $this->Students->getAll();
        $unsortedGroups = $this->Groups->getAll();

        $groups = array();
        foreach ($unsortedGroups as $group) {
            if (!array_key_exists($group->idGroup, $groups)) {
                $gr = new stdClass();
                $gr->idGroup = $group->idGroup;
                $gr->groupName = $group->groupName . $group->courseType;
                $gr->schoolYear = $group->schoolYear;

                $groups[$group->idGroup] = $gr;
            }

            $student = new stdClass();
            $student->idStudent = $group->idStudent;
            $student->name = $group->name;
            $student->surname = $group->surname;

            $groups[$group->idGroup]->students[] = $student;
        }

        $resources = array(
            'students' => $students,
            'groups' => $groups
        );

        header('Content-Type: application/json');
        echo json_encode($resources);
    }

}
