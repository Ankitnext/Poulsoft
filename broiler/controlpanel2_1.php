<?php
//controlpanel2_1.php
include "newConfig.php";
//echo $_SERVER['REMOTE_ADDR'];
include "number_format_ind.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $display_dashboard_flag = $row['display_dashboard_flag']; $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$chick_placement_details = $feed_stock_details = $opening_bird_details = $mortality_bird_details = $lifting_bird_details = $yesterday_lifting_bird_details = $closing_bird_details = $total_mortality_bird_details = 
$agewise_available_birds = $agewise_abirds_details = $bird_bal_details = $week_wise_mort_details = $feed_bal_details = $branch_wise_weekly_mort_details = $branch_wise_weekly_mortper_details = $branch_wise_total_mort_details = $branch_wise_single_date_mort_details = $branch_wise_cur_mort_details = 0;
$sql1 = "SELECT * FROM `master_dashboard_links` WHERE `field_name` LIKE 'Broiler' AND `user_code` = '$user_code' ORDER BY `sort_order` ASC";
$query1 = mysqli_query($conn,$sql1); $dcount1 = mysqli_num_rows($query1); $dboard_flag = 0;
if($dcount1 > 0){
    $dboard_flag = 1;
    while($row1 = mysqli_fetch_assoc($query1)){
        if($row1['panel_name'] == "Chick Placement-List"){ $chick_placement_details = $row1['active']; }
        if($row1['panel_name'] == "feed Stock-List"){ $feed_stock_details = $row1['active']; }
        if($row1['panel_name'] == "Opening Birds-List"){ $opening_bird_details = $row1['active']; }
        if($row1['panel_name'] == "Mortality Birds-Doughnut"){ $mortality_bird_details = $row1['active']; }
        if($row1['panel_name'] == "Lifting Birds-Bar Chart"){ $lifting_bird_details = $row1['active']; }
        if($row1['panel_name'] == "Lifting Birds-Bar Chart-2"){ $lifting_bird_details2 = $row1['active']; }
        if($row1['panel_name'] == "Previous Day Lifting Birds-Bar Chart"){ $yesterday_lifting_bird_details = $row1['active']; }
        if($row1['panel_name'] == "Closing Birds-List"){ $closing_bird_details = $row1['active']; }
        if($row1['panel_name'] == "Total Mortality Per-List"){ $total_mortality_bird_details = $row1['active']; }
        if($row1['panel_name'] == "Customer Supplier Balance-List"){ $Customer_Supplier_Balance_details = $row1['active']; }
        if($row1['panel_name'] == "Age Wise Available Birds-Bar Chart"){ $agewise_available_birds = $row1['active']; }
        if($row1['panel_name'] == "Age Wise Available Birds-List"){ $agewise_abirds_details = $row1['active']; }
        if($row1['panel_name'] == "Bird Balance-List"){ $bird_bal_details = $row1['active']; }
        if($row1['panel_name'] == "Feed Balance-List"){ $feed_bal_details = $row1['active']; }
        if($row1['panel_name'] == "Live Farms-List"){ $live_farm_details = $row1['active']; }
        if($row1['panel_name'] == "Week Wise Mortality-List"){ $week_wise_mort_details = $row1['active']; }
        if($row1['panel_name'] == "Branch Wise Weekly Mortality-List"){ $branch_wise_weekly_mort_details = $row1['active']; }
        if($row1['panel_name'] == "Branch Wise Weekly Mortality Per-List"){ $branch_wise_weekly_mortper_details = $row1['active']; }
        if($row1['panel_name'] == "Branch Wise Total Mortality Percentage-List"){ $branch_wise_total_mort_details = $row1['active']; }
        if($row1['panel_name'] == "Branch Wise Single Date Mortality Percentage-List"){ $branch_wise_single_date_mort_details = $row1['active']; }
        if($row1['panel_name'] == "Branch Wise Current Mortality Percentage-List"){ $branch_wise_cur_mort_details = $row1['active']; }
        if($row1['panel_name'] == "Cash/Bank Balance"){ $cash_or_bank_balance_details = $row1['active']; }
        if($row1['panel_name'] == "Date wise Sale Details"){ $date_wise_sale_details = $row1['active']; }
        if($row1['panel_name'] == "Date wise Broiler Birds Stock Details"){ $date_wise_broilerbird_stock_details = $row1['active']; }
        $sorts[$row1['sort_order']] = $row1['panel_name'];
    }
    ksort($sorts);
}

if((int)$display_dashboard_flag == 1 && (int)$dboard_flag == 1){
    $colors[0] = 'cyan';
    $colors[1] = 'blue';
    $colors[2] = 'yellow';
    $colors[3] = 'red';
    $colors[4] = 'lime';
    $colors[5] = 'green';
    $colors[6] = 'orange';
    $colors[7] = 'purple';
    $colors[8] = 'brown';
    $colors[9] = 'coral';
    $colors[10] = 'silver';
    $colors[11] = 'maroon';
    $colors[12] = 'skyblue';
    $colors[13] = 'gray';
    $colors[14] = 'pink';
    $colors[15] = 'lavender';

    $batch_code = $batch_name = $batch_farm = array();
    $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_farm[$row['code']] = $row['farm_code']; }
    
    $farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
    $closed_farm_list_filter = $closed_line_list_filter = $closed_branch_list_filter = $closed_supervisor_list_filter = "";
    $closed_farm_list_filter = implode("','",$batch_farm);
    $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$closed_farm_list_filter') ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `description` NOT LIKE '%OLD FARMS%' AND `dflag` = '0' AND active = 1 ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
        $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
        $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
        $farm_farmer[$row['code']] = $row['farmer_code'];
    }
    $closed_line_list_filter = implode("','",$farm_line);
    $closed_branch_list_filter = implode("','",$farm_branch);
    $closed_supervisor_list_filter = implode("','",$farm_supervisor);
    
    $region_code = $region_name = array();
    $sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

    $branch_code = $branch_name = $branch_region = array();
    $sql = "SELECT * FROM `location_branch` WHERE `code` IN ('$closed_branch_list_filter') ".$branch_access_filter1." AND `dflag` = '0' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }
    
    $line_code = $line_name = $line_branch = array();
    $sql = "SELECT * FROM `location_line` WHERE `code` IN ('$closed_line_list_filter') ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }
    
    $supervisor_code = $supervisor_name = array();
    $sql = "SELECT * FROM `broiler_employee` WHERE `code` IN ('$closed_supervisor_list_filter') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }
    
    $fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = array(); $regions["all"] = $branches["all"] = $lines["all"] = $supervisors["all"] = $farms["all"] = "all";
    if(isset($_POST['submit'])){
        $fdate = $tdate = date("Y-m-d",strtotime($_POST['fdate']));
        //$tdate = date("Y-m-d",strtotime($_POST['tdate']));
    
        $regions = $branches = $lines = $supervisors = $farms = array(); $r_aflag = $b_aflag = $l_aflag = $s_aflag = $f_aflag = 0;
        foreach($_POST['regions'] as $t1){ $regions[$t1] = $t1; }         foreach($regions as $t1){ if($t1 == "all"){ $r_aflag = 1; } }
        foreach($_POST['branches'] as $t1){ $branches[$t1] = $t1; }         foreach($branches as $t1){ if($t1 == "all"){ $b_aflag = 1; } }
        foreach($_POST['lines'] as $t1){ $lines[$t1] = $t1; }               foreach($lines as $t1){ if($t1 == "all"){ $l_aflag = 1; } }
        foreach($_POST['supervisors'] as $t1){ $supervisors[$t1] = $t1; }   foreach($supervisors as $t1){ if($t1 == "all"){ $s_aflag = 1; } }
        foreach($_POST['farms'] as $t1){ $farms[$t1] = $t1; }               foreach($farms as $t1){ if($t1 == "all"){ $f_aflag = 1; } }
        
        $rgn_list = ""; if((INT)$r_aflag == 1){ $rgn_list = "all"; } else{ foreach($regions as $t1){ if($rgn_list == ""){ $rgn_list = $t1; } else{ $rgn_list = $rgn_list."','".$t1; } } }
        $brh_list = ""; if((INT)$b_aflag == 1){ $brh_list = "all"; } else{ foreach($branches as $t1){ if($brh_list == ""){ $brh_list = $t1; } else{ $brh_list = $brh_list."','".$t1; } } }
        $lne_list = ""; if((INT)$l_aflag == 1){ $lne_list = "all"; } else{ foreach($lines as $t1){ if($lne_list == ""){ $lne_list = $t1; } else{ $lne_list = $lne_list."','".$t1; } } }
        $sup_list = ""; if((INT)$s_aflag == 1){ $sup_list = "all"; } else{ foreach($supervisors as $t1){ if($sup_list == ""){ $sup_list = $t1; } else{ $sup_list = $sup_list."','".$t1; } } }
        $frm_list = ""; if((INT)$f_aflag == 1){ $frm_list = "all"; } else{ foreach($farms as $t1){ if($frm_list == ""){ $frm_list = $t1; } else{ $frm_list = $frm_list."','".$t1; } } }
    }
    else{
        $rgn_list = $brh_list = $lne_list = $sup_list = $frm_list = "all";
    }
    $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = "";
    // if($rgn_list != "all"){
    //     $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
    //     $rbrh_list = implode("','",$rbrh_alist);
    //     $rgn_fltr = " AND `branch_code` IN ('$rbrh_list')";
    // }
     if($rgn_list != "all"){
        $rbrh_alist = array(); 
        foreach($branch_code as $bcode){
             $rcode = $branch_region[$bcode];
              if (in_array($rcode, $regions)) {
                $rbrh_alist[$bcode] = $bcode; 
              }
        }
        $rbrh_list = implode("','",$rbrh_alist);
        $rgn_fltr = " AND `branch_code` IN ('$rbrh_list')";
    }
    if($brh_list != "all"){ $brh_fltr = " AND `branch_code` IN ('$brh_list')"; }
    if($lne_list != "all"){ $lne_fltr = " AND `line_code` IN ('$lne_list')"; }
    if($sup_list != "all"){ $sup_fltr = " AND `supervisor_code` IN ('$sup_list')"; }
    if($frm_list != "all"){ $frm_fltr = " AND `code` IN ('$frm_list')"; }
    
    $farm_list = ""; $farm_list = implode("','", $farm_code);
    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$rgn_fltr."".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $farm_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }
    
    $farm_list = ""; $farm_list = implode("','", $farm_alist); $gc_fltr = " AND `gc_flag` = '0'";
    $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')".$gc_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $batch_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND (`description` LIKE '%Broiler Bird%' OR `description` LIKE '%Broiler Chick%')";
    $query = mysqli_query($conn,$sql); $birds_acats = array();
    while($row = mysqli_fetch_assoc($query)){ $birds_acats[$row['code']] = $row['code']; }

    $ccat_list = ""; $ccat_list = implode("','", $birds_acats);
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$ccat_list') AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $birds_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $birds_alist[$row['code']] = $row['code']; }
    
    $item_list = "";
    foreach($birds_alist as $chks){ if($item_list == ""){ $item_list = $chks; } else{ $item_list = $item_list."','".$chks; } }
    
    $feed_acats = $feed_alist = array(); $fcat_list = "";
    if((int)$feed_stock_details == 1 || (int)$feed_bal_details == 1){
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $feed_acats[$row['code']] = $row['code']; }
    
        $fcat_list = implode("','", $feed_acats);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$fcat_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $feed_alist[$row['code']] = $row['code']; }
        
        foreach($feed_alist as $feeds){ if($item_list == ""){ $item_list = $feeds; } else{ $item_list = $item_list."','".$feeds; } }
    }
    //fetch-Calculations
    $batch_list = implode("','",$batch_alist);

    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch`,`icode` ASC";
    $query = mysqli_query($conn,$sql); $opur_cbirds = $bpur_cbirds = $stk_in_fqty = $opur_fqty = $bpur_fqty = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $row['icode'];
        if(!empty($birds_alist[$icode]) && $birds_alist[$icode] == $icode){
            if(strtotime($row['date']) <= strtotime($fdate)){
                $opur_cbirds[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
            }
            else{
                $bpur_cbirds[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
            }
        }
        else if(!empty($feed_alist[$icode]) && $feed_alist[$icode] == $icode){
            $stk_in_fqty[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
            if(strtotime($row['date']) < strtotime($fdate)){
                $opur_fqty[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
            }
            else{
                $bpur_fqty[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
            }
            
        }
        else{ }
    }

    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch`,`code` ASC";
    $query = mysqli_query($conn,$sql); $otrin_cbirds = $btrin_cbirds = $otrin_fqty = $btrin_fqty = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $row['code'];
        if(!empty($birds_alist[$icode]) && $birds_alist[$icode] == $icode){
            if(strtotime($row['date']) <= strtotime($fdate)){
                $otrin_cbirds[$row['to_batch']] += (float)$row['quantity'];
            }
            else{
                $btrin_cbirds[$row['to_batch']] += (float)$row['quantity'];
            }
        }
        else if(!empty($feed_alist[$icode]) && $feed_alist[$icode] == $icode){
            $stk_in_fqty[$row['to_batch']] += (float)$row['quantity'];
            if(strtotime($row['date']) < strtotime($fdate)){
                $otrin_fqty[$row['to_batch']] += (float)$row['quantity'];
            }
            else{
                $btrin_fqty[$row['to_batch']] += (float)$row['quantity'];
            }
        }
        else{ }
    }

    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_code`,`date` ASC";
    $query = mysqli_query($conn,$sql); $omort_cbirds = $bmort_cbirds = $bchk_mqty = $bchk_cqty = $brood_curage = $dentry_batches = $brood_age = $stk_con_fqty = $ocon_fqty = $bcon_fqty = 
    $mort_w1 = $mort_w2 = $mort_w3 = $mort_w4 = $mort_w5 = $mort_w6 = $mort_w7 = $mort_w8 = array();
    while($row = mysqli_fetch_assoc($query)){
        if(strtotime($row['date']) < strtotime($fdate)){
            $omort_cbirds[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']);
            $ocon_fqty[$row['batch_code']] += ((float)$row['kgs1'] + (float)$row['kgs2']);
        }
        else{
            $bmort_cbirds[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']);
            $bchk_mqty[$row['batch_code']] += (float)$row['mortality'];
            $bchk_cqty[$row['batch_code']] += (float)$row['culls'];
            $brood_curage[$row['batch_code']] = $row['brood_age'];
            $dentry_batches[$row['batch_code']] = $row['batch_code'];
            $bcon_fqty[$row['batch_code']] += ((float)$row['kgs1'] + (float)$row['kgs2']);
        }
        $brood_age[$row['batch_code']] = $row['brood_age'];
        $stk_con_fqty[$row['batch_code']] += ((float)$row['kgs1'] + (float)$row['kgs2']);

        //Weekly Mortality
        if((int)$row['brood_age'] <= 7){ $mort_w1[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 8 && (int)$row['brood_age'] <= 14){ $mort_w2[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 15 && (int)$row['brood_age'] <= 21){ $mort_w3[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 22 && (int)$row['brood_age'] <= 28){ $mort_w4[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 29 && (int)$row['brood_age'] <= 35){ $mort_w5[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 36 && (int)$row['brood_age'] <= 42){ $mort_w6[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 43 && (int)$row['brood_age'] <= 49){ $mort_w7[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); }
        else if((int)$row['brood_age'] >= 50){ $mort_w8[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']); } else{ }
    }
    
    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` = '$fdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_code` ASC";
    $query = mysqli_query($conn,$sql); $live_farms = mysqli_num_rows($query);
    
    $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `farm_batch` ASC";
    $query = mysqli_query($conn,$sql); $osale_cbirds = $osale_cweight = $bsale_cbirds = $bsale_cweight = $stk_out_fqty = $bsale_camt = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $row['icode'];
        if(!empty($birds_alist[$icode]) && $birds_alist[$icode] == $icode){
            if(strtotime($row['date']) < strtotime($fdate)){
                $osale_cbirds[$row['farm_batch']] += (float)$row['birds'];
                $osale_cweight[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
            }
            else{
                $bsale_cbirds[$row['farm_batch']] += (float)$row['birds'];
                $bsale_cweight[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                $bsale_camt[$row['farm_batch']] += (float)$row['item_tamt'];
            }
        }
        else if(!empty($feed_alist[$icode]) && $feed_alist[$icode] == $icode){
            $stk_out_fqty[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
        }
        else{ }
    }

    $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `date` <= '$tdate' AND `item_code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `from_batch` ASC";
    $query = mysqli_query($conn,$sql); $obout_cbirds = $obout_cweight = $bbout_cbirds = $bbout_cweight = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $row['item_code'];
        if(!empty($birds_alist[$icode]) && $birds_alist[$icode] == $icode){
            if(strtotime($row['date']) < strtotime($fdate)){
                $obout_cbirds[$row['from_batch']] += (float)$row['birds'];
                $obout_cweight[$row['from_batch']] += (float)$row['weight'];
            }
            else{
                $bsale_cbirds[$row['farm_batch']] += (float)$row['birds'];
                $bbout_cbirds[$row['from_batch']] += (float)$row['birds'];
                $bbout_cweight[$row['from_batch']] += (float)$row['weight'];
            }
        }
        else if(!empty($feed_alist[$icode]) && $feed_alist[$icode] == $icode){
            $stk_out_fqty[$row['from_batch']] += (float)$row['weight'];
        }
        else{ }
    }

    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch`,`code` ASC";
    $query = mysqli_query($conn,$sql); $otout_cbirds = $btout_cbirds = $otout_fqty = $btout_fqty = array();
    while($row = mysqli_fetch_assoc($query)){
        $icode = $row['code'];
        if(!empty($birds_alist[$icode]) && $birds_alist[$icode] == $icode){
            if(strtotime($row['date']) < strtotime($fdate)){
                $otout_cbirds[$row['from_batch']] += (float)$row['quantity'];
            }
            else{
                $otout_cbirds[$row['from_batch']] += (float)$row['quantity'];
            }
        }
        else if(!empty($feed_alist[$icode]) && $feed_alist[$icode] == $icode){
            $stk_out_fqty[$row['from_batch']] += (float)$row['quantity'];
            if(strtotime($row['date']) < strtotime($fdate)){
                $otout_fqty[$row['from_batch']] += (float)$row['quantity'];
            }
            else{
                $btout_fqty[$row['from_batch']] += (float)$row['quantity'];
            }
        }
        else{ }
    }
    
    //Cash/Bank Balance
    $crb_code = $crb_name = array();
    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `dflag` = '0' ORDER BY `ctype`,`description` ASC"; $query = mysqli_query($conn,$sql); $crb_coa_list = "";
    while($row = mysqli_fetch_assoc($query)){ $crb_code[$row['code']] = $row['code']; $crb_name[$row['code']] = $row['description']; if($crb_coa_list == ""){ $crb_coa_list = $row['code']; } else{ $crb_coa_list = $crb_coa_list."','".$row['code']; } }

    $sql = "SELECT SUM(amount) as amount,coa_code,crdr FROM `account_summary` WHERE `date` <= '$tdate' AND `coa_code` IN ('$crb_coa_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `coa_code`,`crdr` ORDER BY `coa_code`,`crdr` ASC";
    $query = mysqli_query($conn,$sql); $crb_cr_amt = $crb_dr_amt = array();
    while($row = mysqli_fetch_assoc($query)){
        if(empty($crb_cr_amt[$row['coa_code']])){ $crb_cr_amt[$row['coa_code']] = 0; }
        if(empty($crb_dr_amt[$row['coa_code']])){ $crb_dr_amt[$row['coa_code']] = 0; }

        if($row['crdr'] == "CR"){
            $crb_cr_amt[$row['coa_code']] += $row['amount'];
        }
        else if($row['crdr'] == "DR"){
            $crb_dr_amt[$row['coa_code']] += $row['amount'];
        }
        else{ }
    }

    //Day -1 Lifting Details
    $yfdate = date('Y-m-d', strtotime($fdate.'-1 days'));
    $ytdate = date('Y-m-d', strtotime($tdate.'-1 days'));
    $sql = "SELECT * FROM `broiler_sales` WHERE `date` >= '$yfdate' AND `date` <= '$ytdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `farm_batch` ASC";
    $query = mysqli_query($conn,$sql); $ybsale_cbirds = $ybsale_cweight = array();
    while($row = mysqli_fetch_assoc($query)){
        $ybsale_cbirds[$row['farm_batch']] += ((float)$row['birds']);
        $ybsale_cweight[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
    }
    
    $fetch_type = "branch_wise";
    $branch_mort_birds = $branch_cull_birds = $bwopn_fqty = $bwpur_fqty = $bwtrin_fqty = $bwcon_fqty = $bwtout_fqty = $baw_abds_7 = $baw_abds_14 = $baw_abds_21 = $baw_abds_28 = $baw_abds_35 = $baw_abds_42 = $baw_abds_49 = array();
    $bwmort_w1 = $bwmort_w2 = $bwmort_w3 = $bwmort_w4 = $bwmort_w5 = $bwmort_w6 = $bwmort_w7 = $bwmort_w8 = $chkpcd_bdate = $bwtc_oqty = array();
    $cmort_qty = 0;
    foreach($batch_alist as $bcode){
        $fcode = $batch_farm[$bcode]; $bhcode = $farm_branch[$fcode]; $lcode = $farm_line[$fcode]; $scode = $farm_supervisor[$fcode];
        $branch_list[$bhcode] = $bhcode;

        if($fetch_type == "branch_wise"){ $key1 = $bhcode; } else if($fetch_type == "line_wise"){ $key1 = $lcode; } else if($fetch_type == "supvr_wise"){ $key1 = $scode; } else{ $key1 = $bcode; }

        if(empty($opur_cbirds[$bcode])){ $opur_cbirds[$bcode] = 0; }
        if(empty($otrin_cbirds[$bcode])){ $otrin_cbirds[$bcode] = 0; }
        if(empty($omort_cbirds[$bcode])){ $omort_cbirds[$bcode] = 0; }
        if(empty($osale_cbirds[$bcode])){ $osale_cbirds[$bcode] = 0; }
        if(empty($otout_cbirds[$bcode])){ $otout_cbirds[$bcode] = 0; }
        if(empty($obout_cbirds[$bcode])){ $obout_cbirds[$bcode] = 0; }
        if(empty($bmort_cbirds[$bcode])){ $bmort_cbirds[$bcode] = 0; }
        if(empty($bsale_cbirds[$bcode])){ $bsale_cbirds[$bcode] = 0; }
        if(empty($bsale_cweight[$bcode])){ $bsale_cweight[$bcode] = 0; }
        if(empty($ybsale_cbirds[$bcode])){ $ybsale_cbirds[$bcode] = 0; }
        if(empty($ybsale_cweight[$bcode])){ $ybsale_cweight[$bcode] = 0; }
        if(empty($chick_placement[$key1])){ $chick_placement[$key1] = 0; }
        if(empty($branch_opening_birds[$key1])){ $branch_opening_birds[$key1] = 0; }
        if(empty($branch_curmort_birds[$key1])){ $branch_curmort_birds[$key1] = 0; }
        if(empty($branch_cur_birdno[$key1])){ $branch_cur_birdno[$key1] = 0; }
        if(empty($branch_cur_birdwt[$key1])){ $branch_cur_birdwt[$key1] = 0; }
        if(empty($branch_yest_birdno[$key1])){ $branch_yest_birdno[$key1] = 0; }
        if(empty($branch_yest_birdwt[$key1])){ $branch_yest_birdwt[$key1] = 0; }
        if(empty($branch_closing_birds[$key1])){ $branch_closing_birds[$key1] = 0; }

        //Feed Stock
        if((int)$feed_stock_details == 1){
            $bstk_in_fqty[$key1] += (float)$stk_in_fqty[$bcode];
            $bstk_con_fqty[$key1] += (float)$stk_con_fqty[$bcode];
            $bstk_out_fqty[$key1] += (float)$stk_out_fqty[$bcode];
        }
        if((int)$feed_bal_details == 1){
            $bwopn_fqty[$key1] += (((float)$opur_fqty[$bcode] + (float)$otrin_fqty[$bcode]) - ((float)$ocon_fqty[$bcode] + (float)$otout_fqty[$bcode]));
            $bwpur_fqty[$key1] += (float)$bpur_fqty[$bcode];
            $bwtrin_fqty[$key1] += (float)$btrin_fqty[$bcode];
            $bwcon_fqty[$key1] += (float)$bcon_fqty[$bcode];
            $bwtout_fqty[$key1] += (float)$btout_fqty[$bcode];
        }

        $p_tot = $p_tot + $opur_cbirds[$bcode] + $otrin_cbirds[$bcode];
        $m_tot = $m_tot + $omort_cbirds[$bcode];
        $s_tot = $s_tot + $osale_cbirds[$bcode] + $otout_cbirds[$bcode] + $obout_cbirds[$bcode];
        $cm_tot = $cm_tot + $bmort_cbirds[$bcode];
        $cs_tot = $cs_tot + $bsale_cbirds[$bcode];

        /*Placed Chicks*/
        $chick_placement[$key1] += ((float)$opur_cbirds[$bcode] + (float)$otrin_cbirds[$bcode]);
        /*B/w Placed Chicks*/
        $chkpcd_bdate[$key1] += ((float)$bpur_cbirds[$bcode] + (float)$btrin_cbirds[$bcode]);

        /*Opening Birds Calculations*/
        $opening_birds = 0; $opening_birds = (($opur_cbirds[$bcode] + $otrin_cbirds[$bcode]) - ($omort_cbirds[$bcode] + $osale_cbirds[$bcode] + $otout_cbirds[$bcode] + $obout_cbirds[$bcode]));
        $branch_opening_birds[$key1] += (int)$opening_birds;
        $total_opn_birds += (int)$opening_birds;

        /*Total Placement Farms*/
        $total_in_chicks = 0; $total_in_chicks = ((float)$opur_cbirds[$bcode] + (float)$otrin_cbirds[$bcode]);
        if($total_in_chicks != 0){ $total_farms++; }

        /*Current Mortality and Culls*/
        $cur_mortality = 0; $cur_mortality = $bmort_cbirds[$bcode];
        $branch_curmort_birds[$key1] += (float)$cur_mortality;
        $branch_mort_birds[$key1] += (float)$bchk_mqty[$bcode];
        $branch_cull_birds[$key1] += (float)$bchk_cqty[$bcode];
        $total_cur_mort += (float)$cur_mortality;
        $cmort_qty += (float)$bchk_mqty[$bcode];

        /*Live Farm Counts*/
        /*Current Lifting Details*/
        $cur_sale_birdno = 0; $cur_sale_birdno = $bsale_cbirds[$bcode];
        $branch_cur_birdno[$key1] += (float)$cur_sale_birdno;
        $total_cur_birdno += (float)$cur_sale_birdno;

        $cur_sale_birdwt = 0; $cur_sale_birdwt = $bsale_cweight[$bcode];
        $cur_sale_amt = 0; $cur_sale_amt = (float)$bsale_camt[$bcode];
        $branch_cur_birdwt[$key1] += (float)$cur_sale_birdwt;
        $branch_cur_saleamt[$key1] += (float)$cur_sale_amt;
        $total_cur_birdwt += (float)$cur_sale_birdwt;

        /*Day -1 Lifting Details*/
        $yest_sale_birdno = 0; $yest_sale_birdno = $ybsale_cbirds[$bcode];
        $branch_yest_birdno[$key1] += (float)$yest_sale_birdno;
        $total_yest_birdno += (float)$yest_sale_birdno;

        $yest_sale_birdwt = 0; $yest_sale_birdwt = $ybsale_cweight[$bcode];
        $branch_yest_birdwt[$key1] += (float)$yest_sale_birdwt;
        $total_yest_birdwt += (float)$yest_sale_birdwt;

        /*Closing Birds Calculations*/
        $closing_birds = 0; $closing_birds = ($opening_birds - ($cur_mortality + $cur_sale_birdno));
        $branch_closing_birds[$key1] += (int)$closing_birds;
        $total_cls_birds += (int)$closing_birds;

        /*Current Total Avg Mortality*/
       if(empty($dentry_batches[$bcode])){ }
        else{
            /*Present Day Daily entry Openings*/
            $total_popn_birds = $total_popn_birds + $opening_birds;

            $i++;
            if($cur_mortality == 0 || $cur_mortality == ""){ $cur_mortality = 0; $t_mort = 0; }
            else{ if((float)$opening_birds != 0){ $t_mort = round((((float)$cur_mortality / (float)$opening_birds) * 100),3); } else{ $t_mort = 0; } }
            
            if((float)$t_mort >= 0 && (float)$t_mort <= 0.25){
                $tm_farm1 = $tm_farm1 + $cur_mortality;
                $tm_per1 .= "-".$t_mort;
                $farm1++;
            }
            else if((float)$t_mort > 0.25 && (float)$t_mort <= 0.50){
                $tm_farm2 = $tm_farm2 + $cur_mortality;
                $tm_per2 .= "-".$t_mort;
                $farm2++;
            }
            else if((float)$t_mort > 0.50 && (float)$t_mort <= 1){
                $tm_farm3 = $tm_farm3 + $cur_mortality;
                $tm_per3 .= "-".$t_mort;
                $farm3++;
            }
            else if((float)$t_mort > 1){
                $tm_farm4 = $tm_farm4 + $cur_mortality;
                $tm_per4 .= "-".$t_mort."(".$cur_mortality."-".$opening_birds.")&";
                $farm4++;
            }
            else{ }
        }
        
        if(empty($brood_age[$bcode]) && empty($opur_cbirds[$bcode]) && empty($otrin_cbirds[$bcode]) && empty($bpur_cbirds[$bcode]) && empty($btrin_cbirds[$bcode])){ }
        else if(empty($brood_age[$bcode]) || $brood_age[$bcode] == "" || $brood_age[$bcode] >= 0 &&  $brood_age[$bcode] <= 7){
            $age_7 = $age_7 + $closing_birds;
            $baw_abds_7[$key1] += (float)$closing_birds;
            $age_farm7++;
        }
        else if($brood_age[$bcode] >= 8 &&  $brood_age[$bcode] <= 14){
            $age_14 = $age_14 + $closing_birds;
            $baw_abds_14[$key1] += (float)$closing_birds;
            $age_farm14++;
        }
        else if($brood_age[$bcode] >= 15 &&  $brood_age[$bcode] <= 21){
            $age_21 = $age_21 + $closing_birds;
            $baw_abds_21[$key1] += (float)$closing_birds;
            $age_farm21++;
        }
        else if($brood_age[$bcode] >= 22 &&  $brood_age[$bcode] <= 28){
            $age_28 = $age_28 + $closing_birds;
            $baw_abds_28[$key1] += (float)$closing_birds;
            $age_farm28++;
        }
        else if($brood_age[$bcode] >= 29 &&  $brood_age[$bcode] <= 35){
            $age_35 = $age_35 + $closing_birds;
            $baw_abds_35[$key1] += (float)$closing_birds;
            $age_farm35++;
        }
        else if($brood_age[$bcode] >= 36 &&  $brood_age[$bcode] <= 42){
            $age_42 = $age_42 + $closing_birds;
            $baw_abds_42[$key1] += (float)$closing_birds;
            $age_farm42++;
        }
        else if($brood_age[$bcode] >= 43 &&  $brood_age[$bcode] <= 49){
            $age_49 = $age_49 + $closing_birds;
            $baw_abds_49[$key1] += (float)$closing_birds;
            $age_farm49++;
        }
        else if($brood_age[$bcode] >= 50 &&  $brood_age[$bcode] <= 56){
            $age_56 = $age_56 + $closing_birds;
            $baw_abds_49[$key1] += (float)$closing_birds;
            $age_farm56++;
        }
        else{
            $age_oth = $age_oth + $closing_birds;
            $age_farmoth++;
        }

        /*Branch Wise Weekly Mortality Details*/
        $bwmort_w1[$key1] += (float)$mort_w1[$bcode];
        $bwmort_w2[$key1] += (float)$mort_w2[$bcode];
        $bwmort_w3[$key1] += (float)$mort_w3[$bcode];
        $bwmort_w4[$key1] += (float)$mort_w4[$bcode];
        $bwmort_w5[$key1] += (float)$mort_w5[$bcode];
        $bwmort_w6[$key1] += (float)$mort_w6[$bcode]; 
        $bwmort_w7[$key1] += (float)$mort_w7[$bcode];
        $bwmort_w8[$key1] += (float)$mort_w8[$bcode];
        $bwtmort_qty[$key1] += ((float)$mort_w1[$bcode] + (float)$mort_w2[$bcode] + (float)$mort_w3[$bcode] + (float)$mort_w4[$bcode] + (float)$mort_w5[$bcode] + (float)$mort_w6[$bcode] + (float)$mort_w7[$bcode] + (float)$mort_w8[$bcode]);
        /*Branch Wise Single Date Mortality*/
        if((float)$bchk_mqty[$bcode] != 0){ $bwtc_oqty[$key1] += (float)$opening_birds; }
        $bwsd_tmc_qty[$key1] += (float)$bchk_mqty[$bcode];

        /*Week Wise Mortality Details*/
        if($brood_curage[$bcode] >= 1 &&  $brood_curage[$bcode] <= 7){ $farm1_mort++; $week1_mort += (float)$cur_mortality; $week1_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] >= 8 &&  $brood_curage[$bcode] <= 14){ $farm2_mort++; $week2_mort += (float)$cur_mortality; $week2_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] >= 15 &&  $brood_curage[$bcode] <= 21){ $farm3_mort++; $week3_mort += (float)$cur_mortality; $week3_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] >= 22 &&  $brood_curage[$bcode] <= 28){ $farm4_mort++; $week4_mort += (float)$cur_mortality; $week4_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] >= 29 &&  $brood_curage[$bcode] <= 35){ $farm5_mort++; $week5_mort += (float)$cur_mortality; $week5_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] >= 36 &&  $brood_curage[$bcode] <= 42){ $farm6_mort++; $week6_mort += (float)$cur_mortality; $week6_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] >= 43 &&  $brood_curage[$bcode] <= 49){ $farm7_mort++; $week7_mort += (float)$cur_mortality; $week7_opnb += (float)$opening_birds; }
        else if($brood_curage[$bcode] > 49){ $farm8_mort++; $week8_mort += (float)$cur_mortality; $week8_opnb += (float)$opening_birds; }
        
        if(!empty($brood_age[$bcode]) && $brood_age[$bcode] > 42){
            $age_grt42 = $age_grt42 + $closing_birds;
            $age_farmgrt42++;
        }
        /*Above 40 Days Calculations*/
        if(!empty($brood_age[$bcode]) && $brood_age[$bcode] > 40){
            /*Opening*/
            $abv_40_opn = $abv_40_opn + $opening_birds;
            $abv_40_mrt = $abv_40_mrt + $cur_mortality;
            $abv_40_sle = $abv_40_sle + $cur_sale_birdno;
            $abv_40_cls = $abv_40_cls + $closing_birds;
        }
    }

?>
<html lang="en">
    <head>
        <?php include "header_mlinks.php"; ?>
        <style>
            body{
                font-family: sans-serif;
                background-color: #F7EDEF;
            }
            #openings{
                background-image: url("images/db_1.gif");
            }
        </style>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .chart-container {
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 20px;
            border: 2px solid #4169E1;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-header {
            font-weight: bold;
            font-size: 20px;
            text-align: left;
            width: 100%;
            margin-bottom: 10px;
        }
        .chart-average {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            width: 100%;
            text-align: left;
        }
        canvas {
            width: 100%;
            height: 250px !important;
        }
        .tbl-list tbody th,.tbl-list tbody td {
            border-bottom: 1px dotted black;
            border-collapse: collapse;
        }
    </style>
    </head>
    <body>
        <section class="content">
            <div class="container-fluid">
                <div class="content-header">
                    <div class="container-fluid">
                        <form action="controlpanel2_1.php" method="post">
                            <!--<div class="row">
                                <div class="col-md-2">
                                    <h3 class="m-0" style="color:blue;font-size:20px;font-weight:bold;">Live Dashboard</h3>
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-sm-4" style="text-align:center;">
                                    <a href="controlpanel_list.php" style="font-size:20px;font-weight:bold;"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;Click for Dashboards</a>
                                </div>
                            </div>-->
                            <div class="row">
                                <!--- <div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: #008000;margin-bottom: 10px;padding: 5px;">Good news!!! WhatsApp is up and running now. Please call 8746822822/8746855855 to scan and activate.Thank you!!!</marquee></h4></div> --->
                                <!--<div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: red;margin-bottom: 10px;padding: 5px;">Please call 8746822822/8746855855 to scan and activate.Thank you!!!</marquee></h4></div>-->
                                <!--<div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: green;margin-bottom: 10px;padding: 5px;">The server is now up and running successfully! Thank you for your cooperation during this period, and we assure you that this issue will not recur in the future.</marquee></h4></div>-->
                                <!--<div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: green;margin-bottom: 10px;padding: 5px;">Thank you for your warm wishes and continued support. Please note that our office has now shifted to Shivananda Circle, Sheshadripuram.</marquee></h4></div>-->
                    
                                <div class="col-md-2">
                                <h3 class="m-0" style="color:blue;font-size:20px;font-weight:bold;">Live Dashboard</h3>
                                </div>
                                <div class="col-md-10" style="text-align:center;">
                                    <div class="row justify-content-end align-items center">
                                        <div class="mr-2 form-group"><a href="broiler_projection_report1.php" style="font-size:20px;font-weight:bold;" target="_BLANK"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;Forecasting</a></div>
                                        <div class="mr-2 form-group"><a href="controlpanel2.php" style="font-size:20px;font-weight:bold;"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;P.Dashboards</a></div>
                                        <div class="form-group"><a href="controlpanel_list.php" style="font-size:20px;font-weight:bold;"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;Click for Dashboards</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group" style="width:120px;">
                                            <label for="fdate" class="font-weight-bold">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($fdate)); ?>" style="width:110px;">
                                        </div>
                                        <!--<div class="form-group" style="width:120px;">
                                            <label for="fdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepicker" value="<?php //echo date('d.m.Y',strtotime($tdate)); ?>" style="width:110px;">
                                        </div>-->
                                        <div class="form-group" style="width:220px;">
                                            <label for="regions" class="font-weight-bold">Region</label>
                                            <select name="regions[]" id="regions" class="form-control select2" style="width:210px;" multiple onchange="check_options(this.id);fetch_farms_details(this.id)">
                                                <option value="all" <?php foreach($regions as $rlist){ if($rlist == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($region_code as $bcode){ if($region_name[$bcode] != ""){ ?>
                                                <option value="<?php echo $bcode; ?>" <?php foreach($regions as $rlist){ if($rlist == $bcode){ echo "selected"; } } ?>><?php echo $region_name[$bcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:220px;">
                                            <label for="branches" class="font-weight-bold">Branch</label>
                                            <select name="branches[]" id="branches" class="form-control select2" style="width:210px;" multiple onchange="check_options(this.id);fetch_farms_details(this.id)">
                                                <option value="all" <?php foreach($branches as $blist){ if($blist == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                                <option value="<?php echo $bcode; ?>" <?php foreach($branches as $blist){ if($blist == $bcode){ echo "selected"; } } ?>><?php echo $branch_name[$bcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:220px;">
                                            <label for="lines" class="font-weight-bold">Line</label>
                                            <select name="lines[]" id="lines" class="form-control select2" style="width:210px;" multiple onchange="check_options(this.id);fetch_farms_details(this.id)">
                                                <option value="all" <?php foreach($lines as $blist){ if($blist == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                                <option value="<?php echo $lcode; ?>" <?php foreach($lines as $blist){ if($blist == $lcode){ echo "selected"; } } ?>><?php echo $line_name[$lcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:300px;">
                                            <label for="farms" class="font-weight-bold">Farm</label>
                                            <select name="farms[]" id="farms" class="form-control select2" style="width:290px;" multiple >
                                                <option value="all" <?php foreach($farms as $blist){ if($blist == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                                <option value="<?php echo $fcode; ?>" <?php foreach($farms as $blist){ if($blist == $fcode){ echo "selected"; } } ?>><?php echo $farm_name[$fcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:120px;"><br/>
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm btn-success">Submit</button>
                                        </div>
                                        <div class="form-group" style="width:50px;visibility:hidden;">
                                            <label for="supervisors" class="font-weight-bold">Sup</label>
                                            <select name="supervisors[]" id="supervisors" class="form-control select2" style="width:40px;" multiple onchange="fetch_farms_details(this.id)">
                                                <option value="all" <?php foreach($supervisors as $blist){ if($blist == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                                <option value="<?php echo $scode; ?>" <?php foreach($supervisors as $blist){ if($blist == $scode){ echo "selected"; } } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="content-body">
                    <div class="row">
                        <?php
                        foreach($sorts as $sorting){
                            if($sorting == "Chick Placement-List" && (int)$chick_placement_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-3 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100">
                                                            <tr style="text-align:center;">
                                                                <th colspan="3" style="text-align:center;"><label for="">Placed Birds</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $tot_pchicks = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $tot_pchicks += (float)$chick_placement[$bch_code];
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($chick_placement[$bch_code])); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <td colspan="1" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></td>
                                                            <td colspan="2" style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($tot_pchicks)); ?></b></h6></td>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "feed Stock-List" && (int)$feed_stock_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-6 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100">
                                                            <tr style="text-align:center;">
                                                                <th colspan="6" style="text-align:center;color:red;"><label for="">Feed Stock</label></th>
                                                            </tr>
                                                            <tr style="text-align:center;">
                                                                <th style="text-align:center;color:brown;"><label for="">Branch</label></th>
                                                                <th style="text-align:center;color:brown;"><label for=""></label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">In</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Consumed</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Out</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Stock</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $tfin_sqty = $tfcon_sqty = $tfout_sqty = $afeed_sqty = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $afeed_sqty = 0;
                                                                $tfin_sqty += (float)$bstk_in_fqty[$bch_code];
                                                                $tfcon_sqty += (float)$bstk_con_fqty[$bch_code];
                                                                $tfout_sqty += (float)$bstk_out_fqty[$bch_code];
                                                                $afeed_sqty = (float)$bstk_in_fqty[$bch_code] - ((float)$bstk_con_fqty[$bch_code] + (float)$bstk_out_fqty[$bch_code]);
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</h6></th>
                                                            <td style="text-align:center;"><h6><?php echo number_format_ind($bstk_in_fqty[$bch_code]); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo number_format_ind($bstk_con_fqty[$bch_code]); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo number_format_ind($bstk_out_fqty[$bch_code]); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo number_format_ind($afeed_sqty); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        $afeed_sqty = (float)$tfin_sqty - ((float)$tfcon_sqty + (float)$tfout_sqty);
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <td style="text-align:left;color:brown;"><h6>Total</h6></td>
                                                            <td style="text-align:left;"><h6>:</h6></td>
                                                            <td style="text-align:center;color:brown;"><h6><b><?php echo number_format_ind($tfin_sqty); ?></b></h6></td>
                                                            <td style="text-align:center;color:brown;"><h6><b><?php echo number_format_ind($tfcon_sqty); ?></b></h6></td>
                                                            <td style="text-align:center;color:brown;"><h6><b><?php echo number_format_ind($tfout_sqty); ?></b></h6></td>
                                                            <td style="text-align:center;color:brown;"><h6><b><?php echo number_format_ind($afeed_sqty); ?></b></h6></td>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Opening Birds-List" && (int)$opening_bird_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-3 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100">
                                                            <tr style="text-align:center;">
                                                                <th colspan="3" style="text-align:center;color:red;"><label for="">Opening Birds</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;">
                                                                <h6>
                                                                    <a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit_report=true&branches=<?php echo $bch_code; ?>&regions=all&lines=all&supervisors=all&farms=all&manual_nxtfeed=3&export=display" onclick="broiler_openurl(this.id);">
                                                                       <?php echo str_replace(".00","",number_format_ind($branch_opening_birds[$bch_code])); ?>
                                                                    </a>
                                                                </h6>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <td colspan="1" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></td>
                                                            <td colspan="2" style="text-align:center;color:brown;"><h6><a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><b><?php echo str_replace(".00","",number_format_ind($total_opn_birds)); ?></b></a></h6></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Bird Balance-List" && (int)$bird_bal_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-12 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100 tbl-list">
                                                            <tr style="text-align:center;">
                                                                <th colspan="8" style="text-align:center;color:red;"><label for="">Bird Balance</label></th>
                                                            </tr>
                                                            <tr style="text-align:center;">
                                                                <th style="text-align:center;color:brown;"><label for="">Branch</label></th>
                                                                <th style="text-align:center;"></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Opening Birds</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Placement</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Mortality</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Culls</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Sale</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Closing</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $pcd_bno = $opn_bno = $mort_bno = $cull_bno = $sale_bno = $clsd_bno = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $pcd_bno += (float)$chkpcd_bdate[$bch_code];
                                                                $opn_bno += (float)$branch_opening_birds[$bch_code];
                                                                $mort_bno += (float)$branch_mort_birds[$bch_code];
                                                                $cull_bno += (float)$branch_cull_birds[$bch_code];
                                                                $sale_bno += (float)$branch_cur_birdno[$bch_code];
                                                                $bcls_birds = 0; $bcls_birds = ((float)$branch_opening_birds[$bch_code] + (float)$chkpcd_bdate[$bch_code]) - ((float)$branch_mort_birds[$bch_code] + (float)$branch_cull_birds[$bch_code] + (float)$branch_cur_birdno[$bch_code]);
                                                                $clsd_bno += (float)$bcls_birds;
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($branch_opening_birds[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($chkpcd_bdate[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($branch_mort_birds[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($branch_cull_birds[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($branch_cur_birdno[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bcls_birds)); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <th colspan="2" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($opn_bno)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($pcd_bno)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mort_bno)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($cull_bno)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($sale_bno)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($clsd_bno)); ?></b></h6></th>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Feed Balance-List" && (int)$feed_bal_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-12 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100 tbl-list">
                                                            <tr style="text-align:center;">
                                                                <th colspan="8" style="text-align:center;color:red;"><label for="">Feed Balance</label></th>
                                                            </tr>
                                                            <tr style="text-align:center;">
                                                                <th style="text-align:center;color:brown;"><label for="">Branch</label></th>
                                                                <th style="text-align:center;"></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Opening</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Purchase</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Transfer-In</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Transfer-Out</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Consumed</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Balance</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $o_fqty = $p_fqty = $i_fqty = $c_fqty = $u_fqty = $b_fqty = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $o_fqty += (float)$bwopn_fqty[$bch_code];
                                                                $p_fqty += (float)$bwpur_fqty[$bch_code];
                                                                $i_fqty += (float)$bwtrin_fqty[$bch_code];
                                                                $c_fqty += (float)$bwcon_fqty[$bch_code];
                                                                $u_fqty += (float)$bwtout_fqty[$bch_code];
                                                                $a_fqty = 0; $a_fqty = (((float)$bwopn_fqty[$bch_code] + (float)$bwpur_fqty[$bch_code] + (float)$bwtrin_fqty[$bch_code]) - ((float)$bwcon_fqty[$bch_code] + (float)$bwtout_fqty[$bch_code]));
                                                                $b_fqty += (float)$a_fqty;

                                                                $i_url = ''; $i_url = '/records/broiler_batchwise_stocktransfer1.php?submit_report=true&fdate='.$fdate.'&tdate='.$fdate;
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwopn_fqty[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwpur_fqty[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwtrin_fqty[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><a href="javascript:void(0)" id="<?php echo $i_url; ?>" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($bwtout_fqty[$bch_code])); ?></a></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwcon_fqty[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($a_fqty)); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <th colspan="2" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($o_fqty)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($p_fqty)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($i_fqty)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($u_fqty)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($c_fqty)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($b_fqty)); ?></b></h6></th>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Age Wise Available Birds-List" && (int)$agewise_abirds_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-12 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100 tbl-list">
                                                            <tr style="text-align:center;">
                                                                <th colspan="10" style="text-align:center;color:red;"><h6><b>Birds Availability age wise:</b><b><?php echo str_replace(".00","",number_format_ind($total_cls_birds)); ?></b></h6></th>
                                                            </tr>
                                                            <tr style="text-align:center;">
                                                                <th style="text-align:center;color:brown;"><label for="">Branch</label></th>
                                                                <th style="text-align:center;"></th>
                                                                <th style="text-align:center;color:brown;"><label for="">0-7</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">8-14</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">15-21</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">22-28</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">29-35</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">36-42</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">42+</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Total</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $bwab_7 = $bwab_14 = $bwab_21 = $bwab_28 = $bwab_35 = $bwab_42 = $bwab_49 = $b_fqty = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $bwab_7 += (float)$baw_abds_7[$bch_code];
                                                                $bwab_14 += (float)$baw_abds_14[$bch_code];
                                                                $bwab_21 += (float)$baw_abds_21[$bch_code];
                                                                $bwab_28 += (float)$baw_abds_28[$bch_code];
                                                                $bwab_35 += (float)$baw_abds_35[$bch_code];
                                                                $bwab_42 += (float)$baw_abds_42[$bch_code];
                                                                $bwab_49 += (float)$baw_abds_49[$bch_code];
                                                                $a_fqty = 0; $a_fqty = ((float)$baw_abds_7[$bch_code] + (float)$baw_abds_14[$bch_code] + (float)$baw_abds_21[$bch_code] + (float)$baw_abds_28[$bch_code] + (float)$baw_abds_35[$bch_code] + (float)$baw_abds_42[$bch_code] + (float)$baw_abds_49[$bch_code]);
                                                                $b_fqty += (float)$a_fqty;
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_7[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_14[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_21[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_28[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_35[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_42[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($baw_abds_49[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($a_fqty)); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <th colspan="2" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_7)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_14)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_21)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_28)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_35)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_42)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($bwab_49)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($b_fqty)); ?></b></h6></th>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Branch Wise Weekly Mortality-List" && (int)$branch_wise_weekly_mort_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-12 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100 tbl-list">
                                                            <tr style="text-align:center;">
                                                                <th colspan="10" style="text-align:center; color:red;"><label for="">Week Wise Mortality</label></th>
                                                            </tr>
                                                            <tr style="text-align:center;">
                                                                <th style="text-align:center;color:brown;"><label for="">Branch</label></th>
                                                                <th style="text-align:center;"></th>
                                                                <th style="text-align:center;color:brown;"><label for="">1st Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">2nd Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">3rd Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">4th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">5th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">6th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">7th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">>7th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Total</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $mwk_1 = $mwk_2 = $mwk_3 = $mwk_4 = $mwk_5 = $mwk_6 = $mwk_7 = $mwk_8 = $b_fqty = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $mwk_1 += (float)$bwmort_w1[$bch_code];
                                                                $mwk_2 += (float)$bwmort_w2[$bch_code];
                                                                $mwk_3 += (float)$bwmort_w3[$bch_code];
                                                                $mwk_4 += (float)$bwmort_w4[$bch_code];
                                                                $mwk_5 += (float)$bwmort_w5[$bch_code];
                                                                $mwk_6 += (float)$bwmort_w6[$bch_code];
                                                                $mwk_7 += (float)$bwmort_w7[$bch_code];
                                                                $mwk_8 += (float)$bwmort_w8[$bch_code];
                                                                $a_fqty = 0; $a_fqty = ((float)$bwmort_w1[$bch_code] + (float)$bwmort_w2[$bch_code] + (float)$bwmort_w3[$bch_code] + (float)$bwmort_w4[$bch_code] + (float)$bwmort_w5[$bch_code] + (float)$bwmort_w6[$bch_code] + (float)$bwmort_w7[$bch_code] + (float)$bwmort_w8[$bch_code]);
                                                                $b_fqty += (float)$a_fqty;
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w1[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w2[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w3[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w4[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w5[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w6[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w7[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($bwmort_w8[$bch_code])); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind($a_fqty)); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <th colspan="2" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_1)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_2)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_3)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_4)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_5)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_6)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_7)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($mwk_8)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind($b_fqty)); ?></b></h6></th>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Branch Wise Weekly Mortality Per-List" && (int)$branch_wise_weekly_mortper_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-12 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                            <div class="col-md-12">
                                                <div class="m-0 p-0 card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                        <table class="w-100 tbl-list">
                                                            <tr style="text-align:center;">
                                                                <th colspan="10" style="text-align:center; color:red;"><label for="">Week Wise Mortality %</label></th>
                                                            </tr>
                                                            <tr style="text-align:center;">
                                                                <th style="text-align:center;color:brown;"><label for="">Branch</label></th>
                                                                <th style="text-align:center;"></th>
                                                                <th style="text-align:center;color:brown;"><label for="">1st Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">2nd Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">3rd Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">4th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">5th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">6th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">7th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">>7th Wk</label></th>
                                                                <th style="text-align:center;color:brown;"><label for="">Total</label></th>
                                                            </tr>
                                                        
                                                        <?php
                                                        $i = $mwk_1 = $mwk_2 = $mwk_3 = $mwk_4 = $mwk_5 = $mwk_6 = $mwk_7 = $mwk_8 = $b_fqty = $pcd_cqty = $ipc_qty = 0;
                                                        foreach($branch_code as $bch_code){
                                                            if(!empty($branch_list[$bch_code])){
                                                                $mwk_1 += (float)$bwmort_w1[$bch_code];
                                                                $mwk_2 += (float)$bwmort_w2[$bch_code];
                                                                $mwk_3 += (float)$bwmort_w3[$bch_code];
                                                                $mwk_4 += (float)$bwmort_w4[$bch_code];
                                                                $mwk_5 += (float)$bwmort_w5[$bch_code];
                                                                $mwk_6 += (float)$bwmort_w6[$bch_code];
                                                                $mwk_7 += (float)$bwmort_w7[$bch_code];
                                                                $mwk_8 += (float)$bwmort_w8[$bch_code];
                                                                $a_fqty = 0; $a_fqty = ((float)$bwmort_w1[$bch_code] + (float)$bwmort_w2[$bch_code] + (float)$bwmort_w3[$bch_code] + (float)$bwmort_w4[$bch_code] + (float)$bwmort_w5[$bch_code] + (float)$bwmort_w6[$bch_code] + (float)$bwmort_w7[$bch_code] + (float)$bwmort_w8[$bch_code]);
                                                                $b_fqty += (float)$a_fqty;
                                                                $pcd_cqty = $chick_placement[$bch_code];
                                                                if((float)$pcd_cqty != 0){
                                                                    $ipc_qty += (float)$chick_placement[$bch_code];
                                                        ?>
                                                        <tr style="width:100%;border-bottom:none;">
                                                            <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                            <th style="text-align:left;"><h6>:</th>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w1[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w2[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w3[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w4[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w5[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w6[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w7[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$bwmort_w8[$bch_code] / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                            <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round((((float)$a_fqty / (float)$pcd_cqty) * 100),2))); ?></h6></td>
                                                        </tr>
                                                        <?php
                                                                $i++;
                                                                }
                                                            }
                                                        }
                                                        if((float)$ipc_qty != 0){
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <th colspan="2" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_1 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_2 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_3 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_4 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_5 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_6 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_7 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$mwk_8 / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(round((((float)$b_fqty / (float)$ipc_qty) * 100),2))); ?></b></h6></th>
                                                        </tr>
                                                        <?php
                                                        }
                                                        else{
                                                        ?>
                                                        <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                            <th colspan="2" style="text-align:left;color:brown;"><h6><b>Total:</b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                            <th style="text-align:center;color:brown;"><h6><b><?php echo str_replace(".00","",number_format_ind(0)); ?></b></h6></th>
                                                        </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Branch Wise Total Mortality Percentage-List" && (int)$branch_wise_total_mort_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-6 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="container-fluid">
                                        <div class="card card-danger">
                                             <div class="p-0 card-body bg-light" id="openings1">
                                                <div class="card card-success">
                                                    <div class="card-body">
                                                        <div class="chart-container">
                                                            <div class="chart-header">MORTALITY & CULLS</div>

                                                            <canvas id="barChart1"></canvas>
                                                            <?php
                                                            $tcp_cnt = $tmc_cnt = $amc_per = 0;
                                                            foreach($branch_code as $bcode){
                                                                if(!empty($branch_list[$bcode])){
                                                                    $tcp_cnt += ((float)$chick_placement[$bcode] + (float)$chkpcd_bdate[$bcode]);
                                                                    $tmc_cnt += (float)$bwtmort_qty[$bcode];
                                                                }
                                                            }
                                                            if((float)$tcp_cnt != 0 && (float)$tmc_cnt != 0){
                                                                $amc_per = (((float)$tmc_cnt / (float)$tcp_cnt) * 100);
                                                            }
                                                            ?>
                                                            <div class="chart-average">AVERAGE : <strong><?php echo number_format_ind(round($amc_per,2)); ?> %</strong></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Branch Wise Single Date Mortality Percentage-List" && (int)$branch_wise_single_date_mort_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-6 col-6">
                                <section class="m-0 p-0 content">
                                    <div class="container-fluid">
                                        <div class="card card-danger">
                                             <div class="p-0 card-body bg-light" id="openings1">
                                                <div class="card card-success">
                                                    <div class="card-body">
                                                        <div class="chart-container">
                                                            <div class="chart-header">MORTALITY & CULLS</div>

                                                            <canvas id="barChart5"></canvas>
                                                            <?php
                                                                $mac_cnt = 0;
                                                                if($cmort_qty > 0 && $total_popn_birds > 0){
                                                                    $mac_cnt = (($cmort_qty / $total_popn_birds) * 100);
                                                                }
                                                            ?>
                                                            <div class="chart-average">AVERAGE : <strong><?php echo number_format_ind(round($mac_cnt,2)); ?> %</strong></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Live Farms-List" && (int)$live_farm_details == 1){
                            ?>
                            <div class="col-lg-3 col-6">
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card card-danger">
                                                    <div class="card-body bg-light" id="openings1">
                                                
                                                        <table class="w-100">
                                                            <tr style="text-align:center;">
                                                                <th colspan="3" style="text-align:center;"><label for="">Live Farms Count</label></th>
                                                            </tr>
                                                            <tr style="width:100%;border-bottom:none;">
                                                                <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[0].";'></button> Live Farms"; ?></h6></th>
                                                                <th style="text-align:left;"><h6>:</th>
                                                                <td style="text-align:center;"><h6><a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($total_farms)); ?></a></h6></td>
                                                            </tr>
                                                            <tr style="width:100%;border-bottom:none;">
                                                                <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[1].";'></button> Visited Farms"; ?></h6></th>
                                                                <th style="text-align:left;"><h6>:</th>
                                                                <td style="text-align:center;"><h6><a href="javascript:void(0)" id="/records/broiler_dayrecord_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($live_farms)); ?></a></h6></td>
                                                            </tr>
                                                            <tr style="width:100%;border-bottom:none;">
                                                                <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[2].";'></button> Not-Visited Farms"; ?></h6></th>
                                                                <th style="text-align:left;"><h6>:</th>
                                                                <td style="text-align:center;"><h6><a href="javascript:void(0)" id="/records/broiler_dailyentry_gapdays.php?submit=true" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($total_farms - $live_farms)); ?></a></h6></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Mortality Birds-Doughnut" && (int)$mortality_bird_details == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-4 col-6">
                                <!-- Main content -->
                                <section class="m-0 p-0 content">
                                    <div class="m-0 p-0 container-fluid">
                                        <div class="m-0 p-0 row">
                                        <div class="col-md-12">
                                            
                                            <!-- DONUT CHART -->
                                            <div class="m-0 p-0 card card-danger">
                                            <div class="card-body bg-light" align="center">
                                                <div class="w-100" style="width:100%;text-align:center;background-color:powderblue;"><h6 class="card-title"><b>Mortality and Culls</b></h6></div>
                                                <?php
                                                    if($total_cur_mort > 0 && $total_popn_birds > 0){
                                                        $ft_mcount = $total_cur_mort / $total_popn_birds;
                                                    }
                                                    else{
                                                        $ft_mcount = 0;
                                                    }
                                                ?>
                                                <div style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;">
                                                    <canvas id="donutChart"></canvas>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Lifting Birds-Bar Chart" && (int)$lifting_bird_details == 1){
                            ?>
                            <div class="col-lg-5 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                    <div class="card card-danger">
                                                <div class="p-0 card-body bg-light" id="openings1">
                                                    <div class="card card-success">
                                                        <div class="card-body">
                                                            <h6><b>Lifting Details</b></h6>
                                                            <?php
                                                                if($total_cur_birdwt > 0 && $total_cur_birdno > 0){
                                                                    $ft_ldcount = $total_cur_birdwt / $total_cur_birdno;
                                                                }
                                                                else{
                                                                    $ft_ldcount = 0;
                                                                }
                                                            ?>
                                                            <h6 style='color:green;'><?php echo "Total: Birds: <b>".str_replace(".00","",number_format_ind($total_cur_birdno))."</b> Weight: <b>".number_format_ind($total_cur_birdwt)."</b> Avg Wt: <b>".number_format_ind(round(($ft_ldcount),2))."</b>"; ?></h6>
                                                            <div class="chart">
                                                            <canvas id="barChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Lifting Birds-Bar Chart-2" && (int)$lifting_bird_details2 == 1){
                            ?>
                            <div class="m-0 p-0 col-lg-6 col-6">
                                <!-- Main content -->
                                <section class="m-0 p-0 content">
                                    <div class="container-fluid">
                                    <div class="card card-danger">
                                                <div class="p-0 card-body bg-light" id="openings1">
                                                    <div class="card card-success">
                                                        <div class="card-body">
                                                            <h6><b>Lifting Details</b></h6>
                                                            <?php
                                                                if($total_cur_birdwt > 0 && $total_cur_birdno > 0){
                                                                    $ft_ldcount = $total_cur_birdwt / $total_cur_birdno;
                                                                }
                                                                else{
                                                                    $ft_ldcount = 0;
                                                                }
                                                            ?>
                                                            <h6 style='color:green;'><?php echo "Total: Birds: <b>".str_replace(".00","",number_format_ind($total_cur_birdno))."</b> Weight: <b>".number_format_ind($total_cur_birdwt)."</b> Avg Wt: <b>".number_format_ind(round(($ft_ldcount),2))."</b>"; ?></h6>
                                                            <div class="chart">
                                                            <canvas id="barChart6" style="min-height: 360px; height: 360px; max-height: 360px; max-width: 100%;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Previous Day Lifting Birds-Bar Chart" && (int)$yesterday_lifting_bird_details == 1){
                            ?>
                            <div class="col-lg-5 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                    <div class="card card-danger">
                                                <div class="p-0 card-body bg-light" id="openings1">
                                                    <div class="card card-success">
                                                        <div class="card-body">
                                                            <h6><b>Lifting Details</b></h6>
                                                            <?php
                                                                if($total_yest_birdwt > 0 && $total_yest_birdno > 0){
                                                                    $ft_ld2count = $total_yest_birdwt / $total_yest_birdno;
                                                                }
                                                                else{
                                                                    $ft_ld2count = 0;
                                                                }
                                                            ?>
                                                            <h6 style='color:green;'><?php echo "Total: Birds: <b>".str_replace(".00","",number_format_ind($total_yest_birdno))."</b> Weight: <b>".number_format_ind($total_yest_birdwt)."</b> Avg Wt: <b>".number_format_ind(round(($ft_ld2count),2))."</b>"; ?></h6>
                                                            <div class="chart">
                                                            <canvas id="barChart3" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Closing Birds-List" && (int)$closing_bird_details == 1){
                            ?>
                            <div class="col-lg-3 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            
                                            <!-- DONUT CHART -->
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
                                                
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="3" style="text-align:center;"><label for="">Closing Birds</label></th>
                                                    </tr>
                                                
                                                <?php
                                                $i = 0;
                                                foreach($branch_code as $bch_code){
                                                    if(!empty($branch_list[$bch_code])){
                                                ?>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$i].";'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:center;">
                                                        <h6>
                                                            <a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit_report=true&branches=<?php echo $bch_code; ?>&regions=all&lines=all&supervisors=all&farms=all&manual_nxtfeed=3&export=display" onclick="broiler_openurl(this.id);">
                                                                <?php echo str_replace(".00","",number_format_ind($branch_closing_birds[$bch_code])); ?>
                                                            </a>                                                       
                                                        </h6>
                                                    </td>
                                                </tr>
                                                <?php
                                                        $i++;
                                                    }
                                                }
                                                ?>
                                                <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                    <td colspan="1" style="text-align:left;"><h6><b>Total:</b></h6></td>
                                                    <td colspan="2" style="text-align:center;"><h6><a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><b><?php echo str_replace(".00","",number_format_ind($total_cls_birds)); ?></b></a></h6></td>
                                                </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Total Mortality Per-List" && (int)$total_mortality_bird_details == 1){
                            ?>
                            <div class="col-lg-4 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            
                                            <!-- DONUT CHART -->
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
    
    
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="3" style="text-align:center;"><label for="">Total Mortality + Culls</label></th>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[0].";'></button> 0.00 - 0.25%"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm1)); ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $tm_per1; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm1,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[1].";'></button> 0.25 - 0.50%"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <th style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm2)); ?></th>
                                                        <td style="text-align:center;" title="<?php echo $tm_per2; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm2,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[2].";'></button> 0.50 - 1.00%"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm3)); ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $tm_per3; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm3,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[3].";'></button> > 1.00%"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm4)); ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $tm_per4; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm4,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                
                                                    <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                        <td colspan="1" style="text-align:left;"><h6><b>Total:</b></h6></td>
                                                        <td colspan="2" style="text-align:left;"><h6><b><?php echo str_replace(".00","",number_format_ind($total_cur_mort)); ?></b></h6></td>
                                                        <td colspan="2" style="text-align:center;"><h6><b><?php echo str_replace(".00","",number_format_ind(round(($farm1 + $farm2 + $farm3 + $farm4))))." Farms"; ?></b></h6></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Customer Supplier Balance-List" && (int)$Customer_Supplier_Balance_details == 1){
                                $today = date('Y-m-d');
                                $cus_sales = $cus_receipts = $cus_returns = $cus_ccn = $cus_cdn = $cus_contra_cr = $cus_contra_dr = $cus_obcramt = $cus_obdramt = $sup_obcramt = $sup_obdramt = $today_rct = 0;
                                $old_inv = $cus_list = $sup_list = $cus_filter = $sup_filter = "";
                                $cus_code = $sup_code = $cus_obtype = $cus_obamt = $sup_obtype = $sup_obamt = array();

                                $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` ='1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; if($row['obtype'] == "Cr" || $row['obtype'] == "CR"){ $cus_obcramt += (float)$row['obamt']; } else{ $cus_obdramt += (float)$row['obamt']; } }
                                $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' AND `active` ='1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; if($row['obtype'] == "Cr" || $row['obtype'] == "CR"){ $sup_obcramt += (float)$row['obamt']; } else{ $sup_obdramt += (float)$row['obamt']; } }
                                $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S&C' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $sup_code[$row['code']] = $row['code']; if($row['obtype'] == "Cr" || $row['obtype'] == "CR"){ $cus_obcramt += (float)$row['obamt']; } else{ $cus_obdramt += (float)$row['obamt']; } }
                                $cus_list = implode("','",$cus_code); $sup_list = implode("','",$sup_code);

                                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` <= '$today' AND `vcode` IN ('$cus_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $cus_sales += (float)$row['finl_amt']; $old_inv = $row['trnum']; } } }

                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_receipts` WHERE `date` <= '$today' AND `ccode` IN ('$cus_list') AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_receipts += (float)$row['amount']; } }
                                
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_receipts` WHERE `date` = '$fdate' AND `ccode` IN ('$cus_list') AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $today_rct += (float)$row['amount']; } }

                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_itemreturns` WHERE `date` <= '$today' AND `vcode` IN ('$cus_list') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_returns += (float)$row['amount']; } }

                                $sql_record = "SELECT SUM(amount) as amount,crdr FROM `broiler_crdrnote` WHERE `date` <= '$today' AND `vcode` IN ('$cus_list') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $cus_ccn += (float)$row['amount']; } else{ $cus_cdn += (float)$row['amount']; } } }

                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `fcoa` IN ('$cus_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_contra_cr += (float)$row['amount']; } }
                                
                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `tcoa` IN ('$cus_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_contra_dr += (float)$row['amount']; } }

                                $tot_cus_bal = (($cus_sales + $cus_cdn + $cus_contra_dr + $cus_obdramt) - ($cus_receipts + $cus_returns + $cus_ccn + $cus_contra_cr + $cus_obcramt));
                                
                                $sup_ccn = $sup_cdn = $sup_returns = $sup_payments = $sup_purchases = $sup_contra_cr = $sup_contra_dr = $today_pay = 0;
                                
                                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$today' AND `vcode` IN ('$sup_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); } $old_inv = "";
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $sup_purchases += (float)$row['finl_amt']; $old_inv = $row['trnum']; } } }

                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_payments` WHERE `date` <= '$today' AND `ccode` IN ('$sup_list') AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_payments += (float)$row['amount']; } }
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_payments` WHERE `date` = '$fdate' AND `ccode` IN ('$sup_list') AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $today_pay += (float)$row['amount']; } }

                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_itemreturns` WHERE `date` <= '$today' AND `vcode` IN ('$sup_list') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_returns += (float)$row['amount']; } }

                                $sql_record = "SELECT SUM(amount) as amount,crdr FROM `broiler_crdrnote` WHERE `date` <= '$today' AND `vcode` IN ('$sup_list') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $sup_ccn += (float)$row['amount']; } else{ $sup_cdn += (float)$row['amount']; } } }

                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `fcoa` IN ('$sup_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_contra_cr += (float)$row['amount']; } }
                                
                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `tcoa` IN ('$sup_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_contra_dr += (float)$row['amount']; } }

                                $tot_sup_bal = (($sup_purchases + $sup_ccn + $sup_obdramt + $sup_contra_cr) - ($sup_payments + $sup_cdn + $sup_obcramt + $sup_returns + $sup_contra_dr));
                                $sup_title = "$tot_sup_bal = (($sup_purchases + $sup_ccn + $sup_obdramt + $sup_contra_cr) - ($sup_payments + $sup_cdn + $sup_obcramt + $sup_returns + $sup_contra_dr))";
                        ?>
                            <div class="col-lg-4 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            
                                            <!-- DONUT CHART -->
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
    
    
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="2" style="text-align:center;"><label for="">Customer & Supplier Balances</label></th>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[0].";'></button>Total Receivables"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo number_format_ind($tot_cus_bal); ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[1].";'></button>Total Receipts"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo number_format_ind($today_rct); ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[2].";'></button>Total Payables"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6 title="<?php echo $sup_title; ?>"><?php echo number_format_ind($tot_sup_bal); ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[3].";'></button>Total Payments"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo number_format_ind($today_pay); ?></h6></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Week Wise Mortality-List" && (int)$week_wise_mort_details == 1){
                            ?>
                            <div class="col-lg-4 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            
                                            <!-- DONUT CHART -->
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
    
    
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="3" style="text-align:center;"><label for="">Week Wise Mortality</label></th>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[0].";'></button> 1st week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week1_mort)); if($week1_opnb != 0){ $t1 = 0; $t1 = round((($week1_mort / $week1_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week1_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm1_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[1].";'></button> 2nd week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <th style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week2_mort)); if($week2_opnb != 0){ $t1 = 0; $t1 = round((($week2_mort / $week2_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></th>
                                                        <td style="text-align:center;" title="<?php echo $week2_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm2_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[2].";'></button> 3rd week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week3_mort)); if($week3_opnb != 0){ $t1 = 0; $t1 = round((($week3_mort / $week3_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week3_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm3_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[3].";'></button> 4th week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week4_mort)); if($week4_opnb != 0){ $t1 = 0; $t1 = round((($week4_mort / $week4_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week4_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm4_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[4].";'></button> 5th week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week5_mort)); if($week5_opnb != 0){ $t1 = 0; $t1 = round((($week5_mort / $week5_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week5_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm5_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[5].";'></button> 6th week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week6_mort)); if($week6_opnb != 0){ $t1 = 0; $t1 = round((($week6_mort / $week6_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week6_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm6_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[6].";'></button> 7th week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week7_mort)); if($week7_opnb != 0){ $t1 = 0; $t1 = round((($week7_mort / $week7_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week7_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm7_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[7].";'></button> > 7th week"; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week8_mort)); if($week8_opnb != 0){ $t1 = 0; $t1 = round((($week8_mort / $week8_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                        <td style="text-align:center;" title="<?php echo $week8_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm8_mort,2)))." Farms"; ?></h6></td>
                                                    </tr>
                                                
                                                    <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                        <td colspan="1" style="text-align:left;"><h6><b>Total:</b></h6></td>
                                                        <td colspan="2" style="text-align:left;"><h6><b><?php echo str_replace(".00","",number_format_ind($total_cur_mort));  echo " (<b>".round((($ft_mcount) * 100),2)."%</b>)";?></b></h6></td>
                                                        <td colspan="2" style="text-align:center;"><h6><b><?php echo str_replace(".00","",number_format_ind(round(($farm1 + $farm2 + $farm3 + $farm4))))." Farms"; ?></b></h6></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Cash/Bank Balance" && (int)$cash_or_bank_balance_details == 1){
                            ?>
                            <div class="col-lg-4 col-6">
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="3" style="text-align:center;"><label for="">Cash/Bank</label></th>
                                                    </tr>
                                                    <tr style="text-align:center;">
                                                        <th colspan="1" style="text-align:center;"><label for="">name</label></th>
                                                        <th colspan="1" style="text-align:center;"></th>
                                                        <th colspan="1" style="text-align:center;"><label for="">Balance</label></th>
                                                    </tr>
                                                    <?php
                                                    $cb_incr = $crb_tbal_amt = 0;
                                                    foreach($crb_code as $crbs){
                                                        $cr_amt = $dr_amt = 0;
                                                        $cr_amt = round($crb_cr_amt[$crbs],5);
                                                        $dr_amt = round($crb_dr_amt[$crbs],5);
                                                        $bl_amt = (float)$dr_amt - (float)$cr_amt;
                                                        if(number_format_ind($bl_amt) != "0.00"){
                                                            $cb_incr++;
                                                            $crb_tbal_amt += (float)$bl_amt;
                                                    ?>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$cb_incr].";'></button> ".$crb_name[$crbs]; ?></h6></th>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($bl_amt,2)); ?></h6></td>
                                                    </tr>
                                                    <?php } } ?>
                                                    <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                        <td style="text-align:left;"><h6><b>Total:</b></h6></td>
                                                        <th style="text-align:left;"><h6>:</th>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($crb_tbal_amt,2)); ?></h6></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Date wise Sale Details" && (int)$date_wise_sale_details == 1){
                            ?>
                            <div class="col-lg-8 col-8">
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="1" style="text-align:center;"></th>
                                                        <th colspan="2" style="text-align:center;"><label for="">Purchse Details</label></th>
                                                        <th colspan="2" style="text-align:center;"><label for="">Sale Details</label></th>
                                                        <th colspan="2" style="text-align:center;"><label for="">Loss Details</label></th>
                                                    </tr>
                                                    <tr style="text-align:center;">
                                                        <th colspan="1" style="text-align:center;"><label for="">Item</label></th>
                                                        <th colspan="1" style="text-align:center;"><label for="">Birds</label></th>
                                                        <th colspan="1" style="text-align:center;">Weight</th>
                                                        <!--<th colspan="1" style="text-align:center;"><label for="">Amount</label></th>-->
                                                        <th colspan="1" style="text-align:center;"><label for="">Birds</label></th>
                                                        <th colspan="1" style="text-align:center;">Weight</th>
                                                        <!--<th colspan="1" style="text-align:center;"><label for="">Amount</label></th>-->
                                                        <th colspan="1" style="text-align:center;"><label for="">Birds</label></th>
                                                        <th colspan="1" style="text-align:center;">Weight</th>
                                                        <!--<th colspan="1" style="text-align:center;"><label for="">Amount</label></th>-->
                                                    </tr>
                                                    <?php
                                                    $cb_incr = $dws_tbirds = $dws_tweight = $dws_tamount = 0;
                                                    foreach($birds_code as $sbds){
                                                        $rpbirds = $rpweight = $rpamount = $rpprice = $rsbirds = $rsweight = $rsamount = $rlbirds = $rlweight = $rlamount = 0;
                                                        $rpbirds = round($iw_pur_birds[$sbds],5);
                                                        $rpweight = round($iw_pur_weight[$sbds],5);
                                                        $rpamount = round($iw_pur_amount[$sbds],5);
                                                        if((float)$rpweight != 0){ $rpprice = (float)$rpamount / (float)$rpweight; } else{ $rpprice = 0; }
                                                        $rsbirds = round($iw_sale_birds[$sbds],5);
                                                        $rsweight = round($iw_sale_weight[$sbds],5);
                                                        $rsamount = round($iw_sale_amount[$sbds],5);
                                                        
                                                        $rlbirds = (float)$rpbirds - (float)$rsbirds;
                                                        $rlweight = (float)$rpweight - (float)$rsweight;
                                                        $rlamount = round(((float)$rlweight * (float)$rpprice),2);
    
                                                        if(number_format_ind($rpweight) != "0.00" || number_format_ind($rsweight) != "0.00"){
                                                            $cb_incr++;
                                                            $dwp_tbirds += (float)$rpbirds;
                                                            $dwp_tweight += (float)$rpweight;
                                                            $dwp_tamount += (float)$rpamount;
                                                            $dws_tbirds += (float)$rsbirds;
                                                            $dws_tweight += (float)$rsweight;
                                                            $dws_tamount += (float)$rsamount;
                                                            $dwl_tbirds += (float)$rlbirds;
                                                            $dwl_tweight += (float)$rlweight;
                                                            $dwl_tamount += (float)$rlamount;
                                                    ?>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[$cb_incr].";'></button> ".$birds_name[$sbds]; ?></h6></th>
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($rpbirds,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($rpweight,2)); ?></h6></td>
                                                        <!--<td style="text-align:center;"><h6><?php //echo number_format_ind(round($rpamount,2)); ?></h6></td>-->
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($rsbirds,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($rsweight,2)); ?></h6></td>
                                                        <!--<td style="text-align:center;"><h6><?php //echo number_format_ind(round($rsamount,2)); ?></h6></td>-->
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($rlbirds,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($rlweight,2)); ?></h6></td>
                                                        <!--<td style="text-align:center;"><h6><?php //echo number_format_ind(round($rlamount,2)); ?></h6></td>-->
                                                    </tr>
                                                    <?php } } ?>
                                                    <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                        <td style="text-align:left;"><h6><b>Total:</b></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($dwp_tbirds,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($dwp_tweight,2)); ?></h6></td>
                                                        <!--<td style="text-align:center;"><h6><?php //echo number_format_ind(round($dwp_tamount,2)); ?></h6></td>-->
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($dws_tbirds,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($dws_tweight,2)); ?></h6></td>
                                                        <!--<td style="text-align:center;"><h6><?php //echo number_format_ind(round($dws_tamount,2)); ?></h6></td>-->
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($dwl_tbirds,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($dwl_tweight,2)); ?></h6></td>
                                                        <!--<td style="text-align:center;"><h6><?php //echo number_format_ind(round($dwl_tamount,2)); ?></h6></td>-->
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Date wise Broiler Birds Stock Details" && (int)$date_wise_broilerbird_stock_details == 1){
                            ?>
                            <div class="col-lg-4 col-6">
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-danger">
                                            <div class="card-body bg-light" id="openings1">
                                                <table class="w-100">
                                                    <tr style="text-align:center;">
                                                        <th colspan="3" style="text-align:center;"><label for="">Broiler Birds</label></th>
                                                    </tr>
                                                    <tr style="text-align:center;">
                                                        <th colspan="1" style="text-align:center;"><label for="">Transaction</label></th>
                                                        <th colspan="1" style="text-align:center;"><label for="">Birds</label></th>
                                                        <th colspan="1" style="text-align:center;">Weight</th>
                                                    </tr>
                                                    <?php
                                                    $loss_broiler_bird_nos =  $loss_broiler_bird_qty = 0;
                                                    if(number_format_ind($sale_broiler_bird_qty) != "0.00" || number_format_ind($pur_broiler_bird_qty) != "0.00"){
                                                        $loss_broiler_bird_nos = (float)$pur_broiler_bird_nos - (float)$sale_broiler_bird_nos;
                                                        $loss_broiler_bird_qty = (float)$pur_broiler_bird_qty - (float)$sale_broiler_bird_qty;
                                                    ?>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[0].";'></button> Purchases"; ?></h6></th>
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($pur_broiler_bird_nos,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($pur_broiler_bird_qty,2)); ?></h6></td>
                                                    </tr>
                                                    <tr style="width:100%;border-bottom:none;">
                                                        <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn' style='width:30px;height:10px;border-radius:none;background-color:".$colors[1].";'></button> Sales"; ?></h6></th>
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($sale_broiler_bird_nos,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($sale_broiler_bird_qty,2)); ?></h6></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                        <td style="text-align:left;"><h6>Loss:</h6></td>
                                                        <td style="text-align:center;"><h6><?php echo str_replace(".00","",number_format_ind(round($loss_broiler_bird_nos,2))); ?></h6></td>
                                                        <td style="text-align:center;"><h6><?php echo number_format_ind(round($loss_broiler_bird_qty,2)); ?></h6></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                            if($sorting == "Age Wise Available Birds-Bar Chart" && (int)$agewise_available_birds == 1){
                            ?>
                            <div class="col-lg-5 col-6">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="container-fluid">
                                    <div class="card card-danger">
                                                <div class="p-0 card-body bg-light" id="openings1">
                                                    <div class="card card-success">
                                                        <div class="card-body">
                                                            <h6>Age wise available Birds: <b><?php echo str_replace(".00","",number_format_ind($total_cls_birds)); ?></b></h6>
                                                            <div class="chart">
                                                            <canvas id="barChart2" style="min-height: 200px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </section>
                            </div>
                            <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <?php include "footer_mlinks.php"; ?>
        <script>
            var name1 = name2 = name3 = name4 = label_names = ""; var opn_value = names = names2 = value = []; var i = 0;
            <?php
            $key_names = $key_names2 = $key_val = $opn_names = $opnings = "";
            foreach($branch_code as $bch_code){
                if(!empty($branch_list[$bch_code])){
                    $opnings = str_replace(".00","",number_format_ind($branch_opening_birds[$bch_code]));
                    if($opn_names == ""){
                        $opn_names = $branch_name[$bch_code]."-".$opnings;
                    }
                    else{
                        $opn_names = $opn_names."@".$branch_name[$bch_code]."-".$opnings;
                    }
                    if($key_names == ""){
                        $key_names = $branch_name[$bch_code];
                    }
                    else{
                        $key_names = $key_names."@".$branch_name[$bch_code];
                    }
                    
                }
            }
            ?>
            <?php
            $key_val = $opn_birds = $opnings = $sale_bridno = $bno = $bwt = $sale_birdwt = $sale_amt = $sale_bridno2 = $bno2 = $bwt2 = $sale_birdwt2 = "";
            foreach($branch_code as $bch_code){
                if(!empty($branch_list[$bch_code])){
                    $mcount = str_replace(".00","",$branch_curmort_birds[$bch_code]);
                    if($key_val == ""){
                        $key_val = $mcount;
                    }
                    else{
                        $key_val = $key_val."@".$mcount;
                    }
                    $opnings = str_replace(".00","",$branch_opening_birds[$bch_code]);
                    if($opn_birds == ""){
                        $opn_birds = $opnings;
                    }
                    else{
                        $opn_birds = $opn_birds."@".$opnings;
                    }
                    $bno = str_replace(".00","",$branch_cur_birdno[$bch_code]);
                    if($sale_bridno == ""){
                        $sale_bridno = $bno;
                    }
                    else{
                        $sale_bridno = $sale_bridno."@".$bno;
                    }
                    $bwt = str_replace(".00","",$branch_cur_birdwt[$bch_code]);
                    if($sale_birdwt == ""){
                        $sale_birdwt = $bwt;
                    }
                    else{
                        $sale_birdwt = $sale_birdwt."@".$bwt;
                    }
                    $sale_prc = 0; if((float)$branch_cur_birdwt[$bch_code] != 0){ $sale_prc = round(((float)$branch_cur_saleamt[$bch_code] / (float)$branch_cur_birdwt[$bch_code]),2); }
                    //Avg. Prc Names
                    if($key_names2 == ""){
                        $key_names2 = $branch_name[$bch_code]." (".$sale_prc.")";
                    }
                    else{
                        $key_names2 = $key_names2."@".$branch_name[$bch_code]." (".$sale_prc.")";
                    }

                    /*Day -1 Lifting Details*/
                    $bno2 = str_replace(".00","",$branch_yest_birdno[$bch_code]);
                    if($sale_bridno2 == ""){
                        $sale_bridno2 = $bno2;
                    }
                    else{
                        $sale_bridno2 = $sale_bridno2."@".$bno2;
                    }
                    $bwt2 = str_replace(".00","",$branch_yest_birdwt[$bch_code]);
                    if($sale_birdwt2 == ""){
                        $sale_birdwt2 = $bwt2;
                    }
                    else{
                        $sale_birdwt2 = $sale_birdwt2."@".$bwt2;
                    }
                }
            }
            ?>
            name1 = '<?php echo $key_names; ?>'; names = name1.split("@");
            name2 = '<?php echo $key_names2; ?>'; names2 = name2.split("@");
            var value1 = '<?php echo $key_val; ?>'; values = value1.split("@");

            var mortality_bird_details = '<?php echo (int)$mortality_bird_details; ?>';
            if(parseInt(mortality_bird_details) == 1){
                var names_d1 = values_d1 = "";
                var names_d2 = values_d2 = [];
                for(var n1 = 0;n1 < names.length;n1++){
                    if(parseFloat(values[n1]) != 0){
                        if(names_d1 == ""){ names_d1 = names[n1]; } else{ names_d1 = names_d1+"@"+names[n1]; }
                        if(values_d1 == ""){ values_d1 = values[n1]; } else{ values_d1 = values_d1+"@"+values[n1]; }
                    }
                }
                names_d2 = names_d1.split("@");
                values_d2 = values_d1.split("@");
                //alert(name1+"\n"+value1+"\n"+names.length+"\n"+values.length+"\n\n\n"+names_d1+"\n"+values_d1+"\n"+names_d2.length+"\n"+values_d2.length);

                const ctx = document.getElementById('donutChart').getContext('2d');
                const data = {
                    labels: names_d2,
                    datasets: [{
                        label: 'Mortality',
                        data: values_d2,
                        backgroundColor: ['cyan', 'blue', 'yellow', 'red', 'lime', 'green', 'orange','purple', 'brown', 'coral', 'silver', 'maroon', 'skyblue', 'gray', 'pink', 'lavender'],
                        borderWidth: 1
                    }]
                };

                const config = {
                    type: 'doughnut',
                    data: data,
                    options: {
                        cutout: '60%',
                        plugins: {
                            datalabels: {
                                color: 'black',
                                font: {
                                    size: 12,
                                },
                                formatter: (value, context) => {
                                    return value;
                                }
                            },
                            legend: {
                                display: true,
                                position: 'center'
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    },
                    plugins: [
                        ChartDataLabels,
                        {
                            id: 'centerText',
                            beforeDraw: function(chart) {
                                const { width } = chart;
                                const { height } = chart;
                                const ctx = chart.ctx;
                                ctx.save();

                                // Calculate total value
                                const text = '<?php echo str_replace(".00","",number_format_ind($total_cur_mort))."<br/>(".number_format_ind(round((($ft_mcount) * 100),2))."%)"; ?>';
                                const lines = text.split('<br/>');

                                // Set text properties
                                const fontSize = 16;
                                ctx.font = `${fontSize}px Arial`;
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.fillStyle = '#000';

                                // Calculate center position
                                const centerX = width / 2;
                                const centerY = height / 3;

                                // Draw each line of text
                                var verticalOffset = 0
                                lines.forEach((line, index) => {
                                    verticalOffset = verticalOffset + 30;
                                    ctx.fillText(line, centerX, centerY + verticalOffset);
                                });

                                ctx.restore();
                            }
                        }
                    ]
                };
                new Chart(ctx, config);
            }

            var lifting_bird_details = '<?php echo (int)$lifting_bird_details; ?>';
            if(parseInt(lifting_bird_details) == 1){
                var birdno = birdwt = [];
                name3 = '<?php echo $sale_bridno; ?>'; birdno = name3.split("@");
                name3 = '<?php echo $sale_birdwt; ?>'; birdwt = name3.split("@");

                var names_d1 = birds_d1 = weight_d1 = "";
                var names_d2 = birds_d2 = weight_d2 = [];
                for(var n1 = 0;n1 < names.length;n1++){
                    if(parseFloat(birdno[n1]) != 0 && parseFloat(birdwt[n1]) != 0){
                        if(names_d1 == ""){ names_d1 = names[n1]; } else{ names_d1 = names_d1+"@"+names[n1]; }
                        if(birds_d1 == ""){ birds_d1 = birdno[n1]; } else{ birds_d1 = birds_d1+"@"+birdno[n1]; }
                        if(weight_d1 == ""){ weight_d1 = birdwt[n1]; } else{ weight_d1 = weight_d1+"@"+birdwt[n1]; }
                    }
                }
                names_d2 = names_d1.split("@");
                birds_d2 = birds_d1.split("@");
                weight_d2 = weight_d1.split("@");
                //alert(name1+"\n"+value1+"\n"+names.length+"\n"+values.length+"\n\n\n"+names_d1+"\n"+values_d1+"\n"+names_d2.length+"\n"+values_d2.length);

                
                // setup 
                const data = {
                labels: names_d2,
                datasets: [
                    {
                    label: 'Birds',
                    data: birds_d2,
                    maxBarThickness: 45,
                    backgroundColor: [
                    //'rgba(75, 192, 192, 0.2)',
                    'rgba(75, 192, 192, 1)',
                    ],
                    borderColor: [
                    'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1,
                    datalabels: {
                        color: 'rgba(75, 192, 192, 1)',
                        anchor: 'end',
                        align: 'top',
                        offset: 5
                    }
                },
                {
                    label: 'Weight',
                    data: weight_d2,
                    maxBarThickness: 35,
                    backgroundColor: [
                    //'rgba(54, 162, 235, 0.2)',
                    'rgba(54, 162, 235, 1)',
                    ],
                    borderColor: [
                    'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1,
                    datalabels: {
                        display: (context) => context.dataset.data[context.dataIndex] !== 0,
                        formatter: (value) => value,
                        color: 'rgba(54, 162, 235, 1)',
                        anchor: 'end',
                        align: 'top',
                        //rotation: 90,
                        offset: 15
                    }
                }]
                };

                //topLabels plugin block
                const topLabels = {
                    id: 'topLabels',
                    beforeRender(chart) {
                        const dataset = chart.data.datasets[0];
                        const meta = chart.getDatasetMeta(0);
                        meta.data.forEach((bar, index) => {
                            if (dataset.data[index] !== 0) {
                                const value = dataset.data[index];
                                const ctx = chart.ctx;
                                ctx.save();
                                ctx.font = '12px Arial';
                                ctx.textAlign = 'center';
                                ctx.fillStyle = '#000';
                                ctx.fillText(value, bar.x, bar.y - 10);
                                ctx.restore();
                            }
                        });
                    }
                }
                // config 
                const config = {
                type: 'bar',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 4000
                        }
                    }
                },
                plugins: [ChartDataLabels,topLabels]
                };

                // render init block
                const myChart = new Chart(
                document.getElementById('barChart'),
                config
                );
            }
            
            var lifting_bird_details2 = '<?php echo (int)$lifting_bird_details2; ?>';
            if(parseInt(lifting_bird_details2) == 1){
                var birdno = birdwt = [];
                name3 = '<?php echo $sale_bridno; ?>'; birdno = name3.split("@");
                name3 = '<?php echo $sale_birdwt; ?>'; birdwt = name3.split("@");

                var names_d1 = birds_d1 = weight_d1 = "";
                var names_d2 = birds_d2 = weight_d2 = [];
                for(var n1 = 0;n1 < names2.length;n1++){
                    if(parseFloat(birdno[n1]) != 0 && parseFloat(birdwt[n1]) != 0){
                        if(names_d1 == ""){ names_d1 = names2[n1]; } else{ names_d1 = names_d1+"@"+names2[n1]; }
                        if(birds_d1 == ""){ birds_d1 = birdno[n1]; } else{ birds_d1 = birds_d1+"@"+birdno[n1]; }
                        if(weight_d1 == ""){ weight_d1 = birdwt[n1]; } else{ weight_d1 = weight_d1+"@"+birdwt[n1]; }
                    }
                }
                names_d2 = names_d1.split("@");
                birds_d2 = birds_d1.split("@");
                weight_d2 = weight_d1.split("@");
                //alert(name1+"\n"+value1+"\n"+names.length+"\n"+values.length+"\n\n\n"+names_d1+"\n"+values_d1+"\n"+names_d2.length+"\n"+values_d2.length);

                
                // setup 
                const data = {
                labels: names_d2,
                datasets: [
                    {
                    label: 'Birds',
                    data: birds_d2,
                    maxBarThickness: 45,
                    backgroundColor: [
                    //'rgba(75, 192, 192, 0.2)',
                    'rgba(75, 192, 192, 1)',
                    ],
                    borderColor: [
                    'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1,
                    datalabels: {
                        color: 'rgba(75, 192, 192, 1)',
                        anchor: 'end',
                        align: 'top',
                        offset: 5
                    }
                },
                {
                    label: 'Weight',
                    data: weight_d2,
                    maxBarThickness: 35,
                    backgroundColor: [
                    //'rgba(54, 162, 235, 0.2)',
                    'rgba(54, 162, 235, 1)',
                    ],
                    borderColor: [
                    'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1,
                    datalabels: {
                        display: (context) => context.dataset.data[context.dataIndex] !== 0,
                        formatter: (value) => value,
                        color: 'rgba(54, 162, 235, 1)',
                        anchor: 'end',
                        align: 'top',
                        //rotation: 90,
                        offset: 15
                    }
                }]
                };

                //topLabels plugin block
                const topLabels = {
                    id: 'topLabels',
                    beforeRender(chart) {
                        const dataset = chart.data.datasets[0];
                        const meta = chart.getDatasetMeta(0);
                        meta.data.forEach((bar, index) => {
                            if (dataset.data[index] !== 0) {
                                const value = dataset.data[index];
                                const ctx = chart.ctx;
                                ctx.save();
                                ctx.font = '12px Arial';
                                ctx.textAlign = 'center';
                                ctx.fillStyle = '#000';
                                ctx.fillText(value, bar.x, bar.y - 10);
                                ctx.restore();
                            }
                        });
                    }
                }
                // config 
                const config = {
                type: 'bar',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 4000
                        }
                    }
                },
                plugins: [ChartDataLabels,topLabels]
                };

                // render init block
                const myChart = new Chart(
                document.getElementById('barChart6'),
                config
                );
            }
            
            var agewise_available_birds = '<?php echo (int)$agewise_available_birds; ?>';
            if(parseInt(agewise_available_birds) == 1){
                var age_name = age_birds = [];
                name4 = '<?php echo "[0 - 7,".$age_farm7."][@8 - 14,".$age_farm14."]@[15 - 21,".$age_farm21."]@[22 - 28,".$age_farm28."]@[29 - 35,".$age_farm35."]@[36 - 42,".$age_farm42."]@[42+Days,".$age_farmgrt42."]"; ?>'; age_name = name4.split("@");
                <?php
                $abird7 = str_replace(".00","",number_format_ind($age_7));
                $abird14 = str_replace(".00","",number_format_ind($age_14));
                $abird21 = str_replace(".00","",number_format_ind($age_21));
                $abird28 = str_replace(".00","",number_format_ind($age_28));
                $abird35 = str_replace(".00","",number_format_ind($age_35));
                $abird42 = str_replace(".00","",number_format_ind($age_42));
                $abirdgrt42 = str_replace(".00","",number_format_ind($age_grt42));
                ?>
                age_birds[0] = '<?php echo str_replace(".00","",$age_7); ?>';
                age_birds[1] = '<?php echo str_replace(".00","",$age_14); ?>';
                age_birds[2] = '<?php echo str_replace(".00","",$age_21); ?>';
                age_birds[3] = '<?php echo str_replace(".00","",$age_28); ?>';
                age_birds[4] = '<?php echo str_replace(".00","",$age_35); ?>';
                age_birds[5] = '<?php echo str_replace(".00","",$age_42); ?>';
                age_birds[6] = '<?php echo str_replace(".00","",$age_grt42); ?>';
                
                // setup 
                const data = {
                labels: [['0 - 7','<?php echo $age_farm7." Farms"; ?>','<?php echo $abird7; ?>'],['8 - 14','<?php echo $age_farm14." Farms"; ?>','<?php echo $abird14; ?>'],['15 - 21','<?php echo $age_farm21." Farms"; ?>','<?php echo $abird21; ?>'],['22 - 28','<?php echo $age_farm28." Farms"; ?>','<?php echo $abird28; ?>'],['29 - 35','<?php echo $age_farm35." Farms"; ?>','<?php echo $abird35; ?>'],['36 - 42','<?php echo $age_farm42." Farms"; ?>','<?php echo $abird42; ?>'],['42+Days','<?php echo $age_farmgrt42." Farms"; ?>','<?php echo $abirdgrt42; ?>']],
                datasets: [{
                    label: 'Birds',
                    data: age_birds,
                    maxBarThickness: 45,
                    backgroundColor: [
                    /*'rgba(255, 26, 104, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(0, 0, 0, 0.2)'*/
                    'rgba(9, 209, 0)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 0, 0, 1)'
                    ],
                    borderColor: [
                    'rgba(9, 209, 0)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 0, 0, 1)'
                    ],
                    borderWidth: 1,
                    datalabels: {
                        color: 'green',
                        anchor: 'end',
                        align: 'top',
                        offset: 5
                    }
                }]
                };

                // config 
                const config = {
                type: 'bar',
                data,
                options: {
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    weight: 'bold', // Bold labels
                                    size: 14
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    weight: 'bold', // Bold labels
                                    size: 14
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
                };

                // render init block
                const myChart = new Chart(
                document.getElementById('barChart2'),
                config
                );
            }
            var yesterday_lifting_bird_details = '<?php echo (int)$yesterday_lifting_bird_details; ?>';
            if(parseInt(yesterday_lifting_bird_details) == 1){
                var birdno2 = birdwt2 = [];
                var name5 = '<?php echo $sale_bridno2; ?>'; birdno2 = name5.split("@");
                var name6 = '<?php echo $sale_birdwt2; ?>'; birdwt2 = name6.split("@");

                var names_d1 = birds_d1 = weight_d1 = "";
                var names_d2 = birds_d2 = weight_d2 = [];
                for(var n1 = 0;n1 < names.length;n1++){
                    if(parseFloat(birdno2[n1]) != 0 && parseFloat(birdwt2[n1]) != 0){
                        if(names_d1 == ""){ names_d1 = names[n1]; } else{ names_d1 = names_d1+"@"+names[n1]; }
                        if(birds_d1 == ""){ birds_d1 = birdno2[n1]; } else{ birds_d1 = birds_d1+"@"+birdno2[n1]; }
                        if(weight_d1 == ""){ weight_d1 = birdwt2[n1]; } else{ weight_d1 = weight_d1+"@"+birdwt2[n1]; }
                    }
                }
                names_d2 = names_d1.split("@");
                birds_d2 = birds_d1.split("@");
                weight_d2 = weight_d1.split("@");
                //alert(name1+"\n"+value1+"\n"+names.length+"\n"+values.length+"\n\n\n"+names_d1+"\n"+values_d1+"\n"+names_d2.length+"\n"+values_d2.length);

                //setup 
                const data = {
                labels: names_d2,
                datasets: [
                    {
                    label: 'Birds',
                    data: birds_d2,
                    maxBarThickness: 45,
                    backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    ],
                    borderColor: [
                    'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1,
                    datalabels: {
                        color: 'rgba(75, 192, 192, 1)',
                        anchor: 'end',
                        align: 'top',
                        offset: 5
                    }
                },
                {
                    label: 'Weight',
                    data: weight_d2,
                    maxBarThickness: 45,
                    backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    ],
                    borderColor: [
                    'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1,
                    datalabels: {
                        display: (context) => context.dataset.data[context.dataIndex] !== 0,
                        formatter: (value) => value,
                        color: 'rgba(54, 162, 235, 1)',
                        anchor: 'end',
                        align: 'top',
                        offset: 5
                    }
                }]
                };

                //topLabels plugin block
                const topLabels = {
                    id: 'topLabels',
                    beforeRender(chart) {
                        const dataset = chart.data.datasets[0];
                        const meta = chart.getDatasetMeta(0);
                        meta.data.forEach((bar, index) => {
                            if (dataset.data[index] !== 0) {
                                const value = dataset.data[index];
                                const ctx = chart.ctx;
                                ctx.save();
                                ctx.font = '12px Arial';
                                ctx.textAlign = 'center';
                                ctx.fillStyle = '#000';
                                ctx.fillText(value, bar.x, bar.y - 10);
                                ctx.restore();
                            }
                        });
                    }
                }

                // config 
                const config = {
                type: 'bar',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 4
                        }
                    }
                },
                plugins: [ChartDataLabels,topLabels]
                };

                // render init block
                const myChart = new Chart(
                document.getElementById('barChart3'),
                config
                );
            }
        </script>
        <script>
            var branch_wise_total_mort_details = '<?php echo (int)$branch_wise_total_mort_details; ?>';
            if(parseInt(branch_wise_total_mort_details) == 1){
                <?php
                $bwt_mname = $bwt_mper = "";
                foreach($branch_code as $bcode){
                    if(!empty($branch_list[$bcode])){
                        $p_cnt = str_replace(".00","",$chick_placement[$bcode]);
                        $m_cnt = str_replace(".00","",$bwtmort_qty[$bcode]);
                        $m_per = 0; if((float)$p_cnt != 0){ $m_per = round((((float)$m_cnt / (float)$p_cnt) * 100),3); }
                        if((float)$m_per > 0){
                            if($bwt_mname == ""){ $bwt_mname = $branch_name[$bcode]; } else{ $bwt_mname = $bwt_mname."@".$branch_name[$bcode]; }
                            if($bwt_mper == ""){ $bwt_mper = $m_per; } else{ $bwt_mper = $bwt_mper."@".$m_per; }
                        }
                    }
                }
                ?>
                var names = values = [];
                var name1 = '<?php echo $bwt_mname; ?>'; names = name1.split("@");
                var value1 = '<?php echo $bwt_mper; ?>'; values = value1.split("@");
                
                var names_d1 = values_d1 = "";
                for(var n1 = 0;n1 < names.length;n1++){
                    if(parseFloat(values[n1]) != 0){
                        if(names_d1 == ""){ names_d1 = names[n1]; } else{ names_d1 = names_d1+"@"+names[n1]; }
                        if(values_d1 == ""){ values_d1 = values[n1]; } else{ values_d1 = values_d1+"@"+values[n1]; }
                    }
                }
                
                var names_d2 = values_d2 = [];
                names_d2 = names_d1.split("@");
                values_d2 = values_d1.split("@");
                
                Chart.register(ChartDataLabels);
                var ctx = document.getElementById('barChart1').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: names_d2,
                        datasets: [{
                            label: 'Mortality',
                            data: values_d2,
                            backgroundColor: 'rgba(65, 105, 225, 0.8)',
                            borderColor: 'rgba(65, 105, 225, 1)',
                            borderWidth: 1,
                            borderRadius: 5,
                            barThickness: 12
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'center',
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => parseFloat(value).toFixed(2) + " %"
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                //max: 5,
                                ticks: {
                                    callback: (value) => parseFloat(value) + " %"
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        </script>
        <script>
            var branch_wise_single_date_mort_details = '<?php echo (int)$branch_wise_single_date_mort_details; ?>';
            if(parseInt(branch_wise_single_date_mort_details) == 1){
                <?php
                $bwt_mname = $bwt_mper = "";
                foreach($branch_code as $bcode){
                    if(!empty($branch_list[$bcode])){
                        $p_cnt = str_replace(".00","",$bwtc_oqty[$bcode]);
                        $m_cnt = str_replace(".00","",$bwsd_tmc_qty[$bcode]);
                        $m_per = 0; if((float)$p_cnt != 0){ $m_per = round((((float)$m_cnt / (float)$p_cnt) * 100),3); }
                        
                        if((float)$m_per > 0){
                            if($bwt_mname == ""){ $bwt_mname = $branch_name[$bcode]; } else{ $bwt_mname = $bwt_mname."@".$branch_name[$bcode]; }
                            if($bwt_mper == ""){ $bwt_mper = $m_per; } else{ $bwt_mper = $bwt_mper."@".$m_per; }
                        }
                    }
                }
                ?>
                var names = values = [];
                var name1 = '<?php echo $bwt_mname; ?>'; names = name1.split("@");
                var value1 = '<?php echo $bwt_mper; ?>'; values = value1.split("@");
                
                var names_d1 = values_d1 = "";
                for(var n1 = 0;n1 < names.length;n1++){
                    if(parseFloat(values[n1]) != 0){
                        if(names_d1 == ""){ names_d1 = names[n1]; } else{ names_d1 = names_d1+"@"+names[n1]; }
                        if(values_d1 == ""){ values_d1 = values[n1]; } else{ values_d1 = values_d1+"@"+values[n1]; }
                    }
                }
                
                var names_d2 = values_d2 = [];
                names_d2 = names_d1.split("@");
                values_d2 = values_d1.split("@");
                
                Chart.register(ChartDataLabels);
                var ctx = document.getElementById('barChart5').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: names_d2,
                        datasets: [{
                            label: 'Mortality',
                            data: values_d2,
                            backgroundColor: 'rgba(65, 105, 225, 0.8)',
                            borderColor: 'rgba(65, 105, 225, 1)',
                            borderWidth: 1,
                            borderRadius: 5,
                            barThickness: 12
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'center',
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => parseFloat(value).toFixed(2) + " %"
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                //max: 5,
                                ticks: {
                                    callback: (value) => parseFloat(value) + " %"
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        </script>
        <script>
            function broiler_openurl(a){
                window.open(a, "_blank");
            }
        </script>
        <script>
             function check_options(selectId) {
                    var selectElement = document.getElementById(selectId);
                    var allflag = 0; var otherflag = 0;
                    for (var option of selectElement.options) {
                        if (option.selected) {
                            if (option.value === "all") { allflag = 1; 
                            } else { otherflag = 1; }
                        }
                    }
                    console.log(allflag,otherflag);
                    if (allflag === 1 && otherflag === 1) {
                        selectElement.querySelector('option[value="all"]').selected = false;
                        $(selectElement).trigger('change'); 
                    }
             }
            function fetch_farms_details(a){
                var rgn_aflag = brh_aflag = lne_aflag = sup_aflag = 0;
                var regions = branches = lines = supervisors = "";
                for(var option of document.getElementById("regions").options){ if(option.selected){ if(option.value == "all"){ rgn_aflag = 1; } else{ if(regions == ""){ regions = option.value; } else{ regions = regions+"@"+option.value; } } } }
                for(var option of document.getElementById("branches").options){ if(option.selected){ if(option.value == "all"){ brh_aflag = 1; } else{ if(branches == ""){ branches = option.value; } else{ branches = branches+"@"+option.value; } } } }
                for(var option of document.getElementById("lines").options){ if(option.selected){ if(option.value == "all"){ lne_aflag = 1; } else{ if(lines == ""){ lines = option.value; } else{ lines = lines+"@"+option.value; } } } }
                for(var option of document.getElementById("supervisors").options){ if(option.selected){ if(option.value == "all"){ sup_aflag = 1; } else{ if(supervisors == ""){ supervisors = option.value; } else{ supervisors = supervisors+"@"+option.value; } } } }
                if(rgn_aflag == 1){ regions = ""; regions = "all"; }
                if(brh_aflag == 1){ branches = ""; branches = "all"; }
                if(lne_aflag == 1){ lines = ""; lines = "all"; }
                if(sup_aflag == 1){ supervisors = ""; supervisors = "all"; }

                var user_code = '<?php echo $user_code; ?>';
                var rf_flag = bf_flag = lf_flag = sf_flag = ff_flag = gc_flag = 0;
                if(a.match("regions")){ rf_flag = 1; } else if(a.match("branches")){ bf_flag = 1; } else if(a.match("lines")){ lf_flag = 1; } else if(a.match("supervisors")){ sf_flag = 1; } else{ ff_flag = 1; }
                    
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "records/broiler_fetch_farm_filter_master.php?regions="+regions+"&branches="+branches+"&lines="+lines+"&supervisors="+supervisors+"&rf_flag="+rf_flag+"&bf_flag="+bf_flag+"&lf_flag="+lf_flag+"&sf_flag="+sf_flag+"&ff_flag="+ff_flag+"&gc_flag="+gc_flag+"&user_code="+user_code+"&fetch_type=multiple";
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
                    var b_aflag = '<?php echo $b_aflag; ?>';
                    var b_val = blist = "";
                    if(parseInt(b_aflag) == 0){
                        $('#branches').select2();
                        for(var option of document.getElementById("branches").options){
                            option.selected = false;
                            b_val = option.value;
                            <?php
                            foreach($branches as $blist){
                            ?>
                            blist = ''; blist = '<?php echo $blist; ?>';
                            if(b_val == blist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#branches').select2();
                    }
                    var fx = "branches"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    var l_aflag = '<?php echo $l_aflag; ?>';
                    var l_val = llist = "";
                    if(parseInt(l_aflag) == 0){
                        $('#lines').select2();
                        for(var option of document.getElementById("lines").options){
                            option.selected = false;
                            l_val = option.value;
                            <?php
                            foreach($lines as $llist){
                            ?>
                            llist = ''; llist = '<?php echo $llist; ?>';
                            if(l_val == llist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#lines').select2();
                    }
                    var fx = "lines"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    var s_aflag = '<?php echo $s_aflag; ?>';
                    var s_val = slist = "";
                    if(parseInt(s_aflag) == 0){
                        $('#supervisors').select2();
                        for(var option of document.getElementById("supervisors").options){
                            option.selected = false;
                            s_val = option.value;
                            <?php
                            foreach($supervisors as $slist){
                            ?>
                            slist = ''; slist = '<?php echo $slist; ?>';
                            if(s_val == slist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#supervisors').select2();
                    }
                    var fx = "supervisors"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    var f_aflag = '<?php echo $f_aflag; ?>';
                    var f_val = flist = lalist = "";
                    if(parseInt(f_aflag) == 0){
                        $('#farms').select2();
                        for(var option of document.getElementById("farms").options){
                            option.selected = false;
                            f_val = option.value;
                            <?php
                            foreach($farms as $flist){
                            ?>
                            flist = ''; flist = '<?php echo $flist; ?>';
                            if(f_val == flist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#farms').select2();
                    }
                    f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 4){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
    </body>
</html>
<?php
}    