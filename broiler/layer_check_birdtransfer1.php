<?php
//layer_check_birdtransfer1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$trnum = $_GET['trnum'];

$count = 0;
$sql = "SELECT * FROM `layer_bird_transfer` WHERE `trnum` = '$trnum' AND (`flag` = '1' OR `cls_flag` = '1')";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
