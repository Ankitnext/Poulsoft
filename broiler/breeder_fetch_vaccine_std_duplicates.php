<?php
//breeder_fetch_vaccine_std_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$age = $_GET['age'];
$medvac_code = $_GET['medvac_code'];
$row_count = $_GET['row_count'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `breeder_medvac_schedule` WHERE `age` = '$age' AND `medvac_code` = '$medvac_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count."@".$row_count;
?>
