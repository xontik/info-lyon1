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
        $dspromo = $this->ctrlMod->getDsPromo($_SESSION['profId']);



        $css = array("test");
        $js = array("debug");
        $title = "Controles";
        $data = array("controls" => $controls,"dspromo" => $dspromo);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("P_controls",$var);
    }
    public function addControl($promo = ""){
        $this->load->model('control_model','ctrlMod');
        $bool = false;
        if($promo == ""){
            $select =  $this->ctrlMod->getEnseignements($_SESSION['profId']);
        }else{
            $bool = true;
            $select = $this->ctrlMod->getMatieres($_SESSION['profId']);
        }


        $css = array("test");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("select" => $select,"promo" => $bool);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("P_addControl",$var);
    }
    public function editControl($id){
        $this->load->model('control_model','ctrlMod');
        //TODO check si id ok (droit)
        $control = $this->ctrlMod->getControl($id);



        $css = array("test");
        $js = array("debug");
        $title = "Ajout de controles";
        $data = array("control" => $control);
        $var = array(   "css" => $css,
            "js" => $js,
            "title" => $title,
            "data" => $data);

        show("P_editControl",$var);
    }

}