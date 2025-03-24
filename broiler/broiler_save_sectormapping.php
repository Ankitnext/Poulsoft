<?php
//broiler_save_sectormapping.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['sectormapping'];

$sector_code = $branch_code = array();
$i = 0; foreach($_POST['sector_code'] as $sector_codes){ $sector_code[$i] = $sector_codes; $i++; }
$i = 0; foreach($_POST['branch_code'] as $branch_codes){ $branch_code[$i] = $branch_codes; $i++; }
$dsize = sizeof($sector_code); $prefix = "SBM";

for($i = 0;$i < $dsize;$i++){
    $sql = "INSERT INTO `broiler_secbrch_mapping` (sector_code,branch_code,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES('$sector_code[$i]','$branch_code[$i]','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?> 
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Mappings ?");
    if(x == true){
        window.location.href = "broiler_add_sectormapping.php";
    }
    else if(x == false) {
        window.location.href = "broiler_display_sectormapping.php?ccid="+a;
    }
    else {
        window.location.href = "broiler_display_sectormapping.php?ccid="+a;
    }
</script>