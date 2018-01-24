<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function show($view, $data = array())
{
    $instance =& get_instance();
    $instance->load->view('includes/head', $data);
    $instance->load->view('includes/header', $data);
    $instance->load->view($view, $data);
    $instance->load->view('includes/foot', $data);
}

function showPublic($view, $data = array())
{
    $instance =& get_instance();
    $instance->load->view('includes/head', $data);
    $instance->load->view('Public/' . $view, $data);
    $instance->load->view('includes/foot', $data);
}

function showHead($data = array())
{
    get_instance()->load->view('includes/head', $data);
}

function showPart($view, $data = array())
{
    get_instance()->load->view($view, $data);
}

function showFoot($data = array())
{
    get_instance()->load->view('includes/foot', $data);
}