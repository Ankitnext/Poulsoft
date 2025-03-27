<?php
//broiler_modify_growingcharge_kalinga.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['growingcharge_kalinga'];

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_receipts", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_receipts LIKE poulso6_admin_broiler_broilermaster.broiler_receipts;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_payments LIKE poulso6_admin_broiler_broilermaster.broiler_payments;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_voucher_notes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_voucher_notes LIKE poulso6_admin_broiler_broilermaster.broiler_voucher_notes;"; mysqli_query($conn,$sql1); }

/*Check for Column inside Table Availability*/
$sql='SHOW COLUMNS FROM broiler_receipts'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE broiler_receipts ADD farm_batch VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("gc_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE broiler_receipts ADD gc_flag INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM broiler_payments'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE broiler_payments ADD farm_batch VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("gc_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE broiler_payments ADD gc_flag INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM broiler_voucher_notes'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE broiler_voucher_notes ADD farm_batch VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("gc_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE broiler_voucher_notes ADD gc_flag INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `broiler_rearingcharge`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){
    $existing_col_names[$i] = $row['Field']; $i++;
    if($row['Field'] == "actual_charge_exp_prc" && $row['Type'] == "double(20,2)"){
        $sql= "ALTER TABLE `broiler_rearingcharge` CHANGE `actual_charge_exp_prc` `actual_charge_exp_prc` DOUBLE(20,5) NULL DEFAULT NULL COMMENT 'Actual GC Payment';";
        mysqli_query($conn,$sql);
    }
    if($row['Field'] == "actual_charge_exp_amt" && $row['Type'] == "double(20,2)"){
        $sql= "ALTER TABLE `broiler_rearingcharge` CHANGE `actual_charge_exp_amt` `actual_charge_exp_amt` DOUBLE(20,5) NULL DEFAULT NULL COMMENT 'Actual GC Amount';";
        mysqli_query($conn,$sql);
    }
}
if(in_array("fcr_incentive_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `fcr_incentive_amt` double(20,2) NOT NULL DEFAULT '0' COMMENT 'FCR Incentive'"; mysqli_query($conn,$sql); }
if(in_array("advance_deduction", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `advance_deduction` double(20,2) NOT NULL DEFAULT '0' COMMENT 'Advance Deduction'"; mysqli_query($conn,$sql); }
if(in_array("remarks", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `remarks` VARCHAR(500) NULL DEFAULT NULL COMMENT ''"; mysqli_query($conn,$sql); }

if(in_array("mgmt_admin_prc", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `mgmt_admin_prc` double(20,2) NOT NULL DEFAULT '0' COMMENT 'Management Admin Cost' AFTER `admin_cost_amt`"; mysqli_query($conn,$sql); }
if(in_array("mgmt_admin_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `mgmt_admin_amt` double(20,2) NOT NULL DEFAULT '0' COMMENT 'Management Admin Cost' AFTER `mgmt_admin_prc`"; mysqli_query($conn,$sql); }
if(in_array("supervisor_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `supervisor_code` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Supervisor Code' AFTER `batch_code`"; mysqli_query($conn,$sql); }
if(in_array("actual_chick_price", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `actual_chick_price` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Mgmt Chick Price' AFTER `actual_chick_cost`"; mysqli_query($conn,$sql); }
if(in_array("actual_feed_price", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `actual_feed_price` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Mgmt Feed Price' AFTER `actual_feed_cost`"; mysqli_query($conn,$sql); }
if(in_array("actual_medicine_price", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `actual_medicine_price` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Mgmt MedVac Price' AFTER `actual_medicine_cost`"; mysqli_query($conn,$sql); }
if(in_array("schema_id", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `schema_id` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Schema Id' AFTER `branch_code`"; mysqli_query($conn,$sql); }
if(in_array("farmer_receipt_deduction", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `farmer_receipt_deduction` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT 'Farmer Receipt Amount' AFTER `farmer_sale_deduction`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_pvac[$row['code']] = $row['pvac'];
    $icat_pdac[$row['code']] = $row['pdac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}
$sql = "SELECT * FROM `main_groups`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $control_acc_group[$row['code']] = $row['controlaccount'];
    $payable_acc_group[$row['code']] = $row['prepayaccount'];
}
$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $contact_group[$row['code']] = $row['groupcode'];
}
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `tax_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['coa_code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Purchase Discount'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $discount_code = $row['code']; }

$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$placed_birds = $_POST['placed_birds'];
$farm_code = $_POST['farm_code'];
$batch_names = $_POST['batch_code'];
$schema_id = $_POST['schema_id'];

$sql = "SELECT * FROM `broiler_farm` WHERE `code` LIKE '$farm_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code = $row['branch_code']; $line_code = $row['line_code']; $farmer_code = $row['farmer_code']; }

$sql = "SELECT * FROM `broiler_farmer` WHERE `code` LIKE '$farmer_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $fcontact_group = $row['farmer_group']; }

$sql = "SELECT * FROM `broiler_farmergroup` WHERE `code` = '$fcontact_group'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $fcontrol_acc_group = $row['adv_acc_code'];
    $fpayable_acc_group = $row['pay_acc_code'];
}

$fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `description` = '$batch_names' AND `gc_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql);
while($frow = mysqli_fetch_assoc($fquery)){ $batch_code = $frow['code']; $batch_name = $frow['description']; }

$mortality = $_POST['mortality'];
$sold_birds = $_POST['sold_birds'];
$sold_weight = $_POST['sold_weight'];
$excess = $_POST['excess'];
$shortage = $_POST['shortage'];
$liquid_date = date("Y-m-d",strtotime($_POST['liquid_date']));
$sale_amount = $_POST['sale_amount'];
$sale_rate = $_POST['sale_rate'];
$age = $_POST['age'];
$days7_mort = $_POST['days7_mort'];
$days7_mort_count = $_POST['days7_mort_count'];
$days30_mort = $_POST['days30_mort'];
$days30_mort_count = $_POST['days30_mort_count'];
$daysge31_mort = $_POST['daysge31_mort'];
$days31_mort_count = $_POST['days31_mort_count'];
$total_mort = $_POST['total_mort'];
$fcr = $_POST['fcr'];
$cfcr = $_POST['cfcr'];
$avg_wt = $_POST['avg_wt'];
$mean_age = $_POST['mean_age'];
$day_gain = $_POST['day_gain'];
$eef = $_POST['eef'];
$grade = $_POST['grade'];
$feed_in_kgs = $_POST['feed_in_kgs'];
$feed_consume_kgs = $_POST['feed_consume_kgs'];
$feed_out_kgs = $_POST['feed_out_kgs'];
$feed_bal_kgs = $_POST['feed_bal_kgs'];
$feed_in_bag = $_POST['feed_in_bag'];
$feed_consume_bag = $_POST['feed_consume_bag'];
$feed_out_bag = $_POST['feed_out_bag'];
$feed_bal_bag = $_POST['feed_bal_bag'];
$transfer_in = $_POST['transfer_in'];
$consumption = $_POST['consumption'];
$transfer_out = $_POST['transfer_out'];
$closing = $_POST['closing'];

$t1 = $t2 = array(); $t1 = explode("(",$_POST['supervisor_code']); $t2 = explode(")",$t1[1]);
$supervisor_code = $t2[0];
$actual_chick_price = $_POST['mgmt_stkin_chick_prc']; if($actual_chick_price == ""){ $actual_chick_price = 0; }
$actual_chick_cost = $_POST['mgmt_stkin_chick_amt']; if($actual_chick_cost == ""){ $actual_chick_cost = 0; }
$actual_feed_price = $_POST['mgmt_total_feed_consumed_prc']; if($actual_feed_price == ""){ $actual_feed_price = 0; }
$actual_feed_cost = $_POST['mgmt_total_feed_consumed_amt']; if($actual_feed_cost == ""){ $actual_feed_cost = 0; }
$mgmt_admin_prc = $_POST['mgmt_admin_prc']; if($mgmt_admin_prc == ""){ $mgmt_admin_prc = 0; }
$mgmt_admin_amt = $_POST['mgmt_admin_amt']; if($mgmt_admin_amt == ""){ $mgmt_admin_amt = 0; }
$actual_medicine_price = $_POST['mgmt_total_medvac_consumed_prc']; if($actual_medicine_price == ""){ $actual_medicine_price = 0; }
$actual_medicine_cost = $_POST['mgmt_total_medvac_consumed_amt']; if($actual_medicine_cost == ""){ $actual_medicine_cost = 0; }

$chick_cost_amt = $_POST['chick_cost_amt'];
$chick_cost_unit = $_POST['chick_cost_unit'];
$feed_cost_amt = $_POST['feed_cost_amt'];
$feed_cost_unit = $_POST['feed_cost_unit'];
$admin_cost_amt = $_POST['admin_cost_amt'];
$admin_cost_unit = $_POST['admin_cost_unit'];
$medicine_cost_amt = $_POST['medicine_cost_amt'];
$medicine_cost_unit = $_POST['medicine_cost_unit'];
$total_cost_amt = $_POST['total_cost_amt'];
$total_cost_unit = $_POST['total_cost_unit'];
$standard_prod_cost = $_POST['standard_prod_cost'];
$actual_prod_cost = $_POST['actual_prod_cost'];
$standard_gc_prc = $_POST['standard_gc_prc'];
$standard_gc_amt = $_POST['standard_gc_amt'];
$actual_charge_exp_prc = $_POST['actual_charge_exp_prc'];
$actual_charge_exp_amt = $_POST['actual_charge_exp_amt'];
$grow_charge_exp_prc = $_POST['grow_charge_exp_prc'];
$grow_charge_exp_amt = $_POST['grow_charge_exp_amt'];
$sales_incentive_prc = $_POST['sales_incentive_prc'];
$sales_incentive_amt = $_POST['sales_incentive_amt'];
$shortage_deduct_prc = $_POST['shortage_deduct_prc'];
$shortage_deduct_amt = $_POST['shortage_deduct_amt'];
$total_gc_prc = $_POST['total_gc_prc'];
$total_gc_amt = $_POST['total_gc_amt'];
$mortality_incentive_prc = $_POST['mortality_incentive_prc'];
$mortality_incentive_amt = $_POST['mortality_incentive_amt'];
$fcr_incentive_prc = $_POST['fcr_incentive_prc'];
$fcr_incentive_amt = $_POST['fcr_incentive_amt'];
$summer_incentive_prc = $_POST['summer_incentive_prc'];
$summer_incentive_amt = $_POST['summer_incentive_amt'];
$other_incentive = $_POST['other_incentive'];
$ifft_charges = $_POST['ifft_charges'];
$total_incentives = $_POST['total_incentives'];
$birds_shortage = $_POST['birds_shortage'];
$birds_shortage_prc = $_POST['birds_shortage_prc'];
$fcr_deduction = $_POST['fcr_deduction'];
$mortality_deduction = $_POST['mortality_deduction'];
$total_deduction = $_POST['total_deduction'];
$amount_payable = $_POST['amount_payable'];
$farmer_sale_deduction = $_POST['farmer_sale_deduction'];
$farmer_receipt_deduction = $_POST['farmer_receipt_deduction'];
$feed_transfer_charges = $_POST['feed_transfer_charges'];
$vaccinator_charges = $_POST['vaccinator_charges'];
$transportation_charges = $_POST['transportation_charges'];
$total_amount_payable = $_POST['total_amount_payable'];
$tds_amt = $_POST['tds_amt'];
$equipment_charges = $_POST['equipment_charges'];
$other_deduction = $_POST['other_deduction'];
$advance_deduction = $_POST['advance_deduction'];
$farmer_payable = $_POST['farmer_payable'];
$remarks = $_POST['remarks'];
$aggrement_chages = $_POST['aggrement_chages'];
$flag = 0;
$active = 1;
$dflag = 0;

$trnum = $_POST['idvalue'];
$date = date("Y-m-d",strtotime($_POST['gc_date']));

if($placed_birds == "" || $placed_birds == NULL || $placed_birds == 0){ $placed_birds = "0.00"; }
if($mortality == "" || $mortality == NULL || $mortality == 0){ $mortality = "0.00"; }
if($sold_birds == "" || $sold_birds == NULL || $sold_birds == 0){ $sold_birds = "0.00"; }
if($sold_weight == "" || $sold_weight == NULL || $sold_weight == 0){ $sold_weight = "0.00"; }
if($excess == "" || $excess == NULL || $excess == 0){ $excess = "0.00"; }
if($shortage == "" || $shortage == NULL || $shortage == 0){ $shortage = "0.00"; }
if($sale_amount == "" || $sale_amount == NULL || $sale_amount == 0){ $sale_amount = "0.00"; }
if($sale_rate == "" || $sale_rate == NULL || $sale_rate == 0){ $sale_rate = "0.00"; }
if($age == "" || $age == NULL || $age == 0){ $age = "0.00"; }
if($days7_mort == "" || $days7_mort == NULL || $days7_mort == 0){ $days7_mort = "0.00"; }
if($days7_mort_count == "" || $days7_mort_count == NULL || $days7_mort_count == 0){ $days7_mort_count = 0; }
if($days30_mort == "" || $days30_mort == NULL || $days30_mort == 0){ $days30_mort = "0.00"; }
if($days30_mort_count == "" || $days30_mort_count == NULL || $days30_mort_count == 0){ $days30_mort_count = 0; }
if($daysge31_mort == "" || $daysge31_mort == NULL || $daysge31_mort == 0){ $daysge31_mort = "0.00"; }
if($days31_mort_count == "" || $days31_mort_count == NULL || $days31_mort_count == 0){ $days31_mort_count = 0; }
if($total_mort == "" || $total_mort == NULL || $total_mort == 0){ $total_mort = "0.00"; }
if($fcr == "" || $fcr == NULL || $fcr == 0){ $fcr = "0.00"; }
if($cfcr == "" || $cfcr == NULL || $cfcr == 0){ $cfcr = "0.00"; }
if($avg_wt == "" || $avg_wt == NULL || $avg_wt == 0){ $avg_wt = "0.00"; }
if($mean_age == "" || $mean_age == NULL || $mean_age == 0){ $mean_age = "0.00"; }
if($day_gain == "" || $day_gain == NULL || $day_gain == 0){ $day_gain = "0.00"; }
if($eef == "" || $eef == NULL || $eef == 0){ $eef = "0.00"; }
if($feed_in_kgs == "" || $feed_in_kgs == NULL || $feed_in_kgs == 0){ $feed_in_kgs = "0.00"; }
if($feed_consume_kgs == "" || $feed_consume_kgs == NULL || $feed_consume_kgs == 0){ $feed_consume_kgs = "0.00"; }
if($feed_out_kgs == "" || $feed_out_kgs == NULL || $feed_out_kgs == 0){ $feed_out_kgs = "0.00"; }
if($feed_bal_kgs == "" || $feed_bal_kgs == NULL || $feed_bal_kgs == 0){ $feed_bal_kgs = "0.00"; }
if($feed_in_bag == "" || $feed_in_bag == NULL || $feed_in_bag == 0){ $feed_in_bag = "0.00"; }
if($feed_consume_bag == "" || $feed_consume_bag == NULL || $feed_consume_bag == 0){ $feed_consume_bag = "0.00"; }
if($feed_out_bag == "" || $feed_out_bag == NULL || $feed_out_bag == 0){ $feed_out_bag = "0.00"; }
if($feed_bal_bag == "" || $feed_bal_bag == NULL || $feed_bal_bag == 0){ $feed_bal_bag = "0.00"; }
if($transfer_in == "" || $transfer_in == NULL || $transfer_in == 0){ $transfer_in = "0.00"; }
if($consumption == "" || $consumption == NULL || $consumption == 0){ $consumption = "0.00"; }
if($transfer_out == "" || $transfer_out == NULL || $transfer_out == 0){ $transfer_out = "0.00"; }
if($closing == "" || $closing == NULL || $closing == 0){ $closing = "0.00"; }
if($chick_cost_amt == "" || $chick_cost_amt == NULL || $chick_cost_amt == 0){ $chick_cost_amt = "0.00"; }
if($chick_cost_unit == "" || $chick_cost_unit == NULL || $chick_cost_unit == 0){ $chick_cost_unit = "0.00"; }
if($feed_cost_amt == "" || $feed_cost_amt == NULL || $feed_cost_amt == 0){ $feed_cost_amt = "0.00"; }
if($feed_cost_unit == "" || $feed_cost_unit == NULL || $feed_cost_unit == 0){ $feed_cost_unit = "0.00"; }
if($admin_cost_amt == "" || $admin_cost_amt == NULL || $admin_cost_amt == 0){ $admin_cost_amt = "0.00"; }
if($admin_cost_unit == "" || $admin_cost_unit == NULL || $admin_cost_unit == 0){ $admin_cost_unit = "0.00"; }
if($mgmt_admin_prc == "" || $mgmt_admin_prc == NULL || $mgmt_admin_prc == 0){ $mgmt_admin_prc = "0.00"; }
if($mgmt_admin_amt == "" || $mgmt_admin_amt == NULL || $mgmt_admin_amt == 0){ $mgmt_admin_amt = "0.00"; }
if($medicine_cost_amt == "" || $medicine_cost_amt == NULL || $medicine_cost_amt == 0){ $medicine_cost_amt = "0.00"; }
if($medicine_cost_unit == "" || $medicine_cost_unit == NULL || $medicine_cost_unit == 0){ $medicine_cost_unit = "0.00"; }
if($total_cost_amt == "" || $total_cost_amt == NULL || $total_cost_amt == 0){ $total_cost_amt = "0.00"; }
if($total_cost_unit == "" || $total_cost_unit == NULL || $total_cost_unit == 0){ $total_cost_unit = "0.00"; }
if($standard_prod_cost == "" || $standard_prod_cost == NULL || $standard_prod_cost == 0){ $standard_prod_cost = "0.00"; }
if($actual_prod_cost == "" || $actual_prod_cost == NULL || $actual_prod_cost == 0){ $actual_prod_cost = "0.00"; }
if($standard_gc_prc == "" || $standard_gc_prc == NULL || $standard_gc_prc == 0){ $standard_gc_prc = "0.00"; }
if($standard_gc_amt == "" || $standard_gc_amt == NULL || $standard_gc_amt == 0){ $standard_gc_amt = "0.00"; }
if($actual_charge_exp_prc == "" || $actual_charge_exp_prc == NULL || $actual_charge_exp_prc == 0){ $actual_charge_exp_prc = "0.00"; }
if($actual_charge_exp_amt == "" || $actual_charge_exp_amt == NULL || $actual_charge_exp_amt == 0){ $actual_charge_exp_amt = "0.00"; }
if($grow_charge_exp_prc == "" || $grow_charge_exp_prc == NULL || $grow_charge_exp_prc == 0){ $grow_charge_exp_prc = "0.00"; }
if($grow_charge_exp_amt == "" || $grow_charge_exp_amt == NULL || $grow_charge_exp_amt == 0){ $grow_charge_exp_amt = "0.00"; }
if($sales_incentive_prc == "" || $sales_incentive_prc == NULL || $sales_incentive_prc == 0){ $sales_incentive_prc = "0.00"; }
if($sales_incentive_amt == "" || $sales_incentive_amt == NULL || $sales_incentive_amt == 0){ $sales_incentive_amt = "0.00"; }
if($shortage_deduct_prc == "" || $shortage_deduct_prc == NULL || $shortage_deduct_prc == 0){ $shortage_deduct_prc = "0.00"; }
if($shortage_deduct_amt == "" || $shortage_deduct_amt == NULL || $shortage_deduct_amt == 0){ $shortage_deduct_amt = "0.00"; }
if($total_gc_prc == "" || $total_gc_prc == NULL || $total_gc_prc == 0){ $total_gc_prc = "0.00"; }
if($total_gc_amt == "" || $total_gc_amt == NULL || $total_gc_amt == 0){ $total_gc_amt = "0.00"; }
if($mortality_incentive_prc == "" || $mortality_incentive_prc == NULL || $mortality_incentive_prc == 0){ $mortality_incentive_prc = "0.00"; }
if($mortality_incentive_amt == "" || $mortality_incentive_amt == NULL || $mortality_incentive_amt == 0){ $mortality_incentive_amt = "0.00"; }
if($fcr_incentive_prc == "" || $fcr_incentive_prc == NULL || $fcr_incentive_prc == 0){ $fcr_incentive_prc = "0.00"; }
if($fcr_incentive_amt == "" || $fcr_incentive_amt == NULL || $fcr_incentive_amt == 0){ $fcr_incentive_amt = "0.00"; }
if($summer_incentive_prc == "" || $summer_incentive_prc == NULL || $summer_incentive_prc == 0){ $summer_incentive_prc = "0.00"; }
if($summer_incentive_amt == "" || $summer_incentive_amt == NULL || $summer_incentive_amt == 0){ $summer_incentive_amt = "0.00"; }
if($other_incentive == "" || $other_incentive == NULL || $other_incentive == 0){ $other_incentive = "0.00"; }
if($ifft_charges == "" || $ifft_charges == NULL || $ifft_charges == 0){ $ifft_charges = "0.00"; }
if($total_incentives == "" || $total_incentives == NULL || $total_incentives == 0){ $total_incentives = "0.00"; }
if($birds_shortage == "" || $birds_shortage == NULL || $birds_shortage == 0){ $birds_shortage = "0.00"; }
if($birds_shortage_prc == "" || $birds_shortage_prc == NULL || $birds_shortage_prc == 0){ $birds_shortage_prc = "0.00"; }
if($fcr_deduction == "" || $fcr_deduction == NULL || $fcr_deduction == 0){ $fcr_deduction = "0.00"; }
if($mortality_deduction == "" || $mortality_deduction == NULL || $mortality_deduction == 0){ $mortality_deduction = "0.00"; }
if($total_deduction == "" || $total_deduction == NULL || $total_deduction == 0){ $total_deduction = "0.00"; }
if($amount_payable == "" || $amount_payable == NULL || $amount_payable == 0){ $amount_payable = "0.00"; }
if($farmer_sale_deduction == "" || $farmer_sale_deduction == NULL || $farmer_sale_deduction == 0){ $farmer_sale_deduction = "0.00"; }
if($farmer_receipt_deduction == "" || $farmer_receipt_deduction == NULL || $farmer_receipt_deduction == 0){ $farmer_receipt_deduction = "0.00"; }
if($feed_transfer_charges == "" || $feed_transfer_charges == NULL || $feed_transfer_charges == 0){ $feed_transfer_charges = "0.00"; }
if($vaccinator_charges == "" || $vaccinator_charges == NULL || $vaccinator_charges == 0){ $vaccinator_charges = "0.00"; }
if($transportation_charges == "" || $transportation_charges == NULL || $transportation_charges == 0){ $transportation_charges = "0.00"; }
if($total_amount_payable == "" || $total_amount_payable == NULL || $total_amount_payable == 0){ $total_amount_payable = "0.00"; }
if($tds_amt == "" || $tds_amt == NULL || $tds_amt == 0){ $tds_amt = "0.00"; }
if($equipment_charges == "" || $equipment_charges == NULL || $equipment_charges == 0){ $equipment_charges = "0.00"; }
if($other_deduction == "" || $other_deduction == NULL || $other_deduction == 0){ $other_deduction = "0.00"; }
if($advance_deduction == "" || $advance_deduction == NULL || $advance_deduction == 0){ $advance_deduction = "0.00"; }
if($farmer_payable == "" || $farmer_payable == NULL || $farmer_payable == 0){ $farmer_payable = "0.00"; }
if($aggrement_chages == "" || $aggrement_chages == NULL || $aggrement_chages == 0){ $aggrement_chages = "0.00"; }


$sql = "SELECT * FROM `broiler_rearingcharge` WHERE `trnum` = '$trnum' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($query)){
    $incr = $row['incr'];
    $prefix = $row['prefix'];
    $ademp = $row['addedemp'];
    $adtme = $row['addedtime'];
}
$sql = "DELETE FROM `broiler_rearingcharge` WHERE `trnum` = '$trnum' AND `active` = '1' AND `dflag` = '0'"; mysqli_query($conn, $sql);
$sql = "DELETE FROM `account_summary` WHERE `trnum` = '$trnum' AND `active` = '1' AND `dflag` = '0'"; mysqli_query($conn, $sql);

$sql = "INSERT INTO `broiler_rearingcharge` (incr,prefix,trnum,date,branch_code,schema_id,line_code,farm_code,batch_code,supervisor_code,start_date,placed_birds,mortality,sold_birds,sold_weight,excess,shortage,liquid_date,sale_amount,sale_rate,age,days7_mort,days30_mort,daysge31_mort,total_mort,fcr,cfcr,avg_wt,mean_age,day_gain,eef,grade,feed_in_kgs,feed_consume_kgs,feed_out_kgs,feed_bal_kgs,feed_in_bag,feed_consume_bag,feed_out_bag,feed_bal_bag,transfer_in,consumption,transfer_out,closing,chick_cost_amt,chick_cost_unit,actual_chick_cost,actual_chick_price,feed_cost_amt,feed_cost_unit,actual_feed_cost,actual_feed_price,admin_cost_amt,admin_cost_unit,mgmt_admin_amt,mgmt_admin_prc,medicine_cost_amt,medicine_cost_unit,actual_medicine_cost,actual_medicine_price,total_cost_amt,total_cost_unit,standard_prod_cost,actual_prod_cost,standard_gc_prc,standard_gc_amt,actual_charge_exp_prc,actual_charge_exp_amt,grow_charge_exp_prc,grow_charge_exp_amt,sales_incentive_prc,sales_incentive_amt,mortality_incentive_prc,mortality_incentive_amt,fcr_incentive_prc,fcr_incentive_amt,summer_incentive_prc,summer_incentive_amt,other_incentive,ifft_charges,total_incentives,birds_shortage,birds_shortage_prc,fcr_deduction,mortality_deduction,total_deduction,amount_payable,farmer_sale_deduction,farmer_receipt_deduction,feed_transfer_charges,vaccinator_charges,transportation_charges,total_amount_payable,tds_amt,equipment_charges,other_deduction,advance_deduction,farmer_payable,remarks,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) VALUES ('$incr','$prefix','$trnum','$date','$branch_code','$schema_id','$line_code','$farm_code','$batch_code','$supervisor_code','$start_date','$placed_birds','$mortality','$sold_birds','$sold_weight','$excess','$shortage','$liquid_date','$sale_amount','$sale_rate','$age','$days7_mort','$days30_mort','$daysge31_mort','$total_mort','$fcr','$cfcr','$avg_wt','$mean_age','$day_gain','$eef','$grade','$feed_in_kgs','$feed_consume_kgs','$feed_out_kgs','$feed_bal_kgs','$feed_in_bag','$feed_consume_bag','$feed_out_bag','$feed_bal_bag','$transfer_in','$consumption','$transfer_out','$closing','$chick_cost_amt','$chick_cost_unit','$actual_chick_cost','$actual_chick_price','$feed_cost_amt','$feed_cost_unit','$actual_feed_cost','$actual_feed_price','$admin_cost_amt','$admin_cost_unit','$mgmt_admin_amt','$mgmt_admin_prc','$medicine_cost_amt','$medicine_cost_unit','$actual_medicine_cost','$actual_medicine_price','$total_cost_amt','$total_cost_unit','$standard_prod_cost','$actual_prod_cost','$standard_gc_prc','$standard_gc_amt','$actual_charge_exp_prc','$actual_charge_exp_amt','$grow_charge_exp_prc','$grow_charge_exp_amt','$sales_incentive_prc','$sales_incentive_amt','$mortality_incentive_prc','$mortality_incentive_amt','$fcr_incentive_prc','$fcr_incentive_amt','$summer_incentive_prc','$summer_incentive_amt','$other_incentive','$ifft_charges','$total_incentives','$birds_shortage','$birds_shortage_prc','$fcr_deduction','$mortality_deduction','$total_deduction','$amount_payable','$farmer_sale_deduction','$farmer_receipt_deduction','$feed_transfer_charges','$vaccinator_charges','$transportation_charges','$total_amount_payable','$tds_amt','$equipment_charges','$other_deduction','$advance_deduction','$farmer_payable','$remarks','$flag','$active','$dflag','$ademp','$adtme','$addedemp','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
else {
    $sql = "UPDATE `broiler_batch` SET `start_date` = '$start_date',`end_date` = '$liquid_date',`gc_flag` = '1' WHERE `farm_code` = '$farm_code' AND `code` = '$batch_code'";
    if(!mysqli_query($conn,$sql)){ die("Error-2:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `broiler_daily_record` SET `gc_flag` = '1',`flag` = '1' WHERE `farm_code` = '$farm_code' AND `batch_code` = '$batch_code'";
        if(!mysqli_query($conn,$sql)){ die("Error-3:-".mysqli_error($conn)); } else{
            $sql = "UPDATE `broiler_medicine_record` SET `gc_flag` = '1',`flag` = '1' WHERE `farm_code` = '$farm_code' AND `batch_code` = '$batch_code'";
            if(!mysqli_query($conn,$sql)){ die("Error-4:-".mysqli_error($conn)); } else{
                $sql = "UPDATE `item_stocktransfers` SET `gc_flag` = '1',`flag` = '1' WHERE `fromwarehouse` = '$farm_code' AND `from_batch` = '$batch_code'";
                if(!mysqli_query($conn,$sql)){ die("Error-5:-".mysqli_error($conn)); } else{
                    $sql = "UPDATE `item_stocktransfers` SET `gc_flag` = '1',`flag` = '1' WHERE `towarehouse` = '$farm_code' AND `to_batch` = '$batch_code'";
                    if(!mysqli_query($conn,$sql)){ die("Error-6:-".mysqli_error($conn)); } else{
                        $sql = "UPDATE `broiler_purchases` SET `gc_flag` = '1',`flag` = '1' WHERE `warehouse` = '$farm_code' AND `farm_batch` = '$batch_code'";
                        if(!mysqli_query($conn,$sql)){ die("Error-7:-".mysqli_error($conn)); } else{
                            $sql = "UPDATE `broiler_sales` SET `gc_flag` = '1',`flag` = '1' WHERE `warehouse` = '$farm_code' AND `farm_batch` = '$batch_code'";
                            if(!mysqli_query($conn,$sql)){ die("Error-8:-".mysqli_error($conn)); } else{ }
                            
                            $sql = "UPDATE `broiler_receipts` SET `gc_flag` = '1',`flag` = '1' WHERE `farm_batch` = '$batch_code'";
                            if(!mysqli_query($conn,$sql)){ die("Error-8:-".mysqli_error($conn)); } else{ }
                            
                            $sql = "UPDATE `broiler_payments` SET `gc_flag` = '1',`flag` = '1' WHERE `farm_batch` = '$batch_code'";
                            if(!mysqli_query($conn,$sql)){ die("Error-8:-".mysqli_error($conn)); } else{ }
                            
                            $sql = "UPDATE `broiler_voucher_notes` SET `gc_flag` = '1',`flag` = '1' WHERE `farm_batch` = '$batch_code'";
                            if(!mysqli_query($conn,$sql)){ die("Error-8:-".mysqli_error($conn)); } else{ }
                        }
                    }
                }
            }
        }
    }
    //GC Posting Insertion
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_cat = $row['category']; }
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_cat = $row['category']; }
    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; }
    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $med_cat[$row['code']] = $row['code']; }
    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vac_cat[$row['code']] = $row['code']; }

    //Chick Cr Details
    $coa_code = ""; $coa_code = $icat_iac[$chick_cat];
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedemp,updatedtime) 
    VALUES ('CR','$coa_code','$date','$trnum','$chick_code','$sold_birds','$actual_chick_price','$actual_chick_cost','$farm_code','$batch_code',NULL,NULL,NULL,'1','0','1','0','GC Chick-Bird','$ademp','$adtme','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }

    //Farmer Pay Details
    $coa_code = ""; $coa_code = $fpayable_acc_group;
    $fpay = (float)$farmer_payable + (float)$tds_amt;
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,vendor,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedemp,updatedtime) 
    VALUES ('CR','$coa_code','$date','$trnum','$farmer_code','0','0','$fpay','$farm_code','$batch_code',NULL,NULL,NULL,'1','0','1','0','GC-FarmerPayGeneration','$ademp','$adtme','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }

    if((float)$tds_amt != 0 || $tds_amt != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,vendor,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedemp,updatedtime) 
        VALUES ('DR','$coa_code','$date','$trnum','$farmer_code','0','0','$tds_amt','$farm_code','$batch_code',NULL,NULL,NULL,'1','0','1','0','GC-FarmerPayGeneration','$ademp','$adtme','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }
    }

    $coa_arr_list = array();
    foreach($feed_cat as $fcode){ $coa_arr_list[$icat_wpac[$fcode]] = $icat_wpac[$fcode]; }
    foreach($med_cat as $fcode){ $coa_arr_list[$icat_wpac[$fcode]] = $icat_wpac[$fcode]; }
    foreach($vac_cat as $fcode){ $coa_arr_list[$icat_wpac[$fcode]] = $icat_wpac[$fcode]; }
    $coa_list = ""; $coa_list = implode("','",$coa_arr_list);

    $sql = "SELECT item_code,coa_code,crdr,SUM(amount) as amount FROM `account_summary` WHERE `coa_code` IN ('$coa_list') AND `location` = '$farm_code' AND `batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0' GROUP BY `item_code`,`coa_code`,`crdr` ORDER BY `item_code`,`coa_code` ASC";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['item_code']."@".$row['coa_code'];
        $active_wip_coas[$key] = $key;
        if($row['crdr'] == "CR"){ $coa_cr_amt[$key] += $row['amount']; }
        else if($row['crdr'] == "DR"){ $coa_dr_amt[$key] += $row['amount']; }
    }
    foreach($active_wip_coas as $acoas){
        if(empty($coa_cr_amt[$acoas])){ $coa_cr_amt[$acoas] = 0; }
        if(empty($coa_dr_amt[$acoas])){ $coa_dr_amt[$acoas] = 0; }
        $wip_amount = 0; $wip_amount = $coa_dr_amt[$acoas] - $coa_cr_amt[$acoas];
        $t1 = array(); $t1 = explode("@",$acoas);
        $coa_code = $item_code = ""; $coa_code = $t1[1]; $item_code = $t1[0];

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
        VALUES ('CR','$coa_code','$date','$trnum','$item_code','0','0','$wip_amount','$farm_code','$batch_code',NULL,NULL,NULL,'1','0','1','0','$ademp','$adtme','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }
    }
    
    //Broiler Bird Production
    $coa_code = ""; $coa_code = $icat_iac[$bird_cat];
    $mgmt_bird_amount = (float)$actual_chick_cost + (float)$actual_feed_cost + (float)$mgmt_admin_amt + (float)$actual_medicine_cost + (float)$farmer_payable;
    $mgmt_bird_price = (float)$mgmt_bird_amount / (float)$sold_weight;
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedemp,updatedtime) 
    VALUES ('DR','$coa_code','$date','$trnum','$bird_code','$sold_weight','$mgmt_bird_price','$mgmt_bird_amount','$farm_code','$batch_code',NULL,NULL,NULL,'1','0','1','0','GC-BirdProduction','$ademp','$adtme','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }

    //Update Previous Sale Entries
    $iac_coa = $icat_iac[$bird_cat]; $cogs_coa = $icat_cogsac[$bird_cat];
    $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$iac_coa','$cogs_coa') AND `item_code` = '$bird_code' AND `location` = '$farm_code' AND `batch` = '$batch_code' AND `trnum` NOT IN ('$trnum') AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $id = $sale_qty = $sale_amt = 0; $coa_code = "";
        $id = $row['id'];
        $sale_qty = $row['quantity'];
        $coa_code = $row['coa_code'];
        $sale_amt = (float)$sale_qty * (float)$mgmt_bird_price;

        if($coa_code == $iac_coa){
            $sql2 = "UPDATE `account_summary` SET `crdr` = 'CR',`price` = '$mgmt_bird_price',`amount` = '$sale_amt' WHERE `id` = '$id'"; mysqli_query($conn,$sql2);
        }
        else if($coa_code == $cogs_coa){
            $sql2 = "UPDATE `account_summary` SET `crdr` = 'DR',`price` = '$mgmt_bird_price',`amount` = '$sale_amt' WHERE `id` = '$id'"; mysqli_query($conn,$sql2);
        }
    }
}
header('location:broiler_display_growingcharge_kalinga.php?ccid='.$ccid);
?>