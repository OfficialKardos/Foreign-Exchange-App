<?php

// Include autoloader
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../constants.php';
error_reporting(E_ALL);
ini_set('display_errors', 'Off');

if (DB_REQUIRED == true) {
    switch (DB_TYPE) {
        case 'mysql':
            $mysql_dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
            $mysql_dsn_ro = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST_RO . ';port=' . DB_PORT . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_PERSISTENT => true,
            ];
            $html_die = '<strong>Could not connect to the reader database: </strong>';
            $html_die .= '<p>Please check that your configured database server is running...</p>';
            try {
                $db = new PDO($mysql_dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                echo 'Read/Write Database connection error! ' . $e->getMessage();
                die("$html_die <br/> RW");
            }
            try {
                $db_ro = new PDO($mysql_dsn_ro, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                echo 'Read-Only Database connection error! ' . $e->getMessage();
                die("$html_die <br/> RO");
            }
            break;

        default:
            throw new Exception('Invalid database type: ' . DB_TYPE);
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

$params = [];
$params['db'] = $db;
$params['db_ro'] = $db_ro;
$GLOBALS['db_ro'] = $db_ro;

require_once(PAGE_DIR . 'purchase-page\purchase.php');