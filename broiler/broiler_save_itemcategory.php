<?php
//broiler_save_itemcategory.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['itemcategory'];

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `item_category`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("bird_plant", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bird_plant` INT(100) NOT NULL DEFAULT '0' COMMENT 'Bird Processing Flag' AFTER `description`"; mysqli_query($conn,$sql); }
if(in_array("chicken_plant", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `chicken_plant` INT(100) NOT NULL DEFAULT '0' COMMENT 'Chick Processing Flag' AFTER `bird_plant`"; mysqli_query($conn,$sql); }
if(in_array("plant_portioning", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `plant_portioning` INT(100) NOT NULL DEFAULT '0' COMMENT 'Feed Sale Visibility Flag' AFTER `chicken_plant`"; mysqli_query($conn,$sql); }
if(in_array("feedsale_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `feedsale_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Feed Sale Visibility Flag' AFTER `plant_portioning`"; mysqli_query($conn,$sql); }
if(in_array("plant_sort_order", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `plant_sort_order` INT(100) NOT NULL DEFAULT '0' COMMENT 'Plant Portioning Sort Order' AFTER `sort_order`"; mysqli_query($conn,$sql); }
if(in_array("main_category", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `main_category` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `description`"; mysqli_query($conn,$sql); }
if(in_array("bffeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bffeed_flag` INT(100) NOT NULL DEFAULT '0' AFTER `main_category`"; mysqli_query($conn,$sql); }
if(in_array("bmfeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bmfeed_flag` INT(100) NOT NULL DEFAULT '0' AFTER `bffeed_flag`"; mysqli_query($conn,$sql); }
if(in_array("begg_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `begg_flag` INT(100) NOT NULL DEFAULT '0' AFTER `bmfeed_flag`"; mysqli_query($conn,$sql); }
if(in_array("bmv_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bmv_flag` INT(100) NOT NULL DEFAULT '0' AFTER `begg_flag`"; mysqli_query($conn,$sql); }
if(in_array("lfeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `lfeed_flag` INT(100) NOT NULL DEFAULT '0' AFTER `lfeed_flag`"; mysqli_query($conn,$sql); }
if(in_array("legg_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `legg_flag` INT(100) NOT NULL DEFAULT '0' AFTER `legg_flag`"; mysqli_query($conn,$sql); }
if(in_array("lmv_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `lmv_flag` INT(100) NOT NULL DEFAULT '0' AFTER `lmv_flag`"; mysqli_query($conn,$sql); }


$cdesc = $_POST['cdesc'];
$prefix = $_POST['prefix'];
$main_category = $_POST['main_category']; if($main_category == "select"){ $main_category = ""; }
$new_a = $_POST['newaccounts'];
if($_POST['bird_plant'] == "on" || $_POST['bird_plant'] == true || $_POST['bird_plant'] == 1){ $bird_plant = 1; } else{ $bird_plant = 0; }
if($_POST['chicken_plant'] == "on" || $_POST['chicken_plant'] == true || $_POST['chicken_plant'] == 1){ $chicken_plant = 1; } else{ $chicken_plant = 0; }
if($_POST['plant_portioning'] == "on" || $_POST['plant_portioning'] == true || $_POST['plant_portioning'] == 1){ $plant_portioning = 1; } else{ $plant_portioning = 0; }
if($_POST['feedsale_flag'] == "on" || $_POST['feedsale_flag'] == true || $_POST['feedsale_flag'] == 1){ $feedsale_flag = 1; } else{ $feedsale_flag = 0; }
if($_POST['bffeed_flag'] == "on" || $_POST['bffeed_flag'] == true || $_POST['bffeed_flag'] == 1){ $bffeed_flag = 1; } else{ $bffeed_flag = 0; }
if($_POST['bmfeed_flag'] == "on" || $_POST['bmfeed_flag'] == true || $_POST['bmfeed_flag'] == 1){ $bmfeed_flag = 1; } else{ $bmfeed_flag = 0; }
if($_POST['begg_flag'] == "on" || $_POST['begg_flag'] == true || $_POST['begg_flag'] == 1){ $begg_flag = 1; } else{ $begg_flag = 0; }
if($_POST['bmv_flag'] == "on" || $_POST['bmv_flag'] == true || $_POST['bmv_flag'] == 1){ $bmv_flag = 1; } else{ $bmv_flag = 0; }
if($_POST['lfeed_flag'] == "on" || $_POST['lfeed_flag'] == true || $_POST['lfeed_flag'] == 1){ $lfeed_flag = 1; } else{ $lfeed_flag = 0; }
if($_POST['legg_flag'] == "on" || $_POST['legg_flag'] == true || $_POST['legg_flag'] == 1){ $legg_flag = 1; } else{ $legg_flag = 0; }
if($_POST['lmv_flag'] == "on" || $_POST['lmv_flag'] == true || $_POST['lmv_flag'] == 1){ $lmv_flag = 1; } else{ $lmv_flag = 0; }
if($_POST['fgoods_flag'] == "on" || $_POST['fgoods_flag'] == true || $_POST['fgoods_flag'] == 1){ $fgoods_flag = 1; } else{ $fgoods_flag = 0; }
if($_POST['rawing_flag'] == "on" || $_POST['rawing_flag'] == true || $_POST['rawing_flag'] == 1){ $rawing_flag = 1; } else{ $rawing_flag = 0; }


if($new_a == "new_acc"){
    $sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'STK-%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $incr = $row['incr']; $incrs = $incr + 1;
        if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
        $code = "STK-".$incrs; $type = "COA-0001"; $sch = "AS-0001"; $cat = "CAT-0001"; $coadesc = "Stock - ".$cdesc;
        $sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs,addedemp,addedtime) VALUES('$incrs','STK','$code','$coadesc','$type','$sch','$cat','1','$addedemp','$addedtime')"; mysqli_query($conn,$sql);
        $iac = $code;
    }
    $sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'CGS-%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $incr = $row['incr']; $incrs = $incr + 1;
        if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
        $code = "CGS-".$incrs; $type = "COA-0003"; $sch = "EX-0001"; $cat = "CAT-0003"; $coadesc = "COGS - ".$cdesc;
        $sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs,addedemp,addedtime) VALUES('$incrs','CGS','$code','$coadesc','$type','$sch','$cat','1','$addedemp','$addedtime')"; mysqli_query($conn,$sql);
        $icogs = $code;
    }
    $sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'SLA-%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $incr = $row['incr']; $incrs = $incr + 1;
        if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
        $code = "SLA-".$incrs; $type = "COA-0005"; $sch = "RV-0001"; $cat = "CAT-0004"; $coadesc = "Sales - ".$cdesc;
        $sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs,addedemp,addedtime) VALUES('$incrs','SLA','$code','$coadesc','$type','$sch','$cat','1','$addedemp','$addedtime')"; mysqli_query($conn,$sql);
        $isalesac = $code;
    }
    $sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'SRA-%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $incr = $row['incr']; $incrs = $incr + 1;
        if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
        $code = "SRA-".$incrs; $type = "COA-0003"; $sch = "EX-0002"; $cat = "CAT-0003"; $coadesc = "Sales Return - ".$cdesc;
        $sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs,addedemp,addedtime) VALUES('$incrs','SRA','$code','$coadesc','$type','$sch','$cat','1','$addedemp','$addedtime')"; mysqli_query($conn,$sql);
        $israc = $code;
    }
    $sql ="SELECT MAX(incr) as incr,type FROM `acc_coa` WHERE `code` LIKE 'WIP-%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $incr = $row['incr']; $incrs = $incr + 1;
        if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
        $code = "WIP-".$incrs; $type = "COA-0001"; $sch = "AS-0002"; $cat = "CAT-0001"; $coadesc = "WIP - ".$cdesc;
        $sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,schedules,categories,bs,addedemp,addedtime) VALUES('$incrs','SRA','$code','$coadesc','$type','$sch','$cat','1','$addedemp','$addedtime')"; mysqli_query($conn,$sql);
        $iwpac = $code;
    }
}
else {
$iac = $_POST['iac'];
$icogs = $_POST['icogs'];
$isalesac = $_POST['isalesac'];
$israc = $_POST['israc'];
$iwpac = $_POST['iwpac'];
}
$sql ="SELECT * FROM `item_category` WHERE `prefix` = '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ ?>
	<script>
		var x = alert("The Prefix is already available \n Please check and try again ..!");
		if(x == true){ window.location.href = "broiler_display_itemcategory.php?ccid=".$ccid; } else if(x == false) { window.location.href = "broiler_display_itemcategory.php?ccid=".$ccid; } else { window.location.href = "broiler_display_itemcategory.php?ccid=".$ccid; }
	</script>
<?php
}
else {
	$sql ="SELECT MAX(incr) as incr FROM `item_category`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }
	
	if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
	
	$code = "IMS-".$incrs;
	
	$sql = "INSERT INTO `item_category` (incr,prefix,code,description,main_category,bird_plant,chicken_plant,plant_portioning,feedsale_flag,bffeed_flag,bmfeed_flag,lfeed_flag,begg_flag,legg_flag,bmv_flag,lmv_flag,fgoods_flag,rawing_flag,iac,cogsac,wpac,sac,srac,addedemp,addedtime) VALUES 
	('$incrs','$prefix','$code','$cdesc','$main_category','$bird_plant','$chicken_plant','$plant_portioning','$feedsale_flag','$bffeed_flag','$bmfeed_flag','$lfeed_flag','$begg_flag','$legg_flag','$bmv_flag','$lmv_flag','$fgoods_flag','$rawing_flag','$iac','$icogs','$iwpac','$isalesac','$israc','$addedemp','$addedtime')";
	
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_itemcategory.php?ccid='.$ccid); }
}


?>
