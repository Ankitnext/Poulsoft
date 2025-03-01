<?php
//layer_fetch_avlstock_quantity.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$itype = $_GET['itype'];
$ftype = $_GET['ftype'];

$ttype = $_GET['ttype']; $trnum = $_GET['trnum'];
if($ttype == "edit" & $trnum != ""){ $trno_fltr = " AND `trnum` NOT IN ('$trnum')"; } else { $trno_fltr = ""; }

$shed_code = $_GET['shed_code'];
$shed_fltr = ""; if($shed_code != ""){ $shed_fltr = " AND `shed_code` = '$shed_code'"; }
$flock_code = $_GET['flock_code'];
$item_code = $_GET['item_code'];
$date = date("Y-m-d",strtotime($_GET['date']));
$today = date("Y-m-d");
$rows = $_GET['rows'];

if($itype == "feed" || $ftype == "brd_mventry"){
    //Feed Stock in Bags Flag
    $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);

    //Fetch Feed Stock on FARM/UNIT/SHED/BATCH
    $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }

    //Get Filters based on Feed Stock for Summary
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code'".$shed_fltr." AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $fdstk_fltr = "";
    while($row = mysqli_fetch_assoc($query)){
        if($bfeed_stkon == "FARM"){ $farm_code = $row['farm_code']; $fdstk_fltr = " AND `location` IN ('$farm_code')"; }
        else if($bfeed_stkon == "UNIT"){ $unit_code = $row['unit_code']; $fdstk_fltr = " AND `location` IN ('$unit_code')"; }
        else if($bfeed_stkon == "SHED"){ $shed_code = $row['shed_code']; $fdstk_fltr = " AND `location` IN ('$shed_code')"; }
        else if($bfeed_stkon == "BATCH"){ $batch_code = $row['batch_code']; $fdstk_fltr = " AND `location` IN ('$batch_code')"; }
        else if($bfeed_stkon == "FLOCK"){ $flock_code = $row['code']; $fdstk_fltr = " AND `flock_code` IN ('$flock_code')"; }
        else{ }
    }
    if($fdstk_fltr == ""){ $fdstk_fltr = " AND `flock_code` IN ('$flock_code')"; }

    //layer Feed Details
    $sql = "SELECT * FROM `item_details` WHERE `code` LIKE '$item_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = "";
    while($row = mysqli_fetch_assoc($query)){ $icat_code = $row['category']; }

    $sql = "SELECT * FROM `item_category` WHERE `code` = '$icat_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_iac = "";
    while($row = mysqli_fetch_assoc($query)){ $icat_iac = $row['iac']; }

    //Calculate and check stocks
    $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$today' AND `coa_code` IN ('$icat_iac') AND `item_code` IN ('$item_code')".$fdstk_fltr."".$trno_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
    $query = mysqli_query($conn,$sql); $stk_qty = $stk_prc = $stk_amt = $stk_oqty = $stk_oprc = $stk_oamt = $stk_cqty = $stk_cprc = $stk_camt = 0; $stk_adate = "";
    while($row = mysqli_fetch_array($query)){
        if($row['crdr'] == "CR"){
            $stk_qty = (float)$stk_qty - (float)$row['quantity'];
            $stk_amt = (float)$stk_amt - ((float)$stk_prc * (float)$row['quantity']);
            if((float)$stk_qty == 0){ $stk_amt = $stk_prc = 0; }
        }
        else if($row['crdr'] == "DR"){
            $stk_qty = (float)$stk_qty + (float)$row['quantity'];
            $stk_amt = (float)$stk_amt + (float)$row['amount'];
            if((float)$stk_qty != 0){ $stk_prc = (float)$stk_amt / (float)$stk_qty; }
        }
        else{ }
        if(strtotime($row['date']) <= strtotime($date)){
            $stk_oqty = $stk_qty;
            $stk_oprc = $stk_prc;
            $stk_oamt = $stk_amt;
        }
        else{
            $stk_cqty = $stk_qty;
            $stk_cprc = $stk_prc;
            $stk_camt = $stk_amt;
        }
        if((float)$stk_qty == 0){ $stk_amt = $stk_prc = 0; }
        if($stk_adate == "" || strtotime($stk_adate) <= strtotime($row['date'])){ $stk_adate = $row['date']; }
    }
    //Check for available stock and fetch Item list
    $stk_aqty = $stk_aprc = $stk_aamt = $err_flag = 0; $err_msg = "";
    if(strtotime($date) == strtotime($today)){
        $stk_aqty = $stk_oqty;
        $stk_aamt = $stk_oamt;
    }
    else{
        if(strtotime($stk_adate) <= strtotime($date)){
            $stk_aqty = $stk_oqty;
            $stk_aamt = $stk_oamt;
        }
        else{
            if((float)$stk_cqty < (float)$stk_oqty){ $stk_aqty = $stk_cqty; } else{ $stk_aqty = $stk_oqty; }
            if((float)$stk_camt < (float)$stk_oamt){ $stk_aamt = $stk_camt; } else{ $stk_aamt = $stk_oamt; }
        }
    }
    if((float)$stk_aqty != 0){ $stk_aprc = round(((float)$stk_aamt / (float)$stk_aqty),5); }

    //Check and calculate Bags
    if((int)$bfstk_bags == 1){
        $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$item_code' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
        if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $stk_aqty = round(($stk_aqty / $frow1['bag_size']),5); $stk_aprc = round(($stk_aprc * $frow1['bag_size']),5); } }
    }
    if((float)$stk_aqty <= 0){
        $err_flag = 1;
        $err_msg = "layer Item Stock not available. Please check and try again.";
    }
    echo $err_flag."[@$&]".$err_msg."[@$&]".$rows."[@$&]".$stk_aqty."[@$&]".$stk_aprc;
}
if($ftype == "stk_transfer"){
    $fsector = $_GET['fsector'];

    //Feed Stock in Bags Flag
    $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Stock Transfer' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);

    //layer Feed Details
    $sql = "SELECT * FROM `item_details` WHERE `code` LIKE '$item_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = "";
    while($row = mysqli_fetch_assoc($query)){ $icat_code = $row['category']; }

    $sql = "SELECT * FROM `item_category` WHERE `code` = '$icat_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_iac = "";
    while($row = mysqli_fetch_assoc($query)){ $icat_iac = $row['iac']; }

    //Calculate and check stocks
    $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$today' AND `coa_code` IN ('$icat_iac') AND `item_code` IN ('$item_code') AND `location` = '$fsector'".$trno_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
    $query = mysqli_query($conn,$sql); $stk_qty = $stk_prc = $stk_amt = $stk_oqty = $stk_oprc = $stk_oamt = $stk_cqty = $stk_cprc = $stk_camt = 0; $stk_adate = "";
    while($row = mysqli_fetch_array($query)){
        if($row['crdr'] == "CR"){
            $stk_qty = (float)$stk_qty - (float)$row['quantity'];
            $stk_amt = (float)$stk_amt - ((float)$stk_prc * (float)$row['quantity']);
            if((float)$stk_qty == 0){ $stk_amt = $stk_prc = 0; }
        }
        else if($row['crdr'] == "DR"){
            $stk_qty = (float)$stk_qty + (float)$row['quantity'];
            $stk_amt = (float)$stk_amt + (float)$row['amount'];
            if((float)$stk_qty != 0){ $stk_prc = (float)$stk_amt / (float)$stk_qty; }
        }
        else{ }
        if(strtotime($row['date']) <= strtotime($date)){
            $stk_oqty = $stk_qty;
            $stk_oprc = $stk_prc;
            $stk_oamt = $stk_amt;
        }
        else{
            $stk_cqty = $stk_qty;
            $stk_cprc = $stk_prc;
            $stk_camt = $stk_amt;
        }
        if((float)$stk_qty == 0){ $stk_amt = $stk_prc = 0; }
        if($stk_adate == "" || strtotime($stk_adate) <= strtotime($row['date'])){ $stk_adate = $row['date']; }
    }
    //Check for available stock and fetch Item list
    $stk_aqty = $stk_aprc = $stk_aamt = $err_flag = 0; $err_msg = "";
    if(strtotime($date) == strtotime($today)){
        $stk_aqty = $stk_oqty;
        $stk_aamt = $stk_oamt;
    }
    else{
        if(strtotime($stk_adate) <= strtotime($date)){
            $stk_aqty = $stk_oqty;
            $stk_aamt = $stk_oamt;
        }
        else{
            if((float)$stk_cqty < (float)$stk_oqty){ $stk_aqty = $stk_cqty; } else{ $stk_aqty = $stk_oqty; }
            if((float)$stk_camt < (float)$stk_oamt){ $stk_aamt = $stk_camt; } else{ $stk_aamt = $stk_oamt; }
        }
    }
    if((float)$stk_aqty != 0){ $stk_aprc = round(((float)$stk_aamt / (float)$stk_aqty),5); }

    //Check and calculate Bags
    if((int)$bfstk_bags == 1){
        $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$item_code' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
        if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $stk_aqty = round(($stk_aqty / $frow1['bag_size']),5); $stk_aprc = round(($stk_aprc * $frow1['bag_size']),5); } }
    }
    if((float)$stk_aqty <= 0){
        $err_flag = 1;
        $err_msg = "layer Item Stock not available. Please check and try again.";
    }
    echo $err_flag."[@$&]".$err_msg."[@$&]".$rows."[@$&]".$stk_aqty."[@$&]".$stk_aprc;
}
if($ftype == "bird_transfer"){
    $date = date("Y-m-d",strtotime($_GET['date']));
    $flock = $_GET['flock'];
    //layer Feed Details
    $sql = "SELECT * FROM `item_details` WHERE `description` IN ('Layer Birds') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $lbird_code = ""; $icat_code = array();
    while($row = mysqli_fetch_assoc($query)){
        if($row['description'] == "layer birds"){ $lbird_code = $row['code']; }
        else{ }
        $icat_code[$row['category']] = $row['category'];
    }
    $icat_list = implode("','", $icat_code);
    $sql = "SELECT * FROM `item_category` WHERE `code` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_iac = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['iac']] = $row['iac']; }
    $icat_coa = implode("','", $icat_iac);

    //Calculate and check stocks
    $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$today' AND `coa_code` IN ('$icat_coa') AND `item_code` IN ('$lbird_code') AND `flock_code` = '$flock'".$trno_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
    $query = mysqli_query($conn,$sql); $stk_qty = $stk_prc = $stk_amt = $stk_oqty = $stk_oprc = $stk_oamt = $stk_cqty = $stk_cprc = $stk_camt = $stk_adate = $item_alist = array(); $fsdate = "";
    while($row = mysqli_fetch_array($query)){
        $key1 = $row['item_code'];
        if($row['crdr'] == "CR"){
            $stk_qty[$key1] = (float)$stk_qty[$key1] - (float)$row['quantity'];
            $stk_amt[$key1] = (float)$stk_amt[$key1] - ((float)$stk_prc[$key1] * (float)$row['quantity']);
            if((float)$stk_qty[$key1] == 0){ $stk_amt[$key1] = $stk_prc[$key1] = 0; }
        }
        else if($row['crdr'] == "DR"){
            $stk_qty[$key1] = (float)$stk_qty[$key1] + (float)$row['quantity'];
            $stk_amt[$key1] = (float)$stk_amt[$key1] + (float)$row['amount'];
            if((float)$stk_qty[$key1] != 0){ $stk_prc[$key1] = (float)$stk_amt[$key1] / (float)$stk_qty[$key1]; }
            //echo "<br/>if((float)$stk_qty[$key1] != 0){ $stk_prc[$key1] = (float)$stk_amt[$key1] / (float)$stk_qty[$key1]; }";
        }
        else{ }
        if(strtotime($row['date']) <= strtotime($date)){
            $stk_oqty[$key1] = $stk_qty[$key1];
            $stk_oprc[$key1] = $stk_prc[$key1];
            $stk_oamt[$key1] = $stk_amt[$key1];
        }
        else{
            $stk_cqty[$key1] = $stk_qty[$key1];
            $stk_cprc[$key1] = $stk_prc[$key1];
            $stk_camt[$key1] = $stk_amt[$key1];
        }
        if((float)$stk_qty[$key1] == 0){ $stk_amt[$key1] = $stk_prc[$key1] = 0; }
        if($stk_adate[$key1] == "" || strtotime($stk_adate[$key1]) <= strtotime($row['date'])){ $stk_adate[$key1] = $row['date']; }
        $item_alist[$key1] = $key1;

        if($key1 == $lbird_code){
            if($fsdate == "" || strtotime($fsdate) >= strtotime($row["date"])){ $fsdate = $row['date']; }
        }
    }
    //Check for available stock and fetch Item list
    $stk_aqty = $stk_aprc = $stk_aamt = $err_flag = $lbird_sqty = $lbird_sprc = 0; $err_msg = "";
    foreach($item_alist as $key1){
        $stk_aqty = $stk_aprc = $stk_aamt = 0;
        if(strtotime($date) == strtotime($today)){
            $stk_aqty = $stk_oqty[$key1]; $stk_aamt = $stk_oamt[$key1];
        }
        else{
            if(strtotime($stk_adate[$key1]) <= strtotime($date)){
                $stk_aqty = $stk_oqty[$key1]; $stk_aamt = $stk_oamt[$key1];
            }
            else{
                if((float)$stk_cqty[$key1] < (float)$stk_oqty[$key1]){ $stk_aqty = $stk_cqty[$key1]; $stk_aamt = $stk_camt[$key1]; }
                else{ $stk_aqty = $stk_oqty[$key1]; $stk_aamt = $stk_oamt; }
            }
        }
        if((float)$stk_aqty != 0){ $stk_aprc = round(((float)$stk_aamt / (float)$stk_aqty),5); }

        if($key1 == $lbird_code){ $lbird_sqty = (float)$stk_aqty; $lbird_sprc = (float)$stk_aprc; }
        else{ }
    }
    //check and calculate Flock Age
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $sdate = ""; $s_cnt = mysqli_num_rows($query); $sage = 0;
    if((int)$s_cnt > 0){
        while($row = mysqli_fetch_array($query)){
            $sdate = date("d.m.Y",strtotime($row['start_date']));
            $sage = $row['start_age'];
        }
    }
    $bird_age = 0;
    if($sdate == "" && $fsdate == ""){
        $err_flag = 1; $err_msg = "Flock Start Date not available. Please check once.";
    }
    else if($sdate != "" && strtotime($date) < strtotime($sdate)){
        $err_flag = 1; $err_msg = "Date is less than Flock Start Date. Please check once.";
    }
    else if($sdate != ""){
        $bird_age = (INT)((strtotime($date) - strtotime($sdate)) / 60 / 60 / 24) + (int)$sage;
    }
    else if($fsdate != "" && strtotime($date) < strtotime($fsdate)){
        $err_flag = 1; $err_msg = "Date is less than Flock Start Date. Please check once.";
    }
    else if($fsdate != ""){
        $bird_age = (INT)((strtotime($date) - strtotime($fsdate)) / 60 / 60 / 24) + (int)$sage;
    }
    else{ }
    echo $err_flag."[@$&]".$err_msg."[@$&]".$rows."[@$&]".$lbird_sqty."[@$&]".$lbird_sprc."[@$&]".$bird_age;
}
?>
