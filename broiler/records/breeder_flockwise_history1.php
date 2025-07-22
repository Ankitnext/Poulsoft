<?php
//breeder_flockwise_history1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Breeder Flock History Report";
    include "header_head.php";
    $form_path = "breeder_flockwise_history1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Breeder Flock History Report";
    include "header_head.php";
    $form_path = "breeder_flockwise_history1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";

$file_name = "Breeder Flock History Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("item_stocktransfers", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stocktransfers LIKE poulso6_admin_broiler_broilermaster.item_stocktransfers;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_sales", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sales LIKE poulso6_admin_broiler_broilermaster.broiler_sales;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_daily_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_daily_record LIKE poulso6_admin_broiler_broilermaster.broiler_daily_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_medicine_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_medicine_record LIKE poulso6_admin_broiler_broilermaster.broiler_medicine_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_week_define_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_week_define_master LIKE poulso6_admin_broiler_broilermaster.broiler_week_define_master;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_access`'; $query = mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("bfarms_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bfarms_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Farms Access List' AFTER `cgroup_access`"; mysqli_query($conn,$sql); }
if(in_array("bunits_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bunits_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Units Access List' AFTER `bfarms_list`"; mysqli_query($conn,$sql); }
if(in_array("bsheds_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bsheds_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Sheds Access List' AFTER `bunits_list`"; mysqli_query($conn,$sql); }
if(in_array("bbatch_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bbatch_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Batch Access List' AFTER `bsheds_list`"; mysqli_query($conn,$sql); }
if(in_array("bflock_list", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bflock_list` VARCHAR(1500) NULL DEFAULT NULL COMMENT 'Breeder Flock Access List' AFTER `bbatch_list`"; mysqli_query($conn,$sql); }

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1'";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; }

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bfarms_list = $row['bfarms_list']; $bunits_list = $row['bunits_list']; $bsheds_list = $row['bsheds_list']; $bbatch_list = $row['bbatch_list']; $bflock_list = $row['bflock_list']; }
if($bfarms_list == "all" || $bfarms_list == ""){ $bfarms_fltr1 = $bfarms_fltr2 = ""; } else{ $bfarms_list1 = implode("','", explode(",",$bfarms_list)); $bfarms_fltr1 = " AND `code` IN ('$bfarms_list1')"; $bfarms_fltr2 = " AND `farm_code` IN ('$bfarms_list1')"; }
if($bunits_list == "all" || $bunits_list == ""){ $bunits_fltr1 = $bunits_fltr2 = ""; } else{ $bunits_list1 = implode("','", explode(",",$bunits_list)); $bunits_fltr1 = " AND `code` IN ('$bunits_list1')"; $bunits_fltr2 = " AND `unit_code` IN ('$bunits_list1')"; }
if($bsheds_list == "all" || $bsheds_list == ""){ $bsheds_fltr1 = $bsheds_fltr2 = ""; } else{ $bsheds_list1 = implode("','", explode(",",$bsheds_list)); $bsheds_fltr1 = " AND `code` IN ('$bsheds_list1')"; $bsheds_fltr2 = " AND `shed_code` IN ('$bsheds_list1')"; }
if($bbatch_list == "all" || $bbatch_list == ""){ $bbatch_fltr1 = $bbatch_fltr2 = ""; } else{ $bbatch_list1 = implode("','", explode(",",$bbatch_list)); $bbatch_fltr1 = " AND `code` IN ('$bbatch_list1')"; $bbatch_fltr2 = " AND `batch_code` IN ('$bbatch_list1')"; }
if($bflock_list == "all" || $bflock_list == ""){ $bflock_fltr1 = $bflock_fltr2 = ""; } else{ $bflock_list1 = implode("','", explode(",",$bflock_list)); $bflock_fltr1 = " AND `code` IN ('$bflock_list1')"; $bflock_fltr2 = " AND `flock_code` IN ('$bflock_list1')"; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0'".$bfarms_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0'".$bunits_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0'".$bsheds_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0'".$bbatch_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_breed = $batch_epflag = array();
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_breed[$row['code']] = $row['breed_code']; $batch_epflag[$row['code']] = $row['beps_flag']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0'".$bfarms_fltr2."".$bunits_fltr2."".$bsheds_fltr2."".$bbatch_fltr2."".$bflock_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_sdate = $flock_sage = $flock_batch = array();
while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_sdate[$row['code']] = $row['start_date']; $flock_sage[$row['code']] = $row['start_age']; $flock_batch[$row['code']] = $row['batch_code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $emp_code = $emp_name = array();
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

//Feed Items
$sql = "SELECT * FROM `item_category` WHERE (`description` IN ('Female Feed','Male Feed') OR `bffeed_flag` = '1' OR `bmfeed_flag` = '1' OR `bfeed_flag` = '1') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_acat = array();
while($row = mysqli_fetch_assoc($query)){ $feed_acat[$row['code']] = $row['code']; }
$feed_list = implode("','",$feed_acat);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_name = array();
while($row = mysqli_fetch_assoc($query)){ $feed_name[$row['code']] = $row['description']; }
$feed_list = ""; foreach($feed_name as $fcode => $fname){ if($feed_list == ""){ $feed_list = $fcode; } else{ $feed_list .= "','".$fcode; } }

//MedVac Items
$sql = "SELECT * FROM `item_category` WHERE `bmv_flag` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_acat = array();
while($row = mysqli_fetch_assoc($query)){ $medvac_acat[$row['code']] = $row['code']; }
$medvac_list = implode("','",$medvac_acat);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$medvac_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_name = array();
while($row = mysqli_fetch_assoc($query)){ $medvac_name[$row['code']] = $row['code']; }
$medvac_list = ""; foreach($medvac_name as $mcode => $mname){ if($medvac_list == ""){ $medvac_list = $mcode; } else{ $medvac_list .= "','".$mcode; } }

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code);

//Breeder Bird Details
$sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%Breeder Birds%' OR `description` LIKE '%female bird%' OR `description` LIKE '%male bird%') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cbird_code = array();
while($row = mysqli_fetch_assoc($query)){ $cbird_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $bird_list = implode("','", $cbird_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bird_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $fbird_code = $mbird_code = "";
while($row = mysqli_fetch_assoc($query)){ if(strtolower($row['description']) == "female birds"){ $fbird_code = $row['code']; } else if(strtolower($row['description']) == "male birds"){ $mbird_code = $row['code']; } }

$farms = $units = $sheds = $batches = "all"; $flocks = "select"; $excel_type = "display"; $slno_flag = 0;
if(isset($_POST['submit_report']) == true){
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
            <form action="<?php echo $form_path; ?>" method="post" onsubmit="return check_val();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
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
                                        <option value="all" <?php if($batches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($batch_code as $bcode){ if($batch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="flocks">Flock</label>
                                    <select name="flocks" id="flocks" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($flocks == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($flock_code as $bcode){ if($flock_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($flocks == $bcode){ echo "selected"; } ?>><?php echo $flock_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onChange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
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
            if(isset($_POST['submit_report']) == true){
                //Heading Column Counts
                $ccnt1 = 30; $ccnt2 = 14; $ccnt3 = $ccnt1 / 10;
                if($egg_pflag == 1 && $e_cnt > 0){ $ccnt1 += $e_cnt; }
                
                //Egg Production Start Flag
                $egg_pflag = 0; if(!empty($batch_epflag[$flock_batch[$flocks]]) && (float)$batch_epflag[$flock_batch[$flocks]] > 0){ $egg_pflag = 1; }
                
                //Bird Opening/Transfer-In/Purchased-In
                echo '<thead class="thead3" id="head_names">';
                echo '<tr style="text-align:center;" align="center">';
                echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Chick Placement</td>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th colspan="'.$ccnt3.'">Sl.No.</th>';
                echo '<th colspan="'.$ccnt3.'">Date</th>';
                echo '<th colspan="'.$ccnt3.'">Transaction No.</th>';
                echo '<th colspan="'.$ccnt3.'">Age(weeks)</th>';
                echo '<th colspan="'.$ccnt3.'">Female Birds</th>';
                echo '<th colspan="'.$ccnt3.'">Rate</th>';
                echo '<th colspan="'.$ccnt3.'">Amount</th>';
                echo '<th colspan="'.$ccnt3.'">Male Birds</th>';
                echo '<th colspan="'.$ccnt3.'">Rate</th>';
                echo '<th colspan="'.$ccnt3.'">Amount</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="tbody1" id="tbody1">';

                $tfr_qty = $tff_qty = $tfb_amt = $tmr_qty = $tmf_qty = $tmb_amt = $opn_fbird_qty = $opn_fbird_amt = $opn_mbird_qty = $opn_mbird_amt = 0;
                //Opening Details
                $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `code` = '$flocks' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $opn_fbirds = $opn_frate = $opn_famt = $opn_mbirds = $opn_mrate = $opn_mamt = $fbrd_oage = array(); $flk_bch = $osheds = $ounits = ""; $slno = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $start_date = $row['start_date']; $bird_wage = age_in_weeks($row['start_age']); $weks = fetch_cweek($bird_wage);
                    $opn_fbirds[$start_date] += (float)$row['opn_fbirds']; $opn_frate[$start_date] += (float)$row['opn_frate'];
                    $opn_mbirds[$start_date] += (float)$row['opn_mbirds']; $opn_mrate[$start_date] += (float)$row['opn_mrate'];
                    $breed_code = $batch_breed[$row['batch_code']];
                    $fbrd_oage[$start_date] = $row['start_age'];
                    $osheds = $row['shed_code'];
                    $ounits = $row['unit_code'];
                }
                
                $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `shed_code` = '$osheds' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $s_cnt = mysqli_num_rows($query);
                //if($s_cnt == 1){ $flk_fltr1 = " AND (`flock_code` IN ('$flocks') OR `shed_code` IN ('$osheds') OR `unit_code` IN ('$ounits') OR `warehouse` IN ('$osheds','$ounits'))"; } else{ $flk_fltr1 = " AND `flock_code` IN ('$flocks')"; }
                /*$pur_fltr1 = ""; $pur_fltr1 = " AND `flock_code` IN ('$flocks')";
                $pur_fltr2 = ""; $pur_fltr2 = " AND (`flock_code` IN ('$flocks') OR `shed_code` IN ('$osheds') OR `unit_code` IN ('$ounits') OR `warehouse` IN ('$osheds','$ounits'))";
                $sale_fltr1 = ""; $sale_fltr1 = " AND (`flock_code` IN ('$flocks') OR `shed_code` IN ('$osheds') OR `unit_code` IN ('$ounits') OR `warehouse` IN ('$osheds','$ounits'))";
                $mvcon_fltr1 = ""; $mvcon_fltr1 = " AND (`flock_code` IN ('$flocks') OR `shed_code` IN ('$osheds') OR `unit_code` IN ('$ounits'))";
                $trin_fltr1 = ""; $trin_fltr1 = " AND (`to_flock` IN ('$flocks') OR `to_shed` IN ('$osheds') OR `to_unit` IN ('$ounits') OR `towarehouse` IN ('$osheds','$ounits'))";
                $trot_fltr1 = ""; $trot_fltr1 = " AND (`from_flock` IN ('$flocks') OR `from_shed` IN ('$osheds') OR `from_unit` IN ('$ounits') OR `fromwarehouse` IN ('$osheds','$ounits'))";
                */
                $pur_fltr1 = ""; $pur_fltr1 = " AND `flock_code` IN ('$flocks')";
                $pur_fltr2 = ""; $pur_fltr2 = " AND `flock_code` IN ('$flocks')";
                $sale_fltr1 = ""; $sale_fltr1 = " AND `flock_code` IN ('$flocks')";
                $mvcon_fltr1 = ""; $mvcon_fltr1 = " AND `flock_code` IN ('$flocks')";
                $trin_fltr1 = ""; $trin_fltr1 = " AND `to_flock` IN ('$flocks')";
                $trot_fltr1 = ""; $trot_fltr1 = " AND `from_flock` IN ('$flocks')";

                if(empty($opn_fbirds[$start_date]) || $opn_fbirds[$start_date] == ""){ $opn_fbirds[$start_date] = 0; }
                if(empty($opn_frate[$start_date]) || $opn_frate[$start_date] == ""){ $opn_frate[$start_date] = 0; }
                $opn_famt[$start_date] = (float)$opn_fbirds[$start_date] * (float)$opn_frate[$start_date];
                if(empty($opn_mbirds[$start_date]) || $opn_mbirds[$start_date] == ""){ $opn_mbirds[$start_date] = 0; }
                if(empty($opn_mrate[$start_date]) || $opn_mrate[$start_date] == ""){ $opn_mrate[$start_date] = 0; }
                $opn_mamt[$start_date] = (float)$opn_mbirds[$start_date] * (float)$opn_mrate[$start_date];

                $opn_fbird_qty += (float)$opn_fbirds[$start_date];
                $opn_fbird_amt += (float)$opn_famt[$start_date];
                $opn_mbird_qty += (float)$opn_mbirds[$start_date];
                $opn_mbird_amt += (float)$opn_mamt[$start_date];
                
                if((float)$opn_fbirds[$start_date] > 0 || (float)$opn_mbirds[$start_date] > 0){
                    $slno++;
                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($start_date)).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">Opening Birds</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$bird_wage.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_fbirds[$start_date])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($opn_frate[$start_date],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($opn_famt[$start_date],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_mbirds[$start_date])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($opn_mrate[$start_date],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($opn_mamt[$start_date],2).'</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<td colspan="'.$e_cnt.'"></td>'; }
                    echo '</tr>';
                    $tfr_qty += (float)$opn_fbirds[$start_date];
                    $tfb_amt += (float)$opn_famt[$start_date];
                    $tmr_qty += (float)$opn_mbirds[$start_date];
                    $tmb_amt += (float)$opn_mamt[$start_date];
                }

                //Purchase
                $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` IN ('$fbird_code','$mbird_code')".$pur_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`icode` ASC";
                $query = mysqli_query($conn,$sql); $fpur_rqty = $fpur_fqty = $fpur_rate = $fpur_bamt = $mpur_rqty = $mpur_fqty = $mpur_rate = $mpur_bamt = $key_alist = $fbwt_bqty = $fbwt_bamt = $mbwt_bqty = $mbwt_bamt = array();
                while($row = mysqli_fetch_array($query)){
                    $key = $row['date']."@".$row['trnum'];
                    if($fbird_code == $row['icode']){
                        $fpur_rqty[$key] += (float)$row['rcd_qty']; $fpur_fqty[$key] += (float)$row['fre_qty']; $fpur_rate[$key] += (float)$row['rate']; $fpur_bamt[$key] += (float)$row['item_tamt'];
                        if(strtotime($row['date']) <= strtotime($start_date)){
                            $opn_fbird_qty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $opn_fbird_amt += (float)$row['item_tamt'];
                        }
                        else{
                            $fbwt_bqty[$row['date']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $fbwt_bamt[$row['date']] += (float)$row['item_tamt'];
                        }
                    }
                    else if($mbird_code == $row['icode']){
                        $mpur_rqty[$key] += (float)$row['rcd_qty']; $mpur_fqty[$key] += (float)$row['fre_qty']; $mpur_rate[$key] += (float)$row['rate']; $mpur_bamt[$key] += (float)$row['item_tamt'];
                        if(strtotime($row['date']) <= strtotime($start_date)){
                            $opn_mbird_qty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $opn_mbird_amt += (float)$row['item_tamt'];
                        }
                        else{
                            $mbwt_bqty[$row['date']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $mbwt_bamt[$row['date']] += (float)$row['item_tamt'];
                        }
                    }
                    $key_alist[$key] = $key;
                }
                //Transfer-In
                $sql = "SELECT * FROM `breeder_bird_transfer` WHERE `to_flock` IN ('$flocks') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_array($query)){
                    $key = $row['date']."@".$row['trnum'];
                    $fpur_rqty[$key] += (float)$row['female_bqty']; $fpur_rate[$key] += (float)$row['female_bprc']; $fpur_bamt[$key] += (float)$row['female_bamt'];
                    $mpur_rqty[$key] += (float)$row['male_bqty']; $mpur_rate[$key] += (float)$row['male_bprc']; $mpur_bamt[$key] += (float)$row['male_bamt'];
                    $key_alist[$key] = $key; $fbrd_tiage[$key] = age_in_weeks((float)$row['bird_age']);
                    if(strtotime($row['date']) <= strtotime($start_date)){
                        $opn_fbird_qty += (float)$row['female_bqty'];
                        $opn_fbird_amt += (float)$row['female_bamt'];
                        $opn_mbird_qty += (float)$row['male_bqty'];
                        $opn_mbird_amt += (float)$row['male_bamt'];
                    }
                    else{
                        $fbwt_bqty[$row['date']] += (float)$row['female_bqty'];
                        $fbwt_bamt[$row['date']] += (float)$row['female_bamt'];
                        $mbwt_bqty[$row['date']] += (float)$row['male_bqty'];
                        $mbwt_bamt[$row['date']] += (float)$row['male_bamt'];
                    }
                }
                asort($key_alist);
                foreach($key_alist as $key){
                    $inv_dt1 = array(); $inv_dt1 = explode("@",$key);
                    if(empty($fpur_rqty[$key]) || $fpur_rqty[$key] == ""){ $fpur_rqty[$key] = 0; }
                    if(empty($fpur_fqty[$key]) || $fpur_fqty[$key] == ""){ $fpur_fqty[$key] = 0; }
                    if(empty($fpur_rate[$key]) || $fpur_rate[$key] == ""){ $fpur_rate[$key] = 0; }
                    if(empty($fpur_bamt[$key]) || $fpur_bamt[$key] == ""){ $fpur_bamt[$key] = 0; }
                    if(empty($mpur_rqty[$key]) || $mpur_rqty[$key] == ""){ $mpur_rqty[$key] = 0; }
                    if(empty($mpur_fqty[$key]) || $mpur_fqty[$key] == ""){ $mpur_fqty[$key] = 0; }
                    if(empty($mpur_rate[$key]) || $mpur_rate[$key] == ""){ $mpur_rate[$key] = 0; }
                    if(empty($mpur_bamt[$key]) || $mpur_bamt[$key] == ""){ $mpur_bamt[$key] = 0; }
                    if(empty($fbrd_tiage[$key]) || $fbrd_tiage[$key] == ""){ $fbrd_tiage[$key] = age_in_weeks(($fbrd_oage[$start_date] + ((strtotime($inv_dt1[0]) - strtotime($start_date)) / 60 / 60 / 24))); }

                    $slno++;
                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($inv_dt1[0])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$inv_dt1[1].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$fbrd_tiage[$key].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.str_replace(".00","",number_format_ind(($fpur_rqty[$key] + $fpur_fqty[$key]))).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fpur_rate[$key],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fpur_bamt[$key],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.str_replace(".00","",number_format_ind(($mpur_rqty[$key] + $mpur_fqty[$key]))).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($mpur_rate[$key],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($mpur_bamt[$key],2).'</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<td colspan="'.$e_cnt.'"></td>'; }
                    echo '</tr>';
                    $tfr_qty += (float)$fpur_rqty[$key];
                    $tff_qty += (float)$fpur_fqty[$key];
                    $tfb_amt += (float)$fpur_bamt[$key];
                    $tmr_qty += (float)$mpur_rqty[$key];
                    $tmf_qty += (float)$mpur_fqty[$key];
                    $tmb_amt += (float)$mpur_bamt[$key];
                }
                echo '</tbody>';
                echo '<tr class="thead3">';
                echo '<th colspan="'.($ccnt1 - 18).'" style="text-align:left;">Total</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.str_replace(".00","",number_format_ind(($tfr_qty + $tff_qty))).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfb_amt,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.str_replace(".00","",number_format_ind(($tmr_qty + $tmf_qty))).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmb_amt,2).'</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';

                //Feed Opening/Transfer-In/Purchased-In
                $tfeed_bgs = $tfeed_kgs = $tfeed_rate = $tfeed_amt = 0;
                echo '<thead class="thead3" id="head_names">';
                echo '<tr style="text-align:center;" align="center">';
                echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Opening/Transfer-In/Purchased-In</td>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th colspan="'.$ccnt3.'">Sl.No.</th>';
                echo '<th colspan="'.$ccnt3.'">Date</th>';
                echo '<th colspan="'.$ccnt3.'">Transaction No.</th>';
                echo '<th colspan="'.$ccnt3.'">DC No.</th>';
                echo '<th colspan="'.$ccnt3.'">Feed Name</th>';
                echo '<th colspan="'.$ccnt3.'">From Warehouse / Farm</th>';
                echo '<th colspan="'.$ccnt3.'">Quantity in Bags</th>';
                echo '<th colspan="'.$ccnt3.'">Quantity in Kgs</th>';
                echo '<th colspan="'.$ccnt3.'">Rate</th>';
                echo '<th colspan="'.$ccnt3.'">Amount</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="tbody1" id="tbody1">';
                
                $sql = "SELECT * FROM `feed_bagcapacity` WHERE `code` IN ('$feed_list') AND`dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $bag_size = array();
                while($row = mysqli_fetch_assoc($query)){ $bag_size[$row['code']] = $row['bag_size']; }

                //Purchase
                $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` IN ('$feed_list')".$pur_fltr2." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`icode` ASC";
                $query = mysqli_query($conn,$sql); $slno = 0;
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    if(!empty($sector_name[$row['fromwarehouse']]) && $sector_name[$row['fromwarehouse']] != ""){ $sname = $sector_name[$row['fromwarehouse']]; }
                    else if(!empty($sector_name[$row['from_unit']]) && $sector_name[$row['from_unit']] != ""){ $sname = $sector_name[$row['from_unit']]; }
                    else if(!empty($sector_name[$row['from_shed']]) && $sector_name[$row['from_shed']] != ""){ $sname = $sector_name[$row['from_shed']]; }
                    else if(!empty($sector_name[$row['from_batch']]) && $sector_name[$row['from_batch']] != ""){ $sname = $sector_name[$row['from_batch']]; }
                    else if(!empty($sector_name[$row['from_flock']]) && $sector_name[$row['from_flock']] != ""){ $sname = $sector_name[$row['from_flock']]; }
                    else{ $sname = $sector_name[$row['from_flock']]; }

                    $fqty_kgs = $row['rcd_qty'] + $row['fre_qty'];
                    if(!empty($bag_size[$row['icode']]) && $bag_size[$row['icode']] != "" && (float)$bag_size[$row['icode']] > 0){ $fqty_bgs = $fqty_kgs / $bag_size[$row['icode']]; } else{ $fqty_bgs = $fqty_kgs / 50; }

                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['trnum'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['dcno'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$feed_name[$row['icode']].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$sname.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fqty_bgs,2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fqty_kgs,2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['rate'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['item_tamt'],2).'</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    $tfeed_bgs += (float)$fqty_bgs;
                    $tfeed_kgs += (float)$fqty_kgs;
                    $tfeed_amt += (float)$row['item_tamt'];
                }
                //Transfer-In
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$feed_list')".$trin_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`code` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    if(!empty($sector_name[$row['fromwarehouse']]) && $sector_name[$row['fromwarehouse']] != ""){ $sname = $sector_name[$row['fromwarehouse']]; }
                    else if(!empty($sector_name[$row['from_unit']]) && $sector_name[$row['from_unit']] != ""){ $sname = $sector_name[$row['from_unit']]; }
                    else if(!empty($sector_name[$row['from_shed']]) && $sector_name[$row['from_shed']] != ""){ $sname = $sector_name[$row['from_shed']]; }
                    else if(!empty($sector_name[$row['from_batch']]) && $sector_name[$row['from_batch']] != ""){ $sname = $sector_name[$row['from_batch']]; }
                    else if(!empty($sector_name[$row['from_flock']]) && $sector_name[$row['from_flock']] != ""){ $sname = $sector_name[$row['from_flock']]; }
                    else{ $sname = $sector_name[$row['from_flock']]; }

                    $fqty_kgs = $row['quantity'];
                    if(!empty($bag_size[$row['code']]) && $bag_size[$row['code']] != "" && (float)$bag_size[$row['code']] > 0){ $fqty_bgs = $fqty_kgs / $bag_size[$row['code']]; } else{ $fqty_bgs = $fqty_kgs / 50; }

                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['trnum'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['dcno'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$feed_name[$row['code']].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$sname.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fqty_bgs,2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fqty_kgs,2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['price'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['amount'],2).'</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    $tfeed_bgs += (float)$fqty_bgs;
                    $tfeed_kgs += (float)$fqty_kgs;
                    $tfeed_amt += (float)$row['amount'];
                }

                echo '</tbody>';
                echo '<tr class="thead3">';
                echo '<th colspan="'.($ccnt1 - 12).'" style="text-align:left;">Total</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfeed_bgs,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfeed_kgs,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfeed_amt,2).'</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';

                //Feed Transfer-Out
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$feed_list')".$trot_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`code` ASC";
                $query = mysqli_query($conn,$sql); $t_cnt = mysqli_num_rows($query);
                if((int)$t_cnt > 0){
                    $tfeed_obgs = $tfeed_okgs = $tfeed_oamt = 0;
                    echo '<thead class="thead3" id="head_names">';
                    echo '<tr style="text-align:center;" align="center">';
                    echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Feed Return</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    echo '<tr style="text-align:center;" align="center">';
                    echo '<th colspan="'.$ccnt3.'">Sl.No.</th>';
                    echo '<th colspan="'.$ccnt3.'">Date</th>';
                    echo '<th colspan="'.$ccnt3.'">Transaction No.</th>';
                    echo '<th colspan="'.$ccnt3.'">DC No.</th>';
                    echo '<th colspan="'.$ccnt3.'">Feed Name</th>';
                    echo '<th colspan="'.$ccnt3.'">To Warehouse / Farm</th>';
                    echo '<th colspan="'.$ccnt3.'">Quantity in Bags</th>';
                    echo '<th colspan="'.$ccnt3.'">Quantity in Kgs</th>';
                    echo '<th colspan="'.$ccnt3.'">Rate</th>';
                    echo '<th colspan="'.$ccnt3.'">Amount</th>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody class="tbody1" id="tbody1">';
                    while($row = mysqli_fetch_array($query)){
                        $slno++;
                        if(!empty($sector_name[$row['towarehouse']]) && $sector_name[$row['towarehouse']] != ""){ $sname = $sector_name[$row['towarehouse']]; }
                        else if(!empty($sector_name[$row['to_unit']]) && $sector_name[$row['to_unit']] != ""){ $sname = $sector_name[$row['to_unit']]; }
                        else if(!empty($sector_name[$row['to_shed']]) && $sector_name[$row['to_shed']] != ""){ $sname = $sector_name[$row['to_shed']]; }
                        else if(!empty($sector_name[$row['to_batch']]) && $sector_name[$row['to_batch']] != ""){ $sname = $sector_name[$row['to_batch']]; }
                        else if(!empty($sector_name[$row['to_flock']]) && $sector_name[$row['to_flock']] != ""){ $sname = $sector_name[$row['to_flock']]; }
                        else{ $sname = $sector_name[$row['to_flock']]; }

                        $fqty_kgs = $row['quantity'];
                        if(!empty($bag_size[$row['code']]) && $bag_size[$row['code']] != "" && (float)$bag_size[$row['code']] > 0){ $fqty_bgs = $fqty_kgs / $bag_size[$row['code']]; } else{ $fqty_bgs = $fqty_kgs / 50; }

                        echo '<tr>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['trnum'].'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['dcno'].'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$feed_name[$row['code']].'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$sname.'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fqty_bgs,2).'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($fqty_kgs,2).'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['price'],2).'</td>';
                        echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['amount'],2).'</td>';
                        if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                        echo '</tr>';
                        $tfeed_obgs += (float)$fqty_bgs;
                        $tfeed_okgs += (float)$fqty_kgs;
                        $tfeed_oamt += (float)$row['amount'];
                    }

                    echo '</tbody>';
                    echo '<tr class="thead3">';
                    echo '<th colspan="'.($ccnt1 - 12).'" style="text-align:left;">Total</th>';
                    echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfeed_obgs,2).'</th>';
                    echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfeed_okgs,2).'</th>';
                    echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                    echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tfeed_oamt,2).'</th>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                }
                //Female (or) Male birds: Transfer-Out
                $sql = "SELECT * FROM `breeder_bird_transfer` WHERE `from_flock` IN ('$flocks') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $fbwt_obqty = $fbwt_obamt = $mbwt_obqty = $mbwt_obamt = array();
                while($row = mysqli_fetch_array($query)){
                    $key = $row['date']."@".$row['trnum'];
                    $ftout_rqty[$key] += (float)$row['female_bqty']; $ftout_rate[$key] += (float)$row['female_bprc']; $ftout_bamt[$key] += (float)$row['female_bamt'];
                    $mtout_rqty[$key] += (float)$row['male_bqty']; $mtout_rate[$key] += (float)$row['male_bprc']; $mtout_bamt[$key] += (float)$row['male_bamt'];
                    $key_alist[$key] = $key; $fbrd_tiage[$key] = age_in_weeks((float)$row['bird_age']);
                    if(strtotime($row['date']) <= strtotime($start_date)){
                        $opn_fbird_qty -= (float)$row['female_bqty'];
                        $opn_fbird_amt -= (float)$row['female_bamt'];
                        $opn_mbird_qty -= (float)$row['male_bqty'];
                        $opn_mbird_amt -= (float)$row['male_bamt'];
                    }
                    else{
                        $fbwt_obqty[$row['date']] += (float)$row['female_bqty'];
                        $fbwt_obamt[$row['date']] += (float)$row['female_bamt'];
                        $mbwt_obqty[$row['date']] += (float)$row['male_bqty'];
                        $mbwt_obamt[$row['date']] += (float)$row['male_bamt'];
                    }
                }

                //Female (or) Male birds: Sales
                $sql = "SELECT * FROM `broiler_sales` WHERE `icode` IN ('$fbird_code','$mbird_code')".$sale_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`icode` ASC";
                $query = mysqli_query($conn,$sql); $fsale_rqty = $fsale_fqty = $fsale_rate = $fsale_bamt = $msale_rqty = $msale_fqty = $msale_rate = $msale_bamt = $key_alist = $fbwt_sbqty = $fbwt_sbamt = $mbwt_sbqty = $mbwt_sbamt = array();
                while($row = mysqli_fetch_array($query)){
                    $key = $row['date']."@".$row['trnum'];
                    if($fbird_code == $row['icode']){
                        $fsale_rqty[$key] += (float)$row['rcd_qty']; $fsale_fqty[$key] += (float)$row['fre_qty']; $fsale_rate[$key] += (float)$row['rate']; $fsale_bamt[$key] += (float)$row['item_tamt'];
                        if(strtotime($row['date']) <= strtotime($start_date)){
                            $opn_fbird_qty -= ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $opn_fbird_amt -= (float)$row['item_tamt'];
                        }
                        else{
                            $fbwt_sbqty[$row['date']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $fbwt_sbamt[$row['date']] += (float)$row['item_tamt'];
                        }
                    }
                    else if($mbird_code == $row['icode']){
                        $msale_rqty[$key] += (float)$row['rcd_qty']; $msale_fqty[$key] += (float)$row['fre_qty']; $msale_rate[$key] += (float)$row['rate']; $msale_bamt[$key] += (float)$row['item_tamt'];
                        if(strtotime($row['date']) <= strtotime($start_date)){
                            $opn_mbird_qty -= ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $opn_mbird_amt -= (float)$row['item_tamt'];
                        }
                        else{
                            $mbwt_sbqty[$row['date']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $mbwt_sbamt[$row['date']] += (float)$row['item_tamt'];
                        }
                    }
                    $key_alist[$key] = $key;
                }
                //No.of Weeks
                $weeks = $wname = $a_week = $wf_day = array();
                for($i = 0;$i <= 100;$i++){ $j = $i * 7; $weeks[$j] = $j; $wname[$j] = fetch_week_names($i); }
                for($i = 0;$i <= 800;$i++){ $wno = (int) ceil($i / 7); $a_week[$i] = $wno; }
                for($i = 1;$i <= (int) ceil(800 / 7);$i++){ $wf_day[$i] = ($i - 1) * 7 + 1; }


                //Mortality And Feed Consumption Details
                $tfeed_obgs = $tfeed_okgs = $tfeed_oamt = 0;
                echo '<thead class="thead3" id="head_names">';
                echo '<tr style="text-align:center;" align="center">';
                echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Mortality And Feed Consumption Details</td>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th></th>';
                echo '<th></th>';
                echo '<th colspan="'.$ccnt2.'">Female Birds</th>';
                echo '<th colspan="'.$ccnt2.'">Male Birds</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'" rowspan="2">Egg Production</th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th></th>';
                echo '<th></th>';
                echo '<th>Opening</th>';
                echo '<th colspan="5">Mortality</th>';
                echo '<th>Sale</th>';
                echo '<th>Transfer-Out</th>';
                echo '<th>Closing</th>';
                echo '<th></th>';
                echo '<th></th>';
                echo '<th colspan="3">Feed</th>';
                echo '<th>Opening</th>';
                echo '<th colspan="5">Mortality</th>';
                echo '<th>Sale</th>';
                echo '<th>Transfer-Out</th>';
                echo '<th>Closing</th>';
                echo '<th></th>';
                echo '<th></th>';
                echo '<th colspan="3">Feed</th>';
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th>Date</th>';
                echo '<th>Age</th>';
                echo '<th>Birds</th>';
                echo '<th>Count</th>';
                echo '<th>%</th>';
                echo '<th>Culls</th>';
                echo '<th>Cum. Mort.</th>';
                echo '<th>Cum Mort%</th>';
                echo '<th>Birds</th>';
                echo '<th>Birds</th>';
                echo '<th>Birds</th>';
                echo '<th>Avg.Bw</th>';
                echo '<th>FCR</th>';
                echo '<th>Name</th>';
                echo '<th>Kgs</th>';
                echo '<th>Bags</th>';
                echo '<th>Birds</th>';
                echo '<th>Count</th>';
                echo '<th>%</th>';
                echo '<th>Culls</th>';
                echo '<th>Cum. Mort.</th>';
                echo '<th>Cum Mort%</th>';
                echo '<th>Birds</th>';
                echo '<th>Birds</th>';
                echo '<th>Birds</th>';
                echo '<th>Avg.Bw</th>';
                echo '<th>FCR</th>';
                echo '<th>Name</th>';
                echo '<th>Kgs</th>';
                echo '<th>Bags</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ foreach($egg_code as $eggs){ echo '<th>'.$egg_name[$eggs].'</th>'; } }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="tbody1" id="tbody1">';
                //Egg Production
                $sql = "SELECT * FROM `breeder_dayentry_produced` WHERE `flock_code` IN ('$flocks') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql); $egg_pqty = array();
                while($row = mysqli_fetch_array($query)){ $egg_pqty[$row['date']."@".$row['item_code']] += (float)$row['quantity']; }
                //Daily Entry
                $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `flock_code` IN ('$flocks') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql); $t_cnt = mysqli_num_rows($query);
                $slno = $opn_fbrds = $opn_mbrds = $cls_fbrds = $cls_mbrds = $fcum_mcnt = $mcum_mcnt = $fcum_fqty = $mcum_fqty = $fsale_bds = $msale_bds = 0;
                $ww_fmqty = $ww_fcqty = $ww_fsqty = $ww_foqty = $ww_ffqty = $ww_ffbgs = 0; $ww_egqty = array();
                $ww_mmqty = $ww_mcqty = $ww_msqty = $ww_moqty = $ww_mfqty = $ww_mfbgs = 0;
                $gt_fmqty = $gt_fcqty = $gt_fsqty = $gt_foqty = $gt_ffqty = $gt_ffbgs = 0; $gt_egqty = array();
                $gt_mmqty = $gt_mcqty = $gt_msqty = $gt_moqty = $gt_mfqty = $gt_mfbgs = 0;
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    //Opening birds
                    if($slno == 1){ $opn_fbrds = $opn_fbird_qty; $opn_mbrds = $opn_mbird_qty; } else{ $opn_fbrds = $cls_fbrds; $opn_mbrds = $cls_mbrds; }

                    //Mort %
                    $fmort_per = 0; if((float)$opn_fbrds != 0){ $fmort_per = round((($row['fmort_qty'] / $opn_fbrds) * 100),2); }
                    $mmort_per = 0; if((float)$opn_mbrds != 0){ $mmort_per = round((($row['mmort_qty'] / $opn_mbrds) * 100),2); }

                    //Cum. Mort Count & %
                    $fcum_mcnt += (float)$row['fmort_qty'];
                    $fcum_mper = 0; if((float)$opn_fbird_qty != 0){ $fcum_mper = round((($fcum_mcnt / $opn_fbird_qty) * 100),2); }
                    $mcum_mcnt += (float)$row['mmort_qty'];
                    $mcum_mper = 0; if((float)$opn_mbird_qty != 0){ $mcum_mper = round((($mcum_mcnt / $opn_mbird_qty) * 100),2); }

                    //Closing birds
                    $fbp_bds = $mbp_bds = $fbp_obds = $mbp_obds = $fbp_sbds = $mbp_sbds = 0; 
                    if(strtotime($row['date']) > strtotime($start_date)){
                        if(!empty($fbwt_bqty[$row['date']]) && (float)$fbwt_bqty[$row['date']] > 0){ $fbp_bds = (float)$fbwt_bqty[$row['date']]; }
                        if(!empty($mbwt_bqty[$row['date']]) && (float)$mbwt_bqty[$row['date']] > 0){ $mbp_bds = (float)$mbwt_bqty[$row['date']]; }

                        if(!empty($fbwt_obqty[$row['date']]) && (float)$fbwt_obqty[$row['date']] > 0){ $fbp_obds = (float)$fbwt_obqty[$row['date']]; }
                        if(!empty($mbwt_obqty[$row['date']]) && (float)$mbwt_obqty[$row['date']] > 0){ $mbp_obds = (float)$mbwt_obqty[$row['date']]; }

                        if(!empty($fbwt_sbqty[$row['date']]) && (float)$fbwt_sbqty[$row['date']] > 0){ $fbp_sbds = (float)$fbwt_sbqty[$row['date']]; }
                        if(!empty($mbwt_sbqty[$row['date']]) && (float)$mbwt_sbqty[$row['date']] > 0){ $mbp_sbds = (float)$mbwt_sbqty[$row['date']]; }
                    }
                    $cls_fbrds = (((float)$opn_fbrds + (float)$fbp_bds) - ((float)$row['fmort_qty'] + (float)$row['fcull_qty'] + (float)$fbp_obds + (float)$fbp_sbds));
                    $cls_mbrds = (((float)$opn_mbrds + (float)$mbp_bds) - ((float)$row['mmort_qty'] + (float)$row['mcull_qty'] + (float)$mbp_obds + (float)$mbp_sbds));
                    
                    //Feed in Bags
                    if(!empty($bag_size[$row['ffeed_code1']]) && $bag_size[$row['ffeed_code1']] != "" && (float)$bag_size[$row['ffeed_code1']] > 0){ $ffqty_bgs = $row['ffeed_qty1'] / $bag_size[$row['ffeed_code1']]; } else{ $ffqty_bgs = $row['ffeed_qty1'] / 50; }
                    if(!empty($bag_size[$row['mfeed_code1']]) && $bag_size[$row['mfeed_code1']] != "" && (float)$bag_size[$row['mfeed_code1']] > 0){ $mfqty_bgs = $row['mfeed_qty1'] / $bag_size[$row['mfeed_code1']]; } else{ $mfqty_bgs = $row['mfeed_qty1'] / 50; }
                    
                    //Cum. Sale birds
                    $fsale_bds += (float)$fbp_sbds;
                    $msale_bds += (float)$mbp_sbds;

                    //Cum. Feed Consumed
                    $fcum_fqty += (float)$row['ffeed_qty1'];
                    $mcum_fqty += (float)$row['mfeed_qty1'];

                    //FCR
                    $ffcr = 0; if((($row['fbody_weight'] / 1000) * ((float)$cls_fbrds + (float)$fsale_bds)) != 0){ $ffcr = $fcum_fqty / (($row['fbody_weight'] / 1000) * ((float)$cls_fbrds + (float)$fsale_bds)); }
                    $mfcr = 0; if((($row['mbody_weight'] / 1000) * ((float)$cls_mbrds + (float)$msale_bds)) != 0){ $mfcr = $mcum_fqty / (($row['mbody_weight'] / 1000) * ((float)$cls_mbrds + (float)$msale_bds)); }

                    echo '<tr>';
                    //Female Display
                    echo '<td style="text-align:left;background-color:#fff0ed;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                    echo '<td style="text-align:center;background-color:#fff0ed;">'.round($row['breed_wage'],1).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($opn_fbrds)).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($row['fmort_qty'])).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.decimal_adjustments($fmort_per,2).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($row['fcull_qty'])).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($fcum_mcnt)).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.decimal_adjustments($fcum_mper,2).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($fbp_sbds)).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($fbp_obds)).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.str_replace(".00","",number_format_ind($cls_fbrds)).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.decimal_adjustments($row['fbody_weight'],3).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.decimal_adjustments($ffcr,3).'</td>';
                    echo '<td style="text-align:left;background-color:#fff0ed;">'.$feed_name[$row['ffeed_code1']].'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.decimal_adjustments($row['ffeed_qty1'],2).'</td>';
                    echo '<td style="text-align:right;background-color:#fff0ed;">'.decimal_adjustments($ffqty_bgs,2).'</td>';
                    //Male Display
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($opn_mbrds)).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($row['mmort_qty'])).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.decimal_adjustments($mmort_per,2).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($row['mcull_qty'])).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($mcum_mcnt)).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.decimal_adjustments($fcum_mper,2).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($mbp_sbds)).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($mbp_obds)).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.str_replace(".00","",number_format_ind($cls_mbrds)).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.decimal_adjustments($row['mbody_weight'],3).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.decimal_adjustments($mfcr,3).'</td>';
                    echo '<td style="text-align:left;background-color:#edefff;">'.$feed_name[$row['mfeed_code1']].'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.decimal_adjustments($row['mfeed_qty1'],2).'</td>';
                    echo '<td style="text-align:right;background-color:#edefff;">'.decimal_adjustments($mfqty_bgs,2).'</td>';

                    //Egg Production
                    if($egg_pflag == 1 && $e_cnt > 0){
                        foreach($egg_code as $eggs){
                            $key2 = $row['date']."@".$eggs;
                            $e_qty = 0; if(!empty($egg_pqty[$key2]) && (float)$egg_pqty[$key2] > 0){ $e_qty = (float)$egg_pqty[$key2]; }
                            echo '<td style="text-align:right;background-color:#f1ffed;">'.str_replace(".00","",number_format_ind($e_qty)).'</td>';
                            $ww_egqty[$eggs] += (float)$e_qty;
                            $gt_egqty[$eggs] += (float)$e_qty;
                        }
                    }
                    echo '</tr>';

                    //Week Wise Totals
                    $wcp_flag = 1;
                    $ww_wage = $row['breed_wage'];
                    $ww_age = $row['breed_age'];
                    $wno = $a_week[$ww_age]; $fday = $wf_day[$wno]; if((int)$ww_age == (int)$fday){ $ww_ofbds = $opn_fbrds; $ww_ombds = $opn_mbrds; }
                    $ww_fmqty += (float)$row['fmort_qty'];
                    $ww_fcqty += (float)$row['fcull_qty'];
                    $ww_fsqty += (float)$fbp_sbds;
                    $ww_foqty += (float)$fbp_obds;
                    $ww_ffqty += (float)$row['ffeed_qty1'];
                    $ww_ffbgs += (float)$ffqty_bgs;

                    $ww_mmqty += (float)$row['mmort_qty'];
                    $ww_mcqty += (float)$row['mcull_qty'];
                    $ww_msqty += (float)$mbp_sbds;
                    $ww_moqty += (float)$mbp_obds;
                    $ww_mfqty += (float)$row['mfeed_qty1'];
                    $ww_mfbgs += (float)$mfqty_bgs;

                    if(!empty($weeks[$ww_age]) && $weeks[$ww_age] == $ww_age){
                        $fmort_per = 0; if((float)$ww_ofbds != 0){ $fmort_per = round((((float)$ww_fmqty / (float)$ww_ofbds) * 100),2); }
                        $mmort_per = 0; if((float)$ww_ombds != 0){ $mmort_per = round((((float)$ww_mmqty / (float)$ww_ombds) * 100),2); }
                        $ww_fcbds = ((float)$ww_ofbds - ((float)$ww_fmqty + (float)$ww_fcqty + (float)$ww_fsqty + (float)$ww_foqty));
                        $ww_mcbds = ((float)$ww_ombds - ((float)$ww_mmqty + (float)$ww_mcqty + (float)$ww_msqty + (float)$ww_moqty));
                        echo '<tr>';
                        echo '<th style="text-align:left;color:red;">'.$wname[$ww_age].'</th>';
                        //Female Display
                        echo '<th style="text-align:center;color:red;">'.round($ww_wage,1).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_ofbds)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fmqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($fmort_per,2).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fcqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($fcum_mcnt)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($fcum_mper,2).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fsqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_foqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fcbds)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($row['fbody_weight'],3).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ffcr,3).'</th>';
                        echo '<th style="text-align:left;color:red;"></th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_ffqty,2).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_ffbgs,2).'</th>';
                        $ww_fmqty = $ww_fcqty = $ww_fsqty = $ww_foqty = $ww_ffqty = $ww_ffbgs = 0;
                        //Male Display
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_ombds)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_mmqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($mmort_per,2).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_mcqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($mcum_mcnt)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($fcum_mper,2).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_msqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_moqty)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_mcbds)).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($row['mbody_weight'],3).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($mfcr,3).'</th>';
                        echo '<th style="text-align:left;color:red;"></th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_mfqty,2).'</th>';
                        echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_mfbgs,2).'</th>';
                        $ww_mmqty = $ww_mcqty = $ww_msqty = $ww_moqty = $ww_mfqty = $ww_mfbgs = 0;
                        //Egg Production
                        if($egg_pflag == 1 && $e_cnt > 0){
                            foreach($egg_code as $eggs){
                                $e_qty = 0; if(!empty($ww_egqty[$eggs]) && (float)$ww_egqty[$eggs] > 0){ $e_qty = (float)$ww_egqty[$eggs]; }
                                echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($e_qty)).'</th>';
                            }
                        }
                        $ww_egqty = array();
                        $wcp_flag = 0;
                        echo '</tr>';
                    }

                    //Final Totals
                    if((int)$slno == 1){ $gt_age = $row['breed_wage']; $gt_ofbds = $opn_fbrds; $gt_ombds = $opn_mbrds; }
                    $gt_fmqty += (float)$row['fmort_qty'];
                    $gt_fcqty += (float)$row['fcull_qty'];
                    $gt_fsqty += (float)$fbp_sbds;
                    $gt_foqty += (float)$fbp_obds;
                    $gt_ffqty += (float)$row['ffeed_qty1'];
                    $gt_ffbgs += (float)$ffqty_bgs;

                    $gt_mmqty += (float)$row['mmort_qty'];
                    $gt_mcqty += (float)$row['mcull_qty'];
                    $gt_msqty += (float)$mbp_sbds;
                    $gt_moqty += (float)$mbp_obds;
                    $gt_mfqty += (float)$row['mfeed_qty1'];
                    $gt_mfbgs += (float)$mfqty_bgs;
                }
                $slno++;
                if($wcp_flag == 1){
                    $fmort_per = 0; if((float)$ww_ofbds != 0){ $fmort_per = round((((float)$ww_fmqty / (float)$ww_ofbds) * 100),2); }
                    $mmort_per = 0; if((float)$ww_ombds != 0){ $mmort_per = round((((float)$ww_mmqty / (float)$ww_ombds) * 100),2); }
                    $ww_fcbds = ((float)$ww_ofbds - ((float)$ww_fmqty + (float)$ww_fcqty + (float)$ww_fsqty + (float)$ww_foqty));
                    $ww_mcbds = ((float)$ww_ombds - ((float)$ww_mmqty + (float)$ww_mcqty + (float)$ww_msqty + (float)$ww_moqty));
                    echo '<tr>';
                    echo '<th style="text-align:left;color:red;">'.$wname[$ww_age].'</th>';
                    //Female Display
                    echo '<th style="text-align:center;color:red;">'.round($ww_wage,1).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_ofbds)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fmqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($fmort_per,2).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fcqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($fcum_mcnt)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($fcum_mper,2).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fsqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_foqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_fcbds)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($row['fbody_weight'],3).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ffcr,3).'</th>';
                    echo '<th style="text-align:left;color:red;"></th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_ffqty,2).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_ffbgs,2).'</th>';
                    $ww_fmqty = $ww_fcqty = $ww_fsqty = $ww_foqty = $ww_ffqty = $ww_ffbgs = 0;
                    //Male Display
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_ombds)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_mmqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($mmort_per,2).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_mcqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($mcum_mcnt)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($fcum_mper,2).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_msqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_moqty)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($ww_mcbds)).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($row['mbody_weight'],3).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($mfcr,3).'</th>';
                    echo '<th style="text-align:left;color:red;"></th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_mfqty,2).'</th>';
                    echo '<th style="text-align:right;color:red;">'.decimal_adjustments($ww_mfbgs,2).'</th>';
                    $ww_mmqty = $ww_mcqty = $ww_msqty = $ww_moqty = $ww_mfqty = $ww_mfbgs = 0;
                    //Egg Production
                    if($egg_pflag == 1 && $e_cnt > 0){
                        foreach($egg_code as $eggs){
                                $e_qty = 0; if(!empty($ww_egqty[$eggs]) && (float)$ww_egqty[$eggs] > 0){ $e_qty = (float)$ww_egqty[$eggs]; }
                            echo '<th style="text-align:right;color:red;">'.str_replace(".00","",number_format_ind($e_qty)).'</th>';
                        }
                    }
                    $ww_egqty = array();
                    $wcp_flag = 0;
                    echo '</tr>';
                }
                
                echo '</tbody>';
                $fmort_per = 0; if((float)$gt_ofbds != 0){ $fmort_per = round((((float)$gt_fmqty / (float)$gt_ofbds) * 100),2); }
                $mmort_per = 0; if((float)$gt_ombds != 0){ $mmort_per = round((((float)$gt_mmqty / (float)$gt_ombds) * 100),2); }
                $gt_fcbds = ((float)$gt_ofbds - ((float)$gt_fmqty + (float)$gt_fcqty + (float)$gt_fsqty + (float)$gt_foqty));
                $gt_mcbds = ((float)$gt_ombds - ((float)$gt_mmqty + (float)$gt_mcqty + (float)$gt_msqty + (float)$gt_moqty));
                echo '<tr class="thead3">';
                echo '<th style="text-align:left;color:red;">Grand Total</th>';
                //Female Display
                echo '<th style="text-align:center;">'.round($gt_age,1).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_ofbds)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_fmqty)).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($fmort_per,2).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_fcqty)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fcum_mcnt)).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($fcum_mper,2).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_fsqty)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_foqty)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_fcbds)).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($row['fbody_weight'],3).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($ffcr,3).'</th>';
                echo '<th style="text-align:left;"></th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($gt_ffqty,2).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($gt_ffbgs,2).'</th>';
                $gt_fmqty = $gt_fcqty = $gt_fsqty = $gt_foqty = $gt_ffqty = $gt_ffbgs = 0;
                //Male Display
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_ombds)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_mmqty)).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($mmort_per,2).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_mcqty)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($mcum_mcnt)).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($fcum_mper,2).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_msqty)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_moqty)).'</th>';
                echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($gt_mcbds)).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($row['mbody_weight'],3).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($mfcr,3).'</th>';
                echo '<th style="text-align:left;"></th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($gt_mfqty,2).'</th>';
                echo '<th style="text-align:right;">'.decimal_adjustments($gt_mfbgs,2).'</th>';
                $gt_mmqty = $gt_mcqty = $gt_msqty = $gt_moqty = $gt_mfqty = $gt_mfbgs = 0;
                //Egg Production
                if($egg_pflag == 1 && $e_cnt > 0){
                    foreach($egg_code as $eggs){
                            $e_qty = 0; if(!empty($gt_egqty[$eggs]) && (float)$gt_egqty[$eggs] > 0){ $e_qty = (float)$gt_egqty[$eggs]; }
                        echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($e_qty)).'</th>';
                    }
                }
                $gt_egqty = array();
                $wcp_flag = 0;
                echo '</tr>';

                //Medicine and Vaccine Transfer In
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$medvac_list')".$trin_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`code` ASC";
                $query = mysqli_query($conn,$sql); $slno = 0;
                $tmv_iqty = $tmv_iamt = 0;
                echo '<thead class="thead3" id="head_names">';
                echo '<tr style="text-align:center;" align="center">';
                echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Transfer In</td>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th colspan="'.$ccnt3.'">Sl.No.</th>';
                echo '<th colspan="'.$ccnt3.'">Date</th>';
                echo '<th colspan="'.$ccnt3.'">Transaction No.</th>';
                echo '<th colspan="'.$ccnt3.'">DC No.</th>';
                echo '<th colspan="'.$ccnt3.'">Warehouse / Farm</th>';
                echo '<th colspan="'.$ccnt3.'">Code</th>';
                echo '<th colspan="'.$ccnt3.'">Medicine/Vaccine Name</th>';
                echo '<th colspan="'.$ccnt3.'">Quantity</th>';
                echo '<th colspan="'.$ccnt3.'">Rate</th>';
                echo '<th colspan="'.$ccnt3.'">Amount</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="tbody1" id="tbody1">';
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    if(!empty($sector_name[$row['fromwarehouse']]) && $sector_name[$row['fromwarehouse']] != ""){ $sname = $sector_name[$row['fromwarehouse']]; }
                    else if(!empty($sector_name[$row['from_unit']]) && $sector_name[$row['from_unit']] != ""){ $sname = $sector_name[$row['from_unit']]; }
                    else if(!empty($sector_name[$row['from_shed']]) && $sector_name[$row['from_shed']] != ""){ $sname = $sector_name[$row['from_shed']]; }
                    else if(!empty($sector_name[$row['from_batch']]) && $sector_name[$row['from_batch']] != ""){ $sname = $sector_name[$row['from_batch']]; }
                    else if(!empty($sector_name[$row['from_flock']]) && $sector_name[$row['from_flock']] != ""){ $sname = $sector_name[$row['from_flock']]; }
                    else{ $sname = $sector_name[$row['from_flock']]; }

                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['trnum'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['dcno'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$sname.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['code'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$medvac_name[$row['code']].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['quantity'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['price'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['amount'],2).'</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    $tmv_iqty += (float)$row['quantity'];
                    $tmv_iamt += (float)$row['amount'];
                }

                echo '</tbody>';
                echo '<tr class="thead3">';
                echo '<th colspan="'.($ccnt1 - 9).'" style="text-align:left;">Total</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmv_iqty,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmv_iamt,2).'</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';

                //Medicine and Vaccine Consumption
                $sql = "SELECT * FROM `breeder_medicine_consumed` WHERE `item_code` IN ('$medvac_list')".$mvcon_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`item_code` ASC";
                $query = mysqli_query($conn,$sql); $slno = 0;
                $tmv_cqty = $tmv_camt = 0;
                echo '<thead class="thead3" id="head_names">';
                echo '<tr style="text-align:center;" align="center">';
                echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Consumption</td>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th colspan="'.$ccnt3.'">Sl.No.</th>';
                echo '<th colspan="'.$ccnt3.'">Date</th>';
                echo '<th colspan="'.$ccnt3.'">Transaction No.</th>';
                echo '<th colspan="'.$ccnt3.'">Code</th>';
                echo '<th colspan="'.$ccnt3.'">Medicine/Vaccine Name</th>';
                echo '<th colspan="'.$ccnt3.'">Quantity</th>';
                echo '<th colspan="'.$ccnt3.'">Rate</th>';
                echo '<th colspan="'.$ccnt3.'">Amount</th>';
                echo '<th colspan="'.$ccnt3.'"></th>';
                echo '<th colspan="'.$ccnt3.'"></th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="tbody1" id="tbody1">';
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['trnum'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['item_code'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$medvac_name[$row['item_code']].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['quantity'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['mgmt_rate'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['mgmt_amt'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;"></td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;"></td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    $tmv_cqty += (float)$row['quantity'];
                    $tmv_camt += (float)$row['mgmt_amt'];
                }

                echo '</tbody>';
                echo '<tr class="thead3">';
                echo '<th colspan="'.($ccnt1 - 15).'" style="text-align:left;">Total</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmv_cqty,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmv_camt,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';

                //Medicine and Vaccine Return
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$medvac_list')".$trot_fltr1." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`code` ASC";
                $query = mysqli_query($conn,$sql); $slno = 0;
                $tmv_oqty = $tmv_oamt = 0;
                echo '<thead class="thead3" id="head_names">';
                echo '<tr style="text-align:center;" align="center">';
                echo '<td colspan="'.$ccnt1.'" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Return</td>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '<tr style="text-align:center;" align="center">';
                echo '<th colspan="'.$ccnt3.'">Sl.No.</th>';
                echo '<th colspan="'.$ccnt3.'">Date</th>';
                echo '<th colspan="'.$ccnt3.'">Transaction No.</th>';
                echo '<th colspan="'.$ccnt3.'">DC No.</th>';
                echo '<th colspan="'.$ccnt3.'">Warehouse / Farm</th>';
                echo '<th colspan="'.$ccnt3.'">Code</th>';
                echo '<th colspan="'.$ccnt3.'">Medicine/Vaccine Name</th>';
                echo '<th colspan="'.$ccnt3.'">Quantity</th>';
                echo '<th colspan="'.$ccnt3.'">Rate</th>';
                echo '<th colspan="'.$ccnt3.'">Amount</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="tbody1" id="tbody1">';
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    if(!empty($sector_name[$row['towarehouse']]) && $sector_name[$row['towarehouse']] != ""){ $sname = $sector_name[$row['towarehouse']]; }
                    else if(!empty($sector_name[$row['to_unit']]) && $sector_name[$row['to_unit']] != ""){ $sname = $sector_name[$row['to_unit']]; }
                    else if(!empty($sector_name[$row['to_shed']]) && $sector_name[$row['to_shed']] != ""){ $sname = $sector_name[$row['to_shed']]; }
                    else if(!empty($sector_name[$row['to_batch']]) && $sector_name[$row['to_batch']] != ""){ $sname = $sector_name[$row['to_batch']]; }
                    else if(!empty($sector_name[$row['to_flock']]) && $sector_name[$row['to_flock']] != ""){ $sname = $sector_name[$row['to_flock']]; }
                    else{ $sname = $sector_name[$row['to_flock']]; }

                    echo '<tr>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:center;">'.$slno.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['trnum'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['dcno'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$sname.'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$row['code'].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:left;">'.$medvac_name[$row['code']].'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['quantity'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['price'],2).'</td>';
                    echo '<td colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($row['amount'],2).'</td>';
                    if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                    echo '</tr>';
                    $tmv_oqty += (float)$row['quantity'];
                    $tmv_oamt += (float)$row['amount'];
                }

                echo '</tbody>';
                echo '<tr class="thead3">';
                echo '<th colspan="'.($ccnt1 - 9).'" style="text-align:left;">Total</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmv_oqty,2).'</th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;"></th>';
                echo '<th colspan="'.$ccnt3.'" style="text-align:right;">'.decimal_adjustments($tmv_oamt,2).'</th>';
                if($egg_pflag == 1 && $e_cnt > 0){ echo '<th colspan="'.$e_cnt.'"></th>'; }
                echo '</tr>';
            }
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
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                }
                else{ }
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
                var url = "breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&flocks="+flocks+"&fetch_type=single&flock_type=select";
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