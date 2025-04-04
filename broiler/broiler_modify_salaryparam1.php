<?php
//broiler_save_salaryparam1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata"); 
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['salaryparam1'];

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `salary_structures` WHERE `id` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `item_stocktransfers` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    $sql3 = "DELETE FROM `account_summary` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}


$sector = $_POST['sector'];
$desg = $_POST['desg'];
$basic = $_POST['basic'];
$hra = $_POST['hra'];
$med = $_POST['med'];
$conv = $_POST['conv'];
$trans = $_POST['trans'];

$flag = $dflag = 0; $active = 1;
$trtype = "salaryparam1";
$trlink = "broiler_display_salaryparam1.php";

if($basic == ""){ $basic = 0; }
if($hra == ""){ $hra = 0; }
if($med == ""){ $med = 0; }
if($conv == ""){ $conv = 0; }
if($trans == ""){ $trans = 0; }




$amount = round(((float)$quantity * (float)$price),2);
$sql = "UPDATE `salary_structures` SET `incr`='$incr',`prefix`='$prefix',`code`='$code',`sector_code`='$sector',`desig_code`='$desg',`basic`='$basic',`hra`='$hra',`medical`='$med',`con_allow`='$conv',`transport`='$trans',`flag`='$flag',`active`='$active',`dflag`='$dflag',`addedemp`='$addedemp', `addedtime`='$addedtime', `updatedtime`='$addedtime' WHERE `id`='$ids'"; // Replace `some_column` and `some_value` with the appropriate condition.      
if (!mysqli_query($conn, $sql)) {  die("Error 2:-".mysqli_error($conn)); } 
else { }

?> 
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Salary Structure?");
    if(x == true){
        window.location.href = "broiler_add_salaryparam1.php";
    }
    else if(x == false) {
        window.location.href = "broiler_display_salaryparam1.php?ccid="+a;
    }
    else {
        window.location.href = "broiler_display_salaryparam1.php?ccid="+a;
    }
</script>