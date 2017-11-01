<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function connect() {

        $this->load->model('user_model', 'userModel');

        if ( !(isset($_POST['id']) &&
            isset($_POST['password'])) )
        {
            redirect('/');
            return;
        }

        $id = strtolower(htmlspecialchars($_POST['id']));
        $password = htmlspecialchars($_POST['password']);
        $stay_connected = isset($_POST['stayConnected']) && $_POST['stayConnected'] === 'on';

        if ( empty($id) )
            $_SESSION['form_errors']['id'] = 'Veuillez entrer un identifiant';
        if ( empty($password) )
            $_SESSION['form_errors']['password'] = 'Veuillez entrer un mot de passe';


        if ( !empty($_SESSION['form_errors']) ) {
            $this->session->mark_as_flash('form_errors');
            redirect('/');
            return;
        }

        $userdata = $this->userModel->getUserInformations($id, $password);
        if ($userdata !== FALSE) {
            $this->session->set_userdata($userdata);

            if ($stay_connected) {
                //TODO Cookies
            }
        }

        redirect('/');
    }

    public function disconnect() {
        $_SESSION = array();
        redirect('/');
    }

    public function session() {
        //Debug page
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
    }

}
