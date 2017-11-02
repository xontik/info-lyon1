<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Parcours extends CI_Controller
{
    /*
     * AJAX
     */
    public function add_UE()
    {
        $this->load->model('administration_model', 'adminMod');

        $idParcours = intval(htmlspecialchars($_POST['idParcours']));
        $idUEs = $_POST['idUEs'];

        $ids = array();
        if ($this->adminMod->isThisParcoursEditable($idParcours))
        {
            foreach ($idUEs as $idUE) {
                if ($this->adminMod->addUEtoParcours($idParcours, $idUE)) {
                    $ids[] = $idUE;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($ids);
    }

    /*
     * AJAX
     */
    public function remove_UE()
    {
        $this->load->model('administration_model', 'adminMod');
        $ids = array();

        $idParcours = intval(htmlspecialchars($_POST['idParcours']));
        $idUEs = $_POST['idUEs'];

        if ($this->adminMod->isThisParcoursEditable($idParcours)) {

            foreach ($idUEs as $idUE) {
                if ($this->adminMod->removeUEtoParcours($idParcours, $idUE)) {
                    $ids[] = $idUE;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($ids);
    }

    public function add()
    {
        $this->load->model('administration_model', 'adminMod');

        if (isset($_POST['year']) && isset($_POST['type'])) {
            $year = intval(htmlspecialchars($_POST['year']));
            $type = htmlspecialchars($_POST['type']);

            if ($year !== 0 && strlen($type) === 2) {
                if ($this->adminMod->addParcours($year, $type)) {
                    addPageNotification('Parcours créé avec succès', 'success');
                } else {
                    addPageNotification('Erreur lors de la création du parcours', 'danger');
                }
            } else {
                addPageNotification('Données corrompues', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'danger');
        }
        redirect('Administration');
    }

    public function delete()
    {
        $this->load->model('administration_model', 'adminMod');

        if (isset($_POST['parcoursId'])) {
            $parcours = intval(htmlspecialchars($_POST['parcoursId']));

            if ($this->adminMod->isThisParcoursEditable($parcours)) {
                if ($this->adminMod->deleteCascadeParcours($parcours)) {
                    addPageNotification('Parcours supprimé', 'success');
                } else {
                    addPageNotification('Erreur lors de la suppression du parcours', 'danger');
                }
            } else {
                addPageNotification('Ce parcours ne peut etre supprimé !', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'warning');
        }

        redirect('Administration');
    }
}