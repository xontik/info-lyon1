<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Semester extends CI_Controller
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

    public function add()
    {
        $this->load->model('Semesters');
        $this->load->model('Courses');

        if (isset($_POST['schoolYear']) && isset($_POST['courseId']))
        {
            $courseId = (int) htmlspecialchars($_POST['courseId']);
            $schoolYear = (int) htmlspecialchars($_POST['schoolYear']);

            if ($this->Courses->exists($courseId))
            {

                $semester = (object) array(
                    'delayed' => isset($_POST['delayed']) ? 1 : 0,
                    'schoolYear' => $schoolYear,
                    'courseId' => $courseId,
                    'courseType' => $this->Courses->getType($courseId)
                );

                $now = new DateTime();
                $dateStart = $this->Semesters->getPeriodObject($semester)->getBeginDate();

                if ($now < $dateStart) {
                    if ($semesterId = $this->Semesters->create($semester->courseId, $semester->delayed, $semester->schoolYear)) {
                        addPageNotification('Semestre créé', 'success');
                        redirect('Administration/Semester/' . $semesterId);
                    }

                    addPageNotification('Ce semestre existe déjà', 'danger');
                } else {
                    addPageNotification('Création impossible, le semestre aurait déjà commencé', 'danger');
                }
            } else {
                addPageNotification('Parcours inconnu', 'danger');
            }
        } else {
            addPageNotification('Données corrompues', 'danger');
        }

        redirect('Administration');
    }

    public function delete($semesterId)
    {
        $this->load->model('Semesters');

        if ($this->Semesters->isDeletable($semesterId))
        {
            if ($this->Semesters->delete($semesterId)) {
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