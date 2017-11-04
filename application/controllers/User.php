<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function connect() {

        $this->load->model('Users');

        if ( !(isset($_POST['login']) &&
            isset($_POST['password'])) )
        {
            redirect('/');
        }

        $login = strtolower(htmlspecialchars($_POST['login']));
        $password = htmlspecialchars($_POST['password']);
        $stayConnected = isset($_POST['stayConnected']) && $_POST['stayConnected'] === 'on';

        if (empty($login)) {
            $_SESSION['form_errors']['id'] = 'Veuillez entrer un identifiant';
        }
        if (empty($password)) {
            $_SESSION['form_errors']['password'] = 'Veuillez entrer un mot de passe';
        }


        if ( !empty($_SESSION['form_errors']) ) {
            $this->session->mark_as_flash('form_errors');
            redirect('/');
        }

        $userdata = $this->Users->getUserInformations($login, $password);
        if ($userdata !== FALSE) {
            $this->session->set_userdata($userdata);

            if ($stayConnected) {
                //TODO Cookies
            }
        } else {
            $_SESSION['form']['id'] = $login;
            $this->session->mark_as_flash('form_errors');
            $this->session->mark_as_flash('form');
        }

        redirect('/');
    }

    public function disconnect()
    {
        session_destroy();
        redirect('/');
    }

}
