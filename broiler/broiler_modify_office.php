<?php
//broiler_modify_office.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['office'];

$sql='SHOW COLUMNS FROM `inv_sectors`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sector_address", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `inv_sectors` ADD `sector_address` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Office Address' AFTER `description`"; mysqli_query($conn,$sql); }

$cdesc = $_POST['idesc'];
$stype = $_POST['stype'];
$sunits = $_POST['sloc'];
$sector_address = $_POST['sector_address'];
$smanager = $_POST['shop_manager'];
$mobile = $_POST['shop_mobile'];
$state = $_POST['shop_state'];
$address = $_POST['shop_address'];
$email = $_POST['shop_email'];
if($_POST['brd_sflag'] == true || $_POST['brd_sflag'] == "on" || $_POST['brd_sflag'] == 1 || $_POST['brd_sflag'] == "1"){ $brd_sflag = 1; } else{ $brd_sflag = 0; }
if($_POST['lyr_sflag'] == true || $_POST['lyr_sflag'] == "on" || $_POST['lyr_sflag'] == 1 || $_POST['lyr_sflag'] == "1"){ $lyr_sflag = 1; } else{ $lyr_sflag = 0; }

$id = $_POST['idvalue'];
$sql ="SELECT * FROM `inv_sectors` WHERE `description` = '$cdesc' AND `id` NOT IN ('$id')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ ?>
	<script>
		var x = alert("The Description is already available \n Please check and try again ..!");
		if(x == true){ window.location.href = "broiler_display_office.php?ccid=".$ccid; } else if(x == false) { window.location.href = "broiler_display_office.php?ccid=".$ccid; } else { window.location.href = "broiler_display_office.php?ccid=".$ccid; }
	</script>
	<?php
}
else {
	$sql = "UPDATE `inv_sectors` SET `description` = '$cdesc',`sector_address` = '$sector_address',`type` = '$stype',`location` = '$sunits',`smanager` = '$smanager',`mobile` = '$mobile',`email` = '$email',`address` = '$address',`state` = '$state',`brd_sflag` = '$brd_sflag',`lyr_sflag` = '$lyr_sflag',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_office.php?ccid='.$ccid); }
}
?>
