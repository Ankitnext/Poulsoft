<?php
//layer_fetch_flock_autoname.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$farm_code = $_GET['farm_code'];
$unit_code = $_GET['unit_code'];
$shed_code = $_GET['shed_code'];
$batch_code = $_GET['batch_code'];
$flk_name = "";

$sql = "SELECT * FROM `layer_sheds` WHERE `code` = '$shed_code' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $shed_key = "";
while($row = mysqli_fetch_array($query)){ $shed_key = $row['shed_code']; }

$sql = "SELECT * FROM `layer_batch` WHERE `code` = '$batch_code' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $batch_key = "";
while($row = mysqli_fetch_array($query)){ $batch_key = $row['batch_code']; }

$sql = "SELECT * FROM `layer_shed_allocation` WHERE `farm_code` = '$farm_code' AND `unit_code` = '$unit_code' AND `shed_code` = '$shed_code' AND `batch_code` = '$batch_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $count++;

$flk_name = $shed_key."-".$batch_key."-".$count;
echo $flk_name;
?>
