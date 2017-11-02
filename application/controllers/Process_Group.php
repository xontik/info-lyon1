<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Group extends CI_Controller
{

    public function add($idSemestre)
    {
        $idSemestre = intval(htmlspecialchars($idSemestre));

        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');

        if (isset($_POST['nomGroupe'])) {
            $nomGroupe = htmlspecialchars($_POST['nomGroupe']);

            //TODO add preg_match
            if ($this->semMod->isSemesterEditable($idSemestre))
            {
                if ($this->adminMod->addGroupe($idSemestre, $nomGroupe)) {
                    addPageNotification('Groupe ' . $nomGroupe . ' ajouté', 'success');
                } else {
                    addPageNotification('Erreur lors de la création du groupe', 'danger');
                }
            } else {
                addPageNotification('Ce semestre ne peut pas être modifié', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'danger');
        }

        redirect('Administration/Semestre/' . $idSemestre);
    }

    public function delete($idGroupe, $idSemestre)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');

        if ($this->adminMod->isGroupeEditable($idGroupe)) {
            if ($this->adminMod->deleteGroupe($idGroupe)) {
                addPageNotification('Groupe supprimé', 'success');
            } else {
                addPageNotification('Erreur lors de la suppression du groupe', 'danger');
            }
        } else {
            addPageNotification('Ce semestre ne peut etre modifié', 'warning');
        }

        redirect('Administration/Semestre/' . $idSemestre);
    }

    public function add_student($idSemestre)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('semester_model', 'semMod');
        $this->load->model('students_model', 'studentMod');

        if (isset($_POST['submit'])
            && isset($_POST['grp' . $_POST['submit']])
        ) {
            $numEtudiant = htmlspecialchars($_POST['grp' . $_POST['submit']]);
            $idGroupe = intval(htmlspecialchars($_POST['submit']));
            $semestreDuringSamePeriod = $this->semMod->getSemesterIdsSamePeriod($idSemestre);
            $groupStudentIds = $this->studentMod->getIdsFromGroup($idGroupe);

            if ($this->adminMod->isGroupeEditable($idGroupe))
            {
                if ($grp = $this->studentMod->isStudentInGroupsOfSemesters($numEtudiant, $semestreDuringSamePeriod))
                {
                    addPageNotification(
                        'Impossible d\'ajouter cet étudiant car il est déjà en ' . $grp->nomGroupe . $grp->type,
                        'danger'
                    );
                }
                else {
                    $otherGroups = $this->semMod->getOtherGroups($idGroupe);

                    $delete = false;
                    foreach ($otherGroups as $idGrp) {
                        if ($this->studentMod->isStudentInGroup($numEtudiant, $idGrp)) {
                            $this->studentMod->deleteRelationGroupStudent($idGrp, $numEtudiant);
                            $delete = true;
                        }
                    }

                    if ($delete) {
                        addPageNotification('Etudiant déplacé', 'success');
                    } else {
                        addPageNotification('Etudiant ajouté', 'success');
                    }
                    $this->studentMod->addToGroupe($numEtudiant, $idGroupe);

                }
            } else {
                addPageNotification('Impossible de modifier ce groupe', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'danger');
        }

        redirect('Administration/Semestre/' . $idSemestre);
    }

    public function delete_student($groupId, $numEtudiant, $idSemestre)
    {
        $this->load->model('administration_model', 'adminMod');
        $this->load->model('students_model', 'studentMod');

        if ($this->adminMod->isGroupeEditable($groupId))
        {
            $this->studentMod->deleteRelationGroupStudent($groupId, $numEtudiant);
            addPageNotification('Etudiant supprimé du groupe', 'success');
        } else {
            addPageNotification('Impossible de modifier ce groupe', 'danger');
        }
        redirect('Administration/Semestre/' . $idSemestre);

    }
}