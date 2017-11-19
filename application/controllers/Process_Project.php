<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Project extends CI_Controller
{

    public function create() {


        $this->load->model('Projects');
        $this->load->model('Teachers');

        $teacher = $this->Teachers->get($_SESSION['id']);

        if(is_null($teacher)) {
            addPageNotification('Seul un professeur peut créer un projet');
        }


        $lastProject = $this->Teachers->getLastProject($_SESSION['id']);


        if (is_null($lastProject) || !is_null($lastProject->projectName) ) {
            if (!$this->Projects->create($_SESSION['id'])) {
                addPageNotification('Erreur de la création du project', 'danger');
            }
        } else {
            addPageNotification('Merci de completer le dernier project avant d\'en creer un  nouveau', 'warning');
        }

        redirect('/Project');


    }

    /**
     * AJAX
     */

    public function get_student_available() {

        $this->load->model('Projects');

        $students = $this->Projects->getStudentsWithoutProject();
        $output = array();
        foreach ($students as $key => $value) {
            $output[$value] = null;
        }
        header('Content-Type: application/json');
        echo json_encode($output);
    }

    /**
     * AJAX
     */

    public function add_member() {

        $this->load->model('Projects');
        $this->load->model('Teachers');
        $this->load->model('Students');


        header('Content-Type: application/json');

        if ( !isset($_POST['projectId']) || !isset($_POST['studentId'])) {
            addPageNotification('Données corrompues', 'danger');
            echo json_encode(array('error' => true));
        } else {

            $studentId = htmlspecialchars($_POST['studentId']);
            $projectId = htmlspecialchars($_POST['projectId']);

            if (!$this->Teachers->isTutor($projectId, $_SESSION['id'])) {
                addPageNotification('Données invalides', 'danger');
                echo json_encode(array('error' => true));
            } else {

                $student = $this->Students->get($studentId);
                if ($this->Projects->isUserInProject($student->idUser, $projectId)) {
                    addPageNotification('Etudiant déja membre du projet', 'danger');
                    echo json_encode(array('error' => true));
                } else {
                    if (!$this->Students->isAvailableForProject($studentId)) {
                        addPageNotification('Etudiant déja membre d\'un autre groupe', 'danger');
                        echo json_encode(array('error' => true));
                    } else {
                        if ($this->Projects->addMemeber($projectId, $studentId)) {
                            echo json_encode(array('error' => false);
                        } else {
                            addPageNotification('Erreur lors de l\'ajout de l\'étudiant', 'danger');
                            echo json_encode(array('error' => true));
                        }
                    }

                }
            }

        }
    }

    public function delete_member($projectId, $studentId) {

        $this->load->model('Projects');
        $this->load->model('Teachers');
        $this->load->model('Students');


        if (!$this->Teachers->isTutor($projectId, $_SESSION['id'])) {
            addPageNotification('Vous n\avez pas les droits sur ce projet', 'danger');
            redirect('Project');
        } else {
            $student = $this->Students->get($studentId);
            if (!$this->Projects->isUserInProject($student->idUser, $projectId)) {
                addPageNotification('Etudiant ne fait pas parti du projet', 'danger');
            } else {
                if ($this->Projects->deleteMemeber($projectId, $studentId)) {
                    addPageNotification('Etudiant supprimé !', 'success');
                } else {
                    addPageNotification('Erreur lors de la suppression de l\'étudiant', 'danger');
                }
            }
            redirect('Project/manage/'. $projectId);

        }
    }

    public function delete($projectId) {

        $this->load->model('Projects');
        $this->load->model('Teachers');
        $this->load->model('Students');


        if (!$this->Teachers->isTutor($projectId, $_SESSION['id'])) {
            addPageNotification('Vous n\avez pas les droits sur ce projet', 'danger');
            redirect('Project');
        } else {
                if ($this->Projects->delete($projectId)) {
                    addPageNotification('Projet supprimé !', 'success');
                    redirect('Project');
                } else {
                    addPageNotification('Erreur lors de la suppression du projet', 'danger');
                    redirect('Project/manage/'. $projectId);
                }


        }
    }


    public function change_name($projectId) {

        if (!isset($_POST['projectName'])) {
            addPageNotification('Données corrompues', 'danger');
            redirect('/Project/manage/' . $projectId);
        }

        $projectName = htmlspecialchars($_POST['projectName']);

        $this->load->model('Projects');
        $this->load->model('Teachers');
        $this->load->model('Students');


        if (!$this->Teachers->isTutor($projectId, $_SESSION['id'])) {
            addPageNotification('Vous n\avez pas les droits sur ce projet', 'danger');
            redirect('Project');
        } else {
                $project = $this->Projects->get($projectId);
                if($projectName == $project->projectName) {
                    redirect('Project/manage/'. $projectId);
                }

                if ($this->Projects->changeName($projectId, $projectName)) {
                    addPageNotification('Projet renommé !', 'success');
                } else {
                    addPageNotification('Erreur du changeement de nom', 'danger');
                }
                redirect('Project/manage/'. $projectId);
        }


    }

}
