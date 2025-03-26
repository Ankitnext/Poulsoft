<?php
//chicken_save_generalpurchase2.php
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

$trtype = "generalpurchase2";
$trlink = "chicken_display_generalpurchase2.php";

//Generate Transaction No.
$trip_incr = 0; $trip_prefix = $trip_trnum = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"genpur2_tripno","GTI","generate",$_SESSION['dbase']);
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
    $trno_dt1 = generate_transaction_details($date,"generalpurchase2","PTI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    $sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,link_trnum,bookinvoice,vendorcode,itemcode,jals,birds,totalweight,emptyweight,netweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client,trtype,trlink) 
    VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$trip_trnum','$bookinvoice[$i]','$vcode[$i]','$icode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt[$i]','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff[$i]','$finaltotal[$i]','$finaltotal[$i]',NULL,'$warehouse','$flag','$active','$authorization','$tdflag','$pdflag','$driver','$vehicle',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks[$i]','$addedemp','$addedtime','$client','$trtype','$trlink')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//Voucher Informationa
$billno = $_POST['billno'];
$group_code = $_POST['group_code'];
$fcoa = $_POST['fcoa'];
$fcoa_amt = $_POST['fcoa_amt'];
$from_kms = $_POST['from_kms'];
$to_kms = $_POST['to_kms'];
$total_kms = $_POST['total_kms'];
$from_location = $to_location = "";

$tcoa = $tcoa_amt = $remarks2 = array();
$i = 0; foreach($_POST['tcoa'] as $tcoas){ $tcoa[$i] = $tcoas; $i++; }
$i = 0; foreach($_POST['tcoa_amt'] as $tcoa_amts){ $tcoa_amt[$i] = $tcoa_amts; $i++; }
$i = 0; foreach($_POST['remarks2'] as $remarks2s){ $remarks2[$i] = $remarks2s; $i++; }

if($fcoa_amt == ""){ $fcoa_amt = 0; }
if($balance_amt == ""){ $balance_amt = 0; }
if((float)$fcoa_amt > 0 || (float)$balance_amt > 0){
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"pur_evou2","ETI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    $vtype = $etype ="Pay Voucher";
    $dsize = sizeof($tcoa);
    for($i = 0;$i < $dsize;$i++){
        if($tcoa_amt[$i] == ""){ $tcoa_amt[$i] = 0; }
        if($tcoa_amt[$i] > 0){
            $sql = "INSERT INTO `acc_vouchers` (`incr`,`prefix`,`trnum`,`link_trnum`,`vtype`,`date`,`dcno`,`group_code`,`advance_amt`,`fcoa`,`tcoa`,`amount`,`balance_amt`,`warehouse`,`remarks`,`from_location`,`to_location`,`from_kms`,`to_kms`,`total_kms`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES('$incr','$prefix','$trnum','$trip_trnum','$vtype','$date','$billno','$group_code','$fcoa_amt','$fcoa','$tcoa[$i]','$tcoa_amt[$i]','$balance_amt','$warehouse','$remarks2[$i]','$from_location','$to_location','$from_kms','$to_kms','$total_kms','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else{ }
        }
    }
}

//Driver Expense/Salary Information

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Labour Advance' AND `vouexp_flag` = '1' AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $labour_fcoa = "";
while($row = mysqli_fetch_assoc($query)){ $labour_fcoa = $row['code']; }

$emp_scode = $advance_amt = $sup_eamt = $remarks3 = array();
$i = 0; foreach($_POST['emp_scode'] as $emp_scodes){ $emp_scode[$i] = $emp_scodes; $i++; }
$i = 0; foreach($_POST['emps_amt'] as $advance_amts){ $advance_amt[$i] = $advance_amts; $i++; }
$i = 0; foreach($_POST['emps_eamt'] as $sup_eamts){ $sup_eamt[$i] = $sup_eamts; $i++; }
$i = 0; foreach($_POST['remarks3'] as $remarks3s){ $remarks3[$i] = $remarks3s; $i++; }
$tot_empsal_amt = $_POST['tot_empsal_amt'];

if((float)$tot_empsal_amt > 0){
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"pur_dvou2","DTI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    $vtype = $etype ="Pay Voucher";
    $dsize = sizeof($emp_scode);
    for($i = 0;$i < $dsize;$i++){
        if($advance_amt[$i] == ""){ $advance_amt[$i] = 0; }
        if($sup_eamt[$i] == ""){ $sup_eamt[$i] = 0; }
        if($advance_amt[$i] > 0){
            $sflag = $_POST['supr_flag'][$i];
            $supr_flag = 0; if($sflag == 1 || $sflag == "on" || $sflag == true){ $supr_flag = 1; }
            $sql = "INSERT INTO `acc_vouchers` (`incr`,`prefix`,`trnum`,`link_trnum`,`vtype`,`date`,`dcno`,`group_code`,`fcoa`,`tcoa`,`advance_amt`,`supr_flag`,`sup_eamt`,`amount`,`balance_amt`,`warehouse`,`remarks`,`from_location`,`to_location`,`from_kms`,`to_kms`,`total_kms`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES('$incr','$prefix','$trnum','$trip_trnum','$vtype','$date','$billno',NULL,'$labour_fcoa','$emp_scode[$i]','$advance_amt[$i]','$supr_flag','$sup_eamt[$i]','$advance_amt[$i]','0','$warehouse','$remarks3[$i]','$from_location','$to_location','$from_kms','$to_kms','$total_kms','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else{ }
        }
    }
}

header('location:chicken_display_generalpurchase2.php?ccid='.$ccid);

