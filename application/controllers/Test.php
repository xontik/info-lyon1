<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $this->load->model('absence_model', 'absm');

        $var = array(   'css' => array('edt_day'),
                        'js' => array('debug'),
                        'title' => 'Page de test',
                        'data' => array(
                            'absences' => $this->absm->getAbsencesFromSemester("p1600006", 1)
                        ) );

        show('testv', $var);

    }

}
