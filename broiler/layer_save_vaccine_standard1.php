<?php
//layer_save_vaccine_standard1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['vaccine_standard1'];

$breed_code = $_POST['breed_code'];
$age = $medvac_code = array();
$i = 0; foreach($_POST['age'] as $ages){ $age[$i] = $ages; $i++; }
$i = 0; foreach($_POST['medvac_code'] as $medvac_codes){ $medvac_code[$i] = $medvac_codes; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "vaccine_standard1";
$trlink = "layer_display_vaccine_standard1.php";

$dsize = sizeof($medvac_code);
for($i = 0;$i < $dsize;$i++){
    if($age[$i] == ""){ $age[$i] = 0; }
    $sql = "INSERT INTO `layer_medvac_schedule` (`breed_code`,`age`,`medvac_code`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$breed_code','$age[$i]','$medvac_code[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Vaccine Schedules?");
    if(x == true){
        window.location.href = "layer_add_vaccine_standard1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_vaccine_standard1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_vaccine_standard1.php?ccid="+a;
    }
</script>