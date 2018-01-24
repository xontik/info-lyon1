<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends TM_Controller
{
    private function _public()
    {
        $this->setData('view', 'Common/profile');
        $this->show('Profil');
    }

    public function student_index()
    {
        $this->_public();
    }

    public function teacher_index()
    {
        $this->_public();
    }

    public function secretariat_index()
    {
        $this->_public();
    }
}
