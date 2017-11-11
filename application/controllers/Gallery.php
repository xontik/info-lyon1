<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends CI_Controller
{
    public function index()
    {
        $this->load->model('Projects');

        $projects = $this->Projects->getAllPictures();

        $data = array(
            'title' => 'Galerie des projets',
            'data' => array(
                'projects' => $projects
            )
        );
        showPublic('gallery', $data);
    }
}
