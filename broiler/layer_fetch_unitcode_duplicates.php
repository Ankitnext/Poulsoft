<?php
//layer_fetch_unitcode_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$unit_code = $_GET['unit_code'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `layer_units` WHERE `unit_code` = '$unit_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
