<?php
	//inv_updateitemcategory.php
	session_start(); include "newConfig.php";
	$clients = $_SESSION['client'];
	$empcode = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$d = date('Y-m-d H:i:s');
	
	if($_GET['idesc'] != null){
		$cdesc = $_GET['idesc'];
		$stype = $_GET['stype'];
		$sunits = $_GET['sloc'];
		$smanager = $_GET['shop_manager'];
		$mobile = $_GET['shop_mobile'];
		$state = $_GET['shop_state'];
		$address = $_GET['shop_address'];
		$email = $_GET['shop_email'];
		if($_GET['submittrans'] == "addpage"){
			$sql ="SELECT * FROM `inv_sectors` WHERE `description` = '$cdesc'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ ?>
				<script>
					var x = alert("The Description is already available \n Please check and try again ..!");
					if(x == true){ window.location.href = "main_displayoffices.php"; } else if(x == false) { window.location.href = "main_displayoffices.php"; } else { window.location.href = "main_displayoffices.php"; }
				</script>
			<?php
			}
			else {
				$sql = "SELECT MAX(incr) as incr FROM `inv_sectors`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
				if($ccount > 0){
					while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
					$prefix = "SEH"; $code = $prefix."-".$incrs;
				} else {
					$incrs = 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
					$prefix = "SEH"; $code = $prefix."-".$incrs;
				}
				$sql = "INSERT INTO `inv_sectors` (incr,prefix,code,description,type,location,smanager,mobile,email,address,state,addedemp,addedtime,client) VALUES 
				('$incrs','$prefix','$code','$cdesc','$stype','$sunits','$smanager','$mobile','$email','$address','$state','$empcode','$d','$clients')";
				
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayoffices.php'); }
			}
		
		}
		else if($_GET['submittrans'] == "updatepage"){
			$id = $_GET['idvalue'];
			$sql ="SELECT * FROM `inv_sectors` WHERE `description` = '$cdesc' AND `id` NOT IN ('$id')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ ?>
				<script>
					var x = alert("The Description is already available \n Please check and try again ..!");
					if(x == true){ window.location.href = "main_displayoffices.php"; } else if(x == false) { window.location.href = "main_displayoffices.php"; } else { window.location.href = "main_displayoffices.php"; }
				</script>
				<?php
			}
			else {
				$sql = "UPDATE `inv_sectors` SET `description` = '$cdesc',`type` = '$stype',`location` = '$sunits',`smanager` = '$smanager',`mobile` = '$mobile',`email` = '$email',`address` = '$address',`state` = '$state',`addedemp` = '$empcode',`updated` = '$d',`client` = '$clients' WHERE `id` = '$id'";
			}
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayoffices.php'); }
		}
		else {
			
		}
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:inv_editofficemasters.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `inv_sectors` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
				$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('Sector','$catcode','$catdesc','$empcode')";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					$sql = "DELETE FROM `inv_sectors` WHERE `id` = '$id'";
					if(!mysqli_query($conn,$sql)){
						die("Error:-".mysqli_error($conn));
					}
					else {
						header('location:main_displayoffices.php');
					}
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "main_displayoffices.php";
				}
				else if(x == false {
					window.location.href = "main_displayoffices.php";
				}
				else {
					window.location.href = "main_displayoffices.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$sql = "UPDATE `inv_sectors` SET `active` = '1' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayoffices.php'); }
		}
		else if($updatetype == "deactivate"){
			$sql = "UPDATE `inv_sectors` SET `active` = '0' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayoffices.php'); }
		}
		else if($updatetype == "authorize"){
			$sql = "UPDATE `inv_sectors` SET `flag` = '1',`approvedemp` = '$empcode',`approvedtime` = '$d' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayoffices.php'); }
		}
		else {}
	}
?>