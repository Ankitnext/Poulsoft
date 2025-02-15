<?php
//breeder_flockwise_avlbirds1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "breeder_flockwise_avlbirds1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "breeder_flockwise_avlbirds1.php?db=$db&userid=".$user_code;
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

$file_name = "Flock Wise Avalable birds";
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
while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_sdate[$row['code']] = $row['start_date']; $flock_shed[$row['code']] = $row['shed_code'];$flock_unit[$row['code']] = $row['unit_code']; $flock_batch[$row['code']] = $row['batch_code']; }

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
                                    <label>Till Date</label>
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

            $nhtml .= '<th>Sl.No</th>'; $fhtml .= '<th id="order_num">Sl.No</th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order"></th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order"></th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order"></th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order_date"></th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order_num"></th>';
            $nhtml .= '<th colspan="2">Housed Birds</th>'; $fhtml .= '<th id="order_num" colspan="2">Housed Birds</th>';
            $nhtml .= '<th colspan="2">Mortality</th>'; $fhtml .= '<th id="order_num" colspan="2">Mortality</th>';
            $nhtml .= '<th colspan="2">Culls</th>'; $fhtml .= '<th id="order_num" colspan="2">Culls</th>';
            $nhtml .= '<th colspan="2">Sales</th>'; $fhtml .= '<th id="order_num" colspan="2">Sales</th>';
            $nhtml .= '<th colspan="2">Transfer In</th>'; $fhtml .= '<th id="order_num" colspan="2">Transfer In</th>';
            $nhtml .= '<th colspan="2">Transfer Out</th>'; $fhtml .= '<th id="order_num" colspan="2">Transfer Out</th>';
            $nhtml .= '<th colspan="2">Closing Birds</th>'; $fhtml .= '<th id="order_num" colspan="2">Closing Birds</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th></th>'; $fhtml .= '<th></th>';
            $nhtml .= '<th>Unit</th>'; $fhtml .= '<th>Unit</th>';
            $nhtml .= '<th>Shed</th>'; $fhtml .= '<th>Shed</th>';
            $nhtml .= '<th>Flock</th>'; $fhtml .= '<th>Flock</th>';
            $nhtml .= '<th>Last Entry Date</th>'; $fhtml .= '<th>Last Entry Date</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th>Age</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>'; 
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th>F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th>M</th>';
           
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                if(sizeof($flock_alist) > 0){
                    $flock_list = implode("','",$flock_alist); $coa_list = implode("','",$icat_iac);
                    $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$coa_list') AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                    $query = mysqli_query($conn,$sql);
                    $fhsd_birds = $mhsd_birds = $fmrt_birds = $mmrt_birds = $fsls_birds = $fcls_birds = $mcls_birds = $msls_birds = $ftif_birds = $mtif_birds = $ftof_birds = $mtof_birds = $flock_alist = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $icrdr = $row['crdr']; $idate = $row['date']; $icode = $row['item_code']; $iqty = $row['quantity']; $ietype = $row['etype']; $key1 = $row['flock_code'];

                        if($icode == $fbird_code){
                            //Female Bird Calculations
                            if($icrdr == "DR" && $ietype == "Breeder-Female Opening Birds"){
                                $fhsd_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Female Bird Mortality"){
                                $fmrt_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Female Bird Culls"){
                                $fcls_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Female Bird Sales"){
                                $fsls_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "DR" && $ietype == "Breeder-Female Bird Transfer In") {
                                $ftif_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Female Bird Transfer Out") {
                                $ftof_birds[$key1] += (float)$iqty;
                            }


                        }
                        else if($icode == $mbird_code){
                            //Male Bird Calculations
                            if($icrdr == "DR" && $ietype == "Breeder-Male Opening Birds"){
                                $mhsd_birds[$key1] += (float)$iqty;
                            } 

                            if($icrdr == "CR" && $ietype == "Breeder-Male Bird Mortality") {
                                $mmrt_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Male Bird Culls") {
                                $mcls_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Male Bird Sales") {
                                $msls_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "DR" && $ietype == "Breeder-Male Bird Transfer In") {
                                $mtif_birds[$key1] += (float)$iqty;
                            }

                            if($icrdr == "CR" && $ietype == "Breeder-Male Bird Transfer Out") {
                                $mtof_birds[$key1] += (float)$iqty;
                            }

                        }
                        else{ }

                        $flock_alist[$key1] = $key1;
                    }

                    if(sizeof($flock_alist) > 0){
                        $flock_list = implode("','",$flock_alist);

                        // $le_date = "";
                        // if($le_date == "") { $le_date = $row['date']; } else { if(strtotime($le_date) <= strtotime($row['date'])){ $le_date = $row['date']; } }

                        $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `active` = '1' AND `dflag` = '0' AND `flock_code` IN ('$flock_list') ORDER BY `date`,`breed_wage` ASC";
                        $query = mysqli_query($conn,$sql); $le_date = $le_age = array();
                        while($row = mysqli_fetch_assoc($query)){
                            if($le_date[$row['flock_code']] == ""){
                                $le_date[$row['flock_code']] = $row['date'];
                                $le_age[$row['flock_code']] = round($row['breed_wage'],1);
                            }
                            else{
                                if(strtotime($le_date[$row['flock_code']]) <= strtotime($row['date'])){
                                    $le_date[$row['flock_code']] = $row['date'];
                                    $le_age[$row['flock_code']] = round($row['breed_wage'],1);
                                }
                            }
                        }

                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' AND `code` IN ('$flock_list') ORDER BY `description` ASC";
                        $query = mysqli_query($conn,$sql); $flock_alist = array();
                        while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }

                        foreach($flock_alist as $key1){
                            $slno++;

                            $flk_name = $flock_name[$key1];

                            $scode = $flock_shed[$key1];
                            $sname = $shed_name[$scode]; 

                            $sunit = $flock_unit[$key1];
                            $uname = $unit_name[$sunit];

                            $date = "";
                            if(empty($le_date[$key1]) || $le_date[$key1] == ""){ }
                            else{
                                $date = date("d.m.Y",strtotime($le_date[$key1]));   
                            }
                            
                            $breed_age = $le_age[$key1];

                            $html .= '<tr>';
                            $html .= '<td>'.$slno.'</td>';
                            $html .= '<td>'.$uname.'</td>';
                            $html .= '<td>'.$sname.'</td>';
                            $html .= '<td>'.$flk_name.'</td>';
                            $html .= '<td>'.$date.'</td>';
                            $html .= '<td>'.$breed_age.'</td>';
                            
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fhsd_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mhsd_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fmrt_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mmrt_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fcls_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mcls_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fsls_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($msls_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftif_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtif_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftof_birds[$key1],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtof_birds[$key1],5))).'</td>';
                            
                            $fcl_birds =  $fhsd_birds[$key1] - $fmrt_birds[$key1] - $fcls_birds[$key1] - $fsls_birds[$key1] + $ftif_birds[$key1] - $ftof_birds[$key1];
                            $mcl_birds =  $mhsd_birds[$key1] - $mmrt_birds[$key1] - $mcls_birds[$key1] - $msls_birds[$key1] + $mtif_birds[$key1] - $mtof_birds[$key1];

                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fcl_birds,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mcl_birds,5))).'</td>';

                            
                            $html .= '</tr>';

                            $tot_fhsd += (float)$fhsd_birds[$key1];
                            $tot_fmrt += (float)$fmrt_birds[$key1];
                            $tot_fcls += (float)$fcls_birds[$key1];
                            $tot_fsls += (float)$fsls_birds[$key1];
                            $tot_ftif += (float)$ftif_birds[$key1];
                            $tot_ftof += (float)$ftof_birds[$key1];
                            $tot_fcl += (float)$fcl_birds;

                            $tot_mhsd += (float)$mhsd_birds[$key1];
                            $tot_mmrt += (float)$mmrt_birds[$key1];
                            $tot_mcls += (float)$mcls_birds[$key1];
                            $tot_msls += (float)$msls_birds[$key1];
                            $tot_mtif += (float)$mtif_birds[$key1];
                            $tot_mtof += (float)$mtof_birds[$key1];
                            $tot_mcl += (float)$mcl_birds;
                        }
                    }
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="6">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_fhsd,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mhsd,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_fmrt,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mmrt,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_fcls,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mcls,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_fsls,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_msls,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_ftif,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mtif,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_ftof,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mtof,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_fcl,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mcl,5))).'</th>';
            
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