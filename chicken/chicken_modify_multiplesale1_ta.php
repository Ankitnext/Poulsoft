<?php
//chicken_save_multiplesale1_ta.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Fetch Account Modes
$sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }

//Receipt Cash Method
$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Cash In Hand'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cash_code = $row['code']; }

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$itemcode = $_POST['itemcode'];
$warehouse = $_POST['warehouse'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];
$tcds_per = $_POST['tcds_per'];

$old_trnum = $vcode = $bookinvoice = $jals = $birds = $tweight = $eweight = $nweight = $price = $item_amt = $tcds_amt = $rct_amt1 = $bank_method1 = $rct_amt2 = $roundoff = $finaltotal = $remarks = array();
$i = 0; foreach($_POST['trnums'] as $trnumss){ $old_trnum[$i] = $trnumss; $i++; }
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['bookinvoice'] as $bookinvoices){ $bookinvoice[$i] = $bookinvoices; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['tcds_amt'] as $tcds_amts){ $tcds_amt[$i] = $tcds_amts; $i++; }
$i = 0; foreach($_POST['rct_amt1'] as $rct_amt1s){ $rct_amt1[$i] = $rct_amt1s; $i++; }
$i = 0; foreach($_POST['bank_method1'] as $bank_method1s){ $bank_method1[$i] = $bank_method1s; $i++; }
$i = 0; foreach($_POST['rct_amt2'] as $rct_amt2s){ $rct_amt2[$i] = $rct_amt2s; $i++; }
$i = 0; foreach($_POST['roundoff'] as $roundoffs){ $roundoff[$i] = $roundoffs; $i++; }
$i = 0; foreach($_POST['finaltotal'] as $finaltotals){ $finaltotal[$i] = $finaltotals; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$tcds_type1 = $roundoff_type1 = "auto";
$tcds_type2 = "add";
$transporter_code = "";
$freight_amt = 0;

$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "multiplesale1_ta";
$trlink = "chicken_display_multiplesale1_ta.php";
$sale_type = "";

$dsize = sizeof($vcode);
for($i = 0;$i < $dsize;$i++){
    //Save Sale Transaction
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($rct_amt1[$i] == ""){ $rct_amt1[$i] = 0; }
    if($rct_amt2[$i] == ""){ $rct_amt2[$i] = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($finaltotal[$i] == ""){ $finaltotal[$i] = 0; }
    if($roundoff[$i] == ""){ $roundoff[$i] = 0; }

    $net_amt1 = (float)$item_amt[$i] + (float)$tcds_amt[$i];
    if((float)$roundoff[$i] >= 0){ $roundoff_type2 = "add"; } else{ $roundoff_type2 = "deduct"; }

    if($old_trnum[$i] != ""){
        $ids = $old_trnum[$i]; $incr = 0; $prefix = $invoice = $aemp = $atime = $fy = "";
        $sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            if($invoice == ""){ $fy = $row['fy']; $incr = $row['incr']; $prefix = $row['prefix']; $invoice = $row['invoice']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
        }
        if($invoice != ""){

            include_once("poulsoft_store_chngmaster.php");
            $chng_type = "Edit";
            $edit_file = "chicken_save_multiplesale1_ta.php";
            $mtbl_name = "customer_sales";
            $tno_cname = "invoice";
            $msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
            $message = json_encode($msg1);
            store_modified_details($message);

            $sql3 = "DELETE FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
            if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
        }
    }
    else{
        //Generate Transaction No.
        $incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date,"multiplesale1_ta","MSI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];
    }

    $sql = "INSERT INTO `customer_sales` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`bookinvoice`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`freight_amt`,`tcdsper`,`tcds_type1`,`tcds_type2`,`tcdsamt`,`roundoff_type1`,`roundoff_type2`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`drivercode`,`vehiclecode`,`flag`,`active`,`authorization`,`tdflag`,`pdflag`,`trtype`,`trlink`,`sale_type`,`addedemp`,`addedtime`,`updated`,`client`) 
    VALUES ('$incr','$d','$m','$y','$fy','$date','$invoice','$bookinvoice[$i]','$vcode[$i]','$itemcode','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$freight_amt','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt[$i]','$roundoff_type1','$roundoff_type2','$roundoff[$i]','$finaltotal[$i]','$finaltotal[$i]','$warehouse','$remarks[$i]','$driver','$vehicle','$flag','$active','$authorization','$tdflag','$pdflag','$trtype','$trlink','$sale_type','$addedemp','$addedtime','$addedtime','$client')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    //Save Receipt-1 Transaction
    if((float)$rct_amt1[$i] > 0){
        if($old_trnum[$i] != ""){
            $ids = $old_trnum[$i]; $incr = 0; $prefix = $trnum = $aemp = $atime = "";
            $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$cash_mode' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
            }
            if($trnum != ""){

                $chng_type = "Edit";
                $edit_file = "chicken_save_multiplesale1_ta.php";
                $mtbl_name = "customer_receipts";
                $tno_cname = "link_trnum";
                $msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
                $message = json_encode($msg1);
                store_modified_details($message);

                $sql3 = "DELETE FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$cash_mode' AND `tdflag` = '0' AND `pdflag` = '0'";
                if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
            }
            else{
                $sql3 = "DELETE FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$cash_mode' AND `tdflag` = '0' AND `pdflag` = '0'";
                if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
                //Generate Transaction No.
                $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
                $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
                $trno_dt2 = explode("@",$trno_dt1);
                $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
            }
        }
        else{
            //Generate Transaction No.
            $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
            $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
            $trno_dt2 = explode("@",$trno_dt1);
            $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
        }

        $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,cdate,cno,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,updatedemp,updatedtime,tdflag,pdflag,client)
        VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode[$i]','$bookinvoice[$i]','$cash_mode','$cash_code',NULL,NULL,'$rct_amt1[$i]','C','$warehouse','$remarks[$i]','$flag','$active','$aemp','$atime','$addedemp','$addedtime','$tdflag','$pdflag','$client')";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
        $date_sms = date("d.m.Y",strtotime($date));    
    }
    //Save Receipt-2 Transaction
    if((float)$rct_amt2[$i] > 0){
        if($old_trnum[$i] != ""){
            $ids = $old_trnum[$i]; $incr = 0; $prefix = $trnum = $aemp = $atime = "";
            $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$bank_mode' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
            }
            if($trnum != ""){
                $sql3 = "DELETE FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$bank_mode' AND `tdflag` = '0' AND `pdflag` = '0'";
                if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
            }
            else{
                $sql3 = "DELETE FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$bank_mode' AND `tdflag` = '0' AND `pdflag` = '0'";
                if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
                //Generate Transaction No.
                $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
                $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
                $trno_dt2 = explode("@",$trno_dt1);
                $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
            }
        }
        else{
            //Generate Transaction No.
            $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
            $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
            $trno_dt2 = explode("@",$trno_dt1);
            $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
        }

        $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,cdate,cno,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,updatedemp,updatedtime,tdflag,pdflag,client)
        VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode[$i]','$bookinvoice[$i]','$bank_mode','$bank_method1[$i]',NULL,NULL,'$rct_amt2[$i]','C','$warehouse','$remarks[$i]','$flag','$active','$aemp','$atime','$addedemp','$addedtime','$tdflag','$pdflag','$client')";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
        $date_sms = date("d.m.Y",strtotime($date));    
    }
}

header('location:chicken_display_multiplesale1_ta.php?ccid='.$ccid);

