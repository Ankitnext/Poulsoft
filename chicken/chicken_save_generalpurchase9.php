<?php
//chicken_save_generalpurchase9.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Purchase Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$supbrh_code = $_POST['supbrh_code'];
$bookinvoice = $_POST['bookinvoice'];
$vehiclecode = $_POST['vehiclecode'];
$drivercode = $_POST['drivercode'];
$out_balance = $_POST['out_balance'];

$icode = $jals = $birds = $tweight = $eweight = $nweight = $avg_wt = $price = $item_amt = $sector = array();
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['avg_wt'] as $avg_wts){ $avg_wt[$i] = $avg_wts; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }

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

$trtype = "generalpurchase9";
$trlink = "chicken_display_generalpurchase9.php";

//Generate Transaction No.
$incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"generalpurchase9","GPUI","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

//Save Purchase
$dsize = sizeof($icode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }
    if($tcds_per[$i] == ""){ $tcds_per[$i] = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($net_amt1[$i] == ""){ $net_amt1[$i] = 0; }
    if($freight_amt[$i] == ""){ $freight_amt[$i] = 0; }
    if($net_amt2[$i] == ""){ $net_amt2[$i] = 0; }
    if($roundoff_amt == ""){ $roundoff_amt = 0; }

    $sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,bookinvoice,vendorcode,supbrh_code,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client,trtype,trlink) 
    VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$bookinvoice','$vcode','$supbrh_code','$jals[$i]','$tweight[$i]','$eweight[$i]','$icode[$i]','$birds[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff_amt','$net_amt2','$net_amt2',NULL,'$sector[$i]','$flag','$active','$authorization','$tdflag','$pdflag','$drivercode','$vehiclecode',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks','$addedemp','$addedtime','$client','$trtype','$trlink')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_generalpurchase9.php?ccid='.$ccid);

