<?php
//chicken_customerledger_masterreport1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_customerledger_masterreport1_ty.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_customerledger_masterreport1.php?db=".$db;
}
include "number_format_ind.php";

function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; }
    $a = round($a,$b);
    $c = explode(".",$a);
    $ed = "";
    $iv = 0;
    if($c[1] == ""){ $iv = 1; }
    else{ $iv = strlen($c[1]); }
    if($iv == 0){ $iv = 1; }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    return $a."".$ed;
}

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

//Check for Column Availability
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sup_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `sup_code` VARCHAR(300) NULL DEFAULT NULL AFTER `customercode`"; mysqli_query($conn,$sql); }
if(in_array("description", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `description` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Manual Name' AFTER `sup_code`"; mysqli_query($conn,$sql); }
if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("font_style_master", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.font_style_master LIKE poulso6_admin_chickenmaster.font_style_master;"; mysqli_query($conn,$sql1); }
if(in_array("customer_sales", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.customer_sales LIKE poulso6_admin_chickenmaster.customer_sales;"; mysqli_query($conn,$sql1); }
if(in_array("master_cbr_main_details", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_cbr_main_details LIKE poulso6_admin_chickenmaster.master_cbr_main_details;"; mysqli_query($conn,$sql1); }
if(in_array("master_cbr_header_names", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_cbr_header_names LIKE poulso6_admin_chickenmaster.master_cbr_header_names;"; mysqli_query($conn,$sql1); }

//Check for Column Availability
$sql='SHOW COLUMNS FROM `master_cbr_main_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cus_cdays_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `cus_cdays_flag` INT(100) NOT NULL DEFAULT '0' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("cus_outbal_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `cus_outbal_flag` INT(100) NOT NULL DEFAULT '1' AFTER `cus_cdays_flag`"; mysqli_query($conn,$sql); }
if(in_array("field_calign_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `field_calign_flag` INT(100) NOT NULL DEFAULT '0' AFTER `cus_outbal_flag`"; mysqli_query($conn,$sql); }
if(in_array("logo_path", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `logo_path` VARCHAR(500) NULL DEFAULT NULL AFTER `field_calign_flag`"; mysqli_query($conn,$sql); }
if(in_array("logo_ascom_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `logo_ascom_flag` INT(100) NOT NULL DEFAULT '0' AFTER `logo_path`"; mysqli_query($conn,$sql); }

/*Check Flags*/
$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sup_mnuname_flag = $row['description']; } if($sup_mnuname_flag == ""){ $sup_mnuname_flag = 0; }

/*Master Report Format*/
//$field_calign_flag: All Fields except date and transaction type2 are align to center flag
$acname = $icname = array(); $ac_cnt = $cus_cdays_flag = $cus_outbal_flag = $field_calign_flag = $logo_ascom_flag = 0; $slogo_path = "";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `master_cbr_main_details` WHERE `project` LIKE 'CTS' AND `file_url` LIKE 'chicken_customerledger_masterreport1.php' AND `user_code` LIKE '$users_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $count1 = mysqli_num_rows($query);
if($count1 == 0){
    $sql = "SELECT * FROM `master_cbr_main_details` WHERE `project` LIKE 'CTS' AND `file_url` LIKE 'chicken_customerledger_masterreport1.php' AND `user_code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $count2 = mysqli_num_rows($query);
}
if($count1 > 0 || $count2 > 0){
    while($row = mysqli_fetch_assoc($query)){
        $file_code = $row['code'];
        $file_name = $row['file_name'];
        $usr_code = $row['user_code'];
        $ccount = $row['column_count'];
        $cus_cdays_flag = $row['cus_cdays_flag'];
        $cus_outbal_flag = $row['cus_outbal_flag'];
        $field_calign_flag = $row['field_calign_flag'];
        $logo_ascom_flag = $row['logo_ascom_flag']; $slogo_path = $row['logo_path'];
        $view_normal_flag = $row['view_normal_flag'];
        $view_excel_flag = $row['view_excel_flag'];
        $view_print_flag = $row['view_print_flag'];
        $view_pdf_flag = $row['view_pdf_flag'];
        $send_wapp_flag = $row['send_wapp_flag'];

        for($i = 1;$i <= $ccount;$i++){
            $cname = "c".$i; $cval1 = $row[$cname]; $cval2 = explode(":",$cval1);
            if($cval2[0] == "A" && $cval2[1] == "1" && (float)$cval2[2] > 0){
                $acname[$cval1] = $cname; $ac_cnt++;
            }
            else if($cval2[0] == "A" && $cval2[1] == "0" && (float)$cval2[2] > 0){
                $icname[$cval1] = $cname;
            }
            else{ }
        }
    }

    $sql = "SELECT * FROM `master_cbr_header_names` WHERE `link_code` LIKE '$file_code' AND `user_code` LIKE '$usr_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `mst_col_name` ASC";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $tbl_col_name[$row['mst_col_name']] = $row['tbl_col_name'];
        $rpt_col_name[$row['mst_col_name']] = $row['rpt_col_name'];
        $rpt_col_type[$row['mst_col_name']] = $row['col_type'];
        if((int)$field_calign_flag == 1){
            if($row['tbl_col_name'] == "date" || $row['tbl_col_name'] == "trns_type2"){
                $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;"';
            }
            else{
                if($row['tbl_col_name'] == "cr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:green;"'; }
                else if($row['tbl_col_name'] == "dr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:blue;"'; }
                else if($row['tbl_col_name'] == "cr_amt" || $row['tbl_col_name'] == "odue_days"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:red;"'; }
                else{ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;"'; }
            }
        }
        else if($row['col_type'] == "order_date" || $row['col_type'] == "order"){
            $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;"';
        }
        else if($row['col_type'] == "order_num"){
            if($row['tbl_col_name'] == "cr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:green;"'; }
            else if($row['tbl_col_name'] == "dr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:blue;"'; }
            else if($row['tbl_col_name'] == "cr_amt" || $row['tbl_col_name'] == "odue_days"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:red;"'; }
            else{ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;"'; }
        }
        else{ }
    }
}
if($cus_cdays_flag == ""){ $cus_cdays_flag = 0; }
if($cus_outbal_flag == ""){ $cus_outbal_flag = 0; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Customer Ledger Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $loc_access = $row['loc_access'];
    $cgroup_access = $row['cgroup_access'];
    if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $utype = "S"; }
    else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $utype = "A"; }
    else if($row['normal_access'] == 1 || $row['normal_access'] == "1"){ $utype = "N"; }
    else{ $utype = "N"; }
}
$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0'".$user_sector_filter." ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_obtype = $cus_obamt = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_mobile[$row['code']] = $row['mobileno']; $cus_obtype[$row['code']] = $row['obtype']; $cus_obamt[$row['code']] = $row['obamt']; $credit_days[$row['code']] = $row['creditdays']; }

//Supplier Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' AND `dflag` = '0'".$user_sector_filter." ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Account Modes
$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $acc_mode = array();
while($row = mysqli_fetch_assoc($query)){ $acc_mode[$row['code']] = $row['description']; }

//Item Details
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_sname = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_sname[$row['code']] = $row['short_name']; $item_category[$row['code']] = $row['category']; }
//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$sql = "SELECT * FROM `crdr_note_reasons` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $reason_code = $reason_name = array();
while($row = mysqli_fetch_assoc($query)){ $reason_code[$row['code']] = $row['code']; $reason_name[$row['code']] = $row['description']; }

$fdate = $tdate = $today = date("Y-m-d"); $vendors = "select"; $fstyles = $fsizes = "default"; $inc_sac = $otb_amt = $odcr_amt = $oddr_amt = 0;
$exports = "display";
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    if($_POST['inc_sac'] == true || $_POST['inc_sac'] == "on" || $_POST['inc_sac'] == "1"){ $inc_sac = 1; } else{ $inc_sac = 0; }

    $exports = $_POST['exports'];
    //Sales
    $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$today' AND `customercode` = '$vendors' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`id` ASC";
    $query = mysqli_query($conn,$sql); $i = $opn_csale = $btw_csale = 0; $old_inv1 = $old_inv2 = $old_inv3 = $old_date = ""; $cus_sale = $sale_dcount = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtotime($row['date']) < strtotime($fdate)){
            if($old_inv1 != $row['invoice']){ $old_inv1 = $row['invoice']; $opn_csale += (float)$row['finaltotal']; $oddr_amt += (float)$row['finaltotal']; }
        }
        else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
            if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
            $cus_sale[$key] = $row['date']."@".$row['invoice']."@".$row['so_trnum']."@".$row['link_trnum']."@".$row['bookinvoice']."@".$row['customercode']."@".$row['sup_code']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['sent_weight']."@".$row['mort_weight']."@".$row['order_qty']."@".$row['delivery_qty']."@".$row['farm_weight']."@".$row['netweight']."@".$row['actual_price']."@".$row['addOnPrice']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcds_type1']."@".$row['tcds_type2']."@".$row['tcdsamt']."@".$row['delivery_charge']."@".$row['dressing_charge']."@".$row['transporter_code']."@".$row['freight_amount']."@".$row['freight_amt']."@".$row['freight_price_perjal']."@".$row['freight_amount_jal']."@".$row['roundoff_type1']."@".$row['roundoff_type2']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['remarks']."@".$row['drivercode']."@".$row['vehiclecode']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updated']."@".$row['trlink']."@".$row['description'];
            $sale_dcount[$row['date']] = $i;
            if($old_inv2 != $row['invoice']){ $old_inv2 = $row['invoice']; $btw_csale += (float)$row['finaltotal']; }
        } else{ }
        if($old_inv3 != $row['invoice']){ $old_inv3 = $row['invoice']; }
    }
    //Receipt
    $sql = "SELECT * FROM `customer_receipts` WHERE `date` <= '$today' AND `ccode` = '$vendors' AND `vtype` = 'C' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
    $query = mysqli_query($conn,$sql); $i = $opn_crct = $btw_crct = 0; $old_date = ""; $cus_rcts = $crct_dcount = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtotime($row['date']) < strtotime($fdate)){
            $opn_crct += (float)$row['amount'];
        }
        else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
            if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
            $cus_rcts[$key] = $row['trnum']."@".$row['link_trnum']."@".$row['sales_trnum']."@".$row['ccn_trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['trlink'];
            $crct_dcount[$row['date']] = $i;
            $btw_crct += (float)$row['amount'];
        } else{ }
        $odcr_amt += (float)$row['amount'];
    }
    //Customer Crdr
    $sql = "SELECT * FROM `main_crdrnote` WHERE `date` <= '$today' AND `ccode` = '$vendors' AND `mode` IN ('CCN','CDN') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
    $query = mysqli_query($conn,$sql); $i = $j = $opn_cccn = $opn_ccdn = $btw_ccdn = $btw_cccn = 0; $old_date1 = $old_date2 = ""; $cus_ccns = $ccn_dcount = $cus_cdns = $cdn_dcount = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtotime($row['date']) < strtotime($fdate)){
            if($row['mode'] == "CDN"){
                $opn_ccdn += (float)$row['amount'];
                $oddr_amt += (float)$row['amount'];
            }
            else if($row['mode'] == "CCN"){
                $opn_cccn += (float)$row['amount'];
            }
            else{ }
        }
        else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
            if($row['mode'] == "CCN"){
                if(strtotime($old_date1) != strtotime($row['date'])){ $old_date1 = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
                $cus_ccns[$key] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['reason_code'];
                $ccn_dcount[$row['date']] = $i;
                $btw_cccn += (float)$row['amount'];
            }
            else if($row['mode'] == "CDN"){
                if(strtotime($old_date2) != strtotime($row['date'])){ $old_date2 = $row['date']; $j = 0; } $j++; $key = $row['date']."@".$j;
                $cus_cdns[$key] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['reason_code'];
                $cdn_dcount[$row['date']] = $j;
                $btw_ccdn += (float)$row['amount'];
            }
        } else{ }
        if($row['mode'] == "CDN"){ } else if($row['mode'] == "CCN"){ $odcr_amt += (float)$row['amount']; } else{ }
    }
    //Sales Return
    $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$today' AND `vcode` = '$vendors' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
    $query = mysqli_query($conn,$sql); $i = $opn_csrtn = $btw_csrtn = 0; $old_date = ""; $cus_srtn = $srtn_dcount = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtotime($row['date']) < strtotime($fdate)){
            $opn_csrtn += (float)$row['amount'];
        }
        else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
            if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
            $cus_srtn[$key] = $row['trnum']."@".$row['date']."@".$row['inv_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['rtype']."@".$row['warehouse']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
            $srtn_dcount[$row['date']] = $i;
            $btw_csrtn += (float)$row['amount'];
        } else{ }
        $odcr_amt += (float)$row['amount'];
    }
    //Customer Mortality
    $sql = "SELECT * FROM `main_mortality` WHERE `date` <= '$today' AND `ccode` = '$vendors' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`code`,`id` ASC";
    $query = mysqli_query($conn,$sql); $i = $opn_csmort = $btw_csmort = 0; $old_date = ""; $cus_smort = $smort_dcount = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtotime($row['date']) < strtotime($fdate)){
            $opn_csmort += (float)$row['amount'];
        }
        else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
            if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
            $cus_smort[$key] = $row['code']."@".$row['date']."@".$row['ccode']."@".$row['invoice']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
            $smort_dcount[$row['date']] = $i;
            $btw_csmort += (float)$row['amount'];
        } else{ }
        $odcr_amt += (float)$row['amount'];
    }
    $opn_cpur = $btw_cpur = $opn_cpay = $btw_cpay = $opn_ssdn = $opn_sscn = $btw_csdn = $btw_cscn = $opn_sprtn = $btw_sprtn = $opn_spmort = $btw_spmort = 0;
    $sup_pur = $pur_dcount = $sup_pays = $cpay_dcount = $sup_scns = $scn_dcount = $sup_sdns = $sdn_dcount = $sup_prtn = $prtn_dcount = $sup_pmort = $pmort_dcount = array();
    if((int)$inc_sac == 1){
        //Purchase
        $sql = "SELECT * FROM `pur_purchase` WHERE `date` <= '$today' AND `vendorcode` = '$vendors' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`id` ASC";
        $query = mysqli_query($conn,$sql); $i = $opn_cpur = $btw_cpur = 0; $old_pinv1 = $old_pinv2 = $old_pinv3 = $old_date = ""; $sup_pur = $pur_dcount = array();
        while($row = mysqli_fetch_assoc($query)){
            if(strtotime($row['date']) < strtotime($fdate)){
                if($old_pinv1 != $row['invoice']){ $old_pinv1 = $row['invoice']; $opn_cpur += (float)$row['finaltotal']; }
            }
            else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
                if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
                $sup_pur[$key] = $row['date']."@".$row['invoice']."@".$row['po_trnum']."@".$row['link_trnum']."@".$row['bookinvoice']."@".$row['vendorcode']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcds_type1']."@".$row['tcds_type2']."@".$row['tcdsamt']."@".$row['transporter_code']."@".$row['freight_amount']."@".$row['roundoff_type1']."@".$row['roundoff_type2']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['remarks']."@".$row['drivercode']."@".$row['vehiclecode']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updated']."@".$row['trlink'];
                $pur_dcount[$row['date']] = $i;
                if($old_inv2 != $row['invoice']){ $old_inv2 = $row['invoice']; $btw_cpur += (float)$row['finaltotal']; }
            } else{ }
            if($old_inv3 != $row['invoice']){ $old_inv3 = $row['invoice']; }
        }
        //Payment
        $sql = "SELECT * FROM `pur_payments` WHERE `date` <= '$today' AND `ccode` = '$vendors' AND `vtype` = 'S' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
        $query = mysqli_query($conn,$sql); $i = $opn_cpay = $btw_cpay = 0; $old_date = ""; $sup_pays = $cpay_dcount = array();
        while($row = mysqli_fetch_assoc($query)){
            if(strtotime($row['date']) < strtotime($fdate)){
                $opn_cpay += (float)$row['amount'];
            }
            else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
                if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
                $sup_pays[$key] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['trlink'];
                $cpay_dcount[$row['date']] = $i;
                $btw_cpay += (float)$row['amount'];
            } else{ }
            //$odcr_amt += (float)$row['amount'];
        }
        //Supplier Crdr
        $sql = "SELECT * FROM `main_crdrnote` WHERE `date` <= '$today' AND `ccode` = '$vendors' AND `mode` IN ('SCN','SDN') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
        $query = mysqli_query($conn,$sql); $i = $j = $opn_ssdn = $opn_sscn = $btw_csdn = $btw_cscn = 0; $old_date1 = $old_date2 = ""; $sup_scns = $scn_dcount = $sup_sdns = $sdn_dcount = array();
        while($row = mysqli_fetch_assoc($query)){
            if(strtotime($row['date']) < strtotime($fdate)){
                if($row['mode'] == "SDN"){
                    $opn_ssdn += (float)$row['amount'];
                    //$oddr_amt += (float)$row['amount'];
                }
                else if($row['mode'] == "SCN"){
                    $opn_sscn += (float)$row['amount'];
                }
                else{ }
            }
            else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
                if($row['mode'] == "SCN"){
                    if(strtotime($old_date1) != strtotime($row['date'])){ $old_date1 = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
                    $sup_scns[$key] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['reason_code'];
                    $scn_dcount[$row['date']] = $i;
                    $btw_cscn += (float)$row['amount'];
                }
                else if($row['mode'] == "SDN"){
                    if(strtotime($old_date2) != strtotime($row['date'])){ $old_date2 = $row['date']; $j = 0; } $j++; $key = $row['date']."@".$j;
                    $sup_sdns[$key] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['approvedemp']."@".$row['approvedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['reason_code'];
                    $sdn_dcount[$row['date']] = $j;
                    $btw_csdn += (float)$row['amount'];
                }
            } else{ }
            if($row['mode'] == "SDN"){
                //$odcr_amt += (float)$row['amount'];
            }
            else if($row['mode'] == "SCN"){ } else{ }
        }
        //Purchase Return
        $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$today' AND `vcode` = '$vendors' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
        $query = mysqli_query($conn,$sql); $i = $opn_sprtn = $btw_sprtn = 0; $old_date = ""; $sup_prtn = $prtn_dcount = array();
        while($row = mysqli_fetch_assoc($query)){
            if(strtotime($row['date']) < strtotime($fdate)){
                $opn_sprtn += (float)$row['amount'];
            }
            else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
                if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
                $sup_prtn[$key] = $row['trnum']."@".$row['date']."@".$row['inv_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['rtype']."@".$row['warehouse']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                $prtn_dcount[$row['date']] = $i;
                $btw_sprtn += (float)$row['amount'];
            } else{ }
            //$odcr_amt += (float)$row['amount'];
        }
        //Supplier Mortality
        $sql = "SELECT * FROM `main_mortality` WHERE `date` <= '$today' AND `ccode` = '$vendors' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`code`,`id` ASC";
        $query = mysqli_query($conn,$sql); $i = $opn_spmort = $btw_spmort = 0; $old_date = ""; $sup_pmort = $pmort_dcount = array();
        while($row = mysqli_fetch_assoc($query)){
            if(strtotime($row['date']) < strtotime($fdate)){
                $opn_spmort += (float)$row['amount'];
            }
            else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
                if(strtotime($old_date) != strtotime($row['date'])){ $old_date = $row['date']; $i = 0; } $i++; $key = $row['date']."@".$i;
                $sup_pmort[$key] = $row['code']."@".$row['date']."@".$row['ccode']."@".$row['invoice']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                $pmort_dcount[$row['date']] = $i;
                $btw_spmort += (float)$row['amount'];
            } else{ }
            //$odcr_amt += (float)$row['amount'];
        }
    }

    $opn_cramt = $opn_dramt = 0; if($cus_obtype[$vendors] == "Cr"){ $opn_cramt = (float)$cus_obamt[$vendors]; } else{ $opn_dramt = (float)$cus_obamt[$vendors]; }
    
    $opn_sale = (float)$opn_csale + (float)$opn_ccdn + (float)$opn_cpay + (float)$opn_sscn + (float)$opn_sprtn + (float)$opn_spmort + (float)$opn_dramt;
    $opn_receipt = (float)$opn_cpur + (float)$opn_ssdn + (float)$opn_crct + (float)$opn_cccn + (float)$opn_csrtn + (float)$opn_csmort + (float)$opn_cramt;
    
    $btw_sale = (float)$btw_csale + (float)$btw_ccdn + (float)$btw_cpay + (float)$btw_cscn + (float)$btw_sprtn + (float)$btw_spmort;
    $btw_receipt = (float)$btw_cpur + (float)$btw_csdn + (float)$btw_crct + (float)$btw_cccn + (float)$btw_csrtn + (float)$btw_csmort;
    
    $tot_sales = ((float)$opn_sale + (float)$btw_sale);
    $tot_receipt = ((float)$opn_receipt + (float)$btw_receipt);
    $otb_amt = ((float)$tot_sales - (float)$tot_receipt);

    //Over Due Calculations for Opening Balances
    $odcr_amt = ((float)$odcr_amt - (float)$oddr_amt);
}
?>
<html>
	<head>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            .main-table { white-space: nowrap; }
            .tbody1{
                color: black;
            }
        </style>
	</head>
	<body>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table <?php if($exports == "print") { echo ' class="main-table"'; } else{ echo ' class="table-sm table-hover main-table2"'; } ?>>
                        <thead class="thead1">
                            <tr>
                                <?php if((int)$logo_ascom_flag == 1){ ?>
                                <td colspan="4"><img src="<?php echo "../".$slogo_path; ?>" height="200px"/></td>
                                <?php } else{ ?>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <?php } ?>
                                <td colspan="15" align="center">
                                    <?php
                                    if($dbname != "poulso6_chicken_tn_nataraj_broilers"){
                                    ?>
                                    <h3><?php echo $file_name; ?></h3>
                                    <label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;
                                    <label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                    <?php if($vendors != "select"){ echo "<h3>".$cus_name[$vendors]." (".$cus_mobile[$vendors].")</h3>"; } ?>
                                    <?php if(isset($_POST['submit']) == true && (int)$cus_outbal_flag == 1){ echo "<h3 style='color:red;'>BALANCE: ".number_format_ind($otb_amt)."</h3>"; } ?>
                                    <?php if(isset($_POST['submit']) == true && (int)$cus_cdays_flag == 1){ echo "<h5 style='color:green;'>Credit Days: ".str_replace(".00","",number_format_ind($credit_days[$vendors]))."</h5>"; } ?>
                                    <?php
                                    }
                                    else{
                                    ?>
                                        <?php if($vendors != "select"){ echo "<h3>".$cus_name[$vendors]." <br/>".$cus_mobile[$vendors]."</h3>"; } ?>
                                        <label><b style="color: green;">Statement From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;
                                        <label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                        <?php if(isset($_POST['submit']) == true && (int)$cus_outbal_flag == 1){ echo "<h3 style='color:red;'>BALANCE: ".number_format_ind($otb_amt)."</h3>"; } ?>
                                        <?php if(isset($_POST['submit']) == true && (int)$cus_cdays_flag == 1){ echo "<h5 style='color:green;'>Credit Days: ".str_replace(".00","",number_format_ind($credit_days[$vendors]))."</h5>"; } ?>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="vendors">Customer</label>
                                            <select name="vendors" id="vendors" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($vendors == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vendors == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="mr-2 form-group" style="width:auto;">
                                            <label for="inc_sac">S&amp;C</label>
                                            <input type="checkbox" name="inc_sac" id="inc_sac" class="form-control" style="text-align:center;" <?php if($inc_sac == 1){ echo "checked"; } ?> />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div> &nbsp;&nbsp;
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit" onclick="excelfiledownload('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>')">Excel</button>&nbsp;&nbsp;
                                        </div>
                                         <div class="form-group">
                                            <br/><button type="button" class="btn btn-warning btn-sm" onclick="openNewTab(event)">Print</button>&nbsp;&nbsp;
                                        </div>
                                    <!--</div>
                                    <div class="m-1 p-1 row">-->
                                        <?php if((int)$font_fflag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="fstyles">Font-Family</label>
                                            <select name="fstyles" id="fstyles" class="form-control select2" style="width:180px;">
                                                <option value="default" <?php if($fstyles == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_id as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fstyles == $scode){ echo "selected"; } ?>><?php echo $font_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:70px;">
                                            <label for="fsizes">Font-Size</label>
                                            <select name="fsizes" id="fsizes" class="form-control select2" style="width:60px;">
                                                <option value="default" <?php if($fsizes == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_sizes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fsizes == $scode){ echo "selected"; } ?>><?php echo $font_sizes[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="width:150px;visibility: hidden;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                                <?php
                                                if($_SERVER['REMOTE_ADDR'] == "49.205.129.174"){
                                                ?>
                                                <option value="img_download" <?php if($exports == "img_download"){ echo "selected"; } ?>>-Image Download-</option>
                                                <option value="pdf_download" <?php if($exports == "pdf_download"){ echo "selected"; } ?>>-PDF Download-</option>
                                                <option value="img_wapp" <?php if($exports == "img_wapp"){ echo "selected"; } ?>>-Image WhatsApp-</option>
                                                <option value="pdf_wapp" <?php if($exports == "pdf_wapp"){ echo "selected"; } ?>>-PDF WhatsApp-</option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        
                                    </div>
								</td>
							</tr>
						</thead>
                    <?php if($exports == "display" || $exports == "exportpdf"){ ?>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
                    <?php } ?>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            $cr_amt = $dr_amt = $rb_amt = $ocr_amt = $odr_amt = 0;
                            if((float)$opn_sale > (float)$opn_receipt){
                                $dr_amt = $rb_amt = (float)$opn_sale - (float)$opn_receipt;
                            }
                            else{
                                $cr_amt = (float)$opn_receipt - (float)$opn_sale;
                                $rb_amt = (float)$opn_sale - (float)$opn_receipt;
                            }
                            
                            $ocr_amt = $cr_amt;
                            $odr_amt = $dr_amt;

                            $html = $hhtml = $nhtml = $fhtml = ''; $ifix_cnt = $ino_sval = 0;

                            $lpath1 = "../".$logopath; $cd_cnt = $io_cnt = 0;
                            if($ac_cnt > 4){ $cd_cnt = $ac_cnt - 4; $io_cnt = 2; } else if($ac_cnt == 4){ $cd_cnt = 2; $io_cnt = 1; } else{ $cd_cnt = $io_cnt = 1; }

                            
                            $hhtml .= '<tr>';
                            $hhtml .= '<td colspan="'.$io_cnt.'"><img src="'.$lpath1.'" height="150px"/></td>';
                            $hhtml .= '<td colspan="'.$io_cnt.'">'.$cmpy_fname.'</td>';
                            $hhtml .= '<td colspan="'.$cd_cnt.'" align="center">';
                            $hhtml .= '<h3>'.$file_name.'</h3>';
                            $hhtml .= '<label><b style="color: green;">From Date:</b>&nbsp;'.date("d.m.Y",strtotime($fdate)).'</label>&ensp;&ensp;';
                            $hhtml .= '<label><b style="color: green;">To Date:</b>&nbsp;'.date("d.m.Y",strtotime($tdate)).'</label>';
                            if($vendors != "select"){ $hhtml .= '<h3>'.$cus_name[$vendors].' ('.$cus_mobile[$vendors].')</h3>'; }
                            if(isset($_POST['submit']) == true){ $hhtml .= '<h3 style="color:red;">BALANCE: '.number_format_ind($otb_amt).'</h3>'; }
                            $hhtml .= '</td>';
                            $hhtml .= '</tr>';
                            

                            $html .= '<thead class="thead2" id="head_names">';
                            $nhtml .= '<tr>'; $fhtml .= '<tr>';
                            for($i = 1;$i <= $ccount;$i++){
                                $key1 = "A:1:".$i; $key2 = "A:0:".$i;
                                if(empty($acname[$key1]) && $acname[$key1] == "" && empty($icname[$key2]) && $icname[$key2] == ""){ }
                                else{
                                    //echo "<br/>".$acname[$key1]."@".$key1."@";
                                    //echo "<br/>".$icname[$key2]."@".$key1."@";
                                    $cname = $checked = ""; if(!empty($acname[$key1])){ $cname = $acname[$key1]; $checked = "checked"; } else if(!empty($icname[$key2])){ $cname = $icname[$key2]; } else{ }
                                    if($cname != ""){
                                        if($exports == "display" || $exports == "exportpdf") {
                                            echo '<input type="checkbox" class="hide_show" id="'.$cname.'" onclick="update_masterreport_status(this.id);"'.$checked.'><span>'.$rpt_col_name[$cname].'</span>&ensp;';
                                        }
                                    }
                                    if(empty($acname[$key1]) && $acname[$key1] == ""){ }
                                    else{
                                        $nhtml .= '<th>'.$rpt_col_name[$cname].'</th>';
                                        $fhtml .= '<th id="'.$rpt_col_type[$cname].'">'.$rpt_col_name[$cname].'</th>';

                                        //check initial values for total Columns
                                        if($rpt_col_type[$cname] != "order_num" && $ino_sval == 0){ $ifix_cnt++; $ini_val1 = $i; } else{ $ino_sval++; }
                                    }
                                }
                            }
                            $nhtml .= '</tr>'; $fhtml .= '</tr>';
                            $html .= $fhtml;
                            $html .= '</thead>';
                            $html .= '<thead class="tbody1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="'.$ifix_cnt.'">Opening Balance</th>';
                            for($i = $ini_val1 + 1;$i <= $ccount;$i++){
                                $key1 = "A:1:".$i;
                                if(empty($acname[$key1]) && $acname[$key1] == ""){ }
                                else{
                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                    if($cname != ""){
                                        if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ }
                                        else{
                                            $tcname = $tbl_col_name[$cname];
                                            if($tcname == "cr_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; }
                                            else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                            else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                            else{
                                                $html .= '<th></th>';
                                            }
                                        }
                                    }
                                }
                            }
                            $html .= '</tr>';
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';

                            $old_inv1 = "";
                            $tot_jals = $tot_birds = $tot_tweight = $tot_eweight = $tot_sweight = $tot_mweight = $tot_oweight = $tot_dweight = $tot_fweight = $tot_nweight = 
                            $tot_iamount = $tot_dvryamt = $tot_dresamt = $tot_jfrtamt = $tot_tcdsamt = $tot_frtamt1 = $tot_frtamt2 = $tot_rndfamt = $tot_finlamt = $bcr_amt = $bdr_amt = 0;
                            for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                                $adate = date("Y-m-d",$cdate);

                                //Sales
                                if(empty($sale_dcount[$adate]) || $sale_dcount[$adate] == "" || $sale_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $sale_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($cus_sale[$mkey]) || $cus_sale[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$cus_sale[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            if($old_inv1 != $tr_info[1]){
                                                $odue_days = 0;
                                                $old_inv1 = $tr_info[1]; $dr_amt = (float)$tr_info[36]; $rb_amt += (float)$dr_amt;
                                                $tot_tcdsamt += (float)$tr_info[25];
                                                $tot_frtamt1 += (float)$tr_info[29];
                                                $tot_frtamt2 += (float)$tr_info[30];
                                                $tot_rndfamt += (float)$tr_info[35];
                                                $tot_finlamt += (float)$tr_info[36];
                                            
                                                //Over Due Calculations for between days calculations
                                                $odcr_amt = ((float)$odcr_amt - (float)$tr_info[36]);
                                                if((float)$odcr_amt < 0){ $odue_days = ((strtotime($today) - strtotime($tr_info[0])) / 60 / 60 / 24); }
                                            }

                                            //Total Calculations
                                            $tot_jals += (float)$tr_info[8];
                                            $tot_birds += (float)$tr_info[9];
                                            $tot_tweight += (float)$tr_info[10];
                                            $tot_eweight += (float)$tr_info[11];
                                            $tot_sweight += (float)$tr_info[12];
                                            $tot_mweight += (float)$tr_info[13];
                                            $tot_oweight += (float)$tr_info[14];
                                            $tot_dweight += (float)$tr_info[15];
                                            $tot_fweight += (float)$tr_info[16];
                                            $tot_nweight += (float)$tr_info[17];
                                            $tot_iamount += (float)$tr_info[21];
                                            $tot_dvryamt += (float)$tr_info[25];
                                            $tot_dresamt += (float)$tr_info[26];
                                            $tot_jfrtamt += (float)$tr_info[32];

                                            $bdr_amt += (float)$dr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[0])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[1].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[2].'</td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[4].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[5]].'</td>'; }
                                                        else if($tcname == "sup_code"){
                                                            if((int)$sup_mnuname_flag == 1){
                                                                $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[48].'</td>';
                                                            }
                                                            else{
                                                                $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sup_name[$tr_info[6]].'</td>';
                                                            }
                                                        }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_name[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_sname[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[8])).'</td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[9])).'</td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[10]).'</td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[11]).'</td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[12]).'</td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[13]).'</td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[14]).'</td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[15]).'</td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[16]).'</td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[17]).'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[18]).'</td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[19]).'</td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[20]).'</td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[21]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[22]).'</td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[23].'</td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[24].'</td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[25]).'</td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[26]).'</td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[27]).'</td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[28].'</td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[29].'</td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[30]).'</td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[31]).'</td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[32]).'</td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[33].'</td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[34].'</td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[35]).'</td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[36]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[37]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[38].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[39].'</td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[40].'</td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[41]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[42])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[43]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[44].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[45]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[46])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[47].'</td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        
                                                        else if($tcname == "odue_days"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($odue_days)).'</td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Sales</td>'; }
                                                        else if($tcname == "trns_type2"){
                                                            if($tr_info[47] == "chicken_display_multiplesale6.php"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">SPS / '.$item_name[$tr_info[7]].'</td>'; }
                                                            else{ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Sales / '.$item_name[$tr_info[7]].'</td>'; }           
                                                        }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Purchase
                                if(empty($pur_dcount[$adate]) || $pur_dcount[$adate] == "" || $pur_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $pur_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($sup_pur[$mkey]) || $sup_pur[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$sup_pur[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            if($old_inv1 != $tr_info[1]){
                                                $old_inv1 = $tr_info[1]; $cr_amt = (float)$tr_info[23]; $rb_amt -= (float)$cr_amt;
                                                $tot_tcdsamt += (float)$tr_info[17];
                                                $tot_frtamt1 += (float)$tr_info[19];
                                                $tot_frtamt2 = 0;
                                                $tot_rndfamt += (float)$tr_info[22];
                                                $tot_finlamt += (float)$tr_info[23];
                                            }

                                            //Total Calculations
                                            $tot_jals += (float)$tr_info[7];
                                            $tot_birds += (float)$tr_info[8];
                                            $tot_tweight += (float)$tr_info[9];
                                            $tot_eweight += (float)$tr_info[10];
                                            $tot_sweight = 0;
                                            $tot_mweight = 0;
                                            $tot_oweight = 0;
                                            $tot_dweight = 0;
                                            $tot_fweight = 0;
                                            $tot_nweight += (float)$tr_info[11];
                                            $tot_iamount += (float)$tr_info[13];
                                            $tot_dvryamt = 0;
                                            $tot_dresamt = 0;
                                            $tot_jfrtamt = 0;

                                            $bcr_amt += (float)$cr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[0])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[1].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[2].'</td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[4].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[5]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_name[$tr_info[6]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_sname[$tr_info[6]].'</td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[7])).'</td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[8])).'</td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[9]).'</td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[10]).'</td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[11]).'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[12]).'</td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[13]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[14]).'</td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[15].'</td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[16].'</td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[17]).'</td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[18].'</td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[19].'</td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[20].'</td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[21].'</td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[22]).'</td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[23]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[24]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[25].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[26].'</td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[27].'</td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[28]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[29])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[30]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[31].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[32]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[33])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[34].'</td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Purchases</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Purchases / '.$item_name[$tr_info[7]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Receipt
                                if(empty($crct_dcount[$adate]) || $crct_dcount[$adate] == "" || $crct_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $crct_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($cus_rcts[$mkey]) || $cus_rcts[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$cus_rcts[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            $cr_amt = (float)$tr_info[9]; $rb_amt -= (float)$cr_amt;
                                            $bcr_amt += (float)$cr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[4])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[2].'</td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[1].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[6].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[5]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$acc_mode[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$coa_name[$tr_info[8]].'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[9]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[9]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[10]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[11].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[12]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[13])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[14]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[15].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[16]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[17])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[18].'</td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($rb_amt)).'</td>'; }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:center;color:black;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Receipt</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Receipt / '.$acc_mode[$tr_info[7]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Payment
                                if(empty($cpay_dcount[$adate]) || $cpay_dcount[$adate] == "" || $cpay_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $cpay_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($sup_pays[$mkey]) || $sup_pays[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$sup_pays[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            $dr_amt = (float)$tr_info[6]; $rb_amt += (float)$dr_amt;
                                            $bdr_amt += (float)$dr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$acc_mode[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$coa_name[$tr_info[5]].'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[8].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[9]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[10])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[12].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[13]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[14])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[15].'</td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($rb_amt)).'</td>'; }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:center;color:black;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Payment</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Payment / '.$acc_mode[$tr_info[4]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Customer Debit Note
                                if(empty($cdn_dcount[$adate]) || $cdn_dcount[$adate] == "" || $cdn_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $cdn_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($cus_cdns[$mkey]) || $cus_cdns[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$cus_cdns[$mkey]);

                                            $cr_amt = $dr_amt = $odue_days = 0;
                                            $dr_amt = (float)$tr_info[6]; $rb_amt += (float)$dr_amt;
                                            
                                            //Over Due Calculations for between days calculations
                                            $odcr_amt = ((float)$odcr_amt - (float)$dr_amt);
                                            if((float)$odcr_amt <= 0){ $odue_days = ((strtotime($today) - strtotime($tr_info[1])) / 60 / 60 / 24); }

                                            $bdr_amt += (float)$dr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$coa_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[8].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[9]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[10])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[12].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[13]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[14])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($odue_days)).'</td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Debit Note</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Debit Note / '.$tr_info[15].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Supplier Debit Note
                                if(empty($sdn_dcount[$adate]) || $sdn_dcount[$adate] == "" || $sdn_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $sdn_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($sup_sdns[$mkey]) || $sup_sdns[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$sup_sdns[$mkey]);

                                            $cr_amt = $dr_amt = $odue_days = 0;
                                            $cr_amt = (float)$tr_info[6]; $rb_amt -= (float)$cr_amt;
                                            $bcr_amt += (float)$cr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                echo "reason: ".$tr_info[15];
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$coa_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[8].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[9]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[10])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[12].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[13]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[14])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($odue_days)).'</td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Debit Note</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Debit Note / '.$tr_info[15].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Customer Credit Note
                                if(empty($ccn_dcount[$adate]) || $ccn_dcount[$adate] == "" || $ccn_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $ccn_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($cus_ccns[$mkey]) || $cus_ccns[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$cus_ccns[$mkey]);
                                            print_r($tr_info);
                                            $cr_amt = $dr_amt = 0;
                                            $cr_amt = (float)$tr_info[6]; $rb_amt -= (float)$cr_amt;
                                            $bcr_amt += (float)$cr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$coa_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[8].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[9]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[10])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[12].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[13]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[14])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Credit Note</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Credit Note / '.$reason_name[$tr_info[15]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Supplier Credit Note
                                if(empty($scn_dcount[$adate]) || $scn_dcount[$adate] == "" || $scn_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $scn_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($sup_scns[$mkey]) || $sup_scns[$mkey] == ""){ }
                                        else{
                                            echo "else";
                                            $tr_info = array(); $tr_info = explode("@",$sup_scns[$mkey]);
                                             echo "reason: ".$tr_info[15];
                                            $cr_amt = $dr_amt = 0;
                                            $dr_amt = (float)$tr_info[6]; $rb_amt += (float)$dr_amt;
                                            $bdr_amt += (float)$dr_amt;

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$coa_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[7]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[8].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[9]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[10])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[12].'</td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[13]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[14])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Credit Note</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Credit Note / '.$reason_name[$tr_info[15]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Sales Return
                                if(empty($srtn_dcount[$adate]) || $srtn_dcount[$adate] == "" || $srtn_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $srtn_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($cus_srtn[$mkey]) || $cus_srtn[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$cus_srtn[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            $cr_amt = (float)$tr_info[9]; $rb_amt -= (float)$cr_amt;
                                            $bcr_amt += (float)$cr_amt;

                                            $tot_jals -= (float)$tr_info[5];
                                            $tot_birds -= (float)$tr_info[6];
                                            $tot_nweight -= (float)$tr_info[7];

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[2].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[3]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_sname[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[5])).'</td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[6])).'</td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[7]).'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[8]).'</td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } //'.number_format_ind($tr_info[9]).'
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[9]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[12]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[13])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[14]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[15])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Sales Return</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Sales Return / '.$item_name[$tr_info[4]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Purchase Return
                                if(empty($prtn_dcount[$adate]) || $prtn_dcount[$adate] == "" || $prtn_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $prtn_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($sup_prtn[$mkey]) || $sup_prtn[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$sup_prtn[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            $dr_amt = (float)$tr_info[9]; $rb_amt += (float)$dr_amt;
                                            $bdr_amt += (float)$dr_amt;

                                            $tot_jals -= (float)$tr_info[5];
                                            $tot_birds -= (float)$tr_info[6];
                                            $tot_nweight -= (float)$tr_info[7];

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[2].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[3]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_sname[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[5])).'</td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[6])).'</td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[7]).'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[8]).'</td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[9]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[9]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$sector_name[$tr_info[11]].'</td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[12]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[13])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[14]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[15])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Purchase Return</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Purchase Return / '.$item_name[$tr_info[4]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }

                                //Customer Mortality
                                if(empty($smort_dcount[$adate]) || $smort_dcount[$adate] == "" || $smort_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $smort_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($cus_smort[$mkey]) || $cus_smort[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$cus_smort[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            $cr_amt = (float)$tr_info[8]; $rb_amt -= (float)$cr_amt;
                                            $bcr_amt += (float)$cr_amt;

                                            //$tot_jals -= (float)$tr_info[5];
                                            $tot_birds -= (float)$tr_info[5];
                                            $tot_nweight -= (float)$tr_info[6];

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_sname[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[5])).'</td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[7]).'</td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[8]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[8]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[9].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[10]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[11])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[12]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[13])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Mortality</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Mortality / '.$item_name[$tr_info[4]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }
                                
                                //Supplier Mortality
                                if(empty($pmort_dcount[$adate]) || $pmort_dcount[$adate] == "" || $pmort_dcount[$adate] == 0){ }
                                else{
                                    $cnt = 0; $cnt = $pmort_dcount[$adate];
                                    for($i = 1;$i <= $cnt;$i++){
                                        $mkey = ""; $mkey = $adate."@".$i;
                                        if(empty($sup_pmort[$mkey]) || $sup_pmort[$mkey] == ""){ }
                                        else{
                                            $tr_info = array(); $tr_info = explode("@",$sup_pmort[$mkey]);

                                            $cr_amt = $dr_amt = 0;
                                            $dr_amt = (float)$tr_info[8]; $rb_amt += (float)$dr_amt;
                                            $bdr_amt += (float)$dr_amt;

                                            //$tot_jals -= (float)$tr_info[5];
                                            $tot_birds -= (float)$tr_info[5];
                                            $tot_nweight -= (float)$tr_info[6];

                                            $html .= '<tr>';
                                            for($j = 1;$j <= $ccount;$j++){
                                                $key1 = "A:1:".$j;
                                                if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                                else{
                                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                                        $tcname = $tbl_col_name[$cname];
                                                        if($tcname == "date"){ $html .= '<td class="dates" '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y",strtotime($tr_info[1])).'</td>'; }
                                                        else if($tcname == "invoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[0].'</td>'; }
                                                        else if($tcname == "so_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "link_trnum"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[3].'</td>'; }
                                                        else if($tcname == "bookinvoice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "customercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$cus_name[$tr_info[2]].'</td>'; }
                                                        else if($tcname == "sup_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_name[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "item_sname"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$item_sname[$tr_info[4]].'</td>'; }
                                                        else if($tcname == "jals"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "birds"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tr_info[5])).'</td>'; }
                                                        else if($tcname == "totalweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "emptyweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "sent_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "mort_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "order_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_qty"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "farm_weight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "netweight"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[6]).'</td>'; }
                                                        else if($tcname == "actual_price"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addOnPrice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "itemprice"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[7]).'</td>'; }
                                                        else if($tcname == "totalamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[8]).'</td>'; }
                                                        else if($tcname == "tcdsper"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcds_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "tcdsamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "delivery_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "dressing_charge"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "transporter_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_price_perjal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "freight_amount_jal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type1"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff_type2"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "roundoff"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "finaltotal"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tr_info[8]).'</td>'; }
                                                        else if($tcname == "warehouse"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "remarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$tr_info[9].'</td>'; }
                                                        else if($tcname == "drivercode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "vehiclecode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "addedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[10]].'</td>'; }
                                                        else if($tcname == "addedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[11])).'</td>'; }
                                                        else if($tcname == "approvedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "approvedtime"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "updatedemp"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$user_name[$tr_info[12]].'</td>'; }
                                                        else if($tcname == "updated"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.date("d.m.Y h:i:s A",strtotime($tr_info[13])).'</td>'; }
                                                        else if($tcname == "trlink"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; }
                                                        else if($tcname == "cr_amt"){ if((float)$cr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                                                        else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;"></td>'; }
                                                        else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Mortality</td>'; }
                                                        else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;">Mortality / '.$item_name[$tr_info[4]].'</td>'; }
                                                        else{ }
                                                    }
                                                }
                                            }
                                            $html .= '</tr>';
                                        }
                                    }
                                }
                                
                            }
                            $html .= '</tbody>';

                            $html .= '<thead class="tfoot1">';
                            
                            $html .= '<tr>';
                            $html .= '<th colspan="'.$ifix_cnt.'">Between Day closing</th>';
                            for($i = $ini_val1 + 1;$i <= $ccount;$i++){
                                $key1 = "A:1:".$i;
                                if(empty($acname[$key1]) && $acname[$key1] == ""){ }
                                else{
                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                    if($cname != ""){
                                        if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ }
                                        else{
                                            $tcname = $tbl_col_name[$cname];
                                            if($tcname == "jals"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tot_jals)).'</th>'; }
                                            else if($tcname == "birds"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.str_replace(".00","",number_format_ind($tot_birds)).'</th>'; }
                                            else if($tcname == "totalweight"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_tweight).'</th>'; }
                                            else if($tcname == "emptyweight"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_eweight).'</th>'; }
                                            else if($tcname == "sent_weight"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_sweight).'</th>'; }
                                            else if($tcname == "mort_weight"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_mweight).'</th>'; }
                                            else if($tcname == "order_qty"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_oweight).'</th>'; }
                                            else if($tcname == "delivery_qty"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_dweight).'</th>'; }
                                            else if($tcname == "farm_weight"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_fweight).'</th>'; }
                                            else if($tcname == "netweight"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_nweight).'</th>'; }
                                            else if($tcname == "totalamt"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_iamount).'</th>'; }
                                            else if($tcname == "tcdsamt"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_tcdsamt).'</th>'; }
                                            else if($tcname == "delivery_charge"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_dvryamt).'</th>'; }
                                            else if($tcname == "dressing_charge"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_dresamt).'</th>'; }
                                            else if($tcname == "freight_amount"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_frtamt1).'</th>'; }
                                            else if($tcname == "freight_amt"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_frtamt2).'</th>'; }
                                            else if($tcname == "freight_amount_jal"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_jfrtamt).'</th>'; }
                                            else if($tcname == "roundoff"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_rndfamt).'</th>'; }
                                            else if($tcname == "finaltotal"){ $html .= '<th '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.number_format_ind($tot_finlamt).'</th>'; }
                                            else if($tcname == "cr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;">'.number_format_ind($bcr_amt).'</th>'; }
                                            else if($tcname == "dr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;">'.number_format_ind($bdr_amt).'</th>'; }
                                            else if($tcname == "rb_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;"></th>'; }
                                            else if($tcname == "odue_days"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;"></th>'; }
                                            else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;"></td>'; }
                                            else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;"></td>'; }
                                            else{
                                                $html .= '<th></th>';
                                            }
                                        }
                                    }
                                }
                            }
                            $html .= '</tr>';

                            $gcr_amt = ((float)$ocr_amt + (float)$bcr_amt);
                            $gdr_amt = ((float)$odr_amt + (float)$bdr_amt);

                            if($dbname != "poulso6_chicken_tn_nataraj_broilers"){
                                $html .= '<tr>';
                                $html .= '<th colspan="'.$ifix_cnt.'">Grand Total</th>';
                                for($i = $ini_val1 + 1;$i <= $ccount;$i++){
                                    $key1 = "A:1:".$i;
                                    if(empty($acname[$key1]) && $acname[$key1] == ""){ }
                                    else{
                                        $cname = $tcname = ""; $cname = $acname[$key1];
                                        if($cname != ""){
                                            if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ }
                                            else{
                                                $tcname = $tbl_col_name[$cname];
                                                if($tcname == "cr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;">'.number_format_ind($gcr_amt).'</th>'; }
                                                else if($tcname == "dr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;">'.number_format_ind($gdr_amt).'</th>'; }
                                                else if($tcname == "rb_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;"></th>'; }
                                                else{
                                                    $html .= '<th></th>';
                                                }
                                            }
                                        }
                                    }
                                }
                                $html .= '</tr>';
                            }
                            $ccr_amt = $cdr_amt = 0;
                            if((float)$gcr_amt > (float)$gdr_amt){ $ccr_amt = ((float)$gcr_amt - (float)$gdr_amt); }
                            else{ $cdr_amt = ((float)$gdr_amt - (float)$gcr_amt); }

                            $html .= '<tr>';
                            $html .= '<th colspan="'.$ifix_cnt.'">Closing Balance</th>';
                            for($i = $ini_val1 + 1;$i <= $ccount;$i++){
                                $key1 = "A:1:".$i;
                                if(empty($acname[$key1]) && $acname[$key1] == ""){ }
                                else{
                                    $cname = $tcname = ""; $cname = $acname[$key1];
                                    if($cname != ""){
                                        if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ }
                                        else{
                                            $tcname = $tbl_col_name[$cname];
                                            if($tcname == "cr_amt"){
                                                if(number_format_ind($ccr_amt) == "0.00"){
                                                    $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;"></th>';
                                                }
                                                else{
                                                    $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;">'.number_format_ind($ccr_amt).'</th>';
                                                }
                                            }
                                            else if($tcname == "dr_amt"){
                                                if(number_format_ind($cdr_amt) == "0.00"){
                                                    $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;"></th>';
                                                }
                                                else{
                                                    $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;">'.number_format_ind($cdr_amt).'</th>';
                                                }
                                            }
                                            else if($tcname == "rb_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;"></th>'; }
                                            else{
                                                $html .= '<th></th>';
                                            }
                                        }
                                    }
                                }
                            }
                            $html .= '</tr>';

                            $html .= '</thead>';

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                var vendors = document.getElementById("vendors").value;
                var l = true;
                if(vendors == "select"){
                    alert("Kindly select customer to fetch Ledger");
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }


        //     function openNewTab() {
              
        //         var fdate = '<?php echo $fdate; ?>';
        //         var tdate = '<?php echo $tdate; ?>';
        //         var vendors = '<?php echo $vendors; ?>';
        //         var fstyles = "default";
        //         var fsizes = "default";
        //         var inc_sac = '<?php echo $inc_sac; ?>';
        //         var export1 = "print";
 
        //         var modify_col = new XMLHttpRequest();
        //         var method = "GET";
        //         var url = "chicken_customerledger_print.php?fdate=" + fdate + "&tdate=" + tdate + "&vendors=" + vendors + "&fstyles=" + fstyles + "&fsizes=" + fsizes + "&inc_sac=" + inc_sac + "&export=" + export1;
        //         console.log(url);
        //         window.open(url);
        //  }

         function openNewTab(event) {
    // Prevent the form from submitting and causing a page reload
    event.preventDefault();

    var fdate = '<?php echo $fdate; ?>';
    var tdate = '<?php echo $tdate; ?>';
    var vendors = '<?php echo $vendors; ?>';
    var fstyles = "default";
    var fsizes = "default";
    var inc_sac = '<?php echo $inc_sac; ?>';
    var export1 = "print";

    // Build the URL with query parameters
    var url = "chicken_customerledger_print.php?fdate=" + encodeURIComponent(fdate) +
              "&tdate=" + encodeURIComponent(tdate) +
              "&vendors=" + encodeURIComponent(vendors) +
              "&fstyles=" + encodeURIComponent(fstyles) +
              "&fsizes=" + encodeURIComponent(fsizes) +
              "&inc_sac=" + encodeURIComponent(inc_sac) +
              "&export=" + encodeURIComponent(export1);

    // Open the new tab with the constructed URL
    window.open(url, '_blank'); // This should open the link in a new tab
}

            function update_masterreport_status(a) {
                var file_url = '<?php echo $href; ?>';
                var user_code = '<?php echo $usr_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url=" + file_url + "&user_code=" + user_code + "&field_name=" + field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
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
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
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
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();

                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                /*else if(chosen === 'img_download'){
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php //echo $hhtml; ?>';
                    html += '<?php //echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    const table = document.getElementById("main_table");
                    html2canvas(table).then(canvas => {
                        var tmp_fname = filename.replace(" ", "_");
                        const imgData = canvas.toDataURL("image/png");
                        const link = document.createElement("a");
                        link.href = imgData;
                        link.download = tmp_fname+".png";
                        link.click();
                    });
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php //echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();

                }*/
                else{ }
            }
            function excelfiledownload(table, name, filename){
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
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();

                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
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
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
