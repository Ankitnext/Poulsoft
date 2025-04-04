<?php
//broiler_save_designation.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['designation'];

$designation = $_POST['designation'];
$sql ="SELECT MAX(incr) as incr FROM `broiler_designation`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "DSG";
foreach($_POST['designation'] as $designation){
    if($designation != ""){
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = $prefix."-".$incr;
        $sql = "INSERT INTO `broiler_designation` (incr,prefix,code,description,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$designation','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { $incr++; }
    }
}
header('location:broiler_display_designation.php?ccid='.$ccid);
?>