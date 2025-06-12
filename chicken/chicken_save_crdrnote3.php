<?php
//chicken_save_crdrnote3.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Payment Information
$vtype = $cdtype = $ccode = $date = $dcno = $code = $amount = $reason_code = $sector = $remarks = array();
$i = 0; foreach($_POST['vtype'] as $vtypes){ $vtype[$i] = $vtypes; $i++; }
$i = 0; foreach($_POST['cdtype'] as $cdtypes){ $cdtype[$i] = $cdtypes; $i++; }
$i = 0; foreach($_POST['ccode'] as $ccodes){ $ccode[$i] = $ccodes; $i++; }
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
$i = 0; foreach($_POST['code'] as $codes){ $code[$i] = $codes; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['reason_code'] as $reason_codes){ $reason_code[$i] = $reason_codes; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "crdrnote3";
$trlink = "chicken_display_crdrnote3.php";

//Save Payments
$dsize = sizeof($ccode);
for($i = 0;$i < $dsize;$i++){
    $mode = $vtype[$i]."".$cdtype[$i]; $trname = "";
    if($mode == "CCN"){ $crdr = "Dr"; $trname = "cuscredit"; }
    else if($mode == "CDN"){ $crdr = "Cr"; $trname = "cusdebit"; }
    else if($mode == "SCN"){ $crdr = "Dr"; $trname = "vencredit"; }
    else if($mode == "SDN"){ $crdr = "Cr"; $trname = "vendebit"; }
    else{ }
    if($trname != ""){
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date[$i],$trname,$mode,"generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

        if($amount[$i] == ""){ $amount[$i] = 0; }
        
        $sql = "INSERT INTO `main_crdrnote` (`incr`,`mode`,`trnum`,`date`,`ccode`,`docno`,`coa`,`crdr`,`amount`,`reason_code`,`balance`,`vtype`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('$incr','$mode','$trnum','$date[$i]','$ccode[$i]','$dcno[$i]','$code[$i]','$crdr','$amount[$i]','$reason_code[$i]','$amount[$i]','$vtype[$i]','$sector[$i]','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}
header('location:chicken_display_crdrnote3.php?ccid='.$ccid);

