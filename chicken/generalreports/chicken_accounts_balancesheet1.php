<?php
//chicken_accounts_balancesheet1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_accounts_balancesheet1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_accounts_balancesheet1.php?db=".$db;
}
$file_name = "Balance Sheet";
include "number_format_ind.php";
include "decimal_adjustments.php";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Check for Table Availability*/
 $table_head = "Tables_in_".$dbname; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("font_style_master", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $dbname.font_style_master LIKE poulso6_admin_chickenmaster.font_style_master;"; mysqli_query($conn,$sql1); }
if(in_array("customer_sales", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $dbname.customer_sales LIKE poulso6_admin_chickenmaster.customer_sales;"; mysqli_query($conn,$sql1); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Customer Ledger Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Vendor Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = $ven_mobile = $ven_group = $ven_obtype = $ven_obamt = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; $ven_mobile[$row['code']] = $row['mobileno']; $ven_group[$row['code']] = $row['groupcode']; $ven_obtype[$row['code']] = $row['obtype']; $ven_obamt[$row['code']] = $row['obamt']; }

//Vendor Group
$sql = "SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $ven_gcode = $ven_gname = $ven_gcoa = array();
while($row = mysqli_fetch_assoc($query)){ $ven_gcode[$row['code']] = $row['code']; $ven_gname[$row['code']] = $row['description']; $ven_gcoa[$row['code']] = $row['controlaccount']; }

//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Item Details
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_category = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$tdate = date("Y-m-d"); $sectors = "all"; $fstyles = $fsizes = "default";
$exports = "display";
if(isset($_POST['submit']) == true){
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sectors = $_POST['sectors'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];

    //Account Types
    $sql = "SELECT * FROM `acc_types` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn, $sql); $atype_code = $atype_name = $ltype_code = $ltype_name = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtolower($row['description']) == "liability"){ $ltype_code[$row['code']] = $row['code']; $ltype_name[$row['code']] = $row['description']; }
        else if(strtolower($row['description']) == "asset"){ $atype_code[$row['code']] = $row['code']; $atype_name[$row['code']] = $row['description']; }
        else{ }
    }
    $atype_list = "";
    foreach($atype_code as $acode){ if($atype_list == ""){ $atype_list = $acode; } else{ $atype_list .= "','".$acode; } }
    foreach($ltype_code as $acode){ if($atype_list == ""){ $atype_list = $acode; } else{ $atype_list .= "','".$acode; } }
    //Account Categories
    $sql = "SELECT * FROM `acc_category` WHERE `subtype` IN ('$atype_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn, $sql); $acat_code = $acat_name = $acat_type = array();
    while($row = mysqli_fetch_assoc($query)){ $acat_code[$row['code']] = $row['code']; $acat_name[$row['code']] = $row['description']; $acat_type[$row['code']] = $row['subtype']; }
    //Account Schedules
    $sql = "SELECT * FROM `acc_schedules` WHERE `subtype` IN ('$atype_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn, $sql); $aschd_code = $aschd_name = $aschd_type = $aschd_ptype = array();
    while($row = mysqli_fetch_assoc($query)){ $aschd_code[$row['code']] = $row['code']; $aschd_name[$row['code']] = $row['description']; $aschd_type[$row['code']] = $row['subtype']; $aschd_ptype[$row['code']] = $row['pstype']; }
    //Account Modes
    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $amode_code = $amode_name = array();
    while($row = mysqli_fetch_assoc($query)){ $amode_code[$row['code']] = $row['code'];  $amode_name[$row['code']] = $row['description']; }
    //Account CoA
    $sql = "SELECT * FROM `acc_coa` WHERE `type` IN ('$atype_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $coa_code = $coa_name = $coa_ctype = $coa_schd = $coa_cats = array();
    while($row = mysqli_fetch_assoc($query)){
        $coa_code[$row['code']] = $row['code'];
        $coa_name[$row['code']] = $row['description'];
        $coa_ctype[$row['code']] = $row['ctype'];
        $coa_schd[$row['code']] = $row['schedules'];
        $coa_cats[$row['code']] = $row['categories'];
        if(strtolower($row['description']) == "tcs"){ $tcs_code = $row['code']; }
        else if(strtolower($row['description']) == "tds"){ $tds_code = $row['code']; }
        else if(str_contains(strtolower($row['description']),"freight")){ $freight_code = $row['code']; }
        else{ }
    }
    
    //Fetch Details
    //Vouchers
    $sql = "SELECT * FROM `acc_vouchers` WHERE `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn,$sql); $coa_cr_amt = $coa_dr_amt = array();
    while($row = mysqli_fetch_assoc($query)){
        /*From Account*/ if(empty($coa_code[$row['fcoa']]) || $coa_code[$row['fcoa']] == ""){ } else{ $coa_cr_amt[$row['fcoa']] += (float)$row['amount']; }
        /*To Account*/ if(empty($coa_code[$row['tcoa']]) || $coa_code[$row['tcoa']] == ""){ } else{ $coa_dr_amt[$row['tcoa']] += (float)$row['amount']; }
    }

    //Receipt
    $sql = "SELECT * FROM `customer_receipts` WHERE `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn,$sql); $dcrs = 0;
    while($row = mysqli_fetch_assoc($query)){
        /*To Account*/
        $coa_dr_amt[$row['method']] += (float)$row['amount'];
        //Vendor Accounts
        $vg_coa = $ven_gcoa[$ven_group[$row['ccode']]];
        $coa_cr_amt[$vg_coa] += (float)$row['amount'];
    }

    //Payment
    $sql = "SELECT * FROM `pur_payments` WHERE `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        /*From Account*/
        $coa_cr_amt[$row['method']] += (float)$row['amount'];
        //Vendor Accounts
        $vg_coa = $ven_gcoa[$ven_group[$row['ccode']]];
        $coa_dr_amt[$vg_coa] += (float)$row['amount'];
    }

    //CrDr Note
    $sql = "SELECT *  FROM `main_crdrnote` WHERE `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        /*From Account*/
        if(!empty($coa_code[$row['coa']]) && $row['crdr'] == "Cr"){
            $coa_cr_amt[$row['coa']] += (float)$row['amount'];
            //Vendor Accounts
            $vg_coa = $ven_gcoa[$ven_group[$row['ccode']]];
            $coa_dr_amt[$vg_coa] += (float)$row['amount'];
        }
        /*To Account*/
        if(!empty($coa_code[$row['coa']]) && $row['crdr'] == "Dr"){
            $coa_dr_amt[$row['coa']] += (float)$row['amount'];
            //Vendor Accounts
            $vg_coa = $ven_gcoa[$ven_group[$row['ccode']]];
            $coa_cr_amt[$vg_coa] += (float)$row['amount'];
        }
    }

    //Item Stock Related Transactions
    //Item Category
    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; $icat_iac[$row['code']] = $row['iac']; $icat_cogs[$row['code']] = $row['cogsac']; $icat_sac[$row['code']] = $row['sac']; $icat_srac[$row['code']] = $row['srac']; }
    //Item Details
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cats = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cats[$row['code']] = $row['category']; }
    
    //Vendor Openings
    $ven_obtype[$row['code']] = $row['obtype']; $ven_obamt[$row['code']] = $row['obamt'];
    foreach($ven_code as $vcode){
        if($ven_obtype[$vcode] == "Cr"){
            //Vendor Accounts
            $vg_coa = $ven_gcoa[$ven_group[$vcode]];
            $coa_cr_amt[$vg_coa] += (float)$ven_obamt[$vcode];
        }
        else if($ven_obtype[$vcode] == "Dr"){
            //Vendor Accounts
            $vg_coa = $ven_gcoa[$ven_group[$vcode]];
            $coa_dr_amt[$vg_coa] += (float)$ven_obamt[$vcode];
        }
        else{ }
    }
    /*Purchase*/
    $sql = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
    $query = mysqli_query($conn,$sql); $old_inv = ""; $stk_pqty = $stk_pamt = $stk_key = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $icat_iac[$item_cats[$row['itemcode']]]; $key1 = $icode."@".$row['itemcode'];
        //Item Amount
        //$coa_dr_amt[$icode] += (float)$row['totalamt'];
        $stk_pqty[$key1] += (float)$row['netweight'];
        $stk_pamt[$key1] += (float)$row['totalamt'];
        $stk_key[$key1] = $key1;

        
        //Item Discount $pur_disc_amt += (float)$row['discountamt'];
        //Item TAX $pur_tax_amt += (float)$row['taxamount'];
        if($old_inv == "" || $old_inv != $row['invoice']){
            $old_inv = $row['invoice'];
            //Item TCDS
            if($row['tcds_type2'] == "deduct"){ $coa_cr_amt[$tds_code] += (float)$row['tcdsamt']; } else{ $coa_dr_amt[$tds_code] += (float)$row['tcdsamt']; }
            //Item Freight
            $coa_cr_amt[$freight_code] += (float)$row['freight_amount'];
            //Vendor Accounts
            $vg_coa = $ven_gcoa[$ven_group[$row['vendorcode']]];
            $coa_cr_amt[$vg_coa] += (float)$row['finaltotal'];
        }
    }

    /*Purchase Return*/
    $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$tdate' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn,$sql); $old_inv = "";
    while($row = mysqli_fetch_assoc($query)){
        $icode = $icat_iac[$item_cats[$row['itemcode']]]; $key1 = $icode."@".$row['itemcode'];
        //Item Amount
        //$coa_cr_amt[$icode] += (float)$row['amount'];
        $stk_pqty[$key1] -= (float)$row['netweight'];
        $stk_pamt[$key1] -= (float)$row['totalamt'];
        $stk_key[$key1] = $key1;

        //Vendor Accounts
        $vg_coa = $ven_gcoa[$ven_group[$row['vcode']]];
        $coa_dr_amt[$vg_coa] += (float)$row['amount'];
    }

    /*Sale*/
    $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
    $query = mysqli_query($conn,$sql); $old_inv = ""; $cdrs = 0; $stk_sqty = $stk_samt = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $icat_iac[$item_cats[$row['itemcode']]]; $key1 = $icode."@".$row['itemcode'];
        //Item Amount
        $cur_amt = $stk_prc = 0;
        if(!empty($stk_pqty[$key1]) && (float)$stk_pqty[$key1] != 0){
            $stk_prc = ((float)$stk_pamt[$key1] / (float)$stk_pqty[$key1]);
            $cur_amt = ((float)$stk_prc * (float)$row['netweight']);
        }
        $stk_sqty[$key1] += (float)$row['netweight'];
        $stk_samt[$key1] += (float)$cur_amt;
        $stk_key[$key1] = $key1;
        //$coa_cr_amt[$icode] += (float)$row['totalamt'];


        //Item Discount $pur_disc_amt += (float)$row['discountamt'];
        //Item TAX $pur_tax_amt += (float)$row['taxamount'];
        if($old_inv == "" || $old_inv != $row['invoice']){
            $old_inv = $row['invoice'];
            //Item TCDS
            if($row['tcds_type2'] == "deduct"){ $coa_dr_amt[$tds_code] += (float)$row['tcdsamt']; } else{ $coa_cr_amt[$tds_code] += (float)$row['tcdsamt']; }
            //Item Freight
            $coa_dr_amt[$freight_code] += (float)$row['freight_amount'];
            //Vendor Accounts
            $vg_coa = $ven_gcoa[$ven_group[$row['customercode']]];
            $coa_dr_amt[$vg_coa] += (float)$row['finaltotal'];
            //if($vg_coa == "ASG-0001"){ echo "<br/>1.@".$cdrs += (float)$row['finaltotal']; }
        }
    }

    /*Sales Return*/
    $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$tdate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn,$sql); $old_inv = "";
    while($row = mysqli_fetch_assoc($query)){
        $icode = $icat_iac[$item_cats[$row['itemcode']]]; $key1 = $icode."@".$row['itemcode'];
        //Item Amount
        //$coa_dr_amt[$icode] += (float)$row['amount'];
        $stk_sqty[$key1] -= (float)$row['netweight'];
        $stk_samt[$key1] -= (float)$cur_amt;
        $stk_key[$key1] = $key1;
        //Vendor Accounts
        $vg_coa = $ven_gcoa[$ven_group[$row['vcode']]];
        $coa_cr_amt[$vg_coa] += (float)$row['amount'];
    }

    /*Item Wise Amount Calculations
    foreach($stk_key as $key1){
        $keys = array(); $keys = explode("@", $key1);
        if(empty($stk_pqty[$key1]) || (float)$stk_pqty[$key1] == 0){ $stk_pqty[$key1] = 0; }
        if(empty($stk_sqty[$key1]) || (float)$stk_sqty[$key1] == 0){ $stk_sqty[$key1] = 0; }
        if(empty($stk_pamt[$key1]) || (float)$stk_pamt[$key1] == 0){ $stk_pamt[$key1] = 0; }
        if(empty($stk_samt[$key1]) || (float)$stk_samt[$key1] == 0){ $stk_samt[$key1] = 0; }

        //echo "<br/>".$keys[0]."@".$item_name[$keys[1]]."@".$stk_pqty[$key1]."@".$stk_pamt[$key1]."@".$stk_sqty[$key1]."@".$stk_samt[$key1];
        $coa_dr_amt[$keys[0]] += (float)$stk_pamt[$key1];
        $coa_cr_amt[$keys[0]] += (float)$stk_samt[$key1];
    }*/

    //Calculations-1
    $coa_amt = $cschd_amt = array();
    foreach($coa_code as $acode){
        if(empty($coa_cr_amt[$acode]) || $coa_cr_amt[$acode] == ""){ $coa_cr_amt[$acode] = 0; }
        if(empty($coa_dr_amt[$acode]) || $coa_dr_amt[$acode] == ""){ $coa_dr_amt[$acode] = 0; }
        $bal_amt = 0; $bal_amt = (float)$coa_dr_amt[$acode] - (float)$coa_cr_amt[$acode];
        /*CoA Account Wise Balance*/ $coa_amt[$acode] += (float)$bal_amt;
        /*CoA Schedule Wise Balance*/ $scode = $coa_schd[$acode]; $cschd_amt[$scode] += (float)$bal_amt;
    }

    //Calculations-2
    $lrow = $arow = $tot_lamt = $tot_aamt = 0; $l_col1 = $l_col2 = $l_col3 =  $a_col1 = $a_col2 = $a_col3 = array();
    foreach($aschd_code as $scode){
        if(empty($cschd_amt[$scode]) || $cschd_amt[$scode] == ""){ $cschd_amt[$scode] = 0; }
        if(empty($aschd_type[$scode]) || $aschd_type[$scode] == ""){ }
        else{
            $atypes = $aschd_type[$scode];
            //Liability
            $sa_cnt = 0;
            if(!empty($ltype_code[$atypes]) && $ltype_code[$atypes] != ""){

                //Each CoA Details
                foreach($coa_code as $acode){
                    if(!empty($coa_schd[$acode]) && $coa_schd[$acode] == $scode){
                        if(empty($coa_amt[$acode]) || $coa_amt[$acode] == ""){ $coa_amt[$acode] = 0; }
                        if((float)$coa_amt[$acode] != 0){
                            if((int)$sa_cnt == 0){
                                $sa_cnt++;
                                $lrow++;
                                $l_col1[$lrow] .= '<th>'.$scode.'</th>';
                                $l_col2[$lrow] .= '<th>'.$aschd_name[$scode].'</th>';
                                $l_col3[$lrow] .= '<th style="text-align:right;">'.number_format_ind(round($cschd_amt[$scode],2)).'</th>';
                            }
                            $lrow++;
                            $l_col1[$lrow] .= '<td>'.$acode.'</td>';
                            $l_col2[$lrow] .= '<td>'.$coa_name[$acode].'</td>';
                            $l_col3[$lrow] .= '<td style="text-align:right;">'.number_format_ind(round($coa_amt[$acode],2)).'</td>';
                            $tot_lamt += (float)$coa_amt[$acode];
                        }
                    }
                }
            }
            //Asset
            $sa_cnt = 0;
            if(!empty($atype_code[$atypes]) && $atype_code[$atypes] != ""){
                //Each CoA Details
                foreach($coa_code as $acode){
                    if(!empty($coa_schd[$acode]) && $coa_schd[$acode] == $scode){
                        if(empty($coa_amt[$acode]) || $coa_amt[$acode] == ""){ $coa_amt[$acode] = 0; }
                        if((float)$coa_amt[$acode] != 0){
                            if((int)$sa_cnt == 0){
                                $sa_cnt++;
                                $arow++;
                                $a_col1[$arow] .= '<th>'.$scode.'</th>';
                                $a_col2[$arow] .= '<th>'.$aschd_name[$scode].'</th>';
                                $a_col3[$arow] .= '<th style="text-align:right;">'.number_format_ind(round($cschd_amt[$scode],2)).'</th>';
                            }
                            $arow++;
                            $a_col1[$arow] .= '<td>'.$acode.'</td>';
                            $a_col2[$arow] .= '<td>'.$coa_name[$acode].'</td>';
                            $a_col3[$arow] .= '<td style="text-align:right;">'.number_format_ind(round($coa_amt[$acode],2)).'</td>';
                            $tot_aamt += (float)$coa_amt[$acode];
                        }
                    }
                }
            }
        }
    }
    if($lrow < $arow){ for($i = $lrow + 1;$i <= $arow;$i++){ $lrow++; $l_col1[$i] .= '<td></td>'; $l_col2[$i] .= '<td></td>'; $l_col3[$i] .= '<td></td>'; } }
    if($arow < $lrow){ for($i = $arow + 1;$i <= $lrow;$i++){ $arow++; $a_col1[$i] .= '<td></td>'; $a_col2[$i] .= '<td></td>'; $a_col3[$i] .= '<td></td>'; } }

    $ldiff_amt = $adiff_amt = 0;
    if($tot_lamt < $tot_aamt){
        $ldiff_amt = (float)$tot_aamt - (float)$tot_lamt;
    }
    else if($tot_lamt > $tot_aamt){
        $adiff_amt = (float)$tot_lamt - (float)$tot_aamt;
    }
    else{ }
    
    if((float)$ldiff_amt > 0){
        $lrow++; $arow++;
        $tot_lamt += (float)$ldiff_amt;
        $l_col1[$i] .= '<td></td>'; $l_col2[$i] .= '<td>Difference In Opening Balances</td>'; $l_col3[$i] .= '<td style="text-align:right;">'.number_format_ind(round($ldiff_amt,2)).'</td>';
        $a_col1[$i] .= '<td></td>'; $a_col2[$i] .= '<td></td>'; $a_col3[$i] .= '<td></td>';
    }
    else if((float)$adiff_amt > 0){
        $lrow++; $arow++;
        $tot_aamt += (float)$adiff_amt;
        $l_col1[$i] .= '<td></td>'; $l_col2[$i] .= '<td></td>'; $l_col3[$i] .= '<td></td>';
        $a_col1[$i] .= '<td></td>'; $a_col2[$i] .= '<td>Difference In Opening Balances</td>'; $a_col3[$i] .= '<td style="text-align:right;">'.number_format_ind(round($adiff_amt,2)).'</td>';
    }
}
?>
<html>
	<head>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            body{
                zoom: 0.7;
            }
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
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <td colspan="2" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                    <label><b style="color: green;">As on Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="6" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">Till Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:290px;">
                                            <label for="sectors">Sector/Warehouse/Vehicle</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:280px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
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
                                        <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
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
                            $html = '';
                            $lpath1 = "../".$logopath;
                            
                            $hhtml .= '<tr>';
                            $hhtml .= '<td colspan="2"><img src="'.$lpath1.'" height="150px"/></td>';
                            $hhtml .= '<td colspan="2">'.$cmpy_fname.'</td>';
                            $hhtml .= '<td colspan="2" align="center">';
                            $hhtml .= '<h3>'.$file_name.'</h3>';
                            $hhtml .= '<label><b style="color: green;">As on Date:</b>&nbsp;'.date("d.m.Y",strtotime($tdate)).'</label>';
                            $hhtml .= '</td>';
                            $hhtml .= '</tr>';
                            

                            $html .= '<thead class="thead2" id="head_names">';
                            $html .= '<tr><th colspan="4">Liabilities</th><th colspan="3">Assets</th></tr>';
                            $html .= '<tr>';
                            $html .= '<th>Sl No.</th>';
                            $html .= '<th>Code</th>';
                            $html .= '<th>Description</th>';
                            $html .= '<th>Amount</th>';
                            $html .= '<th>Code</th>';
                            $html .= '<th>Description</th>';
                            $html .= '<th>Amount</th>';
                            $html .= '</tr>';
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';
                            
                            $sl = 1;
                            for($i = 1;$i <= $lrow;$i++ ){
                                $html .= '<tr>';
                                $html .= '<td>'.$sl++.'</td>';
                                $html .= $l_col1[$i].''.$l_col2[$i].''.$l_col3[$i];
                                $html .= $a_col1[$i].''.$a_col2[$i].''.$a_col3[$i];
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>';

                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="3">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_lamt,2)).'</th>';
                            $html .= '<th colspan="2">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_aamt,2)).'</th>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();
                }
                else{ }
            }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
</html>
