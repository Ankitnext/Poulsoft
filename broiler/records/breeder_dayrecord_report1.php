<?php
//breeder_dayrecord_report1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";

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
if(in_array("breeder_extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_extra_access LIKE poulso6_admin_broiler_broilermaster.breeder_extra_access;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_breed_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_breed_standards LIKE poulso6_admin_broiler_broilermaster.breeder_breed_standards;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_breed_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_breed_details LIKE poulso6_admin_broiler_broilermaster.breeder_breed_details;"; mysqli_query($conn,$sql1); }

$file_name = "Day Record Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_breed = array();
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_breed[$row['code']] = $row['breed_code']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' ORDER BY `description` ASC";
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
    $std_he_per[$key1] = $row['he_per'];
}

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code);

//Breeder Bird Details
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Breeder Birds%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cbird_code = array();
while($row = mysqli_fetch_assoc($query)){ $cbird_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $bird_list = implode("','", $cbird_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bird_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $fbird_code = $mbird_code = "";
while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Female birds"){ $fbird_code = $row['code']; } else if($row['description'] == "Male birds"){ $mbird_code = $row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Hatch Egg%' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $hegg_code = "";
while($row = mysqli_fetch_assoc($query)){ $hegg_code = $row['code']; }

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
            $html = $nhtml = $fhtml = ''; $e_cnt = $e_cnt + 8;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th colspan="3"></th>'; $fhtml .= '<th colspan="3"></th>';
            $nhtml .= '<th colspan="9">Female Birds</th>'; $fhtml .= '<th colspan="9">Female Birds</th>';
            $nhtml .= '<th colspan="2">Body Weight</th>'; $fhtml .= '<th colspan="2">Body Weight</th>';
            $nhtml .= '<th colspan="9">Male Birds</th>'; $fhtml .= '<th colspan="9">Male Birds</th>';
            $nhtml .= '<th colspan="2">Body Weight</th>'; $fhtml .= '<th colspan="2">Body Weight</th>';
            $nhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Production</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No</th>'; $fhtml .= '<th id="order_num">Sl.No</th>';
            $nhtml .= '<th>Flock</th>'; $fhtml .= '<th id="order">Flock</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>Opening Birds</th>'; $fhtml .= '<th id="order_num">Opening Birds</th>';
            $nhtml .= '<th>Mortality</th>'; $fhtml .= '<th id="order_num">Mortality</th>';
            $nhtml .= '<th>Culls</th>'; $fhtml .= '<th id="order_num">Culls</th>';
            $nhtml .= '<th>Transfer In.</th>'; $fhtml .= '<th id="order_num">Transfer In.</th>';
            $nhtml .= '<th>Transfer Out</th>'; $fhtml .= '<th id="order_num">Transfer Out</th>';
            $nhtml .= '<th>Closing Birds</th>'; $fhtml .= '<th id="order_num">Closing Birds</th>';
            $nhtml .= '<th>Feed Consumed</th>'; $fhtml .= '<th id="order_num">Feed Consumed</th>';
            $nhtml .= '<th>Std. Feed / Bird</th>'; $fhtml .= '<th id="order_num">Std. Feed / Bird</th>';
            $nhtml .= '<th>A. Feed /Bird</th>'; $fhtml .= '<th id="order_num">A. Feed /Bird</th>';
            $nhtml .= '<th>Stadard</th>'; $fhtml .= '<th id="order_num">Stadard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Opening Birds</th>'; $fhtml .= '<th id="order_num">Opening Birds</th>';
            $nhtml .= '<th>Mortality</th>'; $fhtml .= '<th id="order_num">Mortality</th>';
            $nhtml .= '<th>Culls</th>'; $fhtml .= '<th id="order_num">Culls</th>';
            $nhtml .= '<th>Transfer In.</th>'; $fhtml .= '<th id="order_num">Transfer In.</th>';
            $nhtml .= '<th>Transfer Out</th>'; $fhtml .= '<th id="order_num">Transfer Out</th>';
            $nhtml .= '<th>Closing Birds</th>'; $fhtml .= '<th id="order_num">Closing Birds</th>';
            $nhtml .= '<th>Feed Consumed</th>'; $fhtml .= '<th id="order_num">Feed Consumed</th>';
            $nhtml .= '<th>Std. Feed / Bird</th>'; $fhtml .= '<th id="order_num">Std. Feed / Bird</th>';
            $nhtml .= '<th>A. Feed /Bird</th>'; $fhtml .= '<th id="order_num">A. Feed /Bird</th>';
            $nhtml .= '<th>Stadard</th>'; $fhtml .= '<th id="order_num">Stadard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            //$nhtml .= '<th>Hatch Eggs</th>'; $fhtml .= '<th id="order_num">Hatch Eggs</th>';
            //$nhtml .= '<th>Large Eggs</th>'; $fhtml .= '<th id="order_num">Large Eggs</th>';
            //$nhtml .= '<th>Shell Weak</th>'; $fhtml .= '<th id="order_num">Shell Weak</th>';
            //$nhtml .= '<th>Rejected</th>'; $fhtml .= '<th id="order_num">Rejected</th>';
            foreach($egg_code as $eggs){
                $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>';
            }
            $nhtml .= '<th>Total Eggs</th>'; $fhtml .= '<th id="order_num">Total Eggs</th>';
            $nhtml .= '<th>Std.Prod%</th>'; $fhtml .= '<th id="order_num">Std.Prod%</th>';
            $nhtml .= '<th>Prod%</th>'; $fhtml .= '<th id="order_num">Prod%</th>';
            $nhtml .= '<th>Difference</th>'; $fhtml .= '<th id="order_num">Difference</th>';
            $nhtml .= '<th>Std. HE%</th>'; $fhtml .= '<th id="order_num">Std. HE%</th>';
            $nhtml .= '<th>HE%</th>'; $fhtml .= '<th id="order_num">HE%</th>';
            $nhtml .= '<th>Difference</th>'; $fhtml .= '<th id="order_num">Difference</th>';
            $nhtml .= '<th>Egg Weight</th>'; $fhtml .= '<th id="order_num">Egg Weight</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                if(sizeof($flock_alist) > 0){
                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql); $flock_alist = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['flock_code'];
                        $fmort_qty[$key1] += (float)$row['fmort_qty'];
                        $fcull_qty[$key1] += (float)$row['fcull_qty'];
                        $fbody_weight[$key1] = (float)$row['fbody_weight'];
                        $ffeed_qty[$key1] += ((float)$row['ffeed_qty1'] + (float)$row['ffeed_qty2']);
                        $mmort_qty[$key1] += (float)$row['mmort_qty'];
                        $mcull_qty[$key1] += (float)$row['mcull_qty'];
                        $mbody_weight[$key1] = (float)$row['mbody_weight'];
                        $mfeed_qty[$key1] += ((float)$row['mfeed_qty1'] + (float)$row['mfeed_qty2']);
                        $egg_weight[$key1] = (float)$row['egg_weight'];

                        $flock_alist[$key1] = $key1;
                    }

                    $flock_list = implode("','",$flock_alist); $coa_list = implode("','",$icat_iac);
                    $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$coa_list') AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                    $query = mysqli_query($conn,$sql);
                    $fflk_obirds = $fflk_cr_birds = $fflk_dr_birds = $mflk_obirds = $mflk_cr_birds = $mflk_dr_birds = $egg_pqty = $flock_alist = array();
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
                                if($icrdr == "DR" && ($ietype == "Breeder-Female Bird Transfer In" || $ietype == "Breeder-Female Opening Birds")){
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
                                if($icrdr == "DR" && ($ietype == "Breeder-Male Bird Transfer In" || $ietype == "Breeder-Male Opening Birds")){
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
                                }
                            }
                        }

                        $flock_alist[$key1] = $key1;
                    }

                    if(sizeof($flock_alist) > 0){
                        $flock_list = implode("','",$flock_alist);
                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' AND `code` IN ('$flock_list') ORDER BY `description` ASC";
                        $query = mysqli_query($conn,$sql); $flock_alist = array();
                        while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }

                        $slno = 0; $egg_cqty = array();
                        $tfopn_birds = $tfmort_birds = $tfcull_birds = $tftrin_birds = $tftrout_birds = $tfflk_cbirds = $tffeed_cqty = $tfstd_bwht = $tfact_bwht = 0;
                        $tmopn_birds = $tmmort_birds = $tmcull_birds = $tmtrin_birds = $tmtrout_birds = $tmflk_cbirds = $tmfeed_cqty = $tmstd_bwht = $tmact_bwht = 0;
                        $tegg_rqty = $tstd_egg_pper = $tstd_hep = $tact_hep = 0;
                        foreach($flock_alist as $key1){
                            $slno++;
                            $flk_name = $flock_name[$key1];
                            $bird_age = $bird_wage = 0;
                            if(!empty($flock_sdate[$key1]) && !empty($flock_sage[$key1])){
                                $bird_age = (INT)((strtotime($tdate) - strtotime($flock_sdate[$key1])) / 60 / 60 / 24) + (int)$flock_sage[$key1];
                                $bird_wage = age_in_weeks($bird_age);
                            }
                            $key2 = $batch_breed[$flock_batch[$key1]]."@".$bird_age;
                            
                            if(empty($fflk_obirds[$key1]) || $fflk_obirds[$key1] == ""){ $fopn_birds = 0; } else{ $fopn_birds = $fflk_obirds[$key1]; }
                            if(empty($fmort_qty[$key1]) || $fmort_qty[$key1] == ""){ $fmort_birds = 0; } else{ $fmort_birds = $fmort_qty[$key1]; }
                            if(empty($fcull_qty[$key1]) || $fcull_qty[$key1] == ""){ $fcull_birds = 0; } else{ $fcull_birds = $fcull_qty[$key1]; }
                            if(empty($fflk_dr_birds[$key1]) || $fflk_dr_birds[$key1] == ""){ $ftrin_birds = 0; } else{ $ftrin_birds = $fflk_dr_birds[$key1]; }
                            if(empty($fflk_cr_birds[$key1]) || $fflk_cr_birds[$key1] == ""){ $ftrout_birds = 0; } else{ $ftrout_birds = $fflk_cr_birds[$key1]; }
                            $fflk_cbirds += ((int)$fopn_birds - (int)$fmort_birds - (int)$fcull_birds - (int)$ftrout_birds + (int)$ftrin_birds);
                            if(empty($ffeed_qty[$key1]) || $ffeed_qty[$key1] == ""){ $ffeed_cqty = 0; } else{ $ffeed_cqty = $ffeed_qty[$key1]; }

                            $fstd_fbird = $fstd_fpbird[$key2];
                            $fact_fbird = $ffeed_cqty;
                            $fstd_bwht = $fstd_bweight[$key2];
                            $fact_bwht = $fbody_weight[$key1];

                            if(empty($mflk_obirds[$key1]) || $mflk_obirds[$key1] == ""){ $mopn_birds = 0; } else{ $mopn_birds = $mflk_obirds[$key1]; }
                            if(empty($mmort_qty[$key1]) || $mmort_qty[$key1] == ""){ $mmort_birds = 0; } else{ $mmort_birds = $mmort_qty[$key1]; }
                            if(empty($mcull_qty[$key1]) || $mcull_qty[$key1] == ""){ $mcull_birds = 0; } else{ $mcull_birds = $mcull_qty[$key1]; }
                            if(empty($mflk_cr_birds[$key1]) || $mflk_cr_birds[$key1] == ""){ $mtrout_birds = 0; } else{ $mtrout_birds = $mflk_cr_birds[$key1]; }
                            if(empty($mflk_dr_birds[$key1]) || $mflk_dr_birds[$key1] == ""){ $mtrin_birds = 0; } else{ $mtrin_birds = $mflk_dr_birds[$key1]; }
                            $mflk_cbirds += ((int)$mopn_birds - (int)$mmort_birds - (int)$mcull_birds - (int)$mtrout_birds + (int)$mtrin_birds);
                            if(empty($mfeed_qty[$key1]) || $mfeed_qty[$key1] == ""){ $mfeed_cqty = 0; } else{ $mfeed_cqty = $mfeed_qty[$key1]; }
                            
                            $mstd_fbird = $mstd_fpbird[$key2];
                            $mact_fbird = $mfeed_cqty;
                            $mstd_bwht = $mstd_bweight[$key2];
                            $mact_bwht = $mbody_weight[$key1];

                            $html .= '<tr>';
                            $html .= '<td>'.$slno.'</td>';
                            $html .= '<td>'.$flk_name.'</td>';
                            $html .= '<td style="text-align:center;">'.$bird_wage.'</td>';
                            //Female Details
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fopn_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fmort_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fcull_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftrin_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftrout_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fflk_cbirds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ffeed_cqty,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fstd_fbird,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fact_fbird,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($fstd_bwht,2).'</td>';
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($fact_bwht,2).'</td>';

                            //Male Details
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mopn_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mmort_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mcull_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtrin_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtrout_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mflk_cbirds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mfeed_cqty,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mstd_fbird,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mact_fbird,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($mstd_bwht,2).'</td>';
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($mact_bwht,2).'</td>';
                            
                            $egg_rqty = $hegg_rqty = 0;
                            foreach($egg_code as $eggs){
                                $key3 = $key1."@".$eggs;
                                if(empty($egg_pqty[$key3]) || $egg_pqty[$key3] == ""){ $egg_qty = 0; } else{ $egg_qty = $egg_pqty[$key3]; }
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_qty,5))).'</td>';
                                $egg_rqty += (float)$egg_qty;
                                if($hegg_code == $eggs){ $hegg_rqty += (float)$egg_qty; }
                                $egg_cqty[$eggs] += (float)$egg_qty;
                            }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_rqty,5))).'</td>';

                            $std_egg_pper = $hd_per[$key1];
                            $act_egg_pper = 0; if((float)$fopn_birds != 0){ $act_egg_pper = round((((float)$egg_rqty / (float)$fopn_birds) * 100),2); }
                            $dif_egg_pper = 0; round(((float)$act_egg_pper - (float)$std_egg_pper),2);
                            $html .= '<td style="text-align:right;">'.number_format_ind($std_egg_pper).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($act_egg_pper).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($dif_egg_pper).'</td>';

                            $std_hep = $std_he_per[$key1];
                            $act_hep = 0; if((float)$egg_rqty != 0){ $act_hep = round((((float)$hegg_rqty / (float)$egg_rqty) * 100),2); }
                            $dif_hep = 0; round(((float)$act_hep - (float)$std_hep),2);
                            $html .= '<td style="text-align:right;">'.number_format_ind($std_hep).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($act_hep).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($dif_hep).'</td>';
        
                            $egg_wht = $egg_weight[$key1];
                            $html .= '<td style="text-align:right;">'.number_format_ind($egg_wht).'</td>';
                            $html .= '</tr>';


                            $tfopn_birds += (float)$fopn_birds;
                            $tfmort_birds += (float)$fmort_birds;
                            $tfcull_birds += (float)$fcull_birds;
                            $tftrin_birds += (float)$ftrin_birds;
                            $tftrout_birds += (float)$ftrout_birds;
                            $tffeed_cqty += (float)$ffeed_cqty;
                            $tfstd_bwht += (float)$fstd_bwht;
                            $tfact_bwht += (float)$fact_bwht;

                            $tmopn_birds += (float)$mopn_birds;
                            $tmmort_birds += (float)$mmort_birds;
                            $tmcull_birds += (float)$mcull_birds;
                            $tmtrin_birds += (float)$mtrin_birds;
                            $tmtrout_birds += (float)$mtrout_birds;
                            $tmfeed_cqty += (float)$mfeed_cqty;
                            $tmstd_bwht += (float)$mstd_bwht;
                            $tmact_bwht += (float)$mact_bwht;

                            $tegg_rqty += (float)$egg_rqty;
                            $tstd_egg_pper += (float)$std_egg_pper;
                            $tstd_hep += (float)$std_hep;
                            $tact_hep += (float)$act_hep;
                        }
                    }
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="3">Total</th>';
                //Female Bird Details
                $act_fpbird = 0; if((float)$tfopn_birds != 0){ $act_fpbird = round((((float)$tffeed_cqty / (float)$tfopn_birds) * 1000),2); }
                $avg_sdbwht = 0; if((float)$slno != 0){ $avg_sdbwht = round((((float)$tfstd_bwht / (float)$slno)),2); }
                $avg_atbwht = 0; if((float)$slno != 0){ $avg_atbwht = round((((float)$tfact_bwht / (float)$slno)),2); }
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfopn_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfmort_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfcull_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tftrin_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tftrout_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfflk_cbirds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tffeed_cqty,5))).'</th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($act_fpbird,5)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($avg_sdbwht,5)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($avg_atbwht,5)).'</th>';
                //Male Bird Details
                $act_fpbird = 0; if((float)$tmopn_birds != 0){ $act_fpbird = round((((float)$tmfeed_cqty / (float)$tmopn_birds) * 1000),2); }
                $avg_sdbwht = 0; if((float)$slno != 0){ $avg_sdbwht = round((((float)$tmstd_bwht / (float)$slno)),2); }
                $avg_atbwht = 0; if((float)$slno != 0){ $avg_atbwht = round((((float)$tmact_bwht / (float)$slno)),2); }
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmopn_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmmort_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmcull_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmtrin_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmtrout_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmflk_cbirds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmfeed_cqty,5))).'</th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($act_fpbird,5)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($avg_sdbwht,5)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($avg_atbwht,5)).'</th>';
                //Egg Prod Details
                $avg_sdeper = 0; if((float)$slno != 0){ $avg_sdeper = round((((float)$tstd_egg_pper / (float)$slno)),2); }
                $avg_ateper = 0; if((float)$tfopn_birds != 0){ $avg_ateper = round((((float)$tegg_rqty / (float)$tfopn_birds) * 100),2); }
                $avg_dfbwht = ((float)$avg_ateper - (float)$avg_sdeper);
                foreach($egg_code as $eggs){
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_cqty[$eggs],5))).'</th>';
                }
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_rqty,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($avg_sdeper,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($avg_ateper,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($avg_dfbwht,5))).'</th>';

                $avg_sheper = 0; if((float)$slno != 0){ $avg_sheper = round((((float)$tstd_hep / (float)$slno)),2); }
                $avg_aheper = 0; if((float)$slno != 0){ $avg_aheper = round((((float)$tact_hep / (float)$slno)),2); }
                $avg_dheper = ((float)$avg_aheper - (float)$avg_sheper);
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($avg_sheper,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($avg_aheper,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($avg_dheper,5))).'</th>';
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