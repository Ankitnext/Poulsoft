<?php
//layer_modify_shedallocate1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['shedallocate1'];

$ids = $_POST['idvalue']; $opn_trnum = "";
$sql = "SELECT * FROM `layer_shed_allocation` WHERE `id` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $opn_trnum = $row['opn_trnum']; 
}
if($opn_trnum != "" ){
    $sql3 = "DELETE FROM `account_summary` WHERE `trnum` IN ('$opn_trnum') AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_pvac[$row['code']] = $row['pvac'];
    $icat_pdac[$row['code']] = $row['pdac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $icat_code[$row['code']] = $row['category'];
    if($row['description'] == "Female birds" || $row['description'] == "Male birds"){ $lbird_code = $row['code']; }
}

$farm_code = $_POST['farm_code'];
$unit_code = $_POST['unit_code'];
$shed_code = $_POST['shed_code'];
$batch_code = $_POST['batch_code'];
$description = $_POST['description'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$start_age = $_POST['start_age']; if($start_age == ""){ $start_age = 0; }
$age_weeks = $_POST['age_weeks']; if($age_weeks == ""){ $age_weeks = 0; }
$opn_birds = $_POST['opn_birds']; if($opn_birds == ""){ $opn_birds = 0; }
$opn_rate = $_POST['opn_rate']; if($opn_rate == ""){ $opn_rate = 0; }

$sql = "SELECT * FROM `layer_shed_allocation` WHERE `id` = '$ids' AND `dflag` = '0'";
$query = mysqli_query($conn, $sql); $code = "";
while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }

$sql = "UPDATE `layer_shed_allocation` SET `description` = '$description',`farm_code` = '$farm_code',`unit_code` = '$unit_code',`shed_code` = '$shed_code',`batch_code` = '$batch_code',`start_date` = '$start_date',`start_age` = '$start_age',`age_weeks` = '$age_weeks',`opn_birds` = '$opn_birds',`opn_rate` = '$opn_rate',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$ids'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
else{
    if((float)$opn_birds > 0){
        if($opn_trnum != ""){
            $trnum = $opn_trnum;
        }
        else{
            //Generate Transaction No.
            $incr = 0; $prefix = $trnum = $fyear = "";
            $trno_dt1 = generate_transaction_details($start_date,"shedallocate1","LBO","generate",$_SESSION['dbase']);
            $trno_dt2 = explode("@",$trno_dt1);
            $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
        }
        $coa_Dr = $icat_iac[$icat_code[$bfbird_code]]; $amount = 0; $amount = round(((float)$opn_fbirds * (float)$opn_frate),5);
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('DR','$coa_Dr','$start_date','$trnum','$lbird_code','$opn_birds','$opn_rate','$amount','$shed_code','$batch_code','$code','0','layer-Female Opening Birds','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); }
        else{
            $from_post = "UPDATE `layer_shed_allocation` SET `opn_trnum` = '$trnum' WHERE `id` = '$ids' AND `dflag` = '0'";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_shedallocate1.php?ccid="+a;
</script>