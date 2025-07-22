<?php
//chicken_modify_generalsales10.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['invoice']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

//Purchase Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$bookinvoice = $_POST['bookinvoice'];
$vehiclecode = $_POST['vehiclecode'];
$drivercode = $_POST['drivercode'];
$out_balance = $_POST['out_balance'];

$icode = $jals = $birds = $tweight = $eweight = $nweight = $avg_wt = $price = $item_amt = $sector = $nof_bags = $kgs = $rate = $bpkg = array();
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['nof_bags'] as $bags){ $nof_bags[$i] = $bags; $i++; }
$i = 0; foreach($_POST['kgs'] as $kgss){ $kgs[$i] = $kgss; $i++; }
$i = 0; foreach($_POST['rate'] as $rts){ $rate[$i] = $rts; $i++; }
$i = 0; foreach($_POST['amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
$i = 0; foreach($_POST['bperkg'] as $bkgs){ $bpkg[$i] = $bkgs; $i++; }

$tcds_chk = $_POST['tcds_chk'];
$tcds_per = $_POST['tcds_per'];
$tcds_type1 = $_POST['tcds_type1'];
$tcds_type2 = $_POST['tcds_type2'];
$tcds_amt = $_POST['tcds_amt'];
$net_amt1 = $_POST['net_amt1'];
$transporter_code = $_POST['transporter_code'];
$freight_amt = $_POST['freight_amt'];
$roundoff_type1 = $_POST['roundoff_type1'];
$roundoff_type2 = $_POST['roundoff_type2'];
$roundoff_amt = $_POST['roundoff_amt'];
$net_amt2 = $_POST['net_amt2'];
$remarks = $_POST['remarks'];

$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "generalsales10";
$trlink = "chicken_display_generalsales10.php";

//Save Purchase
$dsize = sizeof($icode);
for($i = 0;$i < $dsize;$i++){
 //   if($jals[$i] == ""){ $jals[$i] = 0; }
   // if($birds[$i] == ""){ $birds[$i] = 0; }
   // if($tweight[$i] == ""){ $tweight[$i] = 0; }
  //  if($eweight[$i] == ""){ $eweight[$i] = 0; }
  //  if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($rate[$i] == ""){ $rate[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }
    if($tcds_per[$i] == ""){ $tcds_per[$i] = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($net_amt1[$i] == ""){ $net_amt1[$i] = 0; }
    if($freight_amt[$i] == ""){ $freight_amt[$i] = 0; }
    if($net_amt2[$i] == ""){ $net_amt2[$i] = 0; }
    if($roundoff_amt == ""){ $roundoff_amt = 0; }

    // $sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,bookinvoice,vendorcode,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,updatedemp,updated,client,trtype,trlink) 
    // VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$bookinvoice','$vcode','$jals[$i]','$tweight[$i]','$eweight[$i]','$icode[$i]','$birds[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff_amt','$net_amt2','$net_amt2',NULL,'$sector[$i]','$flag','$active','$authorization','$tdflag','$pdflag','$drivercode','$vehiclecode',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks','$aemp','$atime','$addedemp','$addedtime','$client','$trtype','$trlink')";
    // if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    $sql = "INSERT INTO `customer_sales` (date,incr,d,m,y,fy,invoice,bookinvoice,sup_code,itemcode,nof_bags,bag_kg,totalweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client,trtype,trlink) 
    VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$bookinvoice','$vcode','$icode[$i]','$nof_bags[$i]','$bpkg[$i]','$kgs[$i]','$rate[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff_amt','$net_amt2','$net_amt2',NULL,'$sector[$i]','$flag','$active','$authorization','$tdflag','$pdflag','$drivercode','$vehiclecode',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks','$addedemp','$addedtime','$client','$trtype','$trlink')";
  //  echo $sql;
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:chicken_display_generalsales10.php?ccid='.$ccid);

