<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function _createNotification($id, $content, $notifType, $icon, $storageType) {
    $instance =& get_instance();
    $instance->load->config('notification');

    if ($notifType === '') {
        $notifType = 'info';
    }

    if ($icon === '') {
        $notificationIcons = $instance->config->item('notificationIcons');
        $icon = array_key_exists($notifType, $notificationIcons) ? $notificationIcons[$notifType] : 'info_outline';
    }

    return array(
        'id' => $id,
        'content' => $content,
        'notifType' => $notifType,
        'icon' => $icon,
        'storageType' => $storageType
    );
}

/**
 * Add a notification to the current user.
 * The notification will only last for one page at max.
 *
 * @param string $content The content of the notification (can be html)
 * @param string $notifType The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 */
function addPageNotification($content, $notifType = '', $icon = '') {
    $lastId = isset($_SESSION['notifFlash']) && count($_SESSION['notifFlash'])
        ? max(array_keys($_SESSION['notifFlash'])) : 0;

    $_SESSION['notifFlash'][++$lastId] = _createNotification($lastId, $content, $notifType, $icon, 'page');
    get_instance()->session->mark_as_flash('notifFlash');
}

/**
 * Add a notification to the current user.
 * The notification will last for the session if the user doesn't click it.
 *
 * @param string $content The content of the notification (can be html)
 * @param string $notifType The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 */
function addSessionNotification($content, $notifType = '', $icon = '') {
    $lastId = isset($_SESSION['notif']) && count($_SESSION['notif'])
        ? max(array_keys($_SESSION['notif'])) : 0;

    $_SESSION['notif'][++$lastId] = _createNotification($lastId, $content, $notifType, $icon, 'session');
}

/**
 * Add a notification to the current user.
 * The notification will last until the user click it.
 * This is the only way to send
 *
 * @param string $content The content of the notification (can be html)
 * @param string $notifType The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 * @param int $userId The user who will receive it (optionnal)
 */
function addSeenNotification($content, $notifType = '', $icon = '', $userId = -1) {
    $this->load->model('notification_model');
    $userId = $userId === -1 ? $_SESSION['userId'] : $userId;

    $this->notification_model->create($content, $notifType, $icon, $userId);
}
