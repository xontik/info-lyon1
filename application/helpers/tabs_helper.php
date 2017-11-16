<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function createTab($content, $url, $active = false)
{
    $tab = new stdClass();
    $tab->content = $content;
    $tab->url = $url;
    $tab->active = $active;
    return $tab;
}
