<?php
//chicken_save_generalpurchase11.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Purchase Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$warehouse = $_POST['warehouse'];
$tcds_per = $_POST['tcds_per'];

$vcode = $bookinvoice = $icode = $jals = $birds = $tweight = $eweight = $nweight = $price = $item_amt = $tcds_chk = $tcds_amt = $rndoff_chk = $roundoff = $finaltotal = $remarks = array();
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['bookinvoice'] as $bookinvoices){ $bookinvoice[$i] = $bookinvoices; $i++; }
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['tcds_chk'] as $tcds_chks){ $tcds_chk[$i] = $tcds_chks; $i++; }
$i = 0; foreach($_POST['tcds_amt'] as $tcds_amts){ $tcds_amt[$i] = $tcds_amts; $i++; }
$i = 0; foreach($_POST['rndoff_chk'] as $rndoff_chks){ $rndoff_chk[$i] = $rndoff_chks; $i++; }
$i = 0; foreach($_POST['roundoff'] as $roundoffs){ $roundoff[$i] = $roundoffs; $i++; }
$i = 0; foreach($_POST['finaltotal'] as $finaltotals){ $finaltotal[$i] = $finaltotals; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$tcds_type1 = $roundoff_type1 = "auto";
$tcds_type2 = "add";
$transporter_code = $driver = $vehicle = "";
$freight_amt = 0;

$tot_jals = $_POST['tot_jals'];
$tot_birds = $_POST['tot_birds'];
$tot_tweight = $_POST['tot_tweight'];
$tot_eweight = $_POST['tot_eweight'];
$tot_nweight = $_POST['tot_nweight'];
$tot_item_amt = $_POST['tot_item_amt'];
$tot_tcds_amt = $_POST['tot_tcds_amt'];
$tot_finl_amt = $_POST['tot_finl_amt'];

$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "generalpurchase11";
$trlink = "chicken_display_generalpurchase11.php";

//Generate Transaction No.
$trip_incr = 0; $trip_prefix = $trip_trnum = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"generalpurchase11","GNP","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$trip_incr = $trno_dt2[0]; $trip_prefix = $trno_dt2[1]; $trip_trnum = $trno_dt2[2];

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
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($finaltotal[$i] == ""){ $finaltotal[$i] = 0; }
    if($roundoff[$i] == ""){ $roundoff[$i] = 0; }

    $net_amt1 = (float)$item_amt[$i] + (float)$tcds_amt[$i];
    if((float)$roundoff[$i] >= 0){ $roundoff_type2 = "add"; } else{ $roundoff_type2 = "deduct"; }

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"generalpurchase11","PTI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    $sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,link_trnum,bookinvoice,vendorcode,itemcode,jals,birds,totalweight,emptyweight,netweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client,trtype,trlink) 
    VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$trip_trnum','$bookinvoice[$i]','$vcode[$i]','$icode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt[$i]','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff[$i]','$finaltotal[$i]','$finaltotal[$i]',NULL,'$warehouse','$flag','$active','$authorization','$tdflag','$pdflag','$driver','$vehicle',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks[$i]','$addedemp','$addedtime','$client','$trtype','$trlink')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_generalpurchase11.php?ccid='.$ccid);

