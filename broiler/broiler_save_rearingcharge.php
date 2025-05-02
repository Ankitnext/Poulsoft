<?php
//broiler_save_rearingcharge.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['rearingcharge'];

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'FCR Rearing Charge Master' AND `field_function` LIKE 'FCR Based GC' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $fcr_gc_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'CFCR Based GC' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $cfcr_gc_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'COP on Avg.BodyWeight AND FCR' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $copabwfcr_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'SI on between Avg. body weight std' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $sibabws_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Loyalty Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $loyalinc_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Summer Incentive on Body Weight' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $smrincbdw_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Winter Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $winter_incv_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Shortage Max Allowed Birds' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $shortage_maxbirds_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'COP based Incentive and Decentive calculations' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $copbincdec_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Standard GC based on between Avg.Weights' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $stdgconavgwt_flag = mysqli_num_rows($query);
                
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Mortality Incentives Based on grades' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $mibong_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Seasonal Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $seasoninc_flag = mysqli_num_rows($query);

//Fetch Column From Generator Table
$sql='SHOW COLUMNS FROM `broiler_gc_si_incentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
//Add Columns to Generator Table
if(in_array("max_prod_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_si_incentive` ADD `max_prod_cost` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Maximun Production Cost' AFTER `sales_max_rate`"; mysqli_query($conn,$sql); }
if(in_array("sales_inc_grade", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_si_incentive` ADD `sales_inc_grade` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `std_code`"; mysqli_query($conn,$sql); }

//Fetch Column From Generator Table
$sql='SHOW COLUMNS FROM `broiler_gc_standard`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
//Add Columns to Generator Table
if(in_array("maize_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `maize_cost` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `feed_cost`"; mysqli_query($conn,$sql); }
if(in_array("mgmt_admin_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `mgmt_admin_cost` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Management Admin Cost' AFTER `admin_cost`"; mysqli_query($conn,$sql); }
if(in_array("avgwt_upto", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `avgwt_upto` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Max Avg Weight' AFTER `standard_cost`"; mysqli_query($conn,$sql); }
if(in_array("avgwt_gccost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `avgwt_gccost` VARCHAR(300) NULL DEFAULT NULL COMMENT 'GC-Cost on Max Avg Weight' AFTER `avgwt_upto`"; mysqli_query($conn,$sql); }
if(in_array("schema_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `schema_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Schema name' AFTER `branch_code`"; mysqli_query($conn,$sql); }
if(in_array("gcm_unl_charge", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `gcm_unl_charge` DECIMAL(20,5) NULL DEFAULT NULL COMMENT 'GC Master Unloading Charges' AFTER `standard_mortality`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `broiler_gc_st_decentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("max_srate_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_st_decentive` ADD `max_srate_flag` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `sale_flag`"; mysqli_query($conn,$sql); }

$region_code = $_POST['region_code'];
$branches = $_POST['branch_code'];
$schema_name = $_POST['schema_name'];
$from_date = date("Y-m-d",strtotime($_POST['from_date']));
$to_date = date("Y-m-d",strtotime($_POST['to_date']));

$chick_cost = $_POST['chick_cost']; if($chick_cost == ""){ $chick_cost = 0; }
$feed_cost = $_POST['feed_cost']; if($feed_cost == ""){ $feed_cost = 0; }
$maize_cost = $_POST['maize_cost']; if($maize_cost == ""){ $maize_cost = 0; }
$medicine_cost = $_POST['medicine_cost']; if($medicine_cost == ""){ $medicine_cost = ""; }
if($medicine_cost == "A"){ $med_price = "0.00"; } else{ $med_price = $_POST['fixed_cost']; } if($med_price == "" || $med_price == 0 || $med_price == "0.00"){ $med_price = "0.00"; }
$admin_cost = $_POST['admin_cost']; if($admin_cost == ""){ $admin_cost = 0; }
$mgmt_admin_cost = $_POST['mgmt_admin_cost']; if($mgmt_admin_cost == ""){ $mgmt_admin_cost = 0; }
$standard_prod_cost = $_POST['standard_prod_cost']; if($standard_prod_cost == ""){ $standard_prod_cost = 0; }
$standard_cost = $_POST['standard_cost']; if($standard_cost == ""){ $standard_cost = 0; }
$avgwt_upto = $_POST['avgwt_upto']; if($avgwt_upto == ""){ $avgwt_upto = 0; }
$avgwt_gccost = $_POST['avgwt_gccost']; if($avgwt_gccost == ""){ $avgwt_gccost = 0; }
$minimum_cost = $_POST['minimum_cost']; if($minimum_cost == ""){ $minimum_cost = 0; }
$standard_fcr = $_POST['standard_fcr']; if($standard_fcr == ""){ $standard_fcr = 0; }
$standard_mortality = $_POST['standard_mortality']; if($standard_mortality == ""){ $standard_mortality = 0; }
$gcm_unl_charge = $_POST['gcm_unl_charge']; if($gcm_unl_charge == ""){ $gcm_unl_charge = 0; }

$fcrs_from_val = $fcrs_to_val = $std_rates = array();
if($fcr_gc_flag == 1){
    $i = 0; foreach($_POST['fcrs_from_val'] as $fcrs_from_vals){ $i++; if($fcrs_from_vals == ""){ $fcrs_from_vals = 0; } $fcrs_from_val[$i] = $fcrs_from_vals; }
    $i = 0; foreach($_POST['fcrs_to_val'] as $fcrs_to_vals){ $i++; if($fcrs_to_vals == ""){ $fcrs_to_vals = 0; } $fcrs_to_val[$i] = $fcrs_to_vals; }
    $i = 0; foreach($_POST['std_rates'] as $std_ratess){ $i++; if($std_ratess == ""){ $std_ratess = 0; } $std_rates[$i] = $std_ratess; }
}

$cfcrs_from_val = $cfcrs_to_val = $ngc_rate = $cfcr_from_wht = $cfcr_to_wht = $sbgc_rate = array();
if($cfcr_gc_flag == 1){
    $i = 0; foreach($_POST['cfcrs_from_val'] as $cfcrs_from_vals){ $i++; if($cfcrs_from_vals == ""){ $cfcrs_from_vals = 0; } $cfcrs_from_val[$i] = $cfcrs_from_vals; }
    $i = 0; foreach($_POST['cfcrs_to_val'] as $cfcrs_to_vals){ $i++; if($cfcrs_to_vals == ""){ $cfcrs_to_vals = 0; } $cfcrs_to_val[$i] = $cfcrs_to_vals; }
    $i = 0; foreach($_POST['ngc_rate'] as $ngc_rates){ $i++; if($ngc_rates == ""){ $ngc_rates = 0; } $ngc_rate[$i] = $ngc_rates; }
    $i = 0; foreach($_POST['cfcr_from_wht'] as $cfcr_from_whts){ $i++; if($cfcr_from_whts == ""){ $cfcr_from_whts = 0; } $cfcr_from_wht[$i] = $cfcr_from_whts; }
    $i = 0; foreach($_POST['cfcr_to_wht'] as $cfcr_to_whts){ $i++; if($cfcr_to_whts == ""){ $cfcr_to_whts = 0; } $cfcr_to_wht[$i] = $cfcr_to_whts; }
    $i = 0; foreach($_POST['sbgc_rate'] as $sbgc_rates){ $i++; if($sbgc_rates == ""){ $sbgc_rates = 0; } $sbgc_rate[$i] = $sbgc_rates; }
}

$copabw_from_val = $copabw_to_val = $copfcr_from_val = $copfcr_to_val = $copfcr_std_val = array();
if($copabwfcr_flag == 1){
    $i = 0; foreach($_POST['copabw_from_val'] as $copabw_from_vals){ $i++; if($copabw_from_vals == ""){ $copabw_from_vals = 0; } $copabw_from_val[$i] = $copabw_from_vals; }
    $i = 0; foreach($_POST['copabw_to_val'] as $copabw_to_vals){ $i++; if($copabw_to_vals == ""){ $copabw_to_vals = 0; } $copabw_to_val[$i] = $copabw_to_vals; }
    $i = 0; foreach($_POST['copfcr_from_val'] as $copfcr_from_vals){ $i++; if($copfcr_from_vals == ""){ $copfcr_from_vals = 0; } $copfcr_from_val[$i] = $copfcr_from_vals; }
    $i = 0; foreach($_POST['copfcr_to_val'] as $copfcr_to_vals){ $i++; if($copfcr_to_vals == ""){ $copfcr_to_vals = 0; } $copfcr_to_val[$i] = $copfcr_to_vals; }
    $i = 0; foreach($_POST['copfcr_std_val'] as $copfcr_std_vals){ $i++; if($copfcr_std_vals == ""){ $copfcr_std_vals = 0; } $copfcr_std_val[$i] = $copfcr_std_vals; }
}

$si_from_avgwt = $si_to_avgwt = $avgwt_value = array(); $awti_max_prod_cost = $awti_sales_max_rate = 0;
if($sibabws_flag == 1){
    $sql='SHOW COLUMNS FROM `broiler_si_avgwts`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("awti_max_prod_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_si_avgwts` ADD `awti_max_prod_cost` DECIMAL(20,5) NULL DEFAULT NULL COMMENT 'Maximun Production Cost' AFTER `avgwt_value`"; mysqli_query($conn,$sql); }
    if(in_array("awti_sales_max_rate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_si_avgwts` ADD `awti_sales_max_rate` DECIMAL(20,5) NULL DEFAULT NULL COMMENT 'Maximun Production Cost' AFTER `awti_max_prod_cost`"; mysqli_query($conn,$sql); }
    
    $i = 0; foreach($_POST['si_from_avgwt'] as $si_from_avgwts){ $i++; if($si_from_avgwts == ""){ $si_from_avgwts = 0; } $si_from_avgwt[$i] = $si_from_avgwts; }
    $i = 0; foreach($_POST['si_to_avgwt'] as $si_to_avgwts){ $i++; if($si_to_avgwts == ""){ $si_to_avgwts = 0; } $si_to_avgwt[$i] = $si_to_avgwts; }
    $i = 0; foreach($_POST['avgwt_value'] as $avgwt_values){ $i++; if($avgwt_values == ""){ $avgwt_values = 0; } $avgwt_value[$i] = $avgwt_values; }

    $awti_max_prod_cost = $_POST['awti_max_prod_cost']; if($awti_max_prod_cost == ""){ $awti_max_prod_cost = 0; }
    $awti_sales_max_rate = $_POST['awti_sales_max_rate']; if($awti_sales_max_rate == ""){ $awti_sales_max_rate = 0; }
}

$smri_from_avgbd_wt = $smri_to_avgbd_wt = $smri_rate_inc = array(); $smri_incentive_on = $smri_grade = "";
if($smrincbdw_flag == 1){
    $smri_incentive_on = $_POST['smri_incentive_on'];
    foreach($_POST['smri_grades_to_consider'] as $sgrds){ if($smri_grade == ""){ $smri_grade = $sgrds; } else{ $smri_grade .= ",".$sgrds; } }
    $i = 0; foreach($_POST['smri_from_avgbd_wt'] as $smri_from_avgbd_wts){ $i++; if($smri_from_avgbd_wts == ""){ $smri_from_avgbd_wts = 0; } $smri_from_avgbd_wt[$i] = $smri_from_avgbd_wts; }
    $i = 0; foreach($_POST['smri_to_avgbd_wt'] as $smri_to_avgbd_wts){ $i++; if($smri_to_avgbd_wts == ""){ $smri_to_avgbd_wts = 0; } $smri_to_avgbd_wt[$i] = $smri_to_avgbd_wts; }
    $i = 0; foreach($_POST['smri_rate_inc'] as $smri_rate_incs){ $i++; if($smri_rate_incs == ""){ $smri_rate_incs = 0; } $smri_rate_inc[$i] = $smri_rate_incs; }
}

$sgc_from_avgwt = $sgc_to_avgwt = $sgc_std_cost = array();
if($stdgconavgwt_flag == 1){
    $i = 0; foreach($_POST['sgc_from_avgwt'] as $sgc_from_avgwts){ $i++; if($sgc_from_avgwts == ""){ $sgc_from_avgwts = 0; } $sgc_from_avgwt[$i] = $sgc_from_avgwts; }
    $i = 0; foreach($_POST['sgc_to_avgwt'] as $sgc_to_avgwts){ $i++; if($sgc_to_avgwts == ""){ $sgc_to_avgwts = 0; } $sgc_to_avgwt[$i] = $sgc_to_avgwts; }
    $i = 0; foreach($_POST['sgc_std_cost'] as $sgc_std_costs){ $i++; if($sgc_std_costs == ""){ $sgc_std_costs = 0; } $sgc_std_cost[$i] = $sgc_std_costs; }
}

$loyalty_grade = ""; $nof_old_batches = $loyalty_inc_rate = 0; $loyalty_incentive_on = "";
if($loyalinc_flag == 1){
    foreach($_POST['loyalty_grades_to_consider'] as $lgrds){ if($loyalty_grade == ""){ $loyalty_grade = $lgrds; } else{ $loyalty_grade .= ",".$lgrds; } }
    $nof_old_batches = $_POST['nof_old_batches']; if($nof_old_batches == ""){ $nof_old_batches = 0; }
    $loyalty_incentive_on = $_POST['loyalty_incentive_on'];
    $loyalty_inc_rate = $_POST['loyalty_inc_rate']; if($loyalty_inc_rate == ""){ $loyalty_inc_rate = 0; }
}

$season_grade = ""; $season_max_prod_cost = $season_inc_rate = 0; $season_incentive_on = "";
if($seasoninc_flag == 1){
    foreach($_POST['season_grades_to_consider'] as $lgrds){ if($season_grade == ""){ $season_grade = $lgrds; } else{ $season_grade .= ",".$lgrds; } }
    $season_max_prod_cost = $_POST['season_max_prod_cost']; if($season_max_prod_cost == ""){ $season_max_prod_cost = 0; }
    $season_incentive_on = $_POST['season_incentive_on'];
    $season_inc_rate = $_POST['season_inc_rate']; if($season_inc_rate == ""){ $season_inc_rate = 0; }
}

$prod_inc_sdtcop = $prod_from_inc = $prod_to_inc = $rate_inc = array();
if((int)$copbincdec_flag == 1){ $i = 0; foreach($_POST['prod_inc_sdtcop'] as $prod_inc_sdtcops){ $i++; if($prod_inc_sdtcops == ""){ $prod_inc_sdtcops = 0; } $prod_inc_sdtcop[$i] = $prod_inc_sdtcops; } }
$i = 0; foreach($_POST['prod_from_inc'] as $prod_from_incs){ $i++; if($prod_from_incs == ""){ $prod_from_incs = 0; } $prod_from_inc[$i] = $prod_from_incs; }
$i = 0; foreach($_POST['prod_to_inc'] as $prod_to_incs){ $i++; if($prod_to_incs == ""){ $prod_to_incs = 0; } $prod_to_inc[$i] = $prod_to_incs; }
$i = 0; foreach($_POST['rate_inc'] as $rate_incs){ $i++; if($rate_incs == ""){ $rate_incs = 0; } $rate_inc[$i] = $rate_incs; }

$sales_inc_grade = $sales_from_inc = $sales_to_inc = $sales_rate_inc = array(); $max_prod_cost = $sales_max_rate = 0;
$i = 0; foreach($_POST['sales_inc_grade'] as $sales_inc_grades){ $i++; $sales_inc_grade[$i] = $sales_inc_grades; }
$i = 0; foreach($_POST['sales_from_inc'] as $sales_from_incs){ $i++; if($sales_from_incs == ""){ $sales_from_incs = 0; } $sales_from_inc[$i] = $sales_from_incs; }
$i = 0; foreach($_POST['sales_to_inc'] as $sales_to_incs){ $i++; if($sales_to_incs == ""){ $sales_to_incs = 0; } $sales_to_inc[$i] = $sales_to_incs; }
$i = 0; foreach($_POST['sales_rate_inc'] as $sales_rate_incs){ $i++; if($sales_rate_incs == ""){ $sales_rate_incs = 0; } $sales_rate_inc[$i] = $sales_rate_incs; }
$max_prod_cost = $_POST['max_prod_cost']; if($max_prod_cost == ""){ $max_prod_cost = 0; }
$sales_max_rate = $_POST['sales_max_rate']; if($sales_max_rate == ""){ $sales_max_rate = 0; }

$mort_from_inc = $mort_to_inc = $mort_rate_inc = array(); $mi_grades = "";
if($mibong_flag == 1){ foreach($_POST['mi_grades'] as $mi_gradess){ if($mi_grades == ""){ $mi_grades = $mi_gradess; } else{ $mi_grades = $mi_grades.",".$mi_gradess; } } }
$i = 0; foreach($_POST['mort_from_inc'] as $mort_from_incs){ $i++; if($mort_from_incs == ""){ $mort_from_incs = 0; } $mort_from_inc[$i] = $mort_from_incs; }
$i = 0; foreach($_POST['mort_to_inc'] as $mort_to_incs){ $i++; if($mort_to_incs == ""){ $mort_to_incs = 0; } $mort_to_inc[$i] = $mort_to_incs; }
$i = 0; foreach($_POST['mort_rate_inc'] as $mort_rate_incs){ $i++; if($mort_rate_incs == ""){ $mort_rate_incs = 0; } $mort_rate_inc[$i] = $mort_rate_incs; }

$fcr_limit_inc = $body_weight_inc = $fcr_rate_inc = array();
$i = 0; foreach($_POST['fcr_limit_inc'] as $fcr_limit_incs){ $i++; if($fcr_limit_incs == ""){ $fcr_limit_incs = 0; } $fcr_limit_inc[$i] = $fcr_limit_incs; }
$i = 0; foreach($_POST['body_weight_inc'] as $body_weight_incs){ $i++; if($body_weight_incs == ""){ $body_weight_incs = 0; } $body_weight_inc[$i] = $body_weight_incs; }
$i = 0; foreach($_POST['fcr_rate_inc'] as $fcr_rate_incs){ $i++; if($fcr_rate_incs == ""){ $fcr_rate_incs = 0; } $fcr_rate_inc[$i] = $fcr_rate_incs; }

$wi_from_prod_cost = $wi_to_prod_cost = $wi_rate_inc = array(); $wi_min_prod_cost = $wi_max_prod_cost = 0; $wi_incentive_on = "";
if($winter_incv_flag == 1){
    $wi_min_prod_cost = $_POST['wi_min_prod_cost']; if($wi_min_prod_cost == ""){ $wi_min_prod_cost = 0; }
    $wi_max_prod_cost = $_POST['wi_max_prod_cost']; if($wi_max_prod_cost == ""){ $wi_max_prod_cost = 0; }
    $wi_incentive_on = $_POST['wi_incentive_on'];

    $i = 0; foreach($_POST['wi_from_prod_cost'] as $wi_from_prod_costs){ $i++; if($wi_from_prod_costs == ""){ $wi_from_prod_costs = 0; } $wi_from_prod_cost[$i] = $wi_from_prod_costs; }
    $i = 0; foreach($_POST['wi_to_prod_cost'] as $wi_to_prod_costs){ $i++; if($wi_to_prod_costs == ""){ $wi_to_prod_costs = 0; } $wi_to_prod_cost[$i] = $wi_to_prod_costs; }
    $i = 0; foreach($_POST['wi_rate_inc'] as $wi_rate_incs){ $i++; if($wi_rate_incs == ""){ $wi_rate_incs = 0; } $wi_rate_inc[$i] = $wi_rate_incs; }
}

$smr_incv_flag = 1; $si_from_prod_cost = $si_to_prod_cost = $si_rate_inc = array(); $si_min_prod_cost = $si_max_prod_cost = 0; $si_incentive_on = "";
if($smr_incv_flag == 1){
    $si_min_prod_cost = $_POST['si_min_prod_cost']; if($si_min_prod_cost == ""){ $si_min_prod_cost = 0; }
    $si_max_prod_cost = $_POST['si_max_prod_cost']; if($si_max_prod_cost == ""){ $si_max_prod_cost = 0; }
    $si_incentive_on = $_POST['si_incentive_on'];

    $i = 0; foreach($_POST['si_from_prod_cost'] as $si_from_prod_costs){ $i++; if($si_from_prod_costs == ""){ $si_from_prod_costs = 0; } $si_from_prod_cost[$i] = $si_from_prod_costs; }
    $i = 0; foreach($_POST['si_to_prod_cost'] as $si_to_prod_costs){ $i++; if($si_to_prod_costs == ""){ $si_to_prod_costs = 0; } $si_to_prod_cost[$i] = $si_to_prod_costs; }
    $i = 0; foreach($_POST['si_rate_inc'] as $si_rate_incs){ $i++; if($si_rate_incs == ""){ $si_rate_incs = 0; } $si_rate_inc[$i] = $si_rate_incs; }
}

$prod_dec_sdtcop = $prod_from_dec = $prod_to_dec = $prod_rate_dec = array();
if((int)$copbincdec_flag == 1){ $i = 0; foreach($_POST['prod_dec_sdtcop'] as $prod_dec_sdtcops){ $i++; if($prod_dec_sdtcops == ""){ $prod_dec_sdtcops = 0; } $prod_dec_sdtcop[$i] = $prod_dec_sdtcops; } }
$i = 0; foreach($_POST['prod_from_dec'] as $prod_from_decs){ $i++; if($prod_from_decs == ""){ $prod_from_decs = 0; } $prod_from_dec[$i] = $prod_from_decs; }
$i = 0; foreach($_POST['prod_to_dec'] as $prod_to_decs){ $i++; if($prod_to_decs == ""){ $prod_to_decs = 0; } $prod_to_dec[$i] = $prod_to_decs; }
$i = 0; foreach($_POST['prod_rate_dec'] as $prod_rate_decs){ $i++; if($prod_rate_decs == ""){ $prod_rate_decs = 0; } $prod_rate_dec[$i] = $prod_rate_decs; }
    
$week1_limit = $_POST['week1_limit']; if($week1_limit == ""){ $week1_limit = 0; }
$week1_above = $_POST['week1_above']; if($week1_above == ""){ $week1_above = 0; }
$week1_rate = $_POST['week1_rate']; if($week1_rate == ""){ $week1_rate = 0; }

$mort_from_dec = $mort_to_dec = $mort_rate_dec = array();
$i = 0; foreach($_POST['mort_from_dec'] as $mort_from_decs){ $i++; if($mort_from_decs == ""){ $mort_from_decs = 0; } $mort_from_dec[$i] = $mort_from_decs; }
$i = 0; foreach($_POST['mort_to_dec'] as $mort_to_decs){ $i++; if($mort_to_decs == ""){ $mort_to_decs = 0; } $mort_to_dec[$i] = $mort_to_decs; }
$i = 0; foreach($_POST['mort_rate_dec'] as $mort_rate_decs){ $i++; if($mort_rate_decs == ""){ $mort_rate_decs = 0; } $mort_rate_dec[$i] = $mort_rate_decs; }

$short_flag = $_POST['short_flag']; $sprod_flag = $prod_flag = $sale_flag = $max_srate_flag = $high_flag = 0;
if($short_flag == "standard_production_cost"){ $sprod_flag = 1; }
else if($short_flag == "production_cost"){ $prod_flag = 1; }
else if($short_flag == "sale_rate"){ $sale_flag = 1; }
else if($short_flag == "max_sale_rate"){ $max_srate_flag = 1; }
else if($short_flag == "which_is_high"){ $high_flag = 1; }
else{ }

if($shortage_maxbirds_flag == 1){
    $shortage_allowed_maxbirds = $_POST['shortage_allowed_maxbirds']; if($shortage_allowed_maxbirds == ""){ $shortage_allowed_maxbirds = 0; }
    $smab1 = ",shortage_allowed_maxbirds";
    $smab2 = ",'$shortage_allowed_maxbirds'";
}
else{
    $smab1 = $smab2 = "";
}

$fcr_limit_dec = $prod_limit_dec = $fcr_rate_dec = array();
$i = 0; foreach($_POST['fcr_limit_dec'] as $fcr_limit_decs){ $i++; if($fcr_limit_decs == ""){ $fcr_limit_decs = 0; } $fcr_limit_dec[$i] = $fcr_limit_decs; }
$i = 0; foreach($_POST['prod_limit_dec'] as $prod_limit_decs){ $i++; if($prod_limit_decs == ""){ $prod_limit_decs = 0; } $prod_limit_dec[$i] = $prod_limit_decs; }
$i = 0; foreach($_POST['fcr_rate_dec'] as $fcr_rate_decs){ $i++; if($fcr_rate_decs == ""){ $fcr_rate_decs = 0; } $fcr_rate_dec[$i] = $fcr_rate_decs; }

$prod_from_classify = $prod_to_classify = $grade_classify = array();
$i = 0; foreach($_POST['prod_from_classify'] as $prod_from_classifys){ $i++; if($prod_from_classifys == ""){ $prod_from_classifys = 0; } $prod_from_classify[$i] = $prod_from_classifys; }
$i = 0; foreach($_POST['prod_to_classify'] as $prod_to_classifys){ $i++; if($prod_to_classifys == ""){ $prod_to_classifys = 0; } $prod_to_classify[$i] = $prod_to_classifys; }
$i = 0; foreach($_POST['grade_classify'] as $grade_classifys){ $i++; if($grade_classifys == ""){ $grade_classifys = 0; } $grade_classify[$i] = $grade_classifys; }

$brh_arr_list = array();
if($branches == "all"){
    $sql = "SELECT * FROM `location_branch` WHERE `region_code` = '$region_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $brh_arr_list[$row['code']] = $row['code']; }
}
else{ $brh_arr_list[$branches] = $branches; }

foreach($brh_arr_list as $branch_code){
    $sql ="SELECT MAX(incr) as incr FROM `broiler_gc_standard`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
    $prefix = "GCS";
    
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $std_code = $code = $prefix."-".$incr;
    
    $sql = "INSERT INTO `broiler_gc_standard` (incr,prefix,code,region_code,branch_code,schema_name,from_date,to_date,chick_cost,feed_cost,maize_cost,medicine_cost,med_price,admin_cost,mgmt_admin_cost,standard_prod_cost,standard_cost,avgwt_upto,avgwt_gccost,minimum_cost,standard_fcr,standard_mortality,gcm_unl_charge,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$code','$region_code','$branch_code','$schema_name','$from_date','$to_date','$chick_cost','$feed_cost','$maize_cost','$medicine_cost','$med_price','$admin_cost','$mgmt_admin_cost','$standard_prod_cost','$standard_cost','$avgwt_upto','$avgwt_gccost','$minimum_cost','$standard_fcr','$standard_mortality','$gcm_unl_charge','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
    if($fcr_gc_flag == 1){
        for($j = 1;$j <= sizeof($fcrs_from_val);$j++){
            $sql = "INSERT INTO `broiler_gc_fcr_standards` (std_code,fcrs_from_val,fcrs_to_val,std_rates) VALUES ('$std_code','$fcrs_from_val[$j]','$fcrs_to_val[$j]','$std_rates[$j]')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
    
    if($cfcr_gc_flag == 1){
        for($j = 1;$j <= sizeof($cfcrs_from_val);$j++){
            $sql = "INSERT INTO `broiler_gc_cfcr_standards` (std_code,cfcrs_from_val,cfcrs_to_val,ngc_rate,cfcr_from_wht,cfcr_to_wht,sbgc_rate) VALUES ('$std_code','$cfcrs_from_val[$j]','$cfcrs_to_val[$j]','$ngc_rate[$j]','$cfcr_from_wht[$j]','$cfcr_to_wht[$j]','$sbgc_rate[$j]')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
    
    if($copabwfcr_flag == 1){
        for($j = 1;$j <= sizeof($copabw_from_val);$j++){
            if($copabw_from_val[$j] == "0" && $copabw_to_val[$j] == "0" && $copfcr_from_val[$j] == "0" && $copfcr_to_val[$j] == "0" && $copfcr_std_val[$j] == "0"){ }
            else{
                $sql = "INSERT INTO `broiler_gc_cop_standards` (std_code,copabw_from_val,copabw_to_val,copfcr_from_val,copfcr_to_val,copfcr_std_val) VALUES ('$std_code','$copabw_from_val[$j]','$copabw_to_val[$j]','$copfcr_from_val[$j]','$copfcr_to_val[$j]','$copfcr_std_val[$j]')";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            }
        }
    }
    
    if($sibabws_flag == 1){
        for($j = 1;$j <= sizeof($si_from_avgwt);$j++){
            if($si_from_avgwt[$j] == "0" && $si_to_avgwt[$j] == "0" && $avgwt_value[$j] == "0"){ }
            else{
                $sql = "INSERT INTO `broiler_si_avgwts` (std_code,si_from_avgwt,si_to_avgwt,avgwt_value,awti_max_prod_cost,awti_sales_max_rate) VALUES ('$std_code','$si_from_avgwt[$j]','$si_to_avgwt[$j]','$avgwt_value[$j]','$awti_max_prod_cost','$awti_sales_max_rate')";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            }
        }
    }
    
    if($smrincbdw_flag == 1){
        for($j = 1;$j <= sizeof($smri_from_avgbd_wt);$j++){
            if($smri_from_avgbd_wt[$j] == "0" && $smri_to_avgbd_wt[$j] == "0" && $smri_rate_inc[$j] == "0"){ }
            else{
                $sql = "INSERT INTO `broiler_gc_smrbdw_incentive` (std_code,smri_grades_to_consider,smri_incentive_on,smri_from_avgbd_wt,smri_to_avgbd_wt,smri_rate_inc) VALUES ('$std_code','$smri_grade','$smri_incentive_on','$smri_from_avgbd_wt[$j]','$smri_to_avgbd_wt[$j]','$smri_rate_inc[$j]')";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            }
        }
    }
    
    if($stdgconavgwt_flag == 1){
        for($j = 1;$j <= sizeof($sgc_from_avgwt);$j++){
            if($sgc_from_avgwt[$j] == "0" && $sgc_to_avgwt[$j] == "0" && $sgc_std_cost[$j] == "0"){ }
            else{
                $sql = "INSERT INTO `broiler_gc_sgc_standards` (std_code,sgc_from_avgwt,sgc_to_avgwt,sgc_std_cost) VALUES ('$std_code','$sgc_from_avgwt[$j]','$sgc_to_avgwt[$j]','$sgc_std_cost[$j]')";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            }
        }
    }
    
    if($loyalinc_flag == 1){
        if($loyalty_inc_rate == "0"){ }
        else{
            $sql = "INSERT INTO `broiler_gc_loyalty_incentive` (std_code,loyalty_grades_to_consider,nof_old_batches,loyalty_incentive_on,loyalty_inc_rate) VALUES ('$std_code','$loyalty_grade','$nof_old_batches','$loyalty_incentive_on','$loyalty_inc_rate')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
    
    if($seasoninc_flag == 1){
        if($season_inc_rate == "0"){ }
        else{
            $sql = "INSERT INTO `broiler_gc_seasonal_incentive` (std_code,season_max_prod_cost,season_grades_to_consider,season_incentive_on,season_inc_rate) VALUES ('$std_code','$season_max_prod_cost','$loyalty_grade','$season_incentive_on','$season_inc_rate')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
    
    for($j = 1;$j <= sizeof($prod_from_inc);$j++){
        $sql = "INSERT INTO `broiler_gc_pc_incentive` (std_code,std_prod_cost,prod_from_inc,prod_to_inc,rate_inc) VALUES ('$std_code','$prod_inc_sdtcop[$j]','$prod_from_inc[$j]','$prod_to_inc[$j]','$rate_inc[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
    for($j = 1;$j <= sizeof($sales_rate_inc);$j++){
        $sql = "INSERT INTO `broiler_gc_si_incentive` (std_code,sales_inc_grade,sales_from_inc,sales_to_inc,sales_rate_inc,sales_max_rate,max_prod_cost) VALUES ('$std_code','$sales_inc_grade[$j]','$sales_from_inc[$j]','$sales_to_inc[$j]','$sales_rate_inc[$j]','$sales_max_rate','$max_prod_cost')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
    for($j = 1;$j <= sizeof($mort_rate_inc);$j++){
        $sql = "INSERT INTO `broiler_gc_mi_incentive` (std_code,mi_grades,mort_from_inc,mort_to_inc,mort_rate_inc) VALUES ('$std_code','$mi_grades','$mort_from_inc[$j]','$mort_to_inc[$j]','$mort_rate_inc[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
    for($j = 1;$j <= sizeof($fcr_rate_inc);$j++){
        $sql = "INSERT INTO `broiler_gc_fcr_incentive` (std_code,fcr_limit_inc,body_weight_inc,fcr_rate_inc) VALUES ('$std_code','$fcr_limit_inc[$j]','$body_weight_inc[$j]','$fcr_rate_inc[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
    if($winter_incv_flag == 1){
        for($j = 1;$j <= sizeof($wi_rate_inc);$j++){
            $sql = "INSERT INTO `broiler_gc_wi_incentive` (std_code,prod_cost_from,prod_cost_to,incentive_on,incentive_rate,min_prod_cost,max_prod_cost) VALUES ('$std_code','$wi_from_prod_cost[$j]','$wi_to_prod_cost[$j]','$wi_incentive_on','$wi_rate_inc[$j]','$wi_min_prod_cost','$wi_max_prod_cost')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
    
    if($smr_incv_flag == 1){
        for($j = 1;$j <= sizeof($si_rate_inc);$j++){
            $sql = "INSERT INTO `broiler_gc_smr_incentive` (std_code,prod_cost_from,prod_cost_to,incentive_on,incentive_rate,min_prod_cost,max_prod_cost) VALUES ('$std_code','$si_from_prod_cost[$j]','$si_to_prod_cost[$j]','$si_incentive_on','$si_rate_inc[$j]','$si_min_prod_cost','$si_max_prod_cost')";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        }
    }
    
    for($j = 1;$j <= sizeof($prod_rate_dec);$j++){
        $sql = "INSERT INTO `broiler_gc_pc_decentive` (std_code,std_prod_cost,prod_from_dec,prod_to_dec,prod_rate_dec) VALUES ('$std_code','$prod_dec_sdtcop[$j]','$prod_from_dec[$j]','$prod_to_dec[$j]','$prod_rate_dec[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
    $sql = "INSERT INTO `broiler_gc_mi_decentive` (std_code,weeks,mort_from_dec,mort_to_dec,mort_rate_dec) VALUES ('$std_code','LDO','$week1_limit','$week1_above','$week1_rate')";
    if(!mysqli_query($conn,$sql)){ die("Error:-1".mysqli_error($conn)); } else { }
    
    for($j = 1;$j <= sizeof($mort_rate_dec);$j++){
        $sql = "INSERT INTO `broiler_gc_mi_decentive` (std_code,weeks,mort_from_dec,mort_to_dec,mort_rate_dec) VALUES ('$std_code','GDO','$mort_from_dec[$j]','$mort_to_dec[$j]','$mort_rate_dec[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-2".mysqli_error($conn)); } else { }
    }
    
    $sql = "INSERT INTO `broiler_gc_st_decentive` (std_code,sprod_flag,prod_flag,sale_flag,max_srate_flag,high_flag".$smab1.") VALUES ('$std_code','$sprod_flag','$prod_flag','$sale_flag','$max_srate_flag','$high_flag'".$smab2.")";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
    for($j = 1;$j <= sizeof($fcr_rate_dec);$j++){
        $sql = "INSERT INTO `broiler_gc_fcr_decentive` (std_code,fcr_limit_dec,prod_limit_dec,fcr_rate_dec) VALUES ('$std_code','$fcr_limit_dec[$j]','$prod_limit_dec[$j]','$fcr_rate_dec[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
    for($j = 1;$j <= sizeof($prod_to_classify);$j++){
        $sql = "INSERT INTO `broiler_farmer_classify` (std_code,prod_from_classify,prod_to_classify,grade_classify) VALUES ('$std_code','$prod_from_classify[$j]','$prod_to_classify[$j]','$grade_classify[$j]')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}

header('location:broiler_display_rearingcharge.php?ccid='.$ccid);
?>