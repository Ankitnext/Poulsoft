<?php
//main_updatepursale
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
//include "cus_sale_messages.php";
include "cus_outbalfunction.php";
include "pur_outbalfunction.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');

global $sms_type; $sms_type = "WappKey"; include "chicken_wapp_connectionmaster.php";

if((int)$wapp_error_flag == 0){
	/*Check for Table Availability*/
	$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
	$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
	if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }

	$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Send WhatsApp Timer' AND `field_function` = 'main_addpursale.php' AND `user_access` = 'all'";
	$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $wapp_timer_flag = 0;
	if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_timer_flag = $row1['flag']; } }
	else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','main_addpursale.php','all','0');"; mysqli_query($conn,$sql1); }
}

$client = $_SESSION['client'];
$cid = $_SESSION['disppursale'];
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
if($_POST['submittrans'] == "addpage"){
	$date = date("Y-m-d",strtotime($_POST['pdate']));
	$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
	$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
	$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
	$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcsper = $row['tcds']; }
				
	$d = date("d",strtotime($date)); $m = date("m",strtotime($date)); $y = date("Y",strtotime($date));
	$warehouse = $_POST['wcodes'];
	$tr_incr = $_POST['incr'];
	$i = 0; foreach($_POST['snames'] as $snames){ $i = $i + 1; $sdetails = explode("@",$snames); $sup_code[$i] = $sdetails[0]; $sup_names[$i] = $sdetails[1]; }
	$i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
	$i = 0; foreach($_POST['bno'] as $bno){ $i = $i + 1; $bnos[$i] = $bno; }
	$i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; }
	$i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }
	$i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; }
	$i = 0; foreach($_POST['ewval'] as $eweights){ $i = $i + 1; $emptyweight[$i] = $eweights; }
	$i = 0; foreach($_POST['nwval'] as $nweights){ $i = $i + 1; $netweight[$i] = $nweights; }
	$i = 0; foreach($_POST['sup_iprice'] as $sup_iprices){ $i = $i + 1; $sup_iprice[$i] = $sup_iprices; }
	$i = 0; foreach($_POST['sup_tamt'] as $sup_tamts){ $i = $i + 1; if($sup_tamts ==""){ $sup_tamts = 0; } $sup_tamt[$i] = $sup_tamts; }
	$i = 0; foreach($_POST['tds_tamt'] as $tds_tamts){ $i = $i + 1; if($tds_tamts ==""){ $tds_tamts = 0; } $tds_tamt[$i] = $tds_tamts; }
	$i = 0; foreach($_POST['sup_famt'] as $sup_famts){ $i = $i + 1; if($sup_famts ==""){ $sup_famts = 0; } $sup_ftotal[$i] = round($sup_famts); }
	
	$i = 0; foreach($_POST['cnames'] as $cnames){ $i = $i + 1; $cdetails = explode("@",$cnames); $cus_code[$i] = $cdetails[0]; $cus_names[$i] = $cdetails[1]; }
	$i = 0; foreach($_POST['cus_iprice'] as $cus_iprices){ $i = $i + 1; $cus_iprice[$i] = $cus_iprices; }
	$i = 0; foreach($_POST['cus_tamt'] as $cus_tamts){ $i = $i + 1; if($cus_tamts ==""){ $cus_tamts = 0; } $cus_tamt[$i] = $cus_tamts; }
	$i = 0; foreach($_POST['tcs_tamt'] as $tcs_tamts){ $i = $i + 1; if($tcs_tamts ==""){ $tcs_tamts = 0; } $tcs_tamt[$i] = $tcs_tamts; }
	$i = 0; foreach($_POST['cus_famt'] as $cus_famts){ $i = $i + 1; if($cus_famts ==""){ $cus_famts = 0; } $cus_ftotal[$i] = round($cus_famts); }
	$i = 0; foreach($_POST['vehicle'] as $vehicles){ $i = $i + 1; $vehicle[$i] = $vehicles; }
	$i = 0; foreach($_POST['driver'] as $drivers){ $i = $i + 1; $driver[$i] = $drivers; }
	$i = 0; foreach($_POST['narr'] as $narr){ $i = $i + 1; $remarks[$i] = $narr; }
	
	$tr_size = sizeof($cus_code);
	$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
	
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
	
	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; $purchases = $row['purchases']; $sms = $row['sms']; $wapp = $row['wapp']; }
	$incr_sale = $sales + $tr_size; $incr_purchase = $purchases + $tr_size;
	$sql = "UPDATE `master_generator` SET `sales` = '$incr_sale',`purchases` = '$incr_purchase' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
	if(!mysqli_query($conn,$sql)){ echo "Error:-".mysqli_error($conn); } else { }
	
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $sales_sms_flag = $row['sales_sms']; $sales_wapp_flag = $row['sales_wapp']; $purchase_sms_flag = $row['purchase_sms']; $purchase_wapp_flag = $row['purchase_wapp']; } }
	else { $sales_sms_flag = $sales_wapp_flag = $purchase_sms_flag = $purchase_wapp_flag = 0; }
	
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
	$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-Sales' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $instance_id = $row['sms_key']; $access_token = $row['msg_key']; $url_id = $row['url_id']; }
	$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
	
	$incr_sale = $sales; $incr_purchase = $purchases;
	for($j = 1;$j <= $tr_size;$j++){
		$item_dlt = "";
		if($item_dlt == ""){
			if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
			$item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$cus_iprice[$j];
		}
		else{
			if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
			$item_dlt = $item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$cus_iprice[$j];
		}
		$sup_item_dlt = "";
		if($sup_item_dlt == ""){
			if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
			$sup_item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$sup_iprice[$j];
		}
		else{
			if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
			$sup_item_dlt = $sup_item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$sup_iprice[$j];
		}
		
		$incr_sale = $incr_sale + 1;
		if($incr_sale < 10){ $incr_sale = '000'.$incr_sale; } else if($incr_sale >= 10 && $incr_sale < 100){ $incr_sale = '00'.$incr_sale; } else if($incr_sale >= 100 && $incr_sale < 1000){ $incr_sale = '0'.$incr_sale; } else { }
		$sale_inv = "S".$pfx."-".$incr_sale;
		
		$incr_purchase = $incr_purchase + 1;
		if($incr_purchase < 10){ $incr_purchase = '000'.$incr_purchase; } else if($incr_purchase >= 10 && $incr_purchase < 100){ $incr_purchase = '00'.$incr_purchase; } else if($incr_purchase >= 100 && $incr_purchase < 1000){ $incr_purchase = '0'.$incr_purchase; } else { }
		$pur_inv = "P".$pfx."-".$incr_purchase;
		
		$cus_amtinwds = convert_number_to_words($cus_ftotal[$j]); $cus_amtinwds = $cus_amtinwds." Rupees Only";
		$cus_roundoff = ""; $cus_roundoff = $cus_ftotal[$j] - ($cus_tamt[$j] + $tcs_tamt[$j]);
		
		$sup_amtinwds = convert_number_to_words($sup_ftotal[$j]); $sup_amtinwds = $sup_amtinwds." Rupees Only";
		$sup_roundoff = ""; $sup_roundoff = $sup_ftotal[$j] - ($sup_tamt[$j] + $tds_tamt[$j]);
		
		if($sales_sms_flag == 1 || $sales_wapp_flag == 1){
			$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $sms = $row['sms']; } $incr_sms = $sms + 1;
			$error_code = 0;
			if($sales_sms_flag == 1){ $sales_msg_incr = "`sms` = '$incr_sms'"; }
			else{ $sales_msg_incr = ""; }
			if($sales_msg_incr != ""){
				$sql = "UPDATE `master_generator` SET ".$sales_msg_incr." WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ $error_code = 1; } else { $error_code = 0; }
			}
		
			$cusdet = $obdetails = $customer_name = $customer_mobile = ""; $out_amt = 0;
			$cusdet = customer_outbalance($cus_code[$j]); $obdetails = explode("@",$cusdet);
			$customer_name = $obdetails[0]; $customer_mobile = "91".$obdetails[1];
			$out_amt = $obdetails[2];
			$cus_sale_amt = number_format_ind($cus_ftotal[$j]); $bals = $out_amt + $cus_ftotal[$j]; $cus_bal_amt = number_format_ind($bals);
			
			if($sales_sms_flag == 1){
				if(!$conn){ }
				else{
				if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
				$sms_code = "SMS-".$incr_sms;
$xml_data ='<?xml version="1.0"?>
<parent>
<child>
<user>'.$sms_user.'</user>
<key>'.$sms_key.'</key>
<mobile>'.$customer_mobile.'</mobile>
<message>
Dear: '.$customer_name.'
Date: '.date("d.m.Y",strtotime($date)).',
'.$item_dlt.',
Sale Amt: Rs. '.$cus_sale_amt.'/-
Balance: Rs. '.$cus_bal_amt.'/-
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
				$sms_sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client)
				 VALUES ('$sms_code','$cus_code[$j]','$customer_mobile','$xml_data','$status[1]','SALES','$addedemp','$addedtime','$addedtime','$client')";
				if(!mysqli_query($conn,$sms_sql)) { $error_code = 2; } else{  }
				}
			}
			else{ }
			if($sales_wapp_flag == 1){
				if(!$conn){ }
				else{					
					$message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$cus_sale_amt."/-%0D%0ABalance: Rs. ".$cus_bal_amt."/-%0D%0AThank You,%0D%0A".$cdetails;
					$message = str_replace(" ","+",$message);
					$number = $customer_mobile; $type = "text";
					$ccode = $cus_code[$j];
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
						$trnum = NULL;
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
		}
		else{
			$sms_code = $wapp_code = "NA"; $error_code = 0;
		}
		if($purchase_sms_flag == 1 || $purchase_wapp_flag == 1){
			$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($pur_conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $sms = $row['sms']; $wapp = $row['wapp']; } $incr_sms = $sms + 1; $incr_wapp = $wapp + 1;
			$error_code = 0;
			if($purchase_sms_flag == 1 && $purchase_wapp_flag == 0){ $purchase_msg_incr = "`sms` = '$incr_sms'"; }
			else if($purchase_sms_flag == 1 && $purchase_wapp_flag == 1){ $purchase_msg_incr = "`sms` = '$incr_sms',`wapp` = '$incr_wapp'"; }
			else if($purchase_sms_flag == 0 && $purchase_wapp_flag == 1){ $purchase_msg_incr = "`wapp` = '$incr_wapp'"; }
			else{ $purchase_msg_incr = ""; }
				
			$sql = "UPDATE `master_generator` SET ".$purchase_msg_incr." WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
			if(!mysqli_query($pur_conn,$sql)){ $error_code = 1; } else { $error_code = 0; }
			
			$supdet = $obdetails = $sup_name = $sup_mobile = ""; $sftotal = 0;
			$supdet = supplier_outbalance($sup_code[$j]); $obdetails = explode("@",$supdet);
			$sup_name = $obdetails[0]; $sup_mobile = "91".$obdetails[1];
			$sftotal = $obdetails[2];
			$sup_pur_amt = number_format_ind($sup_ftotal[$j]); $bal = $sftotal + $sup_ftotal[$j]; $sup_bal_amt = number_format_ind($bal);
			
			if($purchase_sms_flag == 1){
				if(!$conn){ }
				else{
				if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
				$pur_sms_code = "SMS-".$incr_sms;
$xml_data ='<?xml version="1.0"?>
<parent>
<child>
<user>'.$sms_user.'</user>
<key>'.$sms_key.'</key>
<mobile>'.$sup_mobile.'</mobile>
<message>
Dear: '.$sup_name.'
Date: '.date("d.m.Y",strtotime($date)).',
'.$sup_item_dlt.',
Sale Amt: Rs. '.$sup_pur_amt.'/-
Balance: Rs. '.$sup_bal_amt.'/-
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
if($number > 0 && $baseUnit > 0){ $numBaseUnits = (int) ($number / $baseUnit); } else{ $numBaseUnits = 0; }
				curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				$status = explode(",",$output);
				$status[1];
				$sms_sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client)
				 VALUES ('$pur_sms_code','$sup_code[$j]','$sup_mobile','$xml_data','$status[1]','BB-Purchase','$addedemp','$addedtime','$addedtime','$client')";
				if(!mysqli_query($pur_conn,$sms_sql)) { $error_code = 2; } else{  }
                }
			}
			else{ }
			if($purchase_wapp_flag == 1){
				if(!$conn){ }
				else{
				if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
				$pur_wapp_code = "WAPP-".$incr_wapp;
				
                $message = "Dear: ".$sup_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$sup_item_dlt.",%0D%0APurchased Amt: ".$sup_pur_amt."/-%0D%0ABalance: Rs. ".$sup_bal_amt."/-%0D%0AThank You,%0D%0A".$cdetails;
				$message = str_replace(" ","+",$message);
				$number = $sup_mobile; $type = "text";
				$ccode = $sup_code[$j];
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
						$trnum = NULL;
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
		}
		else{
			$pur_sms_code = $pur_wapp_code = "NA"; $error_code = 0;
		}
		
		if($jals[$j] == "" || $jals[$j] == "0.00" || $jals[$j] == 0.00 || $jals[$j] == 0 || $jals[$j] == "0" || $jals[$j] == NULL || strlen($jals[$j]) == 0){ $jals[$j] = "0.00"; }
		if($totalweight[$j] == "" || $totalweight[$j] == "0.00" || $totalweight[$j] == 0.00 || $totalweight[$j] == 0 || $totalweight[$j] == "0" || $totalweight[$j] == NULL || strlen($totalweight[$j]) == 0){ $totalweight[$j] = "0.00"; }
		if($emptyweight[$j] == "" || $emptyweight[$j] == "0.00" || $emptyweight[$j] == 0.00 || $emptyweight[$j] == 0 || $emptyweight[$j] == "0" || $emptyweight[$j] == NULL || strlen($emptyweight[$j]) == 0){ $emptyweight[$j] = "0.00"; }
		if($birds[$j] == "" || $birds[$j] == "0.00" || $birds[$j] == 0.00 || $birds[$j] == 0 || $birds[$j] == "0" || $birds[$j] == NULL || strlen($birds[$j]) == 0){ $birds[$j] = "0.00"; }
		if($netweight[$j] == "" || $netweight[$j] == "0.00" || $netweight[$j] == 0.00 || $netweight[$j] == 0 || $netweight[$j] == "0" || $netweight[$j] == NULL || strlen($netweight[$j]) == 0){ $netweight[$j] = "0.00"; }
		
		$sql = "INSERT INTO `customer_sales` (date,incr,d,m,y,fy,invoice,link_trnum,bookinvoice,customercode,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcdsamt,roundoff,finaltotal,balance,amtinwords,trtype,warehouse,flag,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,sms_sent,addedemp,addedtime,client) 
		VALUES ('$date','$incr_sale','$d','$m','$y','$pfx','$sale_inv','$pur_inv','$bnos[$j]','$cus_code[$j]','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$cus_iprice[$j]','$cus_tamt[$j]','$tcsper','$tcs_tamt[$j]','$cus_roundoff','$cus_ftotal[$j]','$cus_ftotal[$j]','$cus_amtinwds','PST','$warehouse','0','0','0','0','$driver[$j]','$vehicle[$j]','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','$remarks[$j]','$sms_code','$addedemp','$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ die("Cus-Error:-".mysqli_error($conn)); } else { }
		
		$sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,link_trnum,bookinvoice,vendorcode,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcdsamt,roundoff,finaltotal,balance,amtinwords,warehouse,flag,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client) 
		VALUES ('$date','$incr_purchase','$d','$m','$y','$pfx','$pur_inv','$sale_inv','$bnos[$j]','$sup_code[$j]','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$sup_iprice[$j]','$sup_tamt[$j]','$tdsper','$tds_tamt[$j]','$sup_roundoff','$sup_ftotal[$j]','$sup_ftotal[$j]','$sup_amtinwds','$warehouse','0','0','0','0','$driver[$j]','$vehicle[$j]','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','$remarks[$j]','$addedemp','$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ die("Sup-Error:-".mysqli_error($conn)); } else { }
		
	}
	?>
	<script>
		var x = confirm("Would you like to save more entries?");
		var a = '<?php echo $cid; ?>';
		if(x == true){
			window.location.href = "main_addpursale.php";
		}
		else if(x == false){
			window.location.href = "main_displaypursale.php?cid="+a;
		}
	</script>
	<?php
}
else if($_GET['page'] == "delete"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){
		while($row = mysqli_fetch_assoc($query)){
			$type = "Purchase-Sales Invoice";
			$date = $row['date'];
			$transactionno = $row['invoice'];
			$link_trnum = $row['link_trnum'];
			$description = $row['itemcode'];
			$doccode = $row['bookinvoice'];
			$pcode = $row['customercode'];
			$icode = $row['itemcode'];
			$quantity = $row['netweight'];
			$amount = $row['finaltotal'];
			$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
			VALUES('$type','$date','$transactionno','$description','$link_trnum','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
		$sql ="SELECT * FROM `pur_purchase` WHERE `invoice` = '$link_trnum' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){
				$type = "Purchase-Sales Invoice";
				$date = $row['date'];
				$transactionno = $row['invoice'];
				$link_trnum2 = $row['link_trnum'];
				$description = $row['itemcode'];
				$doccode = $row['bookinvoice'];
				$pcode = $row['vendorcode'];
				$icode = $row['itemcode'];
				$quantity = $row['netweight'];
				$amount = $row['finaltotal'];
				$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
				VALUES('$type','$date','$transactionno','$description','$link_trnum2','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
		}
		$sql = "DELETE FROM `pur_purchase` WHERE `invoice` = '$link_trnum'";
		if(!mysqli_query($conn,$sql)){
			die("Error:-".mysqli_error($conn));
		}
		else {
			$sql = "DELETE FROM `customer_sales` WHERE `invoice` = '$id'";
			if(!mysqli_query($conn,$sql)){
				die("Error:-".mysqli_error($conn));
			}
			else {
				header('location:main_displaypursale.php');
			}
		}
	}
	else {
	?>
<script>
var x = alert("This transaction is already approved or used, \n kindly check the transaction");
if(x == true){
	window.location.href = "main_displaypursale.php";
}
else if(x == false) {
	window.location.href = "main_displaypursale.php";
}
else {
	window.location.href = "main_displaypursale.php";
}
</script>
	<?php
	}
}
else if($_GET['page'] == "activate"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['link_trnum']; } }
	$sql = "UPDATE `customer_sales` SET `active` = '1' WHERE `invoice` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaypursale.php'); }
	
	$sql = "UPDATE `pur_purchase` SET `active` = '1' WHERE `invoice` = '$link_trnum'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaypursale.php'); }
}
else if($_GET['page'] == "pause"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['link_trnum']; } }
	$sql = "UPDATE `customer_sales` SET `active` = '0' WHERE `invoice` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaypursale.php'); }
	
	$sql = "UPDATE `pur_purchase` SET `active` = '0' WHERE `invoice` = '$link_trnum'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaypursale.php'); }
}
else if($_GET['page'] == "authorize"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['link_trnum']; } }
	$sql = "UPDATE `customer_sales` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `invoice` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaypursale.php'); }
	
	$sql = "UPDATE `pur_purchase` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `invoice` = '$link_trnum'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaypursale.php'); }
}
else{
	header('location:main_displaypursale.php');
}
function convert_number_to_words($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    if (!is_numeric($number)) {
        return false;
    }
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }
    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    $string = $fraction = null;
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    return $string;
}
?>