<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 27/04/2017
 * Time: 10:06
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiant extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $newdata = array(
            'username'  => 'johndoe',
            'email'     => 'johndoe@some-site.com',
            'etuId' => 'p1111111'
        );

        $this->session->set_userdata($newdata);
    }
    public function index(){
        redirect("etudiant/mark");
    }

    public function mark($semestre){

        $this->load->model("mark_model","markMod");
        $marks = $this->markMod->getMarksFromSemester($_SESSION["etuId"],$semestre);

        $css = array("test");
        $js = array("debug");
        $title = "Notes";
        $data = array("marks" => $marks);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("E_mark",$var);

    }
}
