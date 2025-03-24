<?php
//inv_updateitemcategory.php
session_start(); include "newConfig.php";
$clients = $_SESSION['client'];
$empcode = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$d = date('Y-m-d H:i:s');

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `item_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("short_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `short_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Short Name' AFTER `description`"; mysqli_query($conn,$sql); }

	if($_GET['idesc'] != null){
		$cdesc = $_GET['idesc'];
		$short_name = $_GET['short_name'];
		$ctype = $_GET['icat'];
		$sunits = $_GET['istored'];
		$cunits = $_GET['icunit'];
		if($_GET['submittrans'] == "addpage"){
			$sql ="SELECT * FROM `item_details` WHERE `description` = '$cdesc'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ ?>
				<script>
					var x = alert("The Description is already available \n Please check and try again ..!");
					if(x == true){ window.location.href = "inv_additemmaster.php"; } else if(x == false) { window.location.href = "inv_additemmaster.php"; } else { window.location.href = "inv_additemmaster.php"; }
				</script>
			<?php
			}
			else {
				$sql = "SELECT prefix from `item_category` WHERE `code` = '$ctype'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; }

				$sql ="SELECT MAX(incr) as incr FROM `item_details` WHERE `prefix` LIKE '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
				if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }

				if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }

				$code = $prefix."-".$incrs;

				$sql = "INSERT INTO `item_details` (incr,prefix,category,code,description,short_name,sunits,cunits,addedemp,addedtime,approvedtime,client) VALUES 
				('$incrs','$prefix','$ctype','$code','$cdesc','$short_name','$sunits','$cunits','$empcode','$d','$d','$clients')";

				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitems.php'); }
			}
		}
		else if($_GET['submittrans'] == "updatepage"){
			$id = $_GET['idvalue'];
			$sql ="SELECT * FROM `item_details` WHERE `description` = '$cdesc' AND `id` NOT IN ('$id')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){ ?>
				<script>
					var x = alert("The Description is already available \n Please check and try again ..!");
					if(x == true){ window.location.href = "inv_additemmaster.php"; } else if(x == false) { window.location.href = "inv_additemmaster.php"; } else { window.location.href = "inv_additemmaster.php"; }
				</script>
			<?php
			}
			else {
				$sql = "UPDATE `item_details` SET `category` = '$ctype',`description` = '$cdesc',`short_name` = '$short_name',`sunits` = '$sunits',`cunits` = '$cunits',`updated` = '$d',`client` = '$clients' WHERE `id` = '$id'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitems.php'); }
			}
		}
		else { }
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		if($updatetype == "edit"){ header('location:inv_edititemmasters.php?id='.$id); }

		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `item_details` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
				$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('itemcat','$catcode','$catdesc','$empcode')";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					$sql = "DELETE FROM `item_details` WHERE `id` = '$id'";
					if(!mysqli_query($conn,$sql)){
						die("Error:-".mysqli_error($conn));
					}
					else {
						header('location:inv_displayitems.php');
					}
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "inv_displayitems.php";
				}
				else if(x == false) {
					window.location.href = "inv_displayitems.php";
				}
				else {
					window.location.href = "inv_displayitems.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_details` SET `active` = '1' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitems.php'); }
		}
		else if($updatetype == "pause"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_details` SET `active` = '0' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitems.php'); }
		}
		else if($updatetype == "authorize"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_details` SET `flag` = '1',`approvedemp` = '$empcode',`approvedtime` = '$d' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_displayitems.php'); }
		}
		else {}
	}
?>