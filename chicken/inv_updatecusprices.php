<?php
//inv_updatecusprices.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['rcoamap'];
$db_name = $_SESSION['dbase'];
$today = date("Y-m-d");

//Fetch Column From Table
$sql='SHOW COLUMNS FROM `customer_price`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("freight_perjals", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_price` ADD `freight_perjals` VARCHAR(300) NULL DEFAULT NULL COMMENT ''"; mysqli_query($conn,$sql); }
if(in_array("days", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_price` ADD `days` VARCHAR(300) NULL DEFAULT NULL COMMENT ''"; mysqli_query($conn,$sql); }
if(in_array("days_ptype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_price` ADD `days_ptype` VARCHAR(300) NULL DEFAULT NULL COMMENT ''"; mysqli_query($conn,$sql); }
if(in_array("days_iprice", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_price` ADD `days_iprice` VARCHAR(300) NULL DEFAULT NULL COMMENT ''"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `master_itemfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cus_disc_days_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `cus_disc_days_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Customer prices, display days for extra discount'"; mysqli_query($conn,$sql); }
if(in_array("cus_jalsfreight_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `cus_jalsfreight_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Customer prices, display freight per Jals'"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds'";  $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cus_disc_days_flag = $row['cus_disc_days_flag']; $cus_jalsfreight_flag = $row['cus_jalsfreight_flag']; }
if($cus_disc_days_flag == "" || $cus_disc_days_flag == "0.00" || $cus_disc_days_flag == "" || $cus_disc_days_flag == "0" || $cus_disc_days_flag == 0){ $cus_disc_days_flag = 0; }
if($cus_jalsfreight_flag == "" || $cus_jalsfreight_flag == "0.00" || $cus_jalsfreight_flag == "" || $cus_jalsfreight_flag == "0" || $cus_jalsfreight_flag == 0){ $cus_jalsfreight_flag = 0; }

if($_POST['submittrans'] == "addpage"){
	$a = $_POST['incr']; $prefix = "CPP";
	$sql = "SELECT MAX(incr) as incr FROM `customer_price`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
	
	for($i = 0;$i <= $a;$i++){
		$ccode = $_POST['ccode'.$i];
		$idesc = $_POST['idesc'.$i];
		$ptype = $_POST['ptype'.$i];
		$ivalue = $_POST['iprice'.$i];

		$days = $_POST['days'.$i];
		$days_ptype = $_POST['days_ptype'.$i];
		$days_iprice = $_POST['days_iprice'.$i];
		$freight_perjals = $_POST['freight_perjals'.$i];

		if($ivalue == NULL || $ivalue == ""){ $ivalue = 0; }
		if($days_iprice == NULL || $days_iprice == ""){ $days_iprice = 0; }
		if($freight_perjals == NULL || $freight_perjals == ""){ $freight_perjals = 0; }

		$sql = "SELECT * FROM `customer_price` WHERE `date` = '$today' AND `days` = '$days' AND `ccode` = '$ccode' AND `itemcode` = '$idesc' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $vcount = mysqli_num_rows($query);
		if($vcount > 0){
			while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }
			$sql = "UPDATE `customer_price` SET `price_type` = '$ptype',`value` = '$ivalue',`freight_perjals` = '$freight_perjals',`days` = '$days',`days_ptype` = '$days_ptype',`days_iprice` = '$days_iprice',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `date` = '$today' AND `itemcode` = '$idesc' AND `ccode` = '$ccode' AND `active` = '1'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
		else{
			$incr++;
			if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
			$code = $prefix."-".$incr;
			$sql = "INSERT INTO `customer_price` (incr,prefix,code,date,ccode,itemcode,price_type,value,freight_perjals,days,days_ptype,days_iprice,flag,active,dflag,addedemp,addedtime,updatedtime) 
			VALUES('$incr','$prefix','$code','$today','$ccode','$idesc','$ptype','$ivalue','$freight_perjals','$days','$days_ptype','$days_iprice','0','1','0','$addedemp','$addedtime','$addedtime')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}

		if($cus_disc_days_flag == 1 || $cus_disc_days_flag == "1"){
			$sql = "SELECT * FROM `customer_price2` WHERE `date` = '$today' AND `days` = '$days' AND `ccode` = '$ccode' AND `itemcode` = '$idesc' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $vcount = mysqli_num_rows($query);
			if($vcount > 0){
				$sql = "UPDATE `customer_price2` SET `code` = '$code',`days` = '$days',`days_ptype` = '$days_ptype',`days_iprice` = '$days_iprice' WHERE `date` = '$today' AND `days` = '$days' AND `itemcode` = '$idesc' AND `ccode` = '$ccode' AND `active` = '1' AND `dflag` = '0'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
			else{
				$sql = "INSERT INTO `customer_price2` (code,date,ccode,itemcode,days,days_ptype,days_iprice,flag,active,dflag) 
				VALUES('$code','$today','$ccode','$idesc','$days','$days_ptype','$days_iprice','0','1','0')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
		}
	}
	?>
	<script>
		var a = '<?php echo $cid; ?>';
		var x = confirm("Would you like add more Customer Prices ?");
		if(x == true){
			window.location.href = "inv_addcusprices.php?cid="+a;
		}
		else if(x == false) {
			window.location.href = "main_displaycusprices.php?cid="+a;
		}
		else {
			window.location.href = "main_displaycusprices.php?cid="+a;
		}
	</script>
	<?php
}
else if($_POST['submittrans'] == "updatepage"){
	$code = $_POST['code'];
	$ccode = $_POST['ccode'];
	$idesc = $_POST['idesc'];
	$prate = $_POST['prate'];
	$ptype = $_POST['ptype'];
	$ivalue = $_POST['iprice'];

	$days = $_POST['days'];
	$days_ptype = $_POST['days_ptype'];
	$days_iprice = $_POST['days_iprice'];
	$freight_perjals = $_POST['freight_perjals'];

	if($ivalue == NULL || $ivalue == ""){ $ivalue = 0; }
	if($days_iprice == NULL || $days_iprice == ""){ $days_iprice = 0; }
	if($freight_perjals == NULL || $freight_perjals == ""){ $freight_perjals = 0; }
	$sql = "UPDATE `customer_price` SET `price_type` = '$ptype',`itemcode` = '$idesc',`value` = '$ivalue',`freight_perjals` = '$freight_perjals',`days` = '$days',`days_ptype` = '$days_ptype',`days_iprice` = '$days_iprice',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$code' AND `active` = '1'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices.php?cid='.$cid); }

	if($cus_disc_days_flag == 1 || $cus_disc_days_flag == "1"){
		$sql = "UPDATE `customer_price2` SET `days` = '$days',`days_ptype` = '$days_ptype',`days_iprice` = '$days_iprice' WHERE `code` = '$code' AND `active` = '1'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices.php?cid='.$cid); }
	}
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "UPDATE `customer_price` SET `active` = '0',`dflag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
		else {
			$sql = "UPDATE `customer_price2` SET `active` = '0',`dflag` = '1' WHERE `code` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices.php?cid='.$cid); }
		}
	}
	else if($page == "activate"){
		$sql = "UPDATE `customer_price` SET `active` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
		else {
			$sql = "UPDATE `customer_price2` SET `active` = '1' WHERE `code` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices.php?cid='.$cid); }
		}
	}
	else if($page == "pause"){
		$sql = "UPDATE `customer_price` SET `active` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
		else {
			$sql = "UPDATE `customer_price2` SET `active` = '0' WHERE `code` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices.php?cid='.$cid); }
		}
	}
	else if($page == "authorize"){
		$sql = "UPDATE `customer_price` SET `flag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
		else {
			$sql = "UPDATE `customer_price2` SET `flag` = '1' WHERE `code` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices.php?cid='.$cid); }
		}
	}
	else{
	
	}
}
?>