<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Control extends CI_Controller
{
    public function add($promo = '')
    {
        $this->load->model('control_model', 'ctrlMod');

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
        if (isset($_POST['enseignementId'])
            && isset($_POST['nom'])
            && isset($_POST['coeff'])
            && isset($_POST['diviseur'])
            && isset($_POST['date'])
            && isset($_POST['typeId'])
        ) {
            $enseignementId = intval(htmlspecialchars($_POST['enseignementId']));
            $nom = htmlspecialchars($_POST['nom']);
            $coeff = floatval(htmlspecialchars($_POST['coeff']));
            $diviseur = floatval(htmlspecialchars($_POST['diviseur']));
            $date = htmlspecialchars($_POST['date']);
            $typeId = intval(htmlspecialchars($_POST['typeId']));

            if ($enseignementId !== 0
                && !empty($nom)
                && $coeff !== 0
                && $diviseur !== 0
                && !empty($date)
                && $typeId !== 0
            ) {
                if (!$this->ctrlMod->checkEnseignementProf($enseignementId, $_SESSION['id'])) {
                    addPageNotification('Vous n\'avez pas les droit sur cet enseignement', 'danger');
                    redirect('Control');
                }

                if ($this->ctrlMod->addControl($nom, $coeff, $diviseur, $typeId, $date, $enseignementId)) {
                    addPageNotification('Contrôle ajoutée avec succès', 'success');
                    redirect('Control');
                }
            }
        }
        addPageNotification('Erreur lors de l\'ajout du contrôle', 'danger');
        redirect('Control');
    }

    private function _addPromo()
    {
        if (isset($_POST['matiereId'])
            && isset($_POST['nom'])
            && isset($_POST['coeff'])
            && isset($_POST['diviseur'])
            && isset($_POST['date'])
        ) {
            $matiereId = intval(htmlspecialchars($_POST['matiereId']));
            $nom = htmlspecialchars($_POST['nom']);
            $coeff = floatval(htmlspecialchars($_POST['coeff']));
            $diviseur = floatval(htmlspecialchars($_POST['diviseur']));
            $date = htmlspecialchars($_POST['date']);

            if ($matiereId !== 0
                && !empty($nom)
                && $coeff !== 0
                && $diviseur !== 0
                && !empty($date)
            ) {
                if ($this->ctrlMod->addDsPromo($nom, $coeff, $diviseur, 1, $date, $matiereId)) {
                    addPageNotification('Contrôle de promo ajouté avec succès', 'success');
                    redirect('Control');
                }
            }
        }

        addPageNotification('Impossible d\'ajouter le contrôle de promo', 'danger');
        redirect('Control');
    }

    public function update($id = '')
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas droit d\'accès à ce contrôle', 'danger');
            redirect('Controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce controle', 'danger');
            redirect('Controle');
        }

        if (isset($_POST['nom'])
            && isset($_POST['coeff'])
            && isset($_POST['diviseur'])
            && isset($_POST['date'])
            && isset($_POST['typeId'])
        ) {
            $nom = htmlspecialchars($_POST['nom']);
            $coeff = floatval(htmlspecialchars($_POST['coeff']));
            $diviseur = floatval(htmlspecialchars($_POST['diviseur']));
            $date = htmlspecialchars($_POST['date']);
            $typeId = intval(htmlspecialchars($_POST['typeId']));

            if (!empty($nom)
                && $coeff !== 0
                && $diviseur !== 0
                && !empty($date)
                && $typeId !== 0
            ) {
                if ($this->ctrlMod->editControl($nom, $coeff, $diviseur, $typeId, $date, $id)) {
                    addPageNotification('Contrôle modifié avec succès');
                    redirect('Controle');
                }
            }
        }

        addPageNotification('Erreur lors de l\'ajout du contrôle', 'danger');
        redirect('Controle');
    }

    public function delete($id = '')
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('Control');
        }

        if ($this->ctrlMod->deleteControl($id)) {
            addPageNotification('Contrôle supprimé avec succès', 'success');
            redirect('Control');
        }

        addPageNotification('Erreur lors de la suppression du contrôle', 'danger');
        redirect('Control');
    }
}
