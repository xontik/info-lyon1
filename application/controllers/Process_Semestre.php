<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Semestre extends CI_Controller
{
    public function add()
    {
        $this->load->model('semester_model', 'semMod');
        $this->load->model('administration_model', 'adminMod');

        if (isset($_POST['anneeScolaire']) && isset($_POST['parcoursId']))
        {
            $parcourdId = intval(htmlspecialchars($_POST['parcoursId']));
            $anneeScolaire = intval(htmlspecialchars($_POST['anneeScolaire']));

            if ($this->adminMod->isThisParcoursExist($parcourdId))
            {

                $semester = (object) array(
                    'differe' => isset($_POST['differe']) ? 1 : 0,
                    'anneeScolaire' => $anneeScolaire,
                    'idParcours' => $parcourdId,
                    'type' => $this->adminMod->getParcoursType($parcourdId)
                );

                $now = new DateTime();
                $dateStart = $this->semMod->getSemesterObjectPeriod($semester)->getBeginDate();

                if ($now < $dateStart) {
                    if ($this->semMod->addSemester($semester->idParcours, $semester->differe, $semester->anneeScolaire)) {
                        addPageNotification('Semestre créé', 'success');
                    } else {
                        addPageNotification('Ce semestre existe déjà', 'danger');
                    }
                } else {
                    addPageNotification('Création impossible, le semestre aurait déjà commencé', 'danger');
                }
            } else {
                addPageNotification('Parcours inconnu', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'danger');
        }

        redirect('Administration');
    }

    public function delete($id)
    {
        $this->load->model('semester_model', 'semMod');

        if ($this->semMod->isSemesterDeletable($id))
        {
            if ($this->semMod->deleteSemestre($id)) {
                addPageNotification('Semestre supprimé avec succès', 'success');
            } else {
                addPageNotification('Une erreur s\'est produite lors de la suppression du semestre', 'danger');
            }
        } else {
            addPageNotification('Ce semestre ne peut pas être supprimé', 'warning');
        }

        redirect('Administration');
    }
}