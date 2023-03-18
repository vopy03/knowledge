<?php require_once "../../dbconnect.php";
$_SESSION['dark_old'] = $_SESSION['dark'];
$_SESSION['dark'] = ($_SESSION['dark'] == 0) ? $_SESSION['dark'] = 1 : $_SESSION['dark'] = 0 ;
?>
