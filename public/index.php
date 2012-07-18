<?php

$offset = 60;
header('Date: '.  gmdate( "D, d M Y H:i:s" ) .' GMT');
header('Cache-control: max-age='. $offset);
header('Expires: ' . gmdate( 'D, d M Y H:i:s', time()+$offset ) . ' GMT');

echo 'test';

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

// custom functionss

function consolelog($value) {
    echo '<script>console.log("' . $value . '");</script>';
}