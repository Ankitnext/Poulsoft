<?php
//layer_modify_farm1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['farm1'];

$farm_code = $_POST['farm_code'];
$description = $_POST['description'];
$farm_capacity = $_POST['farm_capacity']; if($farm_capacity == ""){ $farm_capacity = 0; }
$id = $_POST['idvalue'];

$sql = "UPDATE `layer_farms` SET `farm_code` = '$farm_code',`description` = '$description',`farm_capacity` = '$farm_capacity',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_farm1.php?ccid="+a;
</script>