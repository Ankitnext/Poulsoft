<?php
//chicken_save_supplierpayment2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

// $sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
// while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
// if(in_array("sup_pay1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `sup_pay1` INT(100) NOT NULL DEFAULT '0' COMMENT 'Purchase with Voucher' AFTER `tdate`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Hide Dcno and Sector' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $hdcsec_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Generate debit note for TDS amount' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $tdsdnote_flag = mysqli_num_rows($query);

//Payment Information
$date = $ccode = $mode = $code = $amount1 = $dcno = $sector = $remarks = $tcds_per = $tcds_amt = $amount = array();
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['ccode'] as $ccodes){ $ccode[$i] = $ccodes; $i++; }
$i = 0; foreach($_POST['mode'] as $modes){ $mode[$i] = $modes; $i++; }
$i = 0; foreach($_POST['code'] as $codes){ $code[$i] = $codes; $i++; }
$i = 0; foreach($_POST['amount1'] as $amount1s){ $amount1[$i] = $amount1s; $i++; }
if($hdcsec_flag != 1){
    $i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
    $i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
}
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$i = 0; foreach($_POST['tcds_per'] as $tcds_pers){ $tcds_per[$i] = $tcds_pers; $i++; }
$i = 0; foreach($_POST['tcds_amt'] as $tcds_amts){ $tcds_amt[$i] = $tcds_amts; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$vtype = "S";
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "supplierpayment2";
$trlink = "chicken_display_supplierpayment2.php";

//Save Payments
$dsize = sizeof($ccode);
for($i = 0;$i < $dsize;$i++){

    if($amount1[$i] == ""){ $amount1[$i] = 0; }
    if($tcds_per[$i] == ""){ $tcds_per[$i] = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if((float)$tcds_amt[$i] > 0){ } else{ $tcds_per[$i] = 0; }

    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date[$i],"sup_pay1","PMT","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    $sql = "INSERT INTO `pur_payments` (`incr`,`prefix`,`trnum`,`date`,`ccode`,`docno`,`mode`,`method`,`amount1`,`tcds_per`,`tcds_amt`,`amount`,`vtype`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$date[$i]','$ccode[$i]','$dcno[$i]','$mode[$i]','$code[$i]','$amount1[$i]','$tcds_per[$i]','$tcds_amt[$i]','$amount[$i]','$vtype','$sector[$i]','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { 
        if($tdsdnote_flag > 0 && $tcds_amt[$i] > 0){
            $sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$date[$i]' AND `tdate` >= '$date[$i]' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
            $incr = $crdrs = "";

            $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $incrvalue = $row['cusdebit']; } $incr = $incrvalue + 1;
            $sql = "UPDATE `master_generator` SET `cusdebit` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            $crdrs = "Cr";

            if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
            $code = "SDN"."-".$fprefix."".$incr;

            $sql = "SELECT * FROM main_tcds WHERE fdate <= '$date[$i]' AND tdate >= '$date[$i]' AND type LIKE 'TDS' AND active = 1 AND dflag = 0 ORDER BY fdate,id ASC";
            $query = mysqli_query($conn,$sql); $tcds_count = mysqli_num_rows($query);
            if($tcds_count > 0){ while($row = mysqli_fetch_assoc($query)){ $tcds_coa_code = $row['coa']; } } else{ $tcds_coa_code = ""; }

            $sql = "INSERT INTO `main_crdrnote` (mode,trnum,link_trnum,date,ccode,docno,coa,crdr,amount,balance,vtype,warehouse,flag,active,addedemp,addedtime,tdflag,pdflag)
            VALUES ('SDN','$code','$trnum','$date[$i]','$ccode[$i]','$dcno[$i]','$tcds_coa_code','$crdrs','$tcds_amt[$i]','$tcds_amt[$i]','S','$sector[$i]','0','1','$addedemp','$addedtime','0','0')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
   
}
header('location:chicken_display_supplierpayment2.php?ccid='.$ccid);

