<?php
//layer_save_farm1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['farm1'];

$farm_code = $description = $farm_capacity = array();
$i = 0; foreach($_POST['farm_code'] as $farm_codes){ $farm_code[$i] = $farm_codes; $i++; }
$i = 0; foreach($_POST['description'] as $descriptions){ $description[$i] = $descriptions; $i++; }
$i = 0; foreach($_POST['farm_capacity'] as $farm_capacitys){ $farm_capacity[$i] = $farm_capacitys; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "farm1";
$trlink = "layer_display_farm1.php";

$sql = "SELECT MAX(id) as incr FROM `layer_farms`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } if($incr == ""){ $incr = 0; }
$prefix = "BFC";
$dsize = sizeof($description);
for($i = 0;$i < $dsize;$i++){
    if($farm_capacity[$i] == ""){ $farm_capacity[$i] = 0; }
    $incr++; if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $code = $prefix."-".$incr;
    $sql = "INSERT INTO `layer_farms` (`incr`,`prefix`,`code`,`farm_code`,`description`,`farm_capacity`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$code','$farm_code[$i]','$description[$i]','$farm_capacity[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Farms?");
    if(x == true){
        window.location.href = "layer_add_farm1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_farm1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_farm1.php?ccid="+a;
    }
</script>