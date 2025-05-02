<?php
//broiler_fetch_rearingcharge_mistri1.php
session_start(); include "newConfig.php";
$farm_code = $_GET['farm_code'];
$farm_batch = "";

$fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql);
while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; $batch_name = $frow['description']; }

if(!empty($farm_batch)){
    $pur_qty = $pur_amount = $trin_qty = $trin_amount = $day_qty = $day_amount = $day_ages = $day_mort = $mve_qty = $mve_amount = $sal_qty = $sal_amount = array();
    $trot_qty = $trot_amount = $item_all = $sal_birds = $maize_cat = $feed_cat = $medvac_cat = array(); $item_all[$chick_code] = $chick_code;

    $sql = "SELECT * FROM `broiler_farm` WHERE `code` LIKE '$farm_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $region_code = $row['region_code']; $branch_code = $row['branch_code']; $line_code = $row['line_code'];  $supervisor_code = $row['supervisor_code'];  $farmer_code = $row['farmer_code']; }

    $sql = "SELECT * FROM `broiler_employee` WHERE `code` LIKE '$supervisor_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $supervisor_name = $row['name']; }

    $sql = "SELECT * FROM `location_line` WHERE `code` LIKE '$line_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $line_name = $row['description']; }

    $sql = "SELECT * FROM `location_branch` WHERE `code` LIKE '$branch_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $branch_name = $row['description']; }

    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql); $bird_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_alist[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%maize%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $maize_cat[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%coal%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql); $coal_items = array();
    while($row = mysqli_fetch_assoc($query)){ $coal_items[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $medvac_cat[$row['code']] = $row['code']; }

    //Item CoA Accounts
    $sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $icat_iac[$row['code']] = $row['iac']; $icat_pvac[$row['code']] = $row['pvac']; $icat_pdac[$row['code']] = $row['pdac']; $icat_cogsac[$row['code']] = $row['cogsac'];
        $icat_wpac[$row['code']] = $row['wpac']; $icat_sac[$row['code']] = $row['sac']; $icat_srac[$row['code']] = $row['srac'];
    }
    $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

    $chick_iac = $icat_iac[$icat_code[$chick_code]];
    $sql = "SELECT MIN(date) as sdate,MAX(date) as edate FROM `account_summary` WHERE `batch` = '$farm_batch' AND `item_code` = '$chick_code' AND `crdr` = 'DR' AND `coa_code` = '$chick_iac' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $sdate = $row['sdate']; $edate = $row['edate']; }

    $sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$region_code' AND `branch_code` = '$branch_code' AND `from_date` <= '$sdate' AND `to_date` >= '$edate' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $gc_code = $row['code'];
        $chick_cost = $row['chick_cost'];
        $feed_cost = $row['feed_cost'];
        $maize_cost = $row['maize_cost'];
        $medicine_cost = $row['medicine_cost'];
        $med_price = $row['med_price'];
        $admin_cost = $row['admin_cost'];
        $standard_prod_cost = $row['standard_prod_cost'];
        $standard_cost = $row['standard_cost'];
        $minimum_cost = $row['minimum_cost'];
        $standard_fcr = $row['standard_fcr'];
        $standard_mortality = $row['standard_mortality'];
        $mgmt_admin_prc = $row['mgmt_admin_cost'];
    }

    //Farmer and mgmt costing
    //Purchases
    $sql = "SELECT * FROM `broiler_purchases` WHERE `warehouse` = '$farm_code' AND `farm_batch` = '$farm_batch' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        if($row['icode'] == $chick_code){
            $stkin_chick_qty += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $fmr_stkin_chick_amt += ((float)$chick_cost * ((float)$row['rcd_qty']+ (float)$row['fre_qty'])); //Farmer Chick Amount
            $mgmt_stkin_chick_amt += (((float)$row['item_tamt'])); //Mgmt Chick Amount
        }
        else if(!empty($maize_cat[$row['icode']])){
            $key1 = $row['icode'];
            $stkin_maize_qty[$key1] += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $total_stkin_maize_qty += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $stkin_maize_fmr_amt[$key1] += ((float)$maize_cost * ((float)$row['rcd_qty'] + (float)$row['fre_qty'])); //Farmer maize Amount
            
            $stkin_maize_mgmt_amt[$key1] += (((float)$row['item_tamt'])); //Mgmt maize Amount
            $stkin_maize_total_mgmt_amt += (((float)$row['item_tamt'])); //Mgmt maize Amount
        }
        else if(!empty($feed_cat[$row['icode']])){
            $key1 = $row['icode'];
            $stkin_feed_qty[$key1] += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $total_stkin_feed_qty += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $fmr_stkin_feed_amt[$key1] += ((float)$feed_cost * ((float)$row['rcd_qty'] + (float)$row['fre_qty'])); //Farmer Feed Amount
            
            $mgmt_stkin_feed_amt[$key1] += (((float)$row['item_tamt'])); //Mgmt Feed Amount
            $mgmt_total_stkin_feed_amt += (((float)$row['item_tamt'])); //Mgmt Feed Amount
        }
        else if(!empty($medvac_cat[$row['icode']])){
            $key1 = $row['icode'];
            $stkin_medvac_qty[$key1] += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $total_stkin_medvac_qty += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            //Farmer medvac Amount
            $ficode = $fidate = ""; $farmer_price = 0;
            if($medicine_cost == "M"){
                if($row['farmer_price'] == NULL || $row['farmer_price'] == "" || $row['farmer_price'] == 0 || $row['farmer_price'] == "0.00"){
                    $ficode = $row['icode']; $fidate = $row['date'];
                    $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                    $queryf = mysqli_query($conn,$sqlf); while($rowf = mysqli_fetch_assoc($queryf)){ $farmer_price = $rowf['rate']; }
                }
                else{ $farmer_price = $rowf['farmer_price']; }
            }
            else if($medicine_cost == "F"){ $farmer_price = $med_price; } else if($medicine_cost == "A"){ $farmer_price = $row['rate']; } else{ $farmer_price = $row['rate']; }

            $fmr_stkin_medvac_amt[$key1] += ((float)$farmer_price * ((float)$row['rcd_qty'] + (float)$row['fre_qty']));
            $fmr_total_stkin_medvac_amt += ((float)$farmer_price * ((float)$row['rcd_qty'] + (float)$row['fre_qty']));
            if(!empty($datewise_fmr_prc[$row['date']."@".$row['icode']]) && $datewise_fmr_prc[$row['date']."@".$row['icode']] != 0 || (float)$farmer_price == 0){ }
            else{ $datewise_fmr_prc[$row['date']."@".$row['icode']] = (float)$farmer_price; }

            //Mgmt medvac Amount
            $mgmt_stkin_medvac_amt[$key1] += (((float)$row['item_tamt']));
            $mgmt_total_stkin_medvac_amt += (((float)$row['item_tamt']));
        }
        else{ }
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }

    //Transfer In
    $sql = "SELECT * FROM `item_stocktransfers` WHERE `towarehouse` = '$farm_code' AND `to_batch` = '$farm_batch' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        if($row['code'] == $chick_code){
            $stkin_chick_qty += (((float)$row['quantity']));
            $fmr_stkin_chick_amt += ((float)$chick_cost * ((float)$row['quantity'])); //Farmer Chick Amount
            $mgmt_stkin_chick_amt += (((float)$row['amount'])); //Mgmt Chick Amount
        }
        else if(!empty($maize_cat[$row['code']])){
            $key1 = $row['code'];
            $stkin_maize_qty[$key1] += (((float)$row['quantity']));
            $total_stkin_maize_qty += (((float)$row['quantity']));
            $stkin_maize_fmr_amt[$key1] += ((float)$maize_cost * ((float)$row['quantity'])); //Farmer maize Amount
            
            $stkin_maize_mgmt_amt[$key1] += (((float)$row['amount'])); //Mgmt maize Amount
            $stkin_maize_total_mgmt_amt += (((float)$row['amount'])); //Mgmt maize Amount
        }
        else if(!empty($feed_cat[$row['code']])){
            $key1 = $row['code'];
            $stkin_feed_qty[$key1] += (((float)$row['quantity']));
            $total_stkin_feed_qty += (((float)$row['quantity']));
            $fmr_stkin_feed_amt[$key1] += ((float)$feed_cost * ((float)$row['quantity'])); //Farmer Feed Amount
            
            $mgmt_stkin_feed_amt[$key1] += (((float)$row['amount'])); //Mgmt Feed Amount
            $mgmt_total_stkin_feed_amt += (((float)$row['amount'])); //Mgmt Feed Amount
        }
        else if(!empty($medvac_cat[$row['code']])){
            $key1 = $row['code'];
            $stkin_medvac_qty[$key1] += (((float)$row['quantity']));
            $total_stkin_medvac_qty += (((float)$row['quantity']));
            //Farmer medvac Amount
            $ficode = $fidate = ""; $farmer_price = 0;
            if($medicine_cost == "M"){
                if($row['farmer_price'] == NULL || $row['farmer_price'] == "" || $row['farmer_price'] == 0 || $row['farmer_price'] == "0.00"){
                    $ficode = $row['code']; $fidate = $row['date'];
                    $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                    $queryf = mysqli_query($conn,$sqlf); while($rowf = mysqli_fetch_assoc($queryf)){ $farmer_price = $rowf['rate']; }
                }
                else{ $farmer_price = $rowf['farmer_price']; }
            }
            else if($medicine_cost == "F"){ $farmer_price = $med_price; } else if($medicine_cost == "A"){ $farmer_price = $row['price']; } else{ $farmer_price = $row['price']; }

            $fmr_stkin_medvac_amt[$key1] += ((float)$farmer_price * ((float)$row['quantity']));
            $fmr_total_stkin_medvac_amt += ((float)$farmer_price * ((float)$row['quantity']));
            if(!empty($datewise_fmr_prc[$row['date']."@".$row['code']]) && $datewise_fmr_prc[$row['date']."@".$row['code']] != 0 || (float)$farmer_price == 0){ }
            else{ $datewise_fmr_prc[$row['date']."@".$row['code']] = (float)$farmer_price; }
            
            //Mgmt medvac Amount
            $mgmt_stkin_medvac_amt[$key1] += (((float)$row['amount']));
            $mgmt_total_stkin_medvac_amt += (((float)$row['amount']));
        }
        else{ }
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }
    //Daily Entry
    $sql = "SELECT * FROM `broiler_daily_record` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$farm_batch' AND `active` = '1' AND `dflag` = '0' ORDER BY `brood_age` ASC";
    $query = mysqli_query($conn,$sql); $maize_cons_qty = $maize_cons_frmr_prc = $maize_cons_mgmt_prc = $maize_cons_frmr_amt = 0;
    while($row = mysqli_fetch_assoc($query)){
        $de_trnum = $row['trnum']; $de_dates = $row['date']; $de_morts = $row['mortality']; $de_culls = $row['culls'];
        $de_feed1 = $row['item_code1']; $de_fkgs1 = $row['kgs1']; $de_feed2 = $row['item_code2']; $de_fkgs2 = $row['kgs2']; $de_remrk = $row['remarks'];
        $de_ademp = $row['addedemp']; $de_adtme = $row['addedtime']; $de_udemp = $row['updatedemp']; $de_udtme = $row['updatedtime'];

        //Mortality Details
        if($row['brood_age'] <= 7){ $days7_mort_count += ((float)$de_morts + (float)$de_culls); $days30_mort_count += ((float)$de_morts + (float)$de_culls); }
        else if($row['brood_age'] <= 30){ $days30_mort_count += ((float)$de_morts + (float)$de_culls); }
        else if($row['brood_age'] >= 31){ $days31_mort_count += ((float)$de_morts + (float)$de_culls); }
        else{ }
        $total_mort_count += ((float)$de_morts + (float)$de_culls);

        //Feed and Maize Consumption Details
        //Farmer feed Consumed Amount
        if(!empty($maize_cat[$de_feed1])){
            $maize_cons_qty += (float)$de_fkgs1;
            $maize_cons_frmr_amt += ((float)$maize_cost * (float)$de_fkgs1);
        }
        else{
            $total_feed_consumed_qty += (float)$de_fkgs1;
            $fmr_total_feed_consumed_amt += ((float)$feed_cost * (float)$de_fkgs1);
        }
        if(!empty($maize_cat[$de_feed2])){
            $maize_cons_qty += (float)$de_fkgs2;
            $maize_cons_frmr_amt += ((float)$maize_cost * (float)$de_fkgs2);
        }
        else{
            $total_feed_consumed_qty += (float)$de_fkgs2;
            $fmr_total_feed_consumed_amt += ((float)$feed_cost * (float)$de_fkgs2);
        }
        
        //Mgmt feed Consumed Amount
        $destk_prc1 = $destk_amt1 = 0;
        if(!empty($maize_cat[$de_feed1])){
            if(!empty($stkin_maize_qty[$de_feed1]) && (float)$stkin_maize_qty[$de_feed1] != 0){ $destk_prc1 = (float)$stkin_maize_mgmt_amt[$de_feed1] / (float)$stkin_maize_qty[$de_feed1]; }
            $destk_amt1 = (float)$destk_prc1 * (float)$de_fkgs1; $maize_cons_mgmt_amt += (float)$destk_amt1;
            $stkin_maize_qty[$de_feed1] = (float)$stkin_maize_qty[$de_feed1] - (float)$de_fkgs1;
            $stkin_maize_mgmt_amt[$de_feed1] = (float)$stkin_maize_mgmt_amt[$de_feed1] - (float)$destk_amt1;
        }
        else{
            if(!empty($stkin_feed_qty[$de_feed1]) && (float)$stkin_feed_qty[$de_feed1] != 0){ $destk_prc1 = (float)$mgmt_stkin_feed_amt[$de_feed1] / (float)$stkin_feed_qty[$de_feed1]; }
            $destk_amt1 = (float)$destk_prc1 * (float)$de_fkgs1; $mgmt_total_feed_consumed_amt += (float)$destk_amt1;
            $stkin_feed_qty[$de_feed1] = (float)$stkin_feed_qty[$de_feed1] - (float)$de_fkgs1;
            $mgmt_stkin_feed_amt[$de_feed1] = (float)$mgmt_stkin_feed_amt[$de_feed1] - (float)$destk_amt1;
        }
        if(!empty($maize_cat[$de_feed1])){
            $destk_prc2 = $destk_amt2 = 0;
            if(!empty($stkin_maize_qty[$de_feed2]) && (float)$stkin_maize_qty[$de_feed2] != 0){ $destk_prc2 = (float)$stkin_maize_mgmt_amt[$de_feed2] / (float)$stkin_maize_qty[$de_feed2]; }
            $destk_amt2 = (float)$destk_prc2 * (float)$de_fkgs2; $maize_cons_mgmt_amt += (float)$destk_amt2;
            $stkin_maize_qty[$de_feed2] = (float)$stkin_maize_qty[$de_feed2] - (float)$de_fkgs2;
            $stkin_maize_mgmt_amt[$de_feed2] = (float)$stkin_maize_mgmt_amt[$de_feed2] - (float)$destk_amt2;
        }
        else{
            $destk_prc2 = $destk_amt2 = 0;
            if(!empty($stkin_feed_qty[$de_feed2]) && (float)$stkin_feed_qty[$de_feed2] != 0){ $destk_prc2 = (float)$mgmt_stkin_feed_amt[$de_feed2] / (float)$stkin_feed_qty[$de_feed2]; }
            $destk_amt2 = (float)$destk_prc2 * (float)$de_fkgs2; $mgmt_total_feed_consumed_amt += (float)$destk_amt2;
            $stkin_feed_qty[$de_feed2] = (float)$stkin_feed_qty[$de_feed2] - (float)$de_fkgs2;
            $mgmt_stkin_feed_amt[$de_feed2] = (float)$mgmt_stkin_feed_amt[$de_feed2] - (float)$destk_amt2;
        }

        //Summary Corrections
        $sql2 = "DELETE FROM `account_summary` WHERE `trnum` = '$de_trnum'"; if(!mysqli_query($conn,$sql2)){ die("Error 1:-".mysqli_error($conn)); } else { }

        if((float)$de_morts > 0){
            $il1 = ""; $il1 = $icat_code[$chick_code];
            $coa_Cr = $icat_iac[$il1];
            $coa_Dr = $icat_srac[$il1];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('CR','$coa_Cr','$de_dates','$de_trnum','$chick_code','$de_morts','0','0','$farm_code','$farm_batch','$de_remrk','0','DayEntryMortality','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
                
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('DR','$coa_Dr','$de_dates','$de_trnum','$chick_code','$de_morts','0','0','$farm_code','$farm_batch','$de_remrk','0','DayEntryMortality','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
        if((float)$de_culls > 0){
            $il1 = ""; $il1 = $icat_code[$chick_code];
            $coa_Cr = $icat_iac[$il1];
            $coa_Dr = $icat_srac[$il1];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('CR','$coa_Cr','$de_dates','$de_trnum','$chick_code','$de_culls','0','0','$farm_code','$farm_batch','$de_remrk','0','DayEntryCulls','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
            
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('DR','$coa_Dr','$de_dates','$de_trnum','$chick_code','$de_culls','0','0','$farm_code','$farm_batch','$de_remrk','0','DayEntryCulls','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
        if($de_feed1 == "" || $de_feed1 == "select" || $de_feed1 == NULL){ } else{
            $il1 = ""; $il1 = $icat_code[$de_feed1];
            $coa_Cr = $icat_iac[$il1];
            $coa_Dr = $icat_wpac[$il1];

            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('CR','$coa_Cr','$de_dates','$de_trnum','$de_feed1','$de_fkgs1','$destk_prc1','$destk_amt1','$farm_code','$farm_batch','$de_remrk','0','DayEntryFeed','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
                
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('DR','$coa_Dr','$de_dates','$de_trnum','$de_feed1','$de_fkgs1','$destk_prc1','$destk_amt1','$farm_code','$farm_batch','$de_remrk','0','DayEntryFeed','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
        if($de_feed2 == "" || $de_feed2 == "select" || $de_feed2 == NULL){ } else{
            $il1 = ""; $il1 = $icat_code[$de_feed2];
            $coa_Cr = $icat_iac[$il1];
            $coa_Dr = $icat_wpac[$il1];

            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('CR','$coa_Cr','$de_dates','$de_trnum','$de_feed2','$de_fkgs2','$destk_prc2','$destk_amt2','$farm_code','$farm_batch','$de_remrk','0','DayEntryFeed2','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
                
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('DR','$coa_Dr','$de_dates','$de_trnum','$de_feed2','$de_fkgs2','$destk_prc2','$destk_amt2','$farm_code','$farm_batch','$de_remrk','0','DayEntryFeed2','0','1','0','$de_ademp','$de_adtme','$de_udemp','$de_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
        
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
        if($dstart_date == ""){ $dstart_date = $row['date']; } else{ if(strtotime($dstart_date) >= strtotime($row['date'])){ $dstart_date = $row['date']; } }
        if($dend_date == ""){ $dend_date = $row['date']; } else{ if(strtotime($dend_date) <= strtotime($row['date'])){ $dend_date = $row['date']; } }

    }
    //Medecine & Vaccine Consumptions
    $count = 0;
    $sql = "SELECT * FROM `broiler_medicine_record` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$farm_batch' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $mv_trnum = $row['trnum']; $mv_dates = $row['date']; $mv_codes = $row['item_code']; $mv_quant = $row['quantity']; $mv_remrk = $row['remarks'];
        $mv_ademp = $row['addedemp']; $mv_adtme = $row['addedtime']; $mv_udemp = $row['updatedemp']; $mv_udtme = $row['updatedtime'];

        //MedVac Consumption Details
        $total_medvac_consumed_qty += (float)$mv_quant;
        //Farmer MedVac Consumed Amount
        $ficode = $fidate = ""; $farmer_price = 0;
        if($medicine_cost == "M"){
            $ficode = $row['item_code']; $fidate = $row['date']; $m_cnt = 0;
            $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
            $queryf = mysqli_query($conn,$sqlf); $m_cnt = mysqli_num_rows($queryf);
            if($m_cnt > 0){
                while($rowf = mysqli_fetch_assoc($queryf)){ $farmer_price = $rowf['rate']; }
            }
            else if(!empty($datewise_fmr_prc[$row['date']."@".$row['item_code']]) && $datewise_fmr_prc[$row['date']."@".$row['item_code']] > 0){
                $farmer_price = $datewise_fmr_prc[$row['date']."@".$row['item_code']];
            }
            else{
                $farmer_price = 0;
            }
        }
        else if($medicine_cost == "F"){ $farmer_price = $med_price; }
        else if($medicine_cost == "A"){
            if(!empty($stkin_medvac_qty[$mv_codes]) && $stkin_medvac_qty[$mv_codes] != 0){ $farmer_price = $fmr_stkin_medvac_amt[$mv_codes] / $stkin_medvac_qty[$mv_codes]; }
            else{ $farmer_price = 0; }
        }
        else{ $farmer_price = 0; }
        $fmr_total_medvac_consumed_amt += ((float)$farmer_price * (float)$mv_quant);
        //echo "<br/>".date("d.m.Y",strtotime($mv_dates))."@".$medicine_cost."@".$item_name[$mv_codes]."@".$farmer_price."@".$mv_quant."@".((float)$farmer_price * (float)$mv_quant)."<br/>";
        //Mgmt MedVac Consumed Amount
        $destk_prc1 = $destk_amt1 = 0;
        if(!empty($stkin_medvac_qty[$mv_codes]) && (float)$stkin_medvac_qty[$mv_codes] != 0){ $destk_prc1 = (float)$mgmt_stkin_medvac_amt[$mv_codes] / (float)$stkin_medvac_qty[$mv_codes]; }
        $destk_amt1 = (float)$destk_prc1 * (float)$mv_quant; $mgmt_total_medvac_consumed_amt += (float)$destk_amt1;
        $stkin_medvac_qty[$mv_codes] = (float)$stkin_medvac_qty[$mv_codes] - (float)$mv_quant;
        $fmr_stkin_medvac_amt[$mv_codes] = ($fmr_stkin_medvac_amt[$mv_codes] - ((float)$farmer_price * (float)$mv_quant));
        $mgmt_stkin_medvac_amt[$mv_codes] = (float)$mgmt_stkin_medvac_amt[$mv_codes] - (float)$destk_amt1;

        //Summary Corrections
        $sql2 = "DELETE FROM `account_summary` WHERE `trnum` = '$mv_trnum'"; if(!mysqli_query($conn,$sql2)){ die("Error 1:-".mysqli_error($conn)); } else { }
        if($mv_codes == "" || $mv_codes == "select" || $mv_codes == NULL){ } else{
            $il1 = ""; $il1 = $icat_code[$mv_codes];
            $coa_Cr = $icat_iac[$il1];
            $coa_Dr = $icat_wpac[$il1];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('CR','$coa_Cr','$mv_dates','$mv_trnum','$mv_codes','$mv_quant','$destk_prc1','$destk_amt1','$farm_code','$farm_batch','$mv_remrk','0','MedVacEntry','0','1','0','$mv_ademp','$mv_adtme','$mv_udemp','$mv_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
            
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
            VALUES ('DR','$coa_Dr','$mv_dates','$mv_trnum','$mv_codes','$mv_quant','$destk_prc1','$destk_amt1','$farm_code','$farm_batch','$mv_remrk','0','MedVacEntry','0','1','0','$mv_ademp','$mv_adtme','$mv_udemp','$mv_udtme')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
        
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }
    //Sales
    $coal_list = implode("','",$coal_items);
    $count = $farmer_sales_amt = 0; $sale_sdate = $sale_start_date = $sale_end_date = "";
    $sql = "SELECT * FROM `broiler_sales` WHERE `icode` NOT IN ('$coal_list') AND `warehouse` = '$farm_code' AND `farm_batch` = '$farm_batch' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        if(!empty($bird_alist[$row['icode']]) && $bird_alist[$row['icode']] == $row['icode']){
            $cus_sale_birdno += (((float)$row['birds']));
            $cus_sale_birdwt += (((float)$row['rcd_qty']+ (float)$row['fre_qty']));
            $cus_sale_amount += ((float)$row['rcd_qty'] * (float)$row['rate']);
            if($row['sale_type'] == "FormMBSale"){
                $farmer_sales_amt += ((float)$row['rcd_qty'] * (float)$row['rate']);
            }
            
            //Mean Age Calculations
            if(strtotime($row['date']) >= strtotime($dstart_date)){
                $dlist = (INT)((strtotime($row['date']) - strtotime($dstart_date)) / 60 / 60 / 24);
                $dlist2 = $dlist + 1;
                $sbirds = (float)$row['birds'];
                $sold_mean_total += ($dlist2 * $sbirds);
            }

            //Lifting Efficiency
            if($sale_sdate == ""){
                $sale_sdate = $row['date'];
                $s_cnt = 1;
                $s_wt += ((float)$s_cnt * ((float)$row['rcd_qty'] + (float)$row['fre_qty']));
                //echo "<br/>".$row['date']."@".$s_cnt."@".((float)$row['rcd_qty'] + (float)$row['fre_qty'])."@".((float)$s_cnt * ((float)$row['rcd_qty'] + (float)$row['fre_qty']))."@".$s_wt;
            }
            else{
                $s_cnt = (INT)(((strtotime($row['date']) - strtotime($sale_sdate)) / 60 / 60 / 24) + 1);
                $s_wt += ((float)$s_cnt * ((float)$row['rcd_qty'] + (float)$row['fre_qty']));
                //echo "<br/>".$row['date']."@".$s_cnt."@".((float)$row['rcd_qty'] + (float)$row['fre_qty'])."@".((float)$s_cnt * ((float)$row['rcd_qty'] + (float)$row['fre_qty']))."@".$s_wt;
            }

            //Sale Start and End Dates
            if($sale_start_date == ""){ $sale_start_date = $row['date']; } else{ if(strtotime($sale_start_date) >= strtotime($row['date'])){ $sale_start_date = $row['date']; } }
            if($sale_end_date == ""){ $sale_end_date = $row['date']; } else{ if(strtotime($sale_end_date) <= strtotime($row['date'])){ $sale_end_date = $row['date']; } }

        }
        else{ }

        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }
    $farmer_sales_amt = round($farmer_sales_amt,2);
    $sql = "SELECT * FROM `broiler_sales` WHERE `icode` IN ('$coal_list') AND `farm_batch` = '$farm_batch' AND `sale_type` = 'FarmerSale' AND `dflag` = '0' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $coal_damt = 0;
    while($row = mysqli_fetch_assoc($query)){ $coal_damt += (float)$row['item_tamt']; }
    //Transfer Out
    $count = 0;
    $sql = "SELECT * FROM `item_stocktransfers` WHERE `fromwarehouse` = '$farm_code' AND `from_batch` = '$farm_batch' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $so_id = $row['id']; $so_trnum = $row['trnum']; $so_dates = $row['date']; $so_codes = $row['code']; $so_quant = $row['quantity']; $so_price = $row['price']; $so_amont = $row['amount'];
        $so_frsec = $row['fromwarehouse']; $so_frbch = $row['from_batch']; $so_tosec = $row['towarehouse']; $so_tobch = $row['to_batch']; $so_remrk = $row['remarks'];
        $so_ademp = $row['addedemp']; $so_adtme = $row['addedtime']; $so_udemp = $row['updatedemp']; $so_udtme = $row['updatedtime'];

        if(!empty($maize_cat[$so_codes])){
            $total_maize_transout_qty += $so_quant;
            //Farmer maize Out Amount
            $total_maize_transout_fmr_amt += ((float)$maize_cost * ((float)$so_quant));
            
            //Mgmt maize Out Amount
            $destk_prc1 = $destk_amt1 = 0;
            if(!empty($stkin_maize_qty[$so_codes]) && (float)$stkin_maize_qty[$so_codes] != 0){ $destk_prc1 = (float)$stkin_maize_mgmt_amt[$so_codes] / (float)$stkin_maize_qty[$so_codes]; }
            $destk_amt1 = (float)$destk_prc1 * (float)$so_quant; $total_maize_transout_mgmt_amt += (float)$destk_amt1;

            $stkin_maize_qty[$so_codes] = (float)$stkin_maize_qty[$so_codes] - (float)$so_quant;
            $stkin_maize_mgmt_amt[$so_codes] = (float)$stkin_maize_mgmt_amt[$so_codes] - (float)$destk_amt1;

            $sql2 = "UPDATE `item_stocktransfers` SET `price` = '$destk_prc1',`amount` = '$destk_amt1' WHERE `id` = '$so_id'";
            if(!mysqli_query($conn,$sql2)){ die("Error:-".mysqli_error($conn)); } else {
                $sql2 = "DELETE FROM `account_summary` WHERE `date` = '$so_dates' AND `trnum` = '$so_trnum' AND `item_code` IN ('$so_codes') AND `quantity` IN ('$so_quant') AND `location` IN ('$so_frsec','$so_tosec') AND `batch` IN ('$so_frbch','$so_tobch')";
                if(!mysqli_query($conn,$sql2)){ die("Error:-".mysqli_error($conn)); } else{
                    $il1 = ""; $il1 = $icat_code[$so_codes];
                    $coa_Cr = $icat_iac[$il1];
                    $coa_Dr = $icat_iac[$il1];
        
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
                    VALUES ('CR','$coa_Cr','$so_dates','$so_trnum','$so_codes','$so_quant','$destk_prc1','$destk_amt1','$so_frsec','$so_frbch','$so_remrk','0','StockTransfer','0','1','0','$so_ademp','$so_adtme','$so_udemp','$so_udtme')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
                        
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
                    VALUES ('DR','$coa_Dr','$so_dates','$so_trnum','$so_codes','$so_quant','$destk_prc1','$destk_amt1','$so_tosec','$so_tobch','$so_remrk','0','StockTransfer','0','1','0','$so_ademp','$so_adtme','$so_udemp','$so_udtme')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        else if(!empty($feed_cat[$so_codes])){
            $total_feed_transout_qty += $so_quant;
            //Farmer Feed Out Amount
            if(!empty($maize_cat[$so_codes])){
                $fmr_total_feed_transout_amt += ((float)$maize_cost * ((float)$so_quant));
            }
            else{
                $fmr_total_feed_transout_amt += ((float)$feed_cost * ((float)$so_quant));
            }
            
            //Mgmt Feed Out Amount
            $destk_prc1 = $destk_amt1 = 0;
            if(!empty($stkin_feed_qty[$so_codes]) && (float)$stkin_feed_qty[$so_codes] != 0){ $destk_prc1 = (float)$mgmt_stkin_feed_amt[$so_codes] / (float)$stkin_feed_qty[$so_codes]; }
            $destk_amt1 = (float)$destk_prc1 * (float)$so_quant; $mgmt_total_feed_transout_amt += (float)$destk_amt1;

            $stkin_feed_qty[$so_codes] = (float)$stkin_feed_qty[$so_codes] - (float)$so_quant;
            $mgmt_stkin_feed_amt[$so_codes] = (float)$mgmt_stkin_feed_amt[$so_codes] - (float)$destk_amt1;

            $sql2 = "UPDATE `item_stocktransfers` SET `price` = '$destk_prc1',`amount` = '$destk_amt1' WHERE `id` = '$so_id'";
            if(!mysqli_query($conn,$sql2)){ die("Error:-".mysqli_error($conn)); } else {
                $sql2 = "DELETE FROM `account_summary` WHERE `date` = '$so_dates' AND `trnum` = '$so_trnum' AND `item_code` IN ('$so_codes') AND `quantity` IN ('$so_quant') AND `location` IN ('$so_frsec','$so_tosec') AND `batch` IN ('$so_frbch','$so_tobch')";
                if(!mysqli_query($conn,$sql2)){ die("Error:-".mysqli_error($conn)); } else{
                    $il1 = ""; $il1 = $icat_code[$so_codes];
                    $coa_Cr = $icat_iac[$il1];
                    $coa_Dr = $icat_iac[$il1];
        
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
                    VALUES ('CR','$coa_Cr','$so_dates','$so_trnum','$so_codes','$so_quant','$destk_prc1','$destk_amt1','$so_frsec','$so_frbch','$so_remrk','0','StockTransfer','0','1','0','$so_ademp','$so_adtme','$so_udemp','$so_udtme')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
                        
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
                    VALUES ('DR','$coa_Dr','$so_dates','$so_trnum','$so_codes','$so_quant','$destk_prc1','$destk_amt1','$so_tosec','$so_tobch','$so_remrk','0','StockTransfer','0','1','0','$so_ademp','$so_adtme','$so_udemp','$so_udtme')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        else if(!empty($medvac_cat[$so_codes])){
            $total_medvac_transout_qty += $so_quant;
            //Farmer Medvac Transfer-Out Amount
            $ficode = $fidate = ""; $farmer_price = 0;
            if($medicine_cost == "M"){
                if(!empty($datewise_fmr_prc[$so_dates."@".$so_codes]) && $datewise_fmr_prc[$so_dates."@".$so_codes] > 0){
                    $farmer_price = $datewise_fmr_prc[$so_dates."@".$so_codes];
                }
                else{
                    $ficode = $so_codes; $fidate = $so_dates;
                    $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                    $queryf = mysqli_query($conn,$sqlf); while($rowf = mysqli_fetch_assoc($queryf)){ $farmer_price = $rowf['rate']; }  
                }
            }
            else if($medicine_cost == "F"){ $farmer_price = $med_price; }
            else if($medicine_cost == "A"){
                if(!empty($stkin_medvac_qty[$so_codes]) && $stkin_medvac_qty[$so_codes] != 0){ $farmer_price = $fmr_stkin_medvac_amt[$so_codes] / $stkin_medvac_qty[$so_codes]; }
                else{ $farmer_price = 0; }
            }
            else{ $farmer_price = 0; }
            $fmr_total_medvac_transout_amt += ((float)$farmer_price * (float)$so_quant);
            //Mgmt Medvac Transfer-Out Amount
            $destk_prc1 = $destk_amt1 = 0;
            if(!empty($stkin_medvac_qty[$so_codes]) && (float)$stkin_medvac_qty[$so_codes] != 0){ $destk_prc1 = (float)$mgmt_stkin_medvac_amt[$so_codes] / (float)$stkin_medvac_qty[$so_codes]; }
            $destk_amt1 = (float)$destk_prc1 * (float)$so_quant; $mgmt_total_medvac_transout_amt += (float)$destk_amt1;
            $stkin_medvac_qty[$so_codes] = (float)$stkin_medvac_qty[$so_codes] - (float)$so_quant;
            $mgmt_stkin_medvac_amt[$so_codes] = (float)$mgmt_stkin_medvac_amt[$so_codes] - (float)$destk_amt1;

            $sql2 = "UPDATE `item_stocktransfers` SET `price` = '$destk_prc1',`farmer_price` = '$farmer_price',`amount` = '$destk_amt1' WHERE `id` = '$so_id'";
            if(!mysqli_query($conn,$sql2)){ die("Error:-".mysqli_error($conn)); } else {
                $sql2 = "DELETE FROM `account_summary` WHERE `date` = '$so_dates' AND `trnum` = '$so_trnum' AND `item_code` IN ('$so_codes') AND `quantity` IN ('$so_quant') AND `location` IN ('$so_frsec','$so_tosec') AND `batch` IN ('$so_frbch','$so_tobch')";
                if(!mysqli_query($conn,$sql2)){ die("Error:-".mysqli_error($conn)); } else{
                    $il1 = ""; $il1 = $icat_code[$so_codes];
                    $coa_Cr = $icat_iac[$il1];
                    $coa_Dr = $icat_iac[$il1];
        
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
                    VALUES ('CR','$coa_Cr','$so_dates','$so_trnum','$so_codes','$so_quant','$destk_prc1','$destk_amt1','$so_frsec','$so_frbch','$so_remrk','0','StockTransfer','0','1','0','$so_ademp','$so_adtme','$so_udemp','$so_udtme')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
                    
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedemp,updatedtime) 
                    VALUES ('DR','$coa_Dr','$so_dates','$so_trnum','$so_codes','$so_quant','$destk_prc1','$destk_amt1','$so_tosec','$so_tobch','$so_remrk','0','StockTransfer','0','1','0','$so_ademp','$so_adtme','$so_udemp','$so_udtme')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }

    /*  *****Final GC calculations***** */
    $placement_date = date("d.m.Y",strtotime($dstart_date));
    $placed_birds = $stkin_chick_qty;
    $mortality = $total_mort_count;
    $sold_birds = $cus_sale_birdno;
    $sold_weight = $cus_sale_birdwt;
    $sold_amount = $cus_sale_amount;
    if($sold_weight > 0){ $sold_rate = round(((float)$sold_amount / (float)$sold_weight),2); } else{  $sold_rate = 0; }

    if($placed_birds - $mortality - $sold_birds >= 0){ $shortage = (float)$placed_birds - (float)$mortality - (float)$sold_birds; $excess = 0; }
    else{ $excess = (float)$placed_birds - (float)$mortality - (float)$sold_birds; $shortage = 0; }

    $liquid_date = date("d.m.Y",strtotime($end_date));
    $age = ((INT)((strtotime($dend_date) - strtotime($dstart_date)) / 60 / 60 / 24)) + 1;

    if($placed_birds > 0){
        $days7_mort_per = round(((float)$days7_mort_count / (float)$placed_birds) * 100,2);
        $days30_mort_per = round(((float)$days30_mort_count / (float)$placed_birds) * 100,2);
        $days31_mort_per = round(((float)$days31_mort_count / (float)$placed_birds) * 100,2);
        $total_mort_per = round(((float)$total_mort_count / (float)$placed_birds) * 100,2);
    }
    else{
        $days7_mort_per = $days30_mort_per = $days31_mort_per = $total_mort_per = 0;
    }
    
    if($sold_birds > 0){ $avg_weight = round(((float)$sold_weight / (float)$sold_birds),2); } else { $avg_weight = 0; }
    if($sold_weight > 0){ $fcr = round((((float)$total_feed_consumed_qty + (float)$maize_cons_qty) / (float)$sold_weight),3); } else { $fcr = 0; }
    $cfcr = round((((2 - ((float)$avg_weight)) / 4) + (float)$fcr),3);

    if($sold_birds > 0){ $mean_age = round(((float)$sold_mean_total / (float)$sold_birds),2); } else { $mean_age = 0; }
    if($mean_age > 0){ $day_gain = round((((float)$avg_weight * 1000) / (float)$mean_age),2); } else { $day_gain = 0; }

    //EEF Calculations
    $t1 = 0; $t1 = ((float)$placed_birds - (float)$mortality);
    $t2 = 0; $t2 = (float)$placed_birds;
    $t3 = 0; $t3 = (float)$avg_weight;
    $t4 = 0; $t4 = ((float)$fcr * (float)$mean_age);
    if($t1 > 0 && $t2 > 0 && $t3 > 0 && $t4 > 0){ $eef = round((((((($t1) / $t2) * 100) * $t3) * 100) / ($t4))); } else{ $eef = 0; }

    
    //Chick Calculations
    /*Farmer Chick Price*/ if($placed_birds > 0){ $fmr_stkin_chick_prc = round(((float)$fmr_stkin_chick_amt / (float)$placed_birds),2); } else { $fmr_stkin_chick_prc = 0; }
    /*Mgmt Chick Price*/ if($placed_birds > 0){ $mgmt_stkin_chick_prc = round(((float)$mgmt_stkin_chick_amt / (float)$placed_birds),2); } else { $mgmt_stkin_chick_prc = 0; }

    //Feed Calculations
    $feed_in_bag = round(($total_stkin_feed_qty / 50),2);
    $feed_consumed_bag = round(($total_feed_consumed_qty / 50),2);
    $feed_out_bag = round(($total_feed_transout_qty / 50),2);
    $feed_balance = $total_stkin_feed_qty - $total_feed_consumed_qty - $total_feed_transout_qty;
    $feed_balance_bag = round(($feed_balance / 50),2);

    /*Farmer Feed Price*/ if($total_feed_consumed_qty > 0){ $fmr_total_feed_consumed_prc = round(((float)$fmr_total_feed_consumed_amt / (float)$total_feed_consumed_qty),2); } else { $fmr_total_feed_consumed_prc = 0; }
    /*Mgmt Feed Price*/ if($total_feed_consumed_qty > 0){ $mgmt_total_feed_consumed_prc = round(((float)$mgmt_total_feed_consumed_amt / (float)$total_feed_consumed_qty),2); } else { $mgmt_total_feed_consumed_prc = 0; }
    
    //Maize Calculations
    $maize_bal_qty = (float)$total_stkin_maize_qty - (float)$maize_cons_qty - (float)$total_maize_transout_qty;
    /*Farmer Maize Price*/ if((float)$maize_cons_qty != 0){ $maize_cons_frmr_prc = (float)$maize_cons_frmr_amt / (float)$maize_cons_qty; } else { $maize_cons_frmr_prc = 0; }
    /*Mgmt Maize Price*/ if((float)$maize_cons_qty != 0){ $maize_cons_mgmt_prc = (float)$maize_cons_mgmt_amt / (float)$maize_cons_qty; } else { $maize_cons_mgmt_prc = 0; }
    
    //Medvac Calculations
    $medvac_balance = (float)$total_stkin_medvac_qty - (float)$total_medvac_consumed_qty - (float)$total_medvac_transout_qty;
    if($total_stkin_medvac_qty > 0){
        $fmr_stkin_medvac_prc = (float)$fmr_total_stkin_medvac_amt / (float)$total_stkin_medvac_qty;
        $mgmt_stkin_medvac_prc = (float)$mgmt_total_stkin_medvac_amt / (float)$total_stkin_medvac_qty;
    }
    else {
        $fmr_stkin_medvac_prc = $mgmt_stkin_medvac_prc = 0;
    }
    if($placed_birds > 0){
        if($medicine_cost == "F"){ $fmr_total_medvac_consumed_amt = 0; $fmr_total_medvac_consumed_amt = ((float)$med_price * (float)$placed_birds); }
        $fmr_total_medvac_consumed_prc = round(((float)$fmr_total_medvac_consumed_amt / (float)$placed_birds),2);
    }
    else { $fmr_total_medvac_consumed_prc = 0; }
    if($placed_birds > 0){ $mgmt_total_medvac_consumed_prc = round(((float)$mgmt_total_medvac_consumed_amt / (float)$placed_birds),2); } else { $mgmt_total_medvac_consumed_prc = 0; }

    //Farmer Admin Cost Calculations
    $fmr_admin_amt = round(($admin_cost * $placed_birds),2);
    if($placed_birds > 0){ $fmr_admin_prc = round(($fmr_admin_amt / $placed_birds),2); } else{ $fmr_admin_prc = 0; }
    
    //Mgmt Admin Cost Calculations
    $mgmt_admin_amt = $mgmt_admin_prc * $placed_birds;
    if($mgmt_admin_prc == 0 || $mgmt_admin_prc == ""){ $mgmt_admin_prc = 0; } if($mgmt_admin_amt == 0 || $mgmt_admin_amt == ""){ $mgmt_admin_amt = 0; }
    
    //Farmer Production Cost
    $fmr_act_prod_amt = round(((float)$fmr_stkin_chick_amt + (float)$fmr_total_feed_consumed_amt + (float)$maize_cons_frmr_amt + (float)$fmr_admin_amt + (float)$fmr_total_medvac_consumed_amt),2);
    if($sold_weight > 0){ $fmr_act_prod_prc = round(((float)$fmr_act_prod_amt / (float)$sold_weight),2); } else { $fmr_act_prod_prc = 0; }
    
    //Std. Production Cost
    $standard_amount = round(($standard_cost * $sold_weight));

    /*********** Grade ***********/
    $sql = "SELECT * FROM `broiler_farmer_classify` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        if($fmr_act_prod_prc >= $row['prod_from_classify'] && $fmr_act_prod_prc <= $row['prod_to_classify']){ $grade = $row['grade_classify']; }
    }

    //Lifting Efficiency Calculations
    if((float)$cus_sale_birdwt != 0){ $lifting_efficiency = round(((float)$s_wt / (float)$cus_sale_birdwt),2); } else{ $lifting_efficiency = 0; }

    /*********** Production Cost Incentive ***********/
    if(round($fmr_act_prod_prc,2) <= round($standard_prod_cost,2)){        
        $sql = "SELECT * FROM `broiler_gc_pc_incentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
        while($row = mysqli_fetch_assoc($query)){
            $i++;
            $prod_from_incs[$i] = (float)$row['prod_from_inc'];
            $prod_to_incs[$i] = (float)$row['prod_to_inc'];
            $rate_incs[$i] = (float)$row['rate_inc'] / 100;
            $counts[$i] = $i;
        }
        foreach($counts as $cn){
            if($fmr_act_prod_prc <= $prod_to_incs[$cn]){
                $rates = (float)$prod_to_incs[$cn] - (float)$fmr_act_prod_prc;
                $rate_inc[$cn] = (float)$rates * (float)$rate_incs[$cn];
            }
            else{
                $rate_inc[$cn] = 0;
            }
        }
        $prod_inc_rate = 0;
        foreach($counts as $cn){
            $prod_inc_rate = (float)$prod_inc_rate + (float)$rate_inc[$cn];
        }
        $prod_inc_rate = round($prod_inc_rate,3);
        $act_gc_prc = (float)$prod_inc_rate; $actual_gc_per_kg = (float)$standard_cost + (float)$prod_inc_rate;
        $actual_gc_amount = round(((float)$actual_gc_per_kg * (float)$sold_weight),2);
        $act_gc_amt = round(((float)$act_gc_prc * (float)$sold_weight),2);
    }
    else{
        $sql = "SELECT * FROM `broiler_gc_pc_decentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
        while($row = mysqli_fetch_assoc($query)){
            $i++;
            $prod_from_decs[$i] = round((float)$row['prod_from_dec']);
            $prod_to_decs[$i] = (float)$row['prod_to_dec'];
            $rate_decs[$i] = (float)$row['prod_rate_dec'] / 100;
            $counts[$i] = $i;
        }
        foreach($counts as $cn){
            if($fmr_act_prod_prc > $prod_from_decs[$cn]){
                $rates = (float)$fmr_act_prod_prc - (float)$prod_from_decs[$cn];
                $rate_dec[$cn] = (float)$rates * (float)$rate_decs[$cn];
            }
            else{
                $rate_dec[$cn] = 0;
            }
        }
        $prod_dec_rate = 0;
        foreach($counts as $cn){
            $prod_dec_rate = (float)$prod_dec_rate + (float)$rate_dec[$cn];
        }
        $act_gc_prc = - round($prod_dec_rate,3);
        $prod_dec_rate = round($prod_dec_rate,3);
        
        $actual_gc_per_kg = (float)$standard_cost - (float)$prod_dec_rate;
        if((float)$minimum_cost != 0 && $actual_gc_per_kg <= $minimum_cost){ $actual_gc_per_kg = (float)$minimum_cost; }
        $actual_gc_amount = round(((float)$actual_gc_per_kg * (float)$sold_weight));
        $act_gc_amt = round(((float)$act_gc_prc * (float)$sold_weight));
    }
    
    /*********** Sales Incentive ***********/
    if($grade != ""){ $grd_fltr = " AND `sales_inc_grade` = '$grade'"; } else{ $grd_fltr = ""; }
    $sql = "SELECT * FROM `broiler_gc_si_incentive` WHERE `std_code` = '$gc_code'".$grd_fltr; $query = mysqli_query($conn,$sql); $i = 0; $sales_from_incs = $sales_to_incs = $sales_rate_incs = array();
    while($row = mysqli_fetch_assoc($query)){
        $i++;
        $sales_from_incs[$i] = (float)$row['sales_from_inc'];
        $sales_to_incs[$i] = (float)$row['sales_to_inc'];
        $sales_rate_incs[$i] = (float)$row['sales_rate_inc'];
        $sales_max_rates = (float)$row['sales_max_rate'];
        $max_prod_cost = (float)$row['max_prod_cost'];
        $counts[$i] = $i;
    }
    $new_count = array(); $i = 0;
    foreach($counts as $cn){
        if($sold_rate >= $sales_from_incs[$cn]){
            $i++;
            if($sold_rate > $sales_to_incs[$cn]){
                $rates = (float)$sales_to_incs[$cn] - (float)$sales_from_incs[$cn];
                $rate_inc[$cn] = ((float)$rates * ((float)$sales_rate_incs[$cn] / 100));
            }
            else{
                $rates = (float)$sold_rate - (float)$sales_from_incs[$cn];
                $rate_inc[$cn] = ((float)$rates * ((float)$sales_rate_incs[$cn] / 100));
            }
            $new_count[$i] = $i;
        }
    }
    $sale_inc_prc = 0;
    foreach($new_count as $cn){ $sale_inc_prc = (float)$sale_inc_prc + (float)$rate_inc[$cn]; }
    
    if($fmr_act_prod_prc > $max_prod_cost){ $sale_inc_prc = 0; } else{ }
    if((float)$sales_max_rates != 0 && $sale_inc_prc >= $sales_max_rates){ $sale_inc_prc = $sales_max_rates; } else{$sale_inc_prc = round($sale_inc_prc,3); }
    
    $sale_inc_amount = round(((float)$sale_inc_prc * (float)$sold_weight));
    
    /*if($grade != ""){ $grd_fltr = " AND `sales_inc_grade` = '$grade'"; } else{ $grd_fltr = ""; }
    $sql = "SELECT * FROM `broiler_gc_si_incentive` WHERE `std_code` = '$gc_code'".$grd_fltr; $query = mysqli_query($conn,$sql); $i = 0;
    while($row = mysqli_fetch_assoc($query)){
        $i++;
        $sales_from_incs[$i] = (float)$row['sales_from_inc'];
        $sales_to_incs[$i] = (float)$row['sales_to_inc'];
        $sales_rate_incs[$i] = (float)$row['sales_rate_inc'];
        $sales_max_rates = (float)$row['sales_max_rate'];
        $max_prod_cost = (float)$row['max_prod_cost'];
        $counts[$i] = $i;
    }
    $new_count = array(); $i = 0;
    foreach($counts as $cn){
        if($sold_rate >= $sales_from_incs[$cn]){
            $i++;
            if($sold_rate > $sales_to_incs[$cn]){
                $rates = (float)$sales_to_incs[$cn] - (float)$sales_from_incs[$cn];
                $rate_inc[$cn] = (float)$rates * (float)$sales_rate_incs[$cn];
            }
            else{
                $rates = (float)$sold_rate - (float)$sales_from_incs[$cn];
                $rate_inc[$cn] = (float)$rates * (float)$sales_rate_incs[$cn];
            }
            $new_count[$i] = $i;
        }
    }
    $sale_inc_prc = 0;
    foreach($new_count as $cn){ $sale_inc_prc = (float)$sale_inc_prc + (float)$rate_inc[$cn]; }
    
    if($fmr_act_prod_prc > $max_prod_cost){ $sale_inc_prc = 0; } else{ }
    if((float)$sales_max_rates != 0 && $sale_inc_prc >= $sales_max_rates){ $sale_inc_prc = $sales_max_rates; } else{$sale_inc_prc = round($sale_inc_prc,3); }
    
    $sale_inc_amount = round(((float)$sale_inc_prc * (float)$sold_weight));
    */
    /*FCR Incentive Calculations*/
    $sql = "SELECT * FROM `broiler_gc_fcr_incentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0; $counts = array();
    while($row = mysqli_fetch_assoc($query)){
        $i++;
        $fcr_limit_inc[$i] = (float)$row['fcr_limit_inc'];
        $body_weight_inc[$i] = (float)$row['body_weight_inc'];
        $fcr_rate_inc[$i] = (float)$row['fcr_rate_inc'];
        $counts[$i] = $i;
    }
    $new_count = array(); $i = 0;
    foreach($counts as $cn){
        if($cfcr <= $fcr_limit_inc[$cn] && $avg_weight >= $body_weight_inc[$cn]){
            $i++;
            $rate_inc[$cn] = (float)$sold_weight * (float)$fcr_rate_inc[$cn];
            $new_count[$i] = $i;
        }
    }
    $fcr_inc_prc = $fcr_inc_amt = 0;
    foreach($new_count as $cn){
        $fcr_inc_amt = (float)$fcr_inc_amt + (float)$rate_inc[$cn];
    }
    if($fcr_inc_amt == "" || $fcr_inc_amt == "0" || $fcr_inc_amt == 0){ $fcr_inc_prc = $fcr_inc_amt = 0; }
    else{
        if($sold_weight > 0){
            $fcr_inc_prc = (float)$fcr_inc_amt / (float)$sold_weight;
        }
        else{
            $fcr_inc_prc = 0;
        }
        
    }
    /*********** Mortality Incentive ***********/
    $mort_inc_prc = $mort_inc_amount = 0;

    $sql = "SELECT * FROM `broiler_gc_mi_incentive` WHERE `std_code` = '$gc_code' AND `mort_from_inc` <= '$total_mort_per' AND `mort_to_inc` >= '$total_mort_per'"; $query = mysqli_query($conn,$sql); $minc_count = mysqli_num_rows($query);
    if($minc_count > 0){
        while($row = mysqli_fetch_assoc($query)){
            $mort_inc_prc = $row['mort_rate_inc'];
            $mort_inc_amount = round(($row['mort_rate_inc'] * $sold_birds),2);
        }
    }
    else{ }
    
    /* *****Summer Incentive***** */
    $sql = "SELECT * FROM `broiler_gc_smr_incentive` WHERE `std_code` = '$gc_code' AND `prod_cost_from` <= '$fmr_act_prod_prc' AND `prod_cost_to` >= '$fmr_act_prod_prc' AND `min_prod_cost` <= '$fmr_act_prod_prc' AND `max_prod_cost` >= '$fmr_act_prod_prc'";
    $query = mysqli_query($conn,$sql); $summer_inc_prc = $summer_inc_amount = $si_incentive_rate = 0; $si_incentive_on = "";
    while($row = mysqli_fetch_assoc($query)){
        $si_incentive_on = $row['incentive_on'];
        $summer_inc_prc = $row['incentive_rate'];

        if($si_incentive_on == "placed_birds"){
            $summer_inc_amount += ((float)$summer_inc_prc * (float)$placed_birds);
        }
        else if($si_incentive_on == "sold_birds"){
            $summer_inc_amount += ((float)$summer_inc_prc * (float)$sold_birds);
        }
        else if($si_incentive_on == "sold_weight"){
            $summer_inc_amount += ((float)$summer_inc_prc * (float)$sold_weight);
        }
        else{ }
    }

    $other_inc_amount = $ifft_charges = 0;

    $total_incentive =  round(((float)$actual_gc_amount + (float)$sale_inc_amount + (float)$mort_inc_amount + (float)$summer_inc_amount + (float)$other_inc_amount + (float)$ifft_charges + (float)$fcr_inc_amt));

    /*********** Shortage Calculation ***********/
    $sql = "SELECT * FROM `broiler_gc_st_decentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
    while($row = mysqli_fetch_assoc($query)){
        if($row['sprod_flag'] == 1){ $bird_shortage_amount = round((float)$standard_prod_cost * ((float)$shortage * (float)$avg_weight)); }
        else if($row['prod_flag'] == 1){ $bird_shortage_amount = round((float)$fmr_act_prod_prc * ((float)$shortage * (float)$avg_weight)); }
        else if($row['sale_flag'] == 1){ $bird_shortage_amount = round((float)$sold_rate * ((float)$shortage * (float)$avg_weight)); }
        else if($row['high_flag'] == 1){ $bird_shortage_amount = round(max((float)$standard_prod_cost,(float)$fmr_act_prod_prc,$sold_rate) * ((float)$shortage * (float)$avg_weight)); }
        else{ $bird_shortage_amount = 0; }
    }
    
    if($sold_weight > 0){ $bird_shortage_prc = (float)$bird_shortage_amount / (float)$sold_weight; } else { $bird_shortage_prc = 0; }

    $fcr_deduct_prc = $fcr_deduct_amount = $mort_deduct_prc = $mort_deduct_amount = $other_deduct_amount = 0;

    /*Mortality Deduction*/
    if((float)$total_mort_per > (float)$standard_mortality){
       $sql = "SELECT * FROM `broiler_gc_mi_decentive` WHERE `std_code` = '$gc_code' AND `weeks` = 'GDO'"; $query = mysqli_query($conn,$sql); $mdcount = mysqli_num_rows($query);
        if($mdcount > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['mort_to_dec'] < (float)$total_mort_per){
                    $amd1 =  (float)$total_mort_per - (float)$row['mort_to_dec'];
                    $mort_deduct_amount += (((((float)$amd1 * (float)$placed_birds) / 100) * $avg_weight) * $row['mort_rate_dec']);     
                }
            }
        }
    }
    $mort_deduct_amount = round($mort_deduct_amount,2);
    $total_decentives = round(((float)$bird_shortage_amount + (float)$fcr_deduct_amount + (float)$mort_deduct_amount + (float)$coal_damt));

    $amount_payable = round((float)$total_incentive - (float)$total_decentives,2);

    $total_amount_payable = round(((float)$total_incentive - (float)$total_decentives - (float)$farmer_sales_amt));

    //TDS Calculations
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Rearing Charges' AND `field_function` = 'TDS Based on Farmer Master' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn, $sql); $tds_fmflag = mysqli_num_rows($query);
    
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Farmer TDS' AND `field_function` LIKE 'Deduction' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $tds_flag = mysqli_num_rows($query);

    $tds_per = 0;
    if($tds_flag == 1 && $tds_fmflag == 1) {
        $sql = "SELECT * FROM `broiler_farmer` WHERE `code` = '$farmer_code'";
        $query = mysqli_query($conn, $sql); while($row = mysqli_fetch_assoc($query)) { $tds_per = $row['tds_per']; }
        if((float)$tds_per == 0 || $tds_per == ""){ $tds_lbl = 1; $tds_per = 0.01; }
        else{ $tds_lbl = round($tds_per,2); $tds_per = $tds_per / 100; }
        $tds_amount = round(((float)$total_amount_payable * (float)$tds_per));
    }
    else if($tds_flag == 1) { $tds_lbl = 1; $tds_per = 0.01; $tds_amount = round(((float)$total_amount_payable * (float)$tds_per)); }
    else{ $tds_lbl = 0; $tds_amount = $tds_per = 0; }

    $farmer_payable = round(((float)$total_amount_payable - (float)$tds_amount));
    if((float)$sold_birds != 0){ $per_bird_price = round(((float)$farmer_payable / (float)$sold_birds),2); } else{ $per_bird_price = 0; }
    
    if((float)$total_amount_payable <= 0){
        $tds_amount = $farmer_payable = $per_bird_price = 0;
    }
    $farm_value = 
    $placement_date
    /*1*/."@".$placed_birds
    /*2*/."@".$mortality
    /*3*/."@".$sold_birds
    /*4*/."@".$sold_weight
    /*5*/."@".$excess
    /*6*/."@".$shortage
    /*7*/."@".$liquid_date
    /*8*/."@".$sold_amount
    /*9*/."@".$sold_rate
    /*10*/."@".$age
    /*11*/."@".$days7_mort_per
    /*12*/."@".$days30_mort_per
    /*13*/."@".$days31_mort_per
    /*14*/."@".$total_mort_per
    /*15*/."@".$fcr
    /*16*/."@".$cfcr
    /*17*/."@".$avg_weight
    /*18*/."@".$mean_age
    /*19*/."@".$day_gain
    /*20*/."@".$eef
    /*21*/."@".$grade
    /*22*/."@".$total_stkin_feed_qty
    /*23*/."@".$total_feed_consumed_qty
    /*24*/."@".$total_feed_transout_qty
    /*25*/."@".$feed_balance
    /*26*/."@".$feed_in_bag
    /*27*/."@".$feed_consumed_bag
    /*28*/."@".$feed_out_bag
    /*29*/."@".$feed_balance_bag
    /*30*/."@".$total_stkin_medvac_qty
    /*31*/."@".$total_medvac_consumed_qty
    /*32*/."@".$total_medvac_transout_qty
    /*33*/."@".$medvac_balance
    /*34*/."@".$fmr_stkin_chick_amt
    /*35*/."@".$fmr_stkin_chick_prc
    /*36*/."@".$fmr_total_feed_consumed_amt
    /*37*/."@".$fmr_total_feed_consumed_prc
    /*38*/."@".$fmr_admin_amt
    /*39*/."@".$fmr_admin_prc
    /*40*/."@".$fmr_total_medvac_consumed_amt
    /*41*/."@".$fmr_total_medvac_consumed_prc
    /*42*/."@".$fmr_act_prod_amt
    /*43*/."@".$fmr_act_prod_prc
    /*44*/."@".$fmr_act_prod_prc
    /*45*/."@".$standard_cost
    /*46*/."@".$standard_amount
    /*47*/."@".$actual_gc_per_kg
    /*48*/."@".$actual_gc_amount
    /*49*/."@".$sale_inc_prc
    /*50*/."@".$sale_inc_amount
    /*51*/."@".$mort_inc_prc
    /*52*/."@".$mort_inc_amount
    /*53*/."@".$summer_inc_prc
    /*54*/."@".$summer_inc_amount
    /*55*/."@".$other_inc_amount
    /*56*/."@".$ifft_charges
    /*57*/."@".$total_incentive
    /*58*/."@".$bird_shortage_amount
    /*59*/."@".$fcr_deduct_amount
    /*60*/."@".$mort_deduct_amount
    /*61*/."@".$total_decentives
    /*62*/."@".$amount_payable
    /*63*/."@".$tds_amount
    /*64*/."@".$other_deduct_amount
    /*65*/."@".$farmer_payable
    /*66*/."@".$branch_name
    /*67*/."@".$line_name
    /*68*/."@".$batch_name
    /*69*/."@".$standard_prod_cost
    /*70*/."@".$farmer_sales_amt
    /*71*/."@".$total_amount_payable
    /*72*/."@".$fcr_inc_prc
    /*73*/."@".$fcr_inc_amt
    /*74*/."@".$per_bird_price
    /*75*/."@".$tds_per
    /*76*/."@".$tds_lbl
    /*77*/."@".$act_gc_prc
    /*78*/."@".$act_gc_amt
    
    /*79*/."@".$mgmt_stkin_chick_prc
    /*80*/."@".$mgmt_stkin_chick_amt
    /*81*/."@".$mgmt_total_feed_consumed_prc
    /*82*/."@".$mgmt_total_feed_consumed_amt
    /*83*/."@".$mgmt_total_medvac_consumed_prc
    /*84*/."@".$mgmt_total_medvac_consumed_amt
    /*85*/."@".$mgmt_admin_prc
    /*86*/."@".$mgmt_admin_amt
    /*87*/."@".$supervisor_name."(".$supervisor_code.")"
    /*88*/."@".$lifting_efficiency
    /*89*/."@".$maize_cons_qty
    /*90*/."@".$maize_cons_frmr_prc
    /*91*/."@".$maize_cons_frmr_amt
    /*92*/."@".$sale_start_date
    /*93*/."@".$sale_end_date
    /*94*/."@".$total_stkin_maize_qty
    /*95*/."@".$total_maize_transout_qty
    /*96*/."@".$maize_bal_qty
    /*97*/."@".$maize_cons_mgmt_amt
    /*98*/."@".$maize_cons_mgmt_prc
    /*99*/."@".$coal_damt
    ;
    echo $farm_value;
}