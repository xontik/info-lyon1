<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secretariat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
            redirect('/');
    }

    public function index() {
        $this->absences();
    }

    public function absences() {
        $this->load->model('absence_model');
        $this->load->model('semester_model');
        $this->load->model('students_model');

        $bounds = $this->semester_model->getSemesterBounds(
            $this->semester_model->getCurrentSemesterId()
        );

        $data = array(
            'css' => array('Secretariat/absences'),
            'js' => array('debug'),
            'title' => 'Absences',
            'data' => array(
                'students' => $this->students_model->getStudents(),
                'absences' => $this->absence_model->getAbsencesInPeriod($bounds->beginning, $bounds->end),
                'begin_date' => $bounds->beginning,
                'day_number' => $bounds->beginning->diff($bounds->end, true)->days
            )
        );
        show("Secretariat/absences", $data);
    }

}
