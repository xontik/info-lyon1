<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Student extends CI_Controller
{

    public function get_all()
    {
        $this->load->model('Students');
        $this->load->model('Groups');
        $this->load->model('Years');

        $students = $this->Students->getAll();
        $unsortedGroups = $this->Groups->getAll();
        $unsortedYears = $this->Years->getAll();

        $groups = array();
        foreach ($unsortedGroups as $group) {
            if (!array_key_exists($group->idGroup, $groups)) {
                $groups[$group->idGroup] = array(
                    'idGroup' => $group->idGroup,
                    'groupName' => $group->groupName,
                    'schoolYear' => $group->schoolYear
                );
            }

            $groups[$group->idGroup]['students'][] = array(
                'idStudent' => $group->idStudent,
                'name' => $group->name,
                'surname' => $group->surname
            );
        }

        $years = array();
        foreach ($unsortedYears as $year) {
            $years[$year->schoolYear][] = array(
                'idStudent' => $year->idStudent,
                'name' => $year->name,
                'surname' => $year->surname
            );
        }

        $resources = array(
            'students' => $students,
            'groups' => $groups,
            'years' => $years
        );

        header('Content-Type: application/json');
        echo json_encode($resources);
    }

}
