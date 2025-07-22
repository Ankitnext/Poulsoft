<?php
//chicken_save_customerreceipt3.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
include "cus_outbalfunction.php";
include "chicken_send_wapp_master2.php";
include "poulsoft_convert_langmst1.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Check and Insert WhatsApp Details
$file_name = "chicken_display_customerreceipt3.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
$sql = "SELECT * FROM `whatsapp_keygenerate_master` WHERE `file_type` = 'Receipt' AND `file_name` = '$file_name' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $w_cnt = mysqli_num_rows($query);
if($w_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $sms_type = $row['sms_type']; $wapp_ptrn = $row['pattern']; } }

$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql);
$ccount = mysqli_num_rows($query); $rct_sms_flag = $rct_wapp_flag = 0;
while($row = mysqli_fetch_assoc($query)){ $rct_sms_flag = $row['receipt_sms']; $rct_wapp_flag = $row['receipt_wapp']; }

$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE '$file_name' AND `field_function` LIKE 'Send Language Translated WhatsApp' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $slt_wflag = mysqli_num_rows($query); $from = $to = "";
if((int)$slt_wflag > 0){
    while($row = mysqli_fetch_assoc($query)){ $field_value = $row['field_value']; }
    if($field_value != ""){ $l1 = array(); $l1 = explode(",",$field_value); $from = $l1[0]; $to = $l1[1]; }
}

//SMS Master
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
//WhatsApp Master
$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = '$sms_type' AND  `msg_type` IN ('WAPP') AND `active` = '1'";
$query = mysqli_query($conn,$sql); $msg_header = $msg_footer = "";
while($row = mysqli_fetch_assoc($query)){ $msg_header = $row['msg_header']; $msg_footer = $row['msg_footer']; }

//Company Details
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }

//CoA Accounts
$sql = "SELECT *  FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $coa_name = array();
while($row = mysqli_fetch_assoc($query)){ $coa_name[$row['code']] = $row['description']; }

//Payment Information
$date = $ccode = $mode = $code = $amount1 = $dcno = $sector = $remarks = $tcds_per = $tcds_amt = $amount = array();
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['ccode'] as $ccodes){ $ccode[$i] = $ccodes; $i++; }
$i = 0; foreach($_POST['mode'] as $modes){ $mode[$i] = $modes; $i++; }
$i = 0; foreach($_POST['code'] as $codes){ $code[$i] = $codes; $i++; }
$i = 0; foreach($_POST['amount1'] as $amount1s){ $amount1[$i] = $amount1s; $i++; }
//$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$i = 0; foreach($_POST['tcds_per'] as $tcds_pers){ $tcds_per[$i] = $tcds_pers; $i++; }
$i = 0; foreach($_POST['tcds_amt'] as $tcds_amts){ $tcds_amt[$i] = $tcds_amts; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$vtype = "C";
$flag = $active = 1; 
$tdflag = $pdflag = 0;

$trtype = "customerreceipt3";
$trlink = "chicken_display_customerreceipt3.php";

//Save Payments
$dsize = sizeof($ccode);
for($i = 0;$i < $dsize;$i++){
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date[$i],"customerreceipt3","CRT","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    if($amount1[$i] == ""){ $amount1[$i] = 0; }
    if($tcds_per[$i] == ""){ $tcds_per[$i] = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if((float)$tcds_amt[$i] > 0){ } else{ $tcds_per[$i] = 0; }
    
    $sql = "INSERT INTO `customer_receipts` (`incr`,`prefix`,`trnum`,`date`,`ccode`,`mode`,`method`,`amount1`,`tcds_per`,`tcds_amt`,`amount`,`vtype`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$date[$i]','$ccode[$i]','$mode[$i]','$code[$i]','$amount1[$i]','$tcds_per[$i]','$tcds_amt[$i]','$amount[$i]','$vtype','$sector[$i]','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } 
    else {
        //Fetch Out-standing balance
        $cusdet = $customer_name = ""; $ftotal = 0; $obdetails = array();
		$cusdet = customer_outbalance($ccode[$i]); $obdetails = explode("@",$cusdet);
		$customer_name = $obdetails[0]; $mob_alist = array(); $mob_alist =  explode(",",$obdetails[1]);
		$ftotal = $obdetails[2]; $bamt = 0;
        if((float)$ftotal > (float)$amount[$i]){ $bamt = round(((float)$ftotal - (float)$amount[$i]),2); }
		else if($ftotal == "0" || $ftotal == "0.00" || $ftotal == ".00" || $ftotal == ""){ $bamt = "-".round((float)$amount[$i],2); }
		else if((float)$ftotal < (float)$amount[$i]){ $bamt = round(((float)$ftotal - (float)$amount[$i]),2); }
		else if(number_format_ind($ftotal) == number_format_ind($amount[$i])){ $bamt = 0; }
		else{ $bamt = 0; }

        $date_sms = date("d.m.Y",strtotime($date[$i]));
        //Send Messages
        if($rct_sms_flag == 1 && $active == 1){
                if(!$conn){ }
                else{
                    foreach($mob_alist as $mobs){
                        $out_mob = "91".$mobs;
			
$xml_data ='<?xml version="1.0"?>
<parent>
<child>
<user>'.$sms_user.'</user>
<key>'.$sms_key.'</key>
<mobile>'.$out_mob.'</mobile>
<message>
Dear: '.$customer_name.'
Date: '.$date_sms.'
Paid: Rs. '.number_format_ind($amount[$i]).'/-
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

                    $sms_date = date("Y-m-d");
                    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$sms_date' AND `tdate` >= '$sms_date' AND `type` = 'transactions'";
					$query = mysqli_query($conn,$sql); $wapp = 0; while($row = mysqli_fetch_assoc($query)){ $sms_s = $row['sms']; } $sincr = $sms_s + 1;
					$sql = "UPDATE `master_generator` SET `sms` = '$sincr' WHERE `fdate` <='$sms_date' AND `tdate` >= '$sms_date' AND `type` = 'transactions'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
					if($sincr < 10){ $sincr = '000'.$sincr; } else if($sincr >= 10 && $sincr < 100){ $sincr = '00'.$sincr; } else if($sincr >= 100 && $sincr < 1000){ $sincr = '0'.$sincr; } else { }
					$sms_code = "SMS-".$sincr;
					
					$wsfile_path = $_SERVER['REQUEST_URI'];
					$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,file_name,addedemp,addedtime,updatedtime,client)
					VALUES ('$sms_code','$ccode[$i]','$out_mob','$xml_data','$status[1]','RECEIPT','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
					if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); }
				}
			}
		}
		if($rct_wapp_flag == 1 && $active == 1){
			if(!$conn){ }
			else{
				if($wapp_ptrn == "Template"){
					$msg1 = array("dear"=>$customer_name, "date"=>$date_sms, "paid"=>number_format_ind($amount[$i])."/-", "mode"=>$coa_name[$code[$i]], "balance"=>number_format_ind(round((float)$bamt,2))."/-", "cdetails"=>$cdetails);
					$message = json_encode($msg1);
				}
				else{
                    if((int)$slt_wflag > 0 && $from != "" && $to != ""){
                        $words = ['Dear', 'Date', 'Paid: Rs.', 'Balance: Rs.', 'Thank You']; if(!empty($msg_header)){ $words[] = $msg_header; } if(!empty($msg_footer)){ $words[] = $msg_footer; }
                        $res_words = convert_language($conns, $from, $to, $words);

                        $message = "*".$res_words[strtolower($msg_header)]."*%0D%0A".$res_words[strtolower("Dear")].": ".$customer_name."%0D%0A".$res_words[strtolower("Date")].": ".$date_sms.",%0D%0A".$res_words[strtolower("Paid: Rs.")]." ".number_format_ind($amount[$i])."/-%0D%0A".$res_words[strtolower("Balance: Rs.")]." ".number_format_ind(round((float)$bamt,2))."/-%0D%0A".$res_words[strtolower("Thank You")].",%0D%0A".$cdetails."".$res_words[strtolower($msg_footer)];
                        $message = str_replace(" ","+",$message);
                    }
                    else{
                        $message = "Dear: ".$customer_name."%0D%0ADate: ".$date_sms.",%0D%0APaid: Rs. ".number_format_ind($amount[$i])."/-%0D%0ABalance: Rs. ".number_format_ind(round((float)$bamt,2))."/-%0D%0AThank You,%0D%0A".$cdetails;
                        $message = str_replace(" ","+",$message);
                    }
				}
				
				$wapp_date = date("Y-m-d");

				foreach($mob_alist as $mobs){
					$out_mob = "91".$mobs;
					$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
					$query = mysqli_query($conn,$sql); $wapp = 0; while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $wincr = $wapp + 1;
					$sql = "UPDATE `master_generator` SET `wapp` = '$wincr' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
					if($wincr < 10){ $wincr = '000'.$wincr; } else if($wincr >= 10 && $wincr < 100){ $wincr = '00'.$wincr; } else if($wincr >= 100 && $wincr < 1000){ $wincr = '0'.$wincr; } else { }
					
					$database = $_SESSION['dbase'];
					$wapp_type = "Receipt Message";
					$trnum = $code;
					$vcode = $ccode[$i];
					$number = $out_mob;
					$wapp_code = "WAPP-".$wincr;
					$msg_type = "WAPP";
					$msg_project = "CTS";
					$status = "CREATED";
					$wapp_link = $_SERVER['REQUEST_URI'];
					$wapp_msg = $message;
					$send_type = "text";
					chicken_send_wapp_text($database,$wapp_type,$trnum,$vcode,$number,$wapp_code,$sms_type,$msg_type,$msg_project,$status,$wapp_link,$wapp_msg,$send_type,$wapp_ptrn);
			
				}
			}
		}
    

     

     }
}
header('location:chicken_display_customerreceipt3.php?ccid='.$ccid);

