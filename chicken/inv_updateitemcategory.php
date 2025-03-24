<?php
	//inv_updateitemcategory.php

	session_start(); include "newConfig.php";
	$clients = $_SESSION['client'];
	$empcode = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$d = date('Y-m-d H:i:s');
	
	if($_GET['cdesc'] != null){
		$cdesc = $_GET['cdesc'];
		$prefix = $_GET['prefix'];
		$new_a = $_GET['newaccounts'];
		if($new_a == "new_acc"){
			$sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'STK-%'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$incr = $row['incr']; $incrs = $incr + 1;
				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = "STK-".$incrs; $type = "COA-0001"; $sch = "ASC-0001"; $cat = "CAT-0001"; $coadesc = "Stock - ".$cdesc;
				$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs) VALUES('$incrs','STK','$code','$coadesc','$type','$sch','$cat','1')"; mysqli_query($conn,$sql);
				$iac = $code;
			}
			$sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'CGS-%'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$incr = $row['incr']; $incrs = $incr + 1;
				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = "CGS-".$incrs; $type = "COA-0003"; $sch = "EXP-0001"; $cat = "CAT-0007"; $coadesc = "COGS - ".$cdesc;
				$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs) VALUES('$incrs','CGS','$code','$coadesc','$type','$sch','$cat','1')"; mysqli_query($conn,$sql);
				$icogs = $code;
			}
			$sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'SLA-%'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$incr = $row['incr']; $incrs = $incr + 1;
				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = "SLA-".$incrs; $type = "COA-0005"; $sch = "RVN-0001"; $cat = "CAT-0012"; $coadesc = "Sales - ".$cdesc;
				$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs) VALUES('$incrs','SLA','$code','$coadesc','$type','$sch','$cat','1')"; mysqli_query($conn,$sql);
				$isalesac = $code;
			}
			$sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'SRA-%'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$incr = $row['incr']; $incrs = $incr + 1;
				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = "SRA-".$incrs; $type = "COA-0003"; $sch = "EXP-0004"; $cat = "CAT-0007"; $coadesc = "Sales Return - ".$cdesc;
				$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs) VALUES('$incrs','SRA','$code','$coadesc','$type','$sch','$cat','1')"; mysqli_query($conn,$sql);
				$israc = $code;
			}
		}
		else {
			$iac = $_GET['iac'];
			$icogs = $_GET['icogs'];
			$isalesac = $_GET['isalesac'];
			$israc = $_GET['israc'];
		}
		if($_GET['submittrans'] == "addpage"){
			$sql ="SELECT * FROM `item_category` WHERE `prefix` = '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ ?>
				<script>
					var x = alert("The Prefix is already available \n Please check and try again ..!");
					if(x == true){ window.location.href = "inv_displayitemcategory.php"; } else if(x == false) { window.location.href = "inv_displayitemcategory.php"; } else { window.location.href = "inv_displayitemcategory.php"; }
				</script>
			<?php
			}
			else {
				$sql ="SELECT MAX(incr) as incr FROM `item_category`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
				if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }
				
				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				
				$code = "IMS-".$incrs;
				
				$sql = "INSERT INTO `item_category` (incr,prefix,code,description,iac,cogsac,sac,srac,addedemp,addedtime,client) VALUES 
				('$incrs','$prefix','$code','$cdesc','$iac','$icogs','$isalesac','$israc','$empcode','$d','$clients')";
				
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitemcategory.php'); }
			}
		
		}
		else if($_GET['submittrans'] == "updatepage"){
			$cdesc = $_GET['cdesc'];
			$prefix = $_GET['prefix'];
			$iac = $_GET['iac'];
			$icogs = $_GET['icogs'];
			$isalesac = $_GET['isalesac'];
			$israc = $_GET['israc'];
			$id = $_GET['idvalue'];
			$sql ="SELECT * FROM `item_category` WHERE `prefix` = '$prefix' AND `id` NOT IN ('$id')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ ?>
				<script>
					var x = alert("The Prefix is already available \n Please check and try again ..!");
					if(x == true){ window.location.href = "inv_displayitemcategory.php"; } else if(x == false) { window.location.href = "inv_displayitemcategory.php"; } else { window.location.href = "inv_displayitemcategory.php"; }
				</script>
			<?php
			}
			else {
				$sql = "UPDATE `item_category` SET `prefix` = '$prefix',`description` = '$cdesc',`iac` = '$iac',`cogsac` = '$icogs',`sac` = '$isalesac',`srac` = '$israc',`updated` = '$d',`client` = '$clients' WHERE `id` = '$id'";
				
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitemcategory.php'); }
			}
		}
		else {
			
		}
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:inv_edititemcategory.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `item_category` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
				$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('itemcat','$catcode','$catdesc','$empcode')";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					$sql = "DELETE FROM `item_category` WHERE `id` = '$id'";
					if(!mysqli_query($conn,$sql)){
						die("Error:-".mysqli_error($conn));
					}
					else {
						header('location:inv_displayitemcategory.php');
					}
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "inv_displayitemcategory.php";
				}
				else if(x == false {
					window.location.href = "inv_displayitemcategory.php";
				}
				else {
					window.location.href = "inv_displayitemcategory.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_category` SET `active` = '1' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitemcategory.php'); }
		}
		else if($updatetype == "pause"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_category` SET `active` = '0' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitemcategory.php'); }
		}
		else if($updatetype == "authorize"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_category` SET `flag` = '1',`approvedemp` = '$empcode',`approvedtime` = '$d' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitemcategory.php'); }
		}
		else {}
	}
?>