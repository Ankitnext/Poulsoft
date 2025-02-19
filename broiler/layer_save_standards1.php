<?php
//layer_save_standards1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['standards1'];

$breed_age = $livability = $feed_pbird = $hd_per = $chhp_pweek =  $egg_weight = $bird_bweight = array();

$breed_code = $_POST['breed_code']; 

$i = 0; foreach($_POST['breed_age'] as $breed_ages){ $breed_age[$i] = $breed_ages; $i++; }
$i = 0; foreach($_POST['hd_per'] as $hd_pers){ $hd_per[$i] = $hd_pers; $i++; }
$i = 0; foreach($_POST['livability'] as $livabilitys){ $livability[$i] = $livabilitys; $i++; }
$i = 0; foreach($_POST['chhp_pweek'] as $chhp_pweeks){ $chhp_pweek[$i] = $chhp_pweeks; $i++; }
$i = 0; foreach($_POST['egg_weight'] as $egg_weights){ $egg_weight[$i] = $egg_weights; $i++; }
$i = 0; foreach($_POST['feed_pbird'] as $feed_pbirds){ $feed_pbird[$i] = $feed_pbirds; $i++; }
$i = 0; foreach($_POST['bird_bweight'] as $bird_bweights){ $bird_bweight[$i] = $bird_bweights; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "standards1";
$trlink = "layer_display_standards1.php";

$dsize = sizeof($breed_age);
for($i = 0;$i < $dsize;$i++){

    if($breed_age[$i] == "") { $breed_age[$i] = 0; }
    if($hd_per[$i] == "") { $hd_per[$i] = 0; }
    if($livability[$i] == "") { $livability[$i] = 0; }
    if($chhp_pweek[$i] == "") { $chhp_pweek[$i] = 0; }
    if($egg_weight[$i] == "") { $egg_weight[$i] = 0; }
    if($feed_pbird[$i] == "") { $feed_pbird[$i] = 0; }
    if($bird_bweight[$i] == "") { $bird_bweight[$i] = 0; }
    
    $sql = "INSERT INTO `layer_breed_standards` (`breed_code`,`breed_age`,`livability`,`feed_pbird`,`hd_per`,`chhp_pweek`,`egg_weight`,`bird_bweight`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$breed_code','$breed_age[$i]','$livability[$i]','$feed_pbird[$i]','$hd_per[$i]','$chhp_pweek[$i]','$egg_weight[$i]','$bird_bweight[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else { }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Breed Standards?");
    if(x == true){
        window.location.href = "layer_add_standards1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_standards1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_standards1.php?ccid="+a;
    }
</script>