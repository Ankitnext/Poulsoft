<?php
//broiler_check_dashboardaccess_duplication.php $dbname = $_SESSION['dbase'];
include "newConfig.php";
if(!isset($_SESSION)){ session_start(); }
date_default_timezone_set("Asia/Kolkata");

$user_code = $_GET['user_code'];
$panel_name = explode("@",$_GET['panel_name']);
$row_count = $_GET['row_count'];
$id = $_GET['id'];
if($id != ""){ $id_filter = " AND `id` NOT IN ('$id')"; } else{ $id_filter = ""; }

$sql = "SELECT * FROM `master_dashboard_links` WHERE `user_code` = '$user_code'".$id_filter." AND `panel_name` = '$panel_name[1]' AND `dflag` = '0' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);

echo $ccount."@".$row_count;