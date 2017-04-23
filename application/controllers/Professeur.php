<?php
/**
 * Created by PhpStorm.
 * User: xontik
 * Date: 21/04/2017
 * Time: 13:50
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Professeur extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $newdata = array(
            'username'  => 'johndoe',
            'email'     => 'johndoe@some-site.com',
            'profId' => 'e8888888'
        );

        $this->session->set_userdata($newdata);
    }

    public function index(){

    }
    public function control(){
        $this->load->model('control_model','ctrlMod');

        $controls = $this->ctrlMod->getControls($_SESSION['profId']);

        $css = array("test");
        $js = array("debug");
        $title = "Controles";
        $data = array("controls" => $controls);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("P_controls",$var);
    }
    
}