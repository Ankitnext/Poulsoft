<?php
//breeder_fetch_farm1_duplicates2.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$farm_code = $_GET['farm_code'];
$row_count = $_GET['row_count'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `breeder_farms` WHERE `farm_code` = '$farm_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count."@".$row_count;
?>
