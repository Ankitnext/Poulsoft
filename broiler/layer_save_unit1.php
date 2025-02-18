<?php
//layer_save_unit1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['unit1'];

$farm_code = $_POST['farm_code'];
$unit_code = $_POST['unit_code'];
$description = $_POST['description'];
$location = $_POST['location'];
$address = $_POST['address'];
$wtank_capacity = $_POST['wtank_capacity']; if($wtank_capacity == ""){ $wtank_capacity = 0; }
$sump_capacity = $_POST['sump_capacity']; if($sump_capacity == ""){ $sump_capacity = 0; }
$incharge_emp = $_POST['incharge_emp'];
$nof_emps = $_POST['nof_emps']; if($nof_emps == ""){ $nof_emps = 0; }

$flag = $dflag = 0; $active = 1;
$trtype = "unit1";
$trlink = "layer_display_unit1.php";

$sql = "SELECT MAX(id) as incr FROM `layer_units`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } if($incr == ""){ $incr = 0; }
$prefix = "BUC";
$incr++; if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;
$sql = "INSERT INTO `layer_units` (`incr`,`prefix`,`code`,`unit_code`,`description`,`farm_code`,`location`,`address`,`wtank_capacity`,`sump_capacity`,`incharge_emp`,`nof_emps`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
VALUES('$incr','$prefix','$code','$unit_code','$description','$farm_code','$location','$address','$wtank_capacity','$sump_capacity','$incharge_emp','$nof_emps','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Units?");
    if(x == true){
        window.location.href = "layer_add_unit1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_unit1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_unit1.php?ccid="+a;
    }
</script>