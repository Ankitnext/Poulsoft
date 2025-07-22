<?php
//breeder_dayrecord_report3.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = ""; if(!empty($_GET['db']) && $_GET['db'] != ""){ $db = $_SESSION['db'] = $_GET['db']; }
$client = $_SESSION['client'];
if($db == ""){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Breeder Detailed Daily entry Report";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report3.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Breeder Detailed Daily entry Report";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report3.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";
$file_name = "Breeder Detailed Daily entry Report";

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

//Breeder Extra Access
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Female Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $ffeed_2flag = mysqli_num_rows($query);
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Male Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $mfeed_2flag = mysqli_num_rows($query);

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_access`'; $query = mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("bfarms_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bfarms_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Farms Access List' AFTER `cgroup_access`"; mysqli_query($conn,$sql); }
if(in_array("bunits_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bunits_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Units Access List' AFTER `bfarms_list`"; mysqli_query($conn,$sql); }
if(in_array("bsheds_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bsheds_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Sheds Access List' AFTER `bunits_list`"; mysqli_query($conn,$sql); }
if(in_array("bbatch_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bbatch_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Batch Access List' AFTER `bsheds_list`"; mysqli_query($conn,$sql); }
if(in_array("bflock_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bflock_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Flock Access List' AFTER `bbatch_list`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `item_category`'; $query = mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("bfamf_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bfamf_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Female And Male Feed Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }

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

$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $flocks = "all"; $batches = "select"; $excel_type = "display"; $slno_flag = 0;
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $farms = $_POST['farms'];
    $units = $_POST['units'];
    $sheds = $_POST['sheds'];
    $batches = $_POST['batches'];
    $flocks = $_POST['flocks'];
    $excel_type = $_POST['export'];
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
            <form action="<?php echo $form_path; ?>" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="farms">Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $bcode){ if($farm_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($farms == $bcode){ echo "selected"; } ?>><?php echo $farm_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="units">Unit</label>
                                    <select name="units" id="units" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($units == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($unit_code as $bcode){ if($unit_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($units == $bcode){ echo "selected"; } ?>><?php echo $unit_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="sheds">Shed</label>
                                    <select name="sheds" id="sheds" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($sheds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($shed_code as $bcode){ if($shed_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($sheds == $bcode){ echo "selected"; } ?>><?php echo $shed_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="batches">Batch</label>
                                    <select name="batches" id="batches" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="select" <?php if($batches == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($batch_code as $bcode){ if($batch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="flocks">Flock</label>
                                    <select name="flocks" id="flocks" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="select" <?php if($flocks == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($flock_code as $bcode){ if($flock_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($flocks == $bcode){ echo "selected"; } ?>><?php echo $flock_name[$bcode]; ?></option>
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
            $html = $nhtml = $fhtml = ''; $ffcon_cnt = $mfcon_cnt = 4; if((int)$ffeed_2flag == 1){  $ffcon_cnt += 2; } if((int)$mfeed_2flag == 1){  $mfcon_cnt += 2; }
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="2">Opening Birds</th>'; $fhtml .= '<th colspan="2">Opening Birds</th>';
            $nhtml .= '<th colspan="2">Mortality</th>'; $fhtml .= '<th colspan="2">Mortality</th>';
            $nhtml .= '<th colspan="2">Culls</th>'; $fhtml .= '<th colspan="2">Culls</th>';
            $nhtml .= '<th colspan="2">Transfer In</th>'; $fhtml .= '<th colspan="2">Transfer In</th>';
            $nhtml .= '<th colspan="2">Transfer Out</th>'; $fhtml .= '<th colspan="2">Transfer Out</th>';
            $nhtml .= '<th colspan="2">Sales</th>'; $fhtml .= '<th colspan="2">Sales</th>';
            $nhtml .= '<th colspan="2">Closing Birds</th>'; $fhtml .= '<th colspan="2">Closing Birds</th>';
            $nhtml .= '<th colspan="2">Std. B.Wt</th>'; $fhtml .= '<th colspan="2">Std. B.Wt</th>';
            $nhtml .= '<th colspan="2">Actual B.Wt</th>'; $fhtml .= '<th colspan="2">Actual B.Wt</th>';
            $nhtml .= '<th colspan="2">Feed Consumption</th>'; $fhtml .= '<th colspan="2">Feed Consumption</th>';
            $nhtml .= '<th colspan="4">Feed / Bird Gms</th>'; $fhtml .= '<th colspan="4">Feed / Bird Gms</th>';
            $nhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Production</th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th></th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>';
            $nhtml .= '<th>Unit</th>'; $fhtml .= '<th id="order">Unit</th>';
            $nhtml .= '<th>Shed</th>'; $fhtml .= '<th id="order">Shed</th>';
            $nhtml .= '<th>Batch</th>'; $fhtml .= '<th id="order">Batch</th>';
            $nhtml .= '<th>Flock No.</th>'; $fhtml .= '<th id="order">Flock No.</th>';
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Date</th>';
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
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>Std. F</th>'; $fhtml .= '<th id="order_num">Std. F</th>';
            $nhtml .= '<th>Actual F</th>'; $fhtml .= '<th id="order_num">Actual F</th>';
            $nhtml .= '<th>Std. M</th>'; $fhtml .= '<th id="order_num">Std. M</th>';
            $nhtml .= '<th>Actual M</th>'; $fhtml .= '<th id="order_num">Actual M</th>';
            foreach($egg_code as $eggs){
                $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>';
            }
            $nhtml .= '<th>Egg Weight</th>'; $fhtml .= '<th id="order_num">Egg Weight</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                //Fetch Flock Details
                $flk_list = implode("','",$flock_code);
                $flk_fltr = ""; if($flocks != "all"){ $flk_fltr = " AND `code` IN ('$flocks')"; }
                $sql = "WITH group_assignment AS (SELECT code AS flock_code, batch_code, start_age, CONCAT('grp_', DENSE_RANK() OVER (ORDER BY batch_code, start_age)) AS group_name FROM breeder_shed_allocation WHERE `code` IN ('$flk_list') AND `start_age` > '0'".$flk_fltr." AND `dflag` = '0') SELECT * FROM group_assignment ORDER BY start_age;";
                $query = mysqli_query($conn,$sql); $flk_glist = $flk_keys = $flk_alist = $grp_alist = array();
                while($row = mysqli_fetch_assoc($query)){
                    if(empty($flk_glist[$row['group_name']]) || $flk_glist[$row['group_name']] == ""){ $flk_glist[$row['group_name']] = $row['flock_code']; } else{ $flk_glist[$row['group_name']] .= ",".$row['flock_code']; }
                    $flk_keys[$row['flock_code']] = $row['group_name'];
                    $flk_alist[$row['flock_code']] = $row['flock_code'];
                    $grp_alist[$row['group_name']] = $row['group_name'];
                    //echo "<br/>".$row['flock_code']."->".$row['group_name']."->".$row['start_age'];
                }
                if(sizeof($flk_alist) > 0){
                    $flk_list = implode("','",$flk_alist);
                    $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `code` IN ('$flk_list') AND `batch_code` = '$batches'".$flk_fltr." AND `dflag` = '0' ORDER BY `start_date` ASC";
                    $query = mysqli_query($conn,$sql);
                    $placed_fbirds = $placed_mbirds = $opn_fbirds = $opn_mbirds = $breed_code = $flk_alist = array();
                    $flk_farm = $flk_unit = $flk_shed = $flk_batch = $flk_name = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 =  $flk_keys[$row['code']];
                        $bird_wage[$key1] = age_in_weeks($row['start_age']); //$weeks = fetch_cweek($bird_wage);
                        $placed_fbirds[$key1] += (float)$row['opn_fbirds'];
                        $placed_mbirds[$key1] += (float)$row['opn_mbirds'];
                        $opn_fbirds[$key1] += (float)$row['opn_fbirds'];
                        $opn_mbirds[$key1] += (float)$row['opn_mbirds'];
                        $breed_code[$row['code']] = $batch_breed[$row['batch_code']];
                        $flk_alist[$row['code']] = $row['code'];
                        $flk_farm[$row['code']] = $row['farm_code'];
                        $flk_unit[$row['code']] = $row['unit_code'];
                        $flk_shed[$row['code']] = $row['shed_code'];
                        $flk_batch[$row['code']] = $row['batch_code'];
                        $flk_name[$row['code']] = $row['description'];
                    }
                    $flk_list = implode("','",$flk_alist);

                    //Breeder Breed Standards
                    $sql = "SELECT * FROM `breeder_breed_standards` WHERE `dflag` = '0' ORDER BY `breed_code`,`breed_age` ASC";
                    $query = mysqli_query($conn,$sql); $fstd_live = $fstd_fpbird = $mstd_fpbird = $fstd_bweight = $mstd_bweight = $std_hd_per = $std_he_per = $std_hhe_pweek = $std_egg_wht = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['breed_code']."@".$row['breed_age'];
                        $fstd_live[$key1] = $row['livability']; $fstd_fpbird[$key1] = $row['ffeed_pbird']; $fstd_bweight[$key1] = $row['fbird_bweight'];
                        $mstd_fpbird[$key1] = $row['mfeed_pbird']; $mstd_bweight[$key1] = $row['mbird_bweight']; $std_hd_per[$key1] = $row['hd_per'];
                        $std_he_per[$key1] = $row['he_per']; $std_hhe_pweek[$key1] = $row['he_per']; $std_egg_wht[$key1] = $row['egg_weight'];
                    }

                    //Hatch Egg Details
                    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Hatch Egg%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $hegg_ccode = array();
                    while($row = mysqli_fetch_assoc($query)){ $hegg_ccode[$row['code']] = $row['code']; } $hegg_clist = implode("','", $hegg_ccode);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$hegg_clist') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $hegg_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $hegg_code[$row['code']] = $row['code']; }

                    //Breeder Bird Details
                    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Breeder Birds%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cbird_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $cbird_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $bird_list = implode("','", $cbird_code);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bird_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $fbird_code = $mbird_code = "";
                    while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Female birds"){ $fbird_code = $row['code']; } else if($row['description'] == "Male birds"){ $mbird_code = $row['code']; } }

                    //Female Feed Details
                    $sql = "SELECT * FROM `item_category` WHERE (`bffeed_flag` = '1' OR `bfamf_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $ffcat_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $ffcat_alist[$row['code']] = $row['code']; } $ffcat_list = implode("','", $ffcat_alist);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$ffcat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $ffeed_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $ffeed_code[$row['code']] = $row['code']; }

                    //Male Feed Details
                    $sql = "SELECT * FROM `item_category` WHERE (`bmfeed_flag` = '1' OR `bfamf_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $mfcat_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $mfcat_alist[$row['code']] = $row['code']; } $mfcat_list = implode("','", $mfcat_alist);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$mfcat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $mfeed_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $mfeed_code[$row['code']] = $row['code']; }

                    //MedVac Items
                    $sql = "SELECT * FROM `item_category` WHERE `bmv_flag` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_acat = array();
                    while($row = mysqli_fetch_assoc($query)){ $medvac_acat[$row['code']] = $row['code']; } $medvac_list = implode("','",$medvac_acat);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$medvac_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

                    $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
                    $query = mysqli_query($conn,$sql); $item_name = array();
                    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

                    //Purchases
                    $sql1 = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query1 = mysqli_query($conn,$sql1); $opur_fbds = $opur_mbds = $opur_ffeed = $opur_mfeed = $opur_medvac = $bpur_fbds = $bpur_mbds = $bpur_ffeed = $bpur_mfeed = $bpur_medvac = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row1['flock_code']];
                            if($row1['icode'] == $fbird_code){
                                $opur_fbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if($row1['icode'] == $mbird_code){
                                $opur_mbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $ffeed_code) || in_array($row1['icode'], $ffeed_code)){
                                $opur_ffeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $mfeed_code) || in_array($row1['icode'], $mfeed_code)){
                                $opur_mfeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $medvac_code) || in_array($row1['icode'], $medvac_code)){
                                $opur_medvac[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row1['flock_code']];
                            if($row1['icode'] == $fbird_code){
                                $bpur_fbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if($row1['icode'] == $mbird_code){
                                $bpur_mbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $ffeed_code) || in_array($row1['icode'], $ffeed_code)){
                                $bpur_ffeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $mfeed_code) || in_array($row1['icode'], $mfeed_code)){
                                $bpur_mfeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $medvac_code) || in_array($row1['icode'], $medvac_code)){
                                $bpur_medvac[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else{ }
                        }
                    }
                    //Transfer-In
                    $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `to_flock` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_flock` ASC";
                    $query1 = mysqli_query($conn,$sql1); $otin_fbds = $otin_mbds = $otin_ffeed = $otin_mfeed = $otin_medvac = $btin_fbds = $btin_mbds = $btin_ffeed = $btin_mfeed = $btin_medvac = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row['to_flock']];
                            if($row1['code'] == $fbird_code){
                                $otin_fbds[$key1] += (float)$row1['quantity'];
                            }
                            else if($row1['code'] == $mbird_code){
                                $otin_mbds[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $ffeed_code) || in_array($row1['code'], $ffeed_code)){
                                $otin_ffeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $mfeed_code) || in_array($row1['code'], $mfeed_code)){
                                $otin_mfeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $medvac_code) || in_array($row1['code'], $medvac_code)){
                                $otin_medvac[$key1] += (float)$row1['quantity'];
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row['to_flock']];
                            if($row1['code'] == $fbird_code){
                                $btin_fbds[$key1] += (float)$row1['quantity'];
                            }
                            else if($row1['code'] == $mbird_code){
                                $btin_mbds[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $ffeed_code) || in_array($row1['code'], $ffeed_code)){
                                $btin_ffeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $mfeed_code) || in_array($row1['code'], $mfeed_code)){
                                $btin_mfeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $medvac_code) || in_array($row1['code'], $medvac_code)){
                                $btin_medvac[$key1] += (float)$row1['quantity'];
                            }
                            else{ }
                        }
                    }
                    //Bird Transfer-In
                    $sql1 = "SELECT * FROM `breeder_bird_transfer` WHERE `date` <= '$tdate' AND `to_flock` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_flock` ASC";
                    $query1 = mysqli_query($conn,$sql1);
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row['to_flock']];
                            if($row1['female_bcode'] == $fbird_code){
                                $otin_fbds[$key1] += (float)$row1['female_bqty'];
                            }
                            else if($row1['male_bcode'] == $mbird_code){
                                $otin_mbds[$key1] += (float)$row1['male_bqty'];
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row['to_flock']];
                            if($row1['female_bcode'] == $fbird_code){
                                $btin_fbds[$key1] += (float)$row1['female_bqty'];
                            }
                            else if($row1['male_bcode'] == $mbird_code){
                                $btin_mbds[$key1] += (float)$row1['male_bqty'];
                            }
                            else{ }
                        }
                    }
                    //Sales
                    $sql1 = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query1 = mysqli_query($conn,$sql1); $osale_fbds = $osale_mbds = $osale_ffeed = $osale_mfeed = $osale_medvac = $bsale_fbds = $bsale_mbds = $bsale_ffeed = $bsale_mfeed = $bsale_medvac = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row1['flock_code']];
                            if($row1['icode'] == $fbird_code){
                                $osale_fbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if($row1['icode'] == $mbird_code){
                                $osale_mbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $ffeed_code) || in_array($row1['icode'], $ffeed_code)){
                                $osale_ffeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $mfeed_code) || in_array($row1['icode'], $mfeed_code)){
                                $osale_mfeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $medvac_code) || in_array($row1['icode'], $medvac_code)){
                                $osale_medvac[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row1['flock_code']];
                            if($row1['icode'] == $fbird_code){
                                $bsale_fbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if($row1['icode'] == $mbird_code){
                                $bsale_mbds[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $ffeed_code) || in_array($row1['icode'], $ffeed_code)){
                                $bsale_ffeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $mfeed_code) || in_array($row1['icode'], $mfeed_code)){
                                $bsale_mfeed[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else if(array_key_exists($row1['icode'], $medvac_code) || in_array($row1['icode'], $medvac_code)){
                                $bsale_medvac[$key1] += ((float)$row1['rcd_qty'] + (float)$row1['fre_qty']);
                            }
                            else{ }
                        }
                    }
                    //Transfer-Out
                    $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `from_flock` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_flock` ASC";
                    $query1 = mysqli_query($conn,$sql1); $otout_fbds = $otout_mbds = $otout_ffeed = $otout_mfeed = $otout_medvac = $btout_fbds = $btout_mbds = $btout_ffeed = $btout_mfeed = $btout_medvac = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row['from_flock']];
                            if($row1['code'] == $fbird_code){
                                $otout_fbds[$key1] += (float)$row1['quantity'];
                            }
                            else if($row1['code'] == $mbird_code){
                                $otout_mbds[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $ffeed_code) || in_array($row1['code'], $ffeed_code)){
                                $otout_ffeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $mfeed_code) || in_array($row1['code'], $mfeed_code)){
                                $otout_mfeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $medvac_code) || in_array($row1['code'], $medvac_code)){
                                $otout_medvac[$key1] += (float)$row1['quantity'];
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row['from_flock']];
                            if($row1['code'] == $fbird_code){
                                $btout_fbds[$key1] += (float)$row1['quantity'];
                            }
                            else if($row1['code'] == $mbird_code){
                                $btout_mbds[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $ffeed_code) || in_array($row1['code'], $ffeed_code)){
                                $btout_ffeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $mfeed_code) || in_array($row1['code'], $mfeed_code)){
                                $btout_mfeed[$key1] += (float)$row1['quantity'];
                            }
                            else if(array_key_exists($row1['code'], $medvac_code) || in_array($row1['code'], $medvac_code)){
                                $btout_medvac[$key1] += (float)$row1['quantity'];
                            }
                            else{ }
                        }
                    }
                    //Bird Transfer-Out
                    $sql1 = "SELECT * FROM `breeder_bird_transfer` WHERE `date` <= '$tdate' AND `from_flock` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_flock` ASC";
                    $query1 = mysqli_query($conn,$sql1);
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row['from_flock']];
                            if($row1['female_bcode'] == $fbird_code){
                                $otout_fbds[$key1] += (float)$row1['female_bqty'];
                            }
                            else if($row1['male_bcode'] == $mbird_code){
                                $otout_mbds[$key1] += (float)$row1['male_bqty'];
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row['from_flock']];
                            if($row1['female_bcode'] == $fbird_code){
                                $btout_fbds[$key1] += (float)$row1['female_bqty'];
                            }
                            else if($row1['male_bcode'] == $mbird_code){
                                $btout_mbds[$key1] += (float)$row1['male_bqty'];
                            }
                            else{ }
                        }
                    }
                    //Daily Entry
                    $sql1 = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query1 = mysqli_query($conn,$sql1); $omort_fbds = $ocull_fbds = $ocons_ffeed = $omort_mbds = $ocull_mbds = $ocons_mfeed = $bmort_fbds = $bcull_fbds = $bcons_ffeed = $bmort_mbds = $bcull_mbds = $bcons_mfeed = $bfbds_wht = $bmbds_wht = $begg_wht = $bdentry_date = $bage_fbds = $bwage_fbds = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row1['flock_code']];
                            $omort_fbds[$key1] += (float)$row1['fmort_qty'];
                            $ocull_fbds[$key1] += (float)$row1['fcull_qty'];
                            $ocons_ffeed[$key1] += ((float)$row1['ffeed_qty1'] + (float)$row1['ffeed_qty2']);
                            $omort_mbds[$key1] += (float)$row1['mmort_qty'];
                            $ocull_mbds[$key1] += (float)$row1['mcull_qty'];
                            $ocons_mfeed[$key1] += ((float)$row1['mfeed_qty1'] + (float)$row1['mfeed_qty2']);
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row1['flock_code']];
                            $bmort_fbds[$key1] += (float)$row1['fmort_qty'];
                            $bcull_fbds[$key1] += (float)$row1['fcull_qty'];
                            $bcons_ffeed[$key1] += ((float)$row1['ffeed_qty1'] + (float)$row1['ffeed_qty2']);
                            $bmort_mbds[$key1] += (float)$row1['mmort_qty'];
                            $bcull_mbds[$key1] += (float)$row1['mcull_qty'];
                            $bcons_mfeed[$key1] += ((float)$row1['mfeed_qty1'] + (float)$row1['mfeed_qty2']);
                            $bfbds_wht[$key1] = (float)$row1['fbody_weight'];
                            $bmbds_wht[$key1] = (float)$row1['mbody_weight'];
                            $begg_wht[$key1] = (float)$row1['egg_weight'];
                            $bdentry_date[$key1] = $row1['date'];
                            $bage_fbds[$key1] = $row1['breed_age'];
                            $bwage_fbds[$key1] = $row1['breed_wage'];
                        }
                    }
                    $sql1 = "SELECT * FROM `breeder_dayentry_produced` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query1 = mysqli_query($conn,$sql1); $pegg_qty = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){ }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row1['flock_code']]."@".$row1['item_code'];
                            $pegg_qty[$key1] += (float)$row1['quantity'];
                        }
                    }
                    //MedVac Consumed
                    $sql1 = "SELECT * FROM `breeder_medicine_consumed` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flk_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query1 = mysqli_query($conn,$sql1); $omv_qty = $bmv_qty = array();
                    while($row1 = mysqli_fetch_assoc($query1)){
                        if(strtotime($row1['date']) < strtotime($fdate)){
                            $key1 = $flk_keys[$row1['flock_code']];
                            if(array_key_exists($row1['item_code'], $medvac_code) || in_array($row1['item_code'], $medvac_code)){
                                $omv_qty[$key1] += (float)$row1['quantity'];
                            }
                            else{ }
                        }
                        else{
                            $key1 = $row1['date']."@".$flk_keys[$row1['flock_code']];
                            if(array_key_exists($row1['item_code'], $medvac_code) || in_array($row1['item_code'], $medvac_code)){
                                $bmv_qty[$key1] += (float)$row1['quantity'];
                            }
                            else{ }
                        }
                    }
                    $slno = $o_fbds = $o_mbds = $c_fbds = $c_mbds = $to_fbds = $to_mbds = $tb_fmb = $tb_mmb = $tb_fcb = $tb_mcb = $tb_fib = $tb_mib = $tb_fob = $tb_mob = $tb_fsb = $tb_msb = $tb_ffc = $tb_mfc = 0;
                    $o_flks = ""; $tpegg_qty = array();
                    foreach($grp_alist as $flks){
                        $flist = array(); $fk_name = "";
                        if(!empty($flk_glist[$flks]) && $flk_glist[$flks] != ""){
                            $flist = explode(",",$flk_glist[$flks]);
                            foreach($flist as $f1){ if($fk_name == ""){ $fk_name = $flk_name[$f1]; } else{ $fk_name .= ", ".$flk_name[$f1]; } }
                        }

                        for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                            $adate = date("Y-m-d",$cdate); $key1 = $adate."@".$flks;
                            if(!empty($bdentry_date[$key1]) && strtotime($bdentry_date[$key1]) == strtotime($adate)){
                                if($o_flks != $flks){ $o_flks = $flks; $slno = 0; } $slno++;
                                if($slno == 1){
                                    //Flock Details
                                    $fcode = $flk_farm[$flist[0]]; $fname = $farm_name[$fcode];
                                    $ucode = $flk_unit[$flist[0]]; $uname = $unit_name[$ucode];
                                    $scode = $flk_shed[$flist[0]]; $sname = $shed_name[$scode];
                                    $bcode = $flk_batch[$flist[0]]; $bname = $batch_name[$bcode];
                                    //Opening Female Bird Calculations
                                    $o_it1 = 0; if(!empty($opn_fbirds[$flks]) && (float)$opn_fbirds[$flks] > 0){ $o_it1 = (float)$opn_fbirds[$flks]; }
                                    $o_it2 = 0; if(!empty($opur_fbds[$flks]) && (float)$opur_fbds[$flks] > 0){ $o_it2 = (float)$opur_fbds[$flks]; }
                                    $o_it3 = 0; if(!empty($otin_fbds[$flks]) && (float)$otin_fbds[$flks] > 0){ $o_it3 = (float)$otin_fbds[$flks]; }
                                    $o_ot1 = 0; if(!empty($osale_fbds[$flks]) && (float)$osale_fbds[$flks] > 0){ $o_ot1 = (float)$osale_fbds[$flks]; }
                                    $o_ot2 = 0; if(!empty($otout_fbds[$flks]) && (float)$otout_fbds[$flks] > 0){ $o_ot2 = (float)$otout_fbds[$flks]; }
                                    $o_ot3 = 0; if(!empty($omort_fbds[$flks]) && (float)$omort_fbds[$flks] > 0){ $o_ot3 = (float)$omort_fbds[$flks]; }
                                    $o_ot4 = 0; if(!empty($ocull_fbds[$flks]) && (float)$ocull_fbds[$flks] > 0){ $o_ot4 = (float)$ocull_fbds[$flks]; }
                                    $o_fbds = ((float)$o_it1 + (float)$o_it2 + (float)$o_it3) - ((float)$o_ot1 + (float)$o_ot2 + (float)$o_ot3 + (float)$o_ot4);
                                    //Opening Male Bird Calculations
                                    $o_it1 = 0; if(!empty($opn_mbirds[$flks]) && (float)$opn_mbirds[$flks] > 0){ $o_it1 = (float)$opn_mbirds[$flks]; }
                                    $o_it2 = 0; if(!empty($opur_mbds[$flks]) && (float)$opur_mbds[$flks] > 0){ $o_it2 = (float)$opur_mbds[$flks]; }
                                    $o_it3 = 0; if(!empty($otin_mbds[$flks]) && (float)$otin_mbds[$flks] > 0){ $o_it3 = (float)$otin_mbds[$flks]; }
                                    $o_ot1 = 0; if(!empty($osale_mbds[$flks]) && (float)$osale_mbds[$flks] > 0){ $o_ot1 = (float)$osale_mbds[$flks]; }
                                    $o_ot2 = 0; if(!empty($otout_mbds[$flks]) && (float)$otout_mbds[$flks] > 0){ $o_ot2 = (float)$otout_mbds[$flks]; }
                                    $o_ot3 = 0; if(!empty($omort_mbds[$flks]) && (float)$omort_mbds[$flks] > 0){ $o_ot3 = (float)$omort_mbds[$flks]; }
                                    $o_ot4 = 0; if(!empty($ocull_mbds[$flks]) && (float)$ocull_mbds[$flks] > 0){ $o_ot4 = (float)$ocull_mbds[$flks]; }
                                    $o_mbds = ((float)$o_it1 + (float)$o_it2 + (float)$o_it3) - ((float)$o_ot1 + (float)$o_ot2 + (float)$o_ot3 + (float)$o_ot4);
                                    
                                    $to_fbds += (float)$o_fbds;
                                    $to_mbds += (float)$o_mbds;
                                }
                                else{
                                    $o_fbds = $c_fbds;
                                    $o_mbds = $c_mbds;
                                }
                                $b_age = 0; if(!empty($bage_fbds[$key1]) && (float)$bage_fbds[$key1] > 0){ $b_age = (float)$bage_fbds[$key1]; }
                                $b_wage = 0; if(!empty($bwage_fbds[$key1]) && (float)$bwage_fbds[$key1] > 0){ $b_wage = (float)$bwage_fbds[$key1]; }
                                //Mortality
                                $b_fmb = 0; if(!empty($bmort_fbds[$key1]) && (float)$bmort_fbds[$key1] > 0){ $b_fmb = (float)$bmort_fbds[$key1]; }
                                $b_mmb = 0; if(!empty($bmort_mbds[$key1]) && (float)$bmort_mbds[$key1] > 0){ $b_mmb = (float)$bmort_mbds[$key1]; }
                                //Culls
                                $b_fcb = 0; if(!empty($bcull_fbds[$key1]) && (float)$bcull_fbds[$key1] > 0){ $b_fcb = (float)$bcull_fbds[$key1]; }
                                $b_mcb = 0; if(!empty($bcull_mbds[$key1]) && (float)$bcull_mbds[$key1] > 0){ $b_mcb = (float)$bcull_mbds[$key1]; }
                                //Purchase-in Birds
                                $b_fpb = 0; if(!empty($bpur_fbds[$key1]) && (float)$bpur_fbds[$key1] > 0){ $b_fpb = (float)$bpur_fbds[$key1]; }
                                $b_mpb = 0; if(!empty($bpur_mbds[$key1]) && (float)$bpur_mbds[$key1] > 0){ $b_mpb = (float)$bpur_mbds[$key1]; }
                                //Transfer-in Birds
                                $b_fib = 0; if(!empty($btin_fbds[$key1]) && (float)$btin_fbds[$key1] > 0){ $b_fib = (float)$btin_fbds[$key1]; }
                                $b_mib = 0; if(!empty($btin_mbds[$key1]) && (float)$btin_mbds[$key1] > 0){ $b_mib = (float)$btin_mbds[$key1]; }
                                //Transfer-Out Birds
                                $b_fob = 0; if(!empty($btout_fbds[$key1]) && (float)$btout_fbds[$key1] > 0){ $b_fob = (float)$btout_fbds[$key1]; }
                                $b_mob = 0; if(!empty($btout_mbds[$key1]) && (float)$btout_mbds[$key1] > 0){ $b_mob = (float)$btout_mbds[$key1]; }
                                //sale Birds
                                $b_fsb = 0; if(!empty($bsale_fbds[$key1]) && (float)$bsale_fbds[$key1] > 0){ $b_fsb = (float)$bsale_fbds[$key1]; }
                                $b_msb = 0; if(!empty($bsale_mbds[$key1]) && (float)$bsale_mbds[$key1] > 0){ $b_msb = (float)$bsale_mbds[$key1]; }
                                //Closing Birds
                                $c_fbds = ((float)$o_fbds + (float)$b_fpb + (float)$b_fib) - ((float)$b_fob + (float)$b_fsb + (float)$b_fmb + (float)$b_fcb);
                                $c_mbds = ((float)$o_mbds + (float)$b_mpb + (float)$b_mib) - ((float)$b_mob + (float)$b_msb + (float)$b_mmb + (float)$b_mcb);
                                //Weight
                                $sbwt_fbds = $sbwt_mbds = $abwt_fbds = $abwt_mbds = "";
                                if((int)$b_age > 0){
                                    if((float)$fstd_bweight[(int)$b_age] > 0){ $sbwt_fbds = decimal_adjustments((float)$fstd_bweight[(int)$b_age],2); }
                                    if((float)$mstd_bweight[(int)$b_age] > 0){ $sbwt_mbds = decimal_adjustments((float)$mstd_bweight[(int)$b_age],2); }
                                    if((float)$bfbds_wht[$key1] > 0){ $abwt_fbds = decimal_adjustments((float)$bfbds_wht[$key1],2); }
                                    if((float)$bmbds_wht[$key1] > 0){ $abwt_mbds = decimal_adjustments((float)$bmbds_wht[$key1],2); }
                                }
                                //Feed Consumed
                                $b_ffc = 0; if(!empty($bcons_ffeed[$key1]) && (float)$bcons_ffeed[$key1] > 0){ $b_ffc = (float)$bcons_ffeed[$key1]; }
                                $b_mfc = 0; if(!empty($bcons_mfeed[$key1]) && (float)$bcons_mfeed[$key1] > 0){ $b_mfc = (float)$bcons_mfeed[$key1]; }
                                //Feed Per Bird
                                $ba_fpbfc = 0; if((float)$o_fbds > 0){ $ba_fpbfc = (((float)$b_ffc / (float)$o_fbds) * 1000); }
                                $ba_mpbfc = 0; if((float)$o_mbds > 0){ $ba_mpbfc = (((float)$b_mfc / (float)$o_mbds) * 1000); }
                                $bs_fpbfc = $bs_mpbfc = 0;
                                if((int)$b_age > 0){
                                    if((float)$fstd_fpbird[(int)$b_age] > 0){ $bs_fpbfc = decimal_adjustments((float)$fstd_fpbird[(int)$b_age],2); }
                                    if((float)$mstd_fpbird[(int)$b_age] > 0){ $bs_mpbfc = decimal_adjustments((float)$mstd_fpbird[(int)$b_age],2); }
                                }
                                //Egg Weight
                                $b_ewt = 0; if(!empty($begg_wht[$key1]) && (float)$begg_wht[$key1] > 0){ $b_ewt = (float)$begg_wht[$key1]; }

                                $html .= '<tr>';
                                $html .= '<td>'.$fname.'</td>';
                                $html .= '<td>'.$uname.'</td>';
                                $html .= '<td>'.$sname.'</td>';
                                $html .= '<td>'.$bname.'</td>';
                                $html .= '<td title="'.$flks.'">'.$fk_name.'</td>';
                                $html .= '<td>'.date("d.m.Y",strtotime($adate)).'</td>';
                                $html .= '<td style="text-align:center;">'.decimal_adjustments($b_wage,1).'</td>';
                                //Opening Birds
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($o_fbds,5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($o_mbds,5))).'</td>';
                                //Mortality
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_fmb,5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_mmb,5))).'</td>';
                                //Culls
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_fcb,5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_mcb,5))).'</td>';
                                //Transfer-In Birds
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round(((float)$b_fib + (float)$b_fpb),5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round(((float)$b_mib + (float)$b_fpb),5))).'</td>';
                                //Transfer-Out Birds
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_fob,5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_mob,5))).'</td>';
                                //sale Birds
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_fsb,5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_msb,5))).'</td>';
                                //Closing Birds
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($c_fbds,5))).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($c_mbds,5))).'</td>';
                                //Std. Body Weight
                                $html .= '<td style="text-align:center;">'.$sbwt_fbds.'</td>';
                                $html .= '<td style="text-align:center;">'.$sbwt_mbds.'</td>';
                                //Actual Body Weight
                                $html .= '<td style="text-align:center;">'.$abwt_fbds.'</td>';
                                $html .= '<td style="text-align:center;">'.$abwt_mbds.'</td>';
                                //Feed Consumed
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($b_ffc,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($b_mfc,2)).'</td>';
                                //Feed Per Bird
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($bs_fpbfc,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($ba_fpbfc,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($bs_mpbfc,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($ba_mpbfc,2)).'</td>';
                                //Egg Production
                                foreach($egg_code as $eggs){
                                    $key2 = $key1."@".$eggs;
                                    $b_epq = 0; if(!empty($pegg_qty[$key2]) && (float)$pegg_qty[$key2] > 0){ $b_epq = (float)$pegg_qty[$key2]; }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($b_epq,2))).'</td>';
                                    $tpegg_qty[$eggs] += (float)$b_epq;
                                }
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($b_ewt,2)).'</td>';
                                $html .= '</tr>';
                                $tb_fmb += (float)$b_fmb;
                                $tb_mmb += (float)$b_mmb;
                                $tb_fcb += (float)$b_fcb;
                                $tb_mcb += (float)$b_mcb;
                                $tb_fib += ((float)$b_fib + (float)$b_fpb);
                                $tb_mib += ((float)$b_mib + (float)$b_fpb);
                                $tb_fob += (float)$b_fob;
                                $tb_mob += (float)$b_mob;
                                $tb_fsb += (float)$b_fsb;
                                $tb_msb += (float)$b_msb;
                                $tb_ffc += (float)$b_ffc;
                                $tb_mfc += (float)$b_mfc;
                            }
                        }
                    }
                }
                    
            }
            //Closing Birds
            $tc_fbds = ((float)$to_fbds + (float)$tb_fib) - ((float)$tb_fob + (float)$tb_fsb + (float)$tb_fmb + (float)$tb_fcb);
            $tc_mbds = ((float)$to_mbds + (float)$tb_mib) - ((float)$tb_mob + (float)$tb_msb + (float)$tb_mmb + (float)$tb_mcb);
            //Feed Per Bird
            $tba_fpbfc = 0; if((float)$to_fbds > 0){ $tba_fpbfc = (((float)$tb_ffc / (float)$to_fbds) * 1000); }
            $tba_mpbfc = 0; if((float)$to_mbds > 0){ $tba_mpbfc = (((float)$tb_mfc / (float)$to_mbds) * 1000); }

            $html .= '</tbody>';
            $html .= '<tfoot class="thead3">';
            $html .= '<tr>';
            $html .= '<th style="text-align:left;" colspan="7">Total</th>';
            //Opening Birds
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($to_fbds,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($to_mbds,5))).'</th>';
            //Mortality
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_fmb,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_mmb,5))).'</th>';
            //Culls
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_fcb,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_mcb,5))).'</th>';
            //Transfer-in Birds
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_fib,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_mib,5))).'</th>';
            //Transfer-Out Birds
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_fob,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_mob,5))).'</th>';
            //sale Birds
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_fsb,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_msb,5))).'</th>';
            //Closing Birds
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tc_fbds,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tc_mbds,5))).'</th>';
            //Std. Body Weight
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;"></th>';
            //Actual Body Weight
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;"></th>';
            //Feed Consumed
            $html .= '<th style="text-align:right;">'.number_format_ind(round($tb_ffc,2)).'</th>';
            $html .= '<th style="text-align:right;">'.number_format_ind(round($tb_mfc,2)).'</th>';
            //Feed Per Bird
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.number_format_ind(round($tba_fpbfc,2)).'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.number_format_ind(round($tba_mpbfc,2)).'</th>';
            //Egg Production
            foreach($egg_code as $eggs){
                $tb_epq = 0; if(!empty($tpegg_qty[$eggs]) && (float)$tpegg_qty[$eggs] > 0){ $tb_epq = (float)$tpegg_qty[$eggs]; }
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tb_epq,2))).'</th>';
            }
            $html .= '<th style="text-align:right;"></th>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function checkval(){
                var batches = document.getElementById("batches").value;
                var flocks = document.getElementById("flocks").value;
                var l = true;
                if(batches == "select"){
                    alert("Please select Batch");
                    document.getElementById("batches").focus();
                    l = false;
                }
                else if(flocks == "select"){
                    alert("Please select Flock");
                    document.getElementById("flocks").focus();
                    l = false;
                }
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
                var farms = document.getElementById("farms").value;
                var units = document.getElementById("units").value;
                var sheds = document.getElementById("sheds").value;
                var batches = document.getElementById("batches").value;
                var flocks = document.getElementById("flocks").value;

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
                var url = "breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&flocks="+flocks+"&fetch_type=single&batch_type=select&flock_type=all";
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
                    $('#units').select2();
                    var units = '<?php echo $units; ?>';
                    document.getElementById("units").value = units;
                    $('#units').select2();
                    var fx = "units"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    $('#sheds').select2();
                    var sheds = '<?php echo $sheds; ?>';
                    document.getElementById("sheds").value = sheds;
                    $('#sheds').select2();
                    var fx = "sheds"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    $('#batches').select2();
                    var batches = '<?php echo $batches; ?>';
                    document.getElementById("batches").value = batches;
                    $('#batches').select2();
                    var fx = "batches"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    $('#flocks').select2();
                    var flocks = '<?php echo $flocks; ?>';
                    document.getElementById("flocks").value = flocks;
                    $('#flocks').select2();
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