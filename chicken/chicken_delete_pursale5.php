<?php
//chicken_delete_pursale5.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
//include "cus_sale_messages.php";
include "cus_outbalfunction.php";
include "pur_outbalfunction.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');

$client = $_SESSION['client'];
$cid = $_SESSION['pursale5'];
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }

else if($_GET['page'] == "delete"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){
		while($row = mysqli_fetch_assoc($query)){
			$type = "Purchase-Sales Invoice";
			$date = $row['date'];
			$transactionno = $row['invoice'];
			$link_trnum = $row['link_trnum'];
			$description = $row['itemcode'];
			$doccode = $row['bookinvoice'];
			$pcode = $row['customercode'];
			$icode = $row['itemcode'];
			$quantity = $row['netweight'];
			$amount = $row['finaltotal'];
			$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
			VALUES('$type','$date','$transactionno','$description','$link_trnum','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
		$sql ="SELECT * FROM `pur_purchase` WHERE `invoice` = '$link_trnum' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){
				$type = "Purchase-Sales Invoice";
				$date = $row['date'];
				$transactionno = $row['invoice'];
				$link_trnum2 = $row['link_trnum'];
				$description = $row['itemcode'];
				$doccode = $row['bookinvoice'];
				$pcode = $row['vendorcode'];
				$icode = $row['itemcode'];
				$quantity = $row['netweight'];
				$amount = $row['finaltotal'];
				$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
				VALUES('$type','$date','$transactionno','$description','$link_trnum2','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
		}
		$sql = "DELETE FROM `pur_purchase` WHERE `invoice` = '$link_trnum'";
		if(!mysqli_query($conn,$sql)){
			die("Error:-".mysqli_error($conn));
		}
		else {
			$sql = "DELETE FROM `customer_sales` WHERE `invoice` = '$id'";
			if(!mysqli_query($conn,$sql)){
				die("Error:-".mysqli_error($conn));
			}
			else {
				header('location:chicken_display_pursale5.php');
			}
		}
	}
	else {
	?>
<script>
var x = alert("This transaction is already approved or used, \n kindly check the transaction");
if(x == true){
	window.location.href = "chicken_display_pursale5.php";
}
else if(x == false) {
	window.location.href = "chicken_display_pursale5.php";
}
else {
	window.location.href = "chicken_display_pursale5.php";
}
</script>
	<?php
	}
}
else if($_GET['page'] == "activate"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['link_trnum']; } }
	$sql = "UPDATE `customer_sales` SET `active` = '1' WHERE `invoice` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_pursale5.php'); }
	
	$sql = "UPDATE `pur_purchase` SET `active` = '1' WHERE `invoice` = '$link_trnum'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_pursale5.php'); }
}
else if($_GET['page'] == "pause"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['link_trnum']; } }
	$sql = "UPDATE `customer_sales` SET `active` = '0' WHERE `invoice` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_pursale5.php'); }
	
	$sql = "UPDATE `pur_purchase` SET `active` = '0' WHERE `invoice` = '$link_trnum'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_pursale5.php'); }
}
else if($_GET['page'] == "authorize"){
	$id = $_GET['id'];
	$sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['link_trnum']; } }
	$sql = "UPDATE `customer_sales` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `invoice` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_pursale5.php'); }
	
	$sql = "UPDATE `pur_purchase` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `invoice` = '$link_trnum'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_pursale5.php'); }
}
else{
	header('location:chicken_display_pursale5.php');
}
?>