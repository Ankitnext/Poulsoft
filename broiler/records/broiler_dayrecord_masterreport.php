<?php
//broiler_dayrecord_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase']  = $_GET['db']; } else { $db = ''; }
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];


}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}
$file_name = "Farm Day Record";

/**
 * Computes the distance between two coordinates.
 *
 * Implementation based on reverse engineering of
 * <code>google.maps.geometry.spherical.computeDistanceBetween()</code>.
 *
 * @param float $lat1 Latitude from the first point.
 * @param float $lng1 Longitude from the first point.
 * @param float $lat2 Latitude from the second point.
 * @param float $lng2 Longitude from the second point.
 * @param float $radius (optional) Radius in meters.
 *
 * @return float Distance in meters.
 */

function computeDistance($lat1, $lng1, $lat2, $lng2, $radius = 6378137)
{
    static $x = M_PI / 180;
    $lat1 *= $x; $lng1 *= $x;
    $lat2 *= $x; $lng2 *= $x;
    $distance = 2 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2), 2) + cos($lat1) * cos($lat2) * pow(sin(($lng1 - $lng2) / 2), 2)));

    return round(($distance * $radius)/1000,3)." Km";
}
include "../broiler_check_tableavailability.php";

$i = 0;
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial, sans-serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica, Arial, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Verdana, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Tahoma, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Trebuchet MS";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Trebuchet MS', sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman'";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman', serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Georgia, serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Garamond, serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Courier New', monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Courier, monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Optima";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Segoe";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Calibri";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Candara";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Grande";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Sans Unicode";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Gill Sans";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Source Sans Pro', 'Arial', sans-serif";

for($i = 0;$i <= 30;$i++){ $fsizes[$i."px"] = $i."px"; }

$i = 0;

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_link_itembrand", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_link_itembrand LIKE poulso6_admin_broiler_broilermaster.broiler_link_itembrand;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_item_brands", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_item_brands LIKE poulso6_admin_broiler_broilermaster.broiler_item_brands;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `broiler_link_itembrand` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $ibrand_code = array();
while($row = mysqli_fetch_assoc($query)){ $ibrand_code[$row['item_code']] = $row['brand_code']; }

$sql = "SELECT * FROM `broiler_item_brands` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $ibrand_name = array();
while($row = mysqli_fetch_assoc($query)){ $ibrand_name[$row['code']] = $row['description']; }

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); 
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = ""; $col_count = 0;
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
                //echo "<br/>".$cna."-".$row2[$cna];
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}



$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }


//for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; echo "<br/>".$act_col_numbs[$key_id]; }
$branch_code = $branch_name = array();
if($count93 > 0){

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }
}
$line_code = $line_name = array();
if($count94 > 0){
$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }
}
$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_farmer = $farm_latitude = $farm_longitude = array();
if($count26 > 0){
    $i = 0;
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $i++;
    //echo "<br/>".$i."@".$row['code'];
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code']; $farm_latitude[$row['code']] = $row['latitude']; $farm_longitude[$row['code']] = $row['longitude'];
}
}
$batch_code = $batch_name = $batch_book = $batch_gcflag = array();
if($count12 > 0){
$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }
}
$bstd_body_weight = $bstd_daily_gain = $bstd_avg_daily_gain = $bstd_fcr = $bstd_cum_feed = $bstd_feed_consumed = array();
if($count16 > 0){
$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; $bstd_feed_consumed[$row['age']] = (float)$row['feed_consumed']; }
}
$supervisor_code = $supervisor_name = array();
if($count25 > 0){
$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }
}
$db_emp_code = array();
if($count96 > 0){
$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }
}
$item_code = $item_name = array();
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
}
$chick_code = $chick_cat = "";
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_cat = $row['category']; }
}
$farmer_name = $farmer_mobile1 = $farmer_mobile2 = array();
if($count27 > 0){
$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }
}
$dieases_name = array();
if($count21 > 0){
$sql = "SELECT * FROM `broiler_diseases`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $dieases_name[$row['trnum']] = $row['name']; }
}
$bird_code = $bird_name = "";
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }
}
$item_cat = ""; $feed_code = $feed_coa = array();
if($count87 > 0){
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } $feed_coa[$row['iac']] = $row['iac']; }
}

if($count89 > 0){
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }
}
$item_cat = "";
if($count87 > 0){
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
}
$medvac_code = array();
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
}
$item_cat = "";
if($count87 > 0){
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
}
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
}
$sector_code = $sector_name = array();
$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'broiler_dayrecord_masterreport.php' AND `field_function` = 'Day Cons' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $pound_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['name']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $supervisors = $farms = "all"; $excel_type = "display";
$font_stype = ""; $font_size = "11px";
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
     $regions = $_POST['regions'];
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));

    $font_stype = $_POST['font_stype'];
    $font_size = $_POST['font_size'];

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_dayrecord_masterreport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors."&farm=".$farms."&href=".$field_href[0];
}
else{
    $url = "";
}

$farm_query = "";
    if($regions != "all"){
        $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
        $rbrh_list = implode("','",$rbrh_alist);
        $farm_query .= " AND `branch_code` IN ('$rbrh_list')";
    }

    if($branches != "all"){ $farm_query .= " AND `branch_code` LIKE '$branches'"; }
    if($lines != "all"){ $farm_query .= " AND `line_code` LIKE '$lines'"; }
    if($supervisors != "all"){ $farm_query .= " AND `supervisor_code` LIKE '$supervisors'"; }
    if($farms != "all"){ $farm_query .= " AND `code` LIKE '$farms'"; }

    $farm_list = ""; $farm_list = implode("','", $farm_code);
    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_query." AND `dflag` = '0' ORDER BY `description` ASC";

    $query = mysqli_query($conn,$sql); $farm_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }
    
    
    $farm_list = implode("','",$farm_alist);
    $farm_query = " AND a.farm_code IN ('$farm_list')";
    $farm_query2 = " AND farm_code IN ('$farm_list')";
// if($farms != "all"){
//     $farm_query = " AND a.farm_code = '$farms'";
//     $farm_query2 = " AND farm_code IN ('$farms')";
// }
// else if($supervisors != "all"){
//     foreach($farm_code as $fcode){
//         if($farm_supervisor[$fcode] == $supervisors){
//             if($farm_list == ""){
//                 $farm_list = $fcode;
//             }
//             else{
//                 $farm_list = $farm_list."','".$fcode;
//             }
//         }
//     }
//     $farm_query = " AND a.farm_code IN ('$farm_list')";
//     $farm_query2 = " AND farm_code IN ('$farm_list')";
// }
// else if($lines != "all"){
//     foreach($farm_code as $fcode){
//         if($farm_line[$fcode] == $lines){
//             if($farm_list == ""){
//                 $farm_list = $fcode;
//             }
//             else{
//                 $farm_list = $farm_list."','".$fcode;
//             }
//         }
//     }
//     $farm_query = " AND a.farm_code IN ('$farm_list')";
//     $farm_query2 = " AND farm_code IN ('$farm_list')";
// }
// else if($branches != "all"){
//     foreach($farm_code as $fcode){
//         //echo "<br/>".$fcode."@".$farm_branch[$fcode]."@".$branches;
//         if($farm_branch[$fcode] == $branches){
//             if($farm_list == ""){
//                 $farm_list = $fcode;
//             }
//             else{
//                 $farm_list = $farm_list."','".$fcode;
//             }
//         }
//     }
//     $farm_query = " AND a.farm_code IN ('$farm_list')";
//     $farm_query2 = " AND farm_code IN ('$farm_list')";
// }
// else{
//     foreach($farm_code as $fcode){
//         if($farm_list == ""){
//             $farm_list = $fcode;
//         }
//         else{
//             $farm_list = $farm_list."','".$fcode;
//         }
//     }
//     $farm_query = " AND a.farm_code IN ('$farm_list')";
//     $farm_query2 = " AND farm_code IN ('$farm_list')";
// }

$tblcol_size = sizeof($act_col_numbs);
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
       
        <?php
            if($excel_type == "print"){
                include "headerstyle_wprint_font.php";  
            }
            else{
               
                include "headerstyle_woprint_font.php";   
            }
        ?>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
    </head>
    <body align="center">
        <table class="tbl" align="center"   <?php if($excel_type == "print"){ echo ' id="mine"'; } else{ echo 'width="1300px"'; } ?>>
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="<?php echo $tblcol_size - 2; ?>" align="center"><?php echo $row['cdetails']; ?><h5>Day Record Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_dayrecord_masterreport.php" method="post">
            <?php } else { ?>
            <form action="broiler_dayrecord_masterreport.php?db=<?php echo $db; ?>&userid=<?php echo $user_code; ?>" method="post">
            <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                    <th colspan="<?php echo $tblcol_size; ?>">
                            <div class="row">
                                <!--<div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php //echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>-->
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                 <div class="m-2 form-group">
                                    <label>Region</label>
                                    <select name="regions" id="regions" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($regions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" onChange="fetch_farms_details(this.id)>
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onChange="fetch_farms_details(this.id)>
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onChange="fetch_farms_details(this.id)>
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" onChange="fetch_farms_details(this.id)>
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Style</label>
                                    <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                        <option value="" <?php if($font_stype == ""){ echo "selected"; } ?>>-Defalut-</option>
                                        <?php
                                        foreach($font_family_code as $i){
                                        ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if($font_stype == $font_family_name[$i]){ echo "selected"; } ?>><?php echo $font_family_name[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Size</label>
                                    <select name="font_size" id="font_size" class="form-control select2">
                                        <?php
                                        foreach($fsizes as $i){
                                        ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if($font_size == $fsizes[$i]){ echo "selected"; } ?>><?php echo $fsizes[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <!--<div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php //if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php //if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php //if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>-->
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php if($excel_type == "print"){ } else{ ?>
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td colspan="<?php echo $tblcol_size; ?>">
                <div id='control_sh'>
                    <?php
                        //if($_SERVER['REMOTE_ADDR'] == "49.205.129.174"){ for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; if(!empty($act_col_numbs[$key_id])){ echo "<br/>".$act_col_numbs[$key_id]."@".$key_id; } else if(!empty($nac_col_numbs[$key_id1])){ echo "<br/>".$nac_col_numbs[$key_id1]."@".$key_id1; } else{ } } }
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_ccode"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_ccode" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Code</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "batch_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch Code</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "batch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "book_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Book No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "brood_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_placed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_placed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Placed Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "opening_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="opening_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Opening Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_mort" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "previous_day_mort"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="previous_day_mort" onclick="update_masterreport_status(this.id);" '.$checked.'><span>DB Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_mort" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "yesturday_mort"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="yesturday_mort" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Yest Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_img" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort Image</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_cum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_cum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_cum_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_cum_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum Mort%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "culls_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="culls_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Culls</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_img" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "culls_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="culls_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cull Image</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birdswt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdswt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "available_birds"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="available_birds" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Balance Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std B.Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "avg_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="avg_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg B.Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "cfcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cfcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>CFCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedopening_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedopening_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed OB</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedin_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedin_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed In</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedout_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedout_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Out</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } if($pound_flag > 0) { echo '<input type="checkbox" class="hide_show" id="feedconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Day Con</span>';} else { echo '<input type="checkbox" class="hide_show" id="feedconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Con</span>'; }}
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_feedintake" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "day_feedintake"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="day_feedintake" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Day Feed In Take</span>';} 
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedconsumed_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedconsumed_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Bag Con</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_balance_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } if($pound_flag > 0) { echo '<input type="checkbox" class="hide_show" id="feed_balance_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Closing</span>'; } else { echo '<input type="checkbox" class="hide_show" id="feed_balance_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Stock</span>'; }}
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedcumconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedcumconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum. Feed In Take</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cum_feedintake" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "cum_feedintake"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cum_feedintake" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum. Feed</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Images</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_feed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "previous_day_feed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="previous_day_feed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>DB Feed</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_feed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "yesturday_feed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="yesturday_feed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Yest Feed</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "line_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Line</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "branch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mobile_no1"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mobile_no1" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Contact</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "addedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="addedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry Time</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "addedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="addedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry By</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "remakrs" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "remakrs"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="remakrs" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Remarks</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "dieases_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "dieases_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="dieases_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Dieases Names</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_location" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_location"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_location" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Location</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "entry_location" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "entry_location"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="entry_location" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry Location</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "difference_kms" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "difference_kms"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="difference_kms" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Diff KM(mts)</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_feed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_feed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_cumfeed_perbirdno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_cumfeed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_cumfeed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum. Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_received_from"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_received_from" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Hatchery Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "latest_feedin_brand"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="latest_feedin_brand" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Latest Feed-In Brand</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chickin_hatchery_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chickin_hatchery_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Hatchery</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chickin_supplier_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chickin_supplier_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_birds" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "pp_sent_birds"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="pp_sent_birds" onclick="update_masterreport_status(this.id);" '.$checked.'><span>T. Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_weight" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "pp_sent_weight"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="pp_sent_weight" onclick="update_masterreport_status(this.id);" '.$checked.'><span>T. Weight</span>'; }
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table class="tbl" id="main_table" align="center"  style="width:1300px;">
        <?php
        }
        $html = $nhtml = $fhtml = '';
        $html .= '<thead class="thead3" id="head_names">';

        $nhtml .= '<tr style="text-align:center;" align="center">';
        $fhtml .= '<tr style="text-align:center;" align="center">';
        for($i = 1;$i <= $col_count;$i++){
            $key_id = "A:1:".$i;
            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ $nhtml .= '<th>Farm Code</th>'; $fhtml .= '<th id="order">Farm Code</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ $nhtml .= '<th>Farmer</th>'; $fhtml .= '<th id="order">Farmer</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code"){ $nhtml .= '<th>Batch Code</th>'; $fhtml .= '<th id="order">Batch Code</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ $nhtml .= '<th>Batch</th>'; $fhtml .= '<th id="order">Batch</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ $nhtml .= '<th>Book No</th>'; $fhtml .= '<th id="order">Book No</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ $nhtml .= '<th>Supervisor</th>'; $fhtml .= '<th id="order">Supervisor</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ $nhtml .= '<th>Placed Birds</th>'; $fhtml .= '<th id="order_num">Placed Birds</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno"){ $nhtml .= '<th>Opening Birds</th>'; $fhtml .= '<th id="order_num">Opening Birds</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_mort"){ $nhtml .= '<th>DB Mort</th>'; $fhtml .= '<th id="order_num">DB Mort</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_mort"){ $nhtml .= '<th>Yest Mort</th>'; $fhtml .= '<th id="order_num">Yest Mort</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ $nhtml .= '<th>Mort</th>'; $fhtml .= '<th id="order_num">Mort</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ $nhtml .= '<th>Mort%</th>'; $fhtml .= '<th id="order_num">Mort%</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_img"){ $nhtml .= '<th>Mort Image</th>'; $fhtml .= '<th id="order">Mort Image</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum"){ $nhtml .= '<th>Cum Mort</th>'; $fhtml .= '<th id="order_num">Cum Mort</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum_per"){ $nhtml .= '<th>Cum Mort%</th>'; $fhtml .= '<th id="order_num">Cum Mort%</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ $nhtml .= '<th>Culls</th>'; $fhtml .= '<th id="order_num">Culls</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_img"){ $nhtml .= '<th>Cull Image</th>'; $fhtml .= '<th id="order">Cull Image</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ $nhtml .= '<th>Sold</th>'; $fhtml .= '<th id="order_num">Sold</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ $nhtml .= '<th>Sold Wt</th>'; $fhtml .= '<th id="order_num">Sold Wt</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ $nhtml .= '<th>Balance Birds</th>'; $fhtml .= '<th id="order_num">Balance Birds</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ $nhtml .= '<th>Std B.Wt</th>'; $fhtml .= '<th id="order_num">Std B.Wt</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ $nhtml .= '<th>Avg B.Wt</th>'; $fhtml .= '<th id="order_num">Avg B.Wt</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ $nhtml .= '<th>Std FCR</th>'; $fhtml .= '<th id="order_num">Std FCR</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ $nhtml .= '<th>FCR</th>'; $fhtml .= '<th id="order_num">FCR</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ $nhtml .= '<th>CFCR</th>'; $fhtml .= '<th id="order_num">CFCR</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count"){ $nhtml .= '<th>Feed OB</th>'; $fhtml .= '<th id="order_num">Feed OB</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count"){ $nhtml .= '<th>Feed In</th>'; $fhtml .= '<th id="order_num">Feed In</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count"){ $nhtml .= '<th>Feed Out</th>'; $fhtml .= '<th id="order_num">Feed Out</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ if($pound_flag > 0){ $nhtml .= '<th>Day Con</th>'; $fhtml .= '<th id="order_num">Day Con</th>';} else{ $nhtml .= '<th>Feed Con</th>'; $fhtml .= '<th id="order_num">Feed Con</th>'; }}
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_feedintake"){ $nhtml .= '<th>Day Feed In Take</th>'; $fhtml .= '<th id="order_num">Day Feed In Take</th>';} 
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags"){ $nhtml .= '<th>Feed Bag Con</th>'; $fhtml .= '<th id="order_num">Feed Bag Con</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ if($pound_flag > 0){ $nhtml .= '<th>Feed Closing</th>'; $fhtml .= '<th id="order_num">Feed Closing</th>'; }else { $nhtml .= '<th>Feed Stock</th>'; $fhtml .= '<th id="order_num">Feed Stock</th>'; }}
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count"){ $nhtml .= '<th>Cum. Feed</th>'; $fhtml .= '<th id="order_num">Cum. Feed</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cum_feedintake"){ $nhtml .= '<th>Cum. Feed In Take</th>'; $fhtml .= '<th id="order_num">Cum. Feed In Take</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img"){ $nhtml .= '<th>Feed Images</th>'; $fhtml .= '<th id="order">Feed Images</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_feed"){ $nhtml .= '<th>DB Feed</th>'; $fhtml .= '<th id="order_num">DB Feed</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_feed"){ $nhtml .= '<th>Yest Feed</th>'; $fhtml .= '<th id="order_num">Yest Feed</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ $nhtml .= '<th>Line</th>'; $fhtml .= '<th id="order">Line</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ $nhtml .= '<th>Branch</th>'; $fhtml .= '<th id="order">Branch</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1"){ $nhtml .= '<th>Farmer Contact</th>'; $fhtml .= '<th id="order">Farmer Contact</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedtime"){ $nhtml .= '<th>Entry Time</th>'; $fhtml .= '<th id="order_date">Entry Time</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedemp"){ $nhtml .= '<th>Entry By</th>'; $fhtml .= '<th id="order">Entry By</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "remakrs"){ $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "dieases_name"){ $nhtml .= '<th>Dieases Names</th>'; $fhtml .= '<th id="order">Dieases Names</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_location"){ $nhtml .= '<th>Farm Location</th>'; $fhtml .= '<th id="order">Farm Location</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "entry_location"){ $nhtml .= '<th>Entry Location</th>'; $fhtml .= '<th id="order">Entry Location</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "difference_kms"){ $nhtml .= '<th>Diff KM(mts)</th>'; $fhtml .= '<th id="order_num">Diff KM(mts)</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){ $nhtml .= '<th>Std Feed/Bird</th>'; $fhtml .= '<th id="order_num">Std Feed/Bird</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){ $nhtml .= '<th>Act Feed/Bird</th>'; $fhtml .= '<th id="order_num">Act Feed/Bird</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_cumfeed_perbirdno"){ $nhtml .= '<th>Cum. Feed/Bird</th>'; $fhtml .= '<th id="order_num">Cum. Feed/Bird</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ $nhtml .= '<th>Hatchery Name</th>'; $fhtml .= '<th id="order">Hatchery Name</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand"){ $nhtml .= '<th>Latest Feed-In Brand</th>'; $fhtml .= '<th id="order">Latest Feed-In Brand</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ $nhtml .= '<th>Hatchery</th>'; $fhtml .= '<th id="order">Hatchery</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ $nhtml .= '<th>Supplier</th>'; $fhtml .= '<th id="order">Supplier</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_birds"){ $nhtml .= '<th>T. Birds</th>'; $fhtml .= '<th id="order">T. Birds</th>'; }
            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_weight"){ $nhtml .= '<th>T. Weight</th>'; $fhtml .= '<th id="order">T. Weight</th>'; }
            else{ }
        }

        $nhtml .= '</tr>';
        $fhtml .= '</tr>';
        $html .= $fhtml;
        $html .= '</thead>';

        echo $html;
        //<thead class="thead3" align="center" style="width:1212px;">
            if(isset($_POST['submit_report']) == true || isset($_GET['submit']) == true){
            ?>
            <tbody class="tbody1" id = "tbody1" >
                <?php

                
                    $till_date = date("Y-m-d",strtotime($tdate));
                    $from_date = $from_opening_date = date("Y-m-d",strtotime($till_date."-1 days")); 

                    $batch_sql = "SELECT * FROM `broiler_batch` WHERE  active = '1' AND dflag = '0'";
                    $batch_query = mysqli_query($conn,$batch_sql); $batch_all = "";
                    while($row = mysqli_fetch_assoc($batch_query)){ if($batch_all == ""){ $batch_all = $row['code']; } else{ $batch_all = $batch_all."','".$row['code']; } }

                    //AND a.gc_flag = '0' has removed condition By Harish
                    //Commented from 566 to 573 by mallik and added new query from 574 to 581
                    /*$batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code  AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY age DESC";
                    $batch_query = mysqli_query($conn,$batch_sql); $i = 0; $batch_list = $batch_farm = array(); $batch1 = "";
                    while($batch_row = mysqli_fetch_assoc($batch_query)){
                        $i++;
                        $batch_list[$i] = $batch_row['batch_code'];
                        $batch_farm[$batch_row['batch_code']] = $batch_row['farm_code'];
                        if($batch1 == ""){ $batch1 = $batch_row['batch_code']; } else{ $batch1 = $batch1."','".$batch_row['batch_code']; }
                    }*/
                    $batch_sql = "SELECT * FROM `broiler_daily_record` WHERE `date` = '$till_date' AND `active` = '1'".$farm_query2." AND `dflag` = '0' ORDER BY brood_age DESC";
                    $batch_query = mysqli_query($conn,$batch_sql); $i = 0; $batch_list = $batch_farm = array(); $batch1 = "";
                    while($batch_row = mysqli_fetch_assoc($batch_query)){
                        $i++;
                        $batch_list[$i] = $batch_row['batch_code'];
                        $batch_farm[$batch_row['batch_code']] = $batch_row['farm_code'];
                        if($batch1 == ""){ $batch1 = $batch_row['batch_code']; } else{ $batch1 = $batch1."','".$batch_row['batch_code']; }
                    }
                    $sql = "SELECT * FROM `broiler_batch` WHERE 1 ".$farm_query2." AND `code` NOT IN ('$batch1')"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                       // $i++;
                       // $batch_list[$i] = $row['code'];
                        $batch_farm[$row['code']] = $row['farm_code'];
                    }
                    
                    //Bird Transfer to Processing
                    $b_list = implode("','",$batch_list);
                    $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `from_batch` IN ('$b_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_pp_sbirds = $opn_pp_sweight = $pp_sent_birds = $pp_sent_weight = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['date']."@".$row['item_code']."@".$row['from_batch'];
                        $key2 = $from_opening_date."@".$row['from_batch'];
                        if(strtotime($row['date']) < strtotime($tdate)){
                            $okey = $from_opening_date."@".$row['item_code']."@".$row['from_batch'];
                            $opn_pp_sbirds[$okey] += (float)$row['birds'];
                            $opn_pp_sweight[$okey] += (float)$row['weight'];
                            $opn_sent_birds[$key2] += (float)$row['birds'];
                        }
                        $pp_sent_birds[$key] += (float)$row['birds'];
                        $pp_sent_weight[$key] += (float)$row['weight'];
                        
                    }
                    $total_feeds_open = $total_feeds_in = $total_feed_consumed = $total_feed_stock = $total_feed_cumulate = $total_obirds = $total_mort = $total_culls = $total_lifted = $total_liftedwt = $total_bbirds = $total_medvac_qty = $slno = $display_total_cummort = $display_total_present_obirds = 0;
                    
                        
                    /*Latest Feed-In Details*/
                    $fcoa_list = implode("','",$feed_coa); $feed_list = implode("','",$feed_code); $batch2 = implode("','",$batch_list);
                    $sql = "SELECT * FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` IN ('$fcoa_list') AND `item_code` IN ('$feed_list') AND `batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch`,`date` DESC";
                    $query = mysqli_query($conn,$sql); $blentry_date = $blentry_items = array();
                    while($row = mysqli_fetch_assoc($query)){
                        if(empty($blentry_date[$row['batch']])){
                            $blentry_date[$row['batch']] = $row['date'];
                            $blentry_items[$row['batch']] = $ibrand_name[$ibrand_code[$row['item_code']]];
                        }
                        else if(strtotime($blentry_date[$row['batch']]) < strtotime($row['date'])){
                            $blentry_date[$row['batch']] = $row['date'];
                            $blentry_items[$row['batch']] = $ibrand_name[$ibrand_code[$row['item_code']]];
                        }
                    }

                    //Fetch Hatchery and Supplier Details-1
                    $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `gc_flag` = '0' AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $gbch_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $gbch_code[$row['code']] = $row['code']; }
                    $batch_hlist = implode("','",$gbch_code);
    
                    $sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql); $chick_iac = array();
                    while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; }
                    
                    $chick_coa = $icat_iac[$chick_cat];
                    $sql = "SELECT MIN(`date`) as `sdate`,MAX(`date`) as `edate` FROM `account_summary` WHERE `crdr` LIKE 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$batch_hlist') AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql); $hsdate = $hedate = "";
                    while($row = mysqli_fetch_assoc($query)){ $hsdate = $row['sdate']; $hedate = $row['edate']; }
    
                    $hatch_count = $pur_count = 0; $chkin_hcode = $chkin_vcode = array();
                    if($hsdate == "" && $hedate == ""){ }
                    else{
                        $hfdate = date("Y-m-d",strtotime($hsdate. '-3 days'));
                        $sector_list = implode("','",$sector_code);
                        $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$hfdate' AND `date` <= '$hedate' AND `icode` = '$chick_code' AND `warehouse` IN ('$sector_list') AND `active` = '1' AND `dflag` = '0'";
                        $query = mysqli_query($conn,$sql); $pur_vcode =  $pur_keyset = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['warehouse']."@".$i;
                            $pur_vcode[$key_code] = $row['vcode'];
                            $pur_keyset[$key_code] = $key_code;
                            $i++;
                        } $pur_count = sizeof($pur_vcode);
    
                        $sql_record = "SELECT * FROM `broiler_hatchentry` WHERE `hatch_date` >= '$hfdate' AND `hatch_date` <= '$hedate' AND `active` = '1' AND `dflag` = '0' ORDER BY `hatch_date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 0; $hatch_vcode =  $hatch_keyset = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['hatch_date']."@".$row['sector_code']."@".$i;
                            $hatch_vcode[$key_code] = $row['vcode'];
                            $hatch_keyset[$key_code] = $key_code;
                            $i++;
                        } $hatch_count = sizeof($hatch_vcode);
                    }
                    $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `to_batch` IN ('$batch_hlist') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 1;
                    while($row = mysqli_fetch_assoc($query)){
                        $chkin_hcode[$row['to_batch']] = $row['fromwarehouse'];
                        //Fetch Hatchery and Supplier Details-2
                        $ldate = $lsector = $lincr = "";
                        if($hatch_count > 0 && $row['code'] == $chick_code){
                            foreach($hatch_keyset as $key1){
                                $key2 = explode("@",$key1); $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                    if($ldate == ""){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                    else if(strtotime($ldate) < strtotime($hdate)){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                }
                            }
                            if($ldate == "" && $lsector == "" && $lincr == ""){ }
                            else{
                                $hkey = $ldate."@".$lsector."@".$lincr;
                                if(empty($hatch_vcode[$hkey]) || $hatch_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                else{ $chkin_vcode[$row['to_batch']] = $hatch_vcode[$hkey]; }
                            }
                        }
    
                        if(empty($chkin_vcode[$row['to_batch']]) || $chkin_vcode[$row['to_batch']] == ""){
                            if($pur_count > 0 && $row['code'] == $chick_code){
                                $ldate = $lsector = $lincr = "";
                                foreach($pur_keyset as $key1){
                                    $key2 = explode("@",$key1); $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                    if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                        if($ldate == ""){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                        else if(strtotime($ldate) < strtotime($hdate)){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                    }
                                }
                                if($ldate == "" && $lsector == "" && $lincr == ""){ }
                                else{
                                    $hkey = $ldate."@".$lsector."@".$lincr;
                                    if(empty($pur_vcode[$hkey]) || $pur_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                    else{ $chkin_vcode[$row['to_batch']] = $pur_vcode[$hkey]; }
                                }
                            }
                        }
                    }

                    $yday_date = date('Y-m-d', strtotime('-1 days', strtotime($tdate))); $pday_date = date('Y-m-d', strtotime('-2 days', strtotime($tdate)));
                    $key_code = "";  $cin_sup_code = array();
                    if($count61 > 0){

                        $sql_record = "SELECT icode,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,farm_batch,vcode FROM `broiler_purchases` WHERE  `icode` =  '$chick_code' AND `active` = '1' AND `dflag` = '0' GROUP BY farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['farm_batch'];
                                $open_pur_chicks_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                                $cin_sup_code[$row['farm_batch']] = $row['vcode'];
                                $chkin_vcode[$row['farm_batch']] = $row['vocde'];
                            }
                        }
                        $sql_record = "SELECT icode,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,farm_batch,min(date) as `date` FROM `broiler_purchases` WHERE `date` <= '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY icode,farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['icode']."@".$row['farm_batch'];

                                //if($chick_code == $row['icode']){  $open_pur_chicks_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($feed_code[$row['icode']])){ $open_pur_feeds_in_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $open_pur_medvacs_in_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }

                            }
                        }
                        $sql_record = "SELECT icode,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,farm_batch,`date` FROM `broiler_purchases` WHERE `date` = '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,icode,farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['icode']."@".$row['farm_batch'];

                                if(!empty($feed_code[$row['icode']])){ $open_pur_feeds_in_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $open_pur_medvacs_in_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                //if($chick_code == $row['icode']){ $open_pur_chicks_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                //if($chick_code == $row['icode']){ $datewise_present_pur_chicks_in__array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($feed_code[$row['icode']])){ $datewise_present_pur_feeds_in__array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $datewise_present_pur_medvacs_in__array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }

                              
                            }
                        }
                    }

                 

                    if($count91 > 0){
                        $sql_record = "SELECT to_batch,code,sum(quantity) as quantity,fromwarehouse FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `active` = '1' AND `dflag` = '0' GROUP BY code,to_batch  ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query);
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['to_batch'];
                                $open_str_chicks_in_array[$key_code] = (float)$row['quantity'];
                                $cin_sup_code[$row['to_batch']] = $row['fromwarehouse'];
                            }
                        }
                        $sql_record = "SELECT to_batch,code,sum(quantity) as quantity FROM `item_stocktransfers` WHERE `date` <= '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY code,to_batch  ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query);
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['code']."@".$row['to_batch'];

                               // if($chick_code == $row['code']){ $open_str_chicks_in_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($feed_code[$row['code']])){ $open_str_feeds_in_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($medvac_code[$row['code']])){ $open_str_medvacs_in_array[$key_code] = (float)$row['quantity']; }

                            }
                        }
                        $sql_record = "SELECT to_batch,code,sum(quantity) as quantity,date FROM `item_stocktransfers` WHERE `date` = '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,code,to_batch  ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query);
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['code']."@".$row['to_batch'];

                                if(!empty($feed_code[$row['code']])){ $open_str_feeds_in_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($medvac_code[$row['code']])){ $open_str_medvacs_in_array[$key_code] = (float)$row['quantity']; }

                                //if($chick_code == $row['code']){ $open_str_chicks_in_array[$key_code] = (float)$row['quantity']; }
                                //if($chick_code == $row['code']){ $datewise_present_str_chicks_in__array[$key_code] = (float)$row['quantity'];  }
                                if(!empty($feed_code[$row['code']])){ $datewise_present_str_feeds_in__array[$key_code] = (float)$row['quantity'] ; }
                                if(!empty($medvac_code[$row['code']])){ $datewise_present_str_medvacs_in__array[$key_code] = (float)$row['quantity'] ; }

                            }
                        }
                    }

                   // var_dump($open_str_chicks_in_array);
                    if($count18 > 0){
                        $sql_record = "SELECT sum(mortality) as mortality ,max(avg_wt) as avg_wt,sum(culls) as culls,sum(kgs1) as kgs1,sum(kgs2) as kgs2,batch_code FROM `broiler_daily_record` WHERE `date` <= '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['batch_code'];

                                $open_mort_consume_array[$key_code] = (float)$row['mortality'];
                                $open_culls_consume_array[$key_code] = (float)$row['culls'];
                                $open_feed_consume_array[$key_code] = ((float)$row['kgs1'] + (float)$row['kgs2']);
                                if($row['avg_wt'] != "" && $row['avg_wt'] > 0){
                                    $open_latest_avg_wt_array[$key_code] = $row['avg_wt'];
                                }
                                
                               

                            }
                        }
                        $sql_record = "SELECT sum(mortality) as mortality,sum(culls) as culls,sum(kgs1) as kgs1,sum(kgs2) as kgs2,batch_code FROM `broiler_daily_record` WHERE `date` = '$yday_date' AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $yd_mort[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']);
                                $yd_feed[$row['batch_code']] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                            }
                        }
                        $sql_record = "SELECT sum(mortality) as mortality,sum(culls) as culls,sum(kgs1) as kgs1,sum(kgs2) as kgs2,batch_code FROM `broiler_daily_record` WHERE `date` = '$pday_date' AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $pd_mort[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']);
                                $pd_feed[$row['batch_code']] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                            }
                        }
                        $sql_record = "SELECT MAX(date) as last_entry_Date,sum(mortality) as mortality,avg_wt,synch_flag,updatedtime,dieases_codes,remarks,sum(culls) as culls,sum(kgs1) as kgs1,sum(kgs2) as kgs2,batch_code,`date`,mort_image,feed_photos,cull_photos,addedemp,addedtime,latitude,longitude,brood_age FROM `broiler_daily_record` WHERE `date` = '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['batch_code'];

                               

                                $batchwise_last_entry_date[$row['batch_code']]  = $row['last_entry_Date'];
                                $open_mort_consume_array[$key_code] = (float)$row['mortality'];
                                $open_culls_consume_array[$key_code] = (float)$row['culls'];
                                $open_feed_consume_array[$key_code] = ((float)$row['kgs1'] + (float)$row['kgs2']);

                                $datewise_dentry_age_array[$key_code] = (float)$row['brood_age'];
                                $datewise_dentry_date_array[$key_code] = $row['date'];
                                $datewise_present_mort_consume_array[$key_code] += (float)$row['mortality'];
                                $datewise_present_culls_consume_array[$key_code] += (float)$row['culls'];
                                $datewise_present_feed_consume_array[$key_code] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                                $datewise_act_body_weight_array[$key_code] = $row['avg_wt'];
                                $datewise_remarks_array[$key_code] = $row['remarks'];
                                $datewise_dieases_codes_array[$key_code] = $row['dieases_codes'];
                                
                                if($row['avg_wt'] != "" && $row['avg_wt'] > 0){
                                    $datewise_latest_avg_wt_array[$key_code] = $row['avg_wt'];
                                }
                               
                                $datewise_mort_image_array[$key_code] = $row['mort_image'];
                                $datewise_feed_image_array[$key_code] = $row['feed_photos'];
                                $datewise_cull_image_array[$key_code] = $row['cull_photos']; 
                                $datewise_addedemp_array[$key_code] = $row['addedemp']; 
                                $datewise_addedtime_array[$key_code] = $row['addedtime'];
                                $datewise_updatedtime_array[$key_code] = $row['updatedtime'];
                                $datewise_synch_flag_array[$key_code] = $row['synch_flag']; 
                                $datewise_latitude_array[$key_code] = $row['latitude']; 
                                $datewise_longitude_array[$key_code] = $row['longitude'];

                            }
                        }
                    }

                    
                    if($count57 > 0){
                        $sql_record = "SELECT batch_code,sum(quantity) as quantity FROM `broiler_medicine_record` WHERE `date` <= '$from_date'  AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['batch_code'];

                                $open_medvacs_consume_array[$key_code] = (float)$row['quantity'];
                                
                                

                            }
                        }
                        $sql_record = "SELECT batch_code,sum(quantity) as quantity,date FROM `broiler_medicine_record` WHERE `date` = '$till_date'  AND `active` = '1' AND `dflag` = '0' GROUP BY date,batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['batch_code'];

                                $open_medvacs_consume_array[$key_code] = (float)$row['quantity'];

                                $datewise_present_medvacs_consume_array[$key_code] = (float)$row['quantity'];

                            }
                        }
                    }

                    if($count65 > 0){
                        $sql_record = "SELECT sum(birds) as birds,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,icode,farm_batch FROM `broiler_sales` WHERE `date` <= '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY icode,farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['icode']."@".$row['farm_batch'];
                                //Opening Balances
                                if($bird_code == $row['icode']){
                                    $open_birds_sale_array[$key_code] = (float)$row['birds'];
                                    $open_birdwt_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                                }
                                if(!empty($feed_code[$row['icode']])){ $open_feeds_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $open_medvacs_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }

                            }
                        }
                
                        $sql_record = "SELECT sum(birds) as birds,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,icode,farm_batch,date FROM `broiler_sales` WHERE `date` = '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,icode,farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['icode']."@".$row['farm_batch'];
                                $key2 = $from_opening_date."@".$row['farm_batch'];

                                if($bird_code == $row['icode']){
                                    $open_birds_sale_array[$key_code] = (float)$row['birds'];
                                    $open_birdwt_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                                    if(strtotime($row['date']) <= strtotime($from_opening_date)){
                                        $opn_bird_sale[$key2] += (float)$row['birds'];
                                    }
                                }
                                if(!empty($feed_code[$row['icode']])){ $open_feeds_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $open_medvacs_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }



                                //Today's Balances
                                if($bird_code == $row['icode']){
                                    $datewise_present_birds_sale_array[$key_code] += (float)$row['birds'];
                                    $datewise_present_birdwt_sale_array[$key_code] += (float)$row['rcd_qty'] + (float)$row['fre_qty'];
                                }
                                if(!empty($feed_code[$row['icode']])){ $datewise_feed_code_array[$key_code] += (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $datewise_medvac_code_array[$key_code] += (float)$row['rcd_qty'] + (float)$row['fre_qty']; }

                                
                            }
                        }
                    }

                    if($count91 > 0){
                        $sql_record = "SELECT from_batch,date,sum(quantity) as quantity,code FROM `item_stocktransfers` WHERE `date` <= '$from_date'  AND from_batch IS NOT NULL AND `active` = '1' AND `dflag` = '0' GROUP BY code,from_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['code']."@".$row['from_batch'];

                                //Opening Balances
                                if($bird_code == $row['code']){ $open_birds_trout_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($feed_code[$row['code']])){ $open_feeds_trout_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($medvac_code[$row['code']])){ $open_medvacs_trout_array[$key_code] = (float)$row['quantity']; }
                               
                            }
                        }

                        $sql_record = "SELECT from_batch,date,sum(quantity) as quantity,code,date FROM `item_stocktransfers` WHERE `date` = '$till_date'  AND from_batch IS NOT NULL  AND `active` = '1' AND `dflag` = '0' GROUP BY date,code,from_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['code']."@".$row['from_batch'];

                                  //Opening Balances
                                  if($bird_code == $row['code']){ $open_birds_trout_array[$key_code] = (float)$row['quantity']; }
                                  if(!empty($feed_code[$row['code']])){ $open_feeds_trout_array[$key_code] = (float)$row['quantity']; }
                                  if(!empty($medvac_code[$row['code']])){ $open_medvacs_trout_array[$key_code] = (float)$row['quantity']; }

                                //Today's Balances
                                if($bird_code == $row['code']){ $datewise_present_birds_trout_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($feed_code[$row['code']])){ $datewise_present_feeds_trout_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($medvac_code[$row['code']])){ $datewise_present_medvacs_trout_array[$key_code] = (float)$row['quantity']; }
                               
                            }
                        }
                        
                    }




                    $prst_pp_sbirds = $prst_pp_sweight = array(); $totp_birds = $totp_weight = 0;
                    foreach($batch_list as $batches){
                        $fetch_fcode = $batch_farm[$batches];
                        if($batches != ""){
                            $start_date = $end_date = $dend_date = $dstart_date = $mort_image = $feed_image = $addedemp = $addedtime = $latitude = $longitude = "";
                            $pur_qty = $sale_qty = $sold_birds = $trin_qty = $trout_qty = $medvac_qty = array();
                            $pur_chicks = $sale_chicks = $trin_chicks = $trout_chicks = $dentry_chicks = $medvac_chicks = array();

                            $today = date("Y-m-d",strtotime($tdate));
                            $opening_date =  date("Y-m-d",strtotime($tdate."-1 days")); $close_date = strtotime($today);
                            
                            $key_code = ""; $open_chicks_in = $open_feeds_in = $open_medvacs_in = $present_chicks_in = $present_feeds_in = $present_medvacs_in = 0;
                            
                            foreach($item_code as $items){

                                $key_code = $opening_date."@".$items."@".$batches;

                                
                        
                                //Opening Balances
                                if($chick_code == $items){  $open_chicks_in +=  $open_pur_chicks_array[$batches] + $open_str_chicks_in_array[$batches]; }
                                if(!empty($feed_code[$items])){  $open_feeds_in += $open_pur_feeds_in_array[$key_code] + $open_str_feeds_in_array[$key_code]; }
                                if(!empty($medvac_code[$items])){ $open_medvacs_in += $open_pur_medvacs_in_array[$key_code] + $open_str_medvacs_in_array[$key_code]; }

                               $key_code1 = $today."@".$items."@".$batches;

                               //Today's Balances
                                //if($chick_code == $items){ $open_chicks_in += $datewise_present_pur_feeds_in__array[$key_code1] + $datewise_present_str_feeds_in__array[$key_code1]; }
                                //if($chick_code == $items){ $present_chicks_in += (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($feed_code[$items])){   $present_feeds_in += $datewise_present_pur_feeds_in__array[$key_code1] + $datewise_present_str_feeds_in__array[$key_code1]; }
                                if(!empty($medvac_code[$items])){ $present_medvacs_in += $datewise_present_pur_medvacs_in__array[$key_code1] + $datewise_present_str_medvacs_in__array[$key_code1]; }

                           }
                            
                            $open_mort_consume  = $open_culls_consume  = $open_feed_consume  = $act_body_weight = $dentry_age = 
                            $present_mort_consume = $present_culls_consume = $present_feed_consume = $latest_avg_wt = $consumed_feeds = $open_pp_sent = $open_sale_sent = 0;
                            $remarks = $dieases_codes = "";

                            $key_code_dailyentry = $opening_date."@".$batches;
                            $key_code_dailyentry1 = $today."@".$batches;

                            //Opening Balances
                            $open_mort_consume += $open_mort_consume_array[$key_code_dailyentry];
                            $open_culls_consume += $open_culls_consume_array[$key_code_dailyentry];
                            
                            $open_pp_sent += (float)$opn_sent_birds[$key_code_dailyentry];
                            
                            $open_sale_sent += (float)$opn_bird_sale[$key_code_dailyentry];

                            $open_feed_consume += $open_feed_consume_array[$key_code_dailyentry];

                           // echo $key_code_dailyentry."//".$open_mort_consume_array[$key_code_dailyentry]."</br>";
                           
                             //Today's Balances
                             $dentry_age = $datewise_dentry_age_array[$key_code_dailyentry1];
                             $present_mort_consume += $datewise_present_mort_consume_array[$key_code_dailyentry1];
                             $present_culls_consume += $datewise_present_culls_consume_array[$key_code_dailyentry1];
                             $present_feed_consume += $datewise_present_feed_consume_array[$key_code_dailyentry1];
                            
                             $act_body_weight = $datewise_act_body_weight_array[$key_code_dailyentry1];
                             $remarks = $datewise_remarks_array[$key_code_dailyentry1];
                             if($datewise_dieases_codes_array[$key_code_dailyentry1] != ''){
                                $dieases_codes = $datewise_dieases_codes_array[$key_code_dailyentry1];
                             }else{
                                $dieases_codes = "";
                             }
                            

                            // echo $key_code_dailyentry1."//". $datewise_act_body_weight_array[$key_code_dailyentry1]."</br>";

                             if( $act_body_weight != "" &&  $act_body_weight > 0){
                                $latest_avg_wt = $datewise_latest_avg_wt_array[$key_code_dailyentry1];
                            }else if($open_latest_avg_wt_array[$key_code_dailyentry] != ""  && $open_latest_avg_wt_array[$key_code_dailyentry] > 0){
                                $latest_avg_wt = $open_latest_avg_wt_array[$key_code_dailyentry];
                            }

                            $mort_image = $datewise_mort_image_array[$key_code_dailyentry1];
                            $feed_image = $datewise_feed_image_array[$key_code_dailyentry1];
                            $cull_image = $datewise_cull_image_array[$key_code_dailyentry1]; 
                            $addedemp = $datewise_addedemp_array[$key_code_dailyentry1]; 
                            $addedtime = $datewise_addedtime_array[$key_code_dailyentry1];
                            $updatedtime = $datewise_updatedtime_array[$key_code_dailyentry1];
                            $synch_flag = $datewise_synch_flag_array[$key_code_dailyentry1]; 
                            $latitude = $datewise_latitude_array[$key_code_dailyentry1]; 
                            $longitude =  $datewise_longitude_array[$key_code_dailyentry1] ;

                            $dend_date = $datewise_dentry_date_array[$key_code_dailyentry1];

                           
                            $key_code = $medvac_names = ""; $open_medvacs_consume = $present_medvacs_consume = 0;

                            $open_medvacs_consume = (float)$open_medvacs_consume_array[$key_code_dailyentry];
                            $present_medvacs_consume = (float)$datewise_present_medvacs_consume_array[$key_code_dailyentry1];


                            
                            $key_code = "";
                            $open_birds_sale = $open_birdwt_sale = $open_feeds_sale = $open_medvacs_sale = $present_birds_sale = $present_birdwt_sale = 
                            $present_feeds_sale = $present_medvacs_sale = $prst_pp_sbirds = $prst_pp_sweight = 0;



                            foreach($item_code as $items){

                                $key_code = $opening_date."@".$items."@".$batches;
                                $key_code1 = $today."@".$items."@".$batches;
                                  //Opening Balances
                                if($bird_code == $items){
                                    $open_birds_sale += $open_birds_sale_array[$key_code];
                                    $open_birdwt_sale += $open_birdwt_sale_array[$key_code];
                                }
                                if(!empty($feed_code[$items])){ $open_feeds_sale += $open_feeds_sale_array[$key_code]; }
                                if(!empty($medvac_code[$items])){ $open_medvacs_sale += $open_medvacs_sale_array[$key_code]; }


                                 //Today's Balances
                                 if($bird_code == $items){
                                    $present_birds_sale += $datewise_present_birds_sale_array[$key_code1];
                                    $present_birdwt_sale += $datewise_present_birdwt_sale_array[$key_code1];
                                    $prst_pp_sbirds = (float)$pp_sent_birds[$key_code1];
                                    $prst_pp_sweight = (float)$pp_sent_weight[$key_code1];
                                }
                                if(!empty($feed_code[$items])){ $present_feeds_sale += $datewise_feed_code_array[$key_code1]; }
                                if(!empty($medvac_code[$items])){ $present_medvacs_sale += $datewise_medvac_code_array[$key_code1]; }

                            }
                            $totp_birds += (float)$prst_pp_sbirds; $totp_weight += (float)$prst_pp_sweight;

                            $key_code = "";
                            $open_birds_trout = $open_feeds_trout = $open_medvacs_trout = $present_birds_trout = $present_feeds_trout = $present_medvacs_trout = 0;

                            foreach($item_code as $items){

                                $key_code = $opening_date."@".$items."@".$batches;
                                $key_code1 = $today."@".$items."@".$batches;

                                 //Opening Balances
                                 if($bird_code == $items){ $open_birds_trout += $open_birds_trout_array[$key_code]; }
                                 if(!empty($feed_code[$items])){ $open_feeds_trout += $open_feeds_trout_array[$key_code]; }
                                 if(!empty($medvac_code[$items])){ $open_medvacs_trout += $open_medvacs_trout_array[$key_code]; }

                                 //Today's Balances
                                 if($bird_code == $items){ $present_birds_trout += $datewise_present_birds_trout_array[$key_code1]; }
                                 if(!empty($feed_code[$items])){ $present_feeds_trout += $datewise_present_feeds_trout_array[$key_code1]; }
                                 if(!empty($medvac_code[$items])){ $present_medvacs_trout += $datewise_present_medvacs_trout_array[$key_code1]; }
                                
                            }


                            $tot_display_yd_mort += $display_yd_mort = $yd_mort[$batches];
                            $tot_display_pd_mort += $display_pd_mort = $pd_mort[$batches];
                            $tot_display_yd_feed += $display_yd_feed = $yd_feed[$batches];
                            $tot_display_pd_feed += $display_pd_feed = $pd_feed[$batches];
                           
                            /*echo "<br/>".$pur_count = sizeof($pur_qty);
                            echo "-".$trin_count = sizeof($trin_qty);
                            echo "-".$dentry_count = sizeof($dentry_chicks);
                            echo "-".$medvac_count = sizeof($medvac_qty);
                            echo "-".$sale_count = sizeof($sale_qty);
                            echo "-".$trout_count = sizeof($trout_qty);
                            */
                            //if(date("Y-m-d",((int)$start_date)) != "1970-01-01" || date("Y-m-d",((int)$end_date)) != "1970-01-01"){
                               
                                $display_farmlatitude = $farm_latitude[$fetch_fcode];
                                $display_farmlongitude = $farm_longitude[$fetch_fcode];
                                $display_farmcode = $farm_ccode[$fetch_fcode];
                                $display_farmname = $farm_name[$fetch_fcode];
                                if(!empty($batch_name[$batches])){ $display_farmbatch_code = $batches; } else{ $display_farmbatch_code = ""; }
                                if(!empty($batch_name[$batches])){ $display_farmbatch = $batch_name[$batches]; } else{ $display_farmbatch = ""; }
                                if(!empty($batch_book[$batches])){ $display_batchbook = $batch_book[$batches]; } else{ $display_batchbook = ""; }
                                if(!empty($supervisor_name[$farm_supervisor[$fetch_fcode]])){
                                    $display_supervisor = $supervisor_name[$farm_supervisor[$fetch_fcode]];
                                }
                                else{
                                    $display_supervisor = "";
                                }
                                $display_farmer = $farmer_name[$farm_farmer[$fetch_fcode]];
                                $display_age = 0;
                                $display_age = $dentry_age;
                                //Display Feed Section
                                $display_feeds_open = (float)$open_feeds_in - (float)$open_feed_consume - (float)$open_feeds_sale - (float)$open_feeds_trout;
                               // echo (float)$open_feeds_in ."</br>".     (float)$open_feed_consume ."</br>". (float)$open_feeds_sale ."</br>". (float)$open_feeds_trout."</br>";
                                $display_feeds_in = $present_feeds_in;
                                $display_feed_consume = $present_feed_consume;
                                $display_feed_out = (float)$present_feeds_sale + (float)$present_feeds_trout;
                                $display_feed_stock = (((float)$display_feeds_open + (float)$display_feeds_in) - ((float)$display_feed_consume + (float)$display_feed_out));
                                $display_feed_cumulate = (float)$open_feed_consume + (float)$present_feed_consume;

                                if(!empty($bstd_body_weight[$display_age])){
                                    $display_stdbodyWt = ((float)$bstd_body_weight[$display_age] / 1000);
                                }
                                else{
                                    $display_stdbodyWt = 0;
                                }
                                if(!empty($bstd_fcr[$display_age])){
                                    $display_stdfcr = $bstd_fcr[$display_age];
                                }
                                else{
                                    $display_stdfcr = 0;
                                }
                                $display_bodyWt = $act_body_weight;
                                
                                $display_obirds = $open_chicks_in;
                                $display_present_obirds = (float)$open_chicks_in - ((float)$open_mort_consume + (float)$open_culls_consume + (float)$open_sale_sent + (float)$open_pp_sent + (float)$open_birds_trout);
                                //if($_SERVER['REMOTE_ADDR'] == "49.205.129.174"){
                                    //echo "<br/>(float)$open_chicks_in - ((float)$open_mort_consume + (float)$open_culls_consume + (float)$open_sale_sent + (float)$open_pp_sent + (float)$open_birds_trout)</br>";
                                //}
                                $display_mort = $present_mort_consume;
                                $display_cummort = (float)$open_mort_consume + (float)$open_culls_consume + (float)$present_mort_consume;
                                if($display_present_obirds > 0){
                                    $display_mortper = (((float)$display_mort / (float)$display_present_obirds) * 100);
                                }
                                else{
                                    $display_mortper = 0;
                                }
                                if($display_obirds > 0){
                                    $display_cummortper = (((float)$display_cummort / (float)$display_obirds) * 100);
                                }
                                else{
                                    $display_cummortper = 0;
                                }
                                
                                $client = $_SESSION['client'];
                                if(!empty($mort_image)){

                                    $mort_img_list = "";
                                    $mort_img_arr = explode(",",$mort_image);
                                    $mia_size = sizeof($mort_img_arr);
                                    foreach($mort_img_arr as $mia){
                                        if($mort_img_list == ""){
                                            $image_name_arr = explode("/",$mia);
                                            if($image_name_arr[1] == 'AndroidApp_API'){
                                                if($addedtime < "2024-07-04 00:00:00"){  
                                                    $mort_img_list = "window.open('https://broiler.poulsoft.net".$mia."');";
                                                }else{
                                                    $mort_img_list = "window.open('..".$mia."');";
                                                }
                                            }else{
                                                if($addedtime < "2024-07-04 00:00:00"){
                                                    $mort_img_list = "window.open('https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/mortimages/".$mia."');";
                                                }else{
                                                    $mort_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/mortimages/".$mia."');";
                                                }
                                            }
                                        }
                                        else{
                                            $image_name_arr = explode("/",$mia);
                                            if($image_name_arr[1] == 'AndroidApp_API'){
                                                if($addedtime < "2024-07-04 00:00:00"){  
                                                    $mort_img_list = $mort_img_list."window.open('https://broiler.poulsoft.net".$mia."');";
                                                }else{
                                                    $mort_img_list = $mort_img_list."window.open('..".$mia."');";
                                                }
                                            }else{
                                                if($addedtime < "2024-07-04 00:00:00"){
                                                    $mort_img_list = $mort_img_list."window.open('https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/mortimages/".$mia."');";
                                                }else{
                                                    $mort_img_list = $mort_img_list."window.open('../AndroidApp_API/clientimages/".$client."/mortimages/".$mia."');";
                                                }
                                            }
                                        }
                                        
                                    }

                                   // $display_mortimage = "../AndroidApp_API/clientimages/".$client."/mortimages/".$mort_image;
                                    if( $addedtime < "2024-04-20 07:21:20" ){
                                        $display_mortimage = "../AndroidApp_API/clientimages/".$client."/mortimages/".$mort_image;
                                        
                                    }else{
                                        $display_mortimage = "..".$mort_image;
                                    }
                                }
                                else{
                                    $display_mortimage = "";
                                }
                                if(!empty($feed_image)){
                                    $feed_img_list = "";
                                    $feed_img_arr = explode(",",$feed_image);
                                    $fia_size = sizeof($feed_img_arr);
                                    foreach($feed_img_arr as $fia){
                                        if($feed_img_list == ""){
                                            $image_name_arr = explode("/",$fia);
                                            if($image_name_arr[1] == 'AndroidApp_API'){
                                                if($addedtime < "2024-07-04 00:00:00"){
                                                    $feed_img_list = "window.open('https://broiler.poulsoft.net".$fia."');";
                                                }else{
                                                    $feed_img_list = "window.open('..".$fia."');";
                                                }
                                               
                                            }else{
                                                if($addedtime < "2024-07-04 00:00:00"){  
                                            
                                                    $feed_img_list = "window.open('https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/feedimages/".$fia."');";
                                                }else{
                                                    $feed_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/feedimages/".$fia."');";
                                                }
                                            }
                                            
                                        }
                                        else{
                                            $image_name_arr = explode("/",$fia);
                                            if($image_name_arr[1] == 'AndroidApp_API'){
                                                if($addedtime < "2024-07-04 00:00:00"){
                                                    $feed_img_list = $feed_img_list."window.open('https://broiler.poulsoft.net".$fia."');";
                                                }else{
                                                    $feed_img_list = $feed_img_list."window.open('..".$fia."');";
                                                }
                                            }else{
                                            
                                                if($addedtime < "2024-07-04 00:00:00"){  
                                            
                                                    $feed_img_list = $feed_img_list."window.open('https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/feedimages/".$fia."');";
                                                }else{
                                                    $feed_img_list = $feed_img_list."window.open('../AndroidApp_API/clientimages/".$client."/feedimages/".$fia."');";
                                                }
                                            }

                                        }
                                        
                                    }
                                   // $display_feedimage = "../AndroidApp_API/clientimages/".$client."/feedimages/".$feed_image;
                                   if( $addedtime < "2024-04-20 07:21:20" ){
                                        $display_feedimage = "../AndroidApp_API/clientimages/".$client."/feedimages/".$feed_image;
                                        
                                    }else{
                                        $display_feedimage = "..".$feed_image;
                                    }
                                }
                                else{
                                    $display_feedimage = "";
                                }
                                if(!empty($cull_image)){
                                    $cull_img_list = "";
                                    $cull_img_arr = explode(",",$cull_image);
                                    $cia_size = sizeof($cull_img_arr);
                                    foreach($cull_img_arr as $cia){
                                        if($cull_img_list == ""){
                                            $image_name_arr = explode("/",$cia);
                                            if($image_name_arr[1] == 'AndroidApp_API'){
                                                if($addedtime < "2024-07-04 00:00:00"){
                                                    $cull_img_list = "window.open('https://broiler.poulsoft.net".$cia."');";
                                                }else{
                                                    $cull_img_list = "window.open('..".$cia."');";
                                                }
                                            }else{
                                               
                                                if($addedtime < "2024-07-04 00:00:00"){  
                                            
                                                    $cull_img_list = "window.open('https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/cullimages/".$fia."');";
                                                }else{
                                                    $cull_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/cullimages/".$cia."');";
                                                }
                                            }
                                            
                                        }
                                        else{
                                            $image_name_arr = explode("/",$cia);
                                            if($image_name_arr[1] == 'AndroidApp_API'){
                                                if($addedtime < "2024-07-04 00:00:00"){
                                                    $cull_img_list = $cull_img_list."window.open('https://broiler.poulsoft.net".$cia."');";
                                                }else{
                                                    $cull_img_list = $cull_img_list."window.open('..".$cia."');";
                                                }
                                               
                                            }else{
                                                if($addedtime < "2024-07-04 00:00:00"){  
                                            
                                                    $cull_img_list = $cull_img_list."window.open('https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/cullimages/".$fia."');";
                                                }else{
                                                    $cull_img_list = $cull_img_list."window.open('../AndroidApp_API/clientimages/".$client."/cullimages/".$cia."');";
                                                }
                                            }
                                            

                                        }
                                        
                                    }
                                    //$display_cullimage = "../AndroidApp_API/clientimages/".$client."/cullimages/".$cull_image;
                                    if( $addedtime < "2024-04-20 07:21:20" ){
                                        $display_cullimage = "../AndroidApp_API/clientimages/".$client."/cullimages/".$cull_image;
                                        
                                    }else{
                                        $display_cullimage = "..".$cull_image;
                                    }

                                }
                                else{
                                    $display_cullimage = "";
                                }
                                //$display_culls = $open_culls_consume + $present_culls_consume;
                                $display_culls = $present_culls_consume;
                                $display_lifted = (float)$open_birds_sale + (float)$present_birds_sale;
                                $display_liftedwt = (float)$open_birdwt_sale + (float)$present_birdwt_sale;
                                $display_bbirds = (float)$display_obirds - (float)$display_cummort - (float)$display_culls - (float)$display_lifted - (float)$open_sale_sent - (float)$open_pp_sent - (float)$prst_pp_sbirds;
                                $display_medvacname = $medvac_names;
                                $display_remarks = $remarks;

                                if($display_bbirds != 0 || $display_bbirds != "0") { $display_dayfeed_intake = $display_feed_consume / $display_bbirds; }
                                if($display_bbirds != 0 || $display_bbirds != "0") { $display_cumfeed_intake = $display_feed_cumulate / $display_bbirds; }
                                
                                $display_medvacqty = (float)$open_medvacs_consume + (float)$present_medvacs_consume;

                                $consumed_feeds = (float)$open_feed_consume + (float)$present_feed_consume;
                                $sales_birds_qty = (float)$open_birdwt_sale + (float)$present_birdwt_sale;
                                $sales_birds_nos = (float)$open_birds_sale + (float)$present_birds_sale;
                                if($sales_birds_nos > 0){
                                    $display_availableavg_body_wt = ((float)$sales_birds_qty / (float)$sales_birds_nos);
                                }
                                else{
                                    //if($latest_avg_wt > 0){ $display_availableavg_body_wt = ((float)$latest_avg_wt / 1000); } else{ $display_availableavg_body_wt = 0; }
                                    $display_availableavg_body_wt = 0;
                                }
                                

                                $display_std_feed_perbird = $display_act_feed_perbird = $display_actcum_feed_perbird = 0;
                                $page = 0; $page = round($display_age);
                                if(!empty($bstd_feed_consumed[$page]) && $page > 0){ $display_std_feed_perbird = number_format_ind($bstd_feed_consumed[$page]); }
                                else{ $display_std_feed_perbird = 0; }

                                if($display_present_obirds > 0){
                                    $display_act_feed_perbird = number_format_ind((((float)$display_feed_consume * 1000) / (float)$display_present_obirds));
                                }
                                else{
                                    $display_act_feed_perbird = 0;
                                }
                                
                                if($display_obirds > 0){
                                    $display_actcum_feed_perbird = number_format_ind((((float)$display_feed_cumulate * 1000) / (float)$display_obirds));
                                }
                                else{
                                    $display_actcum_feed_perbird = 0;
                                }
                                
                                $fcr_title = "";
                                if($sales_birds_qty > 0) {
                                    $display_fcr = ((float)$consumed_feeds / (float)$sales_birds_qty);
                                    $fcr_title = "$display_fcr = ((float)$consumed_feeds / (float)$sales_birds_qty);";
                                }
                                else if($latest_avg_wt > 0 && $display_present_obirds > 0) {
                                    $display_fcr = ((float)$consumed_feeds / ((float)$display_present_obirds * ((float)$latest_avg_wt / 1000)));
                                    $fcr_title = "$display_fcr = ((float)$consumed_feeds / ((float)$display_present_obirds * ((float)$latest_avg_wt / 1000)));";
                                }
                                else{
                                    $display_fcr = 0;
                                }
                                if($display_availableavg_body_wt > 0){
                                    $display_cfcr = (((2 - ((float)$display_availableavg_body_wt)) / 4) + (float)$display_fcr);
                                }
                                else if($latest_avg_wt > 0 && $display_present_obirds > 0){
                                    $display_cfcr = (((2 - ((float)$latest_avg_wt / 1000)) / 4) + (float)$display_fcr);
                                }
                                else{
                                    $display_cfcr = 0;
                                }

                                $display_line = $line_name[$farm_line[$fetch_fcode]];
                                $display_place = $branch_name[$farm_branch[$fetch_fcode]];
                                $display_contact = $farmer_mobile1[$farm_farmer[$fetch_fcode]];
                                $display_addedemp = $addedemp; $display_addedtime = $addedtime;
                                if(!empty($display_farmlatitude) && !empty($display_farmlongitude)){
                                    /*  $display_farm_location = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$display_farmlatitude.",".$display_farmlongitude."&key=AIzaSyCQO_zZX9F0UzrOzCYsXRAAbhwjhSSXWaw";*/
                                    $display_farm_location = "/records/ShowLocation.php?lat=".$display_farmlatitude."&lng=".$display_farmlongitude."&farm_name=".$display_farmname."&type=Farm Location";
                                }
                                else{
                                    $display_farm_location = "";
                                }
                                if(!empty($latitude) && !empty($longitude)){
                                    /*$display_entry_location = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=AIzaSyCQO_zZX9F0UzrOzCYsXRAAbhwjhSSXWaw";*/
                                    $display_entry_location = "/records/ShowLocation.php?lat=".$latitude."&lng=".$longitude."&farm_name=".$display_farmname."&type=Daily Entry Farm Location";
                                }
                                else{
                                    $display_entry_location = "";
                                }
                                if(!empty($display_farmlatitude) && $display_farmlatitude != 0.0 && !empty($display_farmlongitude) && $display_farmlongitude != 0.0 && !empty($latitude) && $latitude != 0.0 && !empty($longitude) && $longitude != 0.0 ){
                                    $display_differ_location =  computeDistance($display_farmlatitude,$display_farmlongitude,$latitude,$longitude)."";
                                        $display_differ_location_link = "/records/ShowDirection.php?lat1=".$display_farmlatitude."&lng1=".$display_farmlongitude."&lat2=".$latitude."&lng2=".$longitude."&farm_name=".$display_farmname."&type=Daily Entry Farm Location";
                                }else{
                                    $display_differ_location = "";
                                    $display_differ_location_link = "";
                                }

                               
                                if(date("d.m.Y",strtotime($dend_date)) != "01.01.1970" && $dend_date == $tdate){
                                    
                                    if($display_obirds > 0 || $present_chicks_in > 0 || $display_feeds_open > 0 || $display_feeds_in > 0 || $display_feed_stock > 0){ $slno++;
                                        
                                        $total_feeds_open += (float)$display_feeds_open;
                                        $total_feeds_in += (float)$display_feeds_in;
                                        $total_feed_consumed += (float)$display_feed_consume;
                                        $total_dayfeed_intake += (float)$display_dayfeed_intake;
                                        $total_feed_stock += (float)$display_feed_stock;
                                        $total_feed_cumulate += (float)$display_feed_cumulate;
                                        $total_cumfeed_intake += (float)$display_cumfeed_intake;
                                        $total_obirds += (float)$display_obirds;
                                        $display_total_present_obirds += (float)$display_present_obirds;
                                        $total_mort += (float)$display_mort;
                                        $total_culls += (float)$display_culls;
                                        $total_lifted += (float)$display_lifted;
                                        $total_liftedwt += (float)$display_liftedwt;
                                        $total_bbirds += (float)$display_bbirds;
                                        $total_medvac_qty += (float)$display_medvacqty;
                                        $display_total_cummort += (float)$display_cummort;

                                        if($dieases_codes != ""){
                                            $str_arr = explode (",", $dieases_codes); 
                                    
                                            for($a = 0;$a<count($str_arr);$a++){
                                                if($a == 0){
                                                    if(!empty($dieases_name[$str_arr[$a]])){
                                                        $display_dieases_codes = $dieases_name[$str_arr[$a]];
                                                    }
                                                }
                                                else{
                                                    if(!empty($dieases_name[$str_arr[$a]])){
                                                        $display_dieases_codes .= ",".$dieases_name[$str_arr[$a]];
                                                    }
                                                }
                                            
                                            }
                                        }else{
                                            $display_dieases_codes = "";
                                        }

                                        
                                        echo "<tr>";
                                        for($i = 1;$i <= $col_count;$i++){
                                            $key_id = "A:1:".$i;
                                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No.'>".$slno."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<td title='Farm Code'>".$display_farmcode."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farmer'>".$display_farmname."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code"){ echo "<td title='Batch'>".$display_farmbatch_code."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<td title='Batch'>".$display_farmbatch."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<td title='Book No'>".$display_batchbook."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor'>".$display_supervisor."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){
                                                if(date("d.m.Y",strtotime($dend_date)) == "01.01.1970"){ echo "<td title='Age'></td>"; }
                                                else{ echo "<td title='Age' style='text-align:center;'>".round($display_age)."</td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<td title='Placed Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_obirds,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno"){ echo "<td title='Opening Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_present_obirds,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_mort"){ echo "<td title='Mort' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_pd_mort,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_mort"){ echo "<td title='Mort' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_yd_mort,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<td title='Mort' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_mort,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ 
                                                if(number_format_ind(round($display_mortper,2)) > 0.20){
                                                    echo "<td title='Mort%' style='text-align:right;color:#f44336;font-size:15px;'><b>".number_format_ind(round($display_mortper,2))."</b></td>"; 
                                                }else{
                                                    echo "<td title='Mort%' style='text-align:right;'>".number_format_ind(round($display_mortper,2))."</td>"; 
                                                }
                                                
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_img"){
                                                if(!empty($display_mortimage)){ ?><td style="text-align:right;" title="Mort Image"><a href="javascript:void(0)" onClick="<?php echo $mort_img_list; ?>" title="<?php echo $mort_img_list; ?>">mortImage-<?php echo $slno; ?></a></td><?php }
                                                else{ echo "<td title='Mort Image'></td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum"){ echo "<td title='Cum Mort' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_cummort,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum_per"){ echo "<td title='Cum Mort%' style='text-align:right;'>".number_format_ind(round($display_cummortper,2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ echo "<td title='Culls' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_culls,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_img"){
                                                if(!empty($display_cullimage)){ ?> <td title="Cull Image"><a href="javascript:void(0)" onClick="<?php echo $cull_img_list; ?>" title="<?php echo $cull_img_list; ?>">cullImage-<?php echo $slno; ?></a></td><?php }
                                                else{ echo "<td title='Cull Image'></td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Sold' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_lifted,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Sold Wt' style='text-align:right;'>".number_format_ind(round($display_liftedwt,2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ echo "<td title='Balance Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_bbirds,2)))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ echo "<td title='Std B.Wt' style='text-align:right;'>".number_format_ind(round($display_stdbodyWt,2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){
                                                if(number_format_ind($display_bodyWt) == "0.00"){ echo "<td style='text-align:right;color:black;' title='Body Weight'>".number_format_ind(round($display_bodyWt / 1000,2))."</td>"; }
                                                else if((float)$display_bodyWt >= (float)$display_stdbodyWt){
                                                    echo "<td style='text-align:right;color:red;' title='Body Weight'>".number_format_ind(round($display_bodyWt / 1000,3))."</td>";
                                                }
                                                else{
                                                    echo "<td style='text-align:right;color:green;' title='Body Weight'>".number_format_ind(round($display_bodyWt / 1000,3))."</td>";
                                                }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<td title='Std FCR' style='text-align:right;'>".number_format_ind(round($display_stdfcr,2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){
                                                if($display_stdfcr < $display_fcr){ echo "<td title='$fcr_title' style='text-align:right;color:red;'>".(round($display_fcr,3))."</td>"; }
                                                else if(number_format_ind($display_fcr) == "0.00"){ echo "<td title='$fcr_title' style='text-align:right;color:black;'>".(round($display_fcr,3))."</td>"; }
                                                else { echo "<td title='$fcr_title' style='text-align:right;color:green;'>".(round($display_fcr,3))."</td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<td title='CFCR' style='text-align:right;'>".(round($display_cfcr,3))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count"){ echo "<td title='Feed OB' style='text-align:right;'>".number_format_ind(round(($display_feeds_open),2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count"){ echo "<td title='Feed In' style='text-align:right;'>".number_format_ind(round(($display_feeds_in),2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count"){ echo "<td title='Feed Out' style='text-align:right;'>".number_format_ind(round(($display_feed_out),2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ if($pound_flag > 0){ echo "<td title='Day Con' style='text-align:right;'>".number_format_ind(round(($display_feed_consume),2))."</td>";}else { echo "<td title='Feed Con' style='text-align:right;'>".number_format_ind(round(($display_feed_consume),2))."</td>"; }}
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_feedintake"){  echo "<td title='Day Con' style='text-align:right;'>".number_format_ind(round(($display_dayfeed_intake),2))."</td>";}
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ if($pound_flag > 0){ echo "<td title='Feed Closing' style='text-align:right;'>".number_format_ind(round(($display_feed_stock),2))."</td>";}else{ echo "<td title='Feed Stock' style='text-align:right;'>".number_format_ind(round(($display_feed_stock),2))."</td>"; }}
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count"){ echo "<td title='Cum. Feed' style='text-align:right;'>".number_format_ind(round(($display_feed_cumulate),2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cum_feedintake"){ echo "<td title='Cum. Feed In Take' style='text-align:right;'>".number_format_ind(round(($display_cumfeed_intake),2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags"){ echo "<td title='Cum. Feed' style='text-align:right;'>".number_format_ind(round(($display_feed_consume/50),2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img"){
                                                if(!empty($display_feedimage)){ ?><td title="Feed Images"><a href="javascript:void(0)" onClick="<?php echo $feed_img_list; ?>" title="<?php echo $feed_img_list; ?>">feedImage-<?php echo $slno; ?></a></td><?php }
                                                else{ echo "<td title='Feed Images'></td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_feed"){ echo "<td title='Mort' style='text-align:right;'>".number_format_ind(round($display_pd_feed,2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_feed"){ echo "<td title='Mort' style='text-align:right;'>".number_format_ind(round($display_yd_feed,2))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<td title='Line'>".$display_line."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<td title='Branch'>".$display_place."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1"){ echo "<td title='Farmer Contact'>".$display_contact."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedtime"){ echo "<td title='Entry Time'>".date('d.m.Y H:i:s A',strtotime($display_addedtime))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedemp"){ echo "<td title='Entry By'>".$supervisor_name[$db_emp_code[$display_addedemp]]."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "remakrs"){ echo "<td title='Remarks'>".$display_remarks."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "dieases_name"){ echo "<td title='Dieases Names'>".$display_dieases_codes."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_location"){
                                                if(!empty($display_farm_location)){ echo "<td title='Farm Location'><a href='".$display_farm_location."' target='_BLANK'>Location-".$slno."</a></td>"; }
                                                else{ echo "<td title='Farm Location'></td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "entry_location"){
                                                if(!empty($display_entry_location)){ echo "<td title='Entry Location'><a href='".$display_entry_location."' target='_BLANK'>Location-".$slno."</a></td>"; }
                                                else{ echo "<td title='Entry Location'></td>"; }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "difference_kms"){
                                                if(!empty($display_differ_location)){ echo "<td title='Difference K.M'><a href='".$display_differ_location_link."' target='_BLANK'>".$display_differ_location."</a></td>"; }
                                                else{ echo "<td title='Difference K.M'></td>"; }
                                            }
                    
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){ echo "<td title='Std Feed/Bird' style='text-align:right;'>".$display_std_feed_perbird."</td>"; } 
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){
                                                if($display_act_feed_perbird > $display_std_feed_perbird){
                                                    echo "<td title='Feed Feed/Bird' style='text-align:right;color:red;font-size:15px;'><b>".$display_act_feed_perbird."</b></td>"; 
                                                }else{
                                                    echo "<td title='Feed Feed/Bird' style='text-align:right;'>".$display_act_feed_perbird."</td>"; 
                                                }
                                                 
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_cumfeed_perbirdno"){ echo "<td title='Feed Feed/Bird' style='text-align:right;'>".$display_actcum_feed_perbird."</td>"; }
                                            
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ 
                                                if(!empty($sector_name[$cin_sup_code[$batches]])){
                                                    echo "<td title='Sold Weight' style='text-align:left;'>".$sector_name[$cin_sup_code[$batches]]."</td>"; 
                                                }else{
                                                    echo "<td title='Sold Weight' style='text-align:left;'></td>"; 
                                                }
                                            }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand"){ echo "<td title='Feed Feed/Bird' style='text-align:left;'>".$blentry_items[$batches]."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<td title='Feed Feed/Bird' style='text-align:left;'>".$sector_name[$chkin_hcode[$batches]]."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<td title='Feed Feed/Bird' style='text-align:left;'>".$sector_name[$chkin_vcode[$batches]]."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_birds"){ echo "<td title='T. Birds' style='text-align:left;'>".str_replace('.00','',number_format_ind($prst_pp_sbirds))."</td>"; }
                                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_weight"){ echo "<td title='T. Weight' style='text-align:left;'>".number_format_ind($prst_pp_sweight)."</td>"; }
                                        }
                                        echo "</tr>";
                                    }
                                }
                            //}
                        }
                    }
                ?>
            </tbody>
            <tfoot>
                <?php
                echo "<tr class='thead4'>";
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th style='text-align:left; border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_code"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<th style='text-align:center; border-left: 0px;'>Total</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_obirds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($display_total_present_obirds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_mort"){ echo "<th style='text-align:right;'>".str_replace('.00','',$tot_display_pd_mort)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_mort"){ echo "<th style='text-align:right;'>".str_replace('.00','',$tot_display_yd_mort)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th style='text-align:right;'>".str_replace('.00','',$total_mort)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){
                        if($total_mort > 0 && $display_total_present_obirds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(round((($total_mort / $display_total_present_obirds) * 100),2))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_img"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($display_total_cummort))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum_per"){
                        if($display_total_cummort > 0 && $total_obirds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(round((($display_total_cummort / $total_obirds ) * 100),2))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_culls))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_img"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_lifted))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th style='text-align:right;'>".number_format_ind($total_liftedwt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_bbirds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feeds_open)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feeds_in)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count"){ echo "<th style='text-align:right;'>".number_format_ind($display_feed_out)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_consumed)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_feedintake"){ echo "<th style='text-align:right;'>".number_format_ind($total_dayfeed_intake)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_bags"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_consumed/50)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_stock)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_cumulate)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cum_feedintake"){ echo "<th style='text-align:right;'>".number_format_ind($total_cumfeed_intake)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "previous_day_feed"){ echo "<th style='text-align:right;'>".number_format_ind($tot_display_pd_feed)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "yesturday_feed"){ echo "<th style='text-align:right;'>".number_format_ind($tot_display_yd_feed)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedtime"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedemp"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "remakrs"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "dieases_name"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_location"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "entry_location"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "difference_kms"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){ echo "<th style='text-align:left;'></th>"; } 
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_cumfeed_perbirdno"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "latest_feedin_brand"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_birds"){ echo "<th style='text-align:left;'>".str_replace(".00","",number_format_ind($totp_birds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pp_sent_weight"){ echo "<th style='text-align:left;'>".number_format_ind($totp_weight)."</th>"; }
                    else{ }
                }
                echo "</tr>";
                ?>
            </tfoot>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script>
            function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
      
        </script>
        <script>
            function table_sort() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    cdate_format1();
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }


              function fetch_farms_details(a){
                var regions = document.getElementById("regions").value;
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;
                var user_code = '<?php echo $user_code; ?>';
                var rf_flag = bf_flag = lf_flag = sf_flag = ff_flag = 0;
                if(a.match("regions")){ rf_flag = 1; } else if(a.match("branches")){ bf_flag = 1; } else if(a.match("lines")){ lf_flag = 1; } else if(a.match("supervisors")){ sf_flag = 1; } else{ ff_flag = 1; }
                    
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_fetch_farm_filter_master.php?regions="+regions+"&branches="+branches+"&lines="+lines+"&supervisors="+supervisors+"&rf_flag="+rf_flag+"&bf_flag="+bf_flag+"&lf_flag="+lf_flag+"&sf_flag="+sf_flag+"&ff_flag="+ff_flag+"&user_code="+user_code;
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var brnh_list = fltr_dt2[3];
                        var line_list = fltr_dt2[0];
                        var supr_list = fltr_dt2[1];
                        var farm_list = fltr_dt2[2];

                        if(rf_flag == 1){
                            removeAllOptions(document.getElementById("branches"));
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#branches').append(brnh_list);
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(lf_flag == 1){
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("farms"));
                            $('#farms').append(farm_list);
                        }
                        else{ }
                    }
                }
            }
            var f_cnt = 0;
            function set_auto_selectors(){
                if(f_cnt == 0){
                    var fx = "regions"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 1){
                    var br_val = brlist = "";
                    $('#branches').select2();
                    for(var option of document.getElementById("branches").options){
                        option.selected = false;
                        br_val = option.value;
                        brlist = ''; brlist = '<?php echo $branches; ?>';
                        if(br_val == brlist){ option.selected = true; }
                    }
                    $('#branches').select2();
                    var fx = "branches"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    var l_val = llist = "";
                    $('#lines').select2();
                    for(var option of document.getElementById("lines").options){
                        option.selected = false;
                        l_val = option.value;
                        llist = ''; llist = '<?php echo $lines; ?>';
                        if(l_val == llist){ option.selected = true; }
                    }
                    $('#lines').select2();
                    var fx = "lines"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    var s_val = slist = "";
                    $('#supervisors').select2();
                    for(var option of document.getElementById("supervisors").options){
                        option.selected = false;
                        s_val = option.value;
                        slist = ''; slist = '<?php echo $supervisors; ?>';
                        if(s_val == slist){ option.selected = true; }
                    }
                    $('#supervisors').select2();
                    var fx = "supervisors"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    var f_val = flist = "";
                    $('#farms').select2();
                    for(var option of document.getElementById("farms").options){
                        option.selected = false;
                        f_val = option.value;
                        flist = ''; flist = '<?php echo $farms; ?>';
                        if(f_val == flist){ option.selected = true; }
                    }
                    $('#farms').select2();
                    var fx = "farms"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 4){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
        </script>
        <script>
        function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            </script>
    </body>
</html>
<?php
include "header_foot.php";
?>