<?php
//layer_modify_standards1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['standards1'];

$breed_code = $_POST['breed_code']; if($breed_code == "") { $breed_code = 0; }
$breed_age = $_POST['breed_age']; if($breed_age == "") { $breed_age = 0; }
$hd_per = $_POST['hd_per']; if($hd_per == "") { $hd_per = 0; }
$livability = $_POST['livability']; if($livability == "") { $livability = 0; }
$chhp_pweek = $_POST['chhp_pweek']; if($chhp_pweek == "") { $chhp_pweek = 0; }
$egg_weight = $_POST['egg_weight']; if($egg_weight == "") { $egg_weight = 0; }
$feed_pbird = $_POST['feed_pbird']; if($feed_pbird == "") { $feed_pbird = 0; }
$bird_bweight = $_POST['bird_bweight']; if($bird_bweight == "") { $bird_bweight = 0; }
$id = $_POST['idvalue'];

$sql = "UPDATE `layer_breed_standards` SET `breed_code` = '$breed_code',`breed_age` = '$breed_age',`livability` = '$livability',`feed_pbird` = '$feed_pbird',`hd_per` = '$hd_per',`chhp_pweek` = '$chhp_pweek',`egg_weight` = '$egg_weight',`bird_bweight` = '$bird_bweight',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_standards1.php?ccid="+a;
</script>