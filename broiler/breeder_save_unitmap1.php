<?php
//breeder_save_unitmap1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['unitmap1'];

$sector_code = $unit_code = array();
$i = 0; foreach($_POST['sector_code'] as $sector_codes){ $sector_code[$i] = $sector_codes; $i++; }
$i = 0; foreach($_POST['unit_code'] as $unit_codes){ $unit_code[$i] = $unit_codes; $i++; }
$dsize = sizeof($sector_code); $prefix = "SBM";

for($i = 0;$i < $dsize;$i++){
    $sql = "INSERT INTO `broiler_secunit_mapping` (sector_code,unit_code,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES('$sector_code[$i]','$unit_code[$i]','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?> 
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Mappings ?");
    if(x == true){
        window.location.href = "breeder_add_unitmap1.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_unitmap1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_unitmap1.php?ccid="+a;
    }
</script>