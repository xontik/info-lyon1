<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['userTypes'] = array('student', 'teacher', 'secretariat');

$config['dataViewKeys'] = array(
    'css' => 'array',
    'js' => 'array',
    'page' => 'string',
    'title' => 'string',
    'view' => 'string',
    'notifications' => 'unmodifiable',
    'data' => 'unmodifiable',
);
