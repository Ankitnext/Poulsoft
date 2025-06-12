<?php
//pur_updatepayments.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	if($_POST['submittrans'] == "addpage"){
		foreach($_POST['pdate'] as $pdate){ $pdates[] = date("Y-m-d",strtotime($pdate)); $date = date("Y-m-d",strtotime($pdate)); }
		foreach($_POST['dcno'] as $dcno){ $dcnos[] = $dcno; }
		foreach($_POST['fsector'] as $fsector){ $fsectors[] = $fsector; } $spnames = sizeof($fsectors);
		foreach($_POST['code'] as $code){ $codes[] = $code; }
		foreach($_POST['jalqty'] as $jalqtys){ $jalqty[] = $jalqtys; }
		foreach($_POST['birdqty'] as $birdqtys){ $birdqty[] = $birdqtys; }
		foreach($_POST['qty'] as $qty){ $qtys[] = $qty; }
		foreach($_POST['price'] as $price){ $prices[] = $price; }
		foreach($_POST['tsector'] as $tsector){ $tsectors[] = $tsector; }
		foreach($_POST['remark'] as $remark){ $remarks[] = $remark; }
		//$gamts = $_POST['gtamt'];
		$stsize = sizeof($pdates);
		$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $stktransfer = $row['stktransfer']; } $incr = $stktransfer + $stsize;
		$sql = "UPDATE `master_generator` SET `stktransfer` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		$incr = $stktransfer + 1;
		
		for($i = 0;$i < $spnames; $i++){
			$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$pdates[$i]' AND `tdate` >= '$pdates[$i]' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
			if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
			$code = "ST-".$fprefix."".$incr;
			
			if($jalqty[$i] == "" || $jalqty[$i] == NULL){ $jalqty[$i] = 0; }
			if($birdqty[$i] == "" || $birdqty[$i] == NULL){ $birdqty[$i] = 0; }
			
			
			$sql = "INSERT INTO `item_stocktransfers` (incr,prefix,trnum,date,fromwarehouse,code,jals,birds,quantity,price,towarehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
			VALUES ('$incr','ST','$code','$pdates[$i]','$fsectors[$i]','$codes[$i]','$jalqty[$i]','$birdqty[$i]','$qtys[$i]','$prices[$i]','$tsectors[$i]','$dcnos[$i]','$remarks[$i]','0','1','$addedemp','$addedtime','0','0','$client')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
			$incr++;
		}
		if($i == $spnames){
			header('location:main_displaystocktransfer.php');
		}
		else {
			echo "Error:-".mysqli_error($conn);
		}
	}
	else if($_POST['submittrans'] == "updatepage"){
		$trnums = $_POST['trnum']; $pdates = date("Y-m-d",strtotime($_POST['pdate'])); $dcnos = $_POST['dcno']; $fsectors = $_POST['fsector'];
		$codes = $_POST['code']; $qtys = $_POST['qty']; $prices = $_POST['price']; $tsectors = $_POST['tsector']; $remarks = $_POST['remark'];
		$jalqty = $_POST['jalqty']; $birdqty = $_POST['birdqty'];
		
		if($jalqty == "" || $jalqty == NULL){ $jalqty = 0; }
		if($birdqty == "" || $birdqty == NULL){ $birdqty = 0; }
			
		$sql = "UPDATE `item_stocktransfers` SET `date` = '$pdates',`code` = '$codes',`dcno` = '$dcnos',`fromwarehouse` = '$fsectors',`jals` = '$jalqty',`birds` = '$birdqty',`quantity` = '$qtys',`price` = '$prices',`towarehouse` = '$tsectors',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnums'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaystocktransfer.php'); }
		
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:main_editstocktransfer.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){
					$type = "Stock Transfer";
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
				$sql = "DELETE FROM `item_stocktransfers` WHERE `trnum` = '$id'";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					header('location:main_displaystocktransfer.php');
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "main_displaystocktransfer.php";
				}
				else if(x == false {
					window.location.href = "main_displaystocktransfer.php";
				}
				else {
					window.location.href = "main_displaystocktransfer.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_stocktransfers` SET `active` = '1' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaystocktransfer.php'); }
		}
		else if($updatetype == "pause"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_stocktransfers` SET `active` = '0' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaystocktransfer.php'); }
		}
		else if($updatetype == "authorize"){
			$id = $_GET['id'];
			$sql = "UPDATE `item_stocktransfers` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `trnum` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaystocktransfer.php'); }
		}
		else {}
	}
?>