<?php
//layer_save_mvconsumed1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['mvconsumed1'];

$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }

$date = date("Y-m-d",strtotime($_POST['date']));
$flock_code = $item_code = $item_uom = $quantity = $avl_stk = $avg_prc = $remarks = array();
$i = 0; foreach($_POST['flock_code'] as $flock_codes){ $flock_code[$i] = $flock_codes; $i++; }
$i = 0; foreach($_POST['item_code'] as $item_codes){ $item_code[$i] = $item_codes; $i++; }
$i = 0; foreach($_POST['item_uom'] as $item_uoms){ $item_uom[$i] = $item_uoms; $i++; }
$i = 0; foreach($_POST['quantity'] as $quantitys){ $quantity[$i] = $quantitys; $i++; }
$i = 0; foreach($_POST['avl_stk'] as $avl_stks){ $avl_stk[$i] = $avl_stks; $i++; }
$i = 0; foreach($_POST['avg_prc'] as $avg_prcs){ $avg_prc[$i] = $avg_prcs; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$flag = $dflag = $mgmt_rate = $mgmt_amt = $frmr_rate = $frmr_amt = 0; $active = 1;
$trtype = "mvconsumed1";
$trlink = "layer_display_mvconsumed1.php";

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

$dsize = sizeof($flock_code);
for($i = 0;$i < $dsize;$i++){
    //Initialization
    if($quantity[$i] == ""){ $quantity[$i] = 0; }
    if($avl_stk[$i] == ""){ $avl_stk[$i] = 0; }
    if($avg_prc[$i] == ""){ $avg_prc[$i] = 0; }
    $mgmt_rate = (float)$avg_prc[$i];
    $mgmt_amt = round(((float)$quantity[$i] * (float)$avg_prc[$i]),2);
    $frmr_rate = (float)$avg_prc[$i];
    $frmr_amt = round(((float)$quantity[$i] * (float)$avg_prc[$i]),2);

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $fyear = "";
    $trno_dt1 = generate_transaction_details($date,"mvconsumed1","BMC","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    //Fetch Farm/Unit/Shed Details
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code[$i]' AND `active` = '1' AND `dflag` = '0'";
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
    
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code[$i]' AND `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0'";
    $query = mysqli_query($conn, $sql); $farm_code = $unit_code = $shed_code = "";
    while($row = mysqli_fetch_assoc($query)){ $farm_code = $row['farm_code']; $unit_code = $row['unit_code']; $shed_code = $row['shed_code']; $batch_code = $row['batch_code']; }

    $sql = "INSERT INTO `layer_medicine_consumed` (`incr`,`prefix`,`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`item_code`,`item_uom`,`quantity`,`mgmt_rate`,`mgmt_amt`,`frmr_rate`,`frmr_amt`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$trnum','$date','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code[$i]','$item_code[$i]','$item_uom[$i]','$quantity[$i]','$mgmt_rate','$mgmt_amt','$frmr_rate','$frmr_amt','$remarks[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else {
        $coa_Cr = $icat_iac[$icat_code[$item_code[$i]]];
        $coa_Dr = $icat_iac[$icat_code[$bfbird_code]];
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$item_code[$i]','$quantity[$i]','$mgmt_rate','$mgmt_amt','$location','$batch_code','$flock_code[$i]','$remarks[$i]','0','layer-MedVac Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$item_code[$i]','0','$mgmt_rate','$mgmt_amt','$location','$batch_code','$flock_code[$i]','$remarks[$i]','0','layer-MedVac Consumed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more MedVac Consumption?");
    if(x == true){
        window.location.href = "layer_add_mvconsumed1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_mvconsumed1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_mvconsumed1.php?ccid="+a;
    }
</script>