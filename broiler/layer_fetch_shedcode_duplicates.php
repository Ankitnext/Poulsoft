<?php
//layer_fetch_shedcode_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$shed_code = $_GET['shed_code'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `layer_sheds` WHERE `shed_code` = '$shed_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
