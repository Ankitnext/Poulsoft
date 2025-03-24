<?php
//acc_updateretailcoamapping.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['rcoamap'];
$db_name = $_SESSION['dbase'];
if($_POST['submittrans'] == "addpage"){
	$a = $_POST['incr']; $prefix = "UCB";
	$sql = "SELECT MAX(incr) as incr FROM `main_useraccount`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
	$a = $_POST['incr'];
	for($i = 0;$i <= $a;$i++){
		$incr++;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		$code = $prefix."-".$incr;
		
		$acc_type = $_POST['cbtype'.$i];
		$empcode = $_POST['users'.$i];
		$shops = $_POST['shops'.$i];
		$coacode = $_POST['coas'.$i];
		if($acc_type == "B"){
			$rtype = $_POST['types'.$i];
			if($rtype == "upi"){
				$atype = "";
				foreach($_POST['atypes'.$i] as $atp){
					if($atype == ""){
						$atype = $atp;
					}
					else{
						$atype = $atype.",".$atp;
					}
				}
				$ano = $_POST['cbuno'.$i];
			}
			else{
				$atype = $ano = NULL;
			}
		}
		else{
			$rtype = $atype = $ano = NULL;
		}
		$sql = "INSERT INTO `main_useraccount` (incr,prefix,code,empcode,acc_type,coacode,shops,rtype,atype,ano,flag,active,dflag) VALUES ('$incr','$prefix','$code','$empcode','$acc_type','$coacode','$shops','$rtype','$atype','$ano','0','1','0')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
		else {
			$sql = "UPDATE `acc_coa` SET `rflag` = '1',`flag` = '1' WHERE `code` = '$coacode'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
			/*if($acc_type == "C"){
				
				$sql = "UPDATE `main_access` SET `cashcode` = '$coacode' WHERE `empcode` = '$empcode'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
			}
			else if($acc_type == "B"){
				$sql = "UPDATE `main_access` SET `bankcode` = '$coacode' WHERE `empcode` = '$empcode'";
				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
			}
			else{ }*/
			
		}
	}
	?>
	<script>
		var a = '<?php echo $cid; ?>';
		var x = confirm("Would you like add more Retail CoA Mapping ?");
		if(x == true){
			window.location.href = "acc_addretailcoamapping.php?cid="+a;
		}
		else if(x == false) {
			window.location.href = "acc_displayretailcoamapping.php?cid="+a;
		}
		else {
			window.location.href = "acc_displayretailcoamapping.php?cid="+a;
		}
	</script>
	<?php
}
else if($_POST['submittrans'] == "updatepage"){
	$code = $_POST['code'];
	$sql = "SELECT * FROM `main_useraccount` WHERE `code` = '$code'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$empcode = $row['empcode'];
		$old_coa = $row['coacode'];
	}
	$acc_type = $_POST['cbtype'];
	$empcode = $_POST['users'];
	$shops = $_POST['shops'];
	$coacode = $_POST['coas'];
	if($acc_type == "B"){
		$rtype = $_POST['types'];
		if($rtype == "upi"){
			$atype = "";
			foreach($_POST['atypes'] as $atp){
				if($atype == ""){
					$atype = $atp;
				}
				else{
					$atype = $atype.",".$atp;
				}
			}
			$ano = $_POST['cbuno'];
		}
		else{
			$atype = $ano = NULL;
		}
	}
	else{
		$rtype = $atype = $ano = NULL;
	}
	$sql = "UPDATE `main_useraccount` SET `empcode` = '$empcode',`acc_type` = '$acc_type',`coacode` = '$coacode',`rtype` = '$rtype',`atype` = '$atype',`ano` = '$ano',`flag` = '0',`active` = '1',`dflag` = '0' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
	else {
		if($old_coa != $coacode){
			$sql = "UPDATE `acc_coa` SET `rflag` = '0' WHERE `code` = '$old_coa'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		}
		$sql = "UPDATE `acc_coa` SET `rflag` = '1',`flag` = '1' WHERE `code` = '$coacode'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		/*if($acc_type == "C"){
			$sql = "UPDATE `main_access` SET `cashcode` = '$coacode' WHERE `empcode` = '$empcode'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		}
		else if($acc_type == "B"){
			$sql = "UPDATE `main_access` SET `bankcode` = '$coacode' WHERE `empcode` = '$empcode'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		}
		else{ }*/
		
	}
	header('location:acc_displayretailcoamapping.php?cid='.$cid);
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "SELECT * FROM `main_useraccount` WHERE `code` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$code = $row['code'];
			$empcode = $row['empcode'];
			$acc_type = $row['acc_type'];
			$coacode = $row['coacode'];
			$shops = $row['shops'];
			$rtype = $row['rtype'];
			$atype = explode(",",$row['atype']);
			$ano = $row['ano'];
		}
		$sql = "UPDATE `acc_coa` SET `rflag` = '0' WHERE `code` = '$coacode'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		
		/*if($acc_type == "C"){
			$sql = "UPDATE `main_access` SET `cashcode` = NULL WHERE `empcode` = '$empcode'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		}
		else if($acc_type == "B"){
			$sql = "UPDATE `main_access` SET `bankcode` = NULL WHERE `empcode` = '$empcode'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
		}*/
		
		$sql = "UPDATE `main_useraccount` SET `active` = '0',`dflag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayretailcoamapping.php?cid='.$cid); }
	}
	else if($page == "activate"){
		$sql = "UPDATE `main_useraccount` SET `active` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayretailcoamapping.php?cid='.$cid); }
	}
	else if($page == "pause"){
		$sql = "UPDATE `main_useraccount` SET `active` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayretailcoamapping.php?cid='.$cid); }
	}
	else if($page == "authorize"){
		$sql = "UPDATE `main_useraccount` SET `flag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayretailcoamapping.php?cid='.$cid); }
	}
	else{
	
	}
}
?>