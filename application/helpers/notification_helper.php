<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function _createNotification($id, $content, $type, $icon, $storage, $link = '') {
    $instance =& get_instance();
    $instance->load->config('notification');

    if ($type === '') {
        $type = 'info';
    }

    if ($icon === '') {
        $notificationIcons = $instance->config->item('notificationIcons');
        $icon = array_key_exists($type, $notificationIcons) ? $notificationIcons[$type] : 'info_outline';
    }

    return array(
        'idNotification' => $id,
        'content' => $content,
        'type' => $type,
        'icon' => $icon,
        'storage' => $storage,
        'link' => $link
    );
}

/**
 * Add a notification to the current user.
 * The notification will only last for one page at max.
 *
 * @param string $content The content of the notification (can be html)
 * @param string $type The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 */
function addPageNotification($content, $type = '', $icon = '') {
    $lastId = isset($_SESSION['pageNotif']) && count($_SESSION['pageNotif'])
        ? max(array_keys($_SESSION['pageNotif'])) : 0;

    $_SESSION['pageNotif'][++$lastId] = _createNotification($lastId, $content, $type, $icon, 'page');
    get_instance()->session->mark_as_flash('pageNotif');
}

/**
 * Add a notification to the current user.
 * The notification will last for the session if the user doesn't click it.
 *
 * @param string $content The content of the notification (can be html)
 * @param string $type The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 * @param string $link The link to redirect to on click
 */
function addSessionNotification($content, $type = '', $icon = '', $link = '')
{
    $id = isset($_SESSION['sessionNotif']) && count($_SESSION['sessionNotif'])
        ? max(array_keys($_SESSION['sessionNotif'])) + 1 : 1;

    $_SESSION['sessionNotif'][$id] = _createNotification($id, $content, $type, $icon, 'session', $link);
}

/**
 * Add a notification to the current user.
 * The notification will last until the user click it.
 * This is the only way to send
 *
 * @param string $content The content of the notification (can be html)
 * @param string $link The link to redirect to on click (optionnal)
 * @param int $userId The user who will receive it (optionnal)
 * @param string $type The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 */
function addSeenNotification($content, $link = '', $userId = -1, $type = '', $icon = '')
{
    get_instance()->load->model('Notifications');
    $userId = $userId === -1 ? $_SESSION['userId'] : $userId;

    $this->notification_model->create($content, $link, $userId, $type, $icon);
}