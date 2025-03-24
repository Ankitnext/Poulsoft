<?php
//breeder_check_unitmap1_duplicate.php
session_start();
include "newConfig.php";

$sector_code = $_GET['sector_code']; 
$unit_code = $_GET['unit_code'];
$r_cnt = $_GET['row_count'];
$id = $_GET['id']; $id_filter = ""; if($id != ""){ $id_filter = " AND `id` NOT IN ('$id')"; }

$count = 0;
echo $sql = "SELECT * FROM `broiler_secunit_mapping` WHERE `dflag` = '0' AND (`sector_code` = '$sector_code' || `unit_code` = '$unit_code')".$id_filter;
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);

echo $r_cnt."@".$count;
?>