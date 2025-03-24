<?php
//main_updatedcustomeraccess.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['cusacc'];
$db_name = $_SESSION['dbase'];
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$cus_code[$row['code']] = $row['code'];
	$cus_name[$row['code']] = $row['name'];
	$cus_mobl[$row['code']] = $row['mobileno'];
	$cus_ctype[$row['code']] = $row['contacttype'];
}
if($_POST['submittrans'] == "addpage"){
	$i = 0; foreach($_POST['active'] as $active){
		$i = $i + 1;
		$acc_code[$i] = $active;
		$acc_mble[$i] = $_POST[$active];
		if($_POST["slsodr".$active] == true || $_POST["slsodr".$active] == "on"){ $screens[$i] = "salesorder"; } else{ $screens[$i] = ""; }
		if($_POST["sales".$active] == true || $_POST["sales".$active] == "on"){ $screensfour[$i] = "salesreport"; } else{ $screensfour[$i] = ""; }
		if($_POST["receipts".$active] == true || $_POST["receipts".$active] == "on"){ $screensthree[$i] = "receiptreport"; } else{ $screensthree[$i] = ""; }
		if($_POST["sorder".$active] == true || $_POST["sorder".$active] == "on"){ $screensfive[$i] = "salesorderreport"; } else{ $screensfive[$i] = ""; }
		if($_POST["ledger".$active] == true || $_POST["ledger".$active] == "on"){ $screenstwo[$i] = "cl"; } else{ $screenstwo[$i] = ""; }
		if($_POST["ledger_new".$active] == true || $_POST["ledger_new".$active] == "on"){ if($_POST["ledger".$active] == true || $_POST["ledger".$active] == "on"){ $screenstwo[$i] .= ",cl_per"; }else{$screenstwo[$i] .= "cl_per";} } else{ $screenstwo[$i] = ""; }
	}
	$sql = "SELECT MAX(incr) as incr FROM `common_customeraccess`"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = (int)$row['incr']; }
	$prefix = "USR";
	$asize = sizeof($acc_code);
	for($i = 1;$i <= $asize;$i++){
		$incr = $incr + 1;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$code = $prefix."-".$incr;
		$user_name = $cus_name[$acc_code[$i]];
		$ccode = $cus_code[$acc_code[$i]];
		$mobile = $acc_mble[$i];
		$user_type = $cus_ctype[$acc_code[$i]];
		$sql = "INSERT INTO `common_customeraccess` (incr,prefix,code,user_name,mobile,ccode,db_name,client,active_status,user_type,screens,screenstwo,screensthree,screensfour,screensfive,addedempcode,createddatetime,updateddatetime) 
		VALUES('$incr','$prefix','$code','$user_name','$mobile','$ccode','$db_name','$client','1','$user_type','$screens[$i]','$screenstwo[$i]','$screensthree[$i]','$screensfour[$i]','$screensfive[$i]','$addedemp','$addedtime','$addedtime')";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); }
		else {
			if($mobile != $cus_mobl[$ccode]){
				$sql = "UPDATE `main_contactdetails` SET `mobileno` = '$mobile' WHERE `code` = '$ccode'";
				if(!mysqli_query($conn,$sql)){ die("User Mobile Error:-".mysqli_error($conn)); } else { }
			}
		}
	}
	?>
	<script>
		var a = '<?php echo $cid; ?>';
		var x = confirm("Would you like add more Customers ?");
		if(x == true){
			window.location.href = "main_addcustomeraccess.php?cid="+a;
		}
		else if(x == false) {
			window.location.href = "main_displaycustomeraccess.php?cid="+a;
		}
		else {
			window.location.href = "main_displaycustomeraccess.php?cid="+a;
		}
	</script>
	<?php
}
else if($_POST['submittrans'] == "updatepage"){
	$i = 0; foreach($_POST['active'] as $active){
		$i = $i + 1;
		$acc_code[$i] = $active;
		$acc_mble[$i] = $_POST[$active];
		if($_POST["slsodr".$active] == true || $_POST["slsodr".$active] == "on"){ $screens[$i] = "salesorder"; } else{ $screens[$i] = ""; }
		if($_POST["sales".$active] == true || $_POST["sales".$active] == "on"){ $screensfour[$i] = "salesreport"; } else{ $screensfour[$i] = ""; }
		if($_POST["receipts".$active] == true || $_POST["receipts".$active] == "on"){ $screensthree[$i] = "receiptreport"; } else{ $screensthree[$i] = ""; }
		if($_POST["sorder".$active] == true || $_POST["sorder".$active] == "on"){ $screensfive[$i] = "salesorderreport"; } else{ $screensfive[$i] = ""; }
		if($_POST["ledger".$active] == true || $_POST["ledger".$active] == "on"){ $screenstwo[$i] = "cl"; } else{ $screenstwo[$i] = ""; }
		if($_POST["ledger_new".$active] == true || $_POST["ledger_new".$active] == "on"){ if($_POST["ledger".$active] == true || $_POST["ledger".$active] == "on"){ $screenstwo[$i] .= ",cl_per"; }else{$screenstwo[$i] .= "cl_per";} } else{ $screenstwo[$i] = ""; }
	}
	$sql = "SELECT MAX(incr) as incr FROM `common_customeraccess`"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = (int)$row['incr']; }
	$prefix = "USR";
	$asize = sizeof($acc_code);
	for($i = 1;$i <= $asize;$i++){
		$incr = $incr + 1;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$code = $prefix."-".$incr;
		$user_name = $cus_name[$acc_code[$i]];
		$ccode = $cus_code[$acc_code[$i]];
		$mobile = $acc_mble[$i];
		$user_type = $cus_ctype[$acc_code[$i]];
		$sql = "UPDATE `common_customeraccess` SET `mobile` = '$mobile',`screens` = '$screens[$i]',`screenstwo` = '$screenstwo[$i]',`screensthree` = '$screensthree[$i]',`screensfour` = '$screensfour[$i]',`screensfive` = '$screensfive[$i]',`updatedempcode` = '$addedemp',`updateddatetime` = '$addedtime' WHERE `ccode` = '$ccode' AND `db_name` = '$db_name'";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); }
		else {
			if($mobile != $cus_mobl[$ccode]){
				$sql = "UPDATE `main_contactdetails` SET `mobileno` = '$mobile' WHERE `code` = '$ccode'";
				if(!mysqli_query($conn,$sql)){ die("User Mobile Error:-".mysqli_error($conn)); } else { }
			}
		}
	}
	header('location:main_displaycustomeraccess.php?cid='.$cid);
}
else if($_POST['submittrans'] == "updatemultiplepage"){
	$i = 0; foreach($_POST['active'] as $active){
		$i = $i + 1;
		$acc_code[$i] = $active;
		$acc_mble[$i] = $_POST[$active];
		if($_POST["slsodr".$active] == true || $_POST["slsodr".$active] == "on"){ $screens[$i] = "salesorder"; } else{ $screens[$i] = ""; }
		if($_POST["sales".$active] == true || $_POST["sales".$active] == "on"){ $screensfour[$i] = "salesreport"; } else{ $screensfour[$i] = ""; }
		if($_POST["receipts".$active] == true || $_POST["receipts".$active] == "on"){ $screensthree[$i] = "receiptreport"; } else{ $screensthree[$i] = ""; }
		if($_POST["sorder".$active] == true || $_POST["sorder".$active] == "on"){ $screensfive[$i] = "salesorderreport"; } else{ $screensfive[$i] = ""; }
		if($_POST["ledger".$active] == true || $_POST["ledger".$active] == "on"){ $screenstwo[$i] = "cl"; } else{ $screenstwo[$i] = ""; }
	}
	$sql = "SELECT MAX(incr) as incr FROM `common_customeraccess`"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = (int)$row['incr']; }
	$prefix = "USR";
	$asize = sizeof($acc_code);
	for($i = 1;$i <= $asize;$i++){
		$incr = $incr + 1;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$code = $prefix."-".$incr;
		$user_name = $cus_name[$acc_code[$i]];
		$ccode = $cus_code[$acc_code[$i]];
		$mobile = $acc_mble[$i];
		$user_type = $cus_ctype[$acc_code[$i]];
		$sql = "UPDATE `common_customeraccess` SET `mobile` = '$mobile',`screens` = '$screens[$i]',`screenstwo` = '$screenstwo[$i]',`screensthree` = '$screensthree[$i]',`screensfour` = '$screensfour[$i]',`screensfive` = '$screensfive[$i]',`updatedempcode` = '$addedemp',`updateddatetime` = '$addedtime' WHERE `ccode` = '$ccode' AND `db_name` = '$db_name'";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); }
		else {
			if($mobile != $cus_mobl[$ccode]){
				$sql = "UPDATE `main_contactdetails` SET `mobileno` = '$mobile' WHERE `code` = '$ccode'";
				if(!mysqli_query($conn,$sql)){ die("User Mobile Error:-".mysqli_error($conn)); } else { }
			}
		}
	}
	header('location:main_displaycustomeraccess.php?cid='.$cid);
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "UPDATE `common_customeraccess` SET `active_status` = '0' WHERE `id` = '$id'";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); } else { header('location:main_displaycustomeraccess.php?cid='.$cid); }
	}
	else if($page == "activate"){
		$sql = "UPDATE `common_customeraccess` SET `active_status` = '1' WHERE `id` = '$id'";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); } else { header('location:main_displaycustomeraccess.php?cid='.$cid); }
	}
	else if($page == "pause"){
		$sql = "UPDATE `common_customeraccess` SET `active_status` = '0' WHERE `id` = '$id'";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); } else { header('location:main_displaycustomeraccess.php?cid='.$cid); }
	}
	else if($page == "authorize"){
		$sql = "UPDATE `common_customeraccess` SET `active_status` = '1' WHERE `id` = '$id'";
		if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); } else { header('location:main_displaycustomeraccess.php?cid='.$cid); }
	}
	else{
	
	}
}
?>