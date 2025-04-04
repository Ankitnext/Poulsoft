<?php
//broiler_save_employee_allowances1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['employee_allowances1'];

//Check Column Availability
$sql='SHOW COLUMNS FROM `broiler_emp_allowance_master`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("travel_allowance", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_emp_allowance_master` ADD `travel_allowance` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `daily_allowance`"; mysqli_query($conn,$sql); }

$fdate = $tdate = $desig_code = $branch_code = $per_km_rate = $daily_allowance = $travel_allowance = array();
$i = 0; foreach($_POST['fdate'] as $fdates){ $fdate[$i] = date("Y-m-d",strtotime($fdates)); $i++; }
$i = 0; foreach($_POST['tdate'] as $tdates){ $tdate[$i] = date("Y-m-d",strtotime($tdates)); $i++; }
$i = 0; foreach($_POST['desig_code'] as $desig_codes){ $desig_code[$i] = $desig_codes; $i++; }
$i = 0; foreach($_POST['branch_code'] as $branch_codes){ $branch_code[$i] = $branch_codes; $i++; }
$i = 0; foreach($_POST['per_km_rate'] as $per_km_rates){ $per_km_rate[$i] = $per_km_rates; $i++; }
$i = 0; foreach($_POST['daily_allowance'] as $daily_allowances){ $daily_allowance[$i] = $daily_allowances; $i++; }
$i = 0; foreach($_POST['travel_allowance'] as $travel_allowances){ $travel_allowance[$i] = $travel_allowances; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "employee_allowances1";
$trlink = "broiler_display_employee_allowances1.php";

$sql = "SELECT * FROM `broiler_emp_allowance_master`"; $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);

$dsize = sizeof($desig_code);
for($i = 0;$i < $dsize;$i++){
    $incr = 0;
    if($count > 0){
        $sql = "SELECT MAX(id) as incr FROM `broiler_emp_allowance_master`"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
    }
    if($incr == "" || (int)$incr == 0){ $incr = 1; $count = 1; } else{ $incr++; }

    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $prefix = "EDA"; $code = $prefix."-".$incr;

    if($per_km_rate[$i] == ""){ $per_km_rate[$i] = 0; }
    if($daily_allowance[$i] == ""){ $daily_allowance[$i] = 0; }
    if($travel_allowance[$i] == ""){ $travel_allowance[$i] = 0; }

    $sql = "INSERT INTO `broiler_emp_allowance_master` (`incr`,`prefix`,`code`,`fdate`,`tdate`,`desig_code`,`branch_code`,`per_km_rate`,`daily_allowance`,`travel_allowance`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$code','$fdate[$i]','$tdate[$i]','$desig_code[$i]','$branch_code[$i]','$per_km_rate[$i]','$daily_allowance[$i]','$travel_allowance[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else { }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Entries?");
    if(x == true){
        window.location.href = "broiler_add_employee_allowances1.php";
    }
    else if(x == false) {
        window.location.href = "broiler_display_employee_allowances1.php?ccid="+a;
    }
    else {
        window.location.href = "broiler_display_employee_allowances1.php?ccid="+a;
    }
</script>
<?php

