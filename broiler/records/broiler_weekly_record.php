<?php
//broiler_weekly_record.php
$requested_data = json_decode(file_get_contents('php://input'),true);

if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else { $db = ''; }
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Weekly Batch Summary Report";
    include "header_head.php";
    $tblchk_dbname = $_SESSION['dbase'];
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Weekly Batch Summary Report";
    include "header_head.php";
    $tblchk_dbname = $_SESSION['db'];
    $user_code = $_GET['userid'];
}

/*Check for Table Availability*/
$database_name = $tblchk_dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }

$tblchk_tblname = "Tables_in_".$tblchk_dbname;
$sqlt = "SHOW TABLES;"; $queryt = mysqli_query($conn,$sqlt);
while($rowt = mysqli_fetch_array($queryt)){
    if($rowt[$tblchk_tblname] == "acc_category"){$count1 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_coa"){$count2 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_controltype"){$count3 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_modes"){$count4 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_schedules"){$count5 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_types"){$count6 = 1; }
    else if($rowt[$tblchk_tblname] == "account_contranotes"){$count7 = 1; }
    else if($rowt[$tblchk_tblname] == "account_summary"){$count8 = 1; }
    else if($rowt[$tblchk_tblname] == "account_vouchers"){$count9 = 1; }
    else if($rowt[$tblchk_tblname] == "app_permissions"){$count10 = 1; }
    else if($rowt[$tblchk_tblname] == "authorize"){$count11 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch"){$count12 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch_bkp"){$count13 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch_bkp1"){$count14 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_breed"){$count15 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_breedstandard"){$count16 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_crdrnote"){$count17 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_daily_record"){$count18 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_daily_record_unsaved"){$count19 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_designation"){$count20 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_diseases"){$count21 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_doctorvisit"){$count22 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_egg_grading_consume"){$count23 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_egg_grading_produce"){$count24 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_employee"){$count25 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farm"){$count26 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmer"){$count27 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmer_classify"){$count28 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmergroup"){$count29 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_consumed"){$count30 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_expense"){$count31 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_formula"){$count32 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_production"){$count33 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_silos"){$count34 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_decentive"){$count35 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_incentive"){$count36 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_production"){$count37 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_mi_decentive"){$count38 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_mi_incentive"){$count39 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_pc_decentive"){$count40 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_pc_incentive"){$count41 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_si_incentive"){$count42 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_st_decentive"){$count43 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_standard"){$count44 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchentry"){$count45 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery"){$count46 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_consumed"){$count47 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_expense"){$count48 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_hatcher"){$count49 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_setter"){$count50 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_adjustment"){$count51 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_intermediate_issued"){$count52 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_intermediate_received"){$count53 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_itemreturns"){$count54 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_lab_results"){$count55 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_max_values"){$count56 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_medicine_record"){$count57 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_openings"){$count58 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_payments"){$count59 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_placementplan"){$count60 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_purchases"){$count61 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_rearingcharge"){$count62 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_receipts"){$count63 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_reportfields"){$count64 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_sales"){$count65 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_tray_settings"){$count66 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_vaccineschedule"){$count67 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_vehicle"){$count68 = 1; }
    else if($rowt[$tblchk_tblname] == "company_names"){$count69 = 1; }
    else if($rowt[$tblchk_tblname] == "company_price_list"){$count70 = 1; }
    else if($rowt[$tblchk_tblname] == "country_states"){$count71 = 1; }
    else if($rowt[$tblchk_tblname] == "customer_price"){$count72 = 1; }
    else if($rowt[$tblchk_tblname] == "customer_sales"){$count73 = 1; }
    else if($rowt[$tblchk_tblname] == "dataentry_daterange"){$count74 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_sal_generator"){$count75 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_sal_payment"){$count76 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_salary_components"){$count77 = 1; }
    else if($rowt[$tblchk_tblname] == "extra_access"){$count78 = 1; }
    else if($rowt[$tblchk_tblname] == "farm_check_list_record"){$count79 = 1; }
    else if($rowt[$tblchk_tblname] == "farmer_item_price"){$count80 = 1; }
    else if($rowt[$tblchk_tblname] == "feed_bagcapacity"){$count81 = 1; }
    else if($rowt[$tblchk_tblname] == "feedindent"){$count82 = 1; }
    else if($rowt[$tblchk_tblname] == "feedmill_expenses_parameters"){$count83 = 1; }
    else if($rowt[$tblchk_tblname] == "gateway_masters"){$count84 = 1; }
    else if($rowt[$tblchk_tblname] == "gateway_paymentlinks"){$count85 = 1; }
    else if($rowt[$tblchk_tblname] == "inv_sectors"){$count86 = 1; }
    else if($rowt[$tblchk_tblname] == "item_category"){$count87 = 1; }
    else if($rowt[$tblchk_tblname] == "item_closingstock"){$count88 = 1; }
    else if($rowt[$tblchk_tblname] == "item_details"){$count89 = 1; }
    else if($rowt[$tblchk_tblname] == "item_qty_conversion"){$count90 = 1; }
    else if($rowt[$tblchk_tblname] == "item_stocktransfers"){$count91 = 1; }
    else if($rowt[$tblchk_tblname] == "item_units"){$count92 = 1; }
    else if($rowt[$tblchk_tblname] == "location_branch"){$count93 = 1; }
    else if($rowt[$tblchk_tblname] == "location_line"){$count94 = 1; }
    else if($rowt[$tblchk_tblname] == "location_region"){$count95 = 1; }
    else if($rowt[$tblchk_tblname] == "main_access"){$count96 = 1; }
    else if($rowt[$tblchk_tblname] == "main_companyprofile"){$count97 = 1; }
    else if($rowt[$tblchk_tblname] == "main_contactdetails"){$count98 = 1; }
    else if($rowt[$tblchk_tblname] == "main_dailypaperrate"){$count99 = 1; }
    else if($rowt[$tblchk_tblname] == "main_disclaimer"){$count100 = 1; }
    else if($rowt[$tblchk_tblname] == "Tables_in_poulso6_admin_broiler_broilermaster"){$count101 = 1; }
    else if($rowt[$tblchk_tblname] == "main_financialyear"){$count102 = 1; }
    else if($rowt[$tblchk_tblname] == "main_groups"){$count103 = 1; }
    else if($rowt[$tblchk_tblname] == "main_jals"){$count104 = 1; }
    else if($rowt[$tblchk_tblname] == "main_linkdetails"){$count105 = 1; }
    else if($rowt[$tblchk_tblname] == "main_mortality"){$count106 = 1; }
    else if($rowt[$tblchk_tblname] == "main_officetypes"){$count107 = 1; }
    else if($rowt[$tblchk_tblname] == "main_tcds"){$count108 = 1; }
    else if($rowt[$tblchk_tblname] == "main_terms"){$count109 = 1; }
    else if($rowt[$tblchk_tblname] == "master_dashboard_links"){$count110 = 1; }
    else if($rowt[$tblchk_tblname] == "master_farm_checklist"){$count111 = 1; }
    else if($rowt[$tblchk_tblname] == "master_formfields"){$count112 = 1; }
    else if($rowt[$tblchk_tblname] == "master_generator"){$count113 = 1; }
    else if($rowt[$tblchk_tblname] == "master_item_parameter"){$count114 = 1; }
    else if($rowt[$tblchk_tblname] == "master_itemfields"){$count115 = 1; }
    else if($rowt[$tblchk_tblname] == "master_parameters"){$count116 = 1; }
    else if($rowt[$tblchk_tblname] == "master_reportfields"){$count117 = 1; }
    else if($rowt[$tblchk_tblname] == "message_master"){$count118 = 1; }
    else if($rowt[$tblchk_tblname] == "mobile_user_rights"){$count119 = 1; }
    else if($rowt[$tblchk_tblname] == "prefix_master"){$count120 = 1; }
    else if($rowt[$tblchk_tblname] == "price_master"){$count121 = 1; }
    else if($rowt[$tblchk_tblname] == "pur_purchase"){$count122 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_count"){$count123 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_details"){$count124 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_master"){$count125 = 1; }
    else if($rowt[$tblchk_tblname] == "tax_details"){$count126 = 1; }
    else if($rowt[$tblchk_tblname] == "trip_sheet"){$count127 = 1; }
    else if($rowt[$tblchk_tblname] == "upi_types"){$count128 = 1; }
    else{ }
}
$bodywt_p1days = 1;


$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

if($count78 > 0){
    $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Day Entry' AND `field_function` LIKE 'Weekly take plus 1 days Body Wt' AND `user_access` LIKE 'all'";
    $query = mysqli_query($conn,$sql); $qcount = mysqli_num_rows($query);
    if($qcount > 0){
        while($row = mysqli_fetch_assoc($query)){ $bodywt_p1days = (int)$row['flag']; }
    }
    else{
        $sql = "INSERT INTO `extra_access` (`id`, `field_name`, `field_function`, `user_access`, `flag`) VALUES (NULL, 'Day Entry', 'Weekly take plus 1 days Body Wt', 'all', '1');";
        mysqli_query($conn,$sql); $bodywt_p1days = 1;
    }
}

if($count95 > 0){
    $sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }
}
if($count93 > 0){
    $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }
}
if($count94 > 0){
    $sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

}
if($count86 > 0){
    $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
}
if($count98 > 0){
    $sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_name[$row['code']] = $row['name']; }
}
if($count26 > 0){
    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];  $sector_name[$row['code']] = $row['description'];
        $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
        $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
        $farm_farmer[$row['code']] = $row['farmer_code'];
    }
}
if($count12 > 0){
    $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }
}
if($count16 > 0){
    $sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; }
}
if($count20 > 0){
    $sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
    while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
}
if($count25 > 0){
    $sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
    while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }
}
if($count89 > 0){
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $chick_cat = $row['category']; $chick_code = $row['code']; }
}
if($count89 > 0){
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }
}
if($count87 > 0){
    $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $icat_iac = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
}
if($count89 > 0){
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }
}
if($count87 > 0){
    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
}
if($count87 > 0){
    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
}
if($count89 > 0){
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
}
$fdate = $tdate = ""; $regions = $branches = $lines = $supervisors = $farms = "all"; $chk_age = ""; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    if($_POST['fdate'] == ""){ echo $fdate = ""; } else{ $fdate = date("Y-m-d",strtotime($_POST['fdate'])); }
    if($_POST['tdate'] == ""){ echo $tdate = ""; } else{ $tdate = date("Y-m-d",strtotime($_POST['tdate'])); }
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $chk_age = $_POST['chk_age'];
    $batch_type = $_POST['batchtype'];
     $regions = $_POST['regions'];
    if($batch_type == 'CB'){
        $gc_flag = 1;
        $batch_type = 'CB';
    }else{
        $gc_flag = 0;
        $batch_type = 'LB';
    }
    $export_Branch = $branch_name[$_POST['branches']]; 
    if ( $export_Branch == "" || $export_Branch == "all") { $export_Branch = "All"; }
    $export_Line = $line_name[$_POST['lines']];
    if ( $export_Line == "" || $export_Line == "all") { $export_Line = "All"; }
    $export_Supervisor = $supervisor_name[$_POST['supervisors']];
    if ( $export_Supervisor == "" || $export_Supervisor == "all") { $export_Supervisor = "All"; }
    $export_Farm = $farm_name[$_POST['farms']];
    if ( $export_Farm == "" || $export_Farm == "all") { $export_Farm = "All"; }
    $export_Age = $_POST['chk_age'];
    $export_Batch_type = $_POST['batchtype'];
    if ($export_Batch_type = "CB") {$export_Batch_type = "Culled Batch"; }
    if ($export_Batch_type = "LB") {$export_Batch_type = "Live Batch"; }

    $export_vendors = $sector_name[$_POST['vendors']]; if ( $export_vendors == "") {  $export_vendors = "All"; }

     $farm_query = "";
    if($regions != "all"){
        $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
        $rbrh_list = implode("','",$rbrh_alist);
        $farm_query .= " AND `branch_code` IN ('$rbrh_list')";
    }

      $farm_list = ""; $farm_list = implode("','", $farm_code);
    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_query." AND `dflag` = '0' ORDER BY `description` ASC";

    $query = mysqli_query($conn,$sql); $farm_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }
    
    
    $farm_list = implode("','",$farm_alist);
    if($farms != "all"){
        $farm_list = $farms;
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($supervisors != "all" && $lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors && $farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($supervisors != "all" && $branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($supervisors != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($lines != "all" && $branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
         $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else{
        foreach($farm_code as $fcode){
            if($farm_list == ""){
                $farm_list = $fcode;
            }
            else{
                $farm_list = $farm_list;
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    $excel_type = $_POST['export'];
    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];

    if ($export_fdate == $export_tdate)
    {$filename = "Weekly Batch Summary_".$export_tdate; }
     else {
    $filename = "Weekly Batch Summary_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];
    //$url = "../PHPExcel/Examples/BroilerWeeklyReport-Excel.php?branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms;
}
else{
    $url = "";
}
?>
<html>
    <head>
    <title>Poulsoft Solutions</title>
    <link href="../datepicker/jquery-ui.css" rel="stylesheet">
    <?php if($excel_type != "print"){ ?>
       <!-- jQuery Library -->
       <script src="../../col/jquery-3.5.1.js"></script>
        <!-- Datatable JS -->
        <script src="../../col/jquery.dataTables.min.js"></script>
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
            table thead,
            table tfoot {
            position: sticky;
            }
            table thead {
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
        </style>
<?php } ?>
        <?php
        if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#D5D8DC,#D5D8DC); }
            .thead2_empty_row { display:none; }
            .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
            .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
        }
        else{
            echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
            table.tbl { left:0;margin-right: auto;visibility:visible; }
            table.tbl2 { left:0;margin-right: auto; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
        }
        ?>
    </head>
    <body>
        <table class="tbl" align="center" style="width:auto;">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Weekly Batch Summary Report</h5></th>
                    <th colspan="19" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
             <?php if($db == ''){?>
            <form action="broiler_weekly_record.php" method="post">
                <?php } else { ?>
                <form action="broiler_weekly_record.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="31">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Placement From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php if(strtotime($fdate) == ""){ } else{ echo date("d.m.Y",strtotime($fdate)); } ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Placement To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php if(strtotime($tdate) == ""){ } else{ echo date("d.m.Y",strtotime($tdate)); } ?>" />
                                </div>
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
                                    <select name="branches" id="branches" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != "" && !empty($farm_svr[$scode])){ ?>
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
                                    <label>Batch Type</label>
                                    <select name="batchtype" id="batchtype" class="form-control select2">
                                        <option value="LB" <?php if($batch_type == "LB"){ echo "selected"; } ?>>Live Batch</option>
                                        <option value="CB" <?php if($batch_type == "CB"){ echo "selected"; } ?>>Culled Batch</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Age</label>
                                    <input type="text" name="chk_age" id="chk_age" class="form-control" style="width:60px;" value="<?php echo $chk_age; ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Weekly Batch Summary_','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
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
            <?php if($excel_type != "print"){ ?>
            <table id="main_body" class="tbl" align="center"  style="width:1300px;">
                <?php } ?>
                <div class="row" style="padding-left:100px;">
                    <div class="m-2 form-group">
                        <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                        <br/>
                    </div>
                </div>
                <thead class="thead1" align="center" style="width:1212px;  display:none; ">
                    <?php
                    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                    ?>
                    <tr align="center">
                        <th colspan="31" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Weekly Batch Summary Report</h5></th>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="31">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Placement From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Placement To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch: <?php echo $export_Branch; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line: <?php echo $export_Line; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor: <?php echo $export_Supervisor; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm: <?php echo $export_Farm; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Batch Type: <?php echo $export_Batch_type; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Age: <?php echo $export_Age; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label><br/></label>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <thead class="thead3" align="center">
                    <tr align="center">
                        <th>Sl.No.</th>
                        <th>Branch</th>
                        <th>Hatchery</th>
                        <th>Supplier</th>
                        <th>Line</th>
                        <th>Supervisor</th>
                        <th>Farm</th>
                        <th>Batch</th>
                        <th>Book No</th>
                        <th>Placement Date</th>
                        <th>Age</th>
                        <th>Date</th>
                        <th>Opening Birds</th>
                        <th>Mortality</th>
                        <th>Mortality %</th>
                        <th>Birds Sold</th>
                        <th>Birds Sold Weight</th>
                        <th>Balance Birds</th>
                        <th>Std Feed Consumption</th>
                        <th>Actual Feed Consuption</th>
                        <th>Std Body Weight</th>
                        <th>Actual Body Weight</th>
                        <th>Feed/Bird</th>
                        <th>Std Day Gain</th>
                        <th>Actual Day Gain</th>
                        <th>Std FCR</th>
                        <th>FCR</th>
                        <th>Diff in FCR</th>
                        <th>Act Feed Conversation Bwt</th>
                        <th>Diff Bwt Conversation</th>
                        <th>Act CFCR</th>
                    </tr>
                </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
                //Fetch Hatchery and Supplier Details-1
                $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `gc_flag` = '0' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $gbch_code = array();
                while($row = mysqli_fetch_assoc($query)){ $gbch_code[$row['code']] = $row['code']; }
                $batch_list = implode("','",$gbch_code);

                $chick_coa = $icat_iac[$chick_cat];
                $sql = "SELECT MIN(`date`) as `sdate`,MAX(`date`) as `edate` FROM `account_summary` WHERE `crdr` LIKE 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $hsdate = $hedate = "";
                while($row = mysqli_fetch_assoc($query)){ $hsdate = $row['sdate']; $hedate = $row['edate']; }

                $hatch_count = $pur_count = 0;
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

                $bag_size = 50; $slno = 0;
                $batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,c.description as branch_name FROM broiler_batch a,broiler_farm b,location_branch c WHERE a.farm_code = b.code and b.branch_code = c.code  AND a.gc_flag = '$gc_flag' AND a.active = '1' AND a.dflag = '0'  $farm_query ORDER BY `branch_name`,`farm_name` ASC"; $batch_query = mysqli_query($conn,$batch_sql);
                while($batch_row = mysqli_fetch_assoc($batch_query)){
                    $batches = $batch_row['batch_code'];
                    $fetch_fcode = $batch_row['farm_code'];
                    if($batches != ""){
                        $start_date = $end_date = $dend_date = $dstart_date = $mort_image = $addedemp = $addedtime = $latitude = $longitude = "";
                        $pur_qty = $sale_qty = $sold_birds = $trin_qty = $trout_qty = $medvac_qty = array();
                        $pur_chicks = $sale_chicks = $trin_chicks = $trout_chicks = $dentry_chicks = $medvac_chicks = $chkin_vcode = $chkin_hcode = array();
                        $placed_in = 0;
                        $sql_record = "SELECT * FROM `broiler_purchases` WHERE `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['icode']."@".$i;
                            $pur_qty[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                            $i++;
                            if($row['icode'] == $chick_code){
                                $placed_in += (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                                $chkin_vcode[$row['farm_batch']] = $row['vocde'];
                            }
                        }
                        $sql_record = "SELECT * FROM `broiler_sales` WHERE `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['icode']."@".$i;
                            $sold_birds[$key_code] = (float)$row['birds'];
                            $sale_qty[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                            $i++;
                        }
                        $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['code']."@".$i;
                            $trin_qty[$key_code] = (float)$row['quantity'];
                            $i++;
                            if($row['code'] == $chick_code){
                                $placed_in += (float)$row['quantity'];
                                $chkin_hcode[$row['to_batch']] = $row['fromwarehouse'];
                            }

                            //Fetch Hatchery and Supplier Details-2
                            $ldate = $lsector = $lincr = "";
                            if($hatch_count > 0 && $row['code'] == $chick_code){
                                foreach($hatch_keyset as $key1){
                                    $key2 = explode("@",$key1);
                                    $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                    if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                        if($ldate == ""){
                                            $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                        }
                                        else if(strtotime($ldate) < strtotime($hdate)){
                                            $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                        }
                                    }
                                }
                                if($ldate == "" && $lsector == "" && $lincr == ""){ }
                                else{
                                    $hkey = $ldate."@".$lsector."@".$lincr;
                                    if(empty($hatch_vcode[$hkey]) || $hatch_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                    else{
                                        $chkin_vcode[$row['to_batch']] = $hatch_vcode[$hkey];
                                    }
                                }
                            }

                            if(empty($chkin_vcode[$row['to_batch']]) || $chkin_vcode[$row['to_batch']] == ""){
                                if($pur_count > 0 && $row['code'] == $chick_code){
                                    $ldate = $lsector = $lincr = "";
                                    foreach($pur_keyset as $key1){
                                        $key2 = explode("@",$key1);
                                        $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                        if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                            if($ldate == ""){
                                                $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                            }
                                            else if(strtotime($ldate) < strtotime($hdate)){
                                                $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                            }
                                        }
                                    }
                                    if($ldate == "" && $lsector == "" && $lincr == ""){ }
                                    else{
                                        $hkey = $ldate."@".$lsector."@".$lincr;
                                        if(empty($pur_vcode[$hkey]) || $pur_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                        else{
                                            $chkin_vcode[$row['to_batch']] = $pur_vcode[$hkey];
                                        }
                                    }
                                }
                            }
                        }
                        $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['code']."@".$i;
                            $trout_qty[$key_code] = (float)$row['quantity'];
                            $i++;
                        }
                        $brood_ages = array();
                        $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date'];
                            $dentry_chicks[$key_code] = $row['trnum']."@".$row['supervisor_code']."@".$row['date']."@".$row['farm_code']."@".$row['batch_code']."@".$row['brood_age']."@".$row['mortality']."@".$row['culls']."@".$row['item_code1']."@".$row['kgs1']."@".$row['item_code2']."@".$row['kgs2']."@".$row['avg_wt']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime'];
                            
                            if($bodywt_p1days == 1 || $bodywt_p1days == "1"){
                                if((int)$row['brood_age'] >= 1 && (int)$row['brood_age'] <= 8){ if((float)$row['avg_wt'] > 0){ for($j = 1;$j <= 7;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 9 && (int)$row['brood_age'] <= 15){ if((float)$row['avg_wt'] > 0){ for($j = 8;$j <= 14;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 16 && (int)$row['brood_age'] <= 22){ if((float)$row['avg_wt'] > 0){ for($j = 15;$j <= 21;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 23 && (int)$row['brood_age'] <= 29){ if((float)$row['avg_wt'] > 0){ for($j = 22;$j <= 28;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 30 && (int)$row['brood_age'] <= 36){ if((float)$row['avg_wt'] > 0){ for($j = 29;$j <= 35;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 37 && (int)$row['brood_age'] <= 43){ if((float)$row['avg_wt'] > 0){ for($j = 36;$j <= 42;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 44 && (int)$row['brood_age'] <= 50){ if((float)$row['avg_wt'] > 0){ for($j = 43;$j <= 49;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 51 && (int)$row['brood_age'] <= 57){ if((float)$row['avg_wt'] > 0){ for($j = 50;$j <= 56;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 58 && (int)$row['brood_age'] <= 64){ if((float)$row['avg_wt'] > 0){ for($j = 57;$j <= 63;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                            }
                            else{
                                if((int)$row['brood_age'] >= 1 && (int)$row['brood_age'] <= 7){ if((float)$row['avg_wt'] > 0){ for($j = 1;$j <= 7;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 8 && (int)$row['brood_age'] <= 14){ if((float)$row['avg_wt'] > 0){ for($j = 8;$j <= 14;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 15 && (int)$row['brood_age'] <= 21){ if((float)$row['avg_wt'] > 0){ for($j = 15;$j <= 21;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 22 && (int)$row['brood_age'] <= 28){ if((float)$row['avg_wt'] > 0){ for($j = 22;$j <= 28;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 29 && (int)$row['brood_age'] <= 35){ if((float)$row['avg_wt'] > 0){ for($j = 29;$j <= 35;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 36 && (int)$row['brood_age'] <= 42){ if((float)$row['avg_wt'] > 0){ for($j = 36;$j <= 42;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 43 && (int)$row['brood_age'] <= 49){ if((float)$row['avg_wt'] > 0){ for($j = 43;$j <= 49;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 50 && (int)$row['brood_age'] <= 56){ if((float)$row['avg_wt'] > 0){ for($j = 50;$j <= 56;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                                if((int)$row['brood_age'] >= 57 && (int)$row['brood_age'] <= 63){ if((float)$row['avg_wt'] > 0){ for($j = 57;$j <= 63;$j++){ $brood_ages[$j] = (float)$row['avg_wt']; } } }
                            }
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_medicine_record` WHERE `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['item_code']."@".$i;
                            $medvac_qty[$key_code] = (float)$row['quantity'];
                            $i++;
                        }
                    ?>
                        <tbody class="tbody1">
                              
                                <?php
                                    $display_week = 0;
                                    $display_date = 0;
                                    $display_age = 0;
                                    $display_obirds = 0;
                                    $display_mort = 0;
                                    $display_mort_per = 0;
                                    $display_mort_cum = 0;
                                    $display_mort_cum_per = 0;
                                    $display_sold_birds = 0;
                                    $display_sold_weight = 0;
                                    $display_closed_birds = 0;
                                    $display_feed_in = 0;
                                    $display_feed_consume = 0;
                                    $display_feed_cum = 0;
                                    $display_feed_closed = 0;
                                    $display_std_feed_per_bird = 0;
                                    $display_actual_feed_per_bird = 0;
                                    $display_cum_feed_per_bird = 0;
                                    $display_std_avg_weight = 0;
                                    $display_actual_avg_weight = 0;
                                    $display_std_fcr = 0;
                                    $display_actual_fcr = 0;
                                    $display_avg_wt =  0;
                                    
                                    $farm_in_chicks = $farm_out_birds = $farm_out_weight = $farm_in_feeds = $farm_consume_medvacs = $feed_consume = $farm_out_feeds = $count = 0;

                                    $pur_size = sizeof($pur_qty);
                                    $trin_size = sizeof($trin_qty);
                                    $sale_size = sizeof($sold_birds);
                                    $sale_wt_size = sizeof($sale_qty);
                                    $trout_size = sizeof($trout_qty);
                                    $medvac_size = sizeof($medvac_qty);
                                    //echo "<br/>".date("d.m.Y",$start_date)."-".date("d.m.Y",$end_date);
                                for ($currentDate = ((int)$start_date); $currentDate <= ((int)$end_date); $currentDate += (86400)){
                                    $prev_date = date("Y-m-d",((int)$currentDate));
                                    
                                    $count++;
                                    for($i = 1;$i <= $pur_size;$i++){
                                        if(!empty($pur_qty[$prev_date."@".$chick_code."@".$i])){
                                            $farm_in_chicks += (float)$pur_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                    }
                                    for($i = 1;$i <= $trin_size;$i++){
                                        if(!empty($trin_qty[$prev_date."@".$chick_code."@".$i])){
                                            $farm_in_chicks += (float)$trin_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                    }
                                    if($count <= 7){
                                        $display_obirds = $farm_in_chicks;
                                    }
                                    else{
                                       // $display_obirds = $farm_in_chicks + $display_closed_birds;
                                        $display_obirds = $pre_close_birds;
                                    }

                                   // echo $prev_date."--".$display_obirds."<br/>";
                                    

                                    if(!empty($dentry_chicks[$prev_date])){
                                        $dentry_details = explode("@",$dentry_chicks[$prev_date]);
                                        $display_week = $count;
                                        $display_date = date("d.m.Y",strtotime($prev_date));
                                        $display_age = (int)$dentry_details[5];
                                        $display_mort += ((float)$dentry_details[6] + (float)$dentry_details[7]);
                                        $display_mort_cum += ((float)$dentry_details[6] + (float)$dentry_details[7]);

                                        /*if($dentry_details[12] != '' && $dentry_details[12] > 0){

                                            //$display_avg_wt = $dentry_details[12];
                                            if($farm_name[$row['farm_code']] == "B.NAVEEN-YELAMAKANNA"){
                                                echo "<br/>".$display_age."@".$brood_ages[$display_age];
                                            }
                                            $display_avg_wt = $brood_ages[$display_age];
                                        }*/
                                        $display_avg_wt = $brood_ages[$display_age];

                                        $feed_consume += ((float)$dentry_details[9] + (float)$dentry_details[11]);
                                    }
                                    if($display_mort > 0 && $display_obirds > 0){
                                        $display_mort_per = (((float)$display_mort / (float)$display_obirds) * 100);
                                    }
                                    else{
                                        $display_mort_per = 0;
                                    }
                                    if($display_mort_cum > 0 && $display_obirds > 0){
                                        $display_mort_cum_per = (((float)$display_mort_cum / (float)$display_obirds) * 100);
                                    }
                                    else{
                                        $display_mort_cum_per = 0;
                                    }
                                    
                                    
                                    $display_sold_birds = $farm_out_birds;
                                    $display_sold_weight = $farm_out_weight;
                                    
                                    for($i = 1;$i <= $sale_size;$i++){
                                        if(!empty($sold_birds[$prev_date."@".$bird_code."@".$i])){
                                            $farm_out_birds += (float)$sold_birds[$prev_date."@".$bird_code."@".$i];
                                        }
                                    }
                                    for($i = 1;$i <= $sale_wt_size;$i++){
                                        if(!empty($sale_qty[$prev_date."@".$bird_code."@".$i])){
                                            $farm_out_weight += (float)$sale_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                    }
                                    for($i = 1;$i <= $trout_size;$i++){
                                        if(!empty($trout_qty[$prev_date."@".$bird_code."@".$i])){
                                            $farm_out_birds += (float)$trout_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                    }
                                    foreach($feed_code as $fds_code){
                                        for($i = 1;$i <= $pur_size;$i++){
                                            if(!empty($pur_qty[$prev_date."@".$fds_code."@".$i])){
                                                $farm_in_feeds += (float)$pur_qty[$prev_date."@".$fds_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $trin_size;$i++){
                                            if(!empty($trin_qty[$prev_date."@".$fds_code."@".$i])){
                                                $farm_in_feeds += (float)$trin_qty[$prev_date."@".$fds_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $sale_wt_size;$i++){
                                            if(!empty($sale_qty[$prev_date."@".$fds_code."@".$i])){
                                                $farm_out_feeds += (float)$sale_qty[$prev_date."@".$fds_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $trout_size;$i++){
                                            if(!empty($trout_qty[$prev_date."@".$fds_code."@".$i])){
                                                $farm_out_feeds += (float)$trout_qty[$prev_date."@".$fds_code."@".$i];
                                            }
                                        }
                                    }
                                    foreach($medvac_code as $mvs_code){
                                        for($i = 1;$i <= $pur_size;$i++){
                                            if(!empty($pur_qty[$prev_date."@".$mvs_code."@".$i])){
                                                $farm_in_medvacs += (float)$pur_qty[$prev_date."@".$mvs_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $trin_size;$i++){
                                            if(!empty($trin_qty[$prev_date."@".$mvs_code."@".$i])){
                                                $farm_in_medvacs += (float)$trin_qty[$prev_date."@".$mvs_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $medvac_size;$i++){
                                            if(!empty($medvac_qty[$prev_date."@".$mvs_code."@".$i])){
                                                $farm_consume_medvacs += (float)$medvac_qty[$prev_date."@".$mvs_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $sale_wt_size;$i++){
                                            if(!empty($sale_qty[$prev_date."@".$mvs_code."@".$i])){
                                                $farm_out_medvacs += (float)$sale_qty[$prev_date."@".$mvs_code."@".$i];
                                            }
                                        }
                                        for($i = 1;$i <= $trout_size;$i++){
                                            if(!empty($trout_qty[$prev_date."@".$mvs_code."@".$i])){
                                                $farm_out_medvacs += (float)$trout_qty[$prev_date."@".$mvs_code."@".$i];
                                            }
                                        }
                                    }
                                    $display_closed_birds = ((float)$display_obirds - ((float)$display_mort + (float)$farm_out_birds));
                                    
                                    $display_feed_in = $farm_in_feeds;
                                    $display_feed_consume = $feed_consume;
                                    $display_feed_cum = $feed_consume;
                                    $display_feed_closed = (float)$farm_in_feeds - ((float)$feed_consume + (float)$farm_out_feeds);
                                    $display_std_feed_per_bird = ((float)$bstd_cum_feed[$display_age] * (float)$placed_in)/1000;
                                    if($display_sold_bird > 0){
                                        $display_actual_feed_per_bird = (float)$feed_consume / (float)$display_sold_bird;
                                    }
                                    else{
                                        $display_actual_feed_per_bird = 0;
                                    }
                                    if($display_sold_birds > 0){
                                        $display_cum_feed_per_bird = (float)$feed_consume / (float)$display_sold_birds;
                                    }
                                    else{
                                        $display_cum_feed_per_bird = 0;
                                    }
                                    
                                    $display_std_avg_weight =$bstd_body_weight[$display_age];
                                    if($display_sold_birds > 0){
                                        $display_actual_avg_weight = (float)$display_sold_weight / (float)$display_sold_birds;
                                    }
                                    
                                    
                                    if($display_actual_avg_weight == '' || $display_actual_avg_weight == 0 || $display_actual_avg_weight !=  $display_avg_wt ){
                                        $display_actual_avg_weight =  $display_avg_wt;
                                    }

                                    $display_std_fcr = $bstd_fcr[$display_age];;

                                    if($display_actual_avg_weight > 0){
                                        if($display_sold_birds > 0){
                                            $fcr1 = (float)$display_closed_birds * (float)$display_actual_avg_weight;
                                        }else{
                                            $fcr1 = ((float)$display_closed_birds * (float)$display_actual_avg_weight) / 1000;
                                        }
                                        if($fcr1 > 0){
                                            $display_actual_fcr = (float)$display_feed_consume/ (float)$fcr1;
                                        }else{
                                            $display_actual_fcr = 0; 
                                        }
                                       
                                       // echo $display_feed_consume."//".$display_closed_birds."//".$display_actual_avg_weight;
                                       // echo "<br/>";
                                    }else{
                                        $display_actual_fcr = 0;
                                    }

                                    $feed_perbird = 0; if((float)$display_obirds != 0){ $feed_perbird = round((((float)$display_feed_consume / (float)$display_obirds) * 1000),2); }
                                    $diff_fcr = (float)$display_std_fcr - (float)$display_actual_fcr;
                                    $afco_obwt = 0; if((float)$display_std_feed_per_bird != 0){ $afco_obwt = round((((float)$feed_perbird * (float)$display_std_avg_weight) / (float)$display_std_feed_per_bird),2); }
                                    $diff_bwtcon = (float)$display_actual_avg_weight - (float)$afco_obwt;
                                    $act_cfcr = 0; $act_cfcr = ((2-1.07)/ 4) + (float)$display_actual_fcr;

                                    if($start_date != ""){
                                        $sdate = date("d.m.Y",$start_date);
                                        if(strtotime($fdate) <= strtotime($sdate) && strtotime($tdate) >= strtotime($sdate) || strtotime($fdate) == "" && strtotime($tdate) == ""){
                                            if($count == 7 || $count == 14 || $count == 21 || $count == 28 || $count == 35 || $count == 42 || $count == 49 || $count == 56){
                                            //if($count > 0){
                                                if($chk_age == 0 || $chk_age == "" || $chk_age == $count){
                                                    $slno++;
                                                ?>
                                                <tr>
                                                    <td title="Serial No" style="text-align:left;"><?php echo $slno; ?></td>
                                                    <td title="Branch" style="text-align:left;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches){ echo ""; } else {echo $branch_name[$farm_branch[$fetch_fcode]]; } ?></td>
                                                    <td title="Hatchery" style="text-align:right;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches){ echo ""; } else { echo $sector_name[$chkin_hcode[$batches]]; } ?></td>
                                                    <td title="Supplier" style="text-align:right;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches){ echo ""; } else { echo $sup_name[$chkin_vcode[$batches]]; } ?></td>
                                                    <td title="Line" style="text-align:left;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches){ echo ""; } else{ echo $line_name[$farm_line[$fetch_fcode]]; }?></td>
                                                    <td title="Supervisor" style="text-align:center;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches){ echo ""; } else { echo $supervisor_name[$farm_supervisor[$fetch_fcode]]; } ?></td>
                                                    <td title="Farm" style="text-align:center;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches){ echo ""; } else { echo $farm_name[$fetch_fcode]; } ?></td>
                                                    <td title="<?php echo $batches; ?>" style="text-align:right;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches ){ echo ""; } else { echo $batch_name[$batches]; }?></td>
                                                    <td title="<?php echo $batches; ?>" style="text-align:right;"><?php if($pre_farm == $farm_name[$fetch_fcode] && $pre_supervisor == $supervisor_name[$farm_supervisor[$fetch_fcode]] && $pre_line == $line_name[$farm_line[$fetch_fcode]] && $pre_batch == $batches ){ echo ""; } else { echo $batch_book[$batches]; }?></td>
                                                    <td title="Placement Date" style="text-align:right;"><?php echo date("d.m.Y",$start_date); ?></td>
                                                    <td title="Age" style="text-align:right;"><?php echo $display_age; ?></td>
                                                    <td title="Week end date" style="text-align:right;"><?php echo $display_date; ?></td>
                                                    <td title="opening birds" style="text-align:right;"><?php echo $display_obirds; ?></td>
                                                    <td title="mortality" style="text-align:right;"><?php echo $display_mort; ?></td>
                                                    <td title="mortality %" style="text-align:right;"><?php echo number_format_ind($display_mort_per); ?></td>
                                                    <td title="sold birds" style="text-align:right;"><?php echo $display_sold_birds; ?></td>
                                                    <td title="Sold Weight" style="text-align:right;"><?php echo number_format_ind($display_sold_weight); ?></td>
                                                    <td title="balance birds" style="text-align:right;"><?php echo $display_closed_birds; ?></td>
                                                    <td title="STD Feed" style="text-align:right;"><?php echo number_format_ind($display_std_feed_per_bird); ?></td>
                                                    <?php if($display_feed_consume > $display_std_feed_per_bird ){?>
                                                        <td title="Actual Feed" bgcolor="yellow" style="text-align:right;color:red;font-weight: bold;"><?php echo number_format_ind($display_feed_consume); ?></td>
                                                    <?php } else { ?>
                                                        <td title="Actual Feed" bgcolor="#90EE90" style="text-align:right;color:black;font-weight: bold;"><?php echo number_format_ind($display_feed_consume); ?></td>
                                                    <?php } ?>
                                                    <td title="STD B.Weight" style="text-align:right;"><?php echo number_format_ind($display_std_avg_weight); ?></td>
                                                    <?php if($display_actual_avg_weight > $display_std_avg_weight ){?>
                                                        <td title="AVG BW" bgcolor="#90EE90" style="text-align:right;color:black;font-weight: bold;"><?php echo number_format_ind($display_actual_avg_weight); ?></td>
                                                    <?php } else { ?>
                                                        <td title="AVG BW" bgcolor="yellow" style="text-align:right;color:red;font-weight: bold;"><?php echo number_format_ind($display_actual_avg_weight); ?></td>
                                                    <?php } ?>
                                                    <td title="Feed/Bird" style="text-align:right;color:green;"><?php echo number_format_ind($feed_perbird); ?></td>
                                                    <td title="STD Day Gain" style="text-align:right;"><?php echo number_format_ind($bstd_daily_gain[$display_age]); ?></td>
                                                    <td title="Actual Day Gain" style="text-align:right;"></td>
                                                    <td title="STD Fcr" style="text-align:right;"><?php echo number_format_ind($display_std_fcr); ?></td>
                                                    <td title="FCR" style="text-align:right;"><?php echo number_format_ind(round($display_actual_fcr,2)); ?></td>
                                                    <td title="Diff in FCR" style="text-align:right;"><?php echo number_format_ind(round($diff_fcr,2)); ?></td>
                                                    <td title="Act Feed Conversation Bwt" style="text-align:right;"><?php echo number_format_ind(round($afco_obwt,2)); ?></td>
                                                    <td title="Diff Bwt Conversation" style="text-align:right;"><?php echo number_format_ind(round($diff_bwtcon,2)); ?></td>
                                                    <td title="Act CFCR" style="text-align:right;"><?php echo round($act_cfcr,4); ?></td>
                                                </tr>
                                                <?php
                                                    $display_mort = $farm_in_chicks = 0;

                                                    $pre_branch = $branch_name[$farm_branch[$fetch_fcode]];
                                                    $pre_line = $line_name[$farm_line[$fetch_fcode]];
                                                    $pre_supervisor = $supervisor_name[$farm_supervisor[$fetch_fcode]];
                                                    $pre_farm = $farm_name[$fetch_fcode];
                                                    $pre_batch = $batches;
                                                    $pre_close_birds = $display_closed_birds;
                                                }
                                            }
                                            else{
                                            }
                                        }
                                    }
                                }/*
                                $slno++;
                                ?>
                                <tr>
                                        <td title="Week" style="text-align:left;"><?php echo $slno; ?></td>
                                        <td title="Week" style="text-align:left;"><?php echo "Week-".$slno; ?></td>
                                        <td title="Week" style="text-align:left;"><?php echo $display_date; ?></td>
                                        <td title="Week" style="text-align:center;"><?php echo $display_age; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_obirds; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_mort; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_mort_per; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_mort_cum; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_mort_cum_per; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_sold_birds; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_sold_weight; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_closed_birds; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_feed_in; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_feed_consume; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_feed_cum; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_feed_closed; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_std_feed_per_bird; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_actual_feed_per_bird; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_cum_feed_per_bird; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_std_avg_weight; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_actual_avg_weight; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_std_fcr; ?></td>
                                        <td title="Week" style="text-align:right;"><?php echo $display_actual_fcr; ?></td>
                                    </tr>
                                <?php
                                */
                                ?>
                        </tbody>
                    <?php
                    }
                }
            ?>
            
           <!--  <tr class="thead4">
                <th colspan="8" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind($total_feeds_open); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind($total_feeds_in); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind($display_feed_out); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind($total_feed_consumed); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind($total_feed_stock); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind($total_feed_cumulate); ?></th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
            </tr> -->
        <?php
            }
        ?>
        </table>
        <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $l_code = $farm_line[$f_code];
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script type="text/javascript">
             function tableToExcel(table, name, filename, chosen){ 
              
            var uri = 'data:application/vnd.ms-excel;base64,'
                , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
          //  return function(table, name, filename, chosen) {
                if (chosen === 'excel') { 
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                //window.location.href = uri + base64(format(template, ctx))
                var link = document.createElement("a");
                                link.download = filename+".xls";
                                link.href = uri + base64(format(template, ctx));
                                link.click();
                }
            //}
        }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script src="../table_search_filter/Search_Script.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>