<?php
//layer_modify_shed1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['shed1'];

$farm_code = $_POST['farm_code'];
$unit_code = $_POST['unit_code'];
$shed_code = $_POST['shed_code'];
$description = $_POST['description'];
$shed_type = $_POST['bs_code'];
$shed_sqft = $_POST['shed_sqft']; if($shed_sqft == ""){ $shed_sqft = 0; }
$bird_capacity = $_POST['bird_capacity']; if($bird_capacity == ""){ $bird_capacity = 0; }
$nof_emps = $_POST['nof_emps']; if($nof_emps == ""){ $nof_emps = 0; }

$id = $_POST['idvalue'];

$sql = "UPDATE `layer_sheds` SET `farm_code` = '$farm_code',`unit_code` = '$unit_code',`shed_code` = '$shed_code',`description` = '$description',`shed_type` = '$shed_type',`shed_sqft` = '$shed_sqft',`bird_capacity` = '$bird_capacity',`nof_emps` = '$nof_emps',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_shed1.php?ccid="+a;
</script>