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
                // TODO Make it work
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

            switch ($_SESSION['userType']) {
                case 'student':
                    $group = $this->Students->getGroup($_SESSION['id']);
                    if ($group === FALSE) {
                        addPageNotification('Il semblerait que vous n\'ayez pas de groupe.<br>'
                            . 'L\'opération est impossible pour le moment', 'warning');
                        redirect('Timetable/edit');
                    }

                    $who = $group->idGroup;
                    $type = 'group';
                    break;
                case 'teacher':
                    $who = $_SESSION['id'];
                    $type = 'teacher';
                    break;
                default:
                    redirect('/');
            }

            if ($this->Timetables->hasTimetable($who, $type)
                && $this->Timetables->update($resource, $who, $type)
                || $this->Timetables->create($resource, $who, $type)
            ) {
                addPageNotification('Emploi du temps modifié avec succès', 'success');
                redirect('Timetable');
            }

            addPageNotification('Erreur lors de la modification de l\'emploi du temps', 'danger');
            redirect('Timetable/edit');
        }

        addPageNotification('Données corrompues', 'danger');
        redirect('Timetable/edit');
    }

    public function update($resource)
    {
        $resource = (int) htmlspecialchars($resource);
        $this->load->model('Timetables');

        if ($resource === 0) {
            addPageNotification('Données corrompues', 'danger');
            redirect('Timetable');
        }

        if ($this->Timetables->setJSON($resource, '')) {
            addPageNotification('Emploi du temps mis à jour', 'success');
        } else {
            addPageNotification('Erreur lors de la mise à jour de l\'emploi du temps', 'danger');
        }

        redirect('Timetable');
    }
}
