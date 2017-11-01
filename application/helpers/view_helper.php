<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function show($view, $var = array())
{
    $instance =& get_instance();
    $instance->load->view('includes/head', $var);
    $instance->load->view($view, $var);
    $instance->load->view('includes/foot', $var);

}

function show_head($var = array())
{
    get_instance()->load->view('includes/head', $var);
}

function show_part($view, $var = array())
{
    get_instance()->load->view($view, $var);
}

function show_foot($var = array())
{
    get_instance()->load->view('includes/foot', $var);
}