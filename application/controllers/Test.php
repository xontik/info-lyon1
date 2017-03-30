<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $css = array("test");
        $title = "Premier essai !";

        $data = array(  "css" => $css,
                        "title" => $title);
        
        show("testv",$data);

    }
}
