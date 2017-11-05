<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Timetable extends CI_Controller
{

    public function edit()
    {
        $this->load->model('Timetables');
        $this->load->model('Students');

        if (isset($_POST['url']))
        {
            $url = htmlspecialchars($_POST['url']);

            // If only resource number sent
            if (is_numeric($url)) {
                $resource = (int) $url;
            } else {
                // Remove useless parts
                $url = substr($url, strpos($url, '?'));
                $url = explode('/', $url);
                foreach ($url as $parameter) {
                    $parameter = explode('=', $parameter);
                    if ($parameter[0] === 'resources') {
                        $resource = (int) $parameter[1];
                    }
                }

                if (!isset($resource)) {
                    addPageNotification('Impossible de trouver de ressource dans l\'url.<br>'
                        . 'Essayez de l\'indiquer manuellement', 'warning');
                    redirect('Timetable/edit');
                }
            }

            $group = $this->Students->getGroup($_SESSION['id']);
            if ($group === FALSE) {
                addPageNotification('Il semblerait que vous n\'ayez pas de groupe.<br>'
                    . 'L\'opération est impossible pour le moment', 'warning');
                redirect('Timetable/edit');
            }

            $this->Timetables->delete($resource);
            if ($this->Timetables->create($resource, '', $group->idGroup, $_SESSION['userType'] === 'student' ? 'group' : 'teacher')) {
                addPageNotification('Emploi du temps modifié avec succès', 'success');
                redirect('Timetable');
            } else {
                addPageNotification('Erreur lors de la création de l\'emploi du temps', 'danger');
                redirect('Timetable/edit');
            }
        }

        addPageNotification('Données corrompues', 'danger');
        redirect('/Timetable/edit');
    }
}