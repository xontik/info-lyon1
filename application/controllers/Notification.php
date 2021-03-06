<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

    /*
     * AJAX
     */
    public function get_alerts() {
        header('Content-Type: application/json');

        echo json_encode(
            $this->session->flashdata('pageNotif'),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_FORCE_OBJECT,
            3
        );
    }

    public function add_notification() {
        $this->load->helper('notification');

        header('Content-Type: text/plain');

        if (isset($_POST['content'])) {
            $content = htmlspecialchars($_POST['content']);
            $type = '';
            $icon = '';
            $storageType = 'page';

            if (isset($_POST['type'])) {
                $type = htmlspecialchars($_POST['type']);
            }

            if (isset($_POST['icon'])) {
                $icon = htmlspecialchars($_POST['icon']);
            }

            if (isset($_POST['storageType'])) {
                $storageType = htmlspecialchars($_POST['storageType']);
            }

            switch ($storageType) {
                case 'page':
                    addPageNotification($content, $type, $icon);
                    break;
                case 'session':
                    addSessionNotification($content, $type, $icon);
                    break;
                case 'seen':
                    addSeenNotification($content, $type, $icon);
                    break;
                default:
                    echo 'fail';
                    return;
            }

            echo 'success';
        }

    }

    public function remove_notification() {
        header('Content-Type: text/plain');

        if (isset($_POST['notifId'])
            && isset($_POST['storage'])
        ) {
            $notifId = (int) htmlspecialchars($_POST['notifId']);
            $storage = htmlspecialchars($_POST['storage']);

            switch ($storage) {
                case 'session':
                    unset($_SESSION['sessionNotif'][$notifId]);
                    break;
                case 'seen':
                    $this->load->model('Notifications');
                    $this->Notifications->delete($notifId);
                    break;
                default:
                    echo 'fail';
                    return;
            }
            echo 'success';
        } else {
            echo 'fail';
        }
    }

    public function remove_all() {
        header('Content-Length: 0');

        $this->load->model('Notifications');
        $this->Notifications->deleteAll($_SESSION['userId']);
        unset($_SESSION['sessionNotif']);
    }

}
