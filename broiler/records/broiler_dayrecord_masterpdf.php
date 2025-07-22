<?php
//broiler_dayrecord_masterpdf.php
$requested_data = json_decode(file_get_contents('php://input'),true);

if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Day Record Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Day Record Report";
    include "header_head.php";
    $user_code = $_GET['userid'];
}
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
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

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); $user_code = $_SESSION['userid'];
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = "";
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
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
//for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; echo "<br/>".$act_col_numbs[$key_id]; }


$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code']; $farm_latitude[$row['code']] = $row['latitude']; $farm_longitude[$row['code']] = $row['longitude'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; $bstd_feed_consumed[$row['age']] = $row['feed_consumed']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }

$sql = "SELECT * FROM `broiler_diseases`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $dieases_name[$row['trnum']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $supervisors = $farms = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));

    $farm_list = "";
    if($farms != "all"){
        $farm_query = " AND a.farm_code = '$farms'";
    }
    else if($supervisors != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else{
        foreach($farm_code as $fcode){
            if($farm_list == ""){
                $farm_list = $fcode;
            }
            else{
                $farm_list = $farm_list."','".$fcode;
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/DayRecordReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors."&farm=".$farms;
}
else{
    $url = "";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <!-- Datatable CSS 
        <link href='../../col/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>-->

        <!-- jQuery Library -->
        <script src="../../col/jquery-3.5.1.js"></script>
        
        <!-- Datatable JS -->
        <script src="../../col/jquery.dataTables.min.js"></script>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
            table thead,
            table tfoot {
                position: sticky;
            }
            table thead {
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="38" align="center"><?php echo $row['cdetails']; ?><h5>Day Record Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="../print/Examples/broiler_daily_record_sendpdf1.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                    <th colspan="39">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
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
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td>
                <div id='control_sh'>
                    <?php
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if($act_col_numbs[$key_id] == "sl_no" || $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No.</span>'; }
                            else if($act_col_numbs[$key_id] == "farm_ccode" || $nac_col_numbs[$key_id1] == "farm_ccode"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_ccode" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Code</span>'; }
                            else if($act_col_numbs[$key_id] == "farm_name" || $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer</span>'; }
                            else if($act_col_numbs[$key_id] == "batch_name" || $nac_col_numbs[$key_id1] == "batch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch</span>'; }
                            else if($act_col_numbs[$key_id] == "book_no" || $nac_col_numbs[$key_id1] == "book_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Book No</span>'; }
                            else if($act_col_numbs[$key_id] == "supervisor_name" || $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
                            else if($act_col_numbs[$key_id] == "brood_age" || $nac_col_numbs[$key_id1] == "brood_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Age</span>'; }
                            else if($act_col_numbs[$key_id] == "chick_placed" || $nac_col_numbs[$key_id1] == "chick_placed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_placed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Placed Birds</span>'; }
                            else if($act_col_numbs[$key_id] == "opening_birdsno" || $nac_col_numbs[$key_id1] == "opening_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="opening_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Opening Birds</span>'; }
                            else if($act_col_numbs[$key_id] == "mortality_count" || $nac_col_numbs[$key_id1] == "mortality_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort</span>'; }
                            else if($act_col_numbs[$key_id] == "mortality_per" || $nac_col_numbs[$key_id1] == "mortality_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort%</span>'; }
                            else if($act_col_numbs[$key_id] == "mortality_img" || $nac_col_numbs[$key_id1] == "mortality_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort Image</span>'; }
                            else if($act_col_numbs[$key_id] == "mortality_cum" || $nac_col_numbs[$key_id1] == "mortality_cum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_cum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum Mort</span>'; }
                            else if($act_col_numbs[$key_id] == "mortality_cum_per" || $nac_col_numbs[$key_id1] == "mortality_cum_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_cum_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum Mort%</span>'; }
                            else if($act_col_numbs[$key_id] == "culls_count" || $nac_col_numbs[$key_id1] == "culls_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="culls_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Culls</span>'; }
                            else if($act_col_numbs[$key_id] == "culls_img" || $nac_col_numbs[$key_id1] == "culls_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="culls_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cull Image</span>'; }
                            else if($act_col_numbs[$key_id] == "sold_birdsno" || $nac_col_numbs[$key_id1] == "sold_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold</span>'; }
                            else if($act_col_numbs[$key_id] == "sold_birdswt" || $nac_col_numbs[$key_id1] == "sold_birdswt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdswt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold Wt</span>'; }
                            else if($act_col_numbs[$key_id] == "available_birds" || $nac_col_numbs[$key_id1] == "available_birds"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="available_birds" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Balance Birds</span>'; }
                            else if($act_col_numbs[$key_id] == "std_bodywt" || $nac_col_numbs[$key_id1] == "std_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std B.Wt</span>'; }
                            else if($act_col_numbs[$key_id] == "avg_bodywt" || $nac_col_numbs[$key_id1] == "avg_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="avg_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg B.Wt</span>'; }
                            else if($act_col_numbs[$key_id] == "std_fcr" || $nac_col_numbs[$key_id1] == "std_fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std FCR</span>'; }
                            else if($act_col_numbs[$key_id] == "fcr" || $nac_col_numbs[$key_id1] == "fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>FCR</span>'; }
                            else if($act_col_numbs[$key_id] == "cfcr" || $nac_col_numbs[$key_id1] == "cfcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cfcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>CFCR</span>'; }
                            else if($act_col_numbs[$key_id] == "feedopening_count" || $nac_col_numbs[$key_id1] == "feedopening_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedopening_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed OB</span>'; }
                            else if($act_col_numbs[$key_id] == "feedin_count" || $nac_col_numbs[$key_id1] == "feedin_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedin_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed In</span>'; }
                            else if($act_col_numbs[$key_id] == "feedout_count" || $nac_col_numbs[$key_id1] == "feedout_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedout_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Out</span>'; }
                            else if($act_col_numbs[$key_id] == "feedconsumed_count" || $nac_col_numbs[$key_id1] == "feedconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Con</span>'; }
                            else if($act_col_numbs[$key_id] == "feed_balance_count" || $nac_col_numbs[$key_id1] == "feed_balance_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_balance_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Stock</span>'; }
                            else if($act_col_numbs[$key_id] == "feedcumconsumed_count" || $nac_col_numbs[$key_id1] == "feedcumconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedcumconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum. Feed</span>'; }
                            else if($act_col_numbs[$key_id] == "feed_img" || $nac_col_numbs[$key_id1] == "feed_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Images</span>'; }
                            else if($act_col_numbs[$key_id] == "line_name" || $nac_col_numbs[$key_id1] == "line_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Line</span>'; }
                            else if($act_col_numbs[$key_id] == "branch_name" || $nac_col_numbs[$key_id1] == "branch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch</span>'; }
                            else if($act_col_numbs[$key_id] == "mobile_no1" || $nac_col_numbs[$key_id1] == "mobile_no1"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mobile_no1" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Contact</span>'; }
                            else if($act_col_numbs[$key_id] == "addedtime" || $nac_col_numbs[$key_id1] == "addedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="addedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry Time</span>'; }
                            else if($act_col_numbs[$key_id] == "addedemp" || $nac_col_numbs[$key_id1] == "addedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="addedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry By</span>'; }
                            else if($act_col_numbs[$key_id] == "remakrs" || $nac_col_numbs[$key_id1] == "remakrs"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="remakrs" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Remarks</span>'; }
                            else if($act_col_numbs[$key_id] == "dieases_name" || $nac_col_numbs[$key_id1] == "dieases_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="dieases_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Dieases Names</span>'; }
                            else if($act_col_numbs[$key_id] == "farm_location" || $nac_col_numbs[$key_id1] == "farm_location"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_location" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Location</span>'; }
                            else if($act_col_numbs[$key_id] == "entry_location" || $nac_col_numbs[$key_id1] == "entry_location"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="entry_location" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry Location</span>'; }
                            else if($act_col_numbs[$key_id] == "difference_kms" || $nac_col_numbs[$key_id1] == "difference_kms"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="difference_kms" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Diff KM(mts)</span>'; }
                            else if($act_col_numbs[$key_id] == "std_feed_perbirdno" || $nac_col_numbs[$key_id1] == "std_feed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Feed/Bird</span>'; }
                            else if($act_col_numbs[$key_id] == "actual_feed_perbirdno" || $nac_col_numbs[$key_id1] == "actual_feed_perbirdno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_perbirdno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act Feed/Bird</span>'; }
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
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
    </body>
</html>
<?php
include "header_foot.php";
?>