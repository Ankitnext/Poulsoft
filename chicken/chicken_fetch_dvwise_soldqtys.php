<?php
//chicken_fetch_dvwise_soldqtys.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";
$date = date("Y-m-d", strtotime($_GET['date']));
$warehouse = $_GET['warehouse'];

$sql = "SELECT SUM(netweight) as quantity FROM `customer_sales` WHERE `date` = '$date' AND `warehouse` = '$warehouse' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `warehouse` ASC;";
$query = mysqli_query($conn,$sql); $quantity = 0;
while ($row = mysqli_fetch_assoc($query)) { $quantity += $row['quantity']; }

$sql = "SELECT SUM(netweight) as quantity FROM `pur_purchase` WHERE `date` = '$date' AND `warehouse` = '$warehouse' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `warehouse` ASC;";
$query = mysqli_query($conn,$sql); $quantity1 = 0;
while ($row = mysqli_fetch_assoc($query)) { $quantity1 += $row['quantity']; }

echo $quantity.'@'.$quantity1;

?>