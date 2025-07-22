<?php
//breeder_medvac_consumed.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Medicine/Vaccine Consumed Report";
    include "header_head.php";
    $form_path = "breeder_medvac_consumed.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Medicine/Vaccine Consumed Report";
    include "header_head.php";
    $form_path = "breeder_medvac_consumed.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_breeder_breedermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_breeder_breedermaster.breeder_units;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_breeder_breedermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_batch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_batch LIKE poulso6_admin_breeder_breedermaster.breeder_batch;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_shed_allocation LIKE poulso6_admin_breeder_breedermaster.breeder_shed_allocation;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_dayentry_consumed", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_consumed LIKE poulso6_admin_breeder_breedermaster.breeder_dayentry_consumed;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_dayentry_produced", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_produced LIKE poulso6_admin_breeder_breedermaster.breeder_dayentry_produced;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_medicine_consumed", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_medicine_consumed LIKE poulso6_admin_breeder_breedermaster.breeder_medicine_consumed;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_extra_access LIKE poulso6_admin_breeder_breedermaster.breeder_extra_access;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_breed_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_breed_standards LIKE poulso6_admin_breeder_breedermaster.breeder_breed_standards;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_breed_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_breed_details LIKE poulso6_admin_breeder_breedermaster.breeder_breed_details;"; mysqli_query($conn,$sql1); }
if(in_array("item_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_details LIKE poulso6_admin_breeder_breedermaster.item_details;"; mysqli_query($conn,$sql1); }

$file_name = "Medicine/Vaccine Consumed Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

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

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code);

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $it_code = $it_name = array();
while($row = mysqli_fetch_assoc($query)){ $it_code[$row['code']] = $row['code']; $it_name[$row['code']] = $row['description']; $it_units[$row['code']] = $row['cunits']; }

//Breeder Bird Details
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Breeder Birds%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cbird_code = array();
while($row = mysqli_fetch_assoc($query)){ $cbird_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $bird_list = implode("','", $cbird_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bird_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $fbird_code = $mbird_code = "";
while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Female birds"){ $fbird_code = $row['code']; } else if($row['description'] == "Male birds"){ $mbird_code = $row['code']; } }

$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $batches = $flocks = array(); $excel_type = "display";
$farms["all"] = $units["all"] = $sheds["all"] = $batches["all"] = $flocks["all"] = "all";
$f_aflag = $u_aflag = $s_aflag = $b_aflag = $fl_aflag = 1;

if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
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

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0'".$farm_fltr."".$unit_fltr."".$shed_list."".$batch_list."".$flock_list." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_alist = array();
while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }

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
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div><div class="m-2 form-group" style="width:120px;">
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
            $html = $nhtml = $fhtml = ''; $e_cnt = $e_cnt + 8;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Sl No.</th>';
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Date</th>';
            $nhtml .= '<th>Batch Name</th>'; $fhtml .= '<th id="order">Batch Name</th>';
            $nhtml .= '<th>Flock Name</th>'; $fhtml .= '<th id="order">Flock Name</th>';
            $nhtml .= '<th>Item Code</th>'; $fhtml .= '<th id="order">Item Code</th>';
            $nhtml .= '<th>Medicine Name</th>'; $fhtml .= '<th id="order">Medicine Name</th>';
            $nhtml .= '<th>Consumed Quantity</th>'; $fhtml .= '<th id="order_num">Consumed Quantity</th>';
            $nhtml .= '<th>Units</th>'; $fhtml .= '<th id="order_num">Units</th>';
            $nhtml .= '<th>Avg Cost</th>'; $fhtml .= '<th id="order_num">Avg Cost</th>';
            $nhtml .= '<th>Total Value</th>'; $fhtml .= '<th id="order_num">Total Value</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                if(sizeof($flock_alist) > 0){
                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_medicine_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql); $sl = 1;
                    while($row = mysqli_fetch_assoc($query)){
                        $date1 = date("d.m.Y",strtotime($row['date'])); 
                        $batx_code = $batch_name[$row['batch_code']];
                        $fl_code = $flock_name[$row['flock_code']];
                        $itm_code = $row['item_code'];
                        $iname = $it_name[$row['item_code']];
                        $quantity = $row['quantity'];
                        $u_name = $it_units[$row['item_code']];
                        $mgmt_rate = $row['mgmt_rate'];
                        $mgmt_amt = $row['mgmt_amt'];

                        $html .= '<tr>';
                        $html .= '<td>'.$sl++.'</td>';
                        $html .= '<td>'.$date1.'</td>';
                        $html .= '<td>'.$batx_code.'</td>';
                        $html .= '<td>'.$fl_code.'</td>';
                        $html .= '<td>'.$itm_code.'</td>';
                        $html .= '<td>'.$iname.'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($quantity,5))).'</td>';
                        $html .= '<td>'.$u_name.'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mgmt_rate,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mgmt_amt,5))).'</td>';
                        $html .= '</tr>';

                        $tot_qty += (float)$quantity;
                        $tot_rate += (float)$mgmt_rate;
                        $tot_amt += (float)$mgmt_amt;
                     
                    }

                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="6">Total</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_qty,5)).'</th>';
                $html .= '<th></th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_rate,5)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_amt,5)).'</th>';
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