<?php
//broiler_check_sectormapping_duplicate.php
session_start();
include "newConfig.php";

$sector_code = $_GET['sector_code'];
$branch_code = $_GET['branch_code'];
$r_cnt = $_GET['row_count'];
$id = $_GET['id']; $id_filter = ""; if($id != ""){ $id_filter = " AND `id` NOT IN ('$id')"; }

$count = 0;
$sql = "SELECT * FROM `broiler_secbrch_mapping` WHERE `dflag` = '0' AND `sector_code` = '$sector_code' AND `branch_code` = '$branch_code'".$id_filter;
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);

echo $r_cnt."@".$count;
?>