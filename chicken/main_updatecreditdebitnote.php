<?php
//pur_updatepayments.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	if($_POST['submittrans'] == "addpage"){
		foreach($_POST['vtype'] as $vtype){ $vtypes[] = $vtype; }
		foreach($_POST['cdtype'] as $cdtype){ $cdtypes[] = $cdtype; }
		foreach($_POST['pname'] as $pname){ $pnames[] = $pname; } $spnames = sizeof($pnames);
		foreach($_POST['pdate'] as $pdate){ $pdates[] = date("Y-m-d",strtotime($pdate)); $date = date("Y-m-d",strtotime($pdate)); }
		foreach($_POST['dcno'] as $dcno){ $dcnos[] = $dcno; }
		foreach($_POST['mode'] as $mode){ $modes[] = $mode; }
		foreach($_POST['amount'] as $amount){ $amounts[] = $amount; }
		foreach($_POST['reason_code'] as $reason_codes){ $reason_code[] = $reason_codes; }
		foreach($_POST['gtamtinwords'] as $amtinword){ $amtinwords[] = $amtinword; }
		foreach($_POST['sector'] as $sector){ $sectors[] = $sector; }
		foreach($_POST['remark'] as $remark){ $remarks[] = $remark; }
		//$gamts = $_POST['gtamt'];
		
		for($i = 0;$i < $spnames; $i++){
			$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$pdates[$i]' AND `tdate` >= '$pdates[$i]' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
			$incr = $crdrs = "";
			$trmode = $vtypes[$i]."".$cdtypes[$i];
			if($trmode == "CCN"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $incrvalue = $row['cuscredit']; } $incr = $incrvalue + 1;
				$sql = "UPDATE `master_generator` SET `cuscredit` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				$crdrs = "Dr";
			}
			else if($trmode == "CDN"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $incrvalue = $row['cusdebit']; } $incr = $incrvalue + 1;
				$sql = "UPDATE `master_generator` SET `cusdebit` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				$crdrs = "Cr";
			}
			else if($trmode == "SCN"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $incrvalue = $row['vencredit']; } $incr = $incrvalue + 1;
				$sql = "UPDATE `master_generator` SET `vencredit` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				$crdrs = "Dr";
			}
			else if($trmode == "SDN"){
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $incrvalue = $row['vendebit']; } $incr = $incrvalue + 1;
				$sql = "UPDATE `master_generator` SET `vendebit` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				$crdrs = "Cr";
			}
			else {
				$incr = "Invalid";
			}
			if($incr == "Invalid"){
				echo "Invalid Transaction=".$pdates[$i].",".$pnames[$i].",".$dcnos[$i].",".$modes[$i].",".$amounts[$i].",".$sectors[$i];
			}
			else {
				if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
				$code = $trmode."-".$fprefix."".$incr;
				//if($dcnos[$i] == "" || $dcnos[$i] == NULL){ $dcnos[$i] = NULL; } else{ }
				//if($remarks[$i] == "" || $remarks[$i] == NULL){ $remarks[$i] = NULL; } else{ }
				$sql = "INSERT INTO `main_crdrnote` (incr,mode,trnum,date,ccode,docno,coa,crdr,amount,reason_code,amtinwords,balance,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
				VALUES ('$incr','$trmode','$code','$pdates[$i]','$pnames[$i]','$dcnos[$i]','$modes[$i]','$crdrs','$amounts[$i]','$reason_code[$i]','$amtinwords[$i]','$amounts[$i]','$vtypes[$i]','$sectors[$i]','$remarks[$i]','0','1','$addedemp','$addedtime','0','0','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			}
		}
		header('location:main_displaycreditdebitnote.php');
	}
	else if($_POST['submittrans'] == "updatepage"){
		$pnames = $_POST['pname']; $pdates = date("Y-m-d",strtotime($_POST['pdate'])); $modes = $_POST['mode'];
		$amounts = $_POST['amount']; $reason_code = $_POST['reason_code']; $amtinwords = $_POST['gtamtinwords']; $dcnos = $_POST['dcno']; $sectors = $_POST['sector']; $trnums = $_POST['trnum']; $remarks = $_POST['remark'];
		$sql = "UPDATE `main_crdrnote` SET `date` = '$pdates',`ccode` = '$pnames',`docno` = '$dcnos',`coa` = '$modes',`amount` = '$amounts',`reason_code` = '$reason_code',`amtinwords` = '$amtinwords',`balance` = '$amounts',`warehouse` = '$sectors',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnums'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycreditdebitnote.php'); }
		
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:main_editcreditdebitnote.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `main_crdrnote` WHERE `trnum` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){
					$type = "CrDr Note";
					$date = $row['date'];
					$transactionno = $row['trnum'];
					$description = $row['itemcode']; if($description == "" || $description == NULL){ $description = 0; }
					$doccode = $row['docno']; if($doccode == "" || $doccode == NULL){ $doccode = 0; }
					$pcode = $row['ccode'];
					$icode = $row['method']; if($icode == "" || $icode == NULL){ $icode = 0; }
					$quantity = "0.00000";
					$amount = $row['amount'];
					$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,description,doccode,pcode,icode,quantity,amount,empcode,client) 
					VALUES('$type','$date','$transactionno','$description','$doccode','$pcode','$icode','$quantity','$amount','$addedemp','$client')";
					if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
				}
				$sql = "DELETE FROM `main_crdrnote` WHERE `trnum` = '$id'";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					header('location:main_displaycreditdebitnote.php');
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "main_displaycreditdebitnote.php";
				}
				else if(x == false) {
					window.location.href = "main_displaycreditdebitnote.php";
				}
				else {
					window.location.href = "main_displaycreditdebitnote.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$id = $_GET['id'];
			$sql = "UPDATE `main_crdrnote` SET `active` = '1' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycreditdebitnote.php'); }
		}
		else if($updatetype == "pause"){
			$id = $_GET['id'];
			$sql = "UPDATE `main_crdrnote` SET `active` = '0' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycreditdebitnote.php'); }
		}
		else if($updatetype == "authorize"){
			$id = $_GET['id'];
			$sql = "UPDATE `main_crdrnote` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycreditdebitnote.php'); }
		}
		else {}
	}
?>