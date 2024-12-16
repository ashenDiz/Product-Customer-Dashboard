<?php
// ewaste_management/logout.php
session_start();
session_unset();
session_destroy();
header('Location: ../screens/collector_login.php');
exit();
?>
