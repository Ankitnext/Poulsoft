<?php
//main_updateauthorization
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "cus_outbalfunction.php";
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['authlog'];
$date = $today = date("Y-m-d");
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$cdetails = $row['cname']." - ".$row['cnum'];
}

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

if($_POST['aut_type'] == "Receipt"){
	$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'receipts' AND  `msg_type` IN ('SMS','WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		if($row['msg_type'] == "SMS"){
			$sms_user = $row['sms_user'];
			$sms_key = $row['sms_key'];
			$sms_msg_key = $row['msg_key'];
			$sms_accusage = $row['sms_accusage'];
			$sms_senderid = $row['sms_senderid'];
			$sms_entityid = $row['sms_entityid'];
			$sms_tempid = $row['sms_tempid'];
		}
		else{ }
	}
}
else if($_POST['aut_type'] == "Sale"){
	$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'sales' AND  `msg_type` IN ('SMS','WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		if($row['msg_type'] == "SMS"){
			$sms_user = $row['sms_user'];
			$sms_key = $row['sms_key'];
			$sms_msg_key = $row['msg_key'];
			$sms_accusage = $row['sms_accusage'];
			$sms_senderid = $row['sms_senderid'];
			$sms_entityid = $row['sms_entityid'];
			$sms_tempid = $row['sms_tempid'];
		}
		else{ }
	}
}
//Latest WhatsApp Configuration
$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'WappKey' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $instance_id = $row['sms_key']; $access_token = $row['msg_key']; $url_id = $row['url_id']; }

$sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){
	$curlopt_url = $row['curlopt_url'];
	$curlopt_returntransfer = $row['curlopt_returntransfer'];
	$curlopt_encoding = $row['curlopt_encoding'];
	$curlopt_maxredirs = $row['curlopt_maxredirs'];
	$curlopt_timeout = $row['curlopt_timeout'];
	$curlopt_followlocation = $row['curlopt_followlocation'];
	$curlopt_http_version = $row['curlopt_http_version'];
	$curlopt_customrequest = $row['curlopt_customrequest'];
}

$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $rct_sms_flag = $row['receipt_sms']; $rct_wapp_flag = $row['receipt_wapp']; $sale_sms_flag = $row['sales_sms']; $sale_wapp_flag = $row['sales_wapp']; } } else { $rct_sms_flag = 0; $rct_wapp_flag = 0; }
			
if($_POST['submittrans'] == "authorizerct"){
	$i = 0; $trno = "";
	foreach($_POST['trnums'] as $trdlt){
		$i = $i + 1; $trans_detail[$i] = $trdlt;
		$tr_dlt = explode("@",$trdlt);
		if($tr_dlt[10] == "Receipt"){
			$sql = "INSERT INTO `authorize` (adate,date,trnum,ccode,dcno,itemcode,quantity,price,amount,finalamt,remarks,trtype,addedemp,addedtime,updatedtime,flag) 
			VALUES ('$today','$tr_dlt[0]','$tr_dlt[1]','$tr_dlt[2]','$tr_dlt[3]','$tr_dlt[4]','$tr_dlt[5]','$tr_dlt[6]','$tr_dlt[7]','$tr_dlt[8]','$tr_dlt[9]','$tr_dlt[10]','$addedemp','$addedtime','$addedtime','1')";
			if(!mysqli_query($conn,$sql)){ die("Error in Query-1 :-".mysqli_error($conn)); }
			else{
				$sms = $wapp = $incr_sms = $incr_whapp = 0;
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $sms = $row['sms']; $wapp = $row['wapp']; } $incr_sms = $sms + 1; $incr_whapp = $wapp + 1;
			
				$cusdet = $customer_name = $customer_mobile = ""; $ftotal = 0; $obdetails = array();
				$cusdet = customer_outbalance($tr_dlt[2]); $obdetails = explode("@",$cusdet);
				$customer_name = $obdetails[0]; $customer_mobile = "91".$obdetails[1];
				$ftotal = $obdetails[2];
				$bamt = 0;
				if($ftotal > $tr_dlt[8]){
					$bamt = $ftotal - $tr_dlt[8];
				}
				else if($ftotal == "0" || $ftotal == "0.00" || $ftotal == ".00" || $ftotal == ""){
					$bamt = "-".$tr_dlt[8];
				}
				else if($ftotal < $tr_dlt[8]){
					$bamt = $ftotal - $tr_dlt[8];
				}
				
				if($rct_sms_flag == 1){
					$sql = "UPDATE `master_generator` SET `sms` = '$incr_sms' WHERE  `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
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
Date: '.date("d.m.Y",strtotime($tr_dlt[0])).'
Paid: Rs. '.number_format_ind($tr_dlt[8]).'/-
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
				
				$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client)
				VALUES ('$sms_code','$tr_dlt[2]','$customer_mobile','$xml_data','$status[1]','RECEIPT','$addedemp','$addedtime','$addedtime','$client')";
				if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); } else{ }
				
				} else{ }
				
				if($rct_wapp_flag == 1){
					$sql = "UPDATE `master_generator` SET `wapp` = '$incr_whapp' WHERE  `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
					if($incr_whapp < 10){ $incr_whapp = '000'.$incr_whapp; } else if($incr_whapp >= 10 && $incr_whapp < 100){ $incr_whapp = '00'.$incr_whapp; } else if($incr_whapp >= 100 && $incr_whapp < 1000){ $incr_whapp = '0'.$incr_whapp; } else { }
					$wapp_code = "WAPP-".$incr_whapp;
					
					$message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($tr_dlt[0])).",%0D%0APaid: Rs. ".number_format_ind($tr_dlt[8])."/-%0D%0ABalance: Rs. ".number_format_ind($bamt)."/-%0D%0AThank You,%0D%0A".$cdetails;
					$message = str_replace(" ","+",$message);

					
					$mobile = $customer_mobile; $type = "text";
					$xml_data = $curlopt_url.'number='.$mobile.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token;
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => $curlopt_url.'number='.$mobile.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token,
					CURLOPT_RETURNTRANSFER => $curlopt_returntransfer,
					CURLOPT_ENCODING => $curlopt_encoding,
					CURLOPT_MAXREDIRS => $curlopt_maxredirs,
					CURLOPT_TIMEOUT => $curlopt_timeout,
					CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
					CURLOPT_HTTP_VERSION => $curlopt_http_version,
					CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
					));
					$response = curl_exec($curl);
					curl_close($curl);
					$d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);
					if($response != ""){
						$wsfile_path = $_SERVER['REQUEST_URI'];
						$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime,client)
						VALUES ('$wapp_code','$tr_dlt[2]','$mobile','$xml_data','$d3[1]','$response','BB-ReceiptAut','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
						if(!mysqli_query($conn,$sql)) { } else{  }
					}
				} else{ }
				
				$sql = "UPDATE `customer_receipts` SET `active` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` IN ('$tr_dlt[1]') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0'";
				if(!mysqli_query($conn,$sql)){ die("Error in Query-1 :-".mysqli_error($conn)); } else{ }
			}
		}
		else if($tr_dlt[10] == "Sale"){
			$sql = "INSERT INTO `authorize` (adate,date,trnum,ccode,dcno,itemcode,quantity,price,amount,finalamt,remarks,trtype,addedemp,addedtime,updatedtime,flag) 
			VALUES ('$today','$tr_dlt[0]','$tr_dlt[1]','$tr_dlt[2]','$tr_dlt[3]','$tr_dlt[4]','$tr_dlt[5]','$tr_dlt[6]','$tr_dlt[7]','$tr_dlt[8]','$tr_dlt[9]','$tr_dlt[10]','$addedemp','$addedtime','$addedtime','1')";
			if(!mysqli_query($conn,$sql)){ die("Error in Query-1 :-".mysqli_error($conn)); }
			else{
				$sms = $wapp = $incr_sms = $incr_whapp = 0;
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $sms = $row['sms']; $wapp = $row['wapp']; } $incr_sms = $sms + 1; $incr_whapp = $wapp + 1;
			
				$cusdet = $customer_name = $customer_mobile = ""; $ftotal = 0; $obdetails = array();
				$cusdet = customer_outbalance($tr_dlt[2]); $obdetails = explode("@",$cusdet);
				$customer_name = $obdetails[0]; $customer_mobile = "91".$obdetails[1];
				$ftotal = $obdetails[2];
				$bamt = 0;
				
				if($ftotal > $tr_dlt[8]){
					$bamt = $ftotal + $tr_dlt[8];
				}
				else if($ftotal == "0" || $ftotal == "0.00" || $ftotal == ".00" || $ftotal == ""){
					$bamt = "-".$tr_dlt[8];
				}
				else if($ftotal < $tr_dlt[8]){
					$bamt = $ftotal - $tr_dlt[8];
				}
				$xml_data = $message = $item_dlt = $item_dlt2 = "";
				if($sale_sms_flag == 1 || $sale_wapp_flag == 1){
					$sqls = "SELECT * FROM `customer_sales` WHERE `invoice` = '$tr_dlt[1]' ORDER BY `id` ASC"; $querys = mysqli_query($conn,$sqls);
					while($rows = mysqli_fetch_assoc($querys)){
						if($item_dlt == ""){
							if($rows['birds'] != ""){ $item_birds = $rows['birds']."No. "; } else{ $item_birds = ""; }
							$item_dlt = $item_name[$rows['itemcode']].": ".$item_birds."".$rows['netweight']."Kgs @ ".$rows['itemprice'];
						}
						else{
							if($rows['birds'] != ""){ $item_birds = $rows['birds']."No. "; } else{ $item_birds = ""; }
							$item_dlt = $item_dlt.", ".$item_name[$rows['itemcode']].": ".$item_birds."".$rows['netweight']."Kgs @ ".$rows['itemprice'];
						}
						if($item_dlt2 == ""){
							if($rows['birds'] != ""){ $item_birds = $rows['birds']."No. "; } else{ $item_birds = ""; }
							$item_dlt2 = $item_name[$rows['itemcode']].": ".$item_birds."".$rows['netweight']."Kgs @ ".$rows['itemprice']."%0D%0A";
						}
						else{
							if($rows['birds'] != ""){ $item_birds = $rows['birds']."No. "; } else{ $item_birds = ""; }
							$item_dlt2 = $item_dlt2.", ".$item_name[$rows['itemcode']].": ".$item_birds."".$rows['netweight']."Kgs @ ".$rows['itemprice']."%0D%0A";
						}
					}
				}
				if($sale_sms_flag == 1){
					$sql = "UPDATE `master_generator` SET `sms` = '$incr_sms' WHERE  `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
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
Date: '.date("d.m.Y",strtotime($tr_dlt[0])).',
'.$item_dlt.',
Sale Amt: Rs. '.$tr_dlt[8].'/-
Balance: Rs. '.$bamt.'/-
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
				
				$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client)
				VALUES ('$sms_code','$tr_dlt[2]','$customer_mobile','$xml_data','$status[1]','SALE','$addedemp','$addedtime','$addedtime','$client')";
				if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); } else{ }
				
				} else{ }
				
				if($sale_wapp_flag == 1){
					$sql = "UPDATE `master_generator` SET `wapp` = '$incr_whapp' WHERE  `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
					if($incr_whapp < 10){ $incr_whapp = '000'.$incr_whapp; } else if($incr_whapp >= 10 && $incr_whapp < 100){ $incr_whapp = '00'.$incr_whapp; } else if($incr_whapp >= 100 && $incr_whapp < 1000){ $incr_whapp = '0'.$incr_whapp; } else { }
					$wapp_code = "WAPP-".$incr_whapp;
					
					$message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($tr_dlt[0])).",%0D%0A".$item_dlt2.",%0D%0ASale Amt: ".$tr_dlt[8]."/-%0D%0ABalance: Rs. ".$bamt."/-%0D%0AThank You,%0D%0A".$cdetails;
            		$message = str_replace(" ","+",$message);

					
					$mobile = $customer_mobile; $type = "text";
					$xml_data = $curlopt_url.'number='.$mobile.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token;
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => $curlopt_url.'number='.$mobile.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token,
					CURLOPT_RETURNTRANSFER => $curlopt_returntransfer,
					CURLOPT_ENCODING => $curlopt_encoding,
					CURLOPT_MAXREDIRS => $curlopt_maxredirs,
					CURLOPT_TIMEOUT => $curlopt_timeout,
					CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
					CURLOPT_HTTP_VERSION => $curlopt_http_version,
					CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
					));
					$response = curl_exec($curl);
					curl_close($curl);
					$d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);
					if($response != ""){
						$wsfile_path = $_SERVER['REQUEST_URI'];
						$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime,client)
						VALUES ('$wapp_code','$tr_dlt[2]','$mobile','$xml_data','$d3[1]','$response','BB-SaleAut','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
						if(!mysqli_query($conn,$sql)) { } else{  }
					}
				} else{ }
				
				$sql = "UPDATE `customer_sales` SET `active` = '1',`updatedemp` = '$addedemp',`updated` = '$addedtime' WHERE `invoice` IN ('$tr_dlt[1]') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0'";
				if(!mysqli_query($conn,$sql)){ die("Error in Query-1 :-".mysqli_error($conn)); } else{ }
			}
		}
	}
	/*$sql = "UPDATE `customer_receipts` SET `active` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` IN ('$trno') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0'";
	if(!mysqli_query($conn,$sql)){ die("Error in Query-1 :-".mysqli_error($conn)); }
	else{ header('location:main_displayauthorization.php?cid='.$cid); }*/
	
	header('location:main_displayauthorization.php?cid='.$cid);
}
?>