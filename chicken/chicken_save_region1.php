<?php
//chicken_save_region1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['region1'];

$description = array();
$i = 0; foreach($_POST['description'] as $descriptions){ $description[$i] = $descriptions; $i++; }


$sql = "SELECT MAX(incr) as incr FROM `main_regions`";
$query = mysqli_query($conn, $sql); $count = mysqli_num_rows($query);
if($count > 0){ while($row = mysqli_fetch_array($query)){ $incr = $row['incr']; } } else { $incr = 0; }

$flag = $dflag = 0; $active = 1;
$prefix = "RGN";
for($i = 0;$i < count($description);$i++){
    if($description[$i] != ""){
        $incr++;
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = ""; $code = $prefix."-".$incr;

        $sql = "INSERT INTO `main_regions`(`incr`, `prefix`, `code`, `description`, `addedemp`, `addedtime`, `flag`, `active`, `dflag`) 
        VALUES ('$incr','$prefix','$code','$description[$i]','$addedemp','$addedtime','0','1','0')";
        if(!mysqli_query($conn, $sql)){ die("Error: ".mysqli_error($conn)); } else{ }
    }
}

header('location:chicken_display_region1.php?ccid='.$cid);
