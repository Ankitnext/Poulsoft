<?php
//breeder_save_eggconvert2.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['eggconvert2'];

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
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Stock-Wastage' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $wst_coa = "";
while($row = mysqli_fetch_assoc($query)){ $wst_coa = $row['code']; }

$date = date("Y-m-d",strtotime($_POST['date']));

$from_loc = $from_item = $to_item = $to_qty = $disposed_qty = $remarks = $stk_qty = $stk_prc = array();
$i = 0; foreach($_POST['from_loc'] as $from_locs){ $from_loc[$i] = $from_locs; $i++; }
$i = 0; foreach($_POST['from_item'] as $from_items){ $from_item[$i] = $from_items; $i++; }
$i = 0; foreach($_POST['to_item'] as $to_items){ $to_item[$i] = $to_items; $i++; }
$i = 0; foreach($_POST['to_qty'] as $to_qtys){ $to_qty[$i] = $to_qtys; $i++; }
$i = 0; foreach($_POST['disposed_qty'] as $disposed_qtys){ $disposed_qty[$i] = $disposed_qtys; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$i = 0; foreach($_POST['stk_qty'] as $stk_qtys){ $stk_qty[$i] = $stk_qtys; $i++; }
$i = 0; foreach($_POST['stk_prc'] as $stk_prcs){ $stk_prc[$i] = $stk_prcs; $i++; }

$tot_cqty = $_POST['tot_cqty'];
$tot_dqty = $_POST['tot_dqty'];

$flag = $dflag = 0; $active = 1;
$trtype = "eggconvert2";
$trlink = "breeder_display_eggconvert2.php";

$dsize = sizeof($from_item);
for($i = 0;$i < $dsize;$i++){
    if($to_qty[$i] == ""){ $to_qty[$i] = 0; }
    if($disposed_qty[$i] == ""){ $disposed_qty[$i] = 0; }
    if($stk_qty[$i] == ""){ $stk_qty[$i] = 0; }
    if($stk_prc[$i] == ""){ $stk_prc[$i] = 0; }

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $fyear = "";
    $trno_dt1 = generate_transaction_details($date,"eggconvert2","BEC","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    //Fetch FARM/UNIT/SHED/Batch Details
    $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `code` = '$from_loc[$i]' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $f_cnt = mysqli_num_rows($query); $farm_code = $unit_code = $shed_code = $batch_code = $flock_code = "";
    if((int)$f_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $farm_code = $row['farm_code']; $unit_code = $row['unit_code']; $locations = $shed_code = $row['shed_code']; $batch_code = $row['batch_code']; $flock_code = $row['code']; } }
    else{ $farm_code = $locations = $from_loc[$i]; }
    
    //From Stock Details
    $from_qty = ((float)$to_qty[$i] + (float)$disposed_qty[$i]);
    $from_prc = (float)$stk_prc[$i];
    $from_amt = round(((float)$from_qty * (float)$from_prc),2);

    //To Stock Details
    $to_prc = 0; if((float)$to_qty[$i] != 0){ $to_prc = round(((float)$from_amt / (float)$to_qty[$i]),5); }
    $to_amt = round(((float)$to_qty[$i] * (float)$to_prc),2);

    $sql = "INSERT INTO `breeder_egg_conversion` (`incr`,`prefix`,`trnum`,`date`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`flock_code`,`from_item`,`from_qty`,`from_prc`,`from_amt`,`to_item`,`to_qty`,`to_prc`,`to_amt`,`disposed_qty`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$trnum','$date','$farm_code','$unit_code','$shed_code','$batch_code','$flock_code','$from_item[$i]','$from_qty','$from_prc','$from_amt','$to_item[$i]','$to_qty[$i]','$to_prc','$to_amt','$disposed_qty[$i]','$remarks[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else{
        $coa_Cr = $icat_iac[$icat_code[$from_item[$i]]];
        $coa_Dr = $icat_iac[$icat_code[$to_item[$i]]];
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date','$trnum','$from_item[$i]','$from_qty','$from_prc','$from_amt','$locations','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Egg Conversion From','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('DR','$coa_Dr','$date','$trnum','$to_item[$i]','$to_qty[$i]','$to_prc','$to_amt','$locations','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Egg Conversion To','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }

        if((float)$disposed_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('DR','$wst_coa','$date','$trnum','$from_item[$i]','$disposed_qty[$i]','0','0','$locations','$batch_code','$flock_code','$remarks[$i]','0','Breeder-Egg Disposed','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Bird Transfers?");
    if(x == true){
        window.location.href = "breeder_add_eggconvert2.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_eggconvert2.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_eggconvert2.php?ccid="+a;
    }
</script>