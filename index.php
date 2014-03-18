<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
require_once 'Queue.php';

function log_message($T, $M) {
    // Do something
}

$j = new Queue();
//echo $j->create('maincontroller', 'funzione', array('culo'), 'un job di test');
echo $j->worker(10);
