<?php

session_start ();
ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', true );
print_r (error_get_last());

if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
	echo "correct model </br>";
}

$logout = new Logout("200287902");
$logout->logout();
echo "done";

 ?>