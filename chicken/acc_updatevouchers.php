<?php
//pur_updatepayments.php
	session_start(); include "newConfig.php";
	include "number_format_ind.php";
	$client = $_SESSION['client'];
	$addedemp = $_SESSION['userid'];
	$username = $_SESSION['users'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	global $sms_type; $sms_type = "BB-Salary"; include "chicken_wapp_connectionmaster.php";
	
	if((int)$wapp_error_flag == 0){
		/*Check for Table Availability*/
		$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
		$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
		if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
	
		$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Send WhatsApp Timer' AND `field_function` = 'cus_add_paperratesales1.php' AND `user_access` = 'all'";
		$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $wapp_timer_flag = 0;
		if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_timer_flag = $row1['flag']; } }
		else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','cus_add_paperratesales1.php','all','0');"; mysqli_query($conn,$sql1); }
	}
	
	if($_POST['submittrans'] == "addpage"){
		foreach($_POST['pdate'] as $pdate){ $pdates[] = date("Y-m-d",strtotime($pdate)); $date = date("Y-m-d",strtotime($pdate)); }
		foreach($_POST['dcno'] as $dcno){ $dcnos[] = $dcno; }
		foreach($_POST['fcoa'] as $fcoa){ $fcoas[] = $fcoa; }
		foreach($_POST['tcoa'] as $tcoa){ $tcoas[] = $tcoa; } $spnames = sizeof($tcoas);
		foreach($_POST['amount'] as $amount){ $amounts[] = $amount; }
		foreach($_POST['gtamtinwords'] as $amtinword){ $amtinwords[] = $amtinword; }
		foreach($_POST['sector'] as $sector){ $sectors[] = $sector; }
		foreach($_POST['remark'] as $remark){ $remarks[] = $remark; }
		//$gamts = $_POST['gtamt'];
		$sql = "SELECT * FROM `acc_coa`"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; $coa_mobile[$row['code']] = $row['mobile_no']; }
		$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
		$colarr=array('salary_voucher_wapp');
		$q='show columns from master_itemfields';
        $qr=mysqli_query($conn,$q);
        $i=0;
        $columns=array();
        while($rw=mysqli_fetch_assoc($qr))
        {
            $columns[$i]=$rw['Field'];       
            $i++;
        }
        $diff_array=array_diff($colarr,$columns);
        if(in_array('salary_voucher_wapp',$diff_array))
        {
            $q="ALTER TABLE `master_itemfields` ADD `salary_voucher_wapp` INT NOT NULL DEFAULT '0' AFTER `type`;";
            $qr=mysqli_query($conn,$q) ;
        }
		$sql = "SELECT * FROM `master_itemfields` WHERE `salary_voucher_wapp` = '1' AND `active` = '1'";
		$query = mysqli_query($conn,$sql); $empsal_wapp = mysqli_num_rows($query);
			
		$vtype = $_POST['pname'];
		for($i = 0;$i < $spnames; $i++){
			if($empsal_wapp > 0){
				$from_coa = $to_coa = ""; $from_coa = $fcoas[$i]; $to_coa = $tcoas[$i];
				
				if(!empty($coa_mobile[$from_coa]) && $coa_mobile[$from_coa] != "" && strlen($coa_mobile[$from_coa]) == 10){
					$cr_amount = $dr_amount = 0;
					$sql1 = "SELECT SUM(amount) as amount FROM `acc_vouchers` WHERE `fcoa` = '$from_coa' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'"; $query1 = mysqli_query($conn,$sql1); $fcount = mysqli_num_rows($query1);
					if($fcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ if(number_format_ind($row1['amount']) != "0.00"){ $cr_amount = $row1['amount']; } else{ $cr_amount = 0; } } }
					else{ $cr_amount = 0; }
					$sql1 = "SELECT SUM(amount) as amount FROM `acc_vouchers` WHERE `tcoa` = '$from_coa' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'"; $query1 = mysqli_query($conn,$sql1); $fcount2 = mysqli_num_rows($query1);
					if($fcount2 > 0){ while($row1 = mysqli_fetch_assoc($query1)){ if(number_format_ind($row1['amount']) != "0.00"){ $dr_amount = $row1['amount']; } else{ $dr_amount = 0; } } }
					else{ $dr_amount = 0; }
					
					$cur_cramt = $opening_bal_amount = $closing_bal_amount = 0; $cur_cramt = (float)$amounts[$i];
					$opening_bal_amount = ((float)$cr_amount - (float)$dr_amount);
					$closing_bal_amount = (((float)$cr_amount + (float)$cur_cramt) - (float)$dr_amount);
					
					$message = "";
					$message = "Dear: ".$coa_name[$from_coa]."%0D%0ADate: ".date('d.m.Y',strtotime($pdates[$i]))."%0D%0AOpening Balance: ".number_format_ind($opening_bal_amount)."/-%0D%0ASalary: ".number_format_ind($cur_cramt)."/-%0D%0AClosing Balance: ".number_format_ind($closing_bal_amount)."/-%0D%0ARemarks: ".$remarks[$i]."%0D%0AThank You,%0D%0A".$cdetails;
					$message = str_replace(" ","+",$message);
					$number = "91".$coa_mobile[$from_coa]; $type = "text";
					$ccode = $from_coa;
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
						$trtype = "Voucher Message";
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
				else if(!empty($coa_mobile[$to_coa]) && $coa_mobile[$to_coa] != "" && strlen($coa_mobile[$to_coa]) == 10){
					$cr_amount = $dr_amount = 0;
					$sql1 = "SELECT SUM(amount) as amount FROM `acc_vouchers` WHERE `fcoa` = '$to_coa' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'"; $query1 = mysqli_query($conn,$sql1); $fcount = mysqli_num_rows($query1);
					if($fcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ if(number_format_ind($row1['amount']) != "0.00"){ $cr_amount = $row1['amount']; } else{ $cr_amount = 0; } } }
					else{ $cr_amount = 0; }
					$sql1 = "SELECT SUM(amount) as amount FROM `acc_vouchers` WHERE `tcoa` = '$to_coa' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'"; $query1 = mysqli_query($conn,$sql1); $fcount = mysqli_num_rows($query1);
					if($fcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ if(number_format_ind($row1['amount']) != "0.00"){ $dr_amount = $row1['amount']; } else{ $dr_amount = 0; } } }
					else{ $dr_amount = 0; }
					
					$cur_cramt = $opening_bal_amount = $closing_bal_amount = 0; $cur_cramt = (float)$amounts[$i];
					$opening_bal_amount = ((float)$cr_amount - (float)$dr_amount);
					$closing_bal_amount = (((float)$cr_amount) - ((float)$dr_amount + (float)$cur_cramt));
					
					$message = "";
					$message = "Dear: ".$coa_name[$to_coa]."%0D%0ADate: ".date('d.m.Y',strtotime($pdates[$i]))."%0D%0AOpening Balance: ".number_format_ind($opening_bal_amount)."/-%0D%0AReceived Amount: ".number_format_ind($cur_cramt)."/-%0D%0AClosing Balance: ".number_format_ind($closing_bal_amount)."/-%0D%0ARemarks: ".$remarks[$i]."%0D%0AThank You,%0D%0A".$cdetails;
					$message = str_replace(" ","+",$message);
					$number = "91".$coa_mobile[$to_coa]; $type = "text";
					$ccode = $to_coa;
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
						$trtype = "Voucher Message";
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
				else{ }
			}
			
			$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$pdates[$i]' AND `tdate` >= '$pdates[$i]' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
			if($vtype == "PV"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $pvouchers = $row['pvouchers']; } $incr = $pvouchers + 1;
				$sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
				$code = "PV-".$fprefix."".$incr; $prefix = "PV";
			}
			else if($vtype == "RV"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $rvouchers = $row['rvouchers']; } $incr = $rvouchers + 1;
				$sql = "UPDATE `master_generator` SET `rvouchers` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
				$code = "RV-".$fprefix."".$incr; $prefix = "RV";
			}
			else if($vtype == "JV"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $jvouchers = $row['jvouchers']; } $incr = $jvouchers + 1;
				$sql = "UPDATE `master_generator` SET `jvouchers` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
				$code = "JV-".$fprefix."".$incr; $prefix = "JV";
			}
			else {
				$code = "Invalid";
			}
			if($code == "Invalid"){}
			else {
				$sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,date,fcoa,tcoa,amount,amtinwords,warehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
				VALUES ('$incr','$prefix','$code','$pdates[$i]','$fcoas[$i]','$tcoas[$i]','$amounts[$i]','$amtinwords[$i]','$sectors[$i]','$dcnos[$i]','$remarks[$i]','0','1','$addedemp','$addedtime','0','0','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
			
		}
		if($i == $spnames){
			header('location:acc_displayvouchers.php');
		}
		else {
			echo "Error:-".mysqli_error($conn);
		}
	}
	else if($_POST['submittrans'] == "updatepage"){
		$trnums = $_POST['trnum']; $pdates = date("Y-m-d",strtotime($_POST['pdate'])); $dcnos = $_POST['dcno']; $fcoas = $_POST['fcoa'];
		$sectors = $_POST['sector']; $tcoas = $_POST['tcoa']; $amounts = $_POST['amount']; $amtinwords = $_POST['gtamtinwords']; $remarks = $_POST['remark'];
		$sql = "UPDATE `acc_vouchers` SET `date` = '$pdates',`dcno` = '$dcnos',`fcoa` = '$fcoas',`tcoa` = '$tcoas',`amount` = '$amounts',`amtinwords` = '$amtinwords',`warehouse` = '$sectors',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnums'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayvouchers.php'); }
		
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:acc_editvouchers.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `acc_vouchers` WHERE `trnum` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){
					$type = "Vouchers";
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
				$sql = "DELETE FROM `acc_vouchers` WHERE `trnum` = '$id'";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					header('location:acc_displayvouchers.php');
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "acc_displayvouchers.php";
				}
				else if(x == false) {
					window.location.href = "acc_displayvouchers.php";
				}
				else {
					window.location.href = "acc_displayvouchers.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$id = $_GET['id'];
			$sql = "UPDATE `acc_vouchers` SET `active` = '1' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayvouchers.php'); }
		}
		else if($updatetype == "pause"){
			$id = $_GET['id'];
			$sql = "UPDATE `acc_vouchers` SET `active` = '0' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayvouchers.php'); }
		}
		else if($updatetype == "authorize"){
			$id = $_GET['id'];
			$sql = "UPDATE `acc_vouchers` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayvouchers.php'); }
		}
		else {}
	}
?>