<?php

define('DS', DIRECTORY_SEPARATOR);
require __DIR__ . DS . 'app' . DS . 'config' . DS . 'config.php';
require __DIR__ . DS . 'constants.php';

session_start();
session_regenerate_id();

if ((!file_exists(LOG_DIR)) || (!is_writable(LOG_DIR)) || (phpversion() < 8.0)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo '<p>ERROR: Initializing application</p>';
    echo '<p>Check the following:</p>';
    echo '<p>PHP version: ' .floor((float) phpversion()) . ' required 8.0.x</P>';
    die('<p>CHeck that ' . LOG_DIR . ' exists and is writable by your web server</p>');
} else {
    if (!isset($_GET['url'])) {
        $url = '/';
    } else {
        $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    require_once(LIB_DIR . 'init.php');
}
