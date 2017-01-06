<?php

define('DB_SERVER', '');
define('DB_USERNAME', ''); // root
define('DB_PASSWORD', '');          // root
define('DB_DATABASE', ''); //buysell
$connection = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
if ($connection->connect_errno) {
    die("ERROR : -> ".$DBcon->connect_error);
}

?>
