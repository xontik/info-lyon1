<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Course extends CI_Controller
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

    /*
     * AJAX
     */
    public function add_teaching_unit()
    {
        if (isset($_POST['courseId'])
            && isset($_POST['TUids'])
        ) {
            $this->load->model('Courses');

            $courseId = (int) htmlspecialchars($_POST['courseId']);
            $TUids = $_POST['TUids'];

            if ($this->Courses->isEditable($courseId)) {

                $ids = array();

                foreach ($TUids as $TUid) {
                    if ($this->Courses->linkTeachingUnit($TUid, $courseId)) {
                        $ids[] = $TUid;
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($ids);
            }
        }

        header('Content-Length: 0', TRUE, 400);
    }

    /*
     * AJAX
     */
    public function remove_teaching_unit()
    {
        if (isset($_POST['courseId'])
            && isset($_POST['TUids'])
        ) {

            $courseId = (int) htmlspecialchars($_POST['courseId']);
            $TUids = $_POST['TUids'];

            $this->load->model('Courses');

            if ($this->Courses->isEditable($courseId)) {

                $ids = array();

                foreach ($TUids as $TUid) {
                    if ($this->Courses->unlinkTeachingUnit($TUid, $courseId)) {
                        $ids[] = $TUid;
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($ids);
            }

        }

        header('Content-Length: 0', TRUE, 400);
    }

    /*
     * AJAX
     */
    public function get_year() {

        if (isset($_POST['courseId'])) {

            $courseId = (int) htmlspecialchars($_POST['courseId']);

            $this->load->model('Courses');

            $course = $this->Courses->get($courseId);
            if ($course === FALSE) {
                header('Content-Length: 0', TRUE, 400);
                exit(0);
            }

            $thisYear = (int) date('Y');
            $courseYear = (int) $course->creationYear;

            $year = $courseYear < $thisYear ? $thisYear + 1 : $courseYear;

            header('Content-Type: application/json');
            echo json_encode(array( 'year' => $year ));
            exit(0);
        }

        header('Content-Length: 0', TRUE, 400);

    }

    public function add()
    {
        $this->load->model('Courses');

        if (isset($_POST['year']) && isset($_POST['type']))
        {
            $year = (int) htmlspecialchars($_POST['year']);
            $type = htmlspecialchars($_POST['type']);

            if ($year !== 0 && strlen($type) === 2) {
                if (!$this->Courses->exists($type, $year)) {
                    if ($this->Courses->create($year, $type)) {
                        addPageNotification('Parcours créé avec succès', 'success');
                    } else {
                        addPageNotification('Erreur lors de la création du parcours', 'danger');
                    }
                } else {
                    addPageNotification('Un parcours de ce type existe déjà', 'warning');
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
        $this->load->model('Courses');

        if (isset($_POST['courseId'])) {
            $courseId = (int) htmlspecialchars($_POST['courseId']);

            if ($this->Courses->isEditable($courseId)) {

                if ($this->Courses->delete($courseId)) {
                    addPageNotification('Parcours supprimé avec succès', 'success');
                } else {
                    addPageNotification('Erreur lors de la suppression du parcours', 'danger');
                }
            } else {
                addPageNotification('Ce parcours ne peut pas être supprimé', 'danger');
            }
        } else {
            addPageNotification('Données manquantes', 'warning');
        }

        redirect('Administration');
    }
}
