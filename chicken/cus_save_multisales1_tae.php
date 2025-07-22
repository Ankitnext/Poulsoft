<?php
//cus_save_multiplesale1_tae.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "cus_outbalfunction.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_send_wapp_master2.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("whatsapp_keygenerate_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.whatsapp_keygenerate_master LIKE poulso6_admin_chickenmaster.whatsapp_keygenerate_master;"; mysqli_query($conn,$sql1); }
if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `sms_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("file_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `sms_details` ADD `file_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'File Name' AFTER `smsto`"; mysqli_query($conn,$sql); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_crdrnote`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_crdrnote` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `trnum`"; mysqli_query($conn,$sql); }

$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Multiple Sales' AND `field_function` LIKE 'Provide Transportation Charges as a crdr note' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $tcdr_flag = mysqli_num_rows($query);

//Check and Insert WhatsApp Details
$file_name = "cus_displaymultisales.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
$sql = "SELECT * FROM `whatsapp_keygenerate_master` WHERE `file_type` = 'Multiple Sale' AND `file_name` = '$file_name' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $w_cnt = mysqli_num_rows($query);
if($w_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $sms_type = $row['sms_type']; $wapp_ptrn = $row['pattern']; } }

//Change WhatsApp Format-1
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'cus_displaymultisales.php' AND `field_function` LIKE 'User Specific WhatsApp Format-1' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $wapp_nfmt1 = mysqli_num_rows($query);

//Fetch Company Details
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }

//Check for Column Availability
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sup_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `sup_code` VARCHAR(300) NULL DEFAULT NULL AFTER `customercode`"; mysqli_query($conn,$sql); }
if(in_array("description", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `description` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Manual Name' AFTER `sup_code`"; mysqli_query($conn,$sql); }
if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }

$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Authorization' AND `field_function` = 'cus_add_multisales1.php' AND `user_access` = 'all'";
$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $aut_flag = 0;
if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $aut_flag = $row1['flag']; } }
else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','cus_add_multisales1.php','all','0');"; mysqli_query($conn,$sql1); }
if((int)$aut_flag == 1){ $active = 0; } else{ $active = 1; }

//COA check
$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Transportation Charges' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coacode = $row['code']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Sales WhatsApp' AND `field_function` = 'WhatsApp Message Format' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $col_val1 = ""; $new_wflag = $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
if($count > 0){ while($row = mysqli_fetch_assoc($query)){ $new_wflag = $row['flag']; $col_val1 = $row['field_value']; } }
if($col_val1 == ""){ $col_val1 = "1,1,1,1"; } if($new_wflag == ""){ $new_wflag = 0; }
$cval1 = array(); $cval1 = explode(",",$col_val1);
if((int)$cval1[0] == 1){ $jals_flag = 1; } if((int)$cval1[1] == 1){ $birds_flag = 1; } if((int)$cval1[2] == 1){ $tweight_flag = 1; } if((int)$cval1[3] == 1){ $eweight_flag = 1; }


$trtype = "multiplesales-1";
$trlink = "cus_displaymultisales.php";

$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Add Multiple Sales' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_flag = $row['vehicle_flag']; $vehicle_row_flag = $row['vehicle_row_flag']; }
if($vehicle_flag == "" || $vehicle_flag == 0){ $vehicle_flag = 0; }
if($vehicle_row_flag == "" || $vehicle_row_flag == 0){ $vehicle_row_flag = 0; }

if($_POST['submittrans'] == "addpage"){
	$date = date("Y-m-d",strtotime($_POST['pdate']));
	$sup_code = $_POST['sup_code'];
	$sup_mnu_name = $_POST['sup_mnu_name'];
	$warehouse = $_POST['wcodes'];
	$bookinvoice = $_POST['binv'];
	if($vehicle_flag == 1){ $vehicle_code = $_POST['vehicleno']; } else{ $vehicle_code = ""; }
	$finaltotal = array();
	//tcost
  	$i = 0; foreach($_POST['tcost'] as $tcosts){ $i = $i + 1; $tcost[$i] = $tcosts; }
	$i = 0; foreach($_POST['cnames'] as $cnames){ $i = $i + 1; $cus_dt1 = explode("@",$cnames); $vendorcode[$i] = $cus_code[$i] = $cus_dt1[0]; $cus_names[$i] = $cus_dt1[1]; }
	$i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
	$i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; }
	$i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }
	$i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; }
	$i = 0; foreach($_POST['ewval'] as $eweights){ $i = $i + 1; $emptyweight[$i] = $eweights; }
	$i = 0; foreach($_POST['nwval'] as $nweights){ $i = $i + 1; $netweight[$i] = $nweights; }
	$i = 0; foreach($_POST['iprice'] as $iprices){ $i = $i + 1; $itemprice[$i] = $iprices; }
	$i = 0; foreach($_POST['tamt'] as $tamts){ $i = $i + 1; $totalamt[$i] = $tamts; $finaltotal[$i] = round($tamts); }
	if($vehicle_row_flag == 1){ $i = 0; foreach($_POST['vehiclerno'] as $vnor){ $i = $i + 1; $vehiclerno[$i] = $vnor; } }
	$i = 0; foreach($_POST['narr'] as $narr){ $i = $i + 1; $remarks[$i] = $narr; }
	
	$d = date("d",strtotime($date));
	$m = date("m",strtotime($date));
	$y = date("Y",strtotime($date));
	$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ 
		while($row = mysqli_fetch_assoc($query)){
			$sales_sms_flag = $row['sales_sms'];
			$sales_wapp_flag = $row['sales_wapp'];
		}
	}
	else {
		$sales_sms_flag = 0;
		$sales_wapp_flag = 0;
	}
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; $sms = $row['sms']; } $incr = $sales + $i; $incr_sms = $sms + $i;
	if($sales_sms_flag == 1){
		$sales_incr = ",`sms` = '$incr_sms'";
	}
	else{ $sales_incr = ""; }
	$sql = "UPDATE `master_generator` SET `sales` = '$incr'".$sales_incr." WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

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
	$incr = $sales; $incr_sms = $sms; $item_birds = "";
	for($j = 1;$j <= $i;$j++){
		if($warehouse == "select" || $itemcode[$j] == "select") {
		}
		else {
			$cusdet = $customer_name = ""; $ftotal = 0; $obdetails = array();
			$cusdet = customer_outbalance($cus_code[$j]); $obdetails = explode("@",$cusdet);
			$customer_name = $obdetails[0]; $mob_alist = array(); $mob_alist =  explode(",",$obdetails[1]);
			$ftotal = $obdetails[2]; $bals = 0;

			$out_amt = $ftotal; $totalamount = number_format_ind($finaltotal[$j]); $bals = $out_amt + $finaltotal[$j]; $bal = number_format_ind($bals);
			$item_dlt = "";
			if($item_dlt == ""){
				if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
				$item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
			}
			else{
				if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
				$item_dlt = $item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
			}
			if($sales_sms_flag == 1){
				$incr_sms = $incr_sms + 1;
				if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
				$sms_code = "SMS-".$incr_sms;
			}
			else{
				$sms_code = NULL;
			}
			
			$incr = $incr + 1;
			if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
			$invoice = "S".$pfx."-".$incr;
			
			$roundoff = "";
			$roundoff = $finaltotal[$j] - $totalamt[$j];
			if($jals[$j] == "" || $jals[$j] == NULL){ $jals[$j] = 0; }
			if($birds[$j] == "" || $birds[$j] == NULL || $birds[$j] == 'NaN'){ $birds[$j] = 0; }
			if($totalweight[$j] == "" || $totalweight[$j] == NULL){ $totalweight[$j] = 0; }
			if($emptyweight[$j] == "" || $emptyweight[$j] == NULL){ $emptyweight[$j] = 0; }
			if($netweight[$j] == "" || $netweight[$j] == NULL){ $netweight[$j] = 0; }
			if($itemprice[$j] == "" || $itemprice[$j] == NULL){ $itemprice[$j] = 0; }
			if($vehicle_row_flag == 1){ $vehiclecode = $vehiclerno[$j]; }
			else if($vehicle_flag == 1){ $vehiclecode = $vehicle_code; }
			else{ $vehiclecode = ""; }

			$sql = "INSERT INTO `customer_sales` (date,incr,d,m,y,fy,invoice,bookinvoice,customercode,sup_code,`description`,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcdsamt,roundoff,finaltotal,balance,warehouse,flag,authorization,active,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,sms_sent,addedemp,addedtime,client,trtype,trlink) 
			VALUES ('$date','$incr','$d','$m','$y','$pfx','$invoice','$bookinvoice','$vendorcode[$j]','$sup_code','$sup_mnu_name','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$itemprice[$j]','$totalamt[$j]','0.00','0.00','$roundoff','$finaltotal[$j]','$finaltotal[$j]','$warehouse','0','0','$active','0','0','','$vehiclecode','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','$remarks[$j]','$sms_code','$addedemp','$addedtime','$client','$trtype','$trlink')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
			else{
				if($tcost[$j] == ""){ $tcost[$j] = 0; }
				if((int)$tcdr_flag > 0 && $tcost[$j] > 0){
				   $cincr = 0;
				   $sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
				   while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
   
				   $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				   while($row = mysqli_fetch_assoc($query)){ $dincr = $row['cusdebit']; } $cincr = (int)$dincr + 1;

				   $sql = "UPDATE `master_generator` SET `cusdebit` = '$cincr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				   if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				   if($cincr < 10){ $cincr = '000'.$cincr; } else if($cincr >= 10 && $cincr < 100){ $cincr = '00'.$cincr; } else if($cincr >= 100 && $cincr < 1000){ $cincr = '0'.$cincr; } else { }
				   $code = "CDN"."-".$fprefix."".$cincr;
   
				   $sql2 = "INSERT INTO `main_crdrnote`(`mode`,`trnum`,`link_trnum`,`date`,`ccode`,`coa`,`crdr`,`amount`,`balance`,`vtype`,`flag`,`active`,`addedemp`,`addedtime`) 
				   VALUES('CDN','$code','$invoice','$date','$vendorcode[$j]','$coacode','Cr','$tcost[$j]','$tcost[$j]','C','0','1','$addedemp','$addedtime')";
				   mysqli_query($conn,$sql2);
			   }

				if($sales_sms_flag == 1 && $active == 1){
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
						VALUES ('$sms_code','$vendorcode[$j]','$out_mob','$xml_data','$status[1]','SALES','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
						if(!mysqli_query($conn,$sql)) { } else{ }
						}
                    }
				}
				if($sales_wapp_flag == 1 && $active == 1){
                    if(!$conn){ }
                    else{
						if($wapp_ptrn == "Template"){
							$msg1 = array("dear"=>$customer_name, "date"=>date("d.m.Y",strtotime($date)), "item_dt1"=>$item_dlt."/-", "samount"=>$totalamount, "balance"=>$bal."/-", "cdetails"=>$cdetails);
							$message = json_encode($msg1);
						}
						else{
							if((int)$wapp_nfmt1 > 0){
								$item_dlt = "";
								if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
								$item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."%0D%0AWeight: ".$netweight[$j]."Kgs @ Rs/-: ".$itemprice[$j];
								$old_bamt = (float)$out_amt;
								
								$message = "*Sale Details*%0D%0ADear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0AOld Balance: ".number_format_ind($old_bamt).",%0D%0ASale Amt: ".$totalamount."/-,%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
								$message = str_replace(" ","+",$message);
							}
							else{
								if((float)$new_wflag > 0){
									$item_dlt = "";
									$item_dlt = $item_name[$itemcode[$j]];
									
									if((int)$jals_flag == 1){
										$item_dlt .= "%0D%0AJals: ".str_replace(".00","",number_format_ind($jals[$j]));
									}
									if((int)$birds_flag == 1){
										$item_dlt .= "%0D%0ABirds: ".str_replace(".00","",number_format_ind($birds[$j]));
									}
									if((int)$tweight_flag == 1){
										$item_dlt .= "%0D%0AGross Wt: ".str_replace(".00","",number_format_ind($totalweight[$j]));
									}
									if((int)$eweight_flag == 1){
										$item_dlt .= "%0D%0AEmpty Wt: ".str_replace(".00","",number_format_ind($emptyweight[$j]));
									}
									$item_dlt .= "%0D%0ANet Wt: ".number_format_ind($netweight[$j]);
									$item_dlt .= "%0D%0APrice: ".number_format_ind($itemprice[$j]);
									$item_dlt .= "%0D%0ATotal Amount: ".number_format_ind($totalamt[$j]);
									$item_dlt .= "%0D%0A";
								}

								$message = "*Sale Details*%0D%0ADear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt."%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
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
							$wapp_type = "Invoice Message";
							$trnum = $invoice;
							$ccode = $vendorcode[$j];
							$number = $out_mob;
							$wapp_code = "WAPP-".$wincr;
							$msg_type = "WAPP";
							$msg_project = "CTS";
							$status = "CREATED";
							$wapp_link = $_SERVER['REQUEST_URI'];
							$wapp_msg = $message;
							$send_type = "text";
							chicken_send_wapp_text($database,$wapp_type,$trnum,$ccode,$number,$wapp_code,$sms_type,$msg_type,$msg_project,$status,$wapp_link,$wapp_msg,$send_type,$wapp_ptrn);
						}
						/*
						if($_SERVER['REMOTE_ADDR'] == "49.207.219.90"){
							echo "chicken_send_wapp_text($database,$trtype,$trnum,$ccode,$number,$wapp_code,$sms_type,$msg_type,$msg_project,$status,$trlink,$wapp_msg,$send_type);";
						}*/
                    }
				}
			}
		}
	}
	//if($_SERVER['REMOTE_ADDR'] == "49.207.219.90"){ }
	//else{
	?>
	<script>
		var x = confirm("Would you like to save more entries?");
		if(x == true){
			window.location.href = "cus_add_multiplesale1_tae.php";
		}
		else if(x == false){
			window.location.href = "cus_displaymultisales.php?cid=P2-C10";
		}
	</script>
	<?php
	//}
}