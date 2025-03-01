<?php
//layer_fetch_avlstock_items.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$itype = $_GET['itype'];
$ftype = $_GET['ftype'];

$ttype = $_GET['ttype']; $trnum = $_GET['trnum'];
if($ttype == "edit" && $trnum != ""){ $trno_fltr = " AND `trnum` NOT IN ('$trnum')"; } else { $trno_fltr = ""; }

$shed_code = $_GET['shed_code'];
$shed_fltr = ""; if($shed_code != ""){ $shed_fltr = " AND `shed_code` = '$shed_code'"; }
$flock_code = $_GET['flock_code'];
$date = date("Y-m-d",strtotime($_GET['date']));
$today = date("Y-m-d");
$rows = $_GET['rows'];

if($itype == "feed"){
    //Feed Stock in Bags Flag
    $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);

    //Fetch Feed Stock on FARM/UNIT/SHED/BATCH
    $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $lfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $lfeed_stkon = $row['field_value']; } if($lfeed_stkon == ""){ $lfeed_stkon = "FLOCK"; }

    //Get Filters based on Feed Stock for Summary
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code'".$shed_fltr." AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $fdstk_fltr = "";
    while($row = mysqli_fetch_assoc($query)){
        if($lfeed_stkon == "FARM"){ $farm_code = $row['farm_code']; $fdstk_fltr = " AND `location` IN ('$farm_code')"; }
        else if($lfeed_stkon == "UNIT"){ $unit_code = $row['unit_code']; $fdstk_fltr = " AND `location` IN ('$unit_code')"; }
        else if($lfeed_stkon == "SHED"){ $shed_code = $row['shed_code']; $fdstk_fltr = " AND `location` IN ('$shed_code')"; }
        else if($lfeed_stkon == "BATCH"){ $batch_code = $row['batch_code']; $fdstk_fltr = " AND `location` IN ('$batch_code')"; }
        else if($lfeed_stkon == "FLOCK"){ $flock_code = $row['code']; $fdstk_fltr = " AND `flock_code` IN ('$flock_code')"; }
        else{ }
    }
    if($fdstk_fltr == ""){ $fdstk_fltr = " AND `flock_code` IN ('$flock_code')"; }

    //layer Feed Details
    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND (`lfeed_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_alist = $icat_iac = $il_flag = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; $icat_iac[$row['iac']] = $row['iac']; $il_flag[$row['code']] = $row['lfeed_flag']; }
    $icat_list = implode("','", $icat_alist);
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $lfeed_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $lfeed_alist[$row['code']] = $row['code']; }

    //Calculate and check stocks
    $icoa_list = implode("','", $icat_iac); $item_list = implode("','", $lfeed_alist);
    $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$today' AND `coa_code` IN ('$icoa_list') AND `item_code` IN ('$item_list')".$fdstk_fltr."".$trno_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
    $query = mysqli_query($conn,$sql); $stk_qty = $stk_prc = $stk_amt = $stk_oqty = $stk_oprc = $stk_oamt = $stk_cqty = $stk_cprc = $stk_camt = $stk_adate = array();
    while($row = mysqli_fetch_array($query)){
        $icode = $row['item_code'];
        if($row['crdr'] == "CR"){
            $stk_qty[$icode] = $stk_qty[$icode] - $row['quantity'];
            $stk_amt[$icode] = $stk_amt[$icode] - ($stk_prc[$icode] * $row['quantity']);
        }
        else if($row['crdr'] == "DR"){
            $stk_qty[$icode] = $stk_qty[$icode] + $row['quantity'];
            $stk_amt[$icode] = $stk_amt[$icode] + $row['amount'];
            if((float)$stk_qty[$icode] != 0){ $stk_prc[$icode] = $stk_amt[$icode] / $stk_qty[$icode]; }
        }
        else{ }
        if(strtotime($row['date']) <= strtotime($date)){
            $stk_oqty[$icode] = $stk_qty[$icode];
            $stk_oprc[$icode] = $stk_prc[$icode];
            $stk_oamt[$icode] = $stk_amt[$icode];
        }
        else{
            $stk_cqty[$icode] = $stk_qty[$icode];
            $stk_cprc[$icode] = $stk_prc[$icode];
            $stk_camt[$icode] = $stk_amt[$icode];
        }
        if(empty($stk_adate[$icode]) || $stk_adate[$icode] == "" || strtotime($stk_adate[$icode]) < strtotime($row['date'])){ $stk_adate[$icode] = $row['date']; }
    }
    //Check for available stock and fetch Item list
    $avl_ilist = array();
    if(strtotime($date) == strtotime($today)){
        foreach($lfeed_alist as $icode){
            if(!empty($stk_oqty[$icode]) && (float)$stk_oqty[$icode] > 0){ $avl_ilist[$icode] = $icode; }
        }
    }
    else{
        foreach($lfeed_alist as $icode){
            if(!empty($stk_adate[$icode]) && strtotime($stk_adate[$icode]) <= strtotime($date)){
                if(!empty($stk_oqty[$icode]) && (float)$stk_oqty[$icode] > 0){ $avl_ilist[$icode] = $icode; }
            }
            else{
                if(!empty($stk_cqty[$icode]) && (float)$stk_cqty[$icode] > 0 && !empty($stk_oqty[$icode]) && (float)$stk_oqty[$icode] > 0){ $avl_ilist[$icode] = $icode; }
            }
        }
    }

    $lfeed_opt = $err_msg = ""; $err_flag = 0;
    $lfeed_opt = '<option value="select">-select-</option>';
    if(sizeof($avl_ilist) > 0){
        $item_list = implode("','", $avl_ilist);
        $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$item_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $code = $row['code']; $name = $row['description'];
            $il_flag[$row['code']] = $row['lfeed_flag'];
            if($ibf_flag[$row['category']] == 1){ $lfeed_opt .= '<option value="'.$code.'">'.$name.'</option>'; }
            
        }
    }
    else{
        $err_flag = 1; $err_msg = "Feed Stock not available for selected Flock.,\nPlease check and try again.";
    }
    echo $err_flag."[@$&]".$err_msg."[@$&]".$rows."[@$&]".$lfeed_opt."[@$&]";
}
?>
