<?php

/**
 * Created by PhpStorm.
 * User: Enzo
 * Date: 17/08/2017
 * Time: 15:17
 */
class User extends CI_Controller {

    public function connect() {

        $this->load->model('user_model', 'userModel');

        if ( !(isset($_POST['id']) &&
            isset($_POST['password'])) )
        {
            redirect('/');
            return;
        }

        $id = strtolower($_POST['id']);
        $password = $_POST['password'];
        $stay_connected = isset($_POST['stayConnected']) && $_POST['stayConnected'] === 'on';

        if ( empty($id) )
            $_SESSION['form_errors']['id'] = 'Merci d\'entrer un identifiant';
        if ( empty($password) )
            $_SESSION['form_errors']['pwd'] = 'Merci d\'entrer un mot de passe';


        if (empty($_SESSION['form_errors'])
            && ($userdata = $this->userModel->getUserInformations($id, $password)) !== FALSE
        ) {
            $this->session->set_userdata($userdata);

            if ($stay_connected) {
                //TODO Cookies
            }
        } else {
            $_SESSION['form']['id'] = $id;
            $this->session->mark_as_flash('form_errors');
            $this->session->mark_as_flash('form');
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
