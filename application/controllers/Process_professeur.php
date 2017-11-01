<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_professeur extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher')
            redirect('/');

    }

    public function addcontrole($promo = '')
    {
        $this->load->model('control_model', 'ctrlMod');
        $this->load->helper('notification');

        if ($promo === '') {
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
                        redirect('professeur/controle');
                    }

                    if ($this->ctrlMod->addControl($nom, $coeff, $diviseur, $typeId, $date, $enseignementId)) {
                        addPageNotification('Contrôle ajoutée avec succès', 'success');
                        redirect('professeur/controle');
                    }
                }
            }
            addPageNotification('Erreur lors de l\'ajout du contrôle', 'danger');
            redirect('professeur/controle');
        }
        else if ($promo == 'promo') {

            if (isset($_POST['matiereId'])
                && isset($_POST['nom'])
                && isset($_POST['coeff'])
                && isset($_POST['diviseur'])
                && isset($_POST['date'])
            ) {
                $matiereId = inval(htmlspecialchars($_POST['matiereId']));
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
                        redirect('professeur/controle');
                    }
                }
            }

            addPageNotification('Impossible d\'ajouter le contrôle de promo', 'danger');
            redirect('professeur/controle');
        } else {
            show_404();
        }

    }

    public function editcontrole($id = '')
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');
        $this->load->helper('notification');

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas droit d\'accès à ce contrôle', 'danger');
            redirect('professeur/controle');
        }

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            $this->session->set_flashdata("notif", array("Vous n'avez pas les droit sur ce controle"));
            redirect("professeur/controle");
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
                    redirect('professeur/controle');
                }
            }
        }

        addPageNotification('Erreur lors de l\'ajout du contrôle', 'danger');
        redirect('professeur/controle');
    }

    public function deletecontrole($id = '')
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');
        $this->load->helper('notification');

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('professeur/controle');
        }

        if ($this->ctrlMod->deleteControl($id)) {
            addPageNotification('Contrôle supprimé avec succès', 'success');
            redirect('professeur/controle');
        }

        addPageNotification('Erreur lors de la suppression du contrôle', 'danger');
        redirect('professeur/controle');
    }

    public function addmarks($id)
    {
        $id = intval(htmlspecialchars($id));
        if ($id === 0) {
            show_404();
        }

        $this->load->model('control_model', 'ctrlMod');
        $this->load->model('mark_model', 'markMod');
        $this->load->helper('notification');

        if (!$this->ctrlMod->checkProfessorRightOnControl($_SESSION['id'], $id)) {
            addPageNotification('Vous n\'avez pas les droit sur ce contrôle', 'danger');
            redirect('professeur/controle');
        }

        $control = $this->ctrlMod->getControl($id);
        $marks = $this->markMod->getMarks($control, $_SESSION['id']);

        $i = 0;
        $correctData = true;
        foreach ($_POST as $key => $value) {
            if ($key !== $marks[$i]->numEtudiant) {
                $correctData = false;
                break;
            }
            $i++;

            if ($i === count($_POST) - 1) {
                break;
            }
        }

        if (!$correctData) {
            addPageNotification('Données reçues incohérentes', 'warning');
            redirect('professeur/controle');
        }
        array_pop($_POST);
        //TODO Ajouter verification sur value

        $this->markMod->addMarks($id, $_POST);

        addPageNotification('Note modifiées avec succès', 'success');
        redirect('professeur/controle');
    }
}
