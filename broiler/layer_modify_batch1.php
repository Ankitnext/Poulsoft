<?php
//layer_modify_batch1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['batch1'];

// $farm_code = $_POST['farm_code'];
// $unit_code = $_POST['unit_code'];
// $bird_source = $_POST['bird_source'];
$vs_code = $_POST['vs_code'];
$batch_code = $_POST['batch_code'];
$description = $_POST['description'];
$breed_code = $_POST['breed_code'];
//$bstart_date = date("Y-m-d",strtotime($_POST['bstart_date']));
//$start_age = $_POST['start_age']; if($start_age == ""){ $start_age = 0; }
if($_POST['beps_flag'] == "on" || $_POST['beps_flag'] == true || $_POST['beps_flag'] == 1){ $beps_flag = 1; } else{ $beps_flag = 0; }

$id = $_POST['idvalue'];

$sql = "UPDATE `layer_batch` SET `farm_code` = '$farm_code',`unit_code` = '$unit_code',`batch_code` = '$batch_code',`description` = '$description',`bird_source` = '$bird_source',`vs_code` = '$vs_code',`breed_code` = '$breed_code',`beps_flag` = '$beps_flag',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_batch1.php?ccid="+a;
</script>