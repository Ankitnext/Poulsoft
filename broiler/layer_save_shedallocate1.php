<?php
//layer_save_shedallocate1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['shedallocate1'];

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

$flag = $dflag = 0; $active = 1;
$trtype = "shedallocate1";
$trlink = "layer_display_shedallocate1.php";

$sql = "SELECT MAX(id) as incr FROM `layer_shed_allocation`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } if($incr == ""){ $incr = 0; }
$prefix = "BFLK";
$incr++; if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;

$sql = "INSERT INTO `layer_shed_allocation` (`incr`,`prefix`,`code`,`description`,`farm_code`,`unit_code`,`shed_code`,`batch_code`,`start_date`,`start_age`,`age_weeks`,`opn_birds`,`opn_rate`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
VALUES('$incr','$prefix','$code','$description','$farm_code','$unit_code','$shed_code','$batch_code','$start_date','$start_age','$age_weeks','$opn_birds','$opn_rate','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
else{
    if((float)$opn_birds > 0){
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $fyear = "";
        $trno_dt1 = generate_transaction_details($start_date,"shedallocate1","LBO","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
        
        $coa_Dr = $icat_iac[$icat_code[$lbird_code]]; $amount = 0; $amount = round(((float)$opn_fbirds * (float)$opn_frate),5);
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`batch`,`flock_code`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('DR','$coa_Dr','$start_date','$trnum','$lbird_code','$opn_birds','$opn_rate','$amount','$shed_code','$batch_code','$code','0','layer Opening Birds','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); }
        else{
            $from_post = "UPDATE `layer_shed_allocation` SET `opn_trnum` = '$trnum' WHERE `code` = '$code' AND `dflag` = '0'";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Flocks?");
    if(x == true){
        window.location.href = "layer_add_shedallocate1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_shedallocate1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_shedallocate1.php?ccid="+a;
    }
</script>