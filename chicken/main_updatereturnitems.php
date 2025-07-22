<?php //main_updatereturnitems
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "cus_outbalfunction.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['dispitmrtn'];
$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Item Return' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
if($jals_flag == "" || $jals_flag == 0){ $jals_flag = 0; } if($birds_flag == "" || $birds_flag == 0){ $birds_flag = 0; }
if($_POST['submittrans'] == "addpage"){
	$mode = $date = $vcode = $inv_trnum = $sitem = $rtypes = $rsector = $sql = "";
	$sqty = $sjals = $sbirds = $rqty = $rprice = $ramount = 0;
	$itemcode = $sold_qty = $jals = $birds = $quantity = $price = $amount = $rtype = $warehouse = array();
	
	$mode = $_POST['vtype'];
	$date = date("Y-m-d",strtotime($_POST['rtn_date']));
	$vcode = $_POST['vendor'];
	$inv_trnum = $_POST['invno'];
	
	$i = 0; foreach($_POST['sitem'] as $sitem){ $i++; $itemcode[$i] = $sitem; }
	$i = 0; foreach($_POST['sqty'] as $sqty){ $i++; $sold_qty[$i] = $sqty; }
	if($jals_flag = 1){ $i = 0; foreach($_POST['sjals'] as $sjals){ $i++; $jals[$i] = $sjals; } } else{ }
	if($jals_flag = 1){ $i = 0; foreach($_POST['sbirds'] as $sbirds){ $i++; $birds[$i] = $sbirds; } } else{ }
	$i = 0; foreach($_POST['rqty'] as $rqty){ $i++; $quantity[$i] = $rqty; }
	$i = 0; foreach($_POST['rprice'] as $rprice){ $i++; $price[$i] = $rprice; }
	$i = 0; foreach($_POST['ramount'] as $ramount){ $i++; $amount[$i] = $ramount; }
	$i = 0; foreach($_POST['rtype'] as $rtypes){ $i++; $rtype[$i] = $rtypes; }
	$i = 0; foreach($_POST['rsector'] as $rsector){ $i++; $warehouse[$i] = $rsector; }
	
	$titem = sizeof($itemcode);
	
	$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
	
	if($mode == "Customer" || $mode == "customer"){
		$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $incr = $row['cus_return']; }
		$incr = $incr + 1;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$trnum = "SRI".$pfx."-".$incr; $prefix = "SRI";
		
		$sql = "UPDATE `master_generator` SET `cus_return` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	}
	else{
		$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $incr = $row['pur_return']; }
		$incr = $incr + 1;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$trnum = "PRI".$pfx."-".$incr; $prefix = "PRI";
		
		$sql = "UPDATE `master_generator` SET `pur_return` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	}
	
	for($i = 1;$i <= $titem;$i++){
		if($quantity[$i] != 0 || $quantity[$i] != 0.00 || $quantity[$i] != ""){
			if($jals[$i] == "" || $jals[$i] == NULL){ $jals[$i] = "0.00"; }
			if($birds[$i] == "" || $birds[$i] == NULL){ $birds[$i] = "0.00"; }
			if($quantity[$i] == "" || $quantity[$i] == NULL){ $quantity[$i] = "0.00"; }
			if($price[$i] == "" || $price[$i] == NULL){ $price[$i] = "0.00"; }
			if($amount[$i] == "" || $amount[$i] == NULL){ $amount[$i] = "0.00"; }
			$sql = "INSERT INTO `main_itemreturns` (incr,prefix,trnum,mode,date,inv_trnum,vcode,itemcode,jals,birds,quantity,price,amount,rtype,warehouse,flag,active,dflag,addedemp,addedtime,updatedtime) 
			VALUES('$incr','$prefix','$trnum','$mode','$date','$inv_trnum','$vcode','$itemcode[$i]','$jals[$i]','$birds[$i]','$quantity[$i]','$price[$i]','$amount[$i]','$rtype[$i]','$warehouse[$i]','0','1','0','$addedemp','$addedtime','$addedtime')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
	}
	header('location: main_displayreturnitems.php?cid='.$cid);
}
else if($_POST['submittrans'] == "updatepage"){
	$ids = $_POST['idvalue']; $incr = 0; $prefix = $trnum = $aemp = $atime = "";

	include_once("poulsoft_store_chngmaster.php");
	$chng_type = "Edit";
	$edit_file = "main_updatereturnitems.php";
	$mtbl_name = "main_itemreturns";
	$tno_cname = "trnum";
	$msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
	$message = json_encode($msg1);
	store_modified_details($message);

	$sql = "SELECT * FROM `main_itemreturns` WHERE `trnum` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
	}
	if($trnum != ""){
		$sql3 = "DELETE FROM `main_itemreturns` WHERE `trnum` = '$ids' AND `dflag` = '0'";
		if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
	}
	
	$mode = $_POST['vtype'];
	$inv_date = date("Y-m-d",strtotime($_POST['inv_date']));
	$date = date("Y-m-d",strtotime($_POST['rtn_date']));
	$vcode = $_POST['vendor'];
	$inv_trnum = $_POST['invno'];

	$itemcode = $jals = $birds = $quantity = $price = $amount = $rtype = $amount = $warehouse = array();
	$i = 0; foreach($_POST['sitem'] as $items){ $itemcode[$i] = $items; $i++; }
	$i = 0; foreach($_POST['sjals'] as $jalss){ $jals[$i] = $jalss; $i++; }
	$i = 0; foreach($_POST['sbirds'] as $birdss){ $birds[$i] = $birdss; $i++; }
	$i = 0; foreach($_POST['rqty'] as $quantitys){ $quantity[$i] = $quantitys; $i++; }
	$i = 0; foreach($_POST['rprice'] as $prices){ $price[$i] = $prices; $i++; }
	$i = 0; foreach($_POST['ramount'] as $amounts){ $amount[$i] = $amounts; $i++; }
	$i = 0; foreach($_POST['rtype'] as $rtypes){ $rtype[$i] = $rtypes; $i++; }
	$i = 0; foreach($_POST['rsector'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }

	$dsize = sizeof($itemcode);
	for($i = 0;$i < $dsize;$i++){
		if((float)$quantity[$i] > 0){
			if($jals[$i] == "" || $jals[$i] == NULL){ $jals[$i] = 0; }
			if($birds[$i] == "" || $birds[$i] == NULL){ $birds[$i] = 0; }
			if($quantity[$i] == "" || $quantity[$i] == NULL){ $quantity[$i] = 0; }
			if($price[$i] == "" || $price[$i] == NULL){ $price[$i] = 0; }
			if($amount[$i] == "" || $amount[$i] == NULL){ $amount[$i] = 0; }
			$sql = "INSERT INTO `main_itemreturns` (incr,prefix,trnum,mode,date,inv_trnum,vcode,itemcode,jals,birds,quantity,price,amount,rtype,warehouse,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
			VALUES('$incr','$prefix','$trnum','$mode','$date','$inv_trnum','$vcode','$itemcode[$i]','$jals[$i]','$birds[$i]','$quantity[$i]','$price[$i]','$amount[$i]','$rtype[$i]','$warehouse[$i]','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
	}
	header('location: main_displayreturnitems.php?cid='.$cid);
}
else{
	$id = $_GET['id'];
	$updatetype = $_GET['page'];
		
	if($updatetype == "edit"){ header('location:cus_editsales.php?id='.$id); }
		
	else if($updatetype == "delete"){

		include_once("poulsoft_store_chngmaster.php");
		$chng_type = "Delete";
		$edit_file = "main_updatereturnitems.php";
		$mtbl_name = "main_itemreturns";
		$tno_cname = "trnum";
		$msg1 = array("file"=>$edit_file, "trnum"=>$id, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
		$message = json_encode($msg1);
		store_modified_details($message);

		$sql ="SELECT * FROM `main_itemreturns` WHERE `trnum` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){
				$type = "Item Return";
				$date = $row['date'];
				$transactionno = $row['trnum'];
				$description = $row['mode'];
				$doccode = $row['inv_trnum'];
				$pcode = $row['vcode'];
				$icode = $row['itemcode'];
				$quantity = $row['quantity'];
				$amount = $row['amount'];
				$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
				VALUES('$type','$date','$transactionno','$description','$doccode','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
			$sql = "DELETE FROM `main_itemreturns` WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){
				die("Error:-".mysqli_error($conn));
			}
			else {
				header('location: main_displayreturnitems.php?cid='.$cid);
			}
		}
		else {
		?>
			<script>
			var x = alert("This transaction is already approved or used, \n kindly check the transaction");
			var cid = '<?php echo $cid; ?>';
			if(x == true){
				window.location.href = "main_displayreturnitems.php?cid="+cid;
			}
			else if(x == false) {
				window.location.href = "main_displayreturnitems.php?cid="+cid;
			}if($number > 0 && $baseUnit > 0){ $numBaseUnits = (int) ($number / $baseUnit); } else{ $numBaseUnits = 0; }
			else {
				window.location.href = "main_displayreturnitems.php?cid="+cid;
			}
			</script>
		<?php
		}
	}
	else if($updatetype == "activate"){
		$id = $_GET['id'];
		$sql = "UPDATE `main_itemreturns` SET `active` = '1' WHERE `trnum` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location: main_displayreturnitems.php?cid='.$cid); }
	}
	else if($updatetype == "pause"){
		$id = $_GET['id'];
		$sql = "UPDATE `main_itemreturns` SET `active` = '0' WHERE `trnum` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location: main_displayreturnitems.php?cid='.$cid); }
	}
	else if($updatetype == "authorize"){
		$id = $_GET['id'];
		$sql = "UPDATE `main_itemreturns` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `trnum` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location: main_displayreturnitems.php?cid='.$cid); }
	}
	else {}
	
}