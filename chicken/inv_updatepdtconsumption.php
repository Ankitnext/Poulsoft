<?php
//inv_updatepdtconsumption.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['pdtcons'];

if($_POST['submittrans'] == "addpage"){
	$cat_incr = $_POST['incr'];
	
	for($i = 0;$i <= $cat_incr;$i++){
		if($from_cat[$i] == ""){
			$from_cat[$i] = $_POST['from_cat'.$i];
		}
		else{
			$from_cat[$i] = $from_cat[$i]."','".$_POST['from_cat'.$i];
		}
		if($from_item[$i] == ""){
			$from_item[$i] = $_POST['from_item'.$i];
		}
		else{
			$from_item[$i] = $from_item[$i]."','".$_POST['from_item'.$i];
		}
		if($to_cat[$i] == ""){
			$to_cat[$i] = $_POST['to_cat'.$i];
		}
		else{
			$to_cat[$i] = $to_cat[$i]."','".$_POST['to_cat'.$i];
		}
		foreach($_POST['to_item'.$i] as $rshop){
			if($to_item[$i] == ""){
				$to_item[$i] = $rshop;
			}
			else{
				$to_item[$i] = $to_item[$i].",".$rshop;
			}
		}
	}
	$sql = "SELECT MAX(incr) as incr FROM `retail_item_conversion`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
	for($i = 0;$i <= $cat_incr;$i++){
		$incr++;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$prefix = "CNV"; $code = $prefix."-".$incr;
		$sql = "INSERT INTO `retail_item_conversion` (incr,prefix,code,fromcat,fromcode,tocat,tocode,addedemp,addedtime,updatedtime,flag,active) 
		VALUES('$incr','$prefix','$code','$from_cat[$i]','$from_item[$i]','$to_cat[$i]','$to_item[$i]','$addedemp','$addedtime','$addedtime','0','1')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	}
	?>
	<script>
		var a = '<?php echo $cid; ?>';
		var x = confirm("Would you like add one more Category ?");
		if(x == true){
			window.location.href = "inv_addpdtconsumption.php?cid="+a;
		}
		else if(x == false) {
			window.location.href = "inv_pdtconsumption.php?cid="+a;
		}
		else {
			window.location.href = "inv_pdtconsumption.php?cid="+a;
		}
	</script>
	<?php
}
else if($_POST['submittrans'] == "updatepage"){
	$code = $_POST['trcode']; $from_cat = $_POST['from_cat']; $from_item = $_POST['from_item']; $to_cat = $_POST['to_cat']; $to_code = "";
	foreach($_POST['to_item'] as $tcode){ if($to_code == ""){ $to_code = $tcode; } else{ $to_code = $to_code.",".$tcode; } }
	
	$sql = "UPDATE `retail_item_conversion` SET `fromcat` = '$from_cat',`fromcode` = '$from_item',`tocat` = '$to_cat',`tocode` = '$to_code',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_pdtconsumption.php?cid='.$cid); }
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "UPDATE `retail_item_conversion` SET `dflag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_pdtconsumption.php?cid='.$cid); }
	}
	else if($page == "activate"){
		$sql = "UPDATE `retail_item_conversion` SET `active` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_pdtconsumption.php?cid='.$cid); }
	}
	else if($page == "pause"){
		$sql = "UPDATE `retail_item_conversion` SET `active` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_pdtconsumption.php?cid='.$cid); }
	}
	else if($page == "authorize"){
		$sql = "UPDATE `retail_item_conversion` SET `flag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_pdtconsumption.php?cid='.$cid); }
	}
	else{
	
	}
}
?>