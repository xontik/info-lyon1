<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Course extends CI_Controller
{
    /*
     * AJAX
     */
    public function add_teaching_unit()
    {
        $this->load->model('Courses');

        $courseId = (int) htmlspecialchars($_POST['courseId']);
        $TUids = $_POST['TUids'];

        $ids = array();
        if ($this->Courses->isEditable($courseId)) {

            foreach ($TUids as $TUid) {
                if ($this->Courses->linkTeachingUnit($TUid, $courseId)) {
                    $ids[] = $TUid;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($ids);
    }

    /*
     * AJAX
     */
    public function remove_teaching_unit()
    {
        $this->load->model('Courses');
        $ids = array();

        $courseId = (int) htmlspecialchars($_POST['courseId']);
        $TUids = $_POST['TUids'];

        if ($this->Courses->isEditable($courseId)) {

            foreach ($TUids as $TUid) {
                if ($this->Courses->unlinkTeachingUnit($TUid, $courseId)) {
                    $ids[] = $TUid;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($ids);
    }

    /*
     * AJAX
     */
    public function get_year() {

        $courseId = (int) htmlspecialchars($_POST['courseId']);

        $this->load->model('Courses');

        $course = $this->Courses->get($courseId);

        $thisYear = (int) date('Y');
        if(!$course) {
            $ouput = array( 'year' => $thisYear + 1);
        }

        $courseYear = (int) $course->creationYear;

        if($courseYear < $thisYear) {
            $ouput = array( 'year' => $thisYear + 1);
        } else {
            $ouput = array( 'year' => $courseYear);
        }

        header('Content-Type: application/json');
        echo json_encode($ouput);

    }

    public function add()
    {
        $this->load->model('Courses');

        if (isset($_POST['year']) && isset($_POST['type']))
        {
            $year = (int) htmlspecialchars($_POST['year']);
            $type = htmlspecialchars($_POST['type']);

            if ($year !== 0 && strlen($type) === 2) {
                if ($this->Courses->create($year, $type)) {
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
