<?php
//chicken_save_pursale4.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$warehouse = $_POST['warehouse'];
$vendorcode = $_POST['vendorcode'];
$supbrh_code = $_POST['supbrh_code'];
$vehiclecode = $_POST['vehiclecode'];

$sup_icode = $sup_jals = $sup_birds = $sup_tweight = $sup_eweight = $sup_nweight = $sup_prc = $sup_amt = array();
$i = 0; foreach($_POST['sup_icode'] as $sup_icodes){ $sup_icode[$i] = $sup_icodes; $i++; }
$i = 0; foreach($_POST['sup_jals'] as $sup_jalss){ $sup_jals[$i] = $sup_jalss; $i++; }
$i = 0; foreach($_POST['sup_birds'] as $sup_birdss){ $sup_birds[$i] = $sup_birdss; $i++; }
$i = 0; foreach($_POST['sup_tweight'] as $sup_tweights){ $sup_tweight[$i] = $sup_tweights; $i++; }
$i = 0; foreach($_POST['sup_eweight'] as $sup_eweights){ $sup_eweight[$i] = $sup_eweights; $i++; }
$i = 0; foreach($_POST['sup_nweight'] as $sup_nweights){ $sup_nweight[$i] = $sup_nweights; $i++; }
$i = 0; foreach($_POST['sup_prc'] as $sup_prcs){ $sup_prc[$i] = $sup_prcs; $i++; }
$i = 0; foreach($_POST['sup_amt'] as $sup_amts){ $sup_amt[$i] = $sup_amts; $i++; }

$sup_tnwt = $_POST['sup_tnwt']; if($sup_tnwt == ""){ $sup_tnwt = 0; }
$net_samt1 = $_POST['net_samt1']; if($net_samt1 == ""){ $net_samt1 = 0; }
$tds_per = $_POST['tds_per']; if($tds_per == ""){ $tds_per = 0; }
$tds_amt = $_POST['tds_amt']; if($tds_amt == ""){ $tds_amt = 0; }
$roff_samt = $_POST['roff_samt']; if($roff_samt == ""){ $roff_samt = 0; }
$sup_famt = $_POST['sup_famt']; if($sup_famt == ""){ $sup_famt = 0; }
$line_sexp = $_POST['line_sexp']; if($line_sexp == ""){ $line_sexp = 0; }

$sdate = date("Y-m-d", strtotime($_POST['sdate']));
$sd = date("d",strtotime($sdate));
$sm = date("m",strtotime($sdate));
$sy = date("Y",strtotime($sdate));

$cus_ccode = $cus_icode = $cus_jals = $cus_birds = $cus_tweight = $cus_eweight = $cus_nweight = $cus_prc = $cus_amt = $tcs_per = $tcs_amt = $roff_camt = $cus_famt = array();
$i = 0; foreach($_POST['cus_ccode'] as $cus_ccodes){ $cus_ccode[$i] = $cus_ccodes; $i++; }
$i = 0; foreach($_POST['cus_icode'] as $cus_icodes){ $cus_icode[$i] = $cus_icodes; $i++; }
$i = 0; foreach($_POST['cus_jals'] as $cus_jalss){ $cus_jals[$i] = $cus_jalss; $i++; }
$i = 0; foreach($_POST['cus_birds'] as $cus_birdss){ $cus_birds[$i] = $cus_birdss; $i++; }
$i = 0; foreach($_POST['cus_tweight'] as $cus_tweights){ $cus_tweight[$i] = $cus_tweights; $i++; }
$i = 0; foreach($_POST['cus_eweight'] as $cus_eweights){ $cus_eweight[$i] = $cus_eweights; $i++; }
$i = 0; foreach($_POST['cus_nweight'] as $cus_nweights){ $cus_nweight[$i] = $cus_nweights; $i++; }
$i = 0; foreach($_POST['cus_prc'] as $cus_prcs){ $cus_prc[$i] = $cus_prcs; $i++; }
$i = 0; foreach($_POST['cus_amt'] as $cus_amts){ $cus_amt[$i] = $cus_amts; $i++; }
$i = 0; foreach($_POST['tcs_per'] as $tcs_pers){ $tcs_per[$i] = $tcs_pers; $i++; }
$i = 0; foreach($_POST['tcs_amt'] as $tcs_amts){ $tcs_amt[$i] = $tcs_amts; $i++; }
$i = 0; foreach($_POST['roff_camt'] as $roff_camts){ $roff_camt[$i] = $roff_camts; $i++; }
$i = 0; foreach($_POST['cus_famt'] as $cus_famts){ $cus_famt[$i] = $cus_famts; $i++; }

$remarks = $_POST['remarks'];

$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "pursale4";
$trlink = "chicken_display_pursale4.php";
$pid = $_POST['pid'];

//$fetch all sales from Purchase Invoice
$sincr = $sprefix = $strnum = $sfyear = array(); $saemp = $satime = "";
$sql = "SELECT * FROM `customer_sales` WHERE `link_trnum` = '$pid' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if(empty($strnum[$row['invoice']]) || $strnum[$row['invoice']] == ""){
        $sincr[$row['invoice']] = $row['incr'];
        $sprefix[$row['invoice']] = $row['prefix'];
        $strnum[$row['invoice']] = $row['invoice'];
        $sfyear[$row['invoice']] = $row['fy'];
        $strno = $row['invoice'];
        include_once("poulsoft_store_chngmaster.php");
        $chng_type = "Edit";
        $edit_file = "chicken_modify_pursale4.php";
        $mtbl_name = "customer_sales";
        $tno_cname = "invoice";
        $msg1 = array("file"=>$edit_file, "trnum"=>$strno, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
        $message = json_encode($msg1);
        store_modified_details($message);
        $sql3 = "DELETE FROM `customer_sales` WHERE `invoice` = '$strno' AND `tdflag` = '0' AND `pdflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    }
    $saemp = $row['addedemp'];
    $satime = $row['addedtime'];
}

//Save Sale Details
$strno_list = "";
$dsize = sizeof($cus_ccode);
for($i = 0;$i < $dsize;$i++){
    if($cus_jals[$i] == ""){ $cus_jals[$i] = 0; }
    if($cus_birds[$i] == ""){ $cus_birds[$i] = 0; }
    if($cus_tweight[$i] == ""){ $cus_tweight[$i] = 0; }
    if($cus_eweight[$i] == ""){ $cus_eweight[$i] = 0; }
    if($cus_nweight[$i] == ""){ $cus_nweight[$i] = 0; }
    if($cus_prc[$i] == ""){ $cus_prc[$i] = 0; }
    if($cus_amt[$i] == ""){ $cus_amt[$i] = 0; }
    if($tcs_per[$i] == ""){ $tcs_per[$i] = 0; }
    if($tcs_amt[$i] == ""){ $tcs_amt[$i] = 0; }
    if($roff_camt[$i] == ""){ $roff_camt[$i] = 0; }
    if($cus_famt[$i] == ""){ $cus_famt[$i] = 0; }

    $sid = $_POST['sid'][$i];
    if(empty($strnum[$sid]) || $strnum[$sid] == ""){
        $incr = 0; $prefix = $trnum = $fyear = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($sdate,"psi_s4","PSCI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
    }
    else{
        $incr = $sincr[$sid]; $prefix = $sprefix[$sid]; $trnum = $strnum[$sid]; $fyear = $sfyear[$sid];
    }
    
    if($strno_list == ""){ $strno_list = $trnum; } else{ $strno_list = $strno_list.",".$trnum; }
    $sql = "INSERT INTO `customer_sales` (`date`,`incr`,`d`,`m`,`y`,`fy`,`invoice`,`link_trnum`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`tcdsper`,`tcdsamt`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`vehiclecode`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
    VALUES ('$sdate','$incr','$sd','$sm','$sy','$fyear','$trnum','$pid','$cus_ccode[$i]','$cus_icode[$i]','$cus_jals[$i]','$cus_birds[$i]','$cus_tweight[$i]','$cus_eweight[$i]','$cus_nweight[$i]','$cus_prc[$i]','$cus_amt[$i]','$tcs_per[$i]','$tcs_amt[$i]','$roff_camt[$i]','$cus_famt[$i]','$cus_famt[$i]','$warehouse','$vehiclecode','$remarks','$flag','$active','$tdflag','$pdflag','PST','$trlink','$saemp','$satime','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
//Save Purchase
$pincr = 0; $pprefix = $ptrnum = $pfyear = $paemp = $patime = "";
$sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$pid' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($ptrnum == ""){ $pincr = $row['incr']; $pprefix = $row['prefix']; $ptrnum = $row['invoice']; $pfyear = $row['fy']; $paemp = $row['addedemp']; $patime = $row['addedtime']; } }
if($ptrnum != ""){
    include_once("poulsoft_store_chngmaster.php");
    $chng_type = "Edit";
    $edit_file = "chicken_modify_pursale4.php";
    $mtbl_name = "pur_purchase";
    $tno_cname = "invoice";
    $msg1 = array("file"=>$edit_file, "trnum"=>$ptrnum, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
    $message = json_encode($msg1);
    store_modified_details($message);

    $sql3 = "DELETE FROM `pur_purchase` WHERE `invoice` = '$pid' AND `tdflag` = '0' AND `pdflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    
    $dsize = sizeof($sup_icode);
    for($i = 0;$i < $dsize;$i++){
        if($sup_jals[$i] == ""){ $sup_jals[$i] = 0; }
        if($sup_birds[$i] == ""){ $sup_birds[$i] = 0; }
        if($sup_tweight[$i] == ""){ $sup_tweight[$i] = 0; }
        if($sup_eweight[$i] == ""){ $sup_eweight[$i] = 0; }
        if($sup_nweight[$i] == ""){ $sup_nweight[$i] = 0; }
        if($sup_prc[$i] == ""){ $sup_prc[$i] = 0; }
        if($sup_amt[$i] == ""){ $sup_amt[$i] = 0; }

        $sql = "INSERT INTO `pur_purchase` (`date`,`incr`,`d`,`m`,`y`,`fy`,`invoice`,`link_trnum`,`vendorcode`,`supbrh_code`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`tcdsper`,`tcdsamt`,`roundoff`,`finaltotal`,`balance`,`line_sexp`,`warehouse`,`vehiclecode`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
        VALUES ('$date','$pincr','$d','$m','$y','$pfyear','$ptrnum','$strno_list','$vendorcode','$supbrh_code','$sup_icode[$i]','$sup_jals[$i]','$sup_birds[$i]','$sup_tweight[$i]','$sup_eweight[$i]','$sup_nweight[$i]','$sup_prc[$i]','$sup_amt[$i]','$tds_per','$tds_amt','$roff_samt','$sup_famt','$sup_famt','$line_sexp','$warehouse','$vehiclecode','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$paemp','$patime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}
?>
<script>
    window.location.href = "chicken_display_pursale4.php";
</script>
<?php
exit;