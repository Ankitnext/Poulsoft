<?php
//broiler_save_line.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['loc_line'];


$sql ="SELECT MAX(incr) as incr FROM `location_line`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "LNS";
$i = 0; foreach($_POST['region'] as $region){ $region_code[$i] = $region; $i++; }
$i = 0; foreach($_POST['branch'] as $branch){ $branch_code[$i] = $branch; $i++; }
$i = 0; foreach($_POST['line'] as $lines){ $description[$i] = $lines; $i++; }

$ssize = sizeof($description);
for($i = 0;$i < $ssize;$i++){
    $dsql = "SELECT * FROM `location_line` WHERE `description` LIKE '$description[$i]' AND `dflag` = '0'"; $dquery = mysqli_query($conn,$dsql); $dcount = mysqli_num_rows($dquery);
    if($dcount > 0){ }
    else{
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = $prefix."-".$incr;
        $sql = "INSERT INTO `location_line` (incr,prefix,code,description,region_code,branch_code,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$description[$i]','$region_code[$i]','$branch_code[$i]','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { $incr++; }
    }
}
header('location:broiler_display_line.php?ccid='.$ccid);
?>