<?php
//layer_check_shed1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$code = $_GET['code'];

$count = 0;
$sql = "SELECT * FROM `layer_shed_allocation` WHERE `shed_code` = '$code' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
