<?php
//chicken_modify_supplierpayment2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Hide Dcno and Sector' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $hdcsec_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Generate debit note for TDS amount' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $tdsdnote_flag = mysqli_num_rows($query);

//Payment Information
$ids = $_POST['idvalue'];
$date = date("Y-m-d", strtotime($_POST['date']));
$ccode = $_POST['ccode'];
$mode = $_POST['mode'];
$code = $_POST['code'];
$amount1 = $_POST['amount1'];
$dcno = $_POST['dcno'];
$sector = $_POST['sector'];
$remarks = $_POST['remarks'];
$tcds_per = $_POST['tcds_per'];
$tcds_amt = $_POST['tcds_amt'];
$amount = $_POST['amount'];

$vtype = "S";
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "supplierpayment2";
$trlink = "chicken_display_supplierpayment2.php";

//Save Payments
if($amount1 == ""){ $amount1 = 0; }
if($tcds_per == ""){ $tcds_per = 0; }
if($tcds_amt == ""){ $tcds_amt = 0; }
if($amount == ""){ $amount = 0; }
if((float)$tcds_amt > 0){ } else{ $tcds_per = 0; }

if($hdcsec_flag == 1){
    $sql = "UPDATE `pur_payments` SET `date` = '$date',`ccode` = '$ccode',`mode` = '$mode',`method` = '$code',`amount1` = '$amount1',`tcds_per` = '$tcds_per',`tcds_amt` = '$tcds_amt',`amount` = '$amount',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`tdflag` = '$tdflag',`pdflag` = '$pdflag',`trtype` = '$trtype',`trlink` = '$trlink',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}else{
    $sql = "UPDATE `pur_payments` SET `date` = '$date',`ccode` = '$ccode',`docno` = '$dcno',`mode` = '$mode',`method` = '$code',`amount1` = '$amount1',`tcds_per` = '$tcds_per',`tcds_amt` = '$tcds_amt',`amount` = '$amount',`warehouse` = '$sector',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`tdflag` = '$tdflag',`pdflag` = '$pdflag',`trtype` = '$trtype',`trlink` = '$trlink',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

if($tdsdnote_flag > 0 && $tcds_amt > 0){
    // check if exist or not in crdrnote
    $sql = "SELECT * FROM `main_crdrnote` WHERE `link_trnum` = '$ids' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    if($count > 0){
        //exist
        $sql = "UPDATE `main_crdrnote` SET `date` = '$date',`docno` = '$dcno',`amount` = '$tcds_amt',`balance` = '$tcds_amt',`warehouse` = '$sector',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `link_trnum` = '$ids'";
        mysqli_query($conn,$sql);
    }else{
        //not exist
         $sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
            $incr = $crdrs = "";

            $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $incrvalue = $row['cusdebit']; } $incr = $incrvalue + 1;
            $sql = "UPDATE `master_generator` SET `cusdebit` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            $crdrs = "Cr";

            if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
            $code = "SDN"."-".$fprefix."".$incr;

            $sql = "SELECT * FROM main_tcds WHERE fdate <= '$date' AND tdate >= '$date' AND type LIKE 'TDS' AND active = 1 AND dflag = 0 ORDER BY fdate,id ASC";
            $query = mysqli_query($conn,$sql); $tcds_count = mysqli_num_rows($query);
            if($tcds_count > 0){ while($row = mysqli_fetch_assoc($query)){ $tcds_coa_code = $row['coa']; } } else{ $tcds_coa_code = ""; }

            $sql = "INSERT INTO `main_crdrnote` (mode,trnum,link_trnum,date,ccode,docno,coa,crdr,amount,balance,vtype,warehouse,flag,active,addedemp,addedtime,tdflag,pdflag)
            VALUES ('SDN','$code','$ids','$date','$ccode','$dcno','$tcds_coa_code','$crdrs','$tcds_amt','$tcds_amt','S','$sector','0','1','$addedemp','$addedtime','0','0')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}



header('location:chicken_display_supplierpayment2.php?ccid='.$ccid);

