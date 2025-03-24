<?php
//inv_updateprtlprices.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['rtlprices'];

if($_POST['submittrans'] == "addpage"){
	$date = date("Y-m-d",strtotime($_POST['date']));
	$loc = $_POST['loc'];
	$i = 0; foreach($_POST['items'] as $icode){ $i = $i + 1; $itemcode[$i] = $icode; } $pcount = sizeof($itemcode);
	$i = 0; foreach($_POST['fromqty'] as $icode){ $i = $i + 1; $fqty[$i] = $icode; }
	$i = 0; foreach($_POST['toqty'] as $icode){ $i = $i + 1; $tqty[$i] = $icode; }
	$i = 0; foreach($_POST['prices'] as $icode){ $i = $i + 1; $price[$i] = $icode; }
	$i = 0; foreach($_POST['eflags'] as $icode){ echo $icode; $i = $i + 1; if($icode == true){ $eflag[$i] = "1"; } else{ $eflag[$i] = "0"; } }
	$sql = "SELECT MAX(incr) as incr FROM `retail_prices`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
	for($i = 1;$i <= $pcount;$i++){
		$incr++;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$prefix = "RPP"; $code = $prefix."-".$incr; if($eflag[$i] == ""){ $eflag[$i] = "0"; }
		$sql = "INSERT INTO `retail_prices` (incr,prefix,code,date,itemcode,fqty,tqty,price,eflag,loc,addedemp,addedtime,updatedtime,flag,active) 
		VALUES('$incr','$prefix','$code','$date','$itemcode[$i]','$fqty[$i]','$tqty[$i]','$price[$i]','$eflag[$i]','$loc','$addedemp','$addedtime','$addedtime','0','1')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	}
	?>
	<script>
		var a = '<?php echo $cid; ?>';
		var x = confirm("Would you like add one more Category ?");
		if(x == true){
			window.location.href = "inv_addprtlprices.php?cid="+a;
		}
		else if(x == false) {
			window.location.href = "inv_prtlprices.php?cid="+a;
		}
		else {
			window.location.href = "inv_prtlprices.php?cid="+a;
		}
	</script>
	<?php
}
else if($_POST['submittrans'] == "updatepage"){
	$code = $_POST['code'];
	$date = date("Y-m-d",strtotime($_POST['date'])); $loc = $_POST['loc']; $itemcode = $_POST['items']; $fqty = $_POST['fromqty']; $tqty = $_POST['toqty']; $price = $_POST['prices'];
	if($_POST['eflags'] == true || $_POST['eflags'] == "on"){ $eflag = "1"; } else{ $eflag = "0"; }
	$sql = "UPDATE `retail_prices` SET `date` = '$date',`loc` = '$loc',`itemcode` = '$itemcode',`fqty` = '$fqty',`tqty` = '$tqty',`price` = '$price',`eflag` = '$eflag',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_prtlprices.php?cid='.$cid); }
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "UPDATE `retail_prices` SET `dflag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_prtlprices.php?cid='.$cid); }
	}
	else if($page == "activate"){
		$sql = "UPDATE `retail_prices` SET `active` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_prtlprices.php?cid='.$cid); }
	}
	else if($page == "pause"){
		$sql = "UPDATE `retail_prices` SET `active` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_prtlprices.php?cid='.$cid); }
	}
	else if($page == "authorize"){
		$sql = "UPDATE `retail_prices` SET `flag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_prtlprices.php?cid='.$cid); }
	}
	else{
	
	}
}
?>