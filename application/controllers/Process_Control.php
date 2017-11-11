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

        $this->load->config('date_format');

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
            $date = DateTime::createFromFormat(
                $this->config->item('dateDisplayFormat'),
                htmlspecialchars($_POST['date']));

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

            if ($this->Controls->create($name, $coefficient, $divisor, $typeId,
                    $date->format($this->config->item('datetimeSystemFormat')), $educationId)
            ) {
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

        $this->load->config('date_format');

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
            $date = DateTime::createFromFormat(
                $this->config->item('dateDisplayFormat'),
                htmlspecialchars($_POST['date']));

            if (empty($name)
                && $coefficient == 0
                && $divisor == 0
                && $subjectId == 0
                && $date == FALSE
            ) {
                addPageNotification('Données corrompues', 'danger');
                redirect('Control/add');
            }

            if ($this->Controls->createPromo($name, $coefficient, $divisor,
                    $date->format($this->config->item('datetimeSystemFormat')), $subjectId)
            ) {
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

        $this->load->config('date_format');

        if (!$this->Teachers->hasRightOn($controlId, $_SESSION['id'])) {
            addPageNotification('Vous n\'avez pas droit d\'accès à ce contrôle', 'danger');
            redirect('Control');
        }

        $control = $this->Controls->get($controlId);
        if ($control === FALSE) {
            addPageNotification('Contrôle introuvable');
            redirect('Control');
        }

        if (isset($_POST['name'])
            && isset($_POST['coefficient'])
        ) {
            $name = htmlspecialchars($_POST['name']);
            $coefficient = (float) htmlspecialchars($_POST['coefficient']);
            $divisor = isset($_POST['divisor'])
                ? (float) htmlspecialchars($_POST['divisor'])
                : null;

            $date = isset($_POST['date'])
                ? DateTime::createFromFormat(
                    $this->config->item('dateDisplayFormat'),
                    htmlspecialchars($_POST['date']))
                : null;

            if (!empty($name)
                && $coefficient !== 0
                && $divisor !== 0
                && ($divisor === null
                    || $divisor == $control->divisor
                    || !$this->Controls->hasMark($controlId)
                )
                && $date !== FALSE
            ) {
                if ($this->Controls->update($controlId, $name, $coefficient, $divisor,
                    is_null($date) ? null : $date->format($this->config->item('datetimeSystemFormat')))
                ) {
                    addPageNotification('Contrôle modifié avec succès', 'success');
                    redirect('Control');
                }
            } else {
                addPageNotification('Données corrompues', 'danger');
                redirect('Control/edit/' . $controlId);
            }
        }

        addPageNotification('Erreur lors de la modification du contrôle', 'danger');
        redirect('Control/edit/' . $controlId);
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

        if ($this->Controls->hasMark($controlId)) {
            addPageNotification('Impossible de supprimer un contrôle alors que des élèves ont été notés !', 'danger');
            redirect('Control/edit/' . $controlId);
        }

        if ($this->Controls->delete($controlId)) {
            addPageNotification('Contrôle supprimé avec succès', 'success');
            redirect('Control');
        }

        addPageNotification('Erreur lors de la suppression du contrôle', 'danger');
        redirect('Control/edit/' . $controlId);
    }

}
