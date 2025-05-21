<?php
//broiler_save_feedformula.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedformula'];


$sql ="SELECT MAX(incr) as incr FROM `broiler_feed_formula`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "BFF";
if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;

$date = date("Y-m-d",strtotime($_POST['date']));
$mill_code = $_POST['mill_code'];
$formula_item_code = $_POST['formula_item_code'];
$description = $_POST['description'];
$total_qty = $_POST['total_qty'];

$i = 0; foreach($_POST['item_code'] as $item_codes){ $item_code[$i] = $item_codes; $i++; }
$i = 0; foreach($_POST['unit_code'] as $unit_codes){ $unit_code[$i] = $unit_codes; $i++; }
$i = 0; foreach($_POST['item_qty'] as $item_qtys){ $item_qty[$i] = $item_qtys; $i++; }

$ssize = sizeof($item_code);
for($i = 0;$i < $ssize;$i++){
    $sql = "INSERT INTO `broiler_feed_formula` (incr,prefix,code,description,formula_item_code,date,mill_code,item_code,unit_code,item_qty,total_qty,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$description','$formula_item_code','$date','$mill_code','$item_code[$i]','$unit_code[$i]','$item_qty[$i]','$total_qty','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:broiler_display_feedformula.php?ccid='.$ccid);
?>