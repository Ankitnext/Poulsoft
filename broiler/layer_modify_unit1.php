<?php
//layer_modify_unit1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['unit1'];

$farm_code = $_POST['farm_code'];
$unit_code = $_POST['unit_code'];
$description = $_POST['description'];
$location = $_POST['location'];
$address = $_POST['address'];
$wtank_capacity = $_POST['wtank_capacity']; if($wtank_capacity == ""){ $wtank_capacity = 0; }
$sump_capacity = $_POST['sump_capacity']; if($sump_capacity == ""){ $sump_capacity = 0; }
$incharge_emp = $_POST['incharge_emp'];
$nof_emps = $_POST['nof_emps']; if($nof_emps == ""){ $nof_emps = 0; }

$id = $_POST['idvalue'];

$sql = "UPDATE `layer_units` SET `farm_code` = '$farm_code',`unit_code` = '$unit_code',`description` = '$description',`location` = '$location',`address` = '$address',`wtank_capacity` = '$wtank_capacity',`sump_capacity` = '$sump_capacity',`incharge_emp` = '$incharge_emp',`nof_emps` = '$nof_emps',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_unit1.php?ccid="+a;
</script>