<?php
//broiler_save_office.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['office'];

$cdesc = $_POST['idesc'];
$stype = $_POST['stype'];
$sunits = $_POST['sloc'];
$smanager = $_POST['shop_manager'];
$mobile = $_POST['shop_mobile'];
$state = $_POST['shop_state'];
$address = $_POST['shop_address'];
$email = $_POST['shop_email'];

$sql ="SELECT * FROM `inv_sectors` WHERE `description` = '$cdesc'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ ?>
	<script>
		var x = alert("The Description is already available \n Please check and try again ..!");
		if(x == true){ window.location.href = "broiler_display_office.php?ccid=".$ccid; } else if(x == false) { window.location.href = "broiler_display_office.php?ccid=".$ccid; } else { window.location.href = "broiler_display_office.php?ccid=".$ccid; }
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
	$sql = "INSERT INTO `inv_sectors` (incr,prefix,code,description,type,location,smanager,mobile,email,address,state,addedemp,addedtime) VALUES 
	('$incrs','$prefix','$code','$cdesc','$stype','$sunits','$smanager','$mobile','$email','$address','$state','$addedemp','$addedtime')";
	
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_office.php?ccid='.$ccid); }
}
?>
