<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_Timetable extends CI_Controller
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

    public function edit($type = null, $who = null)
    {
        if (!is_null($type)) {
            $type = htmlspecialchars($type);
            switch ($type) {
                case '':
                    $who = null;
                    break;
                case 'group':
                case 'teacher':
                case 'room':
                    $who = (int) htmlspecialchars($who);
                    break;
                default:
                    addPageNotification('Données corrompues', 'danger');
                    redirect('Timetable');
            }
        }

        $this->load->model('Timetables');
        $this->load->model('Students');

        if (isset($_POST['url'])) {
            // Decode URL
            $url = htmlspecialchars($_POST['url']);

            // If data received is resource number
            if (is_numeric($url)) {
                $resource = (int) $url;
            } else {
                // Remove URL useless parts
                $url = substr($url, strpos($url, '?') + 1);
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

            // Default values for $type and $who

            if (!isset($type) || !isset($who)) {
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
            }

            if ($this->Timetables->hasTimetable($who, $type)
                && $this->Timetables->update($resource, $who, $type)
                || $this->Timetables->create($resource, $who, $type)
            ) {
                addPageNotification('Emploi du temps modifié avec succès', 'success');
                redirect('Timetable' . ($type === 'room' ? "/room/$who" : ''));
            }

            addPageNotification('Erreur lors de la modification de l\'emploi du temps', 'danger');
            redirect("Timetable/edit/$type/$who");
        }

        addPageNotification('Données corrompues', 'danger');
        redirect("Timetable/edit/$type/$who");
    }

    public function update($resource, $weekNum = 0, $room = '')
    {
        $resource = (int) htmlspecialchars($resource);
        $weekNum = (int) htmlspecialchars($weekNum);
        $room = htmlspecialchars($room);

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

        redirect('Timetable' . ($room ? "/room/$room" : '') . ($weekNum ? "/$weekNum" : ''));
    }

    public function create()
    {
        if (isset($_POST['roomName']) && isset($_POST['url'])) {

            $this->load->model('Timetables');

            $roomName = htmlspecialchars($_POST['roomName']);
            $url = htmlspecialchars($_POST['url']);

            // If resource number sent
            if (is_numeric($url)) {
                $resource = (int) $url;
            } else {

                // Remove URL useless parts
                $url = substr($url, strpos($url, '?') + 1);
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

            if ($this->Timetables->hasTimetable($roomName, 'room')
                && $this->Timetables->update($resource, $roomName, 'room')
                || $this->Timetables->create($resource, $roomName, 'room')
            ) {
                addPageNotification('Salle créée avec succès', 'success');
                redirect('Timetable/room');
            }

            addPageNotification('Erreur lors de la modification de l\'emploi du temps', 'danger');
            redirect('Timetable/room');
        }

        addPageNotification('Données corrompues', 'danger');
        redirect('Timetable/room');
    }
}
