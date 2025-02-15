<?php
//breeder_modify_standards1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['standards1'];

$breed_code = $_POST['breed_code']; if($breed_code == "") { $breed_code = 0; }
$breed_age = $_POST['breed_age']; if($breed_age == "") { $breed_age = 0; }
$livability = $_POST['livability']; if($livability == "") { $livability = 0; }
$ffeed_pbird = $_POST['ffeed_pbird']; if($ffeed_pbird == "") { $ffeed_pbird = 0; }
$mfeed_pbird = $_POST['mfeed_pbird']; if($mfeed_pbird == "") { $mfeed_pbird = 0; }
$hd_per = $_POST['hd_per']; if($hd_per == "") { $hd_per = 0; }
$he_per = $_POST['he_per']; if($he_per == "") { $he_per = 0; }
$hhp_pweek = $_POST['hhp_pweek']; if($hhp_pweek == "") { $hhp_pweek = 0; }
$chhp_pweek = $_POST['chhp_pweek']; if($chhp_pweek == "") { $chhp_pweek = 0; }
$hhe_pweek = $_POST['hhe_pweek']; if($hhe_pweek == "") { $hhe_pweek = 0; }
$chhe_pweek = $_POST['chhe_pweek']; if($chhe_pweek == "") { $chhe_pweek = 0; }
$hatch_per = $_POST['hatch_per']; if($hatch_per == "") { $hatch_per = 0; }
$chicks_pweek = $_POST['chicks_pweek']; if($chicks_pweek == "") { $chicks_pweek = 0; }
$cchicks_pweek = $_POST['cchicks_pweek']; if($cchicks_pweek == "") { $cchicks_pweek = 0; }
$egg_weight = $_POST['egg_weight']; if($egg_weight == "") { $egg_weight = 0; }
$fbird_bweight = $_POST['fbird_bweight']; if($fbird_bweight == "") { $fbird_bweight = 0; }
$mbird_bweight = $_POST['mbird_bweight']; if($mbird_bweight == "") { $mbird_bweight = 0; }
$id = $_POST['idvalue'];

$sql = "UPDATE `breeder_breed_standards` SET `breed_code` = '$breed_code',`breed_age` = '$breed_age',`livability` = '$livability',`ffeed_pbird` = '$ffeed_pbird',`mfeed_pbird` = '$mfeed_pbird',`hd_per` = '$hd_per',`he_per` = '$he_per',`hhp_pweek` = '$hhp_pweek',`chhp_pweek` = '$chhp_pweek',`hhe_pweek` = '$hhe_pweek',`chhe_pweek` = '$chhe_pweek',`hatch_per` = '$hatch_per',`chicks_pweek` = '$chicks_pweek',`cchicks_pweek` = '$cchicks_pweek',`egg_weight` = '$egg_weight',`fbird_bweight` = '$fbird_bweight',`mbird_bweight` = '$mbird_bweight',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "breeder_display_standards1.php?ccid="+a;
</script>