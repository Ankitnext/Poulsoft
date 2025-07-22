<?php
//cus_save_receiptsmart1.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "cus_outbalfunction.php";
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
global $sms_type; $sms_type = "WappKey"; include "chicken_wapp_connectionmaster.php";

if((int)$wapp_error_flag == 0){
	/*Check for Table Availability*/
	$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
	$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
	if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }

	$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Send WhatsApp Timer' AND `field_function` = 'cus_add_receiptsmart1.php' AND `user_access` = 'all'";
	$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $wapp_timer_flag = 0;
	if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_timer_flag = $row1['flag']; } }
	else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','cus_add_receiptsmart1.php','all','0');"; mysqli_query($conn,$sql1); }
}

if($_POST['submittrans'] == "addpage"){
    $pdates = date("Y-m-d",strtotime($_POST['pdate']));
    $date = date("Y-m-d",strtotime($_POST['pdate']));
    $modes = $_POST['mode'];
    $codes = $_POST['code'];
    foreach($_POST['pname'] as $pname){ $pnames[] = $pname; } $spnames = sizeof($pnames);
    foreach($_POST['amount'] as $amount){ $amounts[] = $amount; }
    foreach($_POST['gtamtinwords'] as $amtinword){ $amtinwords[] = $amtinword; }
    foreach($_POST['dcno'] as $dcno){ $dcnos[] = $dcno; }
    foreach($_POST['sector'] as $sector){ $sectors[] = $sector; }
    foreach($_POST['remark'] as $remark){ $remarks[] = $remark; }
    
    $sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; }
    
    $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$addedemp' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $aut_fields = $row['authorize']; }
    
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $receipts = $row['receipts']; $sms = $row['sms']; } $incr = $receipts + $spnames; $sms_incr = $sms + $spnames;
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $aut_flag = $row['aut_flag']; $rct_sms_flag = $row['receipt_sms']; $rct_wapp_flag = $row['receipt_wapp']; } } else { $rct_sms_flag = 0; $rct_wapp_flag = 0; }
    $active = 1;
    if($aut_flag == 1){
        if($aut_fields == "Receipt"){
            $active = 0;
        }
        else{
            $ex_fld = explode(",",$aut_fields);
            $afsize = sizeof($ex_fld);
            if($afsize > 0){
                for($i = 0;$i <= $afsize;$i++){
                    if($ex_fld[$i] != ""){
                        if($ex_fld[$i] == "Receipt"){
                            $active = 0;
                        }
                        else{ }
                    }
                    else{ }
                }
            }
            else{
                $active = 1;
            }
        }
    }
    else{
        $active = 1;
    }
    if($rct_sms_flag == 1 && $active == 1){ $upt_sms_ct = ",`sms` = '$sms_incr'"; } else{ $upt_sms_ct = ""; }
    $sql = "UPDATE `master_generator` SET `receipts` = '$incr'".$upt_sms_ct." WHERE  `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    $incr = $receipts + 1;
    $incr_sms = $sms + 1;
    
    $sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
    
    for($i = 0;$i < $spnames; $i++){
        if($rct_sms_flag == 1 && $active == 1){
            if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
            $sms_code = "SMS-".$incr_sms;
        }
        else{ $sms_code = $incr_sms = NULL; }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = "R-".$prefix."".$incr;
        
        $cusdet = $customer_name = $customer_mobile = ""; $ftotal = 0; $obdetails = array();
        $cusdet = customer_outbalance($pnames[$i]); $obdetails = explode("@",$cusdet);
        $customer_name = $obdetails[0]; $customer_mobile = "91".$obdetails[1];
        $ftotal = $obdetails[2]; $bamt = 0;
        if($ftotal > $amounts[$i]){
            $bamt = $ftotal - $amounts[$i];
        }
        else if($ftotal == "0" || $ftotal == "0.00" || $ftotal == ".00" || $ftotal == ""){
            $bamt = "-".$amounts[$i];
        }
        else if($ftotal < $amounts[$i]){
            $bamt = $ftotal - $amounts[$i];
        }
        else if(number_format_ind($ftotal) == number_format_ind($amounts[$i])){
            $bamt = 0;
        }
        else{
            $bamt = 0;
        }
        
        $sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,date,ccode,docno,mode,method,cdate,cno,amount,amtinwords,vtype,warehouse,remarks,sms_sent,whapp_sent,flag,active,addedemp,addedtime,tdflag,pdflag,client)
        VALUES ('$incr','R','$code','$pdates','$pnames[$i]','$dcnos[$i]','$modes','$codes',NULL,NULL,'$amounts[$i]','$amtinwords[$i]','C','$sectors[$i]','$remarks[$i]','$sms_code','$wapp_code','0','$active','$addedemp','$addedtime','0','0','$client')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        
        $date_sms = date("d.m.Y",strtotime($pdates));
        if($rct_sms_flag == 1 && $active == 1){
            if(!$conn){ }
            else{
                $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'receipts' AND  `msg_type` = 'SMS' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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
<mobile>'.$customer_mobile.'</mobile>
<message>
Dear: '.$customer_name.'
Date: '.$date_sms.'
Paid: Rs. '.number_format_ind($amounts[$i]).'/-
Balance: Rs. '.number_format_ind($bamt).'/-
Thank You,
'.$cdetails.'
'.$sms_msg_key.'</message>
<accusage>'.$sms_accusage.'</accusage>
<senderid>'.$sms_senderid.'</senderid>
<entityid>'.$sms_entityid.'</entityid>
<tempid>'.$sms_tempid.'</tempid>
</child>
</parent>';

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
                
				$wsfile_path = $_SERVER['REQUEST_URI'];
                $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,file_name,addedemp,addedtime,updatedtime,client)
                VALUES ('$sms_code','$pnames[$i]','$customer_mobile','$xml_data','$status[1]','RECEIPT','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
                if(!mysqli_query($conn,$sql)) { } else{ }
            }
        }
        else { }
        if($rct_wapp_flag == 1 && $active == 1){
            if(!$conn){ }
            else{
                $message = "Dear: ".$customer_name."%0D%0ADate: ".$date_sms.",%0D%0APaid: Rs. ".number_format_ind($amounts[$i])."/-%0D%0ABalance: Rs. ".number_format_ind($bamt)."/-%0D%0AThank You,%0D%0A".$cdetails;
                $message = str_replace(" ","+",$message);
                $wapp_date = date("Y-m-d");
                $ccode = $vendorcode[$j];
                $number = $customer_mobile; $type = "text";
                
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
                    $trtype = "Receipt Message";
                    $trnum = $code;
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
        else{ }
        $incr++;
        $incr_sms++;
    }
    if($i == $spnames){
        ?>
        <script>
            var x = confirm("Would you like to save more entries?");
            if(x == true){
                window.location.href = "cus_add_receiptsmart1.php";
            }
            else if(x == false){
                window.location.href = "cus_display_receiptsmart1.php";
            }
        </script>
        <?php
    }
    else {
        echo "Error:-".mysqli_error($conn);
    }
}
?>