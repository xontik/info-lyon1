<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
    {
        if ( isset($_SESSION['userType']) ) {
            $urls = array(
                'student' => 'Dashboard',
                'teacher' => 'Absence',
                'secretariat' => 'Absence'
            );

            // Keep all flashdata for one more request
            foreach ($this->session->flashdata() as $key => $val) {
                $this->session->keep_flashdata($key);
            }

            redirect($urls[$_SESSION['userType']]);
        }
        else {
            $this->load->view('welcome');
        }
	}
}
