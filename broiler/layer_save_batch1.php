<?php
//layer_save_batch1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['batch1'];

// $farm_code = $_POST['farm_code'];
// $unit_code = $_POST['unit_code'];
// $bird_source = $_POST['bird_source'];
$vs_code = $_POST['vs_code'];
$batch_code = $_POST['batch_code'];
$description = $_POST['description'];
$breed_code = $_POST['breed_code'];
//$bstart_date = date("Y-m-d",strtotime($_POST['bstart_date']));
//$start_age = $_POST['start_age']; if($start_age == ""){ $start_age = 0; }
if($_POST['beps_flag'] == "on" || $_POST['beps_flag'] == true || $_POST['beps_flag'] == 1){ $beps_flag = 1; } else{ $beps_flag = 0; }

$flag = $dflag = 0; $active = 1;
$trtype = "batch1";
$trlink = "layer_display_batch1.php";

$sql = "SELECT MAX(id) as incr FROM `layer_batch`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } if($incr == ""){ $incr = 0; }
$prefix = "BBC";
$incr++; if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;
$sql = "INSERT INTO `layer_batch` (`incr`,`prefix`,`code`,`batch_code`,`description`,`farm_code`,`unit_code`,`bird_source`,`vs_code`,`breed_code`,`beps_flag`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
VALUES('$incr','$prefix','$code','$batch_code','$description','$farm_code','$unit_code','$bird_source','$vs_code','$breed_code','$beps_flag','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Batches?");
    if(x == true){
        window.location.href = "layer_add_batch1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_batch1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_batch1.php?ccid="+a;
    }
</script>