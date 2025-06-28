<?php
//chicken_fetch_sale_asectors.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";
$date = date("Y-m-d", strtotime($_GET['date']));

$sql = "SELECT * FROM `customer_sales` WHERE `date` = '$date' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `warehouse` ASC;";
$query = mysqli_query($conn,$sql); $sector_alist = array();
while ($row = mysqli_fetch_assoc($query)) { $sector_alist[$row['warehouse']] = $row['warehouse']; }

$sql = "SELECT * FROM `pur_purchase` WHERE `date` = '$date' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `warehouse` ASC;";
$query = mysqli_query($conn,$sql);
while ($row = mysqli_fetch_assoc($query)) { $sector_alist[$row['warehouse']] = $row['warehouse']; }

//Sector Details
$sec_opt = '<option value="select">-select-</option>'; $sector_list = implode("','",$sector_alist);
$sql = "SELECT * FROM `inv_sectors` WHERE `code` IN ('$sector_list') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; $name = $row['description']; $sec_opt .= '<option value="'.$code.'">'.$name.'</option>'; }

echo $sec_opt;

?>