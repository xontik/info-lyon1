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

        $count = count($res);
        for ($i = 0; $i < $count; $i++) {
            $res[$i]['storage'] = 'seen';
        }

        return $res;
    }

    /**
     * Creates a notification.
     * @param string    $content
     * @param string    $type
     * @param string    $icon
     * @param string    $link
     * @param int       $userId The id of the user to send it to
     * @return int The id of the created notification
     */
    public function create($content, $link, $userId, $type = 'info', $icon = '') {
        $this->load->config('notification');

        if (!$icon) {
            $notificationIcons = $this->config->item('notificationIcons');
            $icon = array_key_exists($type, $notificationIcons) ? $notificationIcons[$type] : 'info_outline';
        }

        $data = array(
            'content' => $content,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
            'idUser' => $userId
        );

        $this->db->insert('Notification', $data);
        return $this->db->insert_id();
    }

    /**
     * Deletes a notification.
     *
     * @param int $notificationId
     * @return int Affected rows
     */
    public function delete($notificationId) {
        $this->db
            ->where('idNotification', $notificationId)
            ->delete('Notification');
        return $this->db->affected_rows();
    }

    /**
     * Deletes all the notifications of an user.
     *
     * @param $userId
     * @return int Affected rows
     */
    public function deleteAll($userId) {
        $this->db
            ->where('idUser', $userId)
            ->delete('Notification');
        return $this->db->affected_rows();
    }

}
