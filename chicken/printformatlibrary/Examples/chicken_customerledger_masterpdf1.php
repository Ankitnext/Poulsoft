<?php
//chicken_customerledger_masterpdf1.php
include "../../newConfig.php";
include "../../number_format_ind.php";

$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$dbname = $_SESSION['dbase'];

/*Check Flags*/
$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sup_mnuname_flag = $row['description']; } if($sup_mnuname_flag == ""){ $sup_mnuname_flag = 0; }

/*Master Report Format*/
//$field_calign_flag: All Fields except date and transaction type2 are align to center flag
$acname = $icname = array(); $ac_cnt = $cus_cdays_flag = $cus_outbal_flag = $field_calign_flag = $logo_ascom_flag = 0; $slogo_path = "";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `master_cbr_main_details` WHERE `project` LIKE 'CTS' AND `file_url` LIKE '$href' AND `user_code` LIKE '$users_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $count1 = mysqli_num_rows($query);
if($count1 == 0){
    $sql = "SELECT * FROM `master_cbr_main_details` WHERE `project` LIKE 'CTS' AND `file_url` LIKE '$href' AND `user_code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $count2 = mysqli_num_rows($query);
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
                $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;border:1px solid black;"';
            }
            else{
                if($row['tbl_col_name'] == "cr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:green;border:1px solid black;"'; }
                else if($row['tbl_col_name'] == "dr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:blue;border:1px solid black;"'; }
                else if($row['tbl_col_name'] == "cr_amt" || $row['tbl_col_name'] == "odue_days"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:red;border:1px solid black;"'; }
                else{ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;border:1px solid black;"'; }
            }
        }
        else if($row['col_type'] == "order_date" || $row['col_type'] == "order"){
            $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;border:1px solid black;"';
        }
        else if($row['col_type'] == "order_num"){
            if($row['tbl_col_name'] == "cr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:green;border:1px solid black;"'; }
            else if($row['tbl_col_name'] == "dr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:blue;border:1px solid black;"'; }
            else if($row['tbl_col_name'] == "cr_amt" || $row['tbl_col_name'] == "odue_days"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:red;border:1px solid black;"'; }
            else{ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;border:1px solid black;"'; }
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

$paper_mode = "L";
$paper_size = "A4";
$d_cnt = $ccount - 6;

$fdate = date("Y-m-d",strtotime($_POST['fdate'])); $tdate = date("Y-m-d",strtotime($_POST['tdate'])); $send_type = $_POST['send_type'];
$s_sdate = date("Y-m-d",strtotime("-7 days".$fdate));$s_edate = date("Y-m-d",strtotime("-1 days".$fdate));
if($_POST['inc_sac'] == true || $_POST['inc_sac'] == "on" || $_POST['inc_sac'] == "1"){ $inc_sac = 1; } else{ $inc_sac = 0; }
$bcount = $pcount = 0; $today = date("Y-m-d");
foreach($_POST['ccode'] as $ccode){ $bcount++; }

//Heading and col width calculations
$nhtml .= '<tr class="tfoot1">';
$ifix_cnt = $ini_val1 = $ino_sval = $img_cwdt = $com_cwdt = $cus_cwdt = $c_cnt = $tpx_cnt = 0;
for($i = 1;$i <= $ccount;$i++){
    $key1 = "A:1:".$i; $key2 = "A:0:".$i;
    if(empty($acname[$key1]) && $acname[$key1] == "" && empty($icname[$key2]) && $icname[$key2] == ""){ }
    else{
        $cname = $checked = ""; if(!empty($acname[$key1])){ $cname = $acname[$key1]; $checked = "checked"; } else if(!empty($icname[$key2])){ $cname = $icname[$key2]; } else{ }
        if(empty($acname[$key1]) && $acname[$key1] == ""){ }
        else{
            $tcname = $tbl_col_name[$cname];
            if($tcname == "date"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "invoice"){ $nhtml .= '<th style="width:110px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 110; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 110; } else if($c_cnt > 4){ $cus_cwdt += 110; } else{ } $tpx_cnt += 110; }
            else if($tcname == "so_trnum"){ $nhtml .= '<th style="width:110px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 110; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 110; } else if($c_cnt > 4){ $cus_cwdt += 110; } else{ } $tpx_cnt += 110; }
            else if($tcname == "link_trnum"){ $nhtml .= '<th style="width:110px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 110; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 110; } else if($c_cnt > 4){ $cus_cwdt += 110; } else{ } $tpx_cnt += 110; }
            else if($tcname == "bookinvoice"){ $nhtml .= '<th style="width:60px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 60; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 60; } else if($c_cnt > 4){ $cus_cwdt += 60; } else{ } $tpx_cnt += 60; }
            else if($tcname == "customercode"){ $nhtml .= '<th style="width:110px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 110; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 110; } else if($c_cnt > 4){ $cus_cwdt += 110; } else{ } $tpx_cnt += 110; }
            else if($tcname == "sup_code"){ $nhtml .= '<th style="width:105px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 105; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 105; } else if($c_cnt > 4){ $cus_cwdt += 105; } else{ } $tpx_cnt += 105; }
            else if($tcname == "itemcode"){ $nhtml .= '<th style="width:110px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 110; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 110; } else if($c_cnt > 4){ $cus_cwdt += 110; } else{ } $tpx_cnt += 110; }
            else if($tcname == "item_sname"){ $nhtml .= '<th style="width:50px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 50; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 50; } else if($c_cnt > 4){ $cus_cwdt += 50; } else{ } $tpx_cnt += 50; }
            else if($tcname == "jals"){ $nhtml .= '<th style="width:60px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 60; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 60; } else if($c_cnt > 4){ $cus_cwdt += 60; } else{ } $tpx_cnt += 60; }
            else if($tcname == "birds"){ $nhtml .= '<th style="width:60px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 60; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 60; } else if($c_cnt > 4){ $cus_cwdt += 60; } else{ } $tpx_cnt += 60; }
            else if($tcname == "totalweight"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "emptyweight"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "sent_weight"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "mort_weight"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "order_qty"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "delivery_qty"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "farm_weight"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "netweight"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "actual_price"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "addOnPrice"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "itemprice"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "totalamt"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "tcdsper"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "tcds_type1"){ $nhtml .= '<th style="width:50px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 50; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 50; } else if($c_cnt > 4){ $cus_cwdt += 50; } else{ } $tpx_cnt += 50; }
            else if($tcname == "tcds_type2"){ $nhtml .= '<th style="width:50px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 50; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 50; } else if($c_cnt > 4){ $cus_cwdt += 50; } else{ } $tpx_cnt += 50; }
            else if($tcname == "tcdsamt"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "delivery_charge"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "dressing_charge"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "transporter_code"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "freight_amount"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "freight_amt"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "freight_price_perjal"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "freight_amount_jal"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "roundoff_type1"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "roundoff_type2"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "roundoff"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "finaltotal"){ $nhtml .= '<th style="width:80px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 80; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 80; } else if($c_cnt > 4){ $cus_cwdt += 80; } else{ } $tpx_cnt += 80; }
            else if($tcname == "warehouse"){ $nhtml .= '<th style="width:80px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 80; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 80; } else if($c_cnt > 4){ $cus_cwdt += 80; } else{ } $tpx_cnt += 80; }
            else if($tcname == "remarks"){ $nhtml .= '<th style="width:100px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 100; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 100; } else if($c_cnt > 4){ $cus_cwdt += 100; } else{ } $tpx_cnt += 100; }
            else if($tcname == "drivercode"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "vehiclecode"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "addedemp"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "addedtime"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "approvedemp"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "approvedtime"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "updatedemp"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "updated"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "trlink"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "cr_amt"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "dr_amt"){ $nhtml .= '<th style="width:90px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 90; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 90; } else if($c_cnt > 4){ $cus_cwdt += 90; } else{ } $tpx_cnt += 90; }
            else if($tcname == "rb_amt"){ $nhtml .= '<th style="width:110px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 110; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 110; } else if($c_cnt > 4){ $cus_cwdt += 110; } else{ } $tpx_cnt += 110; }
            else if($tcname == "odue_days"){ $nhtml .= '<th style="width:70px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 70; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 70; } else if($c_cnt > 4){ $cus_cwdt += 70; } else{ } $tpx_cnt += 70; }
            else if($tcname == "trns_type"){ $nhtml .= '<th style="width:130px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 130; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 130; } else if($c_cnt > 4){ $cus_cwdt += 130; } else{ } $tpx_cnt += 130; }
            else if($tcname == "trns_type2"){ $nhtml .= '<th style="width:130px;border:1px solid black;text-align:center;">'.$rpt_col_name[$cname].'</th>'; $c_cnt++; if($c_cnt <= 2){ $img_cwdt += 130; } else if($c_cnt > 2 && $c_cnt <= 4){ $com_cwdt += 130; } else if($c_cnt > 4){ $cus_cwdt += 130; } else{ } $tpx_cnt += 130; }

            else{ }

            //check initial values for total Columns
            if($rpt_col_type[$cname] != "order_num" && $ino_sval == 0){ $ifix_cnt++; $ini_val1 = $i; } else{ $ino_sval++; }
        }
    }
}
$nhtml .= '</tr>';
$sh_cnt = $tpx_cnt / 2;

if($send_type == "download_pdf"){
    require_once('tcpdf_include.php');
    //generate ZIP Folder
    $zip = new ZipArchive();
    $zip_filename = 'CustomerLedgers.zip';
    $zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $fname_alist = array();
}
foreach($_POST['ccode'] as $ccode){
    $pcount++;
    $vendors = $ccode;
    //Sales
    $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$today' AND `customercode` = '$vendors' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`id` ASC";
    $query = mysqli_query($conn,$sql); $i = $opn_csale = $btw_csale = $pweek_samt = 0; $old_inv1 = $old_inv2 = $old_inv3 = $old_date = ""; $cus_sale = $sale_dcount = array();
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

        //Previouse Week Sale Calculations
        if(strtotime($row['date']) >= strtotime($s_sdate) && strtotime($row['date']) <= strtotime($s_edate)){
            $pweek_samt += (float)$row['totalamt'];
        }        
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


    if($send_type == "send_pdf" || $send_type == "download_pdf"){ $html = ''; }
    if($send_type != "view_pdf_print"){
    $html .= '<html><head><title>PoulSoft Solutions</title><style> table,tr,th,td{ font-size:10px;text-align:left;border:1px solid black;border-collapse:collapse; } td,th{ padding: 3px;text-align:left; } td{ text-align:left; } .thead1, .tfoot1{ background-color: #98fb98; } </style></head>';
    $html .= '<body align="center">';
    }
    $html .= '<table style="white-space:nowrap;" align="center">';
    $html .= '<thead>';
    if($dbname == "poulso6_chicken_tn_nataraj_broilers"){
        $html .= '<tr class="tfoot1" style="line-height:1.7;">';
        $html .= '<td style="width:'.$sh_cnt.'px;border-top:1px solid black;border-left:1px solid black; border-right:1px solid black;text-align:center;">NATARAJ BROILER</td>';
        $html .= '<td style="width:'.$sh_cnt.'px;border-top:1px solid black;border-left:1px solid black; border-right:1px solid black;text-align:center;">'.$cus_name[$vendors].'</td>';
        $html .= '</tr>';
        $html .= '<tr class="tfoot1" style="line-height:1.7;">';
        $html .= '<td style="width:'.$sh_cnt.'px; border-left:1px solid black; border-right:1px solid black; text-align:center;">SEENAPURAM, PERUNDURAI</td>';
        $html .= '<td style="width:'.$sh_cnt.'px; border-left:1px solid black; border-right:1px solid black;text-align:center;">PH: '.$cus_mobile[$vendors].'</td>';
        $html .= '</tr>';
        $html .= '<tr class="tfoot1" style="line-height:1.7;">';
        $html .= '<td style="width:'.$sh_cnt.'px;border-bottom:1px solid black;border-left:1px solid black; border-right:1px solid black;text-align:center;">GPAY - 9566501234, 9952333880</td>';
        $html .= '<td style="width:'.$sh_cnt.'px;border-bottom:1px solid black;border-left:1px solid black; border-right:1px solid black;text-align:center;">Statement '.date("d.m.Y",strtotime($fdate)).' - '.date("d.m.Y",strtotime($tdate)).'</td>';
        $html .= '</tr>';
        $html .= '<tr class="tfoot1" style="line-height:1.7;">';
        $html .= '<td style="width:'.$sh_cnt.'px;border:1px solid black;text-align:center;color:red;">PREVIUOS WEEK BALANCE: '.number_format_ind($pweek_samt).'</td>';
        $html .= '<td style="width:'.$sh_cnt.'px;border:1px solid black;text-align:center;">OPENING: '.number_format_ind($rb_amt).'</td>';
        $html .= '</tr>';
    }
    else{
        $html .= '<tr class="tfoot1">';
        $html .= '<td style="width:'.$img_cwdt.'px;border:1px solid black;"><br/><br/><img src="../../'.$logopath.'" height="110px"/></td>';
        $html .= '<td style="width:'.$com_cwdt.'px;border:1px solid black;text-align:left;"><br/><br/>'.$cdetails.'</td>';
        $html .= '<td style="width:'.$cus_cwdt.'px;border:1px solid black;text-align:left;" align="center">';
        $html .= '<h3>'.$file_name.'</h3>';
        $html .= '<label><b style="color: green;">From Date:</b>&nbsp;'.date("d.m.Y",strtotime($fdate)).'</label>&ensp;&ensp;';
        $html .= '<label><b style="color: green;">To Date:</b>&nbsp;'.date("d.m.Y",strtotime($tdate)).'</label>';
        if($vendors != "select"){ $html .= '<h3>'.$cus_name[$vendors].' ('.$cus_mobile[$vendors].')</h3>'; }
        if((int)$cus_outbal_flag == 1){ $html .= '<h3 style="color:red;">BALANCE: '.number_format_ind($otb_amt).'</h3>'; }
        if((int)$cus_cdays_flag == 1){ $html .= '<h5 style="color:green;">Credit Days: '.str_replace(".00","",number_format_ind($credit_days[$vendors])).'</h5>'; }
        $html .= '</td>';
        $html .= '</tr>';
    }
    $html .= '</thead>';
    $html .= '<tbody>';


    $html .= $nhtml;
    if($dbname != "poulso6_chicken_tn_nataraj_broilers"){
        $html .= '<tr style="line-height:1.7;">';
        $html .= '<th colspan="'.$ifix_cnt.'" style="border:1px solid black;">Opening Balance</th>';
        for($i = $ini_val1 + 1;$i <= $ccount;$i++){
            $key1 = "A:1:".$i;
            if(empty($acname[$key1]) && $acname[$key1] == ""){ }
            else{
                $cname = $tcname = ""; $cname = $acname[$key1];
                if($cname != ""){
                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ }
                    else{
                        $tcname = $tbl_col_name[$cname];
                        if($tcname == "cr_amt"){ $html .= '<td '.$rpt_txt_align[$cname].' style="border:1px solid black;text-align:right;color:green;" title="'.$rpt_col_name[$cname].'">'.number_format_ind($cr_amt).'</td>'; }
                        else if($tcname == "dr_amt"){ if((float)$dr_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' style="border:1px solid black;text-align:right;color:blue;" title="'.$rpt_col_name[$cname].'">'.number_format_ind($dr_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                        else if($tcname == "rb_amt"){ if((float)$rb_amt != 0){ $html .= '<td '.$rpt_txt_align[$cname].' style="border:1px solid black;text-align:right;color:red;" title="'.$rpt_col_name[$cname].'">'.number_format_ind($rb_amt).'</td>'; } else{ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'"></td>'; } }
                        else{
                            $html .= '<th style="border:1px solid black;"></th>';
                        }
                    }
                }
            }
        }
        $html .= '</tr>';
    }

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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Sales</td>'; }
                                else if($tcname == "trns_type2"){
                                    if($tr_info[47] == "chicken_display_multiplesale6.php"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">SPS / '.$item_sname[$tr_info[7]].'</td>'; }
                                    else{ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Sales / '.$item_sname[$tr_info[7]].'</td>'; }           
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Purchases</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Purchases / '.$item_sname[$tr_info[7]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:center;color:black;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Receipt</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Receipt / '.$acc_mode[$tr_info[7]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:center;color:black;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Payment</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Payment / '.$acc_mode[$tr_info[4]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Debit Note</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">DN / '.$reason_name[$tr_info[15]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Debit Note</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">DN / '.$reason_name[$tr_info[15]].'</td>'; }
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

                    $cr_amt = $dr_amt = 0;
                    $cr_amt = (float)$tr_info[6]; $rb_amt -= (float)$cr_amt;
                    $bcr_amt += (float)$cr_amt;

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Credit Note</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">CN / '.$reason_name[$tr_info[15]].'</td>'; }
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
                    $tr_info = array(); $tr_info = explode("@",$sup_scns[$mkey]);

                    $cr_amt = $dr_amt = 0;
                    $dr_amt = (float)$tr_info[6]; $rb_amt += (float)$dr_amt;
                    $bdr_amt += (float)$dr_amt;

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Credit Note</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">CN / '.$reason_name[$tr_info[15]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Sales Return</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Sales Return / '.$item_sname[$tr_info[4]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Purchase Return</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Purchase Return / '.$item_sname[$tr_info[4]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Mortality</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Mortality / '.$item_sname[$tr_info[4]].'</td>'; }
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

                    $html .= '<tr style="line-height:1.7;">';
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
                                else if($tcname == "odue_days"){ $html .= '<td style="text-align:right;color:red;border:1px solid black;"></td>'; }
                                else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Mortality</td>'; }
                                else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;">Mortality / '.$item_sname[$tr_info[4]].'</td>'; }
                                else{ }
                            }
                        }
                    }
                    $html .= '</tr>';
                }
            }
        }
    }
    
    $html .= '<tr class="tfoot1" style="line-height:1.7;">';
    if($dbname == "poulso6_chicken_tn_nataraj_broilers"){
        $html .= '<th colspan="'.$ifix_cnt.'" style="border:1px solid black;">Total</th>';
    }
    else{
        $html .= '<th colspan="'.$ifix_cnt.'" style="border:1px solid black;">Between Day closing</th>';
    }
    
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
                    else if($tcname == "cr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:center;color:green;border:1px solid black;">'.number_format_ind($bcr_amt).'</th>'; }
                    else if($tcname == "dr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:center;color:blue;border:1px solid black;">'.number_format_ind($bdr_amt).'</th>'; }
                    else if($tcname == "rb_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:center;color:red;border:1px solid black;"></th>'; }
                    else if($tcname == "odue_days"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:center;color:red;border:1px solid black;"></th>'; }
                    else if($tcname == "trns_type"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:center;border:1px solid black;"></td>'; }
                    else if($tcname == "trns_type2"){ $html .= '<td title="'.$rpt_col_name[$cname].'" style="text-align:left;border:1px solid black;"></td>'; }
                    else{
                        $html .= '<th style="text-align:right;color:red;border:1px solid black;"></th>';
                    }
                }
            }
        }
    }
    $html .= '</tr>';

    $gcr_amt = ((float)$ocr_amt + (float)$bcr_amt);
    $gdr_amt = ((float)$odr_amt + (float)$bdr_amt);

    if($dbname != "poulso6_chicken_tn_nataraj_broilers"){
        $html .= '<tr class="tfoot1" style="line-height:1.7;">';
        $html .= '<th colspan="'.$ifix_cnt.'" style="border:1px solid black;">Grand Total</th>';
        for($i = $ini_val1 + 1;$i <= $ccount;$i++){
            $key1 = "A:1:".$i;
            if(empty($acname[$key1]) && $acname[$key1] == ""){ }
            else{
                $cname = $tcname = ""; $cname = $acname[$key1];
                if($cname != ""){
                    if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ }
                    else{
                        $tcname = $tbl_col_name[$cname];
                        if($tcname == "cr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;border:1px solid black;">'.number_format_ind($gcr_amt).'</th>'; }
                        else if($tcname == "dr_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;border:1px solid black;">'.number_format_ind($gdr_amt).'</th>'; }
                        else if($tcname == "rb_amt"){ $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;border:1px solid black;"></th>'; }
                        else{
                            $html .= '<th style="text-align:right;color:red;border:1px solid black;"></th>';
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

    $html .= '<tr class="tfoot1" style="line-height:1.7;">';
    $html .= '<th colspan="'.$ifix_cnt.'" style="border:1px solid black;">Closing Balance</th>';
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
                            $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;border:1px solid black;"></th>';
                        }
                        else{
                            $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:green;border:1px solid black;">'.number_format_ind($ccr_amt).'</th>';
                        }
                    }
                    else if($tcname == "dr_amt"){
                        if(number_format_ind($cdr_amt) == "0.00"){
                            $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;border:1px solid black;"></th>';
                        }
                        else{
                            if($dbname == "poulso6_chicken_tn_nataraj_broilers"){
                                $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;border:1px solid black;"></th>';
                            }
                            else{
                                $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:blue;border:1px solid black;">'.number_format_ind($cdr_amt).'</th>';
                            }
                        }
                    }
                    else if($tcname == "rb_amt"){
                        if($dbname == "poulso6_chicken_tn_nataraj_broilers"){
                            $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:center;color:blue;border:1px solid black;">'.number_format_ind($cdr_amt).'</th>';
                        }
                        else{
                            $html .= '<th title="'.$rpt_col_name[$cname].'" style="text-align:right;color:red;border:1px solid black;"></th>';
                        }
                    }
                    else{
                        $html .= '<th style="text-align:right;color:red;border:1px solid black;"></th>';
                    }
                }
            }
        }
    }
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    if($send_type != "view_pdf_print"){
    $html .= '</body>';
    $html .= '</html>';
    }
    if($send_type == "view_normal_print"){ $html .= '<p style="page-break-after:always"></p>'; }
    else if($send_type == "view_pdf_print" && $bcount != $pcount){ $html .= '<div style="page-break-before:always"></div>'; }
    else if($send_type == "send_pdf"){
        //echo $html;
        $file_name1 = $company_name."_".$cus_name[$vendors];
        $file_name = str_replace(" ","_",$file_name1);
        require_once('tcpdf_include.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mallikarjuna K');
        $pdf->SetTitle('Customer Ledger');
        $pdf->SetSubject('Ledger PDF');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetFont('dejavusans', '', 11, '', true);
        $dt = date("d.m.Y",strtotime($odate));
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(5, 5, 5, true);
        $pdf->AddPage($paper_mode, $paper_size);
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $file = $pdf->Output(__DIR__."/".$file_name.".pdf",'F');
        $filepath = "https://chicken.poulsoft.org/printformatlibrary/Examples/".$file_name.".pdf";
        $filepath2 = __DIR__."/".$file_name.".pdf";
        
        $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'WappKey' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $url_id = $row['url_id']; $instance_id = $row['sms_key']; $access_token = $row['msg_key']; $mobilenos = explode(",",$row['numers']); }
        $m1 = ""; $m1 = explode(",",$cus_mobile[$vendors]); $mobno_list = array();

        foreach($m1 as $m2){ if(strlen($m2) == 10 || strlen($m2) == "10"){ $mobno_list[$m2] = $m2; } }
        foreach($mobilenos as $m2){ if(strlen($m2) == 10 || strlen($m2) == "10"){ $mobno_list[$m2] = $m2; } }

        foreach($mobno_list as $mobile){
            if(strlen($mobile) == 10 || strlen($mobile) == "10"){
                $message = "Dear ".$cus_name[$vendors].",%0D%0A%0D%0A".$company_name." shared your Ledger from ".date("d.m.Y",strtotime($fdate))." to ".date("d.m.Y",strtotime($tdate));
                $message = str_replace(" ","+",$message);
        
                $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $curlopt_url = $row['curlopt_url'];
                    $curlopt_returntransfer = $row['curlopt_returntransfer'];
                    $curlopt_encoding = $row['curlopt_encoding'];
                    $curlopt_maxredirs = $row['curlopt_maxredirs'];
                    $curlopt_timeout = $row['curlopt_timeout'];
                    $curlopt_followlocation = $row['curlopt_followlocation'];
                    $curlopt_http_version = $row['curlopt_http_version'];
                    $curlopt_customrequest = $row['curlopt_customrequest'];
                }
                
                $media_url = $filepath; $filename = $file_name.".pdf"; $number = "91".$mobile; $type = "media";
                $url = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&media_url='.$media_url.'&filename='.$filename.'&instance_id='.$instance_id.'&access_token='.$access_token;
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&media_url='.$media_url.'&filename='.$filename.'&instance_id='.$instance_id.'&access_token='.$access_token,
                CURLOPT_RETURNTRANSFER => $curlopt_returntransfer,
                CURLOPT_ENCODING => $curlopt_encoding,
                CURLOPT_MAXREDIRS => $curlopt_maxredirs,
                CURLOPT_TIMEOUT => $curlopt_timeout,
                CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
                CURLOPT_HTTP_VERSION => $curlopt_http_version,
                CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
                ));
        
                $response = curl_exec($curl);
                curl_close($curl);
                $d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);
        
                if($response != ""){
                    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$tdate' AND `tdate` >= '$tdate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                    
                    $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$tdate' AND `tdate` >= '$tdate' AND `type` = 'transactions'";
                    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                    
                    if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                    $wapp_code = "WAPP-".$incr_wapp;
                    
                    $wsfile_path = $_SERVER['REQUEST_URI'];
                    $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime)
                    VALUES ('$wapp_code','$ccode','$number','$url','$d3[1]','$response','Cus_Ledger-pdf','$wsfile_path','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$sql)) { } else{  }
                }
            }
        }
        unlink($filepath2);
    }
    else if($send_type == "download_pdf"){
        $file_name1 = $cus_name[$vendors]."_".$pcount;
        $file_name = str_replace(" ","_",$file_name1).".pdf";
        $filepath2 = __DIR__."/".$file_name;
        $fname_alist[$filepath2] = $filepath2;

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mallikarjuna K');
        $pdf->SetTitle('Customer Ledger');
        $pdf->SetSubject('Ledger PDF');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetFont('dejavusans', '', 11, '', true);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(5, 5, 5, true);
        $pdf->AddPage($paper_mode, $paper_size);
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        //$pdf->Output('CustomerLedger.pdf', 'D');
        $pdf->Output(__DIR__."/".$file_name,'F');
        //$pdf->Output($file_name, 'F'); // Save to file
        $zip->addFile($file_name);
    }
    else{ }
}
if($send_type == "download_pdf"){
    $zip->close();
    
    // Force download of ZIP
    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename=\"$zip_filename\"");
    header('Content-Length: ' . filesize($zip_filename));
    readfile($zip_filename);
    
    // Cleanup (optional)
    foreach ($fname_alist as $files) {
        unlink($files);
    }
    unlink($zip_filename);
}
else if($send_type == "view_pdf_print"){
    require_once('tcpdf_include.php');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Mallikarjuna K');
    $pdf->SetTitle('Customer Ledger');
    $pdf->SetSubject('Ledger PDF');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    $pdf->SetFont('dejavusans', '', 11, '', true);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->SetMargins(5, 5, 5, true);
    $pdf->AddPage($paper_mode, $paper_size);
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('CustomerLedger.pdf', 'I');
}
else if($send_type == "view_normal_print"){ echo $html; }
else if($send_type == "send_pdf"){
    header('location: ../../generalreports/chicken_customerledger_masterpdf1.php');
}
?>