<?php

session_start ();

if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
	echo "correct model </br>";
}

$logout = new Logout("200287902");
$logout->logout();
echo "done";

 ?>