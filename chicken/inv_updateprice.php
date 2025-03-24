<?php
//inv_updateprice.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	
	if($_GET['substdprice'] == "addpage"){
		$ptype = "SDP";
		$c = 0;
		$fdate = date("Y-m-d",strtotime($_GET['fdate']));
		foreach ($_GET['stdidesc'] as $itemcat){
			$c = $c + 1;
			$icode[$c] = $itemcat;
		}
		$d = 0;
		foreach ($_GET['stdiprice'] as $itemcat){
			$d = $d + 1;
			$price[$d] = $itemcat;
		}
		$ssize = sizeof($icode);
		$c = $a = 0;
		for($i = 1; $i <= $ssize;$i++){
			if($price[$i] == "" || $price[$i] == "0.00"){ }
			else {
				$c = $c + 1;
				$sql = "INSERT INTO `item_pricemaster` (fdate,ptype,icode,price,addedemp,addedtime,client) VALUES ('$fdate','$ptype','$icode[$i]','$price[$i]','$addedemp','$addedtime','$client')";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { $a = $a + 1; }
				if($c == $a){
					header('location:main_displayprices.php');
				}
			}
		}
		header('location:main_displayprices.php');
	}
	else if($_GET['substdprice'] == "updatepage"){
		$id = $_GET['id'];
		$ptype = "SDP";
		$c = 0;
		$fdate = date("Y-m-d",strtotime($_GET['fdate']));
		foreach ($_GET['stdidesc'] as $itemcat){
			$c = $c + 1;
			$icode[$c] = $itemcat;
		}
		$d = 0;
		foreach ($_GET['stdiprice'] as $itemcat){
			$d = $d + 1;
			$price[$d] = $itemcat;
		}
		$ssize = sizeof($icode);
		$c = $a = 0;
		for($i = 1; $i <= $ssize;$i++){
			if($price[$i] == "" || $price[$i] == "0.00"){ }
			else {
				$c = $c + 1;
				$sql = "UPDATE `item_pricemaster` SET `ptype` = '$ptype',`fdate` = '$fdate',`tdate` = NULL,`ccode` = NULL,`icode` = '$icode[$i]',`unit` = NULL,`pbasis` = NULL,`price` = '$price[$i]',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { $a = $a + 1; }
				if($c == $a){
					header('location:main_displayprices.php');
				}
			}
		}
		header('location:main_displayprices.php');
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:inv_edititempricemasters.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `item_pricemaster` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $catcode = $row['icode']; $fdate = $row['fdate']; $price = $row['price']; $catdesc = $row['ccode']; }
				$sql = "INSERT INTO `main_deletiondetails` (type,date,icode,pcode,amount,empcode) VALUES('Price Master','$fdate','$catcode','$catdesc','$price','$addedemp')";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					$sql = "DELETE FROM `item_pricemaster` WHERE `id` = '$id'";
					if(!mysqli_query($conn,$sql)){
						die("Error:-".mysqli_error($conn));
					}
					else {
						header('location:main_displayprices.php');
					}
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "main_displayprices.php";
				}
				else if(x == false {
					window.location.href = "main_displayprices.php";
				}
				else {
					window.location.href = "main_displayprices.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$sql = "UPDATE `item_pricemaster` SET `active` = '1' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayprices.php'); }
		}
		else if($updatetype == "pause"){
			$sql = "UPDATE `item_pricemaster` SET `active` = '0' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayprices.php'); }
		}
		else if($updatetype == "authorize"){
			$sql = "UPDATE `item_pricemaster` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displayprices.php'); }
		}
		else {}
	}
?>