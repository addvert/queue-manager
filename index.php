<?php
// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
require_once 'Queue.php';

// You may want to rewrite this function
function log_message($T, $M) {
    // Do something
}

//$j = new Queue();
//echo $j->create('testcontroller', 'index', array('paramtest'), 'Testing the Library');
//echo $j->worker();
