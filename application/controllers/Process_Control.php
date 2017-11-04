<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Control extends CI_Controller
{
    public function add($promo = '')
    {
        if ($promo === '') {
            $this->_addSimple();
        } else if ($promo == 'promo') {
            $this->_addPromo();
        } else {
            show_404();
        }
    }

    private function _addSimple()
    {
        $this->load->model('Teachers');
        $this->load->model('Controls');

        echo '<pre>';
        print_r($_POST);
        echo '</pre>';

        if (isset($_POST['name'])
            && isset($_POST['typeId'])
            && isset($_POST['coefficient'])
            && isset($_POST['divisor'])
            && isset($_POST['educationId'])
            && isset($_POST['date'])
        ) {
            $name = htmlspecialchars($_POST['name']);
            $typeId = (int) htmlspecialchars($_POST['typeId']);
            $coefficient = (float) htmlspecialchars($_POST['coefficient']);
            $divisor = (float) htmlspecialchars($_POST['divisor']);
            $educationId = (int) htmlspecialchars($_POST['educationId']);
            $date = htmlspecialchars($_POST['date']);

            if (!empty($name)
                && $typeId !== 0
                && $coefficient !== 0
                && $divisor !== 0
                && $educationId !== 0
                && !empty($date)
            ) {
                if (!$this->Teachers->hasEducation($educationId, $_SESSION['id'])) {
                    addPageNotification('Vous n\'avez pas les droit sur cet enseignement', 'danger');
                    redirect('Control');
                }

                if ($this->Controls->create($name, $coefficient, $divisor, $typeId, $date, $educationId)) {
                    addPageNotification('Contrôle ajoutée avec succès', 'success');
                    redirect('Control');
                }
            }
        }
        
        addPageNotification('Erreur lors de l\'ajout du contrôle', 'danger');
        //redirect('Control');
    }

    private function _addPromo()
    {
        $this->load->model('Controls');

        if (
            isset($_POST['name'])
            && isset($_POST['coefficient'])
            && isset($_POST['divisor'])
            && isset($_POST['subjectId'])
            && isset($_POST['date'])
        ) {
            $name = htmlspecialchars($_POST['name']);
            $coefficient = (float) htmlspecialchars($_POST['coefficient']);
            $divisor = (float) htmlspecialchars($_POST['divisor']);
            $subjectId = (int) htmlspecialchars($_POST['subjectId']);
            $date = htmlspecialchars($_POST['date']);

            if (!empty($name)
                && $coefficient !== 0
                && $divisor !== 0
                && $subjectId !== 0
                && !empty($date)
            ) {
                if ($this->Controls->createPromo($name, $coefficient, $divisor, $date, $subjectId)) {
                    addPageNotification('Contrôle de promo ajouté avec succès', 'success');
                    redirect('Control');
                }
            }
        }

        addPageNotification('Impossible d\'ajouter le contrôle de promo', 'danger');
        redirect('Control');
    }

    public function update($controlId = '')
    {
        $controlId = (int) htmlspecialchars($controlId);
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('Teachers');
        $this->load->model('Controls');

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas droit d\'accès à ce contrôle', 'danger');
            redirect('Controle');
        }

        if (isset($_POST['name'])
            && isset($_POST['coefficient'])
            && isset($_POST['divisor'])
            && isset($_POST['date'])
            && isset($_POST['typeId'])
        ) {
            $name = htmlspecialchars($_POST['name']);
            $coefficient = (float) htmlspecialchars($_POST['coefficient']);
            $divisor = (float) htmlspecialchars($_POST['divisor']);
            $date = htmlspecialchars($_POST['date']);
            $typeId = (int) htmlspecialchars($_POST['typeId']);

            if (!empty($name)
                && $coefficient !== 0
                && $divisor !== 0
                && !empty($date)
                && $typeId !== 0
            ) {
                if ($this->Controls->update($controlId, $name, $coefficient, $divisor, $typeId, $date)) {
                    addPageNotification('Contrôle modifié avec succès');
                    redirect('Controle');
                }
            }
        }

        addPageNotification('Erreur lors de l\'ajout du contrôle', 'danger');
        redirect('Controle');
    }

    public function delete($controlId = '')
    {
        $controlId = (int) htmlspecialchars($controlId);
        if ($controlId === 0) {
            show_404();
        }

        $this->load->model('Teachers');
        $this->load->model('Controls');

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('Control');
        }

        if ($this->Controls->delete($controlId)) {
            addPageNotification('Contrôle supprimé avec succès', 'success');
            redirect('Control');
        }

        addPageNotification('Erreur lors de la suppression du contrôle', 'danger');
        redirect('Control');
    }

}
