<?php
//breeder_modify_unitmap1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['unitmap1'];

$sector_code = $_POST['sector_code'];
$unit_code = $_POST['unit_code'];
$id = $_POST['idvalue'];

$sql = "UPDATE `broiler_secunit_mapping` SET `sector_code` = '$sector_code',`unit_code` = '$unit_code',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "breeder_display_unitmap1.php?ccid="+a;
</script>