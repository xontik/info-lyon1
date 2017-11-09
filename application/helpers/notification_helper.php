<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function _createNotification($id, $content, $type, $icon, $storage, $link = '', $duration = 'Infinity')
{
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
        'id' => $id,
        'content' => $content,
        'type' => $type,
        'icon' => $icon,
        'storage' => $storage,
        'link' => $link,
        'duration' => $duration
    );
}

/**
 * Add a notification to the current user.
 *
 * @param string    $content    Can be HTML
 * @param string    $type       (default: info)
 * @param string    $icon       (optional)
 * @param int       $duration   (default: 4000)
 */
function addPageNotification($content, $type = '', $icon = '', $duration = 4000)
{
    $_SESSION['pageNotif'][] = _createNotification(-1, $content, $type, $icon, 'page', '', $duration);
    get_instance()->session->mark_as_flash('pageNotif');
}

/**
 * Add a notification to the current user.
 * The notification will last for the session if the user doesn't click it.
 *
 * @param string    $content    Can be HTML
 * @param string    $type       (default: info)
 * @param string    $icon       (optional)
 * @param string    $link       (optional)
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
 * This is the only way to send notification accross session.
 *
 * @param string    $content    Can be HTML
 * @param string    $link       (optional)
 * @param int       $userId     (default: current)
 * @param string    $type       (default: info)
 * @param string    $icon       (optional)
 */
function addSeenNotification($content, $link = '', $userId = -1, $type = '', $icon = '')
{
    get_instance()->load->model('Notifications');
    $userId = $userId === -1 ? $_SESSION['userId'] : $userId;

    $this->notification_model->create($content, $link, $userId, $type, $icon);
}
