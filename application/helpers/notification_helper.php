<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Add a notification to the current user.
 * List of available types:
 * - Info (default)
 * - Success
 * - Warning
 * - Fail
 *
 * @param string $message The content of the notification (can be html)
 * @param string $type The type of the notification (optional)
 * @param string $icon The icon to be displayed (optional)
 */
function add_notification($message, $type = '', $icon = '') {
    static $default_icons = array(
        'info' => 'info_outline',
        'success' => 'done',
        'warning' => 'warning',
        'fail' => 'report_problem'
    );

    if ($type === '') {
        $type = 'info';
    }

    $lastId = isset($_SESSION['notif']) && count($_SESSION['notif']) ? max(array_keys($_SESSION['notif'])) : 0;

    $_SESSION['notif'][++$lastId] = array(
        'type' => $type,
        'icon' => $icon === '' ? $default_icons[$type] : $icon,
        'content' => $message
    );
}
