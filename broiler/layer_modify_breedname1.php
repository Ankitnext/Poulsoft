<?php
//layer_modify_breedname1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['breedname1'];

$description = $_POST['description'];
$id = $_POST['idvalue'];

$sql = "UPDATE `layer_breed_details` SET `description` = '$description',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "layer_display_breedname1.php?ccid="+a;
</script>