<?php
//chicken_save_mortality1.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Payment Information
$jalqty = $birdqty = $cqty = $pdate = $cpri = $code = $camt = $sector = $remarks = $oqty = array();
$i = 0; foreach($_POST['pdate'] as $pdates){ $pdate[$i] = date("Y-m-d", strtotime($pdates)); $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
$i = 0; foreach($_POST['code'] as $codes){ $code[$i] = $codes; $i++; }
$i = 0; foreach($_POST['oqty'] as $oqtys){ $oqty[$i] = $oqtys; $i++; }
$i = 0; foreach($_POST['jalqty'] as $jalqtys){ $jalqty[$i] = $jalqtys; $i++; }
$i = 0; foreach($_POST['birdqty'] as $birdqtys){ $birdqty[$i] = $birdqtys; $i++; }
$i = 0; foreach($_POST['cqty'] as $cqtys){ $cqty[$i] = $cqtys; $i++; }
$i = 0; foreach($_POST['cpri'] as $cpris){ $cpri[$i] = $cpris; $i++; }
$i = 0; foreach($_POST['camt'] as $camts){ $camt[$i] = $camts; $i++; } 
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "mortality1";
$trlink = "chicken_display_mortality1.php";


//Save Payments
$dsize = is_array($code) ? sizeof($code) : 0;
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
        
        $sql = "INSERT INTO `item_closingstock` (`incr`,`trnum`,`date`,`warehouse`,`code`,`existquantity`,`closedjals`,`closedbirds`,`closedquantity`,`price`,`amount`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('$incr','$trnum','$pdate[$i]','$sector[$i]','$code[$i]','$oqty[$i]','$jalqty[$i]','$birdqty[$i]','$cqty[$i]','$cpri[$i]','$camt[$i]','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}
header('location:chicken_display_mortality1.php?ccid='.$ccid);

