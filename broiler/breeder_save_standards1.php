<?php
//breeder_save_standards1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['standards1'];

$breed_age = $livability = $ffeed_pbird = $mfeed_pbird = $hd_per = $he_per = $hhp_pweek = $chhp_pweek = $hhe_pweek = $chhe_pweek = $hatch_per = $chicks_pweek = 
$cchicks_pweek = $egg_weight = $fbird_bweight = $mbird_bweight = array();

$breed_code = $_POST['breed_code']; 

$i = 0; foreach($_POST['breed_age'] as $breed_ages){ $breed_age[$i] = $breed_ages; $i++; }
$i = 0; foreach($_POST['livability'] as $livabilitys){ $livability[$i] = $livabilitys; $i++; }
$i = 0; foreach($_POST['ffeed_pbird'] as $ffeed_pbirds){ $ffeed_pbird[$i] = $ffeed_pbirds; $i++; }
$i = 0; foreach($_POST['mfeed_pbird'] as $mfeed_pbirds){ $mfeed_pbird[$i] = $mfeed_pbirds; $i++; }
$i = 0; foreach($_POST['hd_per'] as $hd_pers){ $hd_per[$i] = $hd_pers; $i++; }
$i = 0; foreach($_POST['he_per'] as $he_pers){ $he_per[$i] = $he_pers; $i++; }
$i = 0; foreach($_POST['hhp_pweek'] as $hhp_pweeks){ $hhp_pweek[$i] = $hhp_pweeks; $i++; }
$i = 0; foreach($_POST['chhp_pweek'] as $chhp_pweeks){ $chhp_pweek[$i] = $chhp_pweeks; $i++; }
$i = 0; foreach($_POST['hhe_pweek'] as $hhe_pweeks){ $hhe_pweek[$i] = $hhe_pweeks; $i++; }
$i = 0; foreach($_POST['chhe_pweek'] as $chhe_pweeks){ $chhe_pweek[$i] = $chhe_pweeks; $i++; }
$i = 0; foreach($_POST['hatch_per'] as $hatch_pers){ $hatch_per[$i] = $hatch_pers; $i++; }
$i = 0; foreach($_POST['chicks_pweek'] as $chicks_pweeks){ $chicks_pweek[$i] = $chicks_pweeks; $i++; }
$i = 0; foreach($_POST['cchicks_pweek'] as $cchicks_pweeks){ $cchicks_pweek[$i] = $cchicks_pweeks; $i++; }
$i = 0; foreach($_POST['egg_weight'] as $egg_weights){ $egg_weight[$i] = $egg_weights; $i++; }
$i = 0; foreach($_POST['fbird_bweight'] as $fbird_bweights){ $fbird_bweight[$i] = $fbird_bweights; $i++; }
$i = 0; foreach($_POST['mbird_bweight'] as $mbird_bweights){ $mbird_bweight[$i] = $mbird_bweights; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "standards1";
$trlink = "breeder_display_standards1.php";

$dsize = sizeof($breed_age);
for($i = 0;$i < $dsize;$i++){

    if($breed_age[$i] == "") { $breed_age[$i] = 0; }
    if($livability[$i] == "") { $livability[$i] = 0; }
    if($ffeed_pbird[$i] == "") { $ffeed_pbird[$i] = 0; }
    if($mfeed_pbird[$i] == "") { $mfeed_pbird[$i] = 0; }
    if($hd_per[$i] == "") { $hd_per[$i] = 0; }
    if($he_per[$i] == "") { $he_per[$i] = 0; }
    if($hhp_pweek[$i] == "") { $hhp_pweek[$i] = 0; }
    if($chhp_pweek[$i] == "") { $chhp_pweek[$i] = 0; }
    if($hhe_pweek[$i] == "") { $hhe_pweek[$i] = 0; }
    if($chhe_pweek[$i] == "") { $chhe_pweek[$i] = 0; }
    if($hatch_per[$i] == "") { $hatch_per[$i] = 0; }
    if($chicks_pweek[$i] == "") { $chicks_pweek[$i] = 0; }
    if($cchicks_pweek[$i] == "") { $cchicks_pweek[$i] = 0; }
    if($egg_weight[$i] == "") { $egg_weight[$i] = 0; }
    if($fbird_bweight[$i] == "") { $fbird_bweight[$i] = 0; }
    if($mbird_bweight[$i] == "") { $mbird_bweight[$i] = 0; }

    $sql = "INSERT INTO `breeder_breed_standards` (`breed_code`,`breed_age`,`livability`,`ffeed_pbird`,`mfeed_pbird`,`hd_per`,`he_per`,`hhp_pweek`,`chhp_pweek`,`hhe_pweek`,`chhe_pweek`,`hatch_per`,`chicks_pweek`,`cchicks_pweek`,`egg_weight`,`fbird_bweight`,`mbird_bweight`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$breed_code','$breed_age[$i]','$livability[$i]','$ffeed_pbird[$i]','$mfeed_pbird[$i]','$hd_per[$i]','$he_per[$i]','$hhp_pweek[$i]','$chhp_pweek[$i]','$hhe_pweek[$i]','$chhe_pweek[$i]','$hatch_per[$i]','$chicks_pweek[$i]','$cchicks_pweek[$i]','$egg_weight[$i]','$fbird_bweight[$i]','$mbird_bweight[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Breed Standards?");
    if(x == true){
        window.location.href = "breeder_add_standards1.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_standards1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_standards1.php?ccid="+a;
    }
</script>