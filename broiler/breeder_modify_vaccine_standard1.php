<?php
//breeder_modify_vaccine_standard1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['vaccine_standard1'];

$breed_code = $_POST['breed_code'];
$medvac_code = $_POST['medvac_code'];
$age = $_POST['age']; if($age == ""){ $age = 0; }
$id = $_POST['idvalue'];

$sql = "UPDATE `breeder_medvac_schedule` SET `breed_code` = '$breed_code',`medvac_code` = '$medvac_code',`age` = '$age',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "breeder_display_vaccine_standard1.php?ccid="+a;
</script>