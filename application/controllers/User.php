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

    public function get_notifications() {
        header('Content-Type: application/json');

        echo json_encode(
            isset($_SESSION['notif']) ? $_SESSION['notif'] : array(),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_FORCE_OBJECT | JSON_PRETTY_PRINT,
            3
        );
    }

    public function add_notification() {
        $this->load->helper('notification');

        header('Content-Length: 0');

        if (isset($_POST['content'])) {
            $content = htmlspecialchars($_POST['content']);
            $type = '';
            $icon = '';

            if (isset($_POST['type'])) {
                $type = htmlspecialchars($_POST['type']);
            }

            if (isset($_POST['icon'])) {
                $icon = htmlspecialchars($_POST['icon']);
            }

            add_notification($content, $type, $icon);
        }

    }

    public function remove_notification() {
        header('Content-Length: 0');

        if (isset($_POST['notifId'])) {
            $notifId = intval(htmlspecialchars($_POST['notifId']));
            unset($_SESSION['notif'][$notifId]);
        }
    }

    // Debug pages
    public function session() {
        $data = array(
            'js' => array('debug'),
            'title' => '$_SESSION',
            'data' => array(
                'session' => $_SESSION
            )
        );
        show('Debug/session', $data);
    }

    public function fillnotif() {
        $this->load->helper('notification');

        add_notification('Message court');
        add_notification('Succès', 'success');
        add_notification('Avertissement', 'warning');
        add_notification('Echec', 'fail');
        add_notification('Icône personnalisé', '', 'schedule');
        add_notification('Un message un peu long, mais l\'enlever redimensionne les autres toasts');
        add_notification('Un message<br>sur deux lignes');

        redirect('/');
    }

}
