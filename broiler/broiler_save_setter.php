<?php
//broiler_save_setter.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['setter'];

$sql ="SELECT MAX(incr) as incr FROM `broiler_hatchery_setter`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "BHS";

$hatchery_code = $_POST['hatchery_code'];

//Transaction Details
$setter_no = $setter_capacity = array();
$i = 0; foreach($_POST['setter_no'] as $setter_nos){ $setter_no[$i]= $setter_nos; $i++; }
$i = 0; foreach($_POST['setter_capacity'] as $setter_capacitys){ $setter_capacity[$i]= $setter_capacitys; $i++; }
$dsize = sizeof($setter_no);

for($i = 0;$i < $dsize;$i++){
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $code = $prefix."-".$incr;

    $sql = "INSERT INTO `broiler_hatchery_setter` (incr,prefix,code,hatchery_code,setter_no,setter_capacity,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$hatchery_code','$setter_no[$i]','$setter_capacity[$i]','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {  }
}

?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Setters?");
    if(x == true){
        window.location.href = "broiler_add_setter.php";
    }
    else if(x == false) {
        window.location.href = "broiler_display_setter.php?ccid="+a;
    }
    else {
        window.location.href = "broiler_display_setter.php?ccid="+a;
    }
</script>