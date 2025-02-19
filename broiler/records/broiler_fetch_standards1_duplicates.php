<?php
//broiler_fetch_standards1_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$breed_age = $_GET['breed_age'];
$row_count = $_GET['row_count'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `breeder_breed_standards` WHERE `breed_age` = '$breed_age'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count."@".$row_count;
?>
