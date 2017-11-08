<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Control extends CI_Controller
{
    private $formatIn = 'd/m/Y';
    private $formatOut = 'Y-m-d';

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
            $date = DateTime::createFromFormat($this->formatIn, htmlspecialchars($_POST['date']));

            if (empty($name)
                || $typeId == 0
                || $coefficient == 0
                || $divisor == 0
                || $educationId == 0
                || $date === FALSE
            ) {
                addPageNotification('Données corrompues', 'danger');
                redirect('Control/add');
            }

            if (!$this->Teachers->hasEducation($educationId, $_SESSION['id'])) {
                addPageNotification('Vous n\'avez pas les droit sur cet enseignement', 'danger');
                redirect('Control');
            }

            if ($this->Controls->create($name, $coefficient, $divisor, $typeId, $date->format($this->formatOut), $educationId)) {
                addPageNotification('Contrôle ajoutée avec succès', 'success');
                redirect('Control');
            }

        }

        addPageNotification('Erreur lors de l\'ajout du contrôle, données corrompues', 'danger');
        redirect('Control/add');
    }

    private function _addPromo()
    {
        $this->load->model('Controls');

        if (isset($_POST['name'])
            && isset($_POST['coefficient'])
            && isset($_POST['divisor'])
            && isset($_POST['subjectId'])
            && isset($_POST['date'])
        ) {
            $name = htmlspecialchars($_POST['name']);
            $coefficient = (float) htmlspecialchars($_POST['coefficient']);
            $divisor = (float) htmlspecialchars($_POST['divisor']);
            $subjectId = (int) htmlspecialchars($_POST['subjectId']);
            $date = DateTime::createFromFormat($this->formatIn, htmlspecialchars($_POST['date']));

            if (empty($name)
                && $coefficient == 0
                && $divisor == 0
                && $subjectId == 0
                && $date == FALSE
            ) {
                addPageNotification('Données corrompues', 'danger');
                redirect('Control/add');
            }

            if ($this->Controls->createPromo($name, $coefficient, $divisor, $date->format($this->formatOut), $subjectId)) {
                addPageNotification('Contrôle de promo ajouté avec succès', 'success');
                redirect('Control');
            }
        }

        addPageNotification('Erreur lors de l\'ajout du contrôle de promo, données corrompues', 'danger');
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
            redirect('Control');
        }

        if (isset($_POST['name'])
            && isset($_POST['coefficient'])
            && isset($_POST['divisor'])
            && isset($_POST['date'])
        ) {
            $name = htmlspecialchars($_POST['name']);
            $coefficient = (float) htmlspecialchars($_POST['coefficient']);
            $divisor = (float) htmlspecialchars($_POST['divisor']);
            $date = DateTime::createFromFormat($this->formatIn, htmlspecialchars($_POST['date']));

            if (!empty($name)
                && $coefficient !== 0
                && $divisor !== 0
                && $date !== FALSE
            ) {
                if ($this->Controls->update($controlId, $name, $coefficient, $divisor, $date->format($this->formatOut))) {
                    addPageNotification('Contrôle modifié avec succès', 'success');
                    redirect('Control');
                }
            }
        }

        addPageNotification('Erreur lors de la modification du contrôle', 'danger');
        redirect('Control');
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
