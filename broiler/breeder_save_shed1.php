<?php
//breeder_save_shed1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['shed1'];

$farm_code = $_POST['farm_code'];
$unit_code = $_POST['unit_code'];
$shed_code = $_POST['shed_code'];
$description = $_POST['description'];
$shed_type = $_POST['bs_code'];
$shed_sqft = $_POST['shed_sqft']; if($shed_sqft == ""){ $shed_sqft = 0; }
$bird_capacity = $_POST['bird_capacity']; if($bird_capacity == ""){ $bird_capacity = 0; }
$nof_emps = $_POST['nof_emps']; if($nof_emps == ""){ $nof_emps = 0; }

$flag = $dflag = 0; $active = 1;
$trtype = "shed1";
$trlink = "breeder_display_shed1.php";

$sql = "SELECT MAX(id) as incr FROM `breeder_sheds`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } if($incr == ""){ $incr = 0; }
$prefix = "BSC";
$incr++; if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;
$sql = "INSERT INTO `breeder_sheds` (`incr`,`prefix`,`code`,`shed_code`,`description`,`farm_code`,`unit_code`,`shed_type`,`shed_sqft`,`nof_emps`,`bird_capacity`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
VALUES('$incr','$prefix','$code','$shed_code','$description','$farm_code','$unit_code','$shed_type','$shed_sqft','$nof_emps','$bird_capacity','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Sheds?");
    if(x == true){
        window.location.href = "breeder_add_shed1.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_shed1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_shed1.php?ccid="+a;
    }
</script>