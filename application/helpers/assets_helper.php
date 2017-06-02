<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('css_url')) {
    function css_url($name)
    {
        return base_url() . 'assets/css/' . $name . '.css';
    }
}

if (!function_exists('js_url')) {
    function js_url($name)
    {
        return base_url() . 'assets/js/' . $name . '.js';
    }
}

if (!function_exists('img_url')) {
    function img_url($name)
    {
        return base_url() . 'assets/images/' . $name;
    }
}

if (!function_exists('img')) {
    function html_img($name, $alt = '', $width = '')
    {
        return '<img src="' . img_url($name) . '" alt="' . $alt . '" width="' . $width . '" />';
    }
}
