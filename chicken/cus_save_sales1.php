<?php
//cus_savesales_new.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "NotificationSending_ct_web.php";
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');

$sql='SHOW COLUMNS FROM `master_itemfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sales_notification", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `sales_notification` INT(11) NULL DEFAULT '0' COMMENT ''"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("delivery_charge", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `delivery_charge` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `tcdsamt`"; mysqli_query($conn,$sql); }
if(in_array("dressing_charge", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `dressing_charge` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `delivery_charge`"; mysqli_query($conn,$sql); }

$authorize = ""; $sale_autoaut_flag = 1;
$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$addedemp' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $authorize = $row['authorize']; }
if($authorize != ""){ $aut_alist = explode(",",$authorize); foreach($aut_alist as $acode){ if($acode == "Sale"){ $sale_autoaut_flag = 0; } } }

if($_POST['submittrans'] == "addpage"){
    $date = date("Y-m-d",strtotime($_POST['pdate']));
    $d = date("d",strtotime($date));
    $m = date("m",strtotime($date));
    $y = date("Y",strtotime($date));
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
    $vendorcode = $_POST['pname'];
    $sql = "UPDATE `main_contactdetails` SET `flag` = '1' WHERE `code` = '$vendorcode'"; mysqli_query($conn,$sql);
    $sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ 
        while($row = mysqli_fetch_assoc($query)){
            $ifwt = $row['wt'];
            $ifbw = $row['bw'];
            $ifjbw = $row['jbw'];
            $ifjbwen = $row['jbwen'];
            $ifctype = $row['ctype'];
            $sales_sms_flag = $row['sales_sms'];
            $sales_wapp_flag = $row['sales_wapp'];
            $sales_notification_flag = $row['sales_notification'];
        }
    }
    else {
        $ifwt = $ifbw = $ifjbw = $ifjbwen = $ifctype = 0;
        $sales_sms_flag = 0;
        $sales_wapp_flag = 0;
    }
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; $sms = $row['sms']; } $incr = $sales + 1; $incr_sms = $sms + 1;
    
    if($sales_sms_flag == 1 && $sale_autoaut_flag == 1){ $sales_incr = ",`sms` = '$incr_sms'"; } else{ $sales_incr = ""; }
    $sql = "UPDATE `master_generator` SET `sales` = '$incr'".$sales_incr." WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    if($sales_sms_flag == 1 && $sale_autoaut_flag == 1){
        if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
        $sms_code = "SMS-".$incr_sms;
    }
    else{
        $sms_code = NULL;
    }
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
    $code = "S".$pfx."-".$incr;
    $invoice = $code;
    $bookinvoice = $_POST['binv'];
    $delivery_charge = $_POST['delivery_charge']; if($delivery_charge == ""){ $delivery_charge = 0; }
    $dressing_charge = $_POST['dressing_charge']; if($dressing_charge == ""){ $dressing_charge = 0; }
    $finaltotal = round($_POST['gtamt']);
    if($_POST['tdsperval'] == "" || $_POST['tdsperval'] == NULL || $_POST['tdsperval'] == "0"){
        $tcdsper = "0.00";
    }
    else{
        $tcdsper = $_POST['tdsperval'];
    }
    if($_POST['tdsamt'] == "" || $_POST['tdsamt'] == NULL || $_POST['tdsamt'] == "0"){
        $tcdsamt = "0.00";
    }
    else{
        $tcdsamt = $_POST['tdsamt'];
    }
    if($tcdsamt == "0.00" || $tcdsamt == "0" || $tcdsamt =="0.0"){
        $tcdsper = 0;
    }
    $amtinwds = convert_number_to_words($finaltotal);
    $amtinwds = $amtinwds." Rupees Only";
    $drivercode = $_POST['dname'];
    $vehiclecode = $_POST['vno'];
    $discounttype = $taxtype = $taxcode = "Amt"; $discountcode = "0.00";
    $flag = $authorization = $tdflag = $pdflag = 0;
    $remarks = $_POST['narr'];
    $jals = $birds = $totalweight = $emptyweight = array();
    $i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
    if($ifjbwen == 1 || $ifjbw == 1){ $i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; } } else { $jals = array(); }
    if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ $i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; } } else { $birds = array(); }
    if($ifjbwen == 1){ $i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; } } else { $totalweight = array(); }
    if($ifjbwen == 1){ $i = 0; foreach($_POST['ewval'] as $eweights){ $i = $i + 1; $emptyweight[$i] = $eweights; } } else { $emptyweight = array(); }
    $i = 0; foreach($_POST['nwval'] as $nweights){ $i = $i + 1; $netweight[$i] = $nweights; }
    $i = 0; foreach($_POST['iprice'] as $iprices){ $i = $i + 1; $itemprice[$i] = $iprices; }
    $i = 0; foreach($_POST['idisc'] as $idiscs){ $i = $i + 1; $discountvalue[$i] = $discountamt[$i] = $idiscs; }
    $i = 0; foreach($_POST['itax'] as $itaxs){ $i = $i + 1; $taxvalue[$i] = $taxamount[$i] = $itaxs; }
    $i = 0; foreach($_POST['tamt'] as $tamts){ $i = $i + 1; $totalamt[$i] = $tamts; }
    $i = 0; foreach($_POST['wcodes'] as $whouses){ $i = $i + 1; $warehouse[$i] = $whouses; }
    $roffsize = sizeof($totalamt); $rtamt = 0; for($k = 1;$k <= $roffsize;$k++){ $rtamt = $rtamt + $totalamt[$k]; } $rtamt = $rtamt + $tcdsamt + $delivery_charge + $dressing_charge;
    if($rtamt >= $finaltotal){ $roundoff = $rtamt - $finaltotal; }
    else { $roundoff = $finaltotal - $rtamt; }
    if($sale_autoaut_flag == 1){ $active = 1; } else{ $active = 0; }
    $item_dlt = "";
    for($j = 1;$j <= $i;$j++){
        if($warehouse[$j] == "select" || $itemcode[$j] == "select") {
        }
        else {
            if($item_dlt == ""){
                if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
                $item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
            }
            else{
                if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
                $item_dlt = $item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
            }
            if($jals[$j] == "" || $jals[$j] == NULL){ $jals[$j] = "0.00"; } else{ }
            if($birds[$j] == "" || $birds[$j] == NULL){ $birds[$j] = "0.00"; } else{ }
            if($totalweight[$j] == "" || $totalweight[$j] == NULL){ $totalweight[$j] = "0.00"; } else{ }
            if($emptyweight[$j] == "" || $emptyweight[$j] == NULL){ $emptyweight[$j] = "0.00"; } else{ }
            if($netweight[$j] == "" || $netweight[$j] == NULL){ $netweight[$j] = "0.00"; } else{ }
            if($itemprice[$j] == "" || $itemprice[$j] == NULL){ $itemprice[$j] = "0.00"; } else{ }
            if($discountvalue[$j] == "" || $discountvalue[$j] == NULL){ $discountvalue[$j] = "0.00"; } else{ }
            if($discountamt[$j] == "" || $discountamt[$j] == NULL){ $discountamt[$j] = "0.00"; } else{ }
            if($taxamount[$j] == "" || $taxamount[$j] == NULL){ $taxamount[$j] = "0.00"; } else{ }
            if($taxvalue[$j] == "" || $taxvalue[$j] == NULL){ $taxvalue[$j] = "0.00"; } else{ }
            $sql = "INSERT INTO `customer_sales` (date,incr,d,m,y,fy,invoice,bookinvoice,customercode,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcdsamt,delivery_charge,dressing_charge,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,sms_sent,addedemp,addedtime,client) 
            VALUES ('$date','$incr','$d','$m','$y','$pfx','$invoice','$bookinvoice','$vendorcode','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$itemprice[$j]','$totalamt[$j]','$tcdsper','$tcdsamt','$delivery_charge','$dressing_charge','$roundoff','$finaltotal','$finaltotal','$amtinwds','$warehouse[$j]','$flag','$active','$authorization','$tdflag','$pdflag','$drivercode','$vehiclecode','$discounttype','$discountvalue[$j]','$taxtype','$taxvalue[$j]','$discountamt[$j]','$taxamount[$j]','$taxcode','$discountcode','$remarks','$sms_code','$addedemp','$addedtime','$client')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { unset($_SESSION['icode']); }
        }
    }
    $out_bals = array(); $out_bals = explode("@",$_POST['outstanding']);
    $out_amt = $out_bals[0];
    $out_nme = $out_bals[1];
    $mob_alist = array(); $mob_alist =  explode(",",$out_bals[2]);
    $customer_mobile1 = $out_bals[2];
    $totalamount = number_format_ind($finaltotal);
    $bals = 0; $bals = $out_amt + $finaltotal;
    $bal = number_format_ind($bals);
    $sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $company_name = $row['cname'];
        $cdetails = $row['cname']." - ".$row['cnum'];
    }
    if($sales_sms_flag == 1 && $sale_autoaut_flag == 1){
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
            foreach($mob_alist as $mobs){
                $out_mob = "91".$mobs;
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
                $status[1];
                $wsfile_path = $_SERVER['REQUEST_URI'];
                $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,file_name,addedemp,addedtime,updatedtime,client)
                VALUES ('$sms_code','$vendorcode','$out_mob','$xml_data','$status[1]','SALES','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
                if(!mysqli_query($conn,$sql)) { } else{ }
            }
        }
    }
    else{ }
         
    if($sales_wapp_flag == 1 && $sale_autoaut_flag == 1){
        if(!$conn){ }
        else{
            global $sms_type; $sms_type = "WappKey"; include "chicken_wapp_connectionmaster.php";
            
            if((int)$wapp_error_flag == 0){
                /*Check for Table Availability*/
                $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
                $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
                if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
            
                $sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Send WhatsApp Timer' AND `field_function` = 'cus_add_sales1.php' AND `user_access` = 'all'";
                $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $wapp_timer_flag = 0;
                if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_timer_flag = $row1['flag']; } }
                else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','cus_add_sales1.php','all','0');"; mysqli_query($conn,$sql1); }
            }
            $wapp_timer_flag = 1;
            
            foreach($mob_alist as $mobs){
                $out_mob = "91".$mobs;
                $message = "Dear: ".$out_nme."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
                $message = str_replace(" ","+",$message);
                $number = $out_mob; $type = "text";
                $ccode = $vendorcode;
                $wapp_date = date("Y-m-d");

                if((int)$url_id == 3){
                    $msg_info = $curlopt_url.''.$instance_id.'/messages/chat?token='.$access_token.'&to='.$number.'&body='.$message;
                }
                else{
                    $msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token;
                }
                
                if($wapp_error_flag == 0){
                    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                    
                    $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                    
                    if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                    $wapp_code = "WAPP-".$incr_wapp;
                    $wsfile_path = $_SERVER['REQUEST_URI'];

                    $database = $_SESSION['dbase'];
                    $trtype = "Invoice Message";
                    $trnum = $invoice;
                    $vendor = $ccode;
                    $mobile = $number;
                    $msg_trnum = $wapp_code;
                    $msg_type = "WAPP";
                    $msg_project = "CTS";
                    $status = "CREATED";
                    $trlink = $_SERVER['REQUEST_URI'];
                    $sql = "INSERT INTO `master_pendingmessages` (`database`,`url_id`,`trtype`,`trnum`,`vendor`,`mobile`,`msg_trnum`,`msg_type`,`msg_info`,`msg_project`,`status`,`trlink`,`addedemp`,`addedtime`,`updatedtime`)
                    VALUES ('$database','$url_id','$trtype','$trnum','$vendor','$mobile','$msg_trnum','$msg_type','$msg_info','$msg_project','$status','$trlink','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conns,$sql)) { } else{ }
                }
            }
        }
    }
    else{ }

    
    if($sales_notification_flag == 1  && $sale_autoaut_flag == 1){
        $message = "Dear: ".$out_nme."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
        $message = str_replace(" ","+",$message);

        $db = $_SESSION['dbase'];
        $sql = "SELECT * FROM `firebase_device_details` where db = '$db' and mobile = '$customer_mobile1'";
        $q3=mysqli_query($conns,$sql);$fb_count = mysqli_num_rows($q3);
        if($fb_count > 0){
            $row = mysqli_fetch_assoc($q3);
            send_notification("Sales","Sales Confirmation From ".$company_name,$row['device_token']);
            save_notification($customer_mobile1,$code,"cus_save_sales1.php","Sales","Sales Confirmation From ".$company_name." (".$code.")",$message);
        }
    }
    
	//if($_SERVER['REMOTE_ADDR'] == "183.83.194.198"){

    //}
    //else{
    ?>
    <script>
        var x = confirm("Would you like to save more entries?");
        if(x == true){
            window.location.href = "cus_add_sales1.php";
        }
        else if(x == false){
            window.location.href = "cus_displaysales.php";
        }
    </script>
    <?php
    //}
}
function convert_number_to_words($amount) {
    $words = array();
    $words[0] = '';
    $words[1] = 'One';
    $words[2] = 'Two';
    $words[3] = 'Three';
    $words[4] = 'Four';
    $words[5] = 'Five';
    $words[6] = 'Six';
    $words[7] = 'Seven';
    $words[8] = 'Eight';
    $words[9] = 'Nine';
    $words[10] = 'Ten';
    $words[11] = 'Eleven';
    $words[12] = 'Twelve';
    $words[13] = 'Thirteen';
    $words[14] = 'Fourteen';
    $words[15] = 'Fifteen';
    $words[16] = 'Sixteen';
    $words[17] = 'Seventeen';
    $words[18] = 'Eighteen';
    $words[19] = 'Nineteen';
    $words[20] = 'Twenty';
    $words[30] = 'Thirty';
    $words[40] = 'Forty';
    $words[50] = 'Fifty';
    $words[60] = 'Sixty';
    $words[70] = 'Seventy';
    $words[80] = 'Eighty';
    $words[90] = 'Ninety';

    $amount = strval($amount);

    $atemp = explode(".",$amount);
    $number = str_replace(",","",$atemp[0]);
    $n_length = strlen($number);
    $words_string = "";

    if($n_length <= 9){
        $received_n_array = array(); $n_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

        for ($i = 0; $i < $n_length; $i++) {
            $received_n_array[$i] = substr($number,$i, 1);
        }
        for ($i = 9 - $n_length, $j = 0; $i < 9; $i++, $j++) {
            $n_array[$i] = $received_n_array[$j];
        }
        for ($i = 0, $j = 1; $i < 9; $i++, $j++) {
            if ($i == 0 || $i == 2 || $i == 4 || $i == 7) {
                if ($n_array[$i] == 1) {
                    $n_array[$j] = 10 + (int)$n_array[$j];
                    $n_array[$i] = 0;
                }
            }
        }
        $value = "";
        for ($i = 0; $i < 9; $i++) {
            if ($i == 0 || $i == 2 || $i == 4 || $i == 7) {
                $value = $n_array[$i] * 10;
            } else {
                $value = $n_array[$i];
            }
            if ($value != 0) {
                $words_string .= $words[$value]." ";
            }
            if (($i == 1 && $value != 0) || ($i == 0 && $value != 0 && $n_array[$i + 1] == 0)) {
                $words_string .= "Crores ";
            }
            if (($i == 3 && $value != 0) || ($i == 2 && $value != 0 && $n_array[$i + 1] == 0)) {
                $words_string .= "Lakhs ";
            }
            if (($i == 5 && $value != 0) || ($i == 4 && $value != 0 && $n_array[$i + 1] == 0)) {
                $words_string .= "Thousand ";
            }
            if ($i == 6 && $value != 0 && ($n_array[$i + 1] != 0 && $n_array[$i + 2] != 0)) {
                $words_string .= "Hundred and ";
            }
            else if ($i == 6 && $value != 0) {
                $words_string .= "Hundred ";
            }
        }
        $words_string = str_replace("  "," ",$words_string);
        if((int)$atemp[1] > 0){
            $paisa = " and ".$words[$atemp[1]*10]." paisa only";
        }
        else{
            $paisa = "rupees only";
        }
        $words_string .= $paisa;
    }
    return $words_string;
}
?>