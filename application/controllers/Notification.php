<?php
/**
 * Created by PhpStorm.
 * User: enzob
 * Date: 26/10/2017
 * Time: 15:51
 */

class Notification extends CI_Controller {

    public function get_notifications() {
        $this->load->model('notification_model');

        header('Content-Type: application/json');

        function castObject(&$element) {
            $element = (array) $element;
        }

        /*
        $seenNotifications = $this->notification_model->get($_SESSION['userId']);
        array_map('caseObject', $seenNotifications);
        */

        $notifications = array_merge(
            isset($_SESSION['notif']) ? $_SESSION['notif'] : array(),
            isset($_SESSION['notifFlash']) ? $_SESSION['notifFlash'] : array()
            //$seenNotifications
        );

        echo json_encode($notifications,
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
                case 'database':
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
        $this->load->model('notification_model');

        header('Content-Type: text/plain');

        if (isset($_POST['notifId']) && isset($_POST['storageType'])) {
            $notifId = intval(htmlspecialchars($_POST['notifId']));
            $storageType = htmlspecialchars($_POST['storageType']);

            switch ($storageType) {
                case 'database':
                    $this->notification_model->delete($notifId);
                    break;
                case 'session':
                    unset($_SESSION['notif'][$notifId]);
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

}