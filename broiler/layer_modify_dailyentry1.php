<?php
//layer_modify_dailyentry1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['dailyentry1'];

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `layer_dayentry_consumed` WHERE `trnum` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `layer_dayentry_consumed` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    $sql3 = "DELETE FROM `layer_dayentry_produced` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    $sql3 = "DELETE FROM `account_summary` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $lstk_bags = mysqli_num_rows($query);
$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For layer Birds' AND `user_access` = 'all' AND `flag` = '1'";
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
$feed_sqty1 = $feed_sprc1 = $feed_sqty2 = $feed_sprc2 = $egg_weight = $remarks = "";

$date = date("Y-m-d",strtotime($_POST['date']));
$breed_wage = $_POST['breed_wage']; if($breed_wage == ""){ $breed_wage = 0; }
$breed_age = $_POST['breed_age']; if($breed_age == ""){ $breed_age = 0; }

$fmort_qty = $_POST['mort_qty']; if($mort_qty == ""){ $mort_qty = 0; }
$fcull_qty = $_POST['cull_qty']; if($cull_qty == ""){ $cull_qty = 0; }
$fbody_weight = $_POST['body_weight']; if($body_weight == ""){ $body_weight = 0; }
$feed_code1 = $_POST['feed_code1']; if($feed_code1 == ""){ $feed_code1 = 0; }
$feed_qty1 = $_POST['feed_qty1']; if($feed_qty1 == ""){ $feed_qty1 = 0; }
$feed_code2 = $_POST['feed_code2']; if($feed_code2 == ""){ $feed_code2 = 0; }
$feed_qty2 = $_POST['feed_qty2']; if($feed_qty2 == ""){ $feed_qty2 = 0; }

$feed_sqty1 = $_POST['feed_sqty1']; if($feed_sqty1 == ""){ $feed_sqty1 = 0; }
$feed_sprc1 = $_POST['feed_sprc1']; if($feed_sprc1 == ""){ $feed_sprc1 = 0; }
$feed_sqty2 = $_POST['feed_sqty2']; if($feed_sqty2 == ""){ $feed_sqty2 = 0; }
$feed_sprc2 = $_POST['feed_sprc2']; if($feed_sprc2 == ""){ $feed_sprc2 = 0; }

$egg_weight = $_POST['egg_weight']; if($egg_weight == ""){ $egg_weight = 0; }
$remarks = $_POST['remarks'];

$flag = $dflag = 0; $active = 1;
$trtype = "dailyentry1";
$trlink = "layer_display_dailyentry1.php";

$icat_alist = $begg_code = $begg_name = array(); $tot_peggs = 0;
if((int)$beps_flag == 1){
    //layer Egg Details
    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `begg_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; } $icat_list = implode("','", $icat_alist);
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
    while($row = mysqli_fetch_assoc($query)){ $begg_code[$row['code']] = $row['code']; $begg_name[$row['code']] = $row['description']; }
    //layer Egg Production Details
    foreach($begg_code as $beggs){
        $ikey = ""; $ikey = "egg_".$beggs;
        $quantity = 0; $quantity = $_POST[$ikey]; if($quantity == ""){ $quantity = 0; }
        $tot_peggs += (float)$quantity;
    }
}

//Feed Consumption in Bags
if((int)$lstk_bags == 1){
    //Female Feed-1
    $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$feed_code1' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
    $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
    if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $feed_qty1 = $feed_qty1 * $frow1['bag_size']; $feed_sprc1 = $feed_sprc1 / $frow1['bag_size']; } }
    //Female Feed-2
    $fsql2 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$feed_code2' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
    $fquery2 = mysqli_query($conn,$fsql2); $f_cnt2 = mysqli_num_rows($fquery2);
    if($f_cnt2 > 0){ while($frow2 = mysqli_fetch_assoc($fquery2)){ $feed_qty2 = $feed_qty2 * $frow2['bag_size']; $feed_sprc2 = $feed_sprc2 / $frow2['bag_size']; } }
    //Male Feed-1
    $msql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$mfeed_code1' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
    $mquery1 = mysqli_query($conn,$msql1); $m_cnt1 = mysqli_num_rows($mquery1);
    if($m_cnt1 > 0){ while($mrow1 = mysqli_fetch_assoc($mquery1)){ $mfeed_qty1 = $mfeed_qty1 * $mrow1['bag_size']; $mfeed_sprc1 = $mfeed_sprc1 / $mrow1['bag_size']; } }
    //Male Feed-2
    $msql2 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$mfeed_code2' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
    $mquery2 = mysqli_query($conn,$msql2); $m_cnt2 = mysqli_num_rows($mquery2);
    if($m_cnt2 > 0){ while($mrow2 = mysqli_fetch_assoc($mquery2)){ $mfeed_qty2 = $mfeed_qty2 * $mrow2['bag_size']; $mfeed_sprc2 = $mfeed_sprc2 / $mrow2['bag_size']; } }
}

$sql = "INSERT INTO `layer_dayentry_consumed` (`incr`,`prefix`,`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`breed_wage`,`breed_age`,`mort_qty`,`cull_qty`,`body_weight`,`feed_code1`,`feed_qty1`,`feed_code2`,`feed_qty2`,`egg_weight`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
VALUES('$incr','$prefix','$trnum','$date','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$breed_wage','$breed_age','$mort_qty','$cull_qty','$body_weight','$feed_code1','$feed_qty1','$feed_code2','$feed_qty2','$egg_weight','$remarks','$flag','$active','$dflag','$trtype','$trlink','$aemp','$atime','$addedemp','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
else{
    //Female Bird Summary
    $coa_Cr = $icat_iac[$icat_code[$bfbird_code]]; $coa_Dr = $bmac_code;
    if((float)$fmort_qty > 0){
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$bfbird_code','$fmort_qty','0','0','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Female Bird Mortality','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$bfbird_code','$fmort_qty','0','0','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Female Bird Mortality','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
    if((float)$fcull_qty > 0){
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$bfbird_code','$fcull_qty','0','0','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Female Bird Culls','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$bfbird_code','$fcull_qty','0','0','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Female Bird Culls','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
    //Female Feed Summary
    if((float)$feed_qty1 > 0){
        $coa_Cr = $icat_iac[$icat_code[$feed_code1]]; $coa_Dr = $icat_iac[$icat_code[$bfbird_code]];
        $amount = 0; $amount = (float)$feed_qty1 * (float)$feed_sprc1;
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$feed_code1','$feed_qty1','$feed_sprc1','$amount','$location','$batch_code','$flock_code','$remarks','0','layer-Female Feed-1 Consumed','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            if((int)$beps_flag == 0){
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date','$trnum','$lbird_code','0','$feed_sprc1','$amount','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Female Feed-1 Consumed','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
    }
    if((float)$feed_2flag > 0 && (float)$feed_qty2 > 0){
        $coa_Cr = $icat_iac[$icat_code[$feed_code2]]; $coa_Dr = $icat_iac[$icat_code[$bfbird_code]];
        $amount = 0; $amount = (float)$feed_qty2 * (float)$feed_sprc2;
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$feed_code2','$feed_qty2','$feed_sprc2','$amount','$location','$batch_code','$flock_code','$remarks','0','layer-Female Feed-2 Consumed','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            if((int)$beps_flag == 0){
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
                VALUES ('DR','$coa_Dr','$date','$trnum','$lbird_code','0','$feed_sprc2','$amount','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Female Feed-2 Consumed','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
    }
   
    //layer Egg Production Details
    if((int)$beps_flag == 1){
        foreach($begg_code as $beggs){
            $ikey = ""; $ikey = "egg_".$beggs;
            $quantity = 0; $quantity = $_POST[$ikey]; if($quantity == ""){ $quantity = 0; }
            $price = $amount = 0;
            if((float)$quantity > 0){
                if((float)$tot_peggs > 0){
                    $tot_amt = 0;
                    $tot_amt = (((float)$feed_qty1 * (float)$feed_sprc1) + ((float)$feed_qty2 * (float)$feed_sprc2) + ((float)$mfeed_qty1 * (float)$mfeed_sprc1) + ((float)$mfeed_qty2 * (float)$mfeed_sprc2));
                    $price = (float)$tot_amt / (float)$tot_peggs;
                    $amount = (float)$price * (float)$quantity;
                }

                $coa_Dr = $icat_iac[$icat_code[$beggs]];
                $sql = "INSERT INTO `layer_dayentry_produced` (`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`breed_wage`,`breed_age`,`item_code`,`quantity`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
                VALUES('$trnum','$date','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$breed_wage','$breed_age','$beggs','$quantity','$flag','$active','$dflag','$trtype','$trlink','$aemp','$atime','$addedemp','$addedtime')";
                if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
                else{
                    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
                    VALUES ('DR','$coa_Dr','$date','$trnum','$beggs','$quantity','$price','$amount','$shed_code','$batch_code','$flock_code','$remarks','0','layer-Egg Production','0','1','0','$aemp','$atime','$addedemp','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
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