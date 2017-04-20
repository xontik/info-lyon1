<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $var = array(   "css" => array('test'),
                        "js" => array(),
                        "title" => 'Header design',
                        "data" => array() );

        show("testv", $var);

    }
}
