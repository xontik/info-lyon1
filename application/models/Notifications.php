<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Model {

    /**
     * Return the notifications of an user.
     * @param int $userId The id of the user
     * @return array The notifications of the user
     */
    public function getAll($userId) {
        $res = $this->db
            ->where('idUser', $userId)
            ->get('Notification')
            ->result_array();

        foreach ($res as $notif) {
            $notif['storage'] = 'seen';
        }

        return $res;
    }

    /**
     * Creates a notification.
     * @param string $content
     * @param string $type
     * @param string $icon
     * @param string $link
     * @param int $userId The id of the user to send it to
     * @return int The id of the created notification
     */
    public function create($content, $link, $userId, $type, $icon) {
        $data = array(
            'content' => $content,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
            'idUser' => $userId
        );

        $this->db->insert('Notification', $data);
        return (int) $this->db->insert_id();
    }

    /**
     * Deletes a notification.
     *
     * @param int $notificationId
     * @return bool Whether the operation was successful or not
     */
    public function delete($notificationId) {
        return $this->db->delete('Notification', array('idNotification', $notificationId));
    }

}
