<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!(isset($_SESSION['userType'])
            && in_array($_SESSION['userType'], $this->config->item('userTypes')))
        ) {
            header('Content-Length: 0', TRUE, 403);
            exit(0);
        }
    }

    public function change_password()
    {
        if (isset($_POST['formerPassword'])
            && isset($_POST['newPassword'])
            && isset($_POST['confirmPassword'])
        ) {
            $formerPassword = htmlspecialchars($_POST['formerPassword']);
            $newPassword = htmlspecialchars($_POST['newPassword']);
            $confirmPassword = htmlspecialchars($_POST['confirmPassword']);

            if ($newPassword !== $confirmPassword) {
                addPageNotification('Les mots de passes ne correspondent pas', 'warning');
                exit(0);
            }

            $this->load->model('Profiles');
            if ($this->Profiles->isPassword($_SESSION['userId'], $formerPassword)) {
                if ($this->Profiles->changePassword($_SESSION['userId'], $newPassword)) {
                    addPageNotification('Changement de mot de passe effectué', 'success');
                    redirect('Profile');
                }
            } else {
                addPageNotification('Mot de passe incorrect', 'warning');
                redirect('Profile');
            }
        }
        addPageNotification('Erreur lors du changement du mot de passe', 'danger');
        redirect('Profile');
    }
}
