<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {

        $css = array("test");
        $js = array("debug");
        $title = "Premier essai !";
        $data = array("a" => "donnée a","b" => "donnée b","c" => "donnée c");
        $var = array(   "css" => $css,
                        "js" => $js,
                        "title" => $title,
                        "data" => $data);

        show("testv",$var);

    }

    public function control(){

        $this->load->model('control_model','ctrlMod');
        $this->ctrlMod->addControl(2,20,"redfc",null,null);





    }
}
