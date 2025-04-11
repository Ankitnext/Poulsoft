<?php
//breeder_fetch_shedallocate_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$farm_code = $_GET['farm_code'];
$unit_code = $_GET['unit_code'];
$shed_code = $_GET['shed_code'];
$batch_code = $_GET['batch_code'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `farm_code` = '$farm_code' AND `unit_code` = '$unit_code' AND `shed_code` = '$shed_code' AND `batch_code` = '$batch_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
