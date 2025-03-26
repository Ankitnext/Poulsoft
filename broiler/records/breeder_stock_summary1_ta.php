<?php
//breeder_stock_summary1_ta.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "breeder_stock_summary1_ta.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "breeder_stock_summary1_ta.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";
$file_name = "Breeder Stock Summary Report";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_broiler_broilermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_broiler_broilermaster.breeder_units;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_broiler_broilermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_batch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_batch LIKE poulso6_admin_broiler_broilermaster.breeder_batch;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_shed_allocation LIKE poulso6_admin_broiler_broilermaster.breeder_shed_allocation;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_dayentry_consumed", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_consumed LIKE poulso6_admin_broiler_broilermaster.breeder_dayentry_consumed;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_dayentry_produced", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_produced LIKE poulso6_admin_broiler_broilermaster.breeder_dayentry_produced;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_medicine_consumed", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_medicine_consumed LIKE poulso6_admin_broiler_broilermaster.breeder_medicine_consumed;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_bird_transfer", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_bird_transfer LIKE poulso6_admin_broiler_broilermaster.breeder_bird_transfer;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_egg_conversion", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_egg_conversion LIKE poulso6_admin_broiler_broilermaster.breeder_egg_conversion;"; mysqli_query($conn,$sql1); }
if(in_array("item_stocktransfers", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stocktransfers LIKE poulso6_admin_broiler_broilermaster.item_stocktransfers;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_extra_access LIKE poulso6_admin_broiler_broilermaster.breeder_extra_access;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_broiler_broilermaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }

$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("unit_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `unit_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("shed_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `shed_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `unit_code`"; mysqli_query($conn,$sql); }
if(in_array("flock_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `flock_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `farm_batch`"; mysqli_query($conn,$sql); }
if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `trtype`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `item_stocktransfers`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("from_unit", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `from_unit` VARCHAR(300) NULL DEFAULT NULL AFTER `fromwarehouse`"; mysqli_query($conn,$sql); }
if(in_array("from_shed", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `from_shed` VARCHAR(300) NULL DEFAULT NULL AFTER `from_unit`"; mysqli_query($conn,$sql); }
if(in_array("from_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `from_batch` VARCHAR(300) NULL DEFAULT NULL AFTER `from_shed`"; mysqli_query($conn,$sql); }
if(in_array("from_flock", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `from_flock` VARCHAR(300) NULL DEFAULT NULL AFTER `from_batch`"; mysqli_query($conn,$sql); }
if(in_array("to_unit", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `to_unit` VARCHAR(300) NULL DEFAULT NULL AFTER `towarehouse`"; mysqli_query($conn,$sql); }
if(in_array("to_shed", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `to_shed` VARCHAR(300) NULL DEFAULT NULL AFTER `to_unit`"; mysqli_query($conn,$sql); }
if(in_array("to_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `to_batch` VARCHAR(300) NULL DEFAULT NULL AFTER `to_shed`"; mysqli_query($conn,$sql); }
if(in_array("to_flock", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `to_flock` VARCHAR(300) NULL DEFAULT NULL AFTER `to_batch`"; mysqli_query($conn,$sql); }
if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `trtype`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `item_category`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("main_category", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `main_category` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `description`"; mysqli_query($conn,$sql); }
if(in_array("bffeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bffeed_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Feed' AFTER `main_category`"; mysqli_query($conn,$sql); }
if(in_array("bmfeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bmfeed_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Feed' AFTER `bffeed_flag`"; mysqli_query($conn,$sql); }
if(in_array("begg_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `begg_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Eggs' AFTER `bmfeed_flag`"; mysqli_query($conn,$sql); }
if(in_array("bmv_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bmv_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder MedVac' AFTER `begg_flag`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

//Breeder Extra Access
$sql = "SELECT *  FROM `breeder_extra_access` WHERE `field_name` LIKE 'Breeder Display section' AND `field_function` LIKE 'breeder_stock_summary1_ta.php' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $e_cnt = mysqli_num_rows($query); $opn_flag = $prd_flag = $tin_flag = $sale_flag = $tout_flag = $cnvti_flag = $cnvto_flag = $disp_flag = $cls_flag = 1;
if($e_cnt > 0){
    $opn_flag = $prd_flag = $tin_flag = $sale_flag = $tout_flag = $cnvti_flag = $cnvto_flag = $disp_flag = $cls_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $fval_alist = explode(",",$row['field_value']); }
    foreach($fval_alist as $fval){
        if(strtolower($fval) == "opening"){ $opn_flag = 1; }
        else if(strtolower($fval) == "production"){ $prd_flag = 1; }
        else if(strtolower($fval) == "transfer-in"){ $tin_flag = 1; }
        else if(strtolower($fval) == "transfer-out"){ $tout_flag = 1; }
        else if(strtolower($fval) == "sales"){ $sale_flag = 1; }
        else if(strtolower($fval) == "convert-in"){ $cnvti_flag = 1; }
        else if(strtolower($fval) == "convert-out"){ $cnvto_flag = 1; }
        else if(strtolower($fval) == "disposed"){ $disp_flag = 1; }
        else if(strtolower($fval) == "closing"){ $cls_flag = 1; }
        else{ }
    }
}

//Breeder Extra Access
$sql = "SELECT *  FROM `breeder_extra_access` WHERE `field_name` LIKE 'Breeder Module' AND `field_function` LIKE 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $e_nt = mysqli_num_rows($query); 
if($e_nt > 0){
    if(strtolower($row['field_value']) == "Unit"){ $un_flag = 1; }
    if(strtolower($row['field_value']) == "Shed"){ $sh_flag = 1; }
}

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_breed = array();
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_breed[$row['code']] = $row['breed_code']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_farm = $flock_unit = $flock_shed = $flock_Bach = array();
while($row = mysqli_fetch_assoc($query)){
    $flock_code[$row['code']] = $row['code'];
    $flock_name[$row['code']] = $row['description'];
    $flock_farm[$row['code']] = $row['farm_code'];
    $flock_unit[$row['code']] = $row['unit_code'];
    $flock_shed[$row['code']] = $row['shed_code'];
    $flock_Bach[$row['code']] = $row['batch_code'];
}

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code);

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Hatch Egg%' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $hegg_code = "";
while($row = mysqli_fetch_assoc($query)){ $hegg_code = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $batches = $flocks = array(); $excel_type = "display";
$farms["all"] = $units["all"] = $sheds["all"] = $batches["all"] = $flocks["all"] = "all";
$f_aflag = $u_aflag = $s_aflag = $b_aflag = $fl_aflag = 1; $fetch_type = "unit_wise";

if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $farms = $units = $sheds = $batches = $flocks = array(); $f_aflag = $u_aflag = $s_aflag = $b_aflag = $fl_aflag = 0;
    foreach($_POST['farms'] as $t1){ $farms[$t1] = $t1; }           foreach($farms as $t1){ if($t1 == "all"){ $f_aflag = 1; } }
    foreach($_POST['units'] as $t1){ $units[$t1] = $t1; }           foreach($units as $t1){ if($t1 == "all"){ $u_aflag = 1; } }
    foreach($_POST['sheds'] as $t1){ $sheds[$t1] = $t1; }           foreach($sheds as $t1){ if($t1 == "all"){ $s_aflag = 1; } }
    foreach($_POST['batches'] as $t1){ $batches[$t1] = $t1; }       foreach($batches as $t1){ if($t1 == "all"){ $b_aflag = 1; } }
    foreach($_POST['flocks'] as $t1){ $flocks[$t1] = $t1; }         foreach($flocks as $t1){ if($t1 == "all"){ $fl_aflag = 1; } }
    $fetch_type = $_POST['fetch_type'];
    $excel_type = $_POST['export'];
}

//Arrange Filters
$farm_fltr = $unit_fltr = $shed_fltr = $batch_fltr = $flock_fltr = "";
if($f_aflag == 0){ $farm_list = implode("','",$farms); $farm_fltr = " AND `farm_code` IN ('$farm_list')"; }
if($u_aflag == 0){ $unit_list = implode("','",$units); $unit_fltr = " AND `unit_code` IN ('$unit_list')"; }
if($s_aflag == 0){ $shed_list = implode("','",$sheds); $shed_fltr = " AND `shed_code` IN ('$shed_list')"; }
if($b_aflag == 0){ $batch_list = implode("','",$batches); $batch_fltr = " AND `batch_code` IN ('$batch_list')"; }
if($fl_aflag == 0){ $flock_list = implode("','",$flocks); $flock_fltr = " AND `code` IN ('$flock_list')"; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0'".$farm_fltr."".$unit_fltr."".$shed_fltr."".$batch_fltr."".$flock_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_alist = $unit_alist = $shed_alist = array();
while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; $unit_alist[$row['unit_code']] = $row['code']; $shed_alist[$row['shed_code']] = $row['code']; }

?> 
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post" onsubmit="return check_val();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div> <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="farms">Farm</label>
                                    <select name="farms[]" id="farms" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($farms as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($farm_code as $bcode){ if($farm_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($farms as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $farm_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="units">Unit</label>
                                    <select name="units[]" id="units" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($units as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($unit_code as $bcode){ if($unit_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($units as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $unit_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="sheds">Shed</label>
                                    <select name="sheds[]" id="sheds" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($sheds as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($shed_code as $bcode){ if($shed_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($sheds as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $shed_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="batches">Batch</label>
                                    <select name="batches[]" id="batches" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($batches as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($batch_code as $bcode){ if($batch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($batches as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="flocks">Flock</label>
                                    <select name="flocks[]" id="flocks" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($flocks as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($flock_code as $bcode){ if($flock_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($flocks as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $flock_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label>Category</label>
                                    <select name="item_cat[]" id="item_cat" class="form-control select2" multiple onchange="fetch_item_list();">
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label>Items</label>
                                    <select name="items[]" id="items" class="form-control select2" multiple>
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php if($item_cat == "all"){ ?>
                                        <?php foreach($item_code as $icodes){ if($item_name[$icodes] != ""){ ?>
                                        <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                        <?php } } }
                                        else{
                                            foreach($item_code as $icodes){
                                                if($item_cat == $item_category[$icodes]){
                                                ?>
                                                <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                                <?php
                                                }
                                            }
                                        }
                                            ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                                <div class="m-2 form-group" style="visibility:hidden;">
                                    <label>Fetch Type</label>
                                    <select name="fetch_type" id="fetch_type" class="form-control select2">
                                        <!-- <option value="farm_wise" <?php //if($fetch_type == "farm_wise"){ echo "selected"; } ?>>Farm</option> -->
                                       <?php if((int)$un_flag == 1){ ?> <option value="unit_wise" <?php if($fetch_type == "unit_wise"){ echo "selected"; } ?>>Unit</option> <?php } ?>
                                       <?php if((int)$sh_flag == 1){ ?> <option value="shed_wise" <?php if($fetch_type == "shed_wise"){ echo "selected"; } ?>>Shed</option> <?php } ?>
                                        <!-- <option value="bach_wise" <?php //if($fetch_type == "bach_wise"){ echo "selected"; } ?>>Batch</option> -->
                                        <!-- <option value="flck_wise" <?php //if($fetch_type == "flck_wise"){ echo "selected"; } ?>>Flock</option> -->
                                    </select>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
        </table>
        <table id="main_table" class="tbl" align="center">
            <?php
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th></th>'; $fhtml .= '<th></th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th></th>';
            if((int)$opn_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Opening</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Opening</th>'; }
            $nhtml .= '<th>Purchase</th>'; $fhtml .= '<th>Purchase</th>';
            if((int)$tin_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Transfer In</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Transfer In</th>'; }
            if((int)$prd_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; }
            if((int)$cnvti_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Convert-In</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Convert-In</th>'; }
            $nhtml .= '<th>Consumption</th>'; $fhtml .= '<th id="order_num">Consumption</th>'; 
            if((int)$tout_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Transfer Out</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Transfer Out</th>'; }
            if((int)$cnvto_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Convert-Out</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Convert-Out</th>'; }
            if((int)$sale_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Sales</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Sales</th>'; }
            if((int)$disp_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Disposed</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Disposed</th>'; }
            $nhtml .= '<th>Balance</th>'; $fhtml .= '<th id="order_num">Balance</th>'; 
            // if((int)$cls_flag == 1){ $nhtml .= '<th colspan="'.$e_cnt.'">Closing Stock</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Closing Stock</th>'; }

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_date">Sl.No.</th>';
            
            if($fetch_type == "farm_wise"){ $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>'; }
            else if($fetch_type == "unit_wise"){ $nhtml .= '<th>Unit</th>'; $fhtml .= '<th id="order">Unit</th>'; }
            else if($fetch_type == "shed_wise"){ $nhtml .= '<th>Shed</th>'; $fhtml .= '<th id="order">Shed</th>'; }
            else if($fetch_type == "bach_wise"){ $nhtml .= '<th>Batch</th>'; $fhtml .= '<th id="order">Batch</th>'; }
            else{ $nhtml .= '<th>Flock</th>'; $fhtml .= '<th id="order">Flock</th>'; }
            $nhtml .= '<th>Item Cat</th>'; $fhtml .= '<th id="order_num">Item Cat</th>'; 
            $nhtml .= '<th>Item</th>'; $fhtml .= '<th id="order_num">Item</th>'; 

            if((int)$opn_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            $nhtml .= '<th>Purchase</th>'; $fhtml .= '<th id="order_num">Purchase</th>'; 
            if((int)$tin_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            if((int)$prd_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            if((int)$cnvti_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            $nhtml .= '<th>Consumption</th>'; $fhtml .= '<th id="order_num">Consumption</th>'; 
            if((int)$tout_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            if((int)$cnvto_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            if((int)$sale_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            if((int)$disp_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }
            $nhtml .= '<th>Balance</th>'; $fhtml .= '<th id="order_num">Balance</th>'; 
           // if((int)$cls_flag == 1){ foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; } }

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true && sizeof($flock_alist) > 0){
                $flock_list = implode("','", $flock_alist);
                if($un_flag == 1){
                    $w_list = implode("','", $unit_alist);
                } else if($sh_flag == 1){
                    $w_list = implode("','", $shed_alist);
                }


                //Egg Consumed
                $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` <= '$tdate' AND `warehouse` IN ('$w_list') AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $dentry_oqty = $dentry_bqty = $flock_alist = $item_alist = array();
                while($row = mysqli_fetch_assoc($query)){ 
                    $idate = $row['date']; $iflock = $row['flock_code']; $icode = $row['item_code']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $dentry_oqty[$key1] += $row['quantity']; 
                    }
                    else{
                        $dentry_bqty[$key1] += $row['quantity']; 
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                } 
                //Egg Produced
                $sql = "SELECT * FROM `breeder_dayentry_produced` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $dentry_oqty = $dentry_bqty = $flock_alist = $item_alist = array();
                while($row = mysqli_fetch_assoc($query)){ 
                    $idate = $row['date']; $iflock = $row['flock_code']; $icode = $row['item_code']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $dentry_oqty[$key1] += $row['quantity']; 
                    }
                    else{
                        $dentry_bqty[$key1] += $row['quantity']; 
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                //Egg purchase
                $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $sale_oqty = $sale_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $idate = $row['date']; $iflock = $row['flock_code']; $icode = $row['icode']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $sale_oqty[$key1] += ($row['rcd_qty'] + $row['fre_qty']);
                    }
                    else{
                        $sale_bqty[$key1] += ($row['rcd_qty'] + $row['fre_qty']);
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                //Egg Transfer-In
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `to_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_flock` ASC";
                $query = mysqli_query($conn,$sql); $tin_oqty = $tin_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $idate = $row['date']; $iflock = $row['to_flock']; $icode = $row['code']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $tin_oqty[$key1] += $row['quantity'];
                    }
                    else{
                        $tin_bqty[$key1] += $row['quantity'];
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                //Egg Transfer-Out
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `from_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_flock` ASC";
                $query = mysqli_query($conn,$sql); $tout_oqty = $tout_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $idate = $row['date']; $iflock = $row['from_flock']; $icode = $row['code']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $tout_oqty[$key1] += $row['quantity'];
                    }
                    else{
                        $tout_bqty[$key1] += $row['quantity'];
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                //Egg Conversion To
                $sql = "SELECT * FROM `breeder_egg_conversion` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $ecto_oqty = $ecto_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $idate = $row['date']; $iflock = $row['flock_code']; $icode = $row['to_item']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $ecto_oqty[$key1] += $row['to_qty'];
                    }
                    else{
                        $ecto_bqty[$key1] += $row['to_qty'];
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                //Egg Conversion From
                $sql = "SELECT * FROM `breeder_egg_conversion` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $ecfrom_oqty = $ecfrom_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $idate = $row['date']; $iflock = $row['flock_code']; $icode = $row['from_item']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $ecfrom_oqty[$key1] += $row['from_qty'];
                    }
                    else{
                        $ecfrom_bqty[$key1] += $row['from_qty'];
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                //Egg Disposed
                $sql = "SELECT * FROM `breeder_egg_conversion` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $disp_oqty = $disp_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $idate = $row['date']; $iflock = $row['flock_code']; $icode = $row['from_item']; $key1 = $iflock."@$&".$icode;
                    if(strtotime($idate) < strtotime($fdate)){
                        $disp_oqty[$key1] += $row['from_qty'];
                    }
                    else{
                        $disp_bqty[$key1] += $row['from_qty'];
                    }
                    $flock_alist[$iflock] = $iflock;
                    $item_alist[$icode] = $icode;
                }
                $opn_sqty = $prd_sqty = $tin_sqty = $sal_sqty = $tou_sqty = $cto_sqty = $cfr_sqty = $dis_sqty = $cls_sqty = $field_alist = array();
                foreach($flock_alist as $flks){
                    foreach($item_alist as $items){
                        $key1 = $flks."@$&".$items;

                        $fcode = $flock_farm[$flks]; $ucode = $flock_unit[$flks]; $scode = $flock_shed[$flks]; $bcode = $flock_Bach[$flks];
                        if($fetch_type == "farm_wise"){ $key2 = $fcode; }
                        else if($fetch_type == "unit_wise"){ $key2 = $ucode; }
                        else if($fetch_type == "shed_wise"){ $key2 = $scode; }
                        else if($fetch_type == "bach_wise"){ $key2 = $bcode; }
                        else{ $key2 = $flks; }

                        $key3 = $key2."@$&".$items;
                        $o_qty = 0;
                        $o_qty = (((float)$dentry_oqty[$key1] + (float)$tin_oqty[$key1] + (float)$ecto_oqty[$key1]) - ((float)$sale_oqty[$key1] + (float)$tout_oqty[$key1] + (float)$ecfrom_oqty[$key1] + (float)$disp_oqty[$key1]));
                        $opn_sqty[$key3] += (float)$o_qty;
                        $prd_sqty[$key3] += (float)$dentry_bqty[$key1];
                        $tin_sqty[$key3] += (float)$tin_bqty[$key1];
                        $sal_sqty[$key3] += (float)$sale_bqty[$key1];
                        $tou_sqty[$key3] += (float)$tout_bqty[$key1];
                        $cto_sqty[$key3] += (float)$ecto_bqty[$key1];
                        $cfr_sqty[$key3] += (float)$ecfrom_bqty[$key1];
                        $dis_sqty[$key3] += (float)$disp_bqty[$key1];
                        $cls_sqty[$key3] += (((float)$o_qty + (float)$dentry_bqty[$key1] + (float)$tin_bqty[$key1] + (float)$ecto_bqty[$key1]) - ((float)$sale_bqty[$key1] + (float)$tout_bqty[$key1] + (float)$ecfrom_bqty[$key1] + (float)$disp_bqty[$key1]));
                    
                        if((float)$cls_sqty[$key3] != 0){
                            $field_alist[$key2] = $key2;
                        }
                    }
                }
                $dentry_oqty = $tin_oqty = $ecto_oqty = $sale_oqty = $tout_oqty = $ecfrom_oqty = $disp_oqty = array();
                $dentry_bqty = $tin_bqty = $ecto_bqty = $sale_bqty = $tout_bqty = $ecfrom_bqty = $disp_bqty = $flock_alist = $item_alist = array();

                $tbl_name = "";
                if($fetch_type == "farm_wise"){ $tbl_name = "breeder_farms"; } else if($fetch_type == "unit_wise"){ $tbl_name = "breeder_units"; }
                else if($fetch_type == "shed_wise"){ $tbl_name = "breeder_sheds"; } else if($fetch_type == "bach_wise"){ $tbl_name = "breeder_batch"; } else{ $tbl_name = "breeder_shed_allocation"; }
                
                $code_list = implode("','", $field_alist);
                $sql = "SELECT * FROM `$tbl_name` WHERE `code` IN ('$code_list') AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $fld_code = $fld_name = array();
                while($row = mysqli_fetch_assoc($query)){ $fld_code[$row['code']] = $row['code']; $fld_name[$row['code']] = $row['description']; }

                $slno = 0;
                $opn_eqty = $prd_eqty = $tin_eqty = $sale_eqty = $tout_eqty = $cin_eqty = $cout_eqty = $dis_eqty = $cls_eqty = array();
                foreach($fld_code as $fcode){
                    $slno++;
                    $html .= '<tr>';
                    $html .= '<td style="text-align:center;">'.$slno.'</td>';
                    $html .= '<td style="text-align:center;">'.$fld_name[$fcode].'</td>';
                    //Opening
                    if((int)$opn_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($opn_sqty[$key1]) || $opn_sqty[$key1] == ""){ } else{ $i_qty = $opn_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $opn_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Production
                    if((int)$prd_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($prd_sqty[$key1]) || $prd_sqty[$key1] == ""){ } else{ $i_qty = $prd_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $prd_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Transfer-In
                    if((int)$tin_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($tin_sqty[$key1]) || $tin_sqty[$key1] == ""){ } else{ $i_qty = $tin_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $tin_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Sales
                    if((int)$sale_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($sal_sqty[$key1]) || $sal_sqty[$key1] == ""){ } else{ $i_qty = $sal_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $sale_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Transfer-Out
                    if((int)$tout_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($tou_sqty[$key1]) || $tou_sqty[$key1] == ""){ } else{ $i_qty = $tou_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $tout_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Conversion-In
                    if((int)$cnvti_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($cto_sqty[$key1]) || $cto_sqty[$key1] == ""){ } else{ $i_qty = $cto_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $cin_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Conversion-Out
                    if((int)$cnvto_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($cfr_sqty[$key1]) || $cfr_sqty[$key1] == ""){ } else{ $i_qty = $cfr_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $cout_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Disposed
                    if((int)$disp_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($dis_sqty[$key1]) || $dis_sqty[$key1] == ""){ } else{ $i_qty = $dis_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $dis_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    //Closing
                    if((int)$cls_flag == 1){
                        foreach($egg_code as $icode){
                            $key1 = $fcode."@$&".$icode;
                            $i_qty = 0; if(empty($cls_sqty[$key1]) || $cls_sqty[$key1] == ""){ } else{ $i_qty = $cls_sqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</td>';
                            $cls_eqty[$icode] += (float)$i_qty;
                        }
                    }
                    $html .= '</tr>';
                }
                
                $opn_sqty = $prd_sqty = $tin_sqty = $sal_sqty = $tou_sqty = $cto_sqty = $cfr_sqty = $dis_sqty = $cls_sqty = $field_alist = array();
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="2">Total</th>';
                //Opening
                if((int)$opn_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($opn_eqty[$key1]) || $opn_eqty[$key1] == ""){ } else{ $i_qty = $opn_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Production
                if((int)$prd_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($prd_eqty[$key1]) || $prd_eqty[$key1] == ""){ } else{ $i_qty = $prd_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Transfer-In
                if((int)$tin_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($tin_eqty[$key1]) || $tin_eqty[$key1] == ""){ } else{ $i_qty = $tin_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Sales
                if((int)$sale_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($sale_eqty[$key1]) || $sale_eqty[$key1] == ""){ } else{ $i_qty = $sale_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Transfer-Out
                if((int)$tout_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($tout_eqty[$key1]) || $tout_eqty[$key1] == ""){ } else{ $i_qty = $tout_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Conversion-In
                if((int)$cnvti_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($cin_eqty[$key1]) || $cin_eqty[$key1] == ""){ } else{ $i_qty = $cin_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Conversion-Out
                if((int)$cnvto_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($cout_eqty[$key1]) || $cout_eqty[$key1] == ""){ } else{ $i_qty = $cout_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Disposed
                if((int)$disp_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($dis_eqty[$key1]) || $dis_eqty[$key1] == ""){ } else{ $i_qty = $dis_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                //Closing
                if((int)$cls_flag == 1){
                    foreach($egg_code as $icode){
                        $key1 = $icode;
                        $i_qty = 0; if(empty($cls_eqty[$key1]) || $cls_eqty[$key1] == ""){ } else{ $i_qty = $cls_eqty[$key1]; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($i_qty,5))).'</th>';
                    }
                }
                $html .= '</tr>';
                $html .= '</tfoot>';
                $opn_eqty = $prd_eqty = $tin_eqty = $sale_eqty = $tout_eqty = $cin_eqty = $cout_eqty = $dis_eqty = $cls_eqty = array();
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function check_val(){
                var flocks = document.getElementById("flocks").value;
                var l = true;
                if(flocks == "select"){
                    alert("Please select Flock");
                    l = false;
                }
                else{ }

                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script>
            function table_sort() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var slno = '<?php echo $slno_flag; ?>';
                if(parseInt(slno) == 1){
                    var rcount = document.getElementById("tbody1").rows.length;
                    var myTable = document.getElementById('tbody1');
                    var j = 0;
                    for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
                }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search_table');
                const table = document.getElementById('main_table');
                const tableBody = table.querySelector('tbody');

                searchInput.addEventListener('input', () => {
                    const filter = searchInput.value.toLowerCase();
                    const rows = tableBody.querySelectorAll('tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    cdate_format1();
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }
        </script>
        <script>
            function fetch_flock_details(a){
                var f_aflag = u_aflag = s_aflag = b_aflag = fl_aflag = 0;
                var farms = units = sheds = batches = flocks = "";
                for(var option of document.getElementById("farms").options){ if(option.selected){ if(option.value == "all"){ f_aflag = 1; } else{ if(farms == ""){ farms = option.value; } else{ farms = farms+"@"+option.value; } } } }
                for(var option of document.getElementById("units").options){ if(option.selected){ if(option.value == "all"){ u_aflag = 1; } else{ if(units == ""){ units = option.value; } else{ units = units+"@"+option.value; } } } }
                for(var option of document.getElementById("sheds").options){ if(option.selected){ if(option.value == "all"){ s_aflag = 1; } else{ if(sheds == ""){ sheds = option.value; } else{ sheds = sheds+"@"+option.value; } } } }
                for(var option of document.getElementById("batches").options){ if(option.selected){ if(option.value == "all"){ b_aflag = 1; } else{ if(batches == ""){ batches = option.value; } else{ batches = batches+"@"+option.value; } } } }
                for(var option of document.getElementById("flocks").options){ if(option.selected){ if(option.value == "all"){ fl_aflag = 1; } else{ if(flocks == ""){ flocks = option.value; } else{ flocks = flocks+"@"+option.value; } } } }
                if(f_aflag == 1){ farms = ""; farms = "all"; }
                if(u_aflag == 1){ units = ""; units = "all"; }
                if(s_aflag == 1){ sheds = ""; sheds = "all"; }
                if(b_aflag == 1){ batches = ""; batches = "all"; }
                if(fl_aflag == 1){ flocks = ""; flocks = "all"; }

                var user_code = '<?php echo $user_code; ?>';
                var ff_flag = uf_flag = sf_flag = bf_flag = fl_flag = 0;
                if(a == "farms"){ ff_flag = 1; }
                else if(a == "units"){ uf_flag = 1; }
                else if(a == "sheds"){ sf_flag = 1; }
                else if(a == "batches"){ bf_flag = 1; }
                else if(a == "flocks"){ fl_flag = 1; }
                else{ ff_flag = 1; }
                
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&flocks="+flocks+"&ff_flag="+ff_flag+"&uf_flag="+uf_flag+"&sf_flag="+sf_flag+"&bf_flag="+bf_flag+"&fl_flag="+fl_flag+"&user_code="+user_code+"&fetch_type=multiple";
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var farm_list = fltr_dt2[0];
                        var unit_list = fltr_dt2[1];
                        var shed_list = fltr_dt2[2];
                        var batch_list = fltr_dt2[3];
                        var flock_list = fltr_dt2[4];

                        if(ff_flag == 1){
                            removeAllOptions(document.getElementById("units"));
                            removeAllOptions(document.getElementById("sheds"));
                            removeAllOptions(document.getElementById("batches"));
                            removeAllOptions(document.getElementById("flocks"));
                            $('#units').append(unit_list);
                            $('#sheds').append(shed_list);
                            $('#batches').append(batch_list);
                            $('#flocks').append(flock_list);
                        }
                        else if(uf_flag == 1){
                            removeAllOptions(document.getElementById("sheds"));
                            removeAllOptions(document.getElementById("batches"));
                            removeAllOptions(document.getElementById("flocks"));
                            $('#sheds').append(shed_list);
                            $('#batches').append(batch_list);
                            $('#flocks').append(flock_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("batches"));
                            removeAllOptions(document.getElementById("flocks"));
                            $('#batches').append(batch_list);
                            $('#flocks').append(flock_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("flocks"));
                            $('#flocks').append(flock_list);
                        }
                        else{ }
                    }
                }
            }
            var f_cnt = 0;
            function set_auto_selectors(){
                if(f_cnt == 0){
                    var fx = "farms"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 1){
                    var u_aflag = '<?php echo $u_aflag; ?>';
                    var u_val = ulist = "";
                    if(parseInt(u_aflag) == 0){
                        $('#units').select2();
                        for(var option of document.getElementById("units").options){
                            option.selected = false;
                            u_val = option.value;
                            <?php
                            foreach($units as $ulist){
                            ?>
                            ulist = ''; ulist = '<?php echo $ulist; ?>';
                            if(u_val == ulist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#units').select2();
                    }
                    var fx = "units"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    var s_aflag = '<?php echo $s_aflag; ?>';
                    var s_val = slist = "";
                    if(parseInt(s_aflag) == 0){
                        $('#sheds').select2();
                        for(var option of document.getElementById("sheds").options){
                            option.selected = false;
                            s_val = option.value;
                            <?php
                            foreach($sheds as $slist){
                            ?>
                            slist = ''; slist = '<?php echo $slist; ?>';
                            if(s_val == slist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#sheds').select2();
                    }
                    var fx = "sheds"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    var b_aflag = '<?php echo $b_aflag; ?>';
                    var b_val = blist = "";
                    if(parseInt(b_aflag) == 0){
                        $('#batches').select2();
                        for(var option of document.getElementById("batches").options){
                            option.selected = false;
                            b_val = option.value;
                            <?php
                            foreach($batches as $blist){
                            ?>
                            blist = ''; blist = '<?php echo $blist; ?>';
                            if(b_val == blist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#farms').select2();
                    }
                    f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    var fl_aflag = '<?php echo $fl_aflag; ?>';
                    var fl_val = fllist = "";
                    if(parseInt(fl_aflag) == 0){
                        $('#flocks').select2();
                        for(var option of document.getElementById("flocks").options){
                            option.selected = false;
                            fl_val = option.value;
                            <?php
                            foreach($flocks as $fllist){
                            ?>
                            fllist = ''; fllist = '<?php echo $fllist; ?>';
                            if(fl_val == fllist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#flocks').select2();
                    }
                    f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 4){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            function fetch_item_list() {
                var fcode = document.getElementById("item_cat").value.trim();
                var myselect = document.getElementById("items");
                removeAllOptions(myselect);
                var defaultOption = document.createElement("OPTION");
                defaultOption.text = "-All-";
                defaultOption.value = "all";
                myselect.add(defaultOption);
                if (fcode !== "all") {
                    <?php
                    foreach ($item_code as $icodes) {
                        $icats = $item_category[$icodes];
                        echo "if (fcode === '$icats') {";
                    ?>
                        var option = document.createElement("OPTION");
                        option.text = "<?php echo addslashes($item_name[$icodes]); ?>";
                        option.value = "<?php echo $icodes; ?>";
                        myselect.add(option);
                    <?php
                        echo "}";
                    }
                    ?>
                } else {
                    <?php
                    foreach ($item_code as $icodes) {
                    ?>
                        var option = document.createElement("OPTION");
                        option.text = "<?php echo addslashes($item_name[$icodes]); ?>";
                        option.value = "<?php echo $icodes; ?>";
                        myselect.add(option);
                    <?php
                    }
                    ?>
                }
            }
            function removeAllOptions(selectbox) {
                while (selectbox.options.length > 0) {
                    selectbox.remove(0);
                }
            }

       </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>