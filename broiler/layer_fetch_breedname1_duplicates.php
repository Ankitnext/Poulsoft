<?php
//layer_fetch_breedname1_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$description = $_GET['description'];
$row_count = $_GET['row_count'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `layer_breed_details` WHERE `description` = '$description'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count."@".$row_count;
?>
