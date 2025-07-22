<?php
//breeder_import_save_dailyentry1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['dailyentry1'];

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `breeder_dayentry_consumed`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("egg_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `breeder_dayentry_consumed` ADD `egg_weight` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `remarks`"; mysqli_query($conn,$sql); }
if(in_array("wi_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `breeder_dayentry_consumed` ADD `wi_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Web Import Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Female Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $ffeed_2flag = mysqli_num_rows($query);
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_iac = $icat_cogsac = $icat_wpac = $icat_sac = $icat_srac = array();
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = array();
while($row = mysqli_fetch_assoc($query)){
    $icat_code[$row['code']] = $row['category'];
    if(strtolower($row['description']) == "female birds"){ $bfbird_code = $row['code']; }
    else if(strtolower($row['description']) == "male birds"){ $bmbird_code = $row['code']; }
}

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Breeder Bird-Mort & Culls' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $bmac_code = "";
while($row = mysqli_fetch_assoc($query)){ $bmac_code = $row['code']; }

$flocks = $date = $breed_wage = $breed_age = $fmort_qty = $fcull_qty = $fbody_weight = $ffeed_code1 = $ffeed_qty1 = $ffeed_code2 = $ffeed_qty2 = $mmort_qty = $mcull_qty = $mbody_weight = $mfeed_code1 = $mfeed_qty1 = $mfeed_code2 = $mfeed_qty2 = 
$ffeed_sqty1 = $ffeed_sprc1 = $ffeed_sqty2 = $ffeed_sprc2 = $mfeed_sqty1 = $mfeed_sprc1 = $mfeed_sqty2 = $mfeed_sprc2 = $egg_weight = $remarks = array();
$i = 0; foreach($_POST['flock_code'] as $flock_codes){ $flocks[$i] = $flock_codes; $i++; }
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['breed_wage'] as $breed_wages){ $breed_wage[$i] = $breed_wages; $i++; }
$i = 0; foreach($_POST['breed_age'] as $breed_ages){ $breed_age[$i] = $breed_ages; $i++; }
$i = 0; foreach($_POST['fmort_qty'] as $fmort_qtys){ $fmort_qty[$i] = $fmort_qtys; $i++; }
$i = 0; foreach($_POST['fcull_qty'] as $fcull_qtys){ $fcull_qty[$i] = $fcull_qtys; $i++; }
$i = 0; foreach($_POST['fbody_weight'] as $fbody_weights){ $fbody_weight[$i] = $fbody_weights; $i++; }
$i = 0; foreach($_POST['ffeed_code1'] as $ffeed_code1s){ $ffeed_code1[$i] = $ffeed_code1s; $i++; }
$i = 0; foreach($_POST['ffeed_qty1'] as $ffeed_qty1s){ $ffeed_qty1[$i] = $ffeed_qty1s; $i++; }
$i = 0; foreach($_POST['ffeed_code2'] as $ffeed_code2s){ $ffeed_code2[$i] = $ffeed_code2s; $i++; }
$i = 0; foreach($_POST['ffeed_qty2'] as $ffeed_qty2s){ $ffeed_qty2[$i] = $ffeed_qty2s; $i++; }
$i = 0; foreach($_POST['mmort_qty'] as $mmort_qtys){ $mmort_qty[$i] = $mmort_qtys; $i++; }
$i = 0; foreach($_POST['mcull_qty'] as $mcull_qtys){ $mcull_qty[$i] = $mcull_qtys; $i++; }
$i = 0; foreach($_POST['mbody_weight'] as $mbody_weights){ $mbody_weight[$i] = $mbody_weights; $i++; }
$i = 0; foreach($_POST['mfeed_code1'] as $mfeed_code1s){ $mfeed_code1[$i] = $mfeed_code1s; $i++; }
$i = 0; foreach($_POST['mfeed_qty1'] as $mfeed_qty1s){ $mfeed_qty1[$i] = $mfeed_qty1s; $i++; }
$i = 0; foreach($_POST['mfeed_code2'] as $mfeed_code2s){ $mfeed_code2[$i] = $mfeed_code2s; $i++; }
$i = 0; foreach($_POST['mfeed_qty2'] as $mfeed_qty2s){ $mfeed_qty2[$i] = $mfeed_qty2s; $i++; }
$i = 0; foreach($_POST['ffeed_sqty1'] as $ffeed_sqty1s){ $ffeed_sqty1[$i] = $ffeed_sqty1s; $i++; }
$i = 0; foreach($_POST['ffeed_sprc1'] as $ffeed_sprc1s){ $ffeed_sprc1[$i] = $ffeed_sprc1s; $i++; }
$i = 0; foreach($_POST['ffeed_sqty2'] as $ffeed_sqty2s){ $ffeed_sqty2[$i] = $ffeed_sqty2s; $i++; }
$i = 0; foreach($_POST['ffeed_sprc2'] as $ffeed_sprc2s){ $ffeed_sprc2[$i] = $ffeed_sprc2s; $i++; }
$i = 0; foreach($_POST['mfeed_sqty1'] as $mfeed_sqty1s){ $mfeed_sqty1[$i] = $mfeed_sqty1s; $i++; }
$i = 0; foreach($_POST['mfeed_sprc1'] as $mfeed_sprc1s){ $mfeed_sprc1[$i] = $mfeed_sprc1s; $i++; }
$i = 0; foreach($_POST['mfeed_sqty2'] as $mfeed_sqty2s){ $mfeed_sqty2[$i] = $mfeed_sqty2s; $i++; }
$i = 0; foreach($_POST['mfeed_sprc2'] as $mfeed_sprc2s){ $mfeed_sprc2[$i] = $mfeed_sprc2s; $i++; }
$i = 0; foreach($_POST['egg_weight'] as $egg_weights){ $egg_weight[$i] = $egg_weights; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$flag = $dflag = 0; $active = $wi_flag = 1;
$trtype = "dailyentry1";
$trlink = "breeder_display_dailyentry1.php";

$icat_alist = $begg_code = $begg_name = $tot_peggs = $beps_flag = array();
//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `begg_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; } $icat_list = implode("','", $icat_alist);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
while($row = mysqli_fetch_assoc($query)){ $begg_code[$row['code']] = $row['code']; $begg_name[$row['code']] = $row['description']; }
//Breeder Egg Production Details
$dsize = sizeof($date);
for($i = 0;$i < $dsize;$i++){
    foreach($begg_code as $beggs){
        $ikey = ""; $ikey = "egg_".$beggs;
        $quantity = 0; $quantity = $_POST[$ikey][$i]; if($quantity == ""){ $quantity = 0; }
        $tot_peggs[$i] += (float)$quantity;
    }
    if((float)$tot_peggs[$i] > 0){ $beps_flag[$i] = 1; } else{ $beps_flag[$i] = 0; }
}

$dsize = sizeof($date);
for($i = 0;$i < $dsize;$i++){
    if($breed_wage[$i] == ""){ $breed_wage[$i] = 0; }
    if($breed_age[$i] == ""){ $breed_age[$i] = 0; }
    if($fmort_qty[$i] == ""){ $fmort_qty[$i] = 0; }
    if($fcull_qty[$i] == ""){ $fcull_qty[$i] = 0; }
    if($fbody_weight[$i] == ""){ $fbody_weight[$i] = 0; }
    if($ffeed_code1[$i] == ""){ $ffeed_code1[$i] = 0; }
    if($ffeed_qty1[$i] == ""){ $ffeed_qty1[$i] = 0; }
    if($ffeed_code2[$i] == ""){ $ffeed_code2[$i] = 0; }
    if($ffeed_qty2[$i] == ""){ $ffeed_qty2[$i] = 0; }
    if($mmort_qty[$i] == ""){ $mmort_qty[$i] = 0; }
    if($mcull_qty[$i] == ""){ $mcull_qty[$i] = 0; }
    if($mbody_weight[$i] == ""){ $mbody_weight[$i] = 0; }
    if($mfeed_code1[$i] == ""){ $mfeed_code1[$i] = 0; }
    if($mfeed_qty1[$i] == ""){ $mfeed_qty1[$i] = 0; }
    if($mfeed_code2[$i] == ""){ $mfeed_code2[$i] = 0; }
    if($mfeed_qty2[$i] == ""){ $mfeed_qty2[$i] = 0; }
    if($ffeed_sqty1[$i] == ""){ $ffeed_sqty1[$i] = 0; }
    if($ffeed_sprc1[$i] == ""){ $ffeed_sprc1[$i] = 0; }
    if($ffeed_sqty2[$i] == ""){ $ffeed_sqty2[$i] = 0; }
    if($ffeed_sprc2[$i] == ""){ $ffeed_sprc2[$i] = 0; }
    if($mfeed_sqty1[$i] == ""){ $mfeed_sqty1[$i] = 0; }
    if($mfeed_sprc1[$i] == ""){ $mfeed_sprc1[$i] = 0; }
    if($mfeed_sqty2[$i] == ""){ $mfeed_sqty2[$i] = 0; }
    if($mfeed_sprc2[$i] == ""){ $mfeed_sprc2[$i] = 0; }
    if($egg_weight[$i] == ""){ $egg_weight[$i] = 0; }
    
    //Fetch Flock Details
    $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `description` LIKE '$flocks[$i]' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $farm_code = $unit_code = $shed_code = $batch_code = $location = $flock_code = "";
    while($row = mysqli_fetch_assoc($query)){
        $flock_code = $row['code'];
        $farm_code = $row['farm_code'];
        $unit_code = $row['unit_code'];
        $shed_code = $row['shed_code'];
        $batch_code = $row['batch_code'];

        //Get Filters based on Feed Stock for Summary
        if($bfeed_stkon == "FARM"){ $location = $row['farm_code']; }
        else if($bfeed_stkon == "UNIT"){ $location = $row['unit_code']; }
        else if($bfeed_stkon == "SHED"){ $location = $row['shed_code']; }
        else if($bfeed_stkon == "BATCH"){ $location = $row['batch_code']; }
        else if($bfeed_stkon == "FLOCK"){ $location = $row['code']; }
        else{ }
    }

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $fyear = "";
    $trno_dt1 = generate_transaction_details($date[$i],"dailyentry1","BDT","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
    
    //Feed Consumption in Bags
    if((int)$bfstk_bags == 1){
        //Female Feed-1
        $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$ffeed_code1[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
        if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $ffeed_qty1[$i] = $ffeed_qty1[$i] * $frow1['bag_size']; $ffeed_sprc1[$i] = $ffeed_sprc1[$i] / $frow1['bag_size']; } }
        //Female Feed-2
        $fsql2 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$ffeed_code2[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $fquery2 = mysqli_query($conn,$fsql2); $f_cnt2 = mysqli_num_rows($fquery2);
        if($f_cnt2 > 0){ while($frow2 = mysqli_fetch_assoc($fquery2)){ $ffeed_qty2[$i] = $ffeed_qty2[$i] * $frow2['bag_size']; $ffeed_sprc2[$i] = $ffeed_sprc2[$i] / $frow2['bag_size']; } }
        //Male Feed-1
        $msql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$mfeed_code1[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $mquery1 = mysqli_query($conn,$msql1); $m_cnt1 = mysqli_num_rows($mquery1);
        if($m_cnt1 > 0){ while($mrow1 = mysqli_fetch_assoc($mquery1)){ $mfeed_qty1[$i] = $mfeed_qty1[$i] * $mrow1['bag_size']; $mfeed_sprc1[$i] = $mfeed_sprc1[$i] / $mrow1['bag_size']; } }
        //Male Feed-2
        $msql2 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$mfeed_code2[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $mquery2 = mysqli_query($conn,$msql2); $m_cnt2 = mysqli_num_rows($mquery2);
        if($m_cnt2 > 0){ while($mrow2 = mysqli_fetch_assoc($mquery2)){ $mfeed_qty2[$i] = $mfeed_qty2[$i] * $mrow2['bag_size']; $mfeed_sprc2[$i] = $mfeed_sprc2[$i] / $mrow2['bag_size']; } }
    }

    $sql = "INSERT INTO `breeder_dayentry_consumed` (`incr`,`prefix`,`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`breed_wage`,`breed_age`,`fmort_qty`,`fcull_qty`,`fbody_weight`,`ffeed_code1`,`ffeed_qty1`,`ffeed_code2`,`ffeed_qty2`,`mmort_qty`,`mcull_qty`,`mbody_weight`,`mfeed_code1`,`mfeed_qty1`,`mfeed_code2`,`mfeed_qty2`,`egg_weight`,`remarks`,`flag`,`active`,`dflag`,`wi_flag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$trnum','$date[$i]','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$breed_wage[$i]','$breed_age[$i]','$fmort_qty[$i]','$fcull_qty[$i]','$fbody_weight[$i]','$ffeed_code1[$i]','$ffeed_qty1[$i]','$ffeed_code2[$i]','$ffeed_qty2[$i]','$mmort_qty[$i]','$mcull_qty[$i]','$mbody_weight[$i]','$mfeed_code1[$i]','$mfeed_qty1[$i]','$mfeed_code2[$i]','$mfeed_qty2[$i]','$egg_weight[$i]','$remarks[$i]','$flag','$active','$dflag','$wi_flag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else{
        //Female Bird Summary
        $coa_Cr = $icat_iac[$icat_code[$bfbird_code]]; $coa_Dr = $bmac_code;
        if((float)$fmort_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$bfbird_code','$fmort_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Bird Mortality','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bfbird_code','$fmort_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Bird Mortality','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
        if((float)$fcull_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$bfbird_code','$fcull_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Bird Culls','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bfbird_code','$fcull_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Bird Culls','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
        //Female Feed Summary
        if((float)$ffeed_qty1[$i] > 0){
            $coa_Cr = $icat_iac[$icat_code[$ffeed_code1[$i]]]; $coa_Dr = $icat_iac[$icat_code[$bfbird_code]];
            $amount = 0; $amount = (float)$ffeed_qty1[$i] * (float)$ffeed_sprc1[$i];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$ffeed_code1[$i]','$ffeed_qty1[$i]','$ffeed_sprc1[$i]','$amount','$location','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Feed-1 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                if((int)$beps_flag[$i] == 0){
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bfbird_code','0','$ffeed_sprc1[$i]','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Feed-1 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        if((float)$ffeed_2flag > 0 && (float)$ffeed_qty2[$i] > 0){
            $coa_Cr = $icat_iac[$icat_code[$ffeed_code2[$i]]]; $coa_Dr = $icat_iac[$icat_code[$bfbird_code]];
            $amount = 0; $amount = (float)$ffeed_qty2[$i] * (float)$ffeed_sprc2[$i];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$ffeed_code2[$i]','$ffeed_qty2[$i]','$ffeed_sprc2[$i]','$amount','$location','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Feed-2 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                if((int)$beps_flag[$i] == 0){
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bfbird_code','0','$ffeed_sprc2[$i]','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Female Feed-2 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        //Male Bird Summary
        $coa_Cr = $icat_iac[$icat_code[$bmbird_code]]; $coa_Dr = $bmac_code;
        if((float)$mmort_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$bmbird_code','$mmort_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Bird Mortality','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bmbird_code','$mmort_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Bird Mortality','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
        if((float)$mcull_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$bmbird_code','$mcull_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Bird Culls','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bmbird_code','$mcull_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Bird Culls','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
        //Male Feed Summary
        if((float)$mfeed_qty1[$i] > 0){
            $coa_Cr = $icat_iac[$icat_code[$mfeed_code1[$i]]]; $coa_Dr = $icat_iac[$icat_code[$bmbird_code]];
            $amount = 0; $amount = (float)$mfeed_qty1[$i] * (float)$mfeed_sprc1[$i];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$mfeed_code1[$i]','$mfeed_qty1[$i]','$mfeed_sprc1[$i]','$amount','$location','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Feed-1 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                if((int)$beps_flag[$i] == 0){
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bmbird_code','0','$mfeed_sprc1[$i]','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Feed-1 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        if((float)$mfeed_2flag > 0 && (float)$mfeed_qty2[$i] > 0){
            $coa_Cr = $icat_iac[$icat_code[$mfeed_code2[$i]]]; $coa_Dr = $icat_iac[$icat_code[$bmbird_code]];
            $amount = 0; $amount = (float)$mfeed_qty2[$i] * (float)$mfeed_sprc2[$i];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$mfeed_code2[$i]','$mfeed_qty2[$i]','$mfeed_sprc2[$i]','$amount','$location','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Feed-2 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                if((int)$beps_flag[$i] == 0){
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$bmbird_code','0','$mfeed_sprc2[$i]','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Male Feed-2 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }

        //Breeder Egg Production Details
        if((int)$beps_flag[$i] > 0){
            foreach($begg_code as $beggs){
                $ikey = ""; $ikey = "egg_".$beggs;
                $quantity = 0; $quantity = $_POST[$ikey][$i]; if($quantity == ""){ $quantity = 0; }
                $price = $amount = 0;
                if((float)$quantity > 0){
                    if((float)$tot_peggs[$i] > 0){
                        $tot_amt = 0;
                        $tot_amt = (((float)$ffeed_qty1[$i] * (float)$ffeed_sprc1[$i]) + ((float)$ffeed_qty2[$i] * (float)$ffeed_sprc2[$i]) + ((float)$mfeed_qty1[$i] * (float)$mfeed_sprc1[$i]) + ((float)$mfeed_qty2[$i] * (float)$mfeed_sprc2[$i]));
                        $price = (float)$tot_amt / (float)$tot_peggs[$i];
                        $amount = (float)$price * (float)$quantity;
                    }

                    $coa_Dr = $icat_iac[$icat_code[$beggs]];
                    $sql = "INSERT INTO `breeder_dayentry_produced` (`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`breed_wage`,`breed_age`,`item_code`,`quantity`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES('$trnum','$date[$i]','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$breed_wage[$i]','$breed_age[$i]','$beggs','$quantity','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
                    else{
                        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                        VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$beggs','$quantity','$price','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Egg Production','0','1','0','$addedemp','$addedtime','$addedtime')";
                        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                    }
                }
            }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like Import more Daily Entries?");
    if(x == true){
        window.location.href = "breeder_import_dailyentry1.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_dailyentry1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_dailyentry1.php?ccid="+a;
    }
</script>