<?php
//broiler_liveflocksummary_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
//if(!isset($_SESSION)){ session_start(); }
session_start();
if(!empty($_GET['db']) && $_GET['db'] != ""){ $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db']; }
//$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_liveflocksummary_masterreport.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_liveflocksummary_masterreport.php?db=$db&userid=".$user_code;
}

$file_name = "Live Batch Summary";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_link_itembrand", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_link_itembrand LIKE poulso6_admin_broiler_broilermaster.broiler_link_itembrand;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_item_brands", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_item_brands LIKE poulso6_admin_broiler_broilermaster.broiler_item_brands;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `broiler_link_itembrand` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $ibrand_code = array();
while($row = mysqli_fetch_assoc($query)){ $ibrand_code[$row['item_code']] = $row['brand_code']; }

$sql = "SELECT * FROM `broiler_item_brands` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $ibrand_name = array();
while($row = mysqli_fetch_assoc($query)){ $ibrand_name[$row['code']] = $row['description']; }

$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial, sans-serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica, Arial, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Verdana, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Tahoma, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Trebuchet MS";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Trebuchet MS', sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman'";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman', serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Georgia, serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Garamond, serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Courier New', monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Courier, monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Optima";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Segoe";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Calibri";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Candara";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Grande";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Sans Unicode";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Gill Sans";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Source Sans Pro', 'Arial', sans-serif";

for($i = 0;$i <= 30;$i++){ $fsizes[$i."px"] = $i."px"; }

$i = 0;

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); 
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = ""; $slno_flag = 0;
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] != 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;

                if($cna == "sl_no"){ $slno_flag = 1; }
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] != 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}

$tblcol_size = sizeof($act_col_numbs);
$region_code = $region_name = $branch_code = $branch_name = $line_code = $line_name = $line_branch = $farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = 
$farm_supervisor = $farm_svr = $farm_farmer = $batch_code = $batch_name = $batch_book = $batch_gcflag = $batch_farm = $supervisor_code = $supervisor_name = 
$supervisor_name = $std_body_weight = $std_daily_gain = $std_avg_daily_gain = $std_fcr = $std_feed_consumed = $std_cum_feed = $gc_code = $chick_cost = $feed_cost = 
$medicine_cost = $med_price = $admin_cost = $standard_prod_cost = $standard_cost = $minimum_cost = $standard_fcr = $standard_mortality = array();

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'".$branch_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1'".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code']; $farm_farmer[$row['code']] = $row['farmer_code'];
}
$farm_list = implode("','", $farm_code);
$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `farm_code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; $batch_farm[$row['code']] = $row['farm_code']; }

$emp_list = implode("','", $farm_supervisor);
$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' AND `active` = '1' AND `code` IN ('$emp_list')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }


$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `active` = '1' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $std_age[$row['age']] = $row['age'];
    $std_body_weight[$row['age']] = $row['body_weight'];
    $std_daily_gain[$row['age']] = $row['daily_gain'];
    $std_avg_daily_gain[$row['age']] = $row['avg_daily_gain'];
    $std_fcr[$row['age']] = $row['fcr'];
    $std_feed_consumed[$row['age']] = $row['feed_consumed'];
    $std_cum_feed[$row['age']] = $row['cum_feed'];
}
$sql1 = "SELECT MAX(from_date) as from_date,region_code,branch_code FROM `broiler_gc_standard` WHERE `active` = '1' AND `dflag` = '0' GROUP BY `region_code`,`branch_code` ORDER BY `region_code`,`branch_code` ASC"; $query1 = mysqli_query($conn,$sql1);
while($row1 = mysqli_fetch_assoc($query1)){
    $rgcode = $row1['region_code'];
    $brcode = $row1['branch_code'];
    $fdcode = $row1['from_date'];
    $sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$rgcode' AND `branch_code` = '$brcode'AND `active` = '1' AND `dflag` = '0' AND `from_date` IN ('$fdcode')"; $query = mysqli_query($conn,$sql); // AND `from_date` >= '$start_date' AND `to_date` >= '$end_date' 
    while($row = mysqli_fetch_assoc($query)){
        $key_code = $row['branch_code'];
        $gc_code[$key_code] = $row['code'];
        $chick_cost[$key_code] = $row['chick_cost'];
        $feed_cost[$key_code] = $row['feed_cost'];
        $medicine_cost[$key_code] = $row['medicine_cost'];
        $med_price[$key_code] = $row['med_price'];
        $admin_cost[$key_code] = $row['admin_cost'];
        $standard_prod_cost[$key_code] = $row['standard_prod_cost'];
        $standard_cost[$key_code] = $row['standard_cost'];
        $minimum_cost[$key_code] = $row['minimum_cost'];
        $standard_fcr[$key_code] = $row['standard_fcr'];
        $standard_mortality[$key_code] = $row['standard_mortality'];
    }
}
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Live Flock Summary') AND `field_function` LIKE 'Display Day Record Age' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $day_entryage_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Live Flock Summary') AND `field_function` LIKE 'Next Days Manual Entry' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $day_entryfeed_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Live Flock Summary') AND `field_function` LIKE 'Add Bird Sending to Bird Sales Count' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bsend_entrytbsale_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }
$fdate = $tdate = $today = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display"; $abirds = $max_age = $min_age = "";
$font_stype = ""; $font_size = "11px";$mort_above = 0;$age_above = ""; $manual_nxtfeed = 3;
if(isset($_REQUEST['submit_report']) == true){
    $branches = $_REQUEST['branches'];
    $lines = $_REQUEST['lines'];
    $farms = $_REQUEST['farms'];
    $regions = $_REQUEST['regions'];
    $supervisors = $_REQUEST['supervisors'];
    $abirds = $_REQUEST['abirds'];
    if($_SERVER['REMOTE_ADDR'] == "49.205.135.183"){
        $min_age = $_REQUEST['min_age'];
        $max_age = $_REQUEST['max_age'];
    }

    $font_stype = $_REQUEST['font_stype'];
    $font_size = $_REQUEST['font_size'];
    $age_above = $_REQUEST['age_above'];
    $mort_above = $_REQUEST['mort_above'];
    if($day_entryfeed_flag == 1){ $manual_nxtfeed = $_REQUEST['manual_nxtfeed']; }

    $farm_query = "";
    if($regions != "all"){ $farm_query .= " AND `region_code` LIKE '$regions'"; }
    if($branches != "all"){ $farm_query .= " AND `branch_code` LIKE '$branches'"; }
    if($lines != "all"){ $farm_query .= " AND `line_code` LIKE '$lines'"; }
    if($supervisors != "all"){ $farm_query .= " AND `supervisor_code` LIKE '$supervisors'"; }
    if($farms != "all"){ $farm_query .= " AND `code` LIKE '$farms'"; }
    $farm_list = ""; $farm_list = implode("','", $farm_code);

    $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1' ".$farm_query." AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $farm_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }
    
    $farm_list = implode("','",$farm_alist);
    $farm_query = $farm_query2 = "";
    $farm_query = " AND a.farm_code IN ('$farm_list')";
    $farm_query2 = " AND farm_code IN ('$farm_list')";
	$excel_type = $_REQUEST['export'];
	//$url = "../PHPExcel/Examples/broiler_liveflocksummary_masterreport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors."&farm=".$farms."&abirds=".$abirds."&age_above=".$age_above."&href=".$field_href[0];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php if($excel_type == "print"){ include "headerstyle_wprint_font.php"; } else{ include "headerstyle_woprint_font.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="<?php echo $tblcol_size; ?>">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Region</label>
                                    <select name="regions" id="regions" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($regions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                
                              <div class="m-2 form-group">
                                    <label>Available Birds</label>
                                    <input type="text" name="abirds" id="abirds" class="form-control" value="<?php echo $abirds; ?>" style="width:90px;" />
                              </div>
                                
                                <div class="m-2 form-group">
                                    <label>Above Age</label>
                                    <input type="text" name="age_above" id="age_above" class="form-control" value="<?php echo $age_above; ?>" style="width:90px;" />
                                </div>
                                <?php
                                if($_SERVER['REMOTE_ADDR'] == "49.205.135.183"){
                                ?>
                                <div class="m-2 form-group">
                                    <label>Min Age</label>
                                    <input type="text" name="min_age" id="min_age" class="form-control" value="<?php echo $min_age; ?>" style="width:90px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Max Age</label>
                                    <input type="text" name="max_age" id="max_age" class="form-control" value="<?php echo $max_age; ?>" style="width:90px;" />
                                </div>
                                <?php
                                }
                                ?>
                                <div class="m-2 form-group">
                                    <label>Font Style</label>
                                    <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                        <option value="" <?php if($font_stype == ""){ echo "selected"; } ?>>-Defalut-</option>
                                        <?php
                                        foreach($font_family_code as $i){
                                        ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if($font_stype == $font_family_name[$i]){ echo "selected"; } ?>><?php echo $font_family_name[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Size</label>
                                    <select name="font_size" id="font_size" class="form-control select2">
                                        <?php
                                        foreach($fsizes as $i){
                                        ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if($font_size == $fsizes[$i]){ echo "selected"; } ?>><?php echo $fsizes[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Above Mort</label>
                                    <input type="text" name="mort_above" id="mort_above" class="form-control" value="<?php echo $mort_above; ?>" style="width:90px;" />
                                </div>
                                <?php
                                if($day_entryfeed_flag == 1){
                                ?>
                                <div class="m-2 form-group">
                                    <label>Next feed Days</label>
                                    <input type="text" name="manual_nxtfeed" id="manual_nxtfeed" class="form-control" value="<?php echo $manual_nxtfeed; ?>" style="width:120px;" />
                                </div>
                                <?php
                                }
                                ?>
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
                            </div>                        </th>
                    </tr>
                </thead>
            </form>
            <?php if($excel_type == "print"){ } else{ ?>
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td colspan="<?php echo $tblcol_size; ?>">
                <div id='control_sh'>
                    <?php
                        //for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; if(!empty($act_col_numbs[$key_id])){ echo "<br/>".$act_col_numbs[$key_id]."@".$key_id; } else if(!empty($nac_col_numbs[$key_id1])){ echo "<br/>".$nac_col_numbs[$key_id1]."@".$key_id1; } else{ } }
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($act_col_numbs[$key_id]) || !empty($nac_col_numbs[$key_id1])){
                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "branch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "line_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Line</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "batch_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch Code</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "batch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "book_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Book No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "brood_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_act_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "brood_act_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_act_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "placement_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="placement_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Placement Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_start_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "lifting_start_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="lifting_start_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lifting Start Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mean_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mean_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mean Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "liquidation_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="liquidation_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Latest Entry Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gap_days" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gap_days"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gap_days" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gap Days</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_placed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_placed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Housed Chicks</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "culls_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="culls_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cull</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "culls_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="culls_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cull%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birdswt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdswt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold Weight</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "available_birds"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="available_birds" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Available Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std B.Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "avg_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="avg_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg B.Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "cfcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cfcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>CFCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "eef"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="eef" onclick="update_masterreport_status(this.id);" '.$checked.'><span>EEF</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mgmt_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mgmt_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>M PC/Kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedin_sector_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedin_sector_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Transferred</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedin_farm_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedin_farm_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transfer In From Other Farms</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_feed_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Feed Con</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_feed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Con</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedconsumed_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedconsumed_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Con Bags</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_feed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Actual Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno2" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_feed_perbirdno2"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_perbirdno2" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Actual Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedout_farms_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedout_farms_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transfer Out to Farms</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_balance_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_balance_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Balance</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_days" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_balance_days"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_balance_days" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Balance Days</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "next_3days_feed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "next_3days_feed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="next_3days_feed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Next '.$manual_nxtfeed.' Days Feed</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birds_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birds_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birds_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold %</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_received_from"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_received_from" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Hatchery Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedin_sector_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedin_sector_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Transferred Bags</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedin_farm_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedin_farm_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transfer In From Other Farms Bags</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedout_farms_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedout_farms_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transfer Out to Farms Bags</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_balance_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_balance_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Balance Bags</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "latest_feedin_brand"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="latest_feedin_brand" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Latest Feed-In Brand</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "today_mort_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "today_mort_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="today_mort_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Today Mortality</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chickin_hatchery_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chickin_hatchery_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Hatchery</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chickin_supplier_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chickin_supplier_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier</span>'; }
                            else{ }
                            }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table id="main_table" class="tbl" align="center">
        <?php } ?>
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                <?php
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th id='order_num'>Sl.No.</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<th id='order'>Branch</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<th id='order'>Line</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th id='order'>Supervisor</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<th id='order'>Farmer</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code"){ echo "<th id='order'>Batch Code</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<th id='order'>Batch</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<th id='order'>Book No</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<th id='order_num'>Age</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_act_age"){ echo "<th id='order_num'>Age</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date"){ echo "<th id='order_date'>Placement Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_start_date"){ echo "<th id='order_date'>Lifting Start Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<th id='order_num'>Mean Age</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date"){ echo "<th id='order_date'>Latest Entry Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gap_days"){ echo "<th id='order_num'>Gap Days</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th id='order_num'>Housed Chicks</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th id='order_num'>Mort</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<th id='order_num'>Mort%</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ echo "<th id='order_num'>Cull</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_per"){ echo "<th id='order_num'>Cull%</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th id='order_num'>Sold Birds</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th id='order_num'>Sold Weight</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ echo "<th id='order_num'>Available Birds</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ echo "<th id='order_num'>Std B.Wt</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th id='order_num'>Avg B.Wt</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<th id='order_num'>Std FCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<th id='order_num'>FCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<th id='order_num'>CFCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<th id='order_num'>EEF</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){ echo "<th id='order_num'>M PC/Kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_count"){ echo "<th id='order_num'>Feed Transferred</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_count"){ echo "<th id='order_num'>Transfer In From Other Farms</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<th id='order_num'>Std Feed Con</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){ echo "<th id='order_num'>Std Feed/Bird</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th id='order_num'>Feed Con</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags"){ echo "<th id='order_num'>Feed Con Bags</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){ echo "<th id='order_num'>Actual Feed/Bird</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno2"){ echo "<th id='order_num'>Actual Feed/Bird</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_count"){ echo "<th id='order_num'>Transfer Out to Farms</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ echo "<th id='order_num'>Feed Balance</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_days"){ echo "<th id='order_num'>Feed Balance Days</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "next_3days_feed"){ echo "<th id='order_num'>Next ".$manual_nxtfeed." Days Feed</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birds_per"){ echo "<th id='order_num'>Sold %</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ echo "<th id='order'>Hatchery Name</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_bags"){ echo "<th id='order_num'>Feed Transferred Bags</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_bags"){ echo "<th id='order_num'>Transfer In From Other Farms Bags</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_bags"){ echo "<th id='order_num'>Transfer Out to Farms Bags</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_bags"){ echo "<th id='order_num'>Feed Balance Bags</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand"){ echo "<th id='order'>Latest Feed-In Brand</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "today_mort_count"){ echo "<th id='order'>Today Mortality</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<th id='order'>Hatchery</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<th id='order'>Supplier</th>"; }
                    else{ }
                }
                ?>
                </tr>
            </thead>
            
            <?php
            if(isset($_REQUEST['submit_report']) == true || isset($_GET['submit']) == true){
            ?>
            <tbody class="tbody1" id = "tbody1">
                <?php
                $batch_all = $batch1 = $batch2 = $batch3 = "";
                $pur_qty = $sold_birds = $sale_qty = $sector_trin_qty = $farm_trin_qty = $farm_trout_qty = $sector_trout_qty = $medvac_qty = $dentry_mort = $dentry_feed = $dentry_age = $davg_wt = $dentry_semp = array();
                $chick_placed_date = array();

                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $chick_codes[$row['code']] = $row['code']; $chick_cat = $row['category']; }
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $bird_codes[$row['code']] = $row['code']; }

                $sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql); $chick_iac = array();
                while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; }
                

                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = ""; $feed_code = $feed_coa = array();
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } $feed_coa[$row['iac']] = $row['iac']; }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }

                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
            
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
            
                $batch_sql = "SELECT * FROM `broiler_batch` WHERE gc_flag = '0' AND active = '1' AND dflag = '0'"; $batch_query = mysqli_query($conn,$batch_sql);
                while($row = mysqli_fetch_assoc($batch_query)){ if($batch_all == ""){ $batch_all = $row['code']; } else{ $batch_all = $batch_all."','".$row['code']; } }
                
                $batch_list = array();
                //$batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code AND a.gc_flag = '0' AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY age DESC, farm_name ASC";
                $batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age,MIN(c.date) as min_entrydate FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code AND a.gc_flag = '0' AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY `min_entrydate`  ASC";
                $batch_query = mysqli_query($conn,$batch_sql);
                 $i = 0;
                while($batch_row = mysqli_fetch_assoc($batch_query)){
                    $i++; 
                    $batch_list[$i] = $batch_row['batch_code'];
                    $batch_age[$batch_row['batch_code']] = $batch_row['age']; 
                    $batch_farm[$batch_row['batch_code']] = $batch_row['farm_code'];
                    if($batch1 == ""){ $batch1 = $batch_row['batch_code']; } else{ $batch1 = $batch1."','".$batch_row['batch_code']; }
                }
                $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `code` NOT IN ('$batch1') AND `dflag` = '0' $farm_query2 GROUP BY `code` ORDER BY `code` DESC"; 
                //$sql = "SELECT a.code as code,a.farm_code as farm_code,b.description as farm_name FROM broiler_batch a,broiler_farm b,broiler_daily_record c  WHERE a.gc_flag = '0' AND a.farm_code = b.code AND a.code NOT IN ('$batch1')  $farm_query GROUP BY b.code ORDER BY c.brood_age DESC"; 
                
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $i++; $batch_list[$i] = $row['code']; $batch_age[$row['code']] = 0; $batch_farm[$row['code']] = $row['farm_code']; }
                
                $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ if($batch3 == ""){ $batch3 = $row['code']; } else{ $batch3 = $batch3."','".$row['code']; } }

                foreach($batch_list as $batches){ $batches; if($batch2 == ""){ $batch2 = $batches; } else{ $batch2 = $batch2."','".$batches; } }
                $start_date = $end_date = array();

                /*Latest Feed-In Details*/
                $fcoa_list = implode("','",$feed_coa);
                $feed_list = implode("','",$feed_code);
                $sql = "SELECT * FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` IN ('$fcoa_list') AND `item_code` IN ('$feed_list') AND `batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch`,`date` DESC";
                $query = mysqli_query($conn,$sql); $blentry_date = $blentry_items = array();
                while($row = mysqli_fetch_assoc($query)){
                    if(empty($blentry_date[$row['batch']])){
                        $blentry_date[$row['batch']] = $row['date'];
                        $blentry_items[$row['batch']] = $ibrand_name[$ibrand_code[$row['item_code']]];
                    }
                    else if(strtotime($blentry_date[$row['batch']]) < strtotime($row['date'])){
                        $blentry_date[$row['batch']] = $row['date'];
                        $blentry_items[$row['batch']] = $ibrand_name[$ibrand_code[$row['item_code']]];
                    }
                }

                //Fetch Hatchery and Supplier Details-1
                $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `gc_flag` = '0' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $gbch_code = array();
                while($row = mysqli_fetch_assoc($query)){ $gbch_code[$row['code']] = $row['code']; }
                $batch_hlist = implode("','",$gbch_code);

                $chick_coa = $icat_iac[$chick_cat];
                $sql = "SELECT MIN(`date`) as `sdate`,MAX(`date`) as `edate` FROM `account_summary` WHERE `crdr` LIKE 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$batch_hlist') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $hsdate = $hedate = "";
                while($row = mysqli_fetch_assoc($query)){ $hsdate = $row['sdate']; $hedate = $row['edate']; }

                $hatch_count = $pur_count = 0; $chkin_hcode = $chkin_vcode = array();
                if($hsdate == "" && $hedate == ""){ }
                else{
                    $hfdate = date("Y-m-d",strtotime($hsdate. '-3 days'));
                    $sector_list = implode("','",$sector_code);
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$hfdate' AND `date` <= '$hedate' AND `icode` = '$chick_code' AND `warehouse` IN ('$sector_list') AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql); $pur_vcode =  $pur_keyset = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['date']."@".$row['warehouse']."@".$i;
                        $pur_vcode[$key_code] = $row['vcode'];
                        $pur_keyset[$key_code] = $key_code;
                        $i++;
                    } $pur_count = sizeof($pur_vcode);

                    $sql_record = "SELECT * FROM `broiler_hatchentry` WHERE `hatch_date` >= '$hfdate' AND `hatch_date` <= '$hedate' AND `active` = '1' AND `dflag` = '0' ORDER BY `hatch_date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $hatch_vcode =  $hatch_keyset = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['hatch_date']."@".$row['sector_code']."@".$i;
                        $hatch_vcode[$key_code] = $row['vcode'];
                        $hatch_keyset[$key_code] = $key_code;
                        $i++;
                    } $hatch_count = sizeof($hatch_vcode);
                }
                $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `to_batch` IN ('$batch_hlist') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $chkin_hcode[$row['to_batch']] = $row['fromwarehouse'];
                    //Fetch Hatchery and Supplier Details-2
                    $ldate = $lsector = $lincr = "";
                    if($hatch_count > 0 && $row['code'] == $chick_code){
                        foreach($hatch_keyset as $key1){
                            $key2 = explode("@",$key1); $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                            if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                if($ldate == ""){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                else if(strtotime($ldate) < strtotime($hdate)){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                            }
                        }
                        if($ldate == "" && $lsector == "" && $lincr == ""){ }
                        else{
                            $hkey = $ldate."@".$lsector."@".$lincr;
                            if(empty($hatch_vcode[$hkey]) || $hatch_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                            else{ $chkin_vcode[$row['to_batch']] = $hatch_vcode[$hkey]; }
                        }
                    }

                    if(empty($chkin_vcode[$row['to_batch']]) || $chkin_vcode[$row['to_batch']] == ""){
                        if($pur_count > 0 && $row['code'] == $chick_code){
                            $ldate = $lsector = $lincr = "";
                            foreach($pur_keyset as $key1){
                                $key2 = explode("@",$key1); $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                    if($ldate == ""){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                    else if(strtotime($ldate) < strtotime($hdate)){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                }
                            }
                            if($ldate == "" && $lsector == "" && $lincr == ""){ }
                            else{
                                $hkey = $ldate."@".$lsector."@".$lincr;
                                if(empty($pur_vcode[$hkey]) || $pur_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                else{ $chkin_vcode[$row['to_batch']] = $pur_vcode[$hkey]; }
                            }
                        }
                    }
                }

                //Purchases
                $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as item_tamt,MIN(date) as sdate,MAX(date) as edate,icode,farm_batch,warehouse,vcode FROM `broiler_purchases` WHERE `farm_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $cin_sup_code = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['farm_batch']."@".$row['icode'];
                    if(!empty($chick_codes[$row['icode']])){
                        $pur_chick_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $pur_chick_amt[$key_code] = $row['item_tamt'];
                        $cin_sup_code[$row['farm_batch']] = $row['vcode'];
                        $chkin_vcode[$row['farm_batch']] = $row['vocde'];
                        if(empty($chick_placed_date[$row['farm_batch']])){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['farm_batch']]){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['icode']])){
                        $pur_feed_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $pur_feed_amt[$key_code] = $row['item_tamt'];

                        //echo "<br/>".$row['warehouse']."@".$row['farm_batch']."@".$row['icode']."@".$row['rcd_qty']."@".$row['fre_qty'];
                    }
                    else if(!empty($medvac_code[$row['icode']])){
                        $pur_medvac_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $pur_medvac_amt[$key_code] = $row['item_tamt'];
                    }
                    else{
                        $pur_other_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $pur_other_amt[$key_code] = $row['item_tamt'];
                    }
                    if(empty($start_date[$row['farm_batch']])){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['farm_batch']]){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['farm_batch']])){ $end_date[$row['farm_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['farm_batch']]){ $end_date[$row['farm_batch']] = strtotime($row['edate']); } }
                    $pur_feedin_date[$row['farm_batch']] = $row['sdate'];

                }
                /*Bird Sending
                $sale_bird_nos = array();
                if((int)$bsend_entrytbsale_flag == 1){
                    $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `item_code` = '$bird_code' AND `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['from_batch']."@".$row['item_code'];
                        $sale_bird_nos[$key_code] += (float)$row['birds'];
                    }
                }*/

                //Sales
                $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as item_tamt,MIN(date) as sdate,MAX(date) as edate,icode,farm_batch FROM `broiler_sales` WHERE `farm_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['farm_batch']."@".$row['icode'];
                    if(!empty($chick_codes[$row['icode']]) || !empty($bird_codes[$row['icode']])){
                        $sale_bird_nos[$key_code] += (float)$row['birds'];
                        $sale_bird_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        if(empty($sale_start_date[$key_code])){ $sale_start_date[$key_code] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $sale_start_date[$key_code]){ $sale_start_date[$key_code] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['icode']])){
                        $sale_feed_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    else if(!empty($medvac_code[$row['icode']])){
                        $sale_medvac_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    else{
                        $sale_other_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    if(empty($start_date[$row['farm_batch']])){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['farm_batch']]){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['farm_batch']])){ $end_date[$row['farm_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['farm_batch']]){ $end_date[$row['farm_batch']] = strtotime($row['edate']); } }
                }
                $sql = "SELECT * FROM `broiler_sales` WHERE `farm_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['date']."@".$row['icode']."@".$row['farm_batch'];
                    if(empty($sal_birds[$key])){ $sal_birds[$key] = $row['birds'];}
                    else{ $sal_birds[$key] = $sal_birds[$key] + $row['birds']; }
                    
                }
                //In-House Processing
                $sql_record = "SELECT SUM(birds) as birds,SUM(`weight`) as rcd_qty,SUM(avg_amount) as item_tamt,MIN(date) as sdate,MAX(date) as edate,item_code,from_batch FROM `broiler_bird_transferout` WHERE `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch`,`item_code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['from_batch']."@".$row['item_code'];
                    if(!empty($chick_codes[$row['item_code']]) || !empty($bird_codes[$row['item_code']])){
                        $sale_bird_nos[$key_code] += (float)$row['birds'];
                        $sale_bird_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        if(empty($sale_start_date[$key_code])){ $sale_start_date[$key_code] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $sale_start_date[$key_code]){ $sale_start_date[$key_code] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['item_code']])){
                        $sale_feed_qty[$key_code] = $row['rcd_qty'];
                    }
                    else if(!empty($medvac_code[$row['item_code']])){
                        $sale_medvac_qty[$key_code] = $row['rcd_qty'];
                    }
                    else{
                        $sale_other_qty[$key_code] = $row['rcd_qty'];
                    }
                    if(empty($start_date[$row['from_batch']])){ $start_date[$row['from_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['from_batch']]){ $start_date[$row['from_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['from_batch']])){ $end_date[$row['from_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['from_batch']]){ $end_date[$row['from_batch']] = strtotime($row['edate']); } }
                }
                $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['date']."@".$row['item_code']."@".$row['from_batch'];
                    if(empty($sal_birds[$key])){ $sal_birds[$key] = $row['birds'];}
                    else{ $sal_birds[$key] = $sal_birds[$key] + $row['birds']; }
                    
                }
                //Transfer IN From Farm to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,to_batch,fromwarehouse FROM `item_stocktransfers` WHERE `from_batch` IN ('$batch3') AND `to_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['to_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $farm_trin_bird_qty[$key_code] = $row['quantity'];
                        $farm_trin_bird_amt[$key_code] = $row['amount'];
                        $cin_sup_code[$row['to_batch']] = $row['fromwarehouse'];
                        if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $farm_trin_feed_qty[$key_code] = $row['quantity'];
                        $farm_trin_feed_amt[$key_code] = $row['amount'];
                        $farm_trin_date[$row['to_batch']] = $row['sdate'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $farm_trin_medvac_qty[$key_code] = $row['quantity'];
                        $farm_trin_medvac_amt[$key_code] = $row['amount'];
                    }
                    else{
                        $farm_trin_other_qty[$key_code] = $row['quantity'];
                        $farm_trin_other_amt[$key_code] = $row['amount'];
                    }
                    if(empty($start_date[$row['to_batch']])){ $start_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['to_batch']]){ $start_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['to_batch']])){ $end_date[$row['to_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['to_batch']]){ $end_date[$row['to_batch']] = strtotime($row['edate']); } }
                }
                //Transfer IN From Warehouse to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,to_batch FROM `item_stocktransfers` WHERE `from_batch` NOT IN ('$batch3') AND `to_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['to_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $sector_trin_bird_qty[$key_code] = $row['quantity'];
                        $sector_trin_bird_amt[$key_code] = $row['amount'];
                        if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $sector_trin_feed_qty[$key_code] = $row['quantity'];
                        $sector_trin_feed_amt[$key_code] = $row['amount'];
                        $sector_trin_date[$row['to_batch']] = $row['sdate'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $sector_trin_medvac_qty[$key_code] = $row['quantity'];
                        $sector_trin_medvac_amt[$key_code] = $row['amount'];
                    }
                    else{
                        $sector_trin_other_qty[$key_code] = $row['quantity'];
                        $sector_trin_other_amt[$key_code] = $row['amount'];
                    }
                    if(empty($start_date[$row['to_batch']])){ $start_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['to_batch']]){ $start_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['to_batch']])){ $end_date[$row['to_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['to_batch']]){ $end_date[$row['to_batch']] = strtotime($row['edate']); } }
                }
                //Transfer OUT From Farm to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,from_batch FROM `item_stocktransfers` WHERE `to_batch` IN ('$batch3') AND `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['from_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $farm_trout_bird_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $farm_trout_feed_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $farm_trout_medvac_qty[$key_code] = $row['quantity'];
                    }
                    else{
                        $farm_trout_other_qty[$key_code] = $row['quantity'];
                    }
                    if(empty($start_date[$row['from_batch']])){ $start_date[$row['from_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['from_batch']]){ $start_date[$row['from_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['from_batch']])){ $end_date[$row['from_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['from_batch']]){ $end_date[$row['from_batch']] = strtotime($row['edate']); } }
                }
                //Transfer OUT From Warehouse to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,from_batch FROM `item_stocktransfers` WHERE `to_batch` NOT IN ('$batch3') AND `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['from_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $sector_trout_bird_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $sector_trout_feed_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $sector_trout_medvac_qty[$key_code] = $row['quantity'];
                    }
                    else{
                        $sector_trout_other_qty[$key_code] = $row['quantity'];
                    }
                    if(empty($start_date[$row['from_batch']])){ $start_date[$row['from_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['from_batch']]){ $start_date[$row['from_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['from_batch']])){ $end_date[$row['from_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['from_batch']]){ $end_date[$row['from_batch']] = strtotime($row['edate']); } }
                }
                //Day record
                $sql_record = "SELECT SUM(mortality) as mortality,SUM(culls) as culls,SUM(kgs1) as kgs1,SUM(kgs2) as kgs2,MIN(date) as sdate,MAX(date) as edate,MAX(brood_age) as brood_age,batch_code,supervisor_code FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY brood_age DESC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['batch_code'];
                    $dentry_mort[$key_code] = $row['mortality'];
                    $dentry_cull[$key_code] = $row['culls'];
                    $dentry_feed[$key_code] = $row['kgs1'] + $row['kgs2'];
                    $dentry_min_date[$key_code] = $row['sdate'];
                    $dentry_max_date[$key_code] = $row['edate'];
                    $dentry_max_age[$key_code] = $row['brood_age'];
                    $dentry_semp[$key_code] = $row['supervisor_code'];
                    if(empty($start_date[$row['batch_code']])){ $start_date[$row['batch_code']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['batch_code']]){ $start_date[$row['batch_code']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['batch_code']])){ $end_date[$row['batch_code']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['batch_code']]){ $end_date[$row['batch_code']] = strtotime($row['edate']); } }
                }
                $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY brood_age DESC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['batch_code'];
                    if(empty($dentry_con[$key_code])){ $dentry_con[$key_code] = $row['kgs1'] + $row['kgs2']; }
                    //if($dentry_con[$key_code] == "" || number_format_ind($dentry_con[$key_code]) == "0.00"){ $dentry_con[$key_code] = $row['kgs1'] + $row['kgs2']; }
                }
                $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND  `avg_wt` > '0' AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC)";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['batch_code'];
                    $dentry_age[$key_code] = $row['brood_age'];
                    $davg_wt[$key_code] = $row['avg_wt'];
                }
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY brood_age DESC";
                $query = mysqli_query($conn,$sql); $cur_date = $cdate_mort = $tdate_awht = array();
                while($row = mysqli_fetch_assoc($query)){
                    $day_ages[$row['date']."@".$row['batch_code']] = $row['brood_age'];
                    $key = $row['date'];
                    if(strtotime($today) == strtotime($row['date'])){
                        $cdate_mort[$row['batch_code']] = ((float)$row['mortality'] + (float)$row['culls']);
                        $tdate_awht[$row['batch_code']] = $row['avg_wt'];
                    }
                }
                //Medicine Record
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(batch_code) as batch_code,MIN(date) as sdate,MAX(date) as edate FROM `broiler_medicine_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['batch_code'];
                    $medvac_qty[$key_code] = $row['quantity'];
                    if(empty($start_date[$row['batch_code']])){ $start_date[$row['batch_code']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['batch_code']]){ $start_date[$row['batch_code']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['batch_code']])){ $end_date[$row['batch_code']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['batch_code']]){ $end_date[$row['batch_code']] = strtotime($row['edate']); } }
                }

                $slno = $total_housed_chicks = $total_mort_chicks = $total_cull_chicks = $total_sold_chicks = $total_sold_weight = $total_aval_chicks = $total_short_chicks = 
                $total_exccess_chicks = $total_feedin_chicks = $total_feedin_farm_chicks = $total_feed_consumed_chicks = $total_feedout_farm_chicks = $total_feed_bal_chicks = 
                $total_next_3_days_feed = $tcur_mort = 0;
                
                //Display section
                //echo implode(",",$batch_list);
                foreach($batch_list as $batches){
                    $brood_age = $batch_age[$batches];
                    $fetch_fcode = $batch_farm[$batches];
                    if($batches != ""){
                        //Chick or Bird Transactions
                        $purchase_chicks = $farm_transferin_chicks = $sector_transferin_chicks = $mortality_chicks = $chicks_cull = 0;
                        $sales_birds_nos = $sales_birds_qty = $farm_transferout_birds = $sector_transferout_birds = 0;
                        $purtrin_chick_amt = 0;
                        //Purchase or Transfer in
                        if(!empty($pur_chick_qty[$batches."@".$chick_code])){
                            $purchase_chicks = $purchase_chicks + $pur_chick_qty[$batches."@".$chick_code];
                            $purtrin_chick_amt = $purtrin_chick_amt + $pur_chick_amt[$batches."@".$chick_code];
                        }
                        if(!empty($pur_chick_qty[$batches."@".$bird_code])){
                            $purchase_chicks = $purchase_chicks + $pur_chick_qty[$batches."@".$bird_code];
                            $purtrin_chick_amt = $purtrin_chick_amt + $pur_chick_amt[$batches."@".$bird_code];
                        }
                        if(!empty($farm_trin_bird_qty[$batches."@".$chick_code])){
                            $farm_transferin_chicks = $farm_transferin_chicks + $farm_trin_bird_qty[$batches."@".$chick_code];
                            $purtrin_chick_amt = $purtrin_chick_amt + $farm_trin_bird_amt[$batches."@".$chick_code];
                        }
                        if(!empty($farm_trin_bird_qty[$batches."@".$bird_code])){
                            $farm_transferin_chicks = $farm_transferin_chicks + $farm_trin_bird_qty[$batches."@".$bird_code];
                            $purtrin_chick_amt = $purtrin_chick_amt + $farm_trin_bird_amt[$batches."@".$bird_code];
                        }
                        if(!empty($sector_trin_bird_qty[$batches."@".$chick_code])){
                            $sector_transferin_chicks = $sector_transferin_chicks + $sector_trin_bird_qty[$batches."@".$chick_code];
                            $purtrin_chick_amt = $purtrin_chick_amt + $sector_trin_bird_amt[$batches."@".$chick_code];
                        }
                        if(!empty($sector_trin_bird_qty[$batches."@".$bird_code])){
                            $sector_transferin_chicks = $sector_transferin_chicks + $sector_trin_bird_qty[$batches."@".$bird_code];
                            $purtrin_chick_amt = $purtrin_chick_amt + $sector_trin_bird_amt[$batches."@".$bird_code];
                        }
                        //Mortality
                        if(!empty($dentry_mort[$batches])){
                            $mortality_chicks = $mortality_chicks + $dentry_mort[$batches];
                        }
                        //Culls
                        if(!empty($dentry_cull[$batches])){
                            $chicks_cull = $chicks_cull + $dentry_cull[$batches];
                        }
                        //Sale or Transfer Out
                        if(!empty($sale_bird_nos[$batches."@".$chick_code])){
                            $sales_birds_nos = $sales_birds_nos + $sale_bird_nos[$batches."@".$chick_code];
                            //echo "<br/>$sales_birds_nos";
                        }
                        if(!empty($sale_bird_nos[$batches."@".$bird_code])){
                            $sales_birds_nos = $sales_birds_nos + $sale_bird_nos[$batches."@".$bird_code];
                            //echo "<br/>$sales_birds_nos";
                        }
                        if(!empty($sale_bird_qty[$batches."@".$chick_code])){
                            $sales_birds_qty = $sales_birds_qty + $sale_bird_qty[$batches."@".$chick_code];
                        }
                        if(!empty($sale_bird_qty[$batches."@".$bird_code])){
                            $sales_birds_qty = $sales_birds_qty + $sale_bird_qty[$batches."@".$bird_code];
                        }
                        if(!empty($farm_trout_bird_qty[$batches."@".$chick_code])){
                            $farm_transferout_birds = $farm_transferout_birds + $farm_trout_bird_qty[$batches."@".$chick_code];
                        }
                        if(!empty($farm_trout_bird_qty[$batches."@".$bird_code])){
                            $farm_transferout_birds = $farm_transferout_birds + $farm_trout_bird_qty[$batches."@".$bird_code];
                        }
                        if(!empty($sector_trout_bird_qty[$batches."@".$chick_code])){
                            $sector_transferout_birds = $sector_transferout_birds + $sector_trout_bird_qty[$batches."@".$chick_code];
                        }
                        if(!empty($sector_trout_bird_qty[$batches."@".$bird_code])){
                            $sector_transferout_birds = $sector_transferout_birds + $sector_trout_bird_qty[$batches."@".$bird_code];
                        }
                        //Feed Transactions
                        $purchase_feeds = $farm_transferin_feeds = $sector_transferin_feeds = $consumed_feeds = $sales_feeds = $farm_transferout_feeds = $sector_transferout_feeds = 0;
                        $purtrin_feed_amt = 0;
                        foreach($feed_code as $fcode){
                            //Purchase or Transfer in
                            if(!empty($pur_feed_qty[$batches."@".$fcode])){
                                $purchase_feeds = $purchase_feeds + $pur_feed_qty[$batches."@".$fcode];
                                $purtrin_feed_amt = $purtrin_feed_amt + $pur_feed_amt[$batches."@".$fcode];
                            }
                            if(!empty($farm_trin_feed_qty[$batches."@".$fcode])){
                                $farm_transferin_feeds = $farm_transferin_feeds + $farm_trin_feed_qty[$batches."@".$fcode];
                                $purtrin_feed_amt = $purtrin_feed_amt + $farm_trin_feed_amt[$batches."@".$fcode];
                            }
                            if(!empty($sector_trin_feed_qty[$batches."@".$fcode])){
                                $sector_transferin_feeds = $sector_transferin_feeds + $sector_trin_feed_qty[$batches."@".$fcode];
                                $purtrin_feed_amt = $purtrin_feed_amt + $sector_trin_feed_amt[$batches."@".$fcode];
                            }
                            
                            //Sale or Transfer Out
                            if(!empty($sale_feed_qty[$batches."@".$fcode])){
                                $sales_feeds = $sales_feeds + $sale_feed_qty[$batches."@".$fcode];
                            }
                            if(!empty($farm_trout_feed_qty[$batches."@".$fcode])){
                                $farm_transferout_feeds = $farm_transferout_feeds + $farm_trout_feed_qty[$batches."@".$fcode];
                            }
                            if(!empty($sector_trout_feed_qty[$batches."@".$fcode])){
                                $sector_transferout_feeds = $sector_transferout_feeds + $sector_trout_feed_qty[$batches."@".$fcode];
                            }
                        }
                        //Feed Consumed
                        if(!empty($dentry_feed[$batches])){
                            $consumed_feeds = $consumed_feeds + $dentry_feed[$batches];
                        }
                        //Medicine & Vaccine Transactions
                        $purchase_medvacs = $farm_transferin_medvacs = $sector_transferin_medvacs = $consumed_medvacs = $sales_medvacs = $farm_transferout_medvacs = $sector_transferout_medvacs = 0;
                        $purtrin_medvac_amt = 0;
                        foreach($medvac_code as $fcode){
                            //Purchase or Transfer in
                            if(!empty($pur_medvac_qty[$batches."@".$fcode])){
                                $purchase_medvacs = $purchase_medvacs + $pur_medvac_qty[$batches."@".$fcode];
                                $purtrin_medvac_amt = $purtrin_medvac_amt + $pur_medvac_amt[$batches."@".$fcode];
                            }
                            if(!empty($farm_trin_medvac_qty[$batches."@".$fcode])){
                                $farm_transferin_medvacs = $farm_transferin_medvacs + $farm_trin_medvac_qty[$batches."@".$fcode];
                                $purtrin_medvac_amt = $purtrin_medvac_amt + $farm_trin_medvac_amt[$batches."@".$fcode];
                            }
                            if(!empty($sector_trin_medvac_qty[$batches."@".$fcode])){
                                $sector_transferin_medvacs = $sector_transferin_medvacs + $sector_trin_medvac_qty[$batches."@".$fcode];
                                $purtrin_medvac_amt = $purtrin_medvac_amt + $sector_trin_medvac_amt[$batches."@".$fcode];
                            }
                            //Medicine Consumption
                            if(!empty($medvac_qty[$batches])){
                                $consumed_medvacs = $consumed_medvacs + $medvac_qty[$batches];
                            }
                            //Sale or Transfer Out
                            if(!empty($sale_medvac_qty[$batches."@".$fcode])){
                                $sales_medvacs = $sales_medvacs + $sale_medvac_qty[$batches."@".$fcode];
                            }
                            if(!empty($farm_trout_medvac_qty[$batches."@".$fcode])){
                                $farm_transferout_medvacs = $farm_transferout_medvacs + $farm_trout_medvac_qty[$batches."@".$fcode];
                            }
                            if(!empty($sector_trout_medvac_qty[$batches."@".$fcode])){
                                $sector_transferout_medvacs = $sector_transferout_medvacs + $sector_trout_medvac_qty[$batches."@".$fcode];
                            }
                        }

                        $display_age = $display_act_age = $display_supervisor = $display_farmbranch = $display_farmname = $display_farbatch = $display_placement_date = 
                        $display_lifting_start_date = $mean_age_total = $display_mean_age = $display_recent_entry_date = $display_gap_days = $display_housed_chicks = $display_mort = 
                        $display_mortper = $display_sold_birds = $display_available_birds = $display_availableavg_body_wt = $display_fcr = $display_cfcr = $display_eef = 
                        $display_shortage_birds = $display_feeds_transferred = $display_feeds_in_farm = $display_feeds_consumed = $display_feeds_out_farm = $display_feeds_balance = 0;
                        //$display_age = $brood_age;
                        $display_supervisor = $supervisor_name[$farm_supervisor[$fetch_fcode]];
                        $display_farmbranch = $branch_name[$farm_branch[$fetch_fcode]];
                        $display_farmline = $line_name[$farm_line[$fetch_fcode]];
                        $display_farmname = $farm_name[$fetch_fcode];
                        $display_farbatch_code = $batches;
                        $display_farbatch = $batch_name[$batches];
                        $display_batchbook = $batch_book[$batches];
                        
                        //if(!empty($display_farbatch) && !empty($chick_placed_date[$batches]) && date("d.m.Y",$chick_placed_date[$batches]) != "01.01.1970"){ 
                        if(!empty($chick_placed_date[$batches]) && date("d.m.Y",$chick_placed_date[$batches]) != "01.01.1970" || !empty($sector_trin_date[$batches]) && date("d.m.Y",strtotime($sector_trin_date[$batches])) != "01.01.1970" || !empty($farm_trin_date[$batches]) && date("d.m.Y",strtotime($farm_trin_date[$batches])) != "01.01.1970" || !empty($pur_feedin_date[$batches]) && date("d.m.Y",strtotime($pur_feedin_date[$batches])) != "01.01.1970"){ 
                            if(date("d.m.Y",$chick_placed_date[$batches]) != "01.01.1970"){
                                $display_placement_date = $chick_placed_date[$batches];
                            }
                            else if(date("d.m.Y",strtotime($dentry_min_date[$batches])) != "01.01.1970"){
                                $display_placement_date = strtotime($dentry_min_date[$batches]);
                            }
                            else{
                                $display_placement_date = strtotime($dentry_min_date[$batches]);
                            }
                            if($day_entryage_flag == 1){
                                $display_age = $dentry_max_age[$batches];
                            }
                            else{
                                $display_age = ((strtotime($today) - $display_placement_date) / 60 / 60 / 24)+1;
                            }
                            if(!empty($dentry_max_age[$batches]) && $dentry_max_age[$batches] != ""){
                                $display_act_age = $dentry_max_age[$batches];
                            }
                            else{
                                $display_act_age = 0;
                            }
                            if(!empty($sale_start_date[$batches."@".$bird_code])){
                                $display_lifting_start_date = $sale_start_date[$batches."@".$bird_code];
                            }
                            else{
                                $display_lifting_start_date = "";
                            }

                            $fdate = $start_date[$batches]; $tdate = $end_date[$batches];
                            for($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
                                $present_date = date("Y-m-d",$currentDate);
                                if(!empty($sal_birds[$present_date."@".$bird_code."@".$batches]) && !empty($day_ages[$present_date."@".$batches])){
                                    $mean_age_total = $mean_age_total + ($day_ages[$present_date."@".$batches] * $sal_birds[$present_date."@".$bird_code."@".$batches]);
                                    //echo "<br/>".$batches;
                                }
                            }
                            $display_recent_entry_date = strtotime($dentry_max_date[$batches]);
                            
                            

                            if(date("d.m.Y",strtotime($dentry_max_date[$batches])) != "01.01.1970"){
                                $display_gap_days = ((strtotime(date("d.m.Y")) - strtotime($dentry_max_date[$batches])) / 60 / 60 / 24);
                            }
                            else if(date("d.m.Y",strtotime($chick_placed_date[$batches])) != "01.01.1970"){
                                $display_gap_days = ((strtotime(date("d.m.Y")) - $chick_placed_date[$batches]) / 60 / 60 / 24);
                            }
                            else{
                                $display_gap_days = 0;
                            }
                            $display_housed_chicks = $purchase_chicks + $farm_transferin_chicks + $sector_transferin_chicks;
                            //echo "<br/>".$purchase_chicks."-".$farm_trin_bird_qty."-".$sector_trin_bird_qty;
                            //echo "<br/>".$batches."-".$display_farbatch."-".date("d.m.Y",$chick_placed_date[$batches])."-".date("d.m.Y",strtotime($dentry_max_date[$batches]));

                            $Actual_prod_cost = (float)$purtrin_chick_amt + (float)$purtrin_feed_amt + (float)$purtrin_medvac_amt + ((float)$admin_cost[$farm_branch[$fetch_fcode]] * (float)$display_housed_chicks);
                            
                            if($sales_birds_nos > 0){
                                $remaining_chicks = (float)$display_housed_chicks - (float)$mortality_chicks - (float)$chicks_cull - (float)$sales_birds_nos;
                                $sold_avg_wt = (float)$sales_birds_qty;
                            }
                            else{
                                $remaining_chicks = (float)$display_housed_chicks - (float)$mortality_chicks - (float)$chicks_cull; $sold_avg_wt = 0;
                            }

                            $davg_bwt = round(((float)$davg_wt[$batches] / 1000),3);
                            if(!empty($davg_wt[$batches]) && $davg_bwt > 0){
                                $dentry_avg_wt = ((float)$remaining_chicks * (float)$davg_bwt);
                                //echo "<br/>".$dentry_avg_wt."-".(float)$remaining_chicks."-".((float)$davg_wt[$batches] / 1000);
                            }
                            else{ $dentry_avg_wt = 0; }

                            $total_bird_wt = $sold_avg_wt + $dentry_avg_wt;
                            if($total_bird_wt > 0){
                                $display_prod_cost = $Actual_prod_cost / $total_bird_wt;
                            }
                            else{
                                $display_prod_cost = 0;
                            }
                            //$display_prod_cost_title = $Actual_prod_cost."/".$total_bird_wt."-".$display_prod_cost;

                            //echo "<br/>".$Actual_prod_cost."-".$total_bird_wt."-".$sold_avg_wt."-".$dentry_avg_wt."-".$purtrin_chick_amt."-".$purtrin_feed_amt."-".$purtrin_medvac_amt."-".$admin_cost[$farm_branch[$fetch_fcode]]."-".$display_housed_chicks;

                            $display_mort = $mortality_chicks;
                            if($display_housed_chicks > 0 && $display_mort > 0){
                                $display_mortper = (($display_mort / $display_housed_chicks) * 100);
                            }
                            else{
                                $display_mortper = 0;
                            }
                            $display_cull = $chicks_cull;
                            if($display_housed_chicks > 0 && $display_cull > 0){
                                $display_cullper = (($display_cull / $display_housed_chicks) * 100);
                            }
                            else{
                                $display_cullper = 0;
                            }
                            $display_sold_birds = $sales_birds_nos;
                            $display_sold_weight = $sales_birds_qty;
                            $display_available_birds = ($display_housed_chicks - ($display_mort + $display_cull + $display_sold_birds));
                            if($sales_birds_nos > 0){
                                $display_availableavg_body_wt = ($sales_birds_qty / $sales_birds_nos);
                                $abwt_title = "$display_availableavg_body_wt = ($sales_birds_qty / $sales_birds_nos);";
                            }
                            else{
                                if(!empty($davg_wt[$batches]) && $davg_wt[$batches] > 0){
                                    $display_availableavg_body_wt = round(((float)$davg_wt[$batches] / 1000),3);
                                    $abwt_title = "$display_availableavg_body_wt = round(((float)$davg_wt[$batches] / 1000),3);";
                                }
                                else{
                                    $display_availableavg_body_wt = 0;
                                    $abwt_title = "";
                                }
                            }
                            if($sales_birds_qty > 0 && $consumed_feeds > 0) {
                                $display_fcr = ($consumed_feeds / $sales_birds_qty);
                                $fcr_title = "$display_fcr = ($consumed_feeds / $sales_birds_qty);";
                            }
                            else if($display_available_birds != 0 && $display_availableavg_body_wt != 0 && $consumed_feeds > 0){
                                $display_fcr = ($consumed_feeds / ($display_available_birds * $display_availableavg_body_wt));
                                $fcr_title = "$display_fcr = ($consumed_feeds / ($display_available_birds * $display_availableavg_body_wt));";
                            }
                            else{
                                $display_fcr = 0;
                                $fcr_title = "";
                            }
                            if($display_availableavg_body_wt > 0){
                                $display_cfcr = (((2 - ($display_availableavg_body_wt)) / 4) + $display_fcr);
                            }
                            else{
                                $display_cfcr = 0;
                            }
                            
                            if($mean_age_total > 0 && $sales_birds_nos > 0){
                                $display_mean_age = $mean_age_total / $sales_birds_nos;
                            }
                            //else if($mean_age_total > 0 && $display_available_birds > 0){ $display_mean_age = $mean_age_total / $display_available_birds; }
                            else{
                                $display_mean_age = 0;
                            }
                            if($display_housed_chicks > 0 && ($display_fcr * $display_mean_age) > 0){
                                $display_eef = (((((($display_housed_chicks - $display_mort - $display_cull) / $display_housed_chicks) * 100) * $display_availableavg_body_wt) * 100) / ($display_fcr * $display_mean_age));
                            }
                            else{
                                $display_eef = 0;
                            }
                            

                            if($display_available_birds > 0){ $display_shortage_birds = 0; $display_excess_birds = $display_available_birds; } else{ $display_shortage_birds = (($display_mort + $display_cull + $display_sold_birds) - $display_housed_chicks); $display_excess_birds = 0; }
                            $display_feeds_transferred = $purchase_feeds + $sector_transferin_feeds;
                            $display_feeds_in_farm = $farm_transferin_feeds;
                            $display_feeds_consumed = $consumed_feeds;
                            $display_feeds_out_farm = $farm_transferout_feeds;
                           
                            $display_feeds_balance = (($display_feeds_transferred + $display_feeds_in_farm) - ($display_feeds_consumed + $display_feeds_out_farm + $sales_feeds + $sector_transferout_feeds));
                            //echo "<br/>$display_feeds_balance = (($display_feeds_transferred + $display_feeds_in_farm) - ($display_feeds_consumed + $display_feeds_out_farm + $sales_feeds + $sector_transferout_feeds));";
                            
                            $latest_consumed_qty = $dentry_con[$batches];

                            if($latest_consumed_qty > 0){
                                $display_feed_bal_days = $display_feeds_balance / $latest_consumed_qty;
                                $display_next_3_days_feed = $latest_consumed_qty * (float)$manual_nxtfeed;
                            }
                            else{
                                $display_feed_bal_days = $display_next_3_days_feed = 0;
                            }
                            if($abirds == "" && $max_age == "" && $min_age == "" || (float)$abirds <= (float)$display_available_birds && $max_age == "" && $min_age == "" || $display_age >= $min_age && $display_age <= $max_age){
                                if($display_mortper >= $mort_above){
                                   if($age_above > 0){
                                    if(date('d.m.Y',((int)$display_placement_date)) == '01.01.1970'){
                                        $display_age = 0; 
                                    }
                                   }
                                    if($age_above == 0 || $display_age >= $age_above){
                                $slno++;
                                $total_housed_chicks = $total_housed_chicks + $display_housed_chicks;
                                $total_mort_chicks = $total_mort_chicks + $display_mort;
                                $total_cull_chicks = $total_cull_chicks + $display_cull;
                                $total_sold_chicks = $total_sold_chicks + $display_sold_birds;
                                $total_sold_weight = $total_sold_weight + $display_sold_weight;
                                $total_aval_chicks = $total_aval_chicks + $display_available_birds;
                                $total_short_chicks = $total_short_chicks + $display_shortage_birds;
                                $total_exccess_chicks = $total_exccess_chicks + $display_excess_birds;
                                $total_feedin_chicks = $total_feedin_chicks + $display_feeds_transferred;
                                $total_feedin_farm_chicks = $total_feedin_farm_chicks + $display_feeds_in_farm;
                                $total_feed_consumed_chicks = $total_feed_consumed_chicks + $display_feeds_consumed;
                                $total_feedout_farm_chicks = $total_feedout_farm_chicks + $display_feeds_out_farm;
                                $total_feed_bal_chicks = $total_feed_bal_chicks + $display_feeds_balance;
                                $total_next_3_days_feed = $total_next_3_days_feed + $display_next_3_days_feed;

                                //Present Day Mortality
                                if(empty($cdate_mort[$batches]) || $cdate_mort[$batches] == ""){ $cdate_mort[$batches] = 0; }
                                $tcur_mort += (float)$cdate_mort[$batches];

                                echo "<tr>";
                                for($i = 1;$i <= $col_count;$i++){
                                    $key_id = "A:1:".$i;
                                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No.' style='text-align:center;'>".$slno."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<td title='Branch' style='text-align:left;'>".$display_farmbranch."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<td title='Line' style='text-align:left;'>".$display_farmline."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor' style='text-align:left;'>".$display_supervisor."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farmer' style='text-align:left;'>".$display_farmname."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code"){ echo "<td title='Batch Code' style='text-align:left;'>".$display_farbatch_code."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<td title='Batch' style='text-align:left;'>".$display_farbatch."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<td title='Book No' style='text-align:left;'>".$display_batchbook."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){
                                        if(date('d.m.Y',((int)$display_placement_date)) == '01.01.1970'){ echo "<td title='Present Day Age' style='text-align:center;'>0</td>"; }
                                        else{ echo "<td title='Present Day Age' style='text-align:center;'>".round($display_age)."</td>"; }
                                    }else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_act_age"){
                                        if(date('d.m.Y',((int)$display_placement_date)) == '01.01.1970'){ echo "<td title='Age' style='text-align:center;'>0</td>"; }
                                        else{ echo "<td title='Age' style='text-align:center;'>".round($display_act_age)."</td>"; }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date"){
                                        if(date('d.m.Y',((int)$display_placement_date)) == '01.01.1970'){ echo "<td title='Placement Date' style='text-align:left;'></td>"; } 
                                        else{ echo "<td title='Placement Date' style='text-align:left;'>".date('d.m.Y',((int)$display_placement_date))."</td>"; }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_start_date"){
                                        if(date("d.m.Y",((int)$display_lifting_start_date)) == "01.01.1970"){ echo "<td title='Lifting Start Date' style='text-align:left;'></td>"; }
                                        else{ echo "<td title='Lifting Start Date' style='text-align:left;'>".date("d.m.Y",((int)$display_lifting_start_date))."</td>"; }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<td title='Mean Age' style='text-align:right;'>".number_format_ind(round($display_mean_age,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date"){
                                        if(date("d.m.Y",((int)$display_recent_entry_date)) == "01.01.1970"){ echo "<td title='Latest Entry Date' style='text-align:left;'><b style='color:red'>Not Started</b></td>"; } 
                                        else{ echo "<td title='Latest Entry Date' style='text-align:left;'>".date("d.m.Y",((int)$display_recent_entry_date))."</td>"; }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gap_days"){
                                        if(number_format_ind($display_gap_days) == "19,579.77" || number_format_ind($display_gap_days) == "19698.770833333"){
                                            echo "<td title='Gap Days' style='text-align:right;'>0</td>";
                                        }
                                        else{
                                            echo "<td title='Gap Days' style='text-align:right;'>".$display_gap_days."</td>";
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<td title='Housed Chicks' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_housed_chicks)))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<td title='Mort' style='text-align:right;'><a href=/records/broiler_single_dayrecord_masterreport.php?submit_report=true&farms=". $fetch_fcode ."&batch=". $batches ." target='_blank'>".str_replace(".00","",number_format_ind(round($display_mort)))."</a></td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){
                                        if((float)$display_mortper > 3){
                                            echo "<td title='Mort%' style='text-align:right;color:red;'>".number_format_ind(round($display_mortper,2))."</td>";
                                        }
                                        else{
                                            echo "<td title='Mort%' style='text-align:right;'>".number_format_ind(round($display_mortper,2))."</td>";
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ echo "<td title='Cull' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_cull)))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_per"){ echo "<td title='Cull%' style='text-align:right;'>".number_format_ind(round($display_cullper,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Sold Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_sold_birds)))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Sold Weight' style='text-align:right;'>".number_format_ind(round($display_sold_weight,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ echo "<td title='Available Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_available_birds)))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ echo "<td title='Std B.Wt' style='text-align:right;'>".number_format($std_body_weight[round($display_age)] / 1000,3)."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){
                                        if(!empty($tdate_awht[$batches]) && (FLOAT)$tdate_awht[$batches] > 0){
                                            echo "<td title='Avg B.Wt: ".$abwt_title."' style='text-align:right;color:blue;'>".number_format($display_availableavg_body_wt,3)."</td>";
                                        }
                                        else if($std_body_weight[round($display_age)] > $display_availableavg_body_wt){
                                            echo "<td title='Avg B.Wt: ".$abwt_title."' style='text-align:right;color:red;'>".number_format($display_availableavg_body_wt,3)."</td>";
                                        }
                                        else if(number_format_ind($display_availableavg_body_wt) == "0.00"){
                                            echo "<td title='Avg B.Wt: ".$abwt_title."' style='text-align:right;color:red;'>".number_format($display_availableavg_body_wt,3)."</td>";
                                        }
                                        else{
                                            echo "<td title='Avg B.Wt: ".$abwt_title."' style='text-align:right;color:green;'>".number_format($display_availableavg_body_wt,3)."</td>";
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<td title='Std FCR' style='text-align:right;'>".number_format($std_fcr[round($display_age)],3)."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){
                                        if(number_format($display_fcr,3) == "INF" || number_format($display_fcr,3) == "NAN"){ echo "<td title='FCR' style='text-align:right;'>0.000</td>"; }
                                        else{ echo "<td title='FCR: ".$fcr_title."' style='text-align:right;'>".number_format($display_fcr,3)."</td>"; }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<td title='CFCR' style='text-align:right;'>".number_format($display_cfcr,3)."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<td title='EEF' style='text-align:right;'>".number_format_ind(round($display_eef,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){ echo "<td title='M PC/Kg' style='text-align:right;'>".number_format_ind(round($display_prod_cost,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_count"){ echo "<td title='Feed Transferred' style='text-align:right;'>".number_format_ind(round($display_feeds_transferred,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_count"){ echo "<td title='Transfer In From Other Farms' style='text-align:right;'>".number_format_ind(round($display_feeds_in_farm,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<td title='Std Feed perKg' style='text-align:right;'>".number_format_ind(round(($std_cum_feed[round($display_age)] * $display_housed_chicks) / 1000,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){
                                        if((float)$display_housed_chicks != 0){ $t1 = 0; $t1 = round(((($std_cum_feed[round($display_age)] * $display_housed_chicks) / 1000) / $display_housed_chicks),2); }
                                        else{ $t1 = 0; }
                                        echo "<td title='Std Feed Con' style='text-align:right;'>".number_format_ind($t1)."</td>";
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){
                                        if((($std_cum_feed[round($display_age)] * $display_housed_chicks) / 1000) > $display_feeds_consumed){
                                            echo "<td title='Feed Con' style='text-align:right;color:green;'>".number_format_ind(round($display_feeds_consumed,2))."</td>";
                                        }
                                        else if(number_format_ind($display_feeds_consumed) == "0.00"){
                                            echo "<td title='Feed Con' style='text-align:right;color:black;'>".number_format_ind(round($display_feeds_consumed,2))."</td>";
                                        }
                                        else{
                                            echo "<td title='Feed Con' style='text-align:right;color:red;'>".number_format_ind(round($display_feeds_consumed,2))."</td>";
                                        }
                                    } else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags"){
                                        if((($std_cum_feed[round($display_age)] * $display_housed_chicks) / 1000) > $display_feeds_consumed){
                                            echo "<td title='Feed Con' style='text-align:right;color:green;'>".number_format_ind(round($display_feeds_consumed/50,2))."</td>";
                                        }
                                        else if(number_format_ind($display_feeds_consumed) == "0.00"){
                                            echo "<td title='Feed Con' style='text-align:right;color:black;'>".number_format_ind(round($display_feeds_consumed/50,2))."</td>";
                                        }
                                        else{
                                            echo "<td title='Feed Con' style='text-align:right;color:red;'>".number_format_ind(round($display_feeds_consumed/50,2))."</td>";
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){
                                        if($display_housed_chicks > 0){
                                            echo "<td title='Actual Feed perKg' style='text-align:right;'>".number_format_ind(round(($display_feeds_consumed / $display_housed_chicks),2))."</td>";
                                        }
                                        else{
                                            echo "<td title='Actual Feed perKg' style='text-align:right;'>".number_format_ind(round((0),2))."</td>";
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno2"){
                                        if(((float)$display_housed_chicks - (float)$display_mort - (float)$display_cull) > 0){
                                            $atitle = "number_format_ind(round(((float)$display_feeds_consumed / ((float)$display_housed_chicks - (float)$display_mort - (float)$display_cull)),2))";
                                            echo "<td title='".$atitle."' style='text-align:right;'>".number_format_ind(round(((float)$display_feeds_consumed / ((float)$display_housed_chicks - (float)$display_mort - (float)$display_cull)),2))."</td>";
                                        }
                                        else{
                                            echo "<td title='Actual Feed perKg' style='text-align:right;'>".number_format_ind(round((0),2))."</td>";
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_count"){ echo "<td title='Transfer Out to Farms' style='text-align:right;'>".number_format_ind(round($display_feeds_out_farm,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ echo "<td title='Feed Balance' style='text-align:right;'>".number_format_ind(round($display_feeds_balance,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_days"){ echo "<td title='Feed Balance Days-".$display_feeds_balance."/".$latest_consumed_qty."' style='text-align:right;'>".substr(number_format_ind($display_feed_bal_days),0,-3)."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "next_3days_feed"){ echo "<td title='Next 3 Days Feed' style='text-align:right;'>".number_format_ind(round($display_next_3_days_feed,2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birds_per"){ 
                                        if($display_housed_chicks > 0){
                                            echo "<td title='Sold Weight' style='text-align:right;'>".number_format_ind(($display_sold_birds/$display_housed_chicks)*100)."</td>"; 
                                        }else{
                                            echo "<td title='Sold Weight' style='text-align:right;'>"."0"."</td>"; 
                                        }
 
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ 
                                        if(!empty($sector_name[$cin_sup_code[$batches]])){
                                            echo "<td title='Sold Weight' style='text-align:right;'>".$sector_name[$cin_sup_code[$batches]]."</td>"; 
                                        }else{
                                            echo "<td title='Sold Weight' style='text-align:right;'></td>"; 
                                        }
                                    }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_bags"){ echo "<td title='Feed Transferred Bags' style='text-align:right;'>".number_format_ind(round(($display_feeds_transferred / 50),2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_bags"){ echo "<td title='Transfer In From Other Farms Bags' style='text-align:right;'>".number_format_ind(round(($display_feeds_in_farm / 50),2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_bags"){ echo "<td title='Transfer Out to Farms Bags' style='text-align:right;'>".number_format_ind(round(($display_feeds_out_farm / 50),2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_bags"){ echo "<td title='Feed Balance Bags' style='text-align:right;'>".number_format_ind(round(($display_feeds_balance / 50),2))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand"){ echo "<td title='Latest Feed-In Brand' style='text-align:left;'>".$blentry_items[$batches]."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "today_mort_count"){ echo "<td title='Today Mortality' style='text-align:right;'>".str_replace('.00','',number_format_ind($cdate_mort[$batches]))."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<td title='Today Mortality' style='text-align:right;'>".$sector_name[$chkin_hcode[$batches]]."</td>"; }
                                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<td title='Today Mortality' style='text-align:right;'>".$sector_name[$chkin_vcode[$batches]]."</td>"; }
                                    else { }
                                }
                                echo "</tr>";
                            }
                            }
                            }
                        }
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                echo "<tr class='thead4'>";
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th style='text-align:left; border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_act_age"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_start_date"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gap_days"){ echo "<th style='text-align:center; border-left: 0px;'>Total</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_housed_chicks))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_mort_chicks))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){
                        if($total_housed_chicks > 0){
                            echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind(($total_mort_chicks / $total_housed_chicks) * 100))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind(0))."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_cull_chicks))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_per"){
                        if($total_housed_chicks > 0){
                            echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind(($total_cull_chicks / $total_housed_chicks) * 100))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind(0))."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_sold_chicks))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birds_per"){ 
                        if($total_housed_chicks > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(($total_sold_chicks/$total_housed_chicks)*100)."</th>";
                        }else{
                            echo "<th style='text-align:right;'>"."0"."</th>";  
                        }
                       
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_sold_weight))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_aval_chicks))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feedin_chicks)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feedin_farm_chicks)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_consumed_chicks)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_consumed_chicks/50)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno2"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feedout_farm_chicks)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_bal_chicks)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_days"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "next_3days_feed"){ echo "<th style='text-align:right;'>".number_format_ind($total_next_3_days_feed)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_sector_bags"){ echo "<th style='text-align:right;'>".number_format_ind(round(($total_feedin_chicks / 50),2))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_farm_bags"){ echo "<th style='text-align:right;'>".number_format_ind(round(($total_feedin_farm_chicks / 50),2))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_farms_bags"){ echo "<th style='text-align:right;'>".number_format_ind(round(($total_feedout_farm_chicks / 50),2))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_bags"){ echo "<th style='text-align:right;'>".number_format_ind(round(($total_feed_bal_chicks / 50),2))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "today_mort_count"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($tcur_mort))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<th style='text-align:right;'></th>"; }
                    
                    else{ }
                }
                echo "</tr>";
                ?>
            </tfoot>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        
          <script>
            function fetch_farms_details(a){
                var regions = document.getElementById("regions").value;
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;
                var user_code = '<?php echo $user_code; ?>';
                var rf_flag = bf_flag = lf_flag = sf_flag = ff_flag = 0;
                if(a.match("regions")){ rf_flag = 1; } else if(a.match("branches")){ bf_flag = 1; } else if(a.match("lines")){ lf_flag = 1; } else if(a.match("supervisors")){ sf_flag = 1; } else{ ff_flag = 1; }
                    
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_fetch_farm_filter_master.php?regions="+regions+"&branches="+branches+"&lines="+lines+"&supervisors="+supervisors+"&rf_flag="+rf_flag+"&bf_flag="+bf_flag+"&lf_flag="+lf_flag+"&sf_flag="+sf_flag+"&ff_flag="+ff_flag+"&user_code="+user_code;
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var brnh_list = fltr_dt2[3];
                        var line_list = fltr_dt2[0];
                        var supr_list = fltr_dt2[1];
                        var farm_list = fltr_dt2[2];

                        if(rf_flag == 1){
                            removeAllOptions(document.getElementById("branches"));
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#branches').append(brnh_list);
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(lf_flag == 1){
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("farms"));
                            $('#farms').append(farm_list);
                        }
                        else{ }
                    }
                }
            }
            var f_cnt = 0;
            function set_auto_selectors(){
                if(f_cnt == 0){
                    var fx = "regions"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 1){
                    var br_val = brlist = "";
                    $('#branches').select2();
                    for(var option of document.getElementById("branches").options){
                        option.selected = false;
                        br_val = option.value;
                        brlist = ''; brlist = '<?php echo $branches; ?>';
                        if(br_val == brlist){ option.selected = true; }
                    }
                    $('#branches').select2();
                    var fx = "branches"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    var l_val = llist = "";
                    $('#lines').select2();
                    for(var option of document.getElementById("lines").options){
                        option.selected = false;
                        l_val = option.value;
                        llist = ''; llist = '<?php echo $lines; ?>';
                        if(l_val == llist){ option.selected = true; }
                    }
                    $('#lines').select2();
                    var fx = "lines"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    var s_val = slist = "";
                    $('#supervisors').select2();
                    for(var option of document.getElementById("supervisors").options){
                        option.selected = false;
                        s_val = option.value;
                        slist = ''; slist = '<?php echo $supervisors; ?>';
                        if(s_val == slist){ option.selected = true; }
                    }
                    $('#supervisors').select2();
                    var fx = "supervisors"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    var f_val = flist = "";
                    $('#farms').select2();
                    for(var option of document.getElementById("farms").options){
                        option.selected = false;
                        f_val = option.value;
                        flist = ''; flist = '<?php echo $farms; ?>';
                        if(f_val == flist){ option.selected = true; }
                    }
                    $('#farms').select2();
                    var fx = "farms"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 4){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
        </script>
        <script>
 function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

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
                console.log("test");
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
                    console.log("test1");

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
                console.log("test");
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
                    console.log("test1");

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
                var slno_flag = '<?php echo $slno_flag; ?>';
                if(parseInt(slno_flag) == 1){
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
        <script>
            function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){ 
                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                }
                else{ }
            }
        </script>
        <script>
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>