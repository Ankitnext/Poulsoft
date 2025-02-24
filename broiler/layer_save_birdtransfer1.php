<?php
//layer_save_birdtransfer1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['birdtransfer1'];

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
    if($row['description'] == "birds"){ $lyr_bcode = $row['code']; }
}

$date = date("Y-m-d",strtotime($_POST['date']));
$from_flock = $_POST['from_flock'];
$bird_age = $_POST['bird_age'];
$lyr_qty = $_POST['lyr_qty'];
$lyr_bprc = $_POST['lyr_bprc'];

$to_flock = $lyr_bqty = $remarks = array();
$i = 0; foreach($_POST['to_flock'] as $to_flocks){ $to_flock[$i] = $to_flocks; $i++; }
$i = 0; foreach($_POST['lyr_bqty'] as $lyr_bqtys){ $lyr_bqty[$i] = $lyr_bqtys; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$tot_qty = $_POST['tot_qty'];

$flag = $dflag = $cls_flag = 0; $active = 1;
$trtype = "birdtransfer1";
$trlink = "layer_display_birdtransfer1.php";

$dsize = sizeof($to_flock);
for($i = 0;$i < $dsize;$i++){
    if($bird_age == ""){ $bird_age = 0; }
    if($lyr_qty == ""){ $lyr_qty = 0; }
    if($lyr_bprc == ""){ $lyr_bprc = 0; }
    if($lyr_bqty[$i] == ""){ $lyr_bqty[$i] = 0; }

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $fyear = "";
    $trno_dt1 = generate_transaction_details($date,"birdtransfer1","BBT","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    //Fetch FARM/UNIT/SHED/Batch Details
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$from_flock' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $from_farm = $from_unit = $from_shed = $from_batch = "";
    while($row = mysqli_fetch_assoc($query)){ $from_farm = $row['farm_code']; $from_unit = $row['unit_code']; $from_shed = $row['shed_code']; $from_batch = $row['batch_code']; }
    
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$to_flock[$i]' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $to_farm = $to_unit = $to_shed = $to_batch = "";
    while($row = mysqli_fetch_assoc($query)){ $to_farm = $row['farm_code']; $to_unit = $row['unit_code']; $to_shed = $row['shed_code']; $to_batch = $row['batch_code']; }
    
    $lyr_bamt = round(((float)$lyr_bqty[$i] * (float)$lyr_bprc),2);
    $sql = "INSERT INTO `layer_bird_transfer` (`incr`,`prefix`,`trnum`,`date`,`from_farm`,`from_unit`,`from_shed`,`from_batch`,`from_flock`,`to_farm`,`to_unit`,`to_shed`,`to_batch`,`to_flock`,`bird_age`,`lyr_bcode`,`lyr_bqty`,`lyr_bprc`,`lyr_bamt`,`remarks`,`flag`,`active`,`dflag`,`cls_flag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$trnum','$date','$from_farm','$from_unit','$from_shed','$from_batch','$from_flock','$to_farm','$to_unit','$to_shed','$to_batch','$to_flock[$i]','$bird_age','$lyr_bcode','$lyr_bqty[$i]','$lyr_bprc','$lyr_bamt','$remarks[$i]','$flag','$active','$dflag','$cls_flag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else{
        if((float)$lyr_bqty[$i] > 0){
            $coa_Cr = $coa_Dr = $icat_iac[$icat_code[$lyr_bcode]];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('CR','$coa_Cr','$date','$trnum','$lyr_bcode','$lyr_bqty[$i]','$lyr_bprc','$lyr_bamt','$from_shed','$from_batch','$from_flock','$remarks[$i]','0','layer-lyr Bird Transfer Out','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$lyr_bcode','$lyr_bqty[$i]','$lyr_bprc','$lyr_bamt','$to_shed','$to_batch','$to_flock[$i]','$remarks[$i]','0','layer-lyr Bird Transfer In','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Bird Transfers?");
    if(x == true){
        window.location.href = "layer_add_birdtransfer1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_birdtransfer1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_birdtransfer1.php?ccid="+a;
    }
</script>