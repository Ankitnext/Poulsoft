<?php
//chicken_modify_labourexp.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

$ids = $_POST['idvalue']; $incr = $prefix = $invoice = $aemp = $atime = "";
$sql = "SELECT * FROM `chicken_labveh_expenses` WHERE `code` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($invoice == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $invoice = $row['code']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($invoice != ""){
    $sql3 = "DELETE FROM `chicken_labveh_expenses` WHERE `code` = '$ids' AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

//Shop Investment Information
$warehouse = $_POST['warehouse'];
$sold_kgs = $_POST['sold_kgs']; if($sold_kgs == ""){ $sold_kgs = 0; }
$pur_kgs = $_POST['pur_kgs']; if($pur_kgs == ""){ $pur_kgs = 0; }
$no_labours = $_POST['no_labours'];  if($no_labours ==""){ $no_labours = 0; }
$date = date("Y-m-d", strtotime($_POST['date']));

$vcode = $amount = $itemcode = $remarks = $bonus = array();
// $i = 0; foreach($_POST['pdate'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['labour_code'] as $labour_codes){ $labour_code[$i] = $labour_codes; $i++; }
$i = 0; foreach($_POST['supr_amt'] as $supervisor_values){ $supervisor_value[$i] = $supervisor_values; $i++; }
$i = 0; foreach($_POST['sold_weight'] as $sold_weights){ $sold_weight[$i] = $sold_weights; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i] = $rates; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['bonus'] as $bonuss){ $bonus[$i] = $bonuss; $i++; }

$active = 1;
$dflag = $flag = 0;

$trtype = "labourexp";
$trlink = "chicken_display_labourexp.php";
// $ids = $_POST['idvalue'];

$dsize = sizeof($labour_code);
for($i = 0;$i < $dsize;$i++){
    if($supervisor_value[$i] == ""){ $supervisor_value[$i] = 0; }
    if($sold_weight[$i] == ""){ $sold_weight[$i] = 0; }
    if($rate[$i] == ""){ $rate[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($bonus[$i] == ""){ $bonus[$i] = 0; }
    
    //Modify Transaction
    $sql = "INSERT INTO `chicken_labveh_expenses` (incr,prefix,code,date,warehouse,sold_kgs,pur_kgs,no_labours,labour_code,supervisor_value,sold_weight,rate,amount,bonus,active,dflag,flag,trtype,trlink,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$invoice','$date','$warehouse','$sold_kgs','$pur_kgs','$no_labours','$labour_code[$i]','$supervisor_value[$i]','$sold_weight[$i]','$rate[$i]','$amount[$i]','$bonus[$i]','1','0','0','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_labourexp.php?ccid='.$ccid);
}
header('location:chicken_display_labourexp.php?ccid='.$ccid);
