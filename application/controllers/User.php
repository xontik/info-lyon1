<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function connect() {

        $this->load->model('user_model', 'userModel');

        if ( !(isset($_POST['id']) &&
            isset($_POST['password'])) )
        {
            redirect('/');
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
        }

        $userdata = $this->userModel->getUserInformations($id, $password);
        if ($userdata !== FALSE) {
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

    public function disconnect()
    {
        session_destroy();
        redirect('/');
    }

    // Debug pages
    public function session()
    {
        $data = array(
            'js' => array('debug'),
            'title' => '$_SESSION',
            'data' => array(
                'session' => $_SESSION
            )
        );
        show('Debug/session', $data);
    }

    public function fillnotif()
    {
        addPageNotification('Succès', 'success');
        addPageNotification('Avertissement', 'warning');
        addPageNotification('Echec', 'danger');
        addPageNotification('Icône personnalisé', '', 'schedule');
        addPageNotification('Un message<br>sur plusieurs lignes');
        addSessionNotification('Un lien qui dure toute la session !', '', '', '#!');
        addSessionNotification('Un message de succès qui dure toute la session !', 'success');

        redirect('/');
    }

}
