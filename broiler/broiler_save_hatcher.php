<?php
//broiler_save_hatcher.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['hatcher'];

$sql ="SELECT MAX(incr) as incr FROM `broiler_hatchery_hatcher`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "BHH";

$hatchery_code = $_POST['hatchery_code'];

//Transaction Details
$hatcher = $setter_capacity = array();
$i = 0; foreach($_POST['hatcher'] as $hatchers){ $hatcher[$i]= $hatchers; $i++; }
$i = 0; foreach($_POST['hatcher_capacity'] as $hatcher_capacitys){ $hatcher_capacity[$i]= $hatcher_capacitys; $i++; }
$dsize = sizeof($hatcher);

for($i = 0;$i < $dsize;$i++){
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $code = $prefix."-".$incr;

    $sql = "INSERT INTO `broiler_hatchery_hatcher` (incr,prefix,code,hatchery_code,hatcher,hatcher_capacity,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$hatchery_code','$hatcher[$i]','$hatcher_capacity[$i]','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {  }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Hatcherys?");
    if(x == true){
        window.location.href = "broiler_add_hatcher.php";
    }
    else if(x == false) {
        window.location.href = "broiler_display_hatcher.php?ccid="+a;
    }
    else {
        window.location.href = "broiler_display_hatcher.php?ccid="+a;
    }
</script>