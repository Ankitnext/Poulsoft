<?php
//layer_save_breedname1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['breedname1'];

$description = array();
$i = 0; foreach($_POST['description'] as $descriptions){ $description[$i] = $descriptions; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "breedname1";
$trlink = "layer_display_breedname1.php";

$sql = "SELECT MAX(id) as incr FROM `layer_breed_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } if($incr == ""){ $incr = 0; }
$prefix = "BBN";
$dsize = sizeof($description);
for($i = 0;$i < $dsize;$i++){
    $incr++; if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $code = $prefix."-".$incr;
    $sql = "INSERT INTO `layer_breed_details` (`incr`,`prefix`,`code`,`description`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$code','$description[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Breeds?");
    if(x == true){
        window.location.href = "layer_add_breedname1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_breedname1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_breedname1.php?ccid="+a;
    }
</script>