<?php
//layer_fetch_batchcode_duplicates.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$batch_code = $_GET['batch_code'];
$count = 0;

$id_filter = ""; $type = $_GET['type']; $id = $_GET['id'];
if($type == "edit"){ $id_filter = " AND `id` NOT IN ('$id')"; }

$sql = "SELECT * FROM `layer_batch` WHERE `batch_code` = '$batch_code'".$id_filter." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
