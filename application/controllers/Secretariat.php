<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secretariat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ( !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretariat')
            redirect('/');
    }

    public function index() {
        $data = array(
            "css" => array(),
            "js" => array(),
            "title" => "Absences"
        );
        show("Secretariat/absences", $data);
    }

}
