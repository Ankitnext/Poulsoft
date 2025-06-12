<?php
//chicken_fetch_supplier_branch.php
if(!isset($_SESSION)){ session_start(); } include "newConfig.php";
$sup_dt1 = $_GET['scode'];
$sup_dt2 = explode("@",$sup_dt1);
$scode = $sup_dt2[0];
$r_cnt = $_GET['row_count'];

$brh_list = "";
$sql = "SELECT * FROM `chicken_supplier_branch` WHERE `sup_code` LIKE '$scode' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){
	$code = $row['code']; $name = $row['description'];
    if($brh_list == ""){ $brh_list = '{"code": "'.$code.'", "name": "'.$name.'"}'; } else{ $brh_list = $brh_list.',{"code": "'.$code.'", "name": "'.$name.'"}'; }
}
echo $count."[@$&]".$r_cnt."[@$&][".$brh_list."]";
?>