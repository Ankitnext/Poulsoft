<?php
//broiler_fetch_feedmill_expenses.php
session_start(); $dbname = $_SESSION['dbase'];
date_default_timezone_set("Asia/Kolkata");
$apcn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$dbname) or die('No apcnection');

$date = date('Y-m-d', strtotime($_GET['date'])); 
$feed_mill = $_GET['feed_mill'];
$feed_code = $_GET['feed_code'];
$formula_code = $_GET['formula_code'];
$total_tons = $_GET['total_tons'];

$total_qty = $labour_charge = $packing_charge = $transport_charge = $electric_charge = $other_charge = $other_charge2 = $exp_type = 0;

$sql = "SELECT * FROM `broiler_feed_formula` WHERE `code` = '$formula_code' AND `active` = '1' AND `dflag` = '0' GROUP BY `code` ORDER BY `description` ASC"; $query = mysqli_query($apcn,$sql);
while($row = mysqli_fetch_assoc($query)){ $total_qty = $row['total_qty']; }

$sql = "SELECT * FROM `broiler_feed_expense` WHERE `id` IN (SELECt MAX(id) as id FROM `broiler_feed_expense` WHERE `mill_code` = '$feed_mill' AND `feed_type` = '$feed_code' AND `active` = '1' AND `dflag` = '0')";
$query = mysqli_query($apcn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $labour_charge = $row['labour_charge'];
    $packing_charge = $row['packing_charge'];
    $transport_charge = $row['transport_charge'];
    $electric_charge = $row['electric_charge'];
    $other_charge = $row['other_charge'];
    $other_charge2 = $row['other_charge2'];
    $exp_type = $row['exp_type'];
}

echo $total_qty."@".$labour_charge."@".$packing_charge."@".$transport_charge."@".$electric_charge."@".$other_charge."@".$other_charge2."@".$exp_type;