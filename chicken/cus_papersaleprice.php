<?php
	//cus_papersaleprice.php
	session_start(); include "broiler_check_tableavailability.php";
	$icode = $_GET['iname'];
	$ccode = $_GET['pname'];
	if($_GET['mdate'] == ""){ $today = date("Y-m-d"); } else{ $today = date("Y-m-d",strtotime($_GET['mdate'])); }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$ccode'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$cgroup = $row['groupcode'];
	}
	$icount = 0;
	if($count33 > 0){
		$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` = '$today' AND `code` = '$icode' AND `cgroup` = '$cgroup' AND `active` = '1'";
		$query = mysqli_query($conn,$sql); $icount = mysqli_num_rows($query);
	}
	if($icount > 0){
		while($row = mysqli_fetch_assoc($query)){
			$paper_price = $row['new_price'];
		}
		$val = 0; $ptype = "";
		if($count11 > 0){
			$sql = "SELECT * FROM `customer_price` WHERE `ccode` = '$ccode' AND `itemcode` = '$icode' AND `active` = '1' AND `date` IN (SELECT MAX(date) as date FROM `customer_price` WHERE `ccode` = '$ccode' AND `itemcode` = '$icode' AND `active` = '1')";
			$query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$val = $row['value'];
				$ptype = $row['price_type'];
			}
		}

		if($ptype == "A"){ $iprate = $paper_price + $val; }
		else if($ptype == "D"){ $iprate = $paper_price - $val; }
		else{
			$iprate = $paper_price;
		}
	}
	else{
		$iprate = 0;
	}
	//echo $paper_price."--".$val."--".$iprate;
	echo $iprate;
?>