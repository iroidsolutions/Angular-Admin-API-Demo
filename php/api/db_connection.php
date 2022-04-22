<?php

ob_start();
error_reporting(E_ALL ^ E_DEPRECATED);
// header("Cache-control: private, no-cache");
// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// header("Pragma: no-cache");
// header("Cache: no-cahce");
ini_set('max_execution_time', 90000);
ini_set("memory_limit", -1);
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
ini_set('memory_limit', '64M');

if ($_SERVER['HTTP_HOST'] == "localhost") {
    $isLocal = true;
} else {
    $isLocal = false;
}

if ($isLocal == true) {
    DEFINE('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
    DEFINE('DB_SERVER_USERNAME', 'root');
    DEFINE('DB_SERVER_PASSWORD', '11');
    DEFINE('DB_DATABASE', 'dbangular_demo');
    DEFINE('SITE_FOLDER', 'angular_demo');
    // echo "connected";
    $conn = mysqli_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD,DB_DATABASE) or die("Error " . mysql_error($conn));

    DEFINE("SITE_URL_REMOTE", "http://" . $_SERVER['HTTP_HOST'] . '/' . SITE_FOLDER);
} else {
    DEFINE('DB_SERVER', 'localhost');
    DEFINE('DB_SERVER_USERNAME', 'live_server_username');
    DEFINE('DB_SERVER_PASSWORD', 'live_server_password');
    DEFINE('DB_DATABASE', 'db_live_server');
    DEFINE('SITE_FOLDER', 'db_live_server_dir');
    DEFINE('SITE_URL_REMOTE', 'https://' . $_SERVER['HTTP_HOST']  . '/' . SITE_FOLDER);

    $conn = mysqli_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD,DB_DATABASE) or die("Error " . mysql_error($conn));

}


mysqli_query($conn,"SET SESSION time_zone = '-6:00'");
date_default_timezone_set('America/Guatemala');

DEFINE('ENCRYPT_KEY', 'vah@_inf0s0l');

mysqli_query($conn,"SET NAMES 'utf8'");
mysqli_query($conn,"SET CHARACTER SET utf8");

//ob_end_flush();
?>