<?php
//inv_updatecusprices2.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['dcusprices2'];
$db_name = $_SESSION['dbase'];
$today = date("Y-m-d");
if($_POST['submittrans'] == "addpage"){
	$a = $_POST['incr']; $prefix = "CPP";
	$sql = "SELECT MAX(incr) as incr FROM `customer_price`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
	
	for($i = 0;$i <= $a;$i++){
		$ccode = $_POST['ccode'.$i];
		$idesc = $_POST['idesc'.$i];
		$ptype = $_POST['ptype'.$i];
		$ptype2 = $_POST['ptype2'.$i];
		$ivalue = $_POST['iprice'.$i]; if($ivalue == "" || $ivalue == "0" || $ivalue == "0.00" || $ivalue == 0){ $ivalue = 0; }
		$ivalue2 = $_POST['iprice2'.$i]; if($ivalue2 == "" || $ivalue2 == "0" || $ivalue2 == "0.00" || $ivalue2 == 0){ $ivalue2 = 0; }
		
		$sql = "SELECT * FROM `customer_price` WHERE `date` = '$today' AND `ccode` = '$ccode' AND `itemcode` = '$idesc' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $vcount = mysqli_num_rows($query);
		if($vcount > 0){
			$sql = "UPDATE `customer_price` SET `price_type` = '$ptype',`value` = '$ivalue',`price_type2` = '$ptype2',`value2` = '$ivalue2',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `date` = '$today' AND `itemcode` = '$idesc' AND `ccode` = '$ccode' AND `active` = '1'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
		else{
			$incr++;
			if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
			$code = $prefix."-".$incr;
			$sql = "INSERT INTO `customer_price` (incr,prefix,code,date,ccode,itemcode,price_type,value,price_type2,value2,flag,active,dflag,addedemp,addedtime,updatedtime) 
			VALUES('$incr','$prefix','$code','$today','$ccode','$idesc','$ptype','$ivalue','$ptype2','$ivalue2','0','1','0','$addedemp','$addedtime','$addedtime')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
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
			window.location.href = "main_displaycusprices2.php?cid="+a;
		}
		else {
			window.location.href = "main_displaycusprices2.php?cid="+a;
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
	$ptype2 = $_POST['ptype2'];
	$ivalue2 = $_POST['iprice2'];
	$sql = "UPDATE `customer_price` SET `price_type` = '$ptype',`price_type2` = '$ptype2',`itemcode` = '$idesc',`value` = '$ivalue',`value2` = '$ivalue2',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$code' AND `active` = '1'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices2.php?cid='.$cid); }
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "UPDATE `customer_price` SET `dflag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices2.php?cid='.$cid); }
	}
	else if($page == "activate"){
		$sql = "UPDATE `customer_price` SET `active` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices2.php?cid='.$cid); }
	}
	else if($page == "pause"){
		$sql = "UPDATE `customer_price` SET `active` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices2.php?cid='.$cid); }
	}
	else if($page == "authorize"){
		$sql = "UPDATE `customer_price` SET `flag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycusprices2.php?cid='.$cid); }
	}
	else{
	
	}
}
?>