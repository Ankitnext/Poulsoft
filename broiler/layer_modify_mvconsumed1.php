<?php
//layer_modify_mvconsumed1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['mvconsumed1'];

$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `layer_medicine_consumed` WHERE `trnum` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `account_summary` WHERE `trnum` = '$ids' AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

$date = date("Y-m-d",strtotime($_POST['date']));
$flock_code = $_POST['flock_code'];
$item_code = $_POST['item_code'];
$item_uom = $_POST['item_uom'];
$quantity = $_POST['quantity']; if($quantity == ""){ $quantity = 0; }
$remarks = $_POST['remarks'];
$avl_stk = $_POST['avl_stk']; if($avl_stk == ""){ $avl_stk = 0; }
$avg_prc = $_POST['avg_prc']; if($avg_prc == ""){ $avg_prc = 0; }

$mgmt_rate = (float)$avg_prc;
$mgmt_amt = round(((float)$quantity * (float)$avg_prc),2);
$frmr_rate = (float)$avg_prc;
$frmr_amt = round(((float)$quantity * (float)$avg_prc),2);

//Item CoA Accounts
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
    if($row['description'] == "Female birds"){ $bfbird_code = $row['code']; }
    else if($row['description'] == "Male birds"){ $bmbird_code = $row['code']; }
}

//Fetch Farm/Unit/Shed Details
$sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code' AND `active` = '1' AND `dflag` = '0'";
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

$sql = "UPDATE `layer_medicine_consumed` SET `date` = '$date',`farm_code` = '$farm_code',`unit_code` = '$unit_code',`shed_code` = '$shed_code',`batch_code` = '$batch_code',`flock_code` = '$flock_code',`item_code` = '$item_code',`item_uom` = '$item_uom',`quantity` = '$quantity',`mgmt_rate` = '$mgmt_rate',`mgmt_amt` = '$mgmt_amt',`frmr_rate` = '$frmr_rate',`frmr_amt` = '$frmr_amt',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
else {
    $sql3 = "DELETE FROM `account_summary` WHERE `trnum` = '$trnum' AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); }
    else{
        $coa_Cr = $icat_iac[$icat_code[$item_code]];
        $coa_Dr = $icat_iac[$icat_code[$bfbird_code]];
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$item_code','$quantity','$mgmt_rate','$mgmt_amt','$location','$batch_code','$flock_code','$remarks','0','layer-MedVac Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$item_code','0','$mgmt_rate','$mgmt_amt','$location','$batch_code','$flock_code','$remarks','0','layer-MedVac Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_mvconsumed1.php?ccid="+a;
</script>