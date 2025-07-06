<?php
//chicken_modify_pursale7.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$warehouse = $_POST['warehouse'];
$itemcode = $_POST['itemcode'];

$vendorcode = $supbrh_code = $bookinvoice = $jals = $birds = $tweight = $eweight = $nweight = $sup_prc = $sup_amt = $tds_per = $tds_amt = $roff_samt = $sup_famt = 
$customercode = $cus_prc = $cus_amt = $tcs_per = $tcs_amt = $roff_camt = $cus_famt = $vehiclecode = $drivercode = $remarks = array();
$i = 0; foreach($_POST['vendorcode'] as $vendorcodes){ $vendorcode[$i] = $vendorcodes; $i++; }
$i = 0; foreach($_POST['supbrh_code'] as $supbrh_codes){ $supbrh_code[$i] = $supbrh_codes; $i++; }
$i = 0; foreach($_POST['bookinvoice'] as $bookinvoices){ $bookinvoice[$i] = $bookinvoices; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['sup_prc'] as $sup_prcs){ $sup_prc[$i] = $sup_prcs; $i++; }
$i = 0; foreach($_POST['sup_amt'] as $sup_amts){ $sup_amt[$i] = $sup_amts; $i++; }
$i = 0; foreach($_POST['tds_per'] as $tds_pers){ $tds_per[$i] = $tds_pers; $i++; }
$i = 0; foreach($_POST['tds_amt'] as $tds_amts){ $tds_amt[$i] = $tds_amts; $i++; }
$i = 0; foreach($_POST['roff_samt'] as $roff_samts){ $roff_samt[$i] = $roff_samts; $i++; }
$i = 0; foreach($_POST['sup_famt'] as $sup_famts){ $sup_famt[$i] = $sup_famts; $i++; }

$i = 0; foreach($_POST['customercode'] as $customercodes){ $customercode[$i] = $customercodes; $i++; }
$i = 0; foreach($_POST['cus_prc'] as $cus_prcs){ $cus_prc[$i] = $cus_prcs; $i++; }
$i = 0; foreach($_POST['cus_amt'] as $cus_amts){ $cus_amt[$i] = $cus_amts; $i++; }
$i = 0; foreach($_POST['tcs_per'] as $tcs_pers){ $tcs_per[$i] = $tcs_pers; $i++; }
$i = 0; foreach($_POST['tcs_amt'] as $tcs_amts){ $tcs_amt[$i] = $tcs_amts; $i++; }
$i = 0; foreach($_POST['roff_camt'] as $roff_camts){ $roff_camt[$i] = $roff_camts; $i++; }
$i = 0; foreach($_POST['cus_famt'] as $cus_famts){ $cus_famt[$i] = $cus_famts; $i++; }

// $i = 0; foreach($_POST['vehiclecode'] as $vehiclecodes){ $vehiclecode[$i] = $vehiclecodes; $i++; }
// $i = 0; foreach($_POST['drivercode'] as $drivercodes){ $drivercode[$i] = $drivercodes; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "pursale7";
$trlink = "chicken_display_pursale7.php";

//Save Purchase
$dsize = sizeof($vendorcode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($sup_prc[$i] == ""){ $sup_prc[$i] = 0; }
    if($sup_amt[$i] == ""){ $sup_amt[$i] = 0; }
    if($tds_per[$i] == ""){ $tds_per[$i] = 0; }
    if($tds_amt[$i] == ""){ $tds_amt[$i] = 0; }
    if($roff_samt[$i] == ""){ $roff_samt[$i] = 0; }
    if($sup_famt[$i] == ""){ $sup_famt[$i] = 0; }
    if($cus_prc[$i] == ""){ $cus_prc[$i] = 0; }
    if($cus_amt[$i] == ""){ $cus_amt[$i] = 0; }
    if($tcs_per[$i] == ""){ $tcs_per[$i] = 0; }
    if($tcs_amt[$i] == ""){ $tcs_amt[$i] = 0; }
    if($roff_camt[$i] == ""){ $roff_camt[$i] = 0; }
    if($cus_famt[$i] == ""){ $cus_famt[$i] = 0; }

    $pid = $_POST['pid']; $pincr = 0; $pprefix = $ptrnum = $pfyear = $paemp = $patime = "";
    $sql = "SELECT * FROM `pur_purchase` WHERE `id` = '$pid' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ if($ptrnum == ""){ $pincr = $row['incr']; $pprefix = $row['prefix']; $ptrnum = $row['invoice']; $lntrnum = $row['link_trnum']; $pfyear = $row['fy']; $paemp = $row['addedemp']; $patime = $row['addedtime']; } }
    
    $sid = $_POST['sid']; $sincr = 0; $sprefix = $strnum = $sfyear = $saemp = $satime = "";
    $sql = "SELECT * FROM `customer_sales` WHERE `id` = '$sid' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ if($strnum == ""){ $sincr = $row['incr']; $sprefix = $row['prefix']; $strnum = $row['invoice']; $sfyear = $row['fy']; $saemp = $row['addedemp']; $satime = $row['addedtime']; } }
    $satrnum = "S".$sfyear."-".$sincr;

    if($ptrnum != ""){
        include_once("poulsoft_store_chngmaster.php");
        $chng_type = "Edit";
        $edit_file = "chicken_modify_pursale7.php";
        $mtbl_name = "pur_purchase";
        $tno_cname = "invoice";
        $msg1 = array("file"=>$edit_file, "trnum"=>$ptrnum, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
        $message = json_encode($msg1);
        store_modified_details($message);

        $sql3 = "DELETE FROM `pur_purchase` WHERE `id` = '$pid' AND `tdflag` = '0' AND `pdflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
        
        $sql = "INSERT INTO `pur_purchase` (`date`,`incr`,`d`,`m`,`y`,`fy`,`invoice`,`bookinvoice`,`link_trnum`,`vendorcode`,`supbrh_code`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`tcdsper`,`tcdsamt`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
        VALUES ('$date','$pincr','$d','$m','$y','$pfyear','$ptrnum','$bookinvoice[$i]','$satrnum','$vendorcode[$i]','$supbrh_code[$i]','$itemcode','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$sup_prc[$i]','$sup_amt[$i]','$tds_per[$i]','$tds_amt[$i]','$roff_samt[$i]','$sup_famt[$i]','$sup_famt[$i]','$warehouse','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$paemp','$patime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    if($strnum != ""){
        include_once("poulsoft_store_chngmaster.php");
        $chng_type = "Edit";
        $edit_file = "chicken_modify_pursale7.php";
        $mtbl_name = "customer_sales";
        $tno_cname = "invoice";
        $msg1 = array("file"=>$edit_file, "trnum"=>$strnum, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
        $message = json_encode($msg1);
        store_modified_details($message);

        $sql3 = "DELETE FROM `customer_sales` WHERE `id` = '$sid' AND `tdflag` = '0' AND `pdflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
        
        $sql = "INSERT INTO `customer_sales` (`date`,`incr`,`d`,`m`,`y`,`fy`,`invoice`,`bookinvoice`,`link_trnum`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`tcdsper`,`tcdsamt`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
        VALUES ('$date','$sincr','$d','$m','$y','$sfyear','$satrnum','$bookinvoice[$i]','$ptrnum','$customercode[$i]','$itemcode','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$cus_prc[$i]','$cus_amt[$i]','$tcs_per[$i]','$tcs_amt[$i]','$roff_camt[$i]','$cus_famt[$i]','$cus_famt[$i]','$warehouse','$remarks[$i]','$flag','$active','$tdflag','$pdflag','PST','$trlink','$saemp','$satime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}
?>
<script>
    window.location.href = "chicken_display_pursale7.php";
</script>
<?php
exit;

