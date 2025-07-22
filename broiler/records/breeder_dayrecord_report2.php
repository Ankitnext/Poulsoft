<?php
//breeder_dayrecord_report2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Daily Farm Summary";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report2.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Daily Farm Summary";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report2.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";
$file_name = "Daily Farm Summary";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `broiler_purchases`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("unit_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `unit_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("shed_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `shed_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `unit_code`"; mysqli_query($conn,$sql); }
if(in_array("flock_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `flock_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `farm_batch`"; mysqli_query($conn,$sql); }
if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `trtype`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

//Breeder Extra Access
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Female Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $ffeed_2flag = mysqli_num_rows($query);
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Male Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $mfeed_2flag = mysqli_num_rows($query);
$sql = "SELECT *  FROM `breeder_extra_access` WHERE `field_name` LIKE 'breeder_dayrecord_report2.php' AND `field_function` LIKE 'Fetch Stock Detailed information' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $fsdi_dflag = mysqli_num_rows($query);

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_access`'; $query = mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("bfarms_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bfarms_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Farms Access List' AFTER `cgroup_access`"; mysqli_query($conn,$sql); }
if(in_array("bunits_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bunits_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Units Access List' AFTER `bfarms_list`"; mysqli_query($conn,$sql); }
if(in_array("bsheds_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bsheds_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Sheds Access List' AFTER `bunits_list`"; mysqli_query($conn,$sql); }
if(in_array("bbatch_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bbatch_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Batch Access List' AFTER `bsheds_list`"; mysqli_query($conn,$sql); }
if(in_array("bflock_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bflock_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Flock Access List' AFTER `bbatch_list`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bfarms_list = $row['bfarms_list']; $bunits_list = $row['bunits_list']; $bsheds_list = $row['bsheds_list']; $bbatch_list = $row['bbatch_list']; $bflock_list = $row['bflock_list']; }
if($bfarms_list == "all" || $bfarms_list == ""){ $bfarms_fltr1 = $bfarms_fltr2 = ""; } else{ $bfarms_list1 = implode("','", explode(",",$bfarms_list)); $bfarms_fltr1 = " AND `code` IN ('$bfarms_list1')"; $bfarms_fltr2 = " AND `farm_code` IN ('$bfarms_list1')"; }
if($bunits_list == "all" || $bunits_list == ""){ $bunits_fltr1 = $bunits_fltr2 = ""; } else{ $bunits_list1 = implode("','", explode(",",$bunits_list)); $bunits_fltr1 = " AND `code` IN ('$bunits_list1')"; $bunits_fltr2 = " AND `unit_code` IN ('$bunits_list1')"; }
if($bsheds_list == "all" || $bsheds_list == ""){ $bsheds_fltr1 = $bsheds_fltr2 = ""; } else{ $bsheds_list1 = implode("','", explode(",",$bsheds_list)); $bsheds_fltr1 = " AND `code` IN ('$bsheds_list1')"; $bsheds_fltr2 = " AND `shed_code` IN ('$bsheds_list1')"; }
if($bbatch_list == "all" || $bbatch_list == ""){ $bbatch_fltr1 = $bbatch_fltr2 = ""; } else{ $bbatch_list1 = implode("','", explode(",",$bbatch_list)); $bbatch_fltr1 = " AND `code` IN ('$bbatch_list1')"; $bbatch_fltr2 = " AND `batch_code` IN ('$bbatch_list1')"; }
if($bflock_list == "all" || $bflock_list == ""){ $bflock_fltr1 = $bflock_fltr2 = ""; } else{ $bflock_list1 = implode("','", explode(",",$bflock_list)); $bflock_fltr1 = " AND `code` IN ('$bflock_list1')"; $bflock_fltr2 = " AND `flock_code` IN ('$bflock_list1')"; }

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0'".$bfarms_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0'".$bunits_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0'".$bsheds_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0'".$bbatch_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_breed = array();
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_breed[$row['code']] = $row['breed_code']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0'".$bfarms_fltr2."".$bunits_fltr2."".$bsheds_fltr2."".$bbatch_fltr2."".$bflock_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_sdate = $flock_sage = $flock_batch = array();
while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_sdate[$row['code']] = $row['start_date']; $flock_sage[$row['code']] = $row['start_age']; $flock_batch[$row['code']] = $row['batch_code']; }

//Breeder Breed Standards
$sql = "SELECT * FROM `breeder_breed_standards` WHERE `dflag` = '0' ORDER BY `breed_code`,`breed_age` ASC";
$query = mysqli_query($conn,$sql); $fstd_fpbird = $mstd_fpbird = $fstd_bweight = $mstd_bweight = $std_he_per = array();
while($row = mysqli_fetch_assoc($query)){
    $key1 = $row['breed_code']."@".$row['breed_age'];
    $fstd_fpbird[$key1] = $row['ffeed_pbird'];
    $fstd_bweight[$key1] = $row['fbird_bweight'];
    $mstd_fpbird[$key1] = $row['mfeed_pbird'];
    $mstd_bweight[$key1] = $row['mbird_bweight'];
    $std_hd_per[$key1] = $row['hd_per'];
    $std_he_per[$key1] = $row['he_per'];
}

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code);

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Hatch Egg%' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $hegg_code = "";
while($row = mysqli_fetch_assoc($query)){ $hegg_code = $row['code']; }

//Breeder Bird Details
$sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%Breeder Birds%' OR `description` LIKE '%female bird%' OR `description` LIKE '%male bird%') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cbird_code = array();
while($row = mysqli_fetch_assoc($query)){ $cbird_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $bird_list = implode("','", $cbird_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bird_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $fbird_code = $mbird_code = "";
while($row = mysqli_fetch_assoc($query)){ if(strtolower($row['description']) == "female birds"){ $fbird_code = $row['code']; } else if(strtolower($row['description']) == "male birds"){ $mbird_code = $row['code']; } }

//Female Feed Details
$sql = "SELECT * FROM `item_category` WHERE `bffeed_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $ffcat_alist = array();
while($row = mysqli_fetch_assoc($query)){ $ffcat_alist[$row['code']] = $row['code']; } $ffcat_list = implode("','", $ffcat_alist);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$ffcat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $ffeed_code = $afeed_code = array();
while($row = mysqli_fetch_assoc($query)){ $ffeed_code[$row['code']] = $row['code']; $afeed_code[$row['code']] = $row['code']; }

//Male Feed Details
$sql = "SELECT * FROM `item_category` WHERE `bmfeed_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $mfcat_alist = array();
while($row = mysqli_fetch_assoc($query)){ $mfcat_alist[$row['code']] = $row['code']; } $mfcat_list = implode("','", $mfcat_alist);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$mfcat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $mfeed_code = array();
while($row = mysqli_fetch_assoc($query)){ $mfeed_code[$row['code']] = $row['code']; $afeed_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $batches = $flocks = array(); $excel_type = "display";
$farms["all"] = $units["all"] = $sheds["all"] = $batches["all"] = $flocks["all"] = "all";
$f_aflag = $u_aflag = $s_aflag = $b_aflag = $fl_aflag = 1;

if(isset($_POST['submit_report']) == true){
    $fdate = $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $farms = $units = $sheds = $batches = $flocks = array(); $f_aflag = $u_aflag = $s_aflag = $b_aflag = $fl_aflag = 0;
    foreach($_POST['farms'] as $t1){ $farms[$t1] = $t1; }           foreach($farms as $t1){ if($t1 == "all"){ $f_aflag = 1; } }
    foreach($_POST['units'] as $t1){ $units[$t1] = $t1; }           foreach($units as $t1){ if($t1 == "all"){ $u_aflag = 1; } }
    foreach($_POST['sheds'] as $t1){ $sheds[$t1] = $t1; }           foreach($sheds as $t1){ if($t1 == "all"){ $s_aflag = 1; } }
    foreach($_POST['batches'] as $t1){ $batches[$t1] = $t1; }       foreach($batches as $t1){ if($t1 == "all"){ $b_aflag = 1; } }
    foreach($_POST['flocks'] as $t1){ $flocks[$t1] = $t1; }         foreach($flocks as $t1){ if($t1 == "all"){ $fl_aflag = 1; } }
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
$query = mysqli_query($conn,$sql); $flock_alist = $unit_alist = $shed_alist = $flock_unit1 = $flock_shed1 = array();
while($row = mysqli_fetch_assoc($query)){
    $flock_alist[$row['code']] = $row['code']; $unit_alist[$row['unit_code']] = $row['unit_code']; $shed_alist[$row['shed_code']] = $row['shed_code'];
    $flock_unit1[$row['unit_code']] = $row['code']; $flock_shed1[$row['shed_code']] = $row['code'];
}

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
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>Date</label>
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
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
        </table>
        <table id="main_table" class="tbl" align="center">
            <?php
            $html = $nhtml = $fhtml = ''; $e_cnt = $e_cnt + 3; $fcon_cnt = 4; if((int)$ffeed_2flag == 1){  $fcon_cnt += 2; } if((int)$mfeed_2flag == 1){  $fcon_cnt += 2; }
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="2">Opening Birds</th>'; $fhtml .= '<th colspan="2">Opening Birds</th>';
            $nhtml .= '<th colspan="2">Mortality</th>'; $fhtml .= '<th colspan="2">Mortality</th>';
            $nhtml .= '<th colspan="2">Culls</th>'; $fhtml .= '<th colspan="2">Culls</th>';
            $nhtml .= '<th colspan="2">Transfer In</th>'; $fhtml .= '<th colspan="2">Transfer In</th>';
            $nhtml .= '<th colspan="2">Transfer Out</th>'; $fhtml .= '<th colspan="2">Transfer Out</th>';
            $nhtml .= '<th colspan="2">Closing Birds</th>'; $fhtml .= '<th colspan="2">Closing Birds</th>';
            if((int)$fsdi_dflag > 0){
                $nhtml .= '<th colspan="2">Feed Opening</th>'; $fhtml .= '<th colspan="2">Feed Opening</th>';
                $nhtml .= '<th colspan="2">Feed Received</th>'; $fhtml .= '<th colspan="2">Feed Received</th>';
                $nhtml .= '<th colspan="2">Feed Consumed</th>'; $fhtml .= '<th colspan="2">Feed Consumed</th>';
                $nhtml .= '<th colspan="2">Feed Transfer-Out</th>'; $fhtml .= '<th colspan="2">Feed Transfer-Out</th>';
                $nhtml .= '<th colspan="2">Feed balance</th>'; $fhtml .= '<th colspan="2">Feed balance</th>';
            }
            else{
                $nhtml .= '<th colspan="'.$fcon_cnt.'">Feed Consumed</th>'; $fhtml .= '<th colspan="'.$fcon_cnt.'">Feed Consumed</th>';
            }
            $nhtml .= '<th colspan="2">Feed / Bird (Gms)</th>'; $fhtml .= '<th colspan="2">Feed / Bird (Gms)</th>';
            $nhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Production</th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No</th>'; $fhtml .= '<th id="order_num">Sl.No</th>';
            $nhtml .= '<th>Flock No.</th>'; $fhtml .= '<th id="order">Flock No.</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            if((int)$fsdi_dflag > 0){
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            }
            else{
                $nhtml .= '<th>F.Feed</th>'; $fhtml .= '<th id="order_num">F.Feed</th>';
                $nhtml .= '<th>Qty</th>'; $fhtml .= '<th id="order_num">Qty</th>';
                if((int)$ffeed_2flag == 1){
                    $nhtml .= '<th>F.Feed-2</th>'; $fhtml .= '<th id="order_num">F.Feed-2</th>';
                    $nhtml .= '<th>Qty-2</th>'; $fhtml .= '<th id="order_num">Qty-2</th>';
                }
                $nhtml .= '<th>M.Feed</th>'; $fhtml .= '<th id="order_num">M.Feed</th>';
                $nhtml .= '<th>Qty</th>'; $fhtml .= '<th id="order_num">Qty</th>';
                if((int)$mfeed_2flag == 1){
                    $nhtml .= '<th>M.Feed-2</th>'; $fhtml .= '<th id="order_num">M.Feed-2</th>';
                    $nhtml .= '<th>Qty</th>'; $fhtml .= '<th id="order_num">Qty</th>';
                }
            }
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            foreach($egg_code as $eggs){
                if($hegg_code == $eggs){
                    $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>';
                    $nhtml .= '<th>HE%</th>'; $fhtml .= '<th id="order_num">HE%</th>';
                }
                else{
                    $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>';
                }
            }
            $nhtml .= '<th>Total Eggs</th>'; $fhtml .= '<th id="order_num">Total Eggs</th>';
            $nhtml .= '<th>Std.HD%</th>'; $fhtml .= '<th id="order_num">Std.HD%</th>';
            $nhtml .= '<th>HD%</th>'; $fhtml .= '<th id="order_num">HD%</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                if(sizeof($flock_alist) > 0){
                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    $fmort_qty = $fcull_qty = $fbody_weight = $ffeed_code1 = $ffeed_qty1 = $ffeed_code2 = $ffeed_qty2 = $mfeed_code1 = $mfeed_qty1 = $mfeed_code2 = $mfeed_qty2 = $mmort_qty = $mcull_qty = 
                    $mbody_weight = $egg_weight = $flock_alist = $ocon_ffqty = $ocon_mfqty = $bcon_ffqty = $bcon_mfqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['flock_code'];
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $ocon_ffqty[$key1] += (float)$row['ffeed_qty1'] + (float)$row['ffeed_qty2'];
                            $ocon_mfqty[$key1] += (float)$row['mfeed_qty1'] + (float)$row['mfeed_qty2'];
                        }
                        else{
                            $fmort_qty[$key1] += (float)$row['fmort_qty'];
                            $fcull_qty[$key1] += (float)$row['fcull_qty'];
                            $fbody_weight[$key1] = (float)$row['fbody_weight'];
                            $ffeed_code1[$key1] = $row['ffeed_code1'];
                            $ffeed_qty1[$key1] = (float)$row['ffeed_qty1'];
                            $ffeed_code2[$key1] = $row['ffeed_code2'];
                            $ffeed_qty2[$key1] = (float)$row['ffeed_qty2'];
                            $mfeed_code1[$key1] = $row['mfeed_code1'];
                            $mfeed_qty1[$key1] = (float)$row['mfeed_qty1'];
                            $mfeed_code2[$key1] = $row['mfeed_code2'];
                            $mfeed_qty2[$key1] = (float)$row['mfeed_qty2'];
                            $mmort_qty[$key1] += (float)$row['mmort_qty'];
                            $mcull_qty[$key1] += (float)$row['mcull_qty'];
                            $mbody_weight[$key1] = (float)$row['mbody_weight'];
                            $egg_weight[$key1] = (float)$row['egg_weight'];
                            $breed_wage[$key1] = $row['breed_wage'];
                            $breed_age[$key1] = $row['breed_age'];
                            $de_remarks[$key1] = $row['remarks'];
                            
                            $bcon_ffqty[$key1] += (float)$row['ffeed_qty1'] + (float)$row['ffeed_qty2'];
                            $bcon_mfqty[$key1] += (float)$row['mfeed_qty1'] + (float)$row['mfeed_qty2'];
                            $flock_alist[$key1] = $key1;
                        }
                    }
                    if((int)$fsdi_dflag > 0){
                        //Check Feed Stock mgmt flag Wise and fetch Stock Details
                        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
                        $query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query); $bfeed_stkon = "";
                        while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
                        $sec_fltr1 = $sec_fltr2 = $sec_fltr3 = $sec_fltr4 = ""; $bfeed_list = implode("','",$afeed_code);
                        if($bfeed_stkon == "UNIT"){
                            $unit_list = implode("','",$unit_alist);
                            $sec_fltr1 = " AND `warehouse` IN ('$unit_list')";
                            $sec_fltr2 = " AND `towarehouse` IN ('$unit_list')";
                            $sec_fltr3 = " AND `fromwarehouse` IN ('$unit_list')";
                            $sec_fltr4 = " AND `sector_code` IN ('$unit_list')";
                        }
                        else if($bfeed_stkon == "SHED"){
                            $shed_list = implode("','",$shed_alist);
                            $sec_fltr1 = " AND `warehouse` IN ('$shed_list')";
                            $sec_fltr2 = " AND `towarehouse` IN ('$shed_list')";
                            $sec_fltr3 = " AND `fromwarehouse` IN ('$shed_list')";
                            $sec_fltr4 = " AND `sector_code` IN ('$shed_list')";
                        }
                        else{
                            $flock_list = implode("','",$flock_alist);
                            $sec_fltr1 = " AND `flock_code` IN ('$flock_list')";
                            $sec_fltr2 = " AND `to_flock` IN ('$flock_list')";
                            $sec_fltr3 = " AND `from_flock` IN ('$flock_list')";
                            $sec_fltr4 = " AND `sector_code` IN ('$flock_list')";
                        }
                        //Openings
                        $sql = "SELECT * FROM `broiler_openings` WHERE `date` <= '$tdate' AND `type_code` IN ('$bfeed_list')".$sec_fltr4." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql); $oipn_ffqty = $bipn_ffqty = $oipn_mfqty = $bipn_mfqty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['type_code']; $isector = $row['sector_code']; $iflock = $row['sector_code']; $iqty = (float)$row['quantity'];
                            if($bfeed_stkon == "UNIT"){ $key1 = $flock_unit1[$isector]; } else if($bfeed_stkon == "SHED"){ $key1 = $flock_shed1[$isector]; } else{ $key1 = $iflock; }
                            if(strtotime($idate) < strtotime($fdate)){
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $oipn_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $oipn_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $bipn_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $bipn_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        //Purchase
                        $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$bfeed_list')".$sec_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql); $opur_ffqty = $bpur_ffqty = $opur_mfqty = $bpur_mfqty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['icode']; $isector = $row['warehouse']; $iflock = $row['flock_code']; $iqty = ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            if($bfeed_stkon == "UNIT"){ $key1 = $flock_unit1[$isector]; } else if($bfeed_stkon == "SHED"){ $key1 = $flock_shed1[$isector]; } else{ $key1 = $iflock; }
                            if(strtotime($idate) < strtotime($fdate)){
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $opur_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $opur_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $bpur_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $bpur_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        //Stock-In
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$bfeed_list')".$sec_fltr2." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql); $otin_ffqty = $btin_ffqty = $otin_mfqty = $btin_mfqty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['code']; $isector = $row['towarehouse']; $iflock = $row['to_flock']; $iqty = (float)$row['quantity'];
                            if($bfeed_stkon == "UNIT"){ $key1 = $flock_unit1[$isector]; } else if($bfeed_stkon == "SHED"){ $key1 = $flock_shed1[$isector]; } else{ $key1 = $iflock; }
                            if(strtotime($idate) < strtotime($fdate)){
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $otin_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $otin_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $btin_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $btin_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        //Stock-Out
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$bfeed_list')".$sec_fltr3." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql); $oout_ffqty = $bout_ffqty = $oout_mfqty = $bout_mfqty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['code']; $isector = $row['fromwarehouse']; $iflock = $row['from_flock']; $iqty = (float)$row['quantity'];
                            if($bfeed_stkon == "UNIT"){ $key1 = $flock_unit1[$isector]; } else if($bfeed_stkon == "SHED"){ $key1 = $flock_shed1[$isector]; } else{ $key1 = $iflock; }
                            if(strtotime($idate) < strtotime($fdate)){
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $oout_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $oout_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if(!empty($ffeed_code[$icode]) && $ffeed_code[$icode] == $icode){
                                    $bout_ffqty[$key1] += (float)$iqty;
                                }
                                else if(!empty($mfeed_code[$icode]) && $mfeed_code[$icode] == $icode){
                                    $bout_mfqty[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                    }

                    //Other Calcualtions
                    $flock_list = implode("','",$flock_alist); $coa_list = implode("','",$icat_iac);
                    $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$coa_list') AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                    $query = mysqli_query($conn,$sql);
                    $fflk_obirds = $fflk_cr_birds = $fflk_dr_birds = $mflk_obirds = $mflk_cr_birds = $mflk_dr_birds = $egg_pqty = $tegg_pqty = $flock_alist = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $icrdr = $row['crdr']; $idate = $row['date']; $icode = $row['item_code']; $iqty = $row['quantity']; $ietype = $row['etype']; $key1 = $row['flock_code'];

                        if($icode == $fbird_code){
                            //Female Bird Calculations
                            if(strtotime($idate) < strtotime($fdate)){
                                if($icrdr == "DR"){
                                    $fflk_obirds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR"){
                                    $fflk_obirds[$key1] -= (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if($icrdr == "DR" && ($ietype == "Breeder-Female Bird Transfer In" || $ietype == "Breeder-Female Opening Birds" || $ietype == "Breeder-Purchased Items" || $ietype == "Breeder-Free Items")){
                                    $fflk_dr_birds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR" && $ietype == "Breeder-Female Bird Transfer Out"){
                                    $fflk_cr_birds[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        else if($icode == $mbird_code){
                            //Male Bird Calculations
                            if(strtotime($idate) < strtotime($fdate)){
                                if($icrdr == "DR"){
                                    $mflk_obirds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR"){
                                    $mflk_obirds[$key1] -= (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if($icrdr == "DR" && ($ietype == "Breeder-Male Bird Transfer In" || $ietype == "Breeder-Male Opening Birds" || $ietype == "Breeder-Purchased Items" || $ietype == "Breeder-Free Items")){
                                    $mflk_dr_birds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR" && $ietype == "Breeder-Male Bird Transfer Out"){
                                    $mflk_cr_birds[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        else if(!empty($egg_code[$icode]) && $egg_code[$icode] == $icode){
                            //Egg Calculations
                            if(strtotime($idate) < strtotime($fdate)){

                            }
                            else{
                                if($icrdr == "DR" && $ietype == "Breeder-Egg Production"){
                                    $key2 = $key1."@".$icode;
                                    $egg_pqty[$key2] += (float)$iqty;
                                    $tegg_pqty[$key1] += (float)$iqty;
                                }
                            }
                        }

                        $flock_alist[$key1] = $key1;
                    }

                    $topn_ffqty = $topn_mfqty = $trcd_ffqty = $trcd_mfqty = $tcon_ffqty = $tcon_mfqty = $tout_ffqty = $tout_mfqty = $tbal_ffqty = $tbal_mfqty = 0;
                    if(sizeof($flock_alist) > 0){
                        $flock_list = implode("','",$flock_alist);
                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' AND `code` IN ('$flock_list') ORDER BY `description` ASC";
                        $query = mysqli_query($conn,$sql); $flock_alist = array();
                        while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }

                        $slno = 0; $egg_cqty = array();
                        $tfopn_birds = $tmopn_birds = $tfmort_birds = $tmmort_birds = $tfcull_birds = $tmcull_birds = $tftrin_birds = $tmtrin_birds = $tftrout_birds = $tmtrout_birds = $tfflk_cbirds = $tmflk_cbirds = 0;
                        $tegg_rqty = $tstd_egg_pper = $tstd_hep = $tact_hep = $ff_qty1 = $ff_qty2 = $mf_qty1 = $mf_qty2 = $tff_qty = $tmf_qty = $hegg_rqty = 0;
                        foreach($flock_alist as $key1){
                            $slno++;
                            $flk_name = $flock_name[$key1];
                            $bird_age = $bird_wage = 0;
                            if(!empty($breed_age[$key1])){
                                $bird_age = $breed_age[$key1];
                                $bird_wage = age_in_weeks($bird_age);
                            }
                            else if(!empty($flock_sage[$key1])){
                                $bird_age = $flock_sage[$key1];
                                $bird_wage = age_in_weeks($bird_age);
                            }
                            $key2 = $batch_breed[$flock_batch[$key1]]."@".fetch_cweek($bird_wage);
                            
                            if(empty($fflk_obirds[$key1]) || $fflk_obirds[$key1] == ""){ $fopn_birds = 0; } else{ $fopn_birds = $fflk_obirds[$key1]; }
                            if(empty($fmort_qty[$key1]) || $fmort_qty[$key1] == ""){ $fmort_birds = 0; } else{ $fmort_birds = $fmort_qty[$key1]; }
                            if(empty($fcull_qty[$key1]) || $fcull_qty[$key1] == ""){ $fcull_birds = 0; } else{ $fcull_birds = $fcull_qty[$key1]; }
                            if(empty($fflk_dr_birds[$key1]) || $fflk_dr_birds[$key1] == ""){ $ftrin_birds = 0; } else{ $ftrin_birds = $fflk_dr_birds[$key1]; }
                            if(empty($fflk_cr_birds[$key1]) || $fflk_cr_birds[$key1] == ""){ $ftrout_birds = 0; } else{ $ftrout_birds = $fflk_cr_birds[$key1]; }
                            $fflk_cbirds = ((int)$fopn_birds - ((int)$fmort_birds + (int)$fcull_birds + (int)$ftrout_birds) + (int)$ftrin_birds);
                            $ftitle = "$fflk_cbirds = ((int)$fopn_birds - ((int)$fmort_birds + (int)$fcull_birds + (int)$ftrout_birds) + (int)$ftrin_birds);";

                            if(empty($mflk_obirds[$key1]) || $mflk_obirds[$key1] == ""){ $mopn_birds = 0; } else{ $mopn_birds = $mflk_obirds[$key1]; }
                            if(empty($mmort_qty[$key1]) || $mmort_qty[$key1] == ""){ $mmort_birds = 0; } else{ $mmort_birds = $mmort_qty[$key1]; }
                            if(empty($mcull_qty[$key1]) || $mcull_qty[$key1] == ""){ $mcull_birds = 0; } else{ $mcull_birds = $mcull_qty[$key1]; }
                            if(empty($mflk_cr_birds[$key1]) || $mflk_cr_birds[$key1] == ""){ $mtrout_birds = 0; } else{ $mtrout_birds = $mflk_cr_birds[$key1]; }
                            if(empty($mflk_dr_birds[$key1]) || $mflk_dr_birds[$key1] == ""){ $mtrin_birds = 0; } else{ $mtrin_birds = $mflk_dr_birds[$key1]; }
                            $mflk_cbirds = ((int)$mopn_birds - ((int)$mmort_birds + (int)$mcull_birds + (int)$mtrout_birds) + (int)$mtrin_birds);
                            $mtitle = "$mflk_cbirds = ((int)$mopn_birds - ((int)$mmort_birds + (int)$mcull_birds + (int)$mtrout_birds) + (int)$mtrin_birds);";
                            
                            $html .= '<tr>';
                            $html .= '<td>'.$slno.'</td>';
                            $html .= '<td>'.$flk_name.'</td>';
                            $html .= '<td style="text-align:center;">'.$bird_wage.'</td>';
                            //Opening Birds
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fopn_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mopn_birds,5))).'</td>';
                            //Mortality
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fmort_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mmort_birds,5))).'</td>';
                            //Culls
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fcull_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mcull_birds,5))).'</td>';
                            //Transfer In
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftrin_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtrin_birds,5))).'</td>';
                            //Transfer-Out
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftrout_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtrout_birds,5))).'</td>';
                            //Closing Birds
                            $html .= '<td style="text-align:right;" title="'.$ftitle.'">'.str_replace(".00","",number_format_ind(round($fflk_cbirds,5))).'</td>';
                            $html .= '<td style="text-align:right;" title="'.$mtitle.'">'.str_replace(".00","",number_format_ind(round($mflk_cbirds,5))).'</td>';

                            //Feed Consumed Details
                            if((int)$fsdi_dflag > 0){
                                //Opening
                                $opn_ffqty = (((float)$opur_ffqty[$key1] + (float)$otin_ffqty[$key1] + (float)$oipn_ffqty[$key1]) - ((float)$ocon_ffqty[$key1] + (float)$oout_ffqty[$key1]));
                                //echo "<br/>$opn_ffqty = (((float)$opur_ffqty[$key1] + (float)$otin_ffqty[$key1] + (float)$oipn_ffqty[$key1]) - ((float)$ocon_ffqty[$key1] + (float)$oout_ffqty[$key1]));";
                                $opn_mfqty = (((float)$opur_mfqty[$key1] + (float)$otin_mfqty[$key1] + (float)$oipn_mfqty[$key1]) - ((float)$ocon_mfqty[$key1] + (float)$oout_mfqty[$key1]));
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($opn_ffqty,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($opn_mfqty,5)).'</td>';
                                //Received
                                $rcd_ffqty = (((float)$bpur_ffqty[$key1] + (float)$btin_ffqty[$key1] + (float)$bipn_ffqty[$key1]));
                                $rcd_mfqty = (((float)$bpur_mfqty[$key1] + (float)$btin_mfqty[$key1] + (float)$bipn_mfqty[$key1]));
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($rcd_ffqty,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($rcd_mfqty,5)).'</td>';
                                //Consumed
                                $con_ffqty = (float)$bcon_ffqty[$key1];
                                $con_mfqty = (float)$bcon_mfqty[$key1];
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($con_ffqty,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($con_mfqty,5)).'</td>';
                                //Transfer-Out
                                $out_ffqty = (float)$bout_ffqty[$key1];
                                $out_mfqty = (float)$bout_mfqty[$key1];
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($out_ffqty,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($out_mfqty,5)).'</td>';
                                //Balance
                                $bal_ffqty = (((float)$opn_ffqty + (float)$rcd_ffqty) - ((float)$con_ffqty + (float)$out_ffqty));
                                $bal_mfqty = (((float)$opn_mfqty + (float)$rcd_mfqty) - ((float)$con_mfqty + (float)$out_mfqty));
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($bal_ffqty,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($bal_mfqty,5)).'</td>';

                                //Totals
                                $topn_ffqty += (float)$opn_ffqty;
                                $topn_mfqty += (float)$opn_mfqty;
                                $trcd_ffqty += (float)$rcd_ffqty;
                                $trcd_mfqty += (float)$rcd_mfqty;
                                $tcon_ffqty += (float)$con_ffqty;
                                $tcon_mfqty += (float)$con_mfqty;
                                $tout_ffqty += (float)$out_ffqty;
                                $tout_mfqty += (float)$out_mfqty;
                                $tbal_ffqty += (float)$bal_ffqty;
                                $tbal_mfqty += (float)$bal_mfqty;
                            }
                            else{
                                $icode = $ffeed_code1[$key1]; $iname = $item_name[$icode]; $iqty = $ffeed_qty1[$key1]; $ff_qty1 += (float)$iqty;
                                $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($iqty,5))).'</td>';
                                if((int)$ffeed_2flag == 1){
                                    $icode = $ffeed_code2[$key1]; $iname = $item_name[$icode]; $iqty = $ffeed_qty2[$key1]; $ff_qty2 += (float)$iqty;
                                    $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($iqty,5))).'</td>';
                                }
                                $icode = $mfeed_code1[$key1]; $iname = $item_name[$icode]; $iqty = $mfeed_qty1[$key1]; $mf_qty1 += (float)$iqty;
                                $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($iqty,5))).'</td>';
                                if((int)$mfeed_2flag == 1){
                                    $icode = $mfeed_code2[$key1]; $iname = $item_name[$icode]; $iqty = $mfeed_qty2[$key1]; $mf_qty2 += (float)$iqty;
                                    $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($iqty,5))).'</td>';
                                }
                            }

                            //Feed per Birds
                            $ff_qty = $ff_pbird = $mf_qty = $mf_pbird = 0;
                            $ff_qty = (float)$ffeed_qty1[$key1] + (float)$ffeed_qty2[$key1]; if((float)$fopn_birds != 0){ $ff_pbird = (((float)$ff_qty / (float)$fopn_birds) * 1000); } $tff_qty += (float)$ff_qty;
                            $mf_qty = (float)$mfeed_qty1[$key1] + (float)$mfeed_qty2[$key1]; if((float)$mopn_birds != 0){ $mf_pbird = (((float)$mf_qty / (float)$mopn_birds) * 1000); } $tmf_qty += (float)$mf_qty;
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($ff_pbird,2).'</td>';
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($mf_pbird,2).'</td>';

                            //Egg Production Details
                            $egg_rqty = 0;
                            foreach($egg_code as $eggs){
                                $key3 = $key1."@".$eggs; if(empty($egg_pqty[$key3]) || $egg_pqty[$key3] == ""){ $egg_qty = 0; } else{ $egg_qty = $egg_pqty[$key3]; }

                                //check Hatch Eggs
                                if($hegg_code == $eggs){
                                    $hegg_rqty += (float)$egg_qty; $hegg_per = 0;
                                    if((float)$tegg_pqty[$key1] != 0){ $hegg_per = (((float)$egg_qty / (float)$tegg_pqty[$key1]) * 100); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_qty,5))).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round(($hegg_per),5))).'</td>';
                                }
                                else{
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_qty,5))).'</td>';
                                }

                                $egg_rqty += (float)$egg_qty;
                                $egg_cqty[$eggs] += (float)$egg_qty;
                            }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_rqty,5))).'</td>';

                            $std_egg_pper = $std_hd_per[$key2];
                            $act_egg_pper = 0; if((float)$fopn_birds != 0){ $act_egg_pper = round((((float)$egg_rqty / (float)$fopn_birds) * 100),2); }
                            $html .= '<td style="text-align:right;">'.number_format_ind($std_egg_pper).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($act_egg_pper).'</td>';
                            
                            $html .= '<td style="text-align:left;">'.$de_remarks[$key1].'</td>';
                            $html .= '</tr>';


                            $tfopn_birds += (float)$fopn_birds;
                            $tmopn_birds += (float)$mopn_birds;
                            $tfmort_birds += (float)$fmort_birds;
                            $tmmort_birds += (float)$mmort_birds;
                            $tfcull_birds += (float)$fcull_birds;
                            $tmcull_birds += (float)$mcull_birds;
                            $tftrin_birds += (float)$ftrin_birds;
                            $tmtrin_birds += (float)$mtrin_birds;
                            $tftrout_birds += (float)$ftrout_birds;
                            $tmtrout_birds += (float)$mtrout_birds;
                            $tfflk_cbirds += (float)$fflk_cbirds;
                            $tmflk_cbirds += (float)$mflk_cbirds;
                            $tegg_rqty += (float)$egg_rqty;
                        }
                    }
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="3">Total</th>';
                //Opening Birds
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfopn_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmopn_birds,5))).'</th>';
                //Mortality
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfmort_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmmort_birds,5))).'</th>';
                //Culls
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfcull_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmcull_birds,5))).'</th>';
                //Transfer-In
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tftrin_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmtrin_birds,5))).'</th>';
                //transfer-out
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tftrout_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmtrout_birds,5))).'</th>';
                //Closing Birds
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfflk_cbirds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmflk_cbirds,5))).'</th>';
                //Feed Consumed Details
                if((int)$fsdi_dflag > 0){
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($topn_ffqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($topn_mfqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($trcd_ffqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($trcd_mfqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tcon_ffqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tcon_mfqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tout_ffqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tout_mfqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tbal_ffqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tbal_mfqty,5))).'</th>';
                }
                else{
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ff_qty1,5))).'</th>';
                    if((int)$ffeed_2flag == 1){
                        $html .= '<th style="text-align:right;"></th>';
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ff_qty2,5))).'</th>';
                    }
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mf_qty1,5))).'</th>';
                    if((int)$mfeed_2flag == 1){
                        $html .= '<th style="text-align:right;"></th>';
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mf_qty2,5))).'</th>';
                    }
                }
                //Feed per Bird
                $act_ffpbird = 0; if((float)$tfopn_birds != 0){ $act_ffpbird = round((((float)$tff_qty / (float)$tfopn_birds) * 1000),2); }
                $html .= '<th style="text-align:right;">'.number_format_ind(round($act_ffpbird,5)).'</th>';
                $act_mfpbird = 0; if((float)$tmopn_birds != 0){ $act_mfpbird = round((((float)$tmf_qty / (float)$tmopn_birds) * 1000),2); }
                $html .= '<th style="text-align:right;">'.number_format_ind(round($act_mfpbird,5)).'</th>';

                //Egg Prod Details
                $avg_sdeper = 0; if((float)$slno != 0){ $avg_sdeper = round((((float)$tstd_egg_pper / (float)$slno)),2); }
                $tot_hdper = 0; if((float)$tfopn_birds != 0){ $tot_hdper = round((((float)$tegg_rqty / (float)$tfopn_birds) * 100),2); }
                foreach($egg_code as $eggs){
                    if($hegg_code == $eggs){
                        $hegg_per = 0; if((float)$tegg_rqty != 0){ $hegg_per = (((float)$egg_cqty[$eggs] / (float)$tegg_rqty) * 100); }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_cqty[$eggs],5))).'</th>';
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($hegg_per,5))).'</th>';
                    }
                    else{
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_cqty[$eggs],5))).'</th>';
                    }
                }
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_rqty,5))).'</th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_hdper,5))).'</th>';
                $html .= '<th style="text-align:right;"></th>';

                $html .= '</tr>';
                $html .= '</tfoot>';
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
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
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
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
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>