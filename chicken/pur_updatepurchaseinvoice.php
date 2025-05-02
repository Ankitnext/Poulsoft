<?php
//pur_updatepurchaseinvoice.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	include "number_format_ind.php";
	include "pur_outbalfunction.php";
	$addedtime = date('Y-m-d H:i:s');

	//Fetch Column From CoA Table
	$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("transporter_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `transporter_code` VARCHAR(300) NULL DEFAULT NULL AFTER `tcdsamt`"; mysqli_query($conn,$sql); }
	if(in_array("freight_amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `freight_amount` DECIMAL(20,2) NOT NULL DEFAULT '0' AFTER `transporter_code`"; mysqli_query($conn,$sql); }
	if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
	if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }

	$trtype = "purchase-1";
	$trlink = "pur_displaypurchases.php";
	if($_POST['submittrans'] == "addpage"){
		$date = date("Y-m-d",strtotime($_POST['pdate']));
		$d = date("d",strtotime($date));
		$m = date("m",strtotime($date));
		$y = date("Y",strtotime($date));
		$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
		$vendorcode = $_POST['pname'];
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
		$sql = "UPDATE `main_contactdetails` SET `flag` = '1' WHERE `code` = '$vendorcode'"; mysqli_query($conn,$sql);
		$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $purchases = $row['purchases']; } $incr = $purchases + 1;
		//$sql = "UPDATE `master_generator` SET `purchases` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
		//if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$code = "P".$pfx."-".$incr;
		$invoice = $code;
		$bookinvoice = $_POST['binv'];
		$finaltotal = round($_POST['gtamt']);
		if($_POST['tdsperval'] == "" || $_POST['tdsperval'] == NULL || $_POST['tdsperval'] == "0"){
			$tcdsper = $tcdsamt = "0.00";
		}
		else{
			$tcdsper = $_POST['tdsperval'];
		}
		if($_POST['tdsamt'] == "" || $_POST['tdsamt'] == NULL || $_POST['tdsamt'] == "0"){
			$tcdsper = $tcdsamt = "0.00";
		}
		else{
			$tcdsamt = $_POST['tdsamt'];
		}
		$drivercode = $_POST['dname'];
		$vehiclecode = $_POST['vno'];
		$amtinwords = $_POST['gtamtinwords'];

		$transporter_code = $_POST['transporter_code'];
		$freight_amount = $_POST['freight_amount']; if($freight_amount == ""){ $freight_amount = 0; }

		$discounttype = $taxtype = $taxcode = $discountcode = "Amt";
		$flag = $authorization = $tdflag = $pdflag = 0; $itemprice = $totalamt = array();
		$remarks = $_POST['narr'];
		$i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
		$i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; }
		$i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }
		$i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; }
		$i = 0; foreach($_POST['ewval'] as $eweights){ $i = $i + 1; $emptyweight[$i] = $eweights; }
		$i = 0; foreach($_POST['nwval'] as $nweights){ $i = $i + 1; $netweight[$i] = $nweights; }
		$i = 0; foreach($_POST['iprice'] as $iprices){ $i = $i + 1; $itemprice[$i] = $iprices; }
		$i = 0; foreach($_POST['idisc'] as $idiscs){ $i = $i + 1; $discountvalue[$i] = $discountamt[$i] = $idiscs; }
		$i = 0; foreach($_POST['itax'] as $itaxs){ $i = $i + 1; $taxvalue[$i] = $taxamount[$i] = $itaxs; }
		$i = 0; foreach($_POST['tamt'] as $tamts){ $i = $i + 1; $totalamt[$i] = $tamts; }
		$i = 0; foreach($_POST['wcodes'] as $whouses){ $i = $i + 1; $warehouse[$i] = $whouses; }
		$roffsize = sizeof($totalamt); $rtamt = 0; for($k = 1;$k <= $roffsize;$k++){ $rtamt = $rtamt + $totalamt[$k]; } $rtamt = $rtamt + $tcdsamt;
		if($rtamt >= $finaltotal){ $roundoff = $rtamt - $finaltotal; }
		else { $roundoff = $finaltotal - $rtamt; }
		
		//SMS and WhatsApp Check
		$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ 
			while($row = mysqli_fetch_assoc($query)){
				$purchase_sms_flag = $row['purchase_sms'];
				$purchase_wapp_flag = $row['purchase_wapp'];
			}
		}
		else {
			$purchase_sms_flag = 0;
			$purchase_wapp_flag = 0;
		}
		if($purchase_sms_flag == 1 || $purchase_wapp_flag == 1){
			$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $sms = $row['sms']; $wapp = $row['wapp']; } $incr_sms = $sms + 1; $incr_wapp = $wapp + 1;
			
			if($purchase_sms_flag == 1){ $pursms_incr = ",`sms` = '$incr_sms'"; } else{ $pursms_incr = ""; }
			if($purchase_wapp_flag == 1){ $purwapp_incr = ",`wapp` = '$incr_wapp'"; } else{ $purwapp_incr = ""; }
			
			$purdet = supplier_outbalance($vendorcode); $obdetails = explode("@",$purdet);
			$supplier_name = $obdetails[0]; $supplier_mobile = "91".$obdetails[1];
			$ftotal = $obdetails[2];
			
			$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
			$out_amt = $ftotal; $totalamount = number_format_ind($finaltotal); $bals = $out_amt + $finaltotal; $bal = number_format_ind($bals);
			
		}
		$sql = "UPDATE `master_generator` SET `purchases` = '$incr'".$pursms_incr."".$purwapp_incr." WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		$item_dlt = "";
		for($j = 1;$j <= $i;$j++){
			if($warehouse[$j] == "select" || $itemcode[$j] == "select") {
				
			}
			else {
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
				/* for amount updation done by suresh 01-05-2024*/
				$totalamt[$j] = $netweight[$j] * $itemprice[$j];

				$final_total = $final_total + $totalamt[$j];
				
				if($item_dlt == ""){
					if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
					$item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
				}
				else{
					if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
					$item_dlt = $item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
				}
				
				$sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,bookinvoice,vendorcode,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcdsamt,transporter_code,freight_amount,roundoff,finaltotal,balance,amtinwords,warehouse,flag,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client,trtype,trlink) 
				VALUES ('$date','$incr','$d','$m','$y','$pfx','$invoice','$bookinvoice','$vendorcode','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$itemprice[$j]','$totalamt[$j]','$tcdsper','$tcdsamt','$transporter_code','$freight_amount','$roundoff','$finaltotal','$finaltotal','$amtinwords','$warehouse[$j]','$flag','$authorization','$tdflag','$pdflag','$drivercode','$vehiclecode','$discounttype','$discountvalue[$j]','$taxtype','$taxvalue[$j]','$discountamt[$j]','$taxamount[$j]','$taxcode','$discountcode','$remarks','$addedemp','$addedtime','$client','$trtype','$trlink')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { unset($_SESSION['icode']); }
			}
		}
		/* for amount updation done by suresh 01-05-2024*/
		$final_total = $final_total + $tcdsamt; $roundoff = 0;
		$roffsize = sizeof($totalamt); $rtamt = 0; for($k = 1;$k <= $roffsize;$k++){ $rtamt = $rtamt + $totalamt[$k]; } $rtamt = $rtamt + $tcdsamt;
		if($rtamt >= $final_total){ $roundoff = $rtamt - $final_total; }
		else { $roundoff = $final_total - $rtamt; }
		
		mysqli_query($conn,"UPDATE `pur_purchase` SET roundoff = '$roundoff' ,finaltotal = '$final_total',balance = '$final_total' WHERE  invoice = '$invoice' ");

		if($purchase_sms_flag == 1){
			if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
			$sms_code = "SMS-".$incr_sms;
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
<mobile>'.$supplier_mobile.'</mobile>
<message>
Dear: '.$supplier_name.'
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
			$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client)
			VALUES ('$sms_code','$vendorcode','$supplier_mobile','$xml_data','$status[1]','PURCHASE','$addedemp','$addedtime','$addedtime','$client')";
			if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); }
			else{  } 
		}
		if($purchase_wapp_flag == 1){
			if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
			$wapp_code = "WAPP-".$incr_wapp;
			$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'sales' AND  `msg_type` = 'WAPP' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$wapp_user = $row['sms_user'];
				$wapp_key = $row['sms_key'];
				$wapp_msg_key = $row['msg_key'];
				$wapp_accusage = $row['sms_accusage'];
				$wapp_senderid = $row['sms_senderid'];
			}
$message = "
Dear: ".$supplier_name."
Date: ".date("d.m.Y",strtotime($date)).",
".$item_dlt.",
Sale Amt: ".$totalamount."/-
Balance: Rs. ".$bal."/-
Thank You,
".$cdetails;
			$xml_data = 'user='.$wapp_user.'&key='.$wapp_key.'&mobile='.'+'.$supplier_mobile.'&message='.$message.'&senderid='.$wapp_senderid.'&accusage='.$wapp_accusage;
			$URL = "http://mobicomm.dove-sms.com//submitsms.jsp?"; 

			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');			
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			//print_r($output); 
			$status = explode(",",$output);

			$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client)
			VALUES ('$wapp_code','$vendorcode','$supplier_mobile','$xml_data','$status[1]','PURCHASE','$addedemp','$addedtime','$addedtime','$client')";
			if(!mysqli_query($conn,$sql)) { die("Error:- WhApp sending error: ".mysqli_error($conn)); }
			else{  }
		}
		?>
		<script>
			var x = confirm("Would you like to save more entries?");
			if(x == true){
				window.location.href = "pur_addpurchases.php";
			}
			else if(x == false){
				window.location.href = "pur_displaypurchases.php";
			}
		</script>
		<?php
	}
	else if($_POST['submittrans'] == "updatepage"){
		$invoice = $_POST['inv'];
		$sql = "DELETE FROM `pur_purchase` WHERE `invoice` = '$invoice'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
		else {
			$date = date("Y-m-d",strtotime($_POST['pdate']));
			$d = date("d",strtotime($date));
			$m = date("m",strtotime($date));
			$y = date("Y",strtotime($date));
			$fys = explode("-",$_POST['inv']);
			$fy = $fys[0];
			$incr = $fys[1];
			$vendorcode = $_POST['pname'];
			$invoice = $_POST['inv'];
			$bookinvoice = $_POST['binv'];
			$finaltotal = round($_POST['gtamt']);
			$drivercode = $_POST['dname'];
			if($_POST['tdsperval'] == "" || $_POST['tdsperval'] == NULL || $_POST['tdsperval'] == "0"){
				$tcdsper = $tcdsamt = "0.00";
			}
			else{
				$tcdsper = $_POST['tdsperval'];
			}
			if($_POST['tdsamt'] == "" || $_POST['tdsamt'] == NULL || $_POST['tdsamt'] == "0"){
				$tcdsper = $tcdsamt = "0.00";
			}
			else{
				$tcdsamt = $_POST['tdsamt'];
			}
			$vehiclecode = $_POST['vno'];
			$amtinwords = $_POST['gtamtinwords'];

			$transporter_code = $_POST['transporter_code'];
			$freight_amount = $_POST['freight_amount']; if($freight_amount == ""){ $freight_amount = 0; }

			$eaddedempdetails = $_POST['addedempdetails'];
			$addedemps = explode("@",$eaddedempdetails);
			$acode = $addedemps[0];
			$atime = $addedemps[1];
			$discounttype = $taxtype = $taxcode = $discountcode = "Amt";
			$flag = $authorization = $tdflag = $pdflag = 0;
			$remarks = $_POST['narr'];
			$i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
			$i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; }
			$i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }
			$i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; }
			$i = 0; foreach($_POST['ewval'] as $eweights){ $i = $i + 1; $emptyweight[$i] = $eweights; }
			$i = 0; foreach($_POST['nwval'] as $nweights){ $i = $i + 1; $netweight[$i] = $nweights; }
			$i = 0; foreach($_POST['iprice'] as $iprices){ $i = $i + 1; $itemprice[$i] = $iprices; }
			$i = 0; foreach($_POST['idisc'] as $idiscs){ $i = $i + 1; $discountvalue[$i] = $discountamt[$i] = $idiscs; }
			$i = 0; foreach($_POST['itax'] as $itaxs){ $i = $i + 1; $taxvalue[$i] = $taxamount[$i] = $itaxs; }
			$i = 0; foreach($_POST['tamt'] as $tamts){ $i = $i + 1; $totalamt[$i] = $tamts; }
			$i = 0; foreach($_POST['wcodes'] as $whouses){ $i = $i + 1; $warehouse[$i] = $whouses; }
			if($roundoff == ""){ $roundoff = 0; }
			for($j = 1;$j <= $i;$j++){
				if($warehouse[$j] == "select" || $itemcode[$j] == "select") {
					
				}
				else {
					if($jals[$j] == "" || $jals[$j] == NULL){ $jals[$j] = "0.00"; } else{ }
					if($birds[$j] == "" || $birds[$j] == NULL){ $birds[$j] = "0.00"; } else{ }
					if($totalweight[$j] == "" || $totalweight[$j] == NULL){ $totalweight[$j] = "0.00"; } else{ }
					if($emptyweight[$j] == "" || $emptyweight[$j] == NULL){ $emptyweight[$j] = "0.00"; } else{ }
					if($netweight[$j] == "" || $netweight[$j] == NULL){ $netweight[$j] = "0.00"; } else{ }
					if($itemprice[$j] == "" || $itemprice[$j] == NULL || $itemprice == 'NaN'){ $itemprice[$j] = "0.00"; } else{ }
					if($discountvalue[$j] == "" || $discountvalue[$j] == NULL){ $discountvalue[$j] = "0.00"; } else{ }
					if($discountamt[$j] == "" || $discountamt[$j] == NULL){ $discountamt[$j] = "0.00"; } else{ }
					if($taxamount[$j] == "" || $taxamount[$j] == NULL){ $taxamount[$j] = "0.00"; } else{ }
					if($taxvalue[$j] == "" || $taxvalue[$j] == NULL){ $taxvalue[$j] = "0.00"; } else{ }

					/* for amount updation done by suresh 01-05-2024*/
					$totalamt[$j] = $netweight[$j] * $itemprice[$j];

					$final_total = $final_total + $totalamt[$j];

					$sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,bookinvoice,vendorcode,jals,totalweight,emptyweight,itemcode,birds,netweight,itemprice,totalamt,tcdsper,tcdsamt,transporter_code,freight_amount,roundoff,finaltotal,balance,amtinwords,warehouse,flag,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,updatedemp,updated,client,trtype,trlink) 
					VALUES ('$date','$incr','$d','$m','$y','$fy','$invoice','$bookinvoice','$vendorcode','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$itemprice[$j]','$totalamt[$j]','$tcdsper','$tcdsamt','$transporter_code','$freight_amount','$roundoff','$finaltotal','$finaltotal','$amtinwords','$warehouse[$j]','$flag','$authorization','$tdflag','$pdflag','$drivercode','$vehiclecode','$discounttype','$discountvalue[$j]','$taxtype','$taxvalue[$j]','$discountamt[$j]','$taxamount[$j]','$taxcode','$discountcode','$remarks','$acode','$atime','$addedemp','$addedtime','$client','$trtype','$trlink')";
				//	echo "<br/>";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { unset($_SESSION['icode']);
					//header('location:pur_displaypurchases.php');
					
					?>
			<script>
				var x = confirm("Would you like to save more entries?");
				if(x == true){
					window.location.href = "pur_addpurchases.php";
				}
				else if(x == false){
					window.location.href = "pur_displaypurchases.php";
				}
			</script>
			<?php
					}
				}
			}

			/* for amount updation done by suresh  01-05-2024*/
			$final_total = $final_total + $tcdsamt;
			$roffsize = sizeof($totalamt); $rtamt = 0; for($k = 1;$k <= $roffsize;$k++){ $rtamt = $rtamt + $totalamt[$k]; } $rtamt = $rtamt + $tcdsamt;
			if($rtamt >= $final_total){ $roundoff = $rtamt - $final_total; }
			else { $roundoff = $final_total - $rtamt; }
			
			mysqli_query($conn,"UPDATE `pur_purchase` SET roundoff = '$roundoff' ,finaltotal = '$final_total',balance = '$final_total' WHERE  invoice = '$invoice' ");
		}
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:pur_editpurchases.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `pur_purchase` WHERE `invoice` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){
					$type = "Purchase Invoice";
					$date = $row['date'];
					$transactionno = $row['invoice'];
					$description = $row['itemcode'];
					$doccode = $row['bookinvoice'];
					$pcode = $row['vendorcode'];
					$icode = $row['itemcode'];
					$quantity = $row['netweight'];
					$amount = $row['finaltotal'];
					$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
					VALUES('$type','$date','$transactionno','$description','$doccode','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				}
				$sql = "DELETE FROM `pur_purchase` WHERE `invoice` = '$id'";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					header('location:pur_displaypurchases.php');
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "pur_displaypurchases.php";
				}
				else if(x == false) {
					window.location.href = "pur_displaypurchases.php";
				}
				else {
					window.location.href = "pur_displaypurchases.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$id = $_GET['id'];
			$sql = "UPDATE `pur_purchase` SET `active` = '1' WHERE `invoice` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:pur_displaypurchases.php'); }
		}
		else if($updatetype == "pause"){
			$id = $_GET['id'];
			$sql = "UPDATE `pur_purchase` SET `active` = '0' WHERE `invoice` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:pur_displaypurchases.php'); }
		}
		else if($updatetype == "authorize"){
			$id = $_GET['id'];
			$sql = "UPDATE `pur_purchase` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `invoice` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:pur_displaypurchases.php'); }
		}
		else {}
	}
?>