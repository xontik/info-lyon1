<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
    {
        if ( isset($_SESSION['user_type']) ) {
            $urls = array(
                'student' => '/Etudiant',
                'teacher' => '/Professeur',
                'secretariat' => '/Secretariat'
            );
            redirect($urls[$_SESSION['user_type']]);
        }
        else {
            $this->load->view('welcome');
        }
	}
}
