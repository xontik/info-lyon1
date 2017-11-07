<?php

class Debug extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (ENVIRONMENT !== 'development') {
            show_404();
        }
    }

    public function session()
    {
        $data = array(
            'title' => '$_SESSION',
            'data' => array(
                'session' => $_SESSION
            )
        );
        show('debug/session', $data);
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