<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Group extends CI_Controller
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

    public function add($semesterId)
    {
        $semesterId = (int) htmlspecialchars($semesterId);

        $this->load->model('Groups');
        $this->load->model('Semesters');

        if ($this->Semesters->isDeletable($semesterId)) {
            if ($groupName = $this->Groups->create($semesterId)) {
                addPageNotification('Groupe ' . $groupName . ' ajouté avec succès', 'success');
            } else {
                addPageNotification('Erreur lors de la création du groupe', 'danger');
            }
        } else {
            addPageNotification('Ce semestre ne peut pas être modifié', 'danger');
        }

        redirect('Administration/Semester/' . $semesterId);
    }

    public function delete($groupId, $semesterId)
    {
        $this->load->model('Groups');
        $this->load->model('Semesters');

        if ($this->Semesters->isDeletable($semesterId)) {
            if ($this->Groups->delete($groupId)) {
                addPageNotification('Groupe supprimé', 'success');
            } else {
                addPageNotification('Erreur lors de la suppression du groupe', 'danger');
            }
        } else {
            addPageNotification('Ce semestre ne peut etre modifié', 'danger');
        }

        redirect('Administration/Semester/' . $semesterId);
    }
    /**
     * AJAX
     */
    public function add_student($semesterId)
    {
        $this->load->model('Groups');
        $this->load->model('Semesters');
        $this->load->model('Students');


        $out = array();
        if (isset($_POST['studentId']) && isset($_POST['groupId'])) {
            $studentId = htmlspecialchars($_POST['studentId']);
            $groupId = htmlspecialchars($_POST['groupId']);
            $concurrentSemesters = $this->Semesters->getConcurrent($semesterId);

            if (!$this->Students->get($studentId)) {
                addPageNotification('Impossible d\'ajouter cet étudiant car il n\'existe pas','danger');
                $out['type'] = 'danger';
            } else {
                if ($this->Groups->isEditable($groupId))
                {
                    if ($group = $this->Semesters->anyHasStudent($studentId, $concurrentSemesters))
                    {
                        addPageNotification('Impossible d\'ajouter cet étudiant car il est déjà en '
                        . $group->groupName . $group->courseType,'danger');
                        $out['type'] = 'danger';

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
                            $out['text'] = 'Etudiant déplacé';
                            $out['type'] = 'success';

                        } else {
                            $out['text'] = 'Etudiant ajouté';
                            $out['type'] = 'success';
                        }
                        $this->Groups->addStudent($studentId, $groupId);

                    }
                } else {
                    addPageNotification('Impossible de modifier ce groupe','danger');
                    $out['type'] = 'danger';
                }
            }
        } else {
            addPageNotification('Données corrompues','danger');
            $out['type'] = 'danger';
        }

        header('Content-Type: application/json');
        echo json_encode($out);
    }

    public function delete_student($groupId, $studentId, $semesterId)
    {
        $this->load->model('Groups');

        if ($this->Groups->isEditable($groupId))
        {
            if($this->Groups->removeStudent($studentId, $groupId)) {
                $out['text'] = 'Etudiant supprimé du groupe';
                $out['type'] = 'success';
                if (!isset($_POST['ajax'])) {
                    addPageNotification($out['text'],$out['type']);
                }
            } else {
                $out['type'] = 'danger';
                addPageNotification('Impossible de supprimer cet élève','danger');

            }

        } else {
            $out['type'] = 'danger';
            addPageNotification('Impossible de modifier ce groupe','danger');
        }


        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($out);
        } else {
            redirect('Administration/Semester/' . $semesterId);
        }

    }
}
