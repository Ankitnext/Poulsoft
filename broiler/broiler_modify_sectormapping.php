<?php
//broiler_modify_sectormapping.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['sectormapping'];

$sector_code = $_POST['sector_code'];
$branch_code = $_POST['branch_code'];
$id = $_POST['idvalue'];

$sql = "UPDATE `broiler_secbrch_mapping` SET `sector_code` = '$sector_code',`branch_code` = '$branch_code',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "broiler_display_sectormapping.php?ccid="+a;
</script>