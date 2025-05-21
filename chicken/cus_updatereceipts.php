<?php
//pur_updatepayments.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "chicken_send_wapp_master2.php";
include "cus_outbalfunction.php";
include "NotificationSending_ct_web.php";

$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("whatsapp_keygenerate_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.whatsapp_keygenerate_master LIKE poulso6_admin_chickenmaster.whatsapp_keygenerate_master;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `master_itemfields`'; $query = mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("receipt_notification", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `receipt_notification` INT(11) NULL DEFAULT '0' COMMENT ''"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `sms_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("file_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `sms_details` ADD `file_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'File Name' AFTER `smsto`"; mysqli_query($conn,$sql); }

//Check and Insert WhatsApp Details
$file_name = "cus_displayreceipts.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
$sql = "SELECT * FROM `whatsapp_keygenerate_master` WHERE `file_type` = 'Receipt' AND `file_name` = '$file_name' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $w_cnt = mysqli_num_rows($query);
if($w_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $sms_type = $row['sms_type']; $wapp_ptrn = $row['pattern']; } }

//Fetch Company Details
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }

//Fetch CoA Details
$sql = "SELECT * FROM `acc_coa`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_name[$row['code']] = $row['description']; }

if($_POST['submittrans'] == "addpage"){
	foreach($_POST['pname'] as $pname){ $pnames[] = $pname; } $spnames = sizeof($pnames);
	foreach($_POST['pdate'] as $pdate){ $pdates[] = date("Y-m-d",strtotime($pdate)); $date = date("Y-m-d",strtotime($pdate)); }
	foreach($_POST['mode'] as $mode){ $modes[] = $mode; }
	foreach($_POST['code'] as $code){ $codes[] = $code; }
	foreach($_POST['amount'] as $amount){ $amounts[] = $amount; }
	foreach($_POST['gtamtinwords'] as $amtinword){ $amtinwords[] = $amtinword; }
	foreach($_POST['dcno'] as $dcno){ $dcnos[] = $dcno; }
	foreach($_POST['sector'] as $sector){ $sectors[] = $sector; }
	foreach($_POST['remark'] as $remark){ $remarks[] = $remark; }
	//$gamts = $_POST['gtamt'];

	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $receipts = $row['receipts']; $sms = $row['sms']; } $incr = $receipts + $spnames; $sms_incr = $sms + $spnames;
	
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $rct_sms_flag = $row['receipt_sms']; $rct_wapp_flag = $row['receipt_wapp'];$receipt_notification_flag = $row['receipt_notification']; } } else { $rct_sms_flag = 0; $rct_wapp_flag = 0; }
	if($rct_sms_flag == 1){ $upt_sms_ct = ",`sms` = '$sms_incr'"; } else{ $upt_sms_ct = ""; }
	$sql = "UPDATE `master_generator` SET `receipts` = '$incr'".$upt_sms_ct." WHERE  `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	$incr = $receipts + 1;
	$incr_sms = $sms + 1;
	
	for($i = 0;$i < $spnames; $i++){
		$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$pdates[$i]' AND `tdate` >= '$pdates[$i]' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
		if($rct_sms_flag == 1){
			if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
			$sms_code = "SMS-".$incr_sms;
		}
		else{ $sms_code = $incr_sms = NULL; }
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$code = "R-".$fprefix."".$incr;
		
		$cusdet = $obdetails = $customer_name = $customer_mobile = $cus_mob = ""; $ftotal = 0;
		$cusdet = customer_outbalance($pnames[$i]); $obdetails = explode("@",$cusdet);
		$customer_name = $obdetails[0]; $customer_mobile = "91".$obdetails[1];$customer_mobile1 = $obdetails[1];
		
		$ftotal = $obdetails[2]; $bamt = 0;
		if((float)$ftotal > (float)$amounts[$i]){
			$bamt = round(((float)$ftotal - (float)$amounts[$i]),2);
		}
		else if($ftotal == "0" || $ftotal == "0.00" || $ftotal == ".00" || $ftotal == ""){
			$bamt = "-".round((float)$amounts[$i],2);
		}
		else if((float)$ftotal < (float)$amounts[$i]){
			$bamt = round(((float)$ftotal - (float)$amounts[$i]),2);
		}
		else if(number_format_ind($ftotal) == number_format_ind($amounts[$i])){
			$bamt = 0;
		}
		else{
			$bamt = 0;
		}

		$sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,date,ccode,docno,mode,method,amount,amtinwords,vtype,warehouse,remarks,sms_sent,whapp_sent,flag,active,addedemp,addedtime,tdflag,pdflag,client)
		VALUES ('$incr','R','$code','$pdates[$i]','$pnames[$i]','$dcnos[$i]','$modes[$i]','$codes[$i]','$amounts[$i]','$amtinwords[$i]','C','$sectors[$i]','$remarks[$i]','$sms_code','$wapp_code','0','1','$addedemp','$addedtime','0','0','$client')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		$date_sms = date("d.m.Y",strtotime($pdates[$i]));
		if($rct_sms_flag == 1){
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
				//foreach($mob_list as $mlist){
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
					if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); }
				}
			}
		//}
		else { }
		if($rct_wapp_flag == 1){
			if(!$conn){ }
			else{
				if($wapp_ptrn == "Template"){
					$msg1 = array("dear"=>$customer_name, "date"=>$date_sms, "paid"=>number_format_ind($amounts[$i])."/-", "mode"=>$coa_name[$codes[$i]], "balance"=>number_format_ind(round((float)$bamt,2))."/-", "cdetails"=>$cdetails);
					$message = json_encode($msg1);
				}
				else{
					$message = "Dear: ".$customer_name."%0D%0ADate: ".$date_sms.",%0D%0APaid: Rs. ".number_format_ind($amounts[$i])."/-%0D%0ABalance: Rs. ".number_format_ind(round((float)$bamt,2))."/-%0D%0AThank You,%0D%0A".$cdetails;
					$message = str_replace(" ","+",$message);
				}
				
				$wapp_date = date("Y-m-d");
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
				$query = mysqli_query($conn,$sql); $wapp = 0; while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $wincr = $wapp + 1;
				$sql = "UPDATE `master_generator` SET `wapp` = '$wincr' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				if($wincr < 10){ $wincr = '000'.$wincr; } else if($wincr >= 10 && $wincr < 100){ $wincr = '00'.$wincr; } else if($wincr >= 100 && $wincr < 1000){ $wincr = '0'.$wincr; } else { }
				
				$database = $_SESSION['dbase'];
				$wapp_type = "Receipt Message";
				$trnum = $code;
				$ccode = $pnames[$i];
				$number = $customer_mobile;
				$wapp_code = "WAPP-".$wincr;
				$msg_type = "WAPP";
				$msg_project = "CTS";
				$status = "CREATED";
				$wapp_link = $_SERVER['REQUEST_URI'];
				$wapp_msg = $message;
				$send_type = "text";
				chicken_send_wapp_text($database,$wapp_type,$trnum,$ccode,$number,$wapp_code,$sms_type,$msg_type,$msg_project,$status,$wapp_link,$wapp_msg,$send_type,$wapp_ptrn);
			}
		}
		else{ }
		
		if($receipt_notification_flag == 1){
			$db = $_SESSION['dbase'];
			$sql = "SELECT * FROM `firebase_device_details` where db = '$db' and mobile = '$customer_mobile1'";
			$q3=mysqli_query($conns,$sql);$fb_count = mysqli_num_rows($q3);
			if($fb_count > 0){
				$row = mysqli_fetch_assoc($q3);
				send_notification("Payment","Payment Confirmation From ".$company_name,$row['device_token']);
				save_notification($customer_mobile1,$code,"cus_updatereceipts.php","Payment","Payment Confirmation From ".$company_name." (".$code.")",$message);
			}
		}
		$incr++;
		$incr_sms++;
		$incr_whapp++;
	}
	if($i == $spnames){
		//header('location:cus_displayreceipts.php');
		?>
		<script>
			var x = confirm("Would you like to save more entries?");
			if(x == true){
				window.location.href = "cus_addreceipt.php";
			}
			else if(x == false){
				window.location.href = "cus_displayreceipts.php";
			}
		</script>
		<?php
	}
	else {
		echo "Error:-".mysqli_error($conn);
	}
}
else if($_POST['submittrans'] == "updatepage"){
	$pnames = $_POST['pname']; $pdates = date("Y-m-d",strtotime($_POST['pdate'])); $modes = $_POST['mode']; $codes = $_POST['code'];
	$amounts = $_POST['amount']; $amtinwords = $_POST['gtamtinwords']; $dcnos = $_POST['dcno']; $sectors = $_POST['sector']; $trnums = $_POST['trnum']; $remarks = $_POST['remark'];
	$sql = "UPDATE `customer_receipts` SET `date` = '$pdates',`ccode` = '$pnames',`docno` = '$dcnos',`mode` = '$modes',`method` = '$codes',`amount` = '$amounts',`amtinwords` = '$amtinwords',`vtype` = 'C',`warehouse` = '$sectors',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnums'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {
		//header('location:cus_displayreceipts.php');
		?>
		<script>
			var x = confirm("Would you like to save more entries?");
			if(x == true){
				window.location.href = "cus_addreceipt.php";
			}
			else if(x == false){
				window.location.href = "cus_displayreceipts.php";
			}
		</script>
		<?php
		}
	
}
else {
	$id = $_GET['id'];
	$updatetype = $_GET['page'];
	
	if($updatetype == "edit"){ header('location:cus_editreceipts.php?id='.$id); }
	
	else if($updatetype == "delete"){
		$sql ="SELECT * FROM `customer_receipts` WHERE `trnum` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){
				$type = "Sales Receipt";
				$date = $row['date'];
				$transactionno = $row['trnum'];
				$description = $row['itemcode']; if($description == "" || $description == NULL){ $description = 0; }
				$doccode = $row['docno']; if($doccode == "" || $doccode == NULL){ $doccode = 0; }
				$pcode = $row['ccode'];
				$icode = $row['method']; if($icode == "" || $icode == NULL){ $icode = 0; }
				$quantity = "0.00000";
				$amount = $row['amount'];
				$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
				VALUES('$type','$date','$transactionno','$description','$doccode','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
			$sql = "DELETE FROM `customer_receipts` WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){
				die("Error:-".mysqli_error($conn));
			}
			else {
				header('location:cus_displayreceipts.php');
			}
		}
		else {
		?>
			<script>
			var x = alert("This transaction is already approved or used, \n kindly check the transaction");
			if(x == true){
				window.location.href = "cus_displayreceipts.php";
			}
			else if(x == false) {
				window.location.href = "cus_displayreceipts.php";
			}
			else {
				window.location.href = "cus_displayreceipts.php";
			}
			</script>
		<?php
		}
	}
	else if($updatetype == "activate"){
		$id = $_GET['id'];
		$sql = "UPDATE `customer_receipts` SET `active` = '1' WHERE `trnum` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_displayreceipts.php'); }
	}
	else if($updatetype == "pause"){
		$id = $_GET['id'];
		$sql = "UPDATE `customer_receipts` SET `active` = '0' WHERE `trnum` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_displayreceipts.php'); }
	}
	else if($updatetype == "authorize"){
		$id = $_GET['id'];
		$sql = "UPDATE `customer_receipts` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `trnum` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_displayreceipts.php'); }
	}
	else {}
}
?>