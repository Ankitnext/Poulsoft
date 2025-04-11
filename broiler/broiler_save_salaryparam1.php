<?php
//broiler_save_salaryparam1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['salaryparam1'];

$sector = $desg = $basic = $hra = $med = $conv = $trans = array();
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
$i = 0; foreach($_POST['desg'] as $desgs){ $desg[$i] = $desgs; $i++; }
$i = 0; foreach($_POST['basic'] as $basics){ $basic[$i] = $basics; $i++; }
$i = 0; foreach($_POST['hra'] as $hras){ $hra[$i] = $hras; $i++; }
$i = 0; foreach($_POST['med'] as $meds){ $med[$i] = $meds; $i++; }
$i = 0; foreach($_POST['conv'] as $convs){ $conv[$i] = $convs; $i++; }
$i = 0; foreach($_POST['trans'] as $transs){ $trans[$i] = $transs; $i++; }


$flag = $dflag = 0; $active = 1; 
$trtype = "salaryparam1";
$trlink = "broiler_display_salaryparam1.php";

$sql = "SELECT * FROM `broiler_emp_allowance_master`"; $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);

$dsize = sizeof($basic);
for($i = 0;$i < $dsize;$i++){

    $incr = 0;
    if($count > 0){
        $sql = "SELECT MAX(id) as incr FROM `broiler_emp_allowance_master`"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; }
    }
    if($incr == "" || (int)$incr == 0){ $incr = 1; $count = 1; } else{ $incr++; }

    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $prefix = "EDA"; $code = $prefix."-".$incr;


    if($basic[$i] == ""){ $basic[$i] = 0; }
    if($hra[$i] == ""){ $hra[$i] = 0; } 
    if($med[$i] == ""){ $med[$i] = 0; }
    if($conv[$i] == ""){ $conv[$i] = 0; }
    if($trans[$i] == ""){ $trans[$i] = 0; }

    $sql = "INSERT INTO `salary_structures` (`incr`,`prefix`,`code`,`sector_code`,`desig_code`,`basic`,`hra`,`medical`,`con_allow`,`transport`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$code','$sector[$i]','$desg[$i]','$basic[$i]','$hra[$i]','$med[$i]','$conv[$i]','$trans[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else{ }
}
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