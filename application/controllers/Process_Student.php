<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Student extends CI_Controller
{

    public function get_all()
    {
        $this->load->model('Students');

        header('Content-Type: application/json');
        echo json_encode($this->Students->getAll());
    }

}
