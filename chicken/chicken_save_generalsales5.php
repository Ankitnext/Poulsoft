<?php
//chicken_save_generalsales5.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("generalsales5", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `generalsales5` INT(100) NOT NULL DEFAULT '0' COMMENT 'Purchase with Voucher' AFTER `tdate`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'generalsales5' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
if($count > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES ('column:flag', 'generalsales5', 'GSI-', '0', '1:1', '1', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1');"; mysqli_query($conn,$sql); }

//Sale Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$bookinvoice = $_POST['bookinvoice'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];

$itemcode = $jals = $birds = $tweight = $eweight = $nweight = $price = $amount = $warehouse = array();
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }

$tcds_chk = $_POST['tcds_chk'];
$tcds_per = $_POST['tcds_per'];
$tcds_type1 = $_POST['tcds_type1'];
$tcds_type2 = $_POST['tcds_type2'];
$tcds_amt = $_POST['tcds_amt'];
$transporter_code = $_POST['transporter_code'];
$freight_amt = $_POST['freight_amt'];
$dressing_charge = $_POST['dressing_charge'];
$roundoff_type1 = $_POST['roundoff_type1'];
$roundoff_type2 = $_POST['roundoff_type2'];
$roundoff_amt = $_POST['roundoff_amt'];
$finaltotal = $_POST['finaltotal'];
$remarks = $_POST['remarks'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "generalsales5";
$trlink = "chicken_display_generalsales5.php";

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }

//Generate Invoice transaction number format
$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $generalsales5 = $row['generalsales5']; } $incr = $generalsales5 + 1;

$sql = "UPDATE `master_generator` SET `generalsales5` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'generalsales5' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
$a = 1; $tr_code = $prefix;
for($j = 0;$j <= 16;$j++){
    if(!empty($inv_format[$j.":".$a])){
        if($inv_format[$j.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
        else if($inv_format[$j.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
        else if($inv_format[$j.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
        else if($inv_format[$j.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
        else if($inv_format[$j.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
        else if($inv_format[$j.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
        else if($inv_format[$j.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
        else if($inv_format[$j.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
        else if($inv_format[$j.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
        else if($inv_format[$j.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
        else if($inv_format[$j.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
        else if($inv_format[$j.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
        else if($inv_format[$j.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
        else if($inv_format[$j.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
        else if($inv_format[$j.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
        else if($inv_format[$j.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
        else{ }
    }
}
if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

//Save Purchase
$dsize = sizeof($itemcode); $item_dlt = "";
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt == ""){ $tcds_amt = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($dressing_charge == ""){ $dressing_charge = 0; }
    if($roundoff_amt == ""){ $roundoff_amt = 0; }
    if($finaltotal == ""){ $finaltotal = 0; }

    if($item_dlt == ""){
        if($birds[$i] != ""){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_name[$itemcode[$i]].": ".$item_birds."".$nweight[$i]."Kgs @ ".$price[$i];
    }
    else{
        if($birds[$i] != ""){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_dlt.", ".$item_name[$itemcode[$i]].": ".$item_birds."".$nweight[$i]."Kgs @ ".$price[$i];
    }

    $sql = "INSERT INTO `customer_sales` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`bookinvoice`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`transporter_code`,`freight_amount`,`tcdsper`,`tcds_type1`,`tcds_type2`,`tcdsamt`,`dressing_charge`,`roundoff_type1`,`roundoff_type2`,`roundoff`,`finaltotal`,`balance`,`drivercode`,`vehiclecode`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updated`) 
    VALUES ('$incr','$d','$m','$y','$pfx','$date','$trnum','$bookinvoice','$vcode','$itemcode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$amount[$i]','$transporter_code','$freight_amt','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$dressing_charge','$roundoff_type1','$roundoff_type2','$roundoff_amt','$finaltotal','$finaltotal','$driver','$vehicle','$warehouse[$i]','$remarks','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//SMS and WAPP Sending
$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql);
$ccount = mysqli_num_rows($query); $sales_sms_flag = $sales_wapp_flag = $sales_notify_flag = 0;
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $sales_sms_flag = $row['sales_sms']; $sales_wapp_flag = $row['sales_wapp']; $sales_notify_flag = $row['sales_notification']; } }
if($sales_sms_flag == ""){ $sales_sms_flag = 0; } if($sales_wapp_flag == ""){ $sales_wapp_flag = 0; } if($sales_notify_flag == ""){ $sales_notify_flag = 0; }

$out_bals = explode("@",$_POST['out_balance']);
$out_amt = $out_bals[0];
$out_nme = $out_bals[1];
$out_mob = "91".$out_bals[2];
$customer_mobile1 = $out_bals[2];
$totalamount = number_format_ind($finaltotal);
$bals = 0; $bals = (float)$out_amt + (float)$finaltotal;
$bal = number_format_ind($bals);

if((int)$sales_sms_flag == 1){
    if(!$conn){ }
    else{
        $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'sales' AND  `msg_type` = 'SMS' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $sms_user = $row['sms_user'];
            $sms_key = $row['sms_key'];
            $sms_msg_key = $row['msg_key'];
            $sms_accusage = $row['sms_accusage'];
            $sms_senderid = $row['sms_senderid'];
            $sms_entityid = $row['sms_entityid'];
            $sms_tempid = $row['sms_tempid'];
        }
$xml_data ='<?xml version="1.0"?>
<parent>
<child>
<user>'.$sms_user.'</user>
<key>'.$sms_key.'</key>
<mobile>'.$out_mob.'</mobile>
<message>
Dear: '.$out_nme.'
Date: '.date("d.m.Y",strtotime($date)).',
'.$item_dlt.',
Sale Amt: Rs. '.$totalamount.'/-
Balance: Rs. '.$bal.'/-
Thank You,
'.$cdetails.'
'.$sms_msg_key.'</message>
<accusage>'.$sms_accusage.'</accusage>
<senderid>'.$sms_senderid.'</senderid>
<entityid>'.$sms_entityid.'</entityid>
<tempid>'.$sms_tempid.'</tempid>
</child></parent>';
        $URL = "http://mobicomm.dove-sms.com//submitsms.jsp?"; 
        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $status = explode(",",$output);
            
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sms = (int)$row['sms']; } $incr = (int)$sms + 1;
        $sql = "UPDATE `master_generator` SET `sms` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $sms_code = "SMS-".$incr;

        $wsfile_path = $_SERVER['REQUEST_URI'];
        $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,file_name,addedemp,addedtime,updatedtime)
        VALUES ('$sms_code','$vcode','$out_mob','$xml_data','$status[1]','SALES','$wsfile_path','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)) { } else{ }
    }
} else{ }
     
if((int)$sales_wapp_flag == 1){
    if(!$conn){ }
    else{
        include "chicken_send_wapp_master2.php";
        $wapp_date = date("Y-m-d");
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
        $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
        
        $message = "Dear: ".$out_nme."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
        
        $database = $_SESSION['dbase'];
        $trtype = "Invoice Message";
        $ccode = $vcode;
        $number = $out_mob; 
        $wapp_code = "WAPP-".$incr_wapp;
        $sms_type = "WappKey";
        $msg_type = "WAPP";
        $msg_type = "WAPP";
        $msg_project = "CTS";
        $status = "CREATED";
        $trlink = $_SERVER['REQUEST_URI'];
        $wapp_msg = str_replace(" ","+",$message);
        $send_type = "text";
        chicken_send_wapp_text($database,$trtype,$trnum,$ccode,$number,$wapp_code,$sms_type,$msg_type,$msg_project,$status,$trlink,$wapp_msg,$send_type);
    }
} else{ }

if((int)$sales_notify_flag == 1){
    include "NotificationSending_ct_web.php";
    $message = "Dear: ".$out_nme."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
    $message = str_replace(" ","+",$message);

    $db = $_SESSION['dbase'];
    $sql = "SELECT * FROM `firebase_device_details` where db = '$db' and mobile = '$customer_mobile1'";
    $q3=mysqli_query($conns,$sql);$fb_count = mysqli_num_rows($q3);
    if($fb_count > 0){
        $row = mysqli_fetch_assoc($q3);
        send_notification("Sales","Sales Confirmation From ".$company_name,$row['device_token']);
        save_notification($customer_mobile1,$trnum,"cus_save_sales1.php","Sales","Sales Confirmation From ".$company_name." (".$trnum.")",$message);
    }
} else{ }

header('location:chicken_display_generalsales5.php?ccid='.$ccid);

