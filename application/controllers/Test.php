<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
<<<<<<< HEAD
        $this->load->helper('calendar');

        $var = array(
            'css' => array('edt_day'),
            'js' => array('debug'),
            'title' => 'Page de test',
            'data' => array()
        );
=======
        $this->load->model('absence_model', 'absm');

        $var = array(   'css' => array('edt_day'),
                        'js' => array('debug'),
                        'title' => 'Page de test',
                        'data' => array(
                            'absences' => $this->absm->getAbsencesFromSemester("p1600006", 1)
                        ) );
>>>>>>> absence

        show('testv', $var);

    }

}
