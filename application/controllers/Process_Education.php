<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Education extends CI_Controller
{

    public function add_teacher($semesterId)
    {
        $this->load->model('Semesters');
        $this->load->model('Educations');
        if (isset($_POST['teacherId']) && isset($_POST['groupId']) && isset($_POST['subjectId'])) {

            $teacherId = $_POST['teacherId'];
            $groupId = $_POST['groupId'];
            $subjectId = $_POST['subjectId'];

            if( $this->Semesters->isEditable($semesterId)) {
                if($this->Educations->create($subjectId,$groupId,$teacherId)){
                    addPageNotification('Affectation effectuée', 'success');
                } else {
                    addPageNotification('Erreur lors de l\'affectation', 'danger');
                }
            } else {
                addPageNotification('Ce semestre ne peut pas être modifié', 'danger');
            }

        } else {
            addPageNotification('Données corrompues','danger');
        }
        redirect('Administration/semester/'.$semesterId);

    }




}
