<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // root
define('DB_PASSWORD', 'root');          // root
define('DB_DATABASE', 'buysell'); //buysell
$connection = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
if ($connection->connect_errno) {
    die("ERROR : -> ".$DBcon->connect_error);
}

?>