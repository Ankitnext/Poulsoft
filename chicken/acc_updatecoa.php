<?php
	//acc_updatecoa.php
	session_start(); include "newConfig.php";
	$clients = $_SESSION['client'];
	$empcode = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = $d = date('Y-m-d H:i:s');
	
	//Fetch Column From CoA Table
	$sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
	if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }
	if(in_array("vouexp_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `vouexp_flag` INT(100) NOT NULL DEFAULT '0' AFTER `transport_flag`"; mysqli_query($conn,$sql); }
	if(in_array("driver_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `driver_flag` INT(100) NOT NULL DEFAULT '0' AFTER `vouexp_flag`"; mysqli_query($conn,$sql); }
	if(in_array("spaof_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `spaof_flag` INT(100) NOT NULL DEFAULT '0' AFTER `driver_flag`"; mysqli_query($conn,$sql); }

	if($_GET['cdesc'] != null){
		$cdesc = $_GET['cdesc'];
		$type = $_GET['type'];
		$ctype = $_GET['ctype']; if($ctype == "select"){ $ctype = NULL; } else { }
		$stype = $_GET['stype'];
		$cptype = $_GET['cptype'];
		$mobile_no = $_GET['mobile_no'];
		$bsis = $_GET['checbox'];
		$transport_flag = $_GET['transport_flag']; if($transport_flag == "on" || $transport_flag == true || $transport_flag == "1"){ $transport_flag = 1; } else{ $transport_flag = 0; }
		$vouexp_flag = $_GET['vouexp_flag']; if($vouexp_flag == "on" || $vouexp_flag == true || $vouexp_flag == "1"){ $vouexp_flag = 1; } else{ $vouexp_flag = 0; }
		$driver_flag = $_GET['driver_flag']; if($driver_flag == "on" || $driver_flag == true || $driver_flag == "1"){ $driver_flag = 1; } else{ $driver_flag = 0; }
		$spaof_flag = $_GET['spaof_flag']; if($spaof_flag == "on" || $spaof_flag == true || $spaof_flag == "1"){ $spaof_flag = 1; } else{ $spaof_flag = 0; }
		if($bsis == "BS"){ $bstat = 1; $istat = 0; }
		else{ $bstat = 0; $istat = 1; }
		if($_GET['submittrans'] == "addpage"){
			$sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `type` = '$type'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }
			
			$sql ="SELECT DISTINCT(prefix) as prefixs FROM `acc_schedules` WHERE `code` = '$stype'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $prefixs = $row['prefixs']; } } else { }
		
			if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
			
			$code = $prefixs."-".$incrs;
			
			$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,ctype,schedules,categories,bs,`is`,`mobile_no`,`transport_flag`,`vouexp_flag`,`driver_flag`,`spaof_flag`,addedemp,addedtime,client) VALUES 
			('$incrs','$prefixs','$code','$cdesc','$type','$ctype','$stype','$cptype','$bstat','$istat','$mobile_no','$transport_flag','$vouexp_flag','$driver_flag','$spaof_flag','$empcode','$d','$clients')";
			
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
			else{
				//Opening
				$odate = date("Y-m-d",strtotime($_GET['odate']));
				$opn_type = $_GET['otype'];
				$oamount = $_GET['oamount']; if($oamount == ""){ $oamount = 0; }
				$osector = $_GET['osector'];
				$oremarks = $_GET['oremarks'];
				
				if((float)$oamount > 0){
					//Generate Transaction No.
					$incr = 0; $fprefix = $trnum = $fyear = "";
					$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$odate' AND `tdate` >= '$odate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
					$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$odate' AND `tdate` >= '$odate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $pvouchers = $row['pvouchers']; } $incr = $pvouchers + 1;
					$sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$odate' AND `tdate` >= '$odate' AND `type` = 'transactions'";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
					if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
					$trnum = "PV-".$fprefix."".$incr; $prefix = "PV"; $vtype = "Openings"; $dcno = "";

					$fcoa = $tcoa = ""; if($opn_type == "CR"){ $fcoa = $code; } else{ $tcoa = $code; }
					$sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,vtype,date,dcno,fcoa,tcoa,amount,amtinwords,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
					VALUES ('$incr','$prefix','$trnum','$vtype','$odate','$dcno','$fcoa','$tcoa','$oamount',NULL,'$osector','$oremarks','1','1','$empcode','$addedtime','0','0','$client')";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
					else{ header('location:acc_displaycoa.php'); }
				}
				else{ header('location:acc_displaycoa.php'); }
			}
		}
		else if($_GET['submittrans'] == "updatepage"){
			$id = $_GET['idvalue'];
			$sql ="SELECT * FROM `acc_coa` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $oldtype = $row['type']; $oldcode = $row['code']; } }
			if($oldtype == $type){
				$sql = "UPDATE `acc_coa` SET `description` = '$cdesc',`ctype` = '$ctype',`schedules` = '$stype',`categories` = '$cptype',`bs` = '$bstat',`is` = '$istat',`mobile_no` = '$mobile_no',`transport_flag` = '$transport_flag',`vouexp_flag` = '$vouexp_flag',`driver_flag` = '$driver_flag',`spaof_flag` = '$spaof_flag',`updated` = '$d',`client` = '$clients' WHERE `id` = '$id'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {
					//Opening
					$odate = date("Y-m-d",strtotime($_GET['odate']));
					$opn_type = $_GET['otype'];
					$oamount = $_GET['oamount']; if($oamount == ""){ $oamount = 0; }
					$osector = $_GET['osector'];
					$oremarks = $_GET['oremarks'];
					$prefix = "PV"; $vtype = "Openings"; $dcno = "";
					$fcoa = $tcoa = ""; if($opn_type == "CR"){ $fcoa = $oldcode; } else{ $tcoa = $oldcode; }

					$sql = "SELECT * FROM `acc_vouchers` WHERE (`fcoa` = '$oldcode' OR `tcoa` = '$oldcode') AND `vtype` = 'Openings' AND `tdflag` = '0' AND `pdflag` = '0'";
					$query = mysqli_query($conn,$sql); $o_cnt = mysqli_num_rows($query);
					if((int)$o_cnt > 0){
						while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; $trnum = $row['trnum']; }
						$sql3 = "DELETE FROM `acc_vouchers` WHERE `trnum` = '$trnum' AND `tdflag` = '0' AND `pdflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
						if((float)$oamount > 0){
							$sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,vtype,date,dcno,fcoa,tcoa,amount,amtinwords,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
							VALUES ('$incr','$prefix','$trnum','$vtype','$odate','$dcno','$fcoa','$tcoa','$oamount',NULL,'$osector','$oremarks','1','1','$empcode','$addedtime','0','0','$client')";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
						}
					}
					else{
						if((float)$oamount > 0){
							//Generate Transaction No.
							$incr = 0; $fprefix = $trnum = $fyear = "";
							$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$odate' AND `tdate` >= '$odate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
							while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
							$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$odate' AND `tdate` >= '$odate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
							while($row = mysqli_fetch_assoc($query)){ $pvouchers = $row['pvouchers']; } $incr = $pvouchers + 1;
							$sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$odate' AND `tdate` >= '$odate' AND `type` = 'transactions'";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
							if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
							$trnum = "PV-".$fprefix."".$incr; $prefix = "PV"; $vtype = "Openings"; $dcno = "";
						
							$sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,vtype,date,dcno,fcoa,tcoa,amount,amtinwords,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
							VALUES ('$incr','$prefix','$trnum','$vtype','$odate','$dcno','$fcoa','$tcoa','$oamount',NULL,'$osector','$oremarks','1','1','$empcode','$addedtime','0','0','$client')";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
						}
					}
					header('location:acc_displaycoa.php');
				}
			}
			else{
				$sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `type` = '$type'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
				if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }
				
				$sql ="SELECT DISTINCT(prefix) as prefixs FROM `acc_schedules` WHERE `code` = '$stype'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
				if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $prefixs = $row['prefixs']; } } else { }
			
				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				
				$code = $prefixs."-".$incrs;
				
				$sql = "UPDATE `acc_coa` SET `incr` = '$incrs',`prefix` = '$prefixs',`code` = '$code',`description` = '$cdesc',`type` = '$type',`ctype` = '$ctype',`schedules` = '$stype',`categories` = '$cptype',`bs` = '$bstat',`is` = '$istat',`transport_flag` = '$transport_flag',`vouexp_flag` = '$vouexp_flag',`driver_flag` = '$driver_flag',`spaof_flag` = '$spaof_flag',`addedemp` = '$empcode',`addedtime` = '$d',`client` = '$clients' WHERE `id` = '$id'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {
					//Opening
					$odate = date("Y-m-d",strtotime($_GET['odate']));
					$opn_type = $_GET['otype'];
					$oamount = $_GET['oamount']; if($oamount == ""){ $oamount = 0; }
					$osector = $_GET['osector'];
					$oremarks = $_GET['oremarks'];
					$prefix = "PV"; $vtype = "Openings"; $dcno = "";
					$fcoa = $tcoa = ""; if($opn_type == "CR"){ $fcoa = $code; } else{ $tcoa = $code; }

					$sql = "SELECT * FROM `acc_vouchers` WHERE (`fcoa` = '$oldcode' OR `tcoa` = '$oldcode') AND `vtype` = 'Openings' AND `tdflag` = '0' AND `pdflag` = '0'";
					$query = mysqli_query($conn,$sql); $o_cnt = mysqli_num_rows($query);
					if((int)$o_cnt > 0){
						while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; $trnum = $row['trnum']; }
						$sql3 = "DELETE FROM `acc_vouchers` WHERE `trnum` = '$trnum' AND `tdflag` = '0' AND `pdflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
						if((float)$oamount > 0){
							$sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,vtype,date,dcno,fcoa,tcoa,amount,amtinwords,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
							VALUES ('$incr','$prefix','$trnum','$vtype','$odate','$dcno','$fcoa','$tcoa','$oamount',NULL,'$osector','$oremarks','1','1','$empcode','$addedtime','0','0','$client')";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
						}
					}
					else{
						if((float)$oamount > 0){
							//Generate Transaction No.
							$incr = 0; $fprefix = $trnum = $fyear = "";
							$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$odate' AND `tdate` >= '$odate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
							while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
							$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$odate' AND `tdate` >= '$odate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
							while($row = mysqli_fetch_assoc($query)){ $pvouchers = $row['pvouchers']; } $incr = $pvouchers + 1;
							$sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$odate' AND `tdate` >= '$odate' AND `type` = 'transactions'";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
							if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
							$trnum = "PV-".$fprefix."".$incr; $prefix = "PV"; $vtype = "Openings"; $dcno = "";
							$fcoa = $tcoa = ""; if($opn_type == "CR"){ $fcoa = $code; } else{ $tcoa = $code; }
						
							$sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,vtype,date,dcno,fcoa,tcoa,amount,amtinwords,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
							VALUES ('$incr','$prefix','$trnum','$vtype','$odate','$dcno','$fcoa','$tcoa','$oamount',NULL,'$osector','$oremarks','1','1','$empcode','$addedtime','0','0','$client')";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
						}
					}
					header('location:acc_displaycoa.php');
				}
			}
		}
		else {
			
		}
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:acc_editcoa.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `acc_coa` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
				$sql3 = "UPDATE `acc_vouchers` SET `tdflag` = '1',`pdflag` = '1',`active` = '0',`flag` = '0' WHERE (`fcoa` = '$catcode' OR `tcoa` = '$catcode') AND `vtype` = 'Openings' AND `tdflag` = '0' AND `pdflag` = '0'";
				mysqli_query($conn,$sql3);
				$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('CoA','$catcode','$catdesc','$empcode')";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					$sql = "DELETE FROM `acc_coa` WHERE `id` = '$id'";
					if(!mysqli_query($conn,$sql)){
						die("Error:-".mysqli_error($conn));
					}
					else {
						header('location:acc_displaycoa.php');
					}
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "acc_displaycoa.php";
				}
				else if(x == false) {
					window.location.href = "acc_displaycoa.php";
				}
				else {
					window.location.href = "acc_displaycoa.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$sql ="SELECT * FROM `acc_coa` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }
				$sql3 = "UPDATE `acc_vouchers` SET `active` = '1' WHERE (`fcoa` = '$code' OR `tcoa` = '$code') AND `vtype` = 'Openings' AND `tdflag` = '0' AND `pdflag` = '0'";
				mysqli_query($conn,$sql3);
			}

			$sql = "UPDATE `acc_coa` SET `active` = '1' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycoa.php'); }
		}
		else if($updatetype == "pause"){
			$sql ="SELECT * FROM `acc_coa` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }
				$sql3 = "UPDATE `acc_vouchers` SET `active` = '0' WHERE (`fcoa` = '$code' OR `tcoa` = '$code') AND `vtype` = 'Openings' AND `tdflag` = '0' AND `pdflag` = '0'";
				mysqli_query($conn,$sql3);
			}
			$sql = "UPDATE `acc_coa` SET `active` = '0' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycoa.php'); }
		}
		else if($updatetype == "authorize"){
			$sql ="SELECT * FROM `acc_coa` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }
				$sql3 = "UPDATE `acc_vouchers` SET `flag` = '1' WHERE (`fcoa` = '$code' OR `tcoa` = '$code') AND `vtype` = 'Openings' AND `tdflag` = '0' AND `pdflag` = '0'";
				mysqli_query($conn,$sql3);
			}
			$sql = "UPDATE `acc_coa` SET `flag` = '1',`approvedemp` = '$empcode',`approveddate` = '$d' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycoa.php'); }
		}
		else {}
	}
?>