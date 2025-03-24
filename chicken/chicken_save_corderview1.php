<?php
//chicken_save_corderview1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$db_name = $_SESSION['dbase'];

$active = 1; $flag = $dflag = 0;
$trtype = "corderview1";
$trlink = "chicken_display_corderview1.php";

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_name = $cus_mobile = array();
while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; $cus_mobile[$row['code']] = $row['mobileno']; }

foreach($_POST['slno'] as $sno){
    $ccode = ""; $bigBoxWeight = $bigBoxes = $tandoori = $dressed_chicken = 0;
    $ccode = $_POST['ccode'][$sno];
    $mobile_no = $cus_mobile[$ccode];
    
    if($_POST['bigBoxWeight'][$sno] == true || $_POST['bigBoxWeight'][$sno] == "on" || $_POST['bigBoxWeight'][$sno] == 1 || $_POST['bigBoxWeight'][$sno] == "1"){ $bigBoxWeight = 1; }
    if($_POST['bigBoxes'][$sno] == true || $_POST['bigBoxes'][$sno] == "on" || $_POST['bigBoxes'][$sno] == 1 || $_POST['bigBoxes'][$sno] == "1"){ $bigBoxes = 1; }
    if($_POST['tandoori'][$sno] == true || $_POST['tandoori'][$sno] == "on" || $_POST['tandoori'][$sno] == 1 || $_POST['tandoori'][$sno] == "1"){ $tandoori = 1; }
    if($_POST['dressed_chicken'][$sno] == true || $_POST['dressed_chicken'][$sno] == "on" || $_POST['dressed_chicken'][$sno] == 1 || $_POST['dressed_chicken'][$sno] == "1"){ $dressed_chicken = 1; }
    
    $c_cnt = 0;
    $sql = "SELECT * FROM `customerOrderViewPermissions` WHERE `ccode` = '$ccode' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $c_cnt = mysqli_num_rows($query);
    if($c_cnt > 0){
        $sql = "UPDATE `customerOrderViewPermissions` SET `mobile_no` = '$cmob',`bigBoxWeight` = '$bigBoxWeight',`bigBoxes` = '$bigBoxes',`tandoori` = '$tandoori',`dressed_chicken` = '$dressed_chicken',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `ccode` = '$ccode' AND `dflag` = '0'";
    }
    else{
        $sql = "INSERT INTO `customerOrderViewPermissions` (`ccode`,`mobile_no`,`bigBoxWeight`,`bigBoxes`,`tandoori`,`dressed_chicken`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) VALUES 
        ('$ccode','$mobile_no','$bigBoxWeight','$bigBoxes','$tandoori','$dressed_chicken','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    }
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "SELECT * FROM `common_customeraccess` WHERE `ccode` = '$ccode' AND `db_name` = '$db_name'";
        $query = mysqli_query($conns,$sql); $c_cnt = mysqli_num_rows($query);
        if((int)$c_cnt > 0){ }
        else{
            $sql = "SELECT MAX(incr) as incr FROM `common_customeraccess`"; $query = mysqli_query($conns,$sql);
	        while($row = mysqli_fetch_assoc($query)){ $incr = (int)$row['incr']; }
            $prefix = "USR";
            $incr = $incr + 1;
            if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
            $code = $prefix."-".$incr;
            $user_name = 
            $sql = "INSERT INTO `common_customeraccess` (incr,prefix,code,user_name,mobile,ccode,db_name,client,active_status,user_type,screens,screenstwo,screensthree,screensfour,screensfive,addedempcode,createddatetime,updateddatetime) 
            VALUES('$incr','$prefix','$code','$user_name','$mobile','$ccode','$db_name','$client','1','$user_type','$screens[$i]','$screenstwo[$i]','$screensthree[$i]','$screensfour[$i]','$screensfive[$i]','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conns,$sql)){ die("Error:-".mysqli_error($conns)); } else{ }
        }
    }
}

header('location:chicken_display_corderview1.php?ccid='.$ccid);