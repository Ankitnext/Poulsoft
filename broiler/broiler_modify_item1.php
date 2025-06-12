<?php
//broiler_modify_item1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['item1'];

$sql='SHOW COLUMNS FROM `item_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("item_size", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `item_size` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `cunits`"; mysqli_query($conn,$sql); }
if(in_array("offals_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `offals_flag` INT(100) NOT NULL DEFAULT '0' AFTER `item_size`"; mysqli_query($conn,$sql); }
if(in_array("bfamf_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `bfamf_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Female And Male Feed Flag' AFTER `offals_flag`"; mysqli_query($conn,$sql); }

$cdesc = $_POST['idesc'];
$ctype = $_POST['icat'];
$sub_category = $_POST['sub_category'];
$item_size = $_POST['item_size']; if($item_size == ""){ $item_size = 0; }
if($_POST['offals_flag'] == "on" || $_POST['offals_flag'] == true || $_POST['offals_flag'] == 1){ $offals_flag = 1; } else{ $offals_flag = 0; }
if($_POST['bfamf_flag'] == "on" || $_POST['bfamf_flag'] == true || $_POST['bfamf_flag'] == 1){ $bfamf_flag = 1; } else{ $bfamf_flag = 0; }
$bag_size = $_POST['bag_size']; if($bag_size == "0" || $bag_size == "0.00"){ $bag_size = ""; }
$sunits = $_POST['istored'];
$cunits = $_POST['icunit'];
$einv_units = $_POST['einv_units'];
$id = $_POST['idvalue'];
$hsn_code = $_POST['hsn_code'];
$ob_stock = $_POST['ob_stock'];
if($ob_stock == "" || $ob_stock == 0 || $ob_stock == "0.00"){ $ob_stock = "0.00"; }
$price = $_POST['price'];
if($price == "" || $price == 0 || $price == "0.00"){ $price = "0.00"; }
$ob_date = $_POST['ob_date'];
$gst_code = $_POST['gst_code'];
$lsflag = $_POST['lsflag'];
if($lsflag == true || $lsflag == 1){ $lsflag = 1; } else{ $lsflag = 0; }
$lsqty = $_POST['lsqty'];
if($lsqty == "" || $lsqty == 0 || $lsqty == "0.00"){ $lsqty = "0.00"; }
$amount = $_POST['amount'];
if($amount == "" || $amount == 0 || $amount == "0.00"){ $amount = "0.00"; }
$ob_date = date("Y-m-d",strtotime($_POST['ob_date']));

$sall_flag = 0; $sec_list = array();
foreach($_POST['sector_access'] as $scode){ if($scode == "all"){ $sall_flag = 1; } $sec_list[$scode] = $scode; }
if($sall_flag == 1){ $sector_access = "all"; } else{ $sector_access = implode(",",$sec_list); }

$sql ="SELECT * FROM `item_details` WHERE `description` = '$cdesc' AND `dflag` = '0' AND `id` NOT IN ('$id')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ ?>
	<script>
        var ccid = '<?php echo $ccid; ?>';
		var x = alert("The Description is already available \n Please check and try again ..!");
		if(x == true){ window.location.href = "broiler_add_item1.php?ccid="+ccid; } else if(x == false) { window.location.href = "broiler_add_item1.php?ccid="+ccid; } else { window.location.href = "broiler_add_item1.php?ccid="+ccid; }
	</script>
<?php
}
else {
	/*$sql = "SELECT category from `item_details` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $category = $row['category']; }
	if($category == $ctype){
		$sql = "UPDATE `item_details` SET `description` = '$cdesc',`sunits` = '$sunits',`cunits` = '$cunits',`hsn_code` = '$hsn_code',`ob_stock` = '$ob_stock',`price` = '$price',`amount` = '$amount',`ob_date` = '$ob_date',`gst_code` = '$gst_code',`lsflag` = '$lsflag',`lsqty` = '$lsqty',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
	}
	else {
		$sql = "SELECT prefix from `item_category` WHERE `code` = '$ctype'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; }
		
		$sql ="SELECT MAX(incr) as incr FROM `item_details` WHERE `prefix` LIKE '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }
		
		if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
		$code = $prefix."-".$incrs;
		
		$sql = "UPDATE `item_details` SET `incr` = '$incrs',`prefix` = '$prefix',`category` = '$ctype',`code` ='$code',`description` = '$cdesc',`sunits` = '$sunits',`cunits` = '$cunits',`hsn_code` = '$hsn_code',`ob_stock` = '$ob_stock',`price` = '$price',`amount` = '$amount',`ob_date` = '$ob_date',`gst_code` = '$gst_code',`lsflag` = '$lsflag',`lsqty` = '$lsqty',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
	}*/
	$sql ="SELECT * FROM `item_details` WHERE `id` IN ('$id')"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }
	if($bag_size != ""){
		$sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0' AND (`code` = '$code' OR `description` = '$cdesc')";
		$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ $sql = "UPDATE `feed_bagcapacity` SET `code` = '$code',`description` = '$cdesc',`bag_size` = '$bag_size',`nob` = '1' WHERE `active` = '1' AND `dflag` = '0' AND (`code` = '$code' OR `description` = '$cdesc')"; }
		else{ $sql = "INSERT INTO `feed_bagcapacity` (code,description,bag_size,nob) VALUES ('$code','$cdesc','$bag_size','1')"; }
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	}
	else{
		$sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0' AND (`code` = '$code' OR `description` = '$cdesc')";
		$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			$sql = "UPDATE `feed_bagcapacity` SET `code` = '$code',`description` = '$cdesc',`bag_size` = '1',`nob` = '1' WHERE `active` = '1' AND `dflag` = '0' AND (`code` = '$code' OR `description` = '$cdesc')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
	}
	$sql = "UPDATE `item_details` SET `category` = '$ctype',`sub_category` = '$sub_category',`description` = '$cdesc',`sunits` = '$cunits',`cunits` = '$cunits',`einv_units` = '$einv_units',`item_size` = '$item_size',`offals_flag` = '$offals_flag',`bfamf_flag` = '$bfamf_flag',`sector_access` = '$sector_access',`hsn_code` = '$hsn_code',`ob_stock` = '$ob_stock',`price` = '$price',`amount` = '$amount',`ob_date` = '$ob_date',`gst_code` = '$gst_code',`lsflag` = '$lsflag',`lsqty` = '$lsqty',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_item1.php?ccid='.$ccid); }
}