<?php
//broiler_fetch_salaryparam1_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$sector = $_GET['sector'];
$desg = $_GET['desg'];
$row_count = $_GET['row_count']; 
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `salary_structures` WHERE `sector_code` = '$sector' AND `desig_code` = '$desg'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count."@".$row_count;
?>
