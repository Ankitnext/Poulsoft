<?php
//layer_save_dailyentry1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['dailyentry1'];

$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $lstk_bags = mysqli_num_rows($query);
$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $feed_2flag = mysqli_num_rows($query);
$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
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
    if($row['description'] == "birds"){ $lbird_code = $row['code']; }
}

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'layer Bird-Mort & Culls' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $bmac_code = "";
while($row = mysqli_fetch_assoc($query)){ $bmac_code = $row['code']; }

$shed_code = $_POST['shed_code'];
$flock_code = $_POST['flock_code'];
$beps_flag = $_POST['beps_flag'];

$shed_fltr = ""; if($shed_code != ""){ $shed_fltr = " AND `shed_code` = '$shed_code'"; }
$sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code'".$shed_fltr." AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $farm_code = $unit_code = $shed_code = $batch_code = $location = "";
while($row = mysqli_fetch_assoc($query)){
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

$date = $breed_wage = $breed_age = $fmort_qty = $fcull_qty = $fbody_weight = $feed_code1 = $feed_qty1 = $feed_code2 = $feed_qty2 =  
$feed_sqty1 = $feed_sprc1 = $feed_sqty2 = $feed_sprc2 = $egg_weight = $remarks = array();
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['breed_wage'] as $breed_wages){ $breed_wage[$i] = $breed_wages; $i++; }
$i = 0; foreach($_POST['breed_age'] as $breed_ages){ $breed_age[$i] = $breed_ages; $i++; }

$i = 0; foreach($_POST['mort_qty'] as $mort_qtys){ $mort_qty[$i] = $mort_qtys; $i++; }
$i = 0; foreach($_POST['cull_qty'] as $cull_qtys){ $cull_qty[$i] = $cull_qtys; $i++; }
$i = 0; foreach($_POST['body_weight'] as $body_weights){ $body_weight[$i] = $body_weights; $i++; }
$i = 0; foreach($_POST['feed_code1'] as $feed_code1s){ $feed_code1[$i] = $feed_code1s; $i++; }
$i = 0; foreach($_POST['feed_qty1'] as $feed_qty1s){ $feed_qty1[$i] = $feed_qty1s; $i++; }
$i = 0; foreach($_POST['feed_code2'] as $feed_code2s){ $feed_code2[$i] = $feed_code2s; $i++; }
$i = 0; foreach($_POST['feed_qty2'] as $feed_qty2s){ $feed_qty2[$i] = $feed_qty2s; $i++; }

$i = 0; foreach($_POST['feed_sqty1'] as $feed_sqty1s){ $feed_sqty1[$i] = $feed_sqty1s; $i++; }
$i = 0; foreach($_POST['feed_sprc1'] as $feed_sprc1s){ $feed_sprc1[$i] = $feed_sprc1s; $i++; }
$i = 0; foreach($_POST['feed_sqty2'] as $feed_sqty2s){ $feed_sqty2[$i] = $feed_sqty2s; $i++; }
$i = 0; foreach($_POST['feed_sprc2'] as $feed_sprc2s){ $feed_sprc2[$i] = $feed_sprc2s; $i++; }

$i = 0; foreach($_POST['egg_weight'] as $egg_weights){ $egg_weight[$i] = $egg_weights; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "dailyentry1";
$trlink = "layer_display_dailyentry1.php";

$icat_alist = $begg_code = $begg_name = $tot_peggs = array();
if((int)$beps_flag == 1){
    //layer Egg Details
    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `begg_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; } $icat_list = implode("','", $icat_alist);
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
    while($row = mysqli_fetch_assoc($query)){ $begg_code[$row['code']] = $row['code']; $begg_name[$row['code']] = $row['description']; }
    //layer Egg Production Details
    $dsize = sizeof($date);
    for($i = 0;$i < $dsize;$i++){
        foreach($begg_code as $beggs){
            $ikey = ""; $ikey = "egg_".$beggs;
            $quantity = 0; $quantity = $_POST[$ikey][$i]; if($quantity == ""){ $quantity = 0; }
            $tot_peggs[$i] += (float)$quantity;
        }
    }
}

$dsize = sizeof($date);
for($i = 0;$i < $dsize;$i++){
    if($breed_wage[$i] == ""){ $breed_wage[$i] = 0; }
    if($breed_age[$i] == ""){ $breed_age[$i] = 0; }
    if($mort_qty[$i] == ""){ $mort_qty[$i] = 0; }
    if($cull_qty[$i] == ""){ $cull_qty[$i] = 0; }
    if($body_weight[$i] == ""){ $body_weight[$i] = 0; }
    if($feed_code1[$i] == ""){ $feed_code1[$i] = 0; }
    if($feed_qty1[$i] == ""){ $feed_qty1[$i] = 0; }
    if($feed_code2[$i] == ""){ $feed_code2[$i] = 0; }
    if($feed_qty2[$i] == ""){ $feed_qty2[$i] = 0; }

    if($feed_sqty1[$i] == ""){ $feed_sqty1[$i] = 0; }
    if($feed_sprc1[$i] == ""){ $feed_sprc1[$i] = 0; }
    if($feed_sqty2[$i] == ""){ $feed_sqty2[$i] = 0; }
    if($feed_sprc2[$i] == ""){ $feed_sprc2[$i] = 0; }

    if($egg_weight[$i] == ""){ $egg_weight[$i] = 0; }

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $fyear = "";
    $trno_dt1 = generate_transaction_details($date[$i],"dailyentry1","BDT","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
    
    //Feed Consumption in Bags
    if((int)$lstk_bags == 1){
        //layer Feed-1
        $sql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$feed_code1[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $query1 = mysqli_query($conn,$sql1); $cnt1 = mysqli_num_rows($query1);
        if($f_cnt1 > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $feed_qty1[$i] = $feed_qty1[$i] * $row1['bag_size']; $feed_sprc1[$i] = $feed_sprc1[$i] / $row1['bag_size']; } }
        //layer Feed-2
        $sql2 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$feed_code2[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $query2 = mysqli_query($conn,$sql2); $cnt2 = mysqli_num_rows($query2);
        if($cnt2 > 0){ while($frow2 = mysqli_fetch_assoc($fquery2)){ $feed_qty2[$i] = $feed_qty2[$i] * $row2['bag_size']; $feed_sprc2[$i] = $feed_sprc2[$i] / $row2['bag_size']; } }
        }

    $sql = "INSERT INTO `layer_dayentry_consumed` (`incr`,`prefix`,`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`breed_wage`,`breed_age`,`mort_qty`,`cull_qty`,`body_weight`,`feed_code1`,`feed_qty1`,`feed_code2`,`feed_qty2`,`egg_weight`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$trnum','$date[$i]','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$breed_wage[$i]','$breed_age[$i]','$mort_qty[$i]','$cull_qty[$i]','$body_weight[$i]','$feed_code1[$i]','$feed_qty1[$i]','$feed_code2[$i]','$feed_qty2[$i]','$egg_weight[$i]','$remarks[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else{
        //layer Bird Summary
        $coa_Cr = $icat_iac[$icat_code[$lbird_code]]; $coa_Dr = $bmac_code;
        if((float)$mort_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$lbird_code','$mort_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer Bird Mortality','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$lbird_code','$mort_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer Bird Mortality','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
        if((float)$cull_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$lbird_code','$cull_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer Bird Culls','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$lbird_code','$cull_qty[$i]','0','0','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer Bird Culls','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
        //layer Feed Summary
        if((float)$feed_qty1[$i] > 0){
            $coa_Cr = $icat_iac[$icat_code[$feed_code1[$i]]]; $coa_Dr = $icat_iac[$icat_code[$lbird_code]];
            $amount = 0; $amount = (float)$feed_qty1[$i] * (float)$feed_sprc1[$i];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$feed_code1[$i]','$feed_qty1[$i]','$feed_sprc1[$i]','$amount','$location','$batch_code','$flock_code','$remarks[$i]','0','layer Feed-1 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                if((int)$beps_flag == 0){
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$lbird_code','0','$feed_sprc1[$i]','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer Feed-1 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        if((float)$feed_2flag > 0 && (float)$feed_qty2[$i] > 0){
            $coa_Cr = $icat_iac[$icat_code[$feed_code2[$i]]]; $coa_Dr = $icat_iac[$icat_code[$lbird_code]];
            $amount = 0; $amount = (float)$feed_qty2[$i] * (float)$feed_sprc2[$i];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$feed_code2[$i]','$feed_qty2[$i]','$feed_sprc2[$i]','$amount','$location','$batch_code','$flock_code','$remarks[$i]','0','layer Feed-2 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                if((int)$beps_flag == 0){
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$lbird_code','0','$feed_sprc2[$i]','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer Feed-2 Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
        }
        //layer Egg Production Details
        if((int)$beps_flag == 1){
            foreach($begg_code as $beggs){
                $ikey = ""; $ikey = "egg_".$beggs;
                $quantity = 0; $quantity = $_POST[$ikey][$i]; if($quantity == ""){ $quantity = 0; }
                $price = $amount = 0;
                if((float)$quantity > 0){
                    if((float)$tot_peggs[$i] > 0){
                        $tot_amt = 0;
                        $tot_amt = (((float)$feed_qty1[$i] * (float)$feed_sprc1[$i]) + ((float)$feed_qty2[$i] * (float)$feed_sprc2[$i]));
                        $price = (float)$tot_amt / (float)$tot_peggs[$i];
                        $amount = (float)$price * (float)$quantity;
                    }

                    $coa_Dr = $icat_iac[$icat_code[$beggs]];
                    $sql = "INSERT INTO `layer_dayentry_produced` (`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`breed_wage`,`breed_age`,`item_code`,`quantity`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
                    VALUES('$trnum','$date[$i]','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$breed_wage[$i]','$breed_age[$i]','$beggs','$quantity','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
                    else{
                        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                        VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$beggs','$quantity','$price','$amount','$shed_code','$batch_code','$flock_code','$remarks[$i]','0','layer-Egg Production','0','1','0','$addedemp','$addedtime','$addedtime')";
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
    var x = confirm("Would you like add more Daily Entries?");
    if(x == true){
        window.location.href = "layer_add_dailyentry1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_dailyentry1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_dailyentry1.php?ccid="+a;
    }
</script>