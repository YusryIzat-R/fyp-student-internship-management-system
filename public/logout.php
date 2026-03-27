<?php
session_start();

/* Ddestroy the session */
session_unset();
session_destroy();

/* Redirect it to the login page */
header("Location: ../public/login/php");

exit;

?>