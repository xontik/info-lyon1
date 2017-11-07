<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Group extends CI_Controller
{

    public function add($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        $this->load->model('Groups');
        $this->load->model('Semesters');

        if (isset($_POST['groupName'])) {
            $groupName = htmlspecialchars($_POST['groupName']);

            if(preg_match('/^G[0-9]$/',$groupName) === 1) {
                if ($this->Semesters->isEditable($semesterId)) {
                    if ($this->Groups->create($semesterId, $groupName)) {
                        addPageNotification('Groupe ' . $groupName . ' ajouté avec succès', 'success');
                    } else {
                        addPageNotification('Erreur lors de la création du groupe', 'danger');
                    }
                } else {
                    addPageNotification('Ce semestre ne peut pas être modifié', 'danger');
                }
            } else {
                addPageNotification('Format du nom de groupe incorrect', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'danger');
        }

        redirect('Administration/Semester/' . $semesterId);
    }

    public function delete($groupId, $semesterId)
    {
        $this->load->model('Groups');

        if ($this->Groups->isEditable($groupId)) {
            if ($this->Groups->delete($groupId)) {
                addPageNotification('Groupe supprimé', 'success');
            } else {
                addPageNotification('Erreur lors de la suppression du groupe', 'danger');
            }
        } else {
            addPageNotification('Ce semestre ne peut etre modifié', 'warning');
        }

        redirect('Administration/Semester/' . $semesterId);
    }

    public function add_student($semesterId)
    {
        $this->load->model('Groups');
        $this->load->model('Semesters');

        if (isset($_POST['submit'])
            && isset($_POST['group' . $_POST['submit']])
        ) {
            $studentId = htmlspecialchars($_POST['group' . $_POST['submit']]);
            $groupId = (int) htmlspecialchars($_POST['submit']);

            $concurrentSemesters = $this->Semesters->getConcurrent($semesterId);

            if ($this->Groups->isEditable($groupId))
            {
                if ($group = $this->Semesters->anyHasStudent($studentId, $concurrentSemesters))
                {
                    addPageNotification(
                        'Impossible d\'ajouter cet étudiant car il est déjà en '
                        . $group->groupName . $group->courseType,
                        'danger'
                    );
                }
                else {
                    $otherGroups = $this->Semesters->getOtherGroups($groupId);

                    $delete = false;
                    foreach ($otherGroups as $otherGroup) {
                        if ($this->Groups->hasStudent($studentId, $otherGroup->idGroup)) {
                            $this->Groups->removeStudent($studentId, $otherGroup->idGroup);
                            $delete = true;
                        }
                    }

                    if ($delete) {
                        addPageNotification('Etudiant déplacé', 'success');
                    } else {
                        addPageNotification('Etudiant ajouté', 'success');
                    }
                    $this->Groups->addStudent($studentId, $groupId);

                }
            } else {
                addPageNotification('Impossible de modifier ce groupe', 'danger');
            }
        } else {
            addPageNotification('Données corrompues', 'danger');
        }

        redirect('Administration/Semester/' . $semesterId);
    }

    public function delete_student($groupId, $studentId, $semesterId)
    {
        $this->load->model('Groups');

        if ($this->Groups->isEditable($groupId))
        {
            $this->Groups->removeStudent($studentId, $groupId);
            addPageNotification('Etudiant supprimé du groupe', 'success');
        } else {
            addPageNotification('Impossible de modifier ce groupe', 'danger');
        }
        redirect('Administration/Semester/' . $semesterId);

    }
}
