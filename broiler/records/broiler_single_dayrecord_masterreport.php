<?php
//broiler_dayrecord_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase']  = $_GET['db']; } else { $db = ''; }
if($db == ''){
    include "../newConfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Single Day Record Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Single Day Record Report";
    include "header_head.php";
    $user_code = $_GET['userid'];
}
$file_name = "Day Record Report";
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

//for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; echo "<br/>".$act_col_numbs[$key_id]; }
$branch_code = $branch_name = array();
if($count93 > 0){
$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }
}
$line_code = $line_name = array();
if($count94 > 0){
$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description'];$line_branch[$row['code']] = $row['branch_code']; }
}
$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_farmer = $farm_latitude = $farm_longitude = array();
if($count26 > 0){
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code']; $farm_latitude[$row['code']] = $row['latitude']; $farm_longitude[$row['code']] = $row['longitude'];
}
}
$batch_code = $batch_name = $batch_book = $batch_gcflag = array();
if($count12 > 0){
$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_farm[$row['code']] = $row['farm_code'];$batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }
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
$chick_code = "";
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
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
$item_cat = "";
if($count87 > 0){
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
}
$feed_code = array();
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

 $branches = $lines = $supervisors = $supervisors = $farms = "all"; $excel_type = "display";
if(isset($_REQUEST['submit_report']) == true){
  
    $farms = $_REQUEST['farms'];
    $batch = $_REQUEST['batch'];
   
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_dayrecord_masterreport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors."&farm=".$farms."&href=".$field_href[0];
}
else{
    $url = "";
}


 $tblcol_size = sizeof($act_col_numbs);
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <!-- Datatable CSS 
        <link href='../../col/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>-->
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
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead3" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="<?php echo $tblcol_size - 2; ?>" align="center"><?php echo $row['cdetails']; ?><h5>Day Record Report</h5></th>
                </tr>
                <?php } ?>
            <?php if($excel_type == "print"){ } else{ ?></thead>
            <?php if($db == ''){?>
            <form action="broiler_single_dayrecord_masterreport.php" method="post"  >
            <?php } else { ?>
            <form action="broiler_single_dayrecord_masterreport.php?db=<?php echo $db; ?>&userid=<?php echo $user_code; ?>" method="post">
            <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                    <th colspan="<?php echo $tblcol_size; ?>">
                            <div class="row">
                               <!--- <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>--->
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2"  onchange="fetch_farms_details1(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2"  onchange="fetch_farms_details1(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                               <!--- <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2"  onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> --->
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2"  onchange="fetch_farms_details()">
    
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Batch</label>
                                    <select name="batch" id="batch" class="form-control select2">
                                        <?php foreach($batch_code as $batchcode){ if($batch_name[$batchcode] != ""){ ?>
                                        <option value="<?php echo $batchcode; ?>" <?php if($batch == $batchcode){ echo "selected"; } ?>><?php echo $batch_name[$batchcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
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
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td colspan="<?php echo $tblcol_size; ?>">
                <div id='control_sh'>
                    <?php
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_ccode"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_ccode" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Code</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "batch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "book_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Book No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "brood_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="entry_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_placed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_placed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Placed Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "opening_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="opening_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Opening Birds</span>'; }
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
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Con</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_balance_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_balance_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Stock</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedcumconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedcumconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cum. Feed</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_img"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_img" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Images</span>'; }
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
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table id="main_table" class="tbl" align="center"  style="width:1300px;">
            <?php  
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';
            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';
            /*?>
            <thead class="thead3" align="center" id="head_names" style="width:1212px;"><?php }
           
            */
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ $nhtml .= '<th>Farm Code</th>'; $fhtml .= '<th id="order">Farm Code</th>';  }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ $nhtml .= '<th>Farmer</th>'; $fhtml .= '<th id="order">Farmer</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ $nhtml .= '<th>Batch</th>'; $fhtml .= '<th id="order">Batch</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ $nhtml .= '<th>Book No</th>'; $fhtml .= '<th id="order">Book No</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ $nhtml .= '<th>Supervisor</th>'; $fhtml .= '<th id="order">Supervisor</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "date"){ $nhtml.= "<th>Entry Date</th>"; $fhtml .= '<th id="order_date">Entry Date</th>';}
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ $nhtml .= '<th>Placed Birds</th>'; $fhtml .= '<th id="order_num">Placed Birds</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno"){ $nhtml .= '<th>Opening Birds</th>'; $fhtml .= '<th id="order_num">Opening Birds</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ $nhtml .= '<th>Mort</th>'; $fhtml .= '<th id="order_num">Mort</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ $nhtml .= '<th>Mort%</th>'; $fhtml .= '<th id="order_num">Mort%</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_img"){ $nhtml .= '<th>Mort Image</th>'; $fhtml .= '<th id="order">Mort Image</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum"){ $nhtml .= '<th>Cum Mort</th>'; $fhtml .= '<th id="order_num">Cum Mort</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum_per"){ $nhtml .= '<th>Cum Mort%</th>'; $fhtml .= '<th id="order_num">Cum Mort%</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ $nhtml .= '<th>Culls</th>'; $fhtml .= '<th id="order_num">Culls</th>';}
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_img"){ $nhtml .= '<th>Cull Image</th>'; $fhtml .= '<th id="order">Cull Image</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ $nhtml .= '<th>Sold</th>'; $fhtml .= '<th id="order_num">Sold</th>';  }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ $nhtml .= '<th>Sold Wt</th>'; $fhtml .= '<th id="order_num">Sold Wt</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){  $nhtml .= '<th>Balance Birds</th>'; $fhtml .= '<th id="order_num">Balance Birds</th>';  }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ $nhtml .= '<th>Std B.Wt</th>'; $fhtml .= '<th id="order_num">Std B.Wt</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ $nhtml .= '<th>Avg B.Wt</th>'; $fhtml .= '<th id="order_num">Avg B.Wt</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ $nhtml .= '<th>Std FCR</th>'; $fhtml .= '<th id="order_num">Std FCR</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ $nhtml .= '<th>FCR</th>'; $fhtml .= '<th id="order_num">FCR</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ $nhtml .= '<th>CFCR</th>'; $fhtml .= '<th id="order_num">CFCR</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count"){ $nhtml .= '<th>Feed OB</th>'; $fhtml .= '<th id="order_num">Feed OB</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count"){ $nhtml .= '<th>Feed In</th>'; $fhtml .= '<th id="order_num">Feed In</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count"){ $nhtml .= '<th>Feed Out</th>'; $fhtml .= '<th id="order_num">Feed Out</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ $nhtml .= '<th>Feed Con</th>'; $fhtml .= '<th id="order_num">Feed Con</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ $nhtml .= '<th>Feed Stock</th>'; $fhtml .= '<th id="order_num">Feed Stock</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count"){ $nhtml .= '<th>Cum. Feed</th>'; $fhtml .= '<th id="order_num">Cum. Feed</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img"){$nhtml .= '<th>Feed Images</th>'; $fhtml .= '<th id="order">Feed Images</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ $nhtml .= '<th>Line</th>'; $fhtml .= '<th id="order">Line</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ $nhtml .= '<th>Branch</th>'; $fhtml .= '<th id="order">Branch</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1"){ $nhtml .= '<th>Farmer Contact</th>'; $fhtml .= '<th id="order">Farmer Contact</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedtime"){ $nhtml .= '<th>Entry Time</th>'; $fhtml .= '<th id="order_date">Entry Time</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedemp"){ $nhtml .= '<th>Entry By</th>'; $fhtml .= '<th id="order">Entry By</th>';}
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "remakrs"){ $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "dieases_name"){ $nhtml .= '<th>Dieases Names</th>'; $fhtml .= '<th id="order">Dieases Names</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_location"){ $nhtml .= '<th>Farm Location</th>'; $fhtml .= '<th id="order">Farm Location</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "entry_location"){  $nhtml .= '<th>Entry Location</th>'; $fhtml .= '<th id="order">Entry Location</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "difference_kms"){ $nhtml .= '<th>Diff KM(mts)</th>'; $fhtml .= '<th id="order_num">Diff KM(mts)</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){$nhtml .= '<th>Std Feed/Bird</th>'; $fhtml .= '<th id="order_num">Std Feed/Bird</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){ $nhtml .= '<th>Act Feed/Bird</th>'; $fhtml .= '<th id="order_num">Act Feed/Bird</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_cumfeed_perbirdno"){ $nhtml .= '<th>Cum. Feed/Bird</th>'; $fhtml .= '<th id="order_num">Cum. Feed/Bird</th>'; }
                    else{ }
                }
                $nhtml .= '</tr>';
                $fhtml .= '</tr>';
                $html .= $fhtml;
                $html .= '</thead>';
                echo $html;
            }
                ?>
            
            <?php
            if(isset($_REQUEST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql = "SELECT *  FROM `prefix_master` WHERE `transaction_type` LIKE 'gcgenerate' AND `active` = '1'";
                $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $gc_pfx = "";
                if($count > 0){ while($row = mysqli_fetch_array($query)){ $gc_pfx = $row['prefix']; } }
                $sql = "SELECT min(date) as startdate FROM `account_summary` WHERE `batch` = '$batch' AND etype like 'DayEntry%' AND `trnum` NOT LIKE '$gc_pfx%' AND `active` = '1' AND `dflag` = '0' GROUP BY batch ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                if($count > 0){ while($row = mysqli_fetch_array($query)){ $start_date = $row['startdate'];  } }
                $sql = "SELECT max(date) as enddate FROM `account_summary` WHERE `batch` = '$batch'  AND `trnum` NOT LIKE '$gc_pfx%' AND `active` = '1' AND `dflag` = '0' GROUP BY batch ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                if($count > 0){ while($row = mysqli_fetch_array($query)){  $end_date = $row['enddate']; } }

                /*
                $sql_record = "SELECT min(date) as startdate,max(date) as enddate FROM `broiler_purchases` WHERE `farm_batch` = '$batch' AND `active` = '1' AND `dflag` = '0' GROUP BY farm_batch ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                if($sqlc > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $start_date = $row['startdate'];
                        $end_date = $row['enddate'];
                    }
                }

                $sql_record = "SELECT min(date) as startdate,max(date) as enddate FROM `item_stocktransfers` WHERE `to_batch` = '$batch' AND `active` = '1' AND `dflag` = '0' GROUP BY to_batch ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                if($sqlc > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        if ($start_date > $row['startdate']){
                            $start_date = $row['startdate'];
                        }

                        if ($row['enddate'] > $end_date){
                            $end_date = $row['enddate'];
                        }
                        
                        
                    }
                }

                $sql_record = "SELECT min(date) as startdate,max(date) as enddate FROM `broiler_daily_record` WHERE `batch_code` = '$batch' AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                if($sqlc > 0){
                    while($row = mysqli_fetch_assoc($query)){


                        if ($start_date > $row['startdate']){
                            $start_date = $row['startdate'];
                        }

                        if ($row['enddate'] > $end_date){
                            $end_date = $row['enddate'];
                        }
                    }
                }

                $sql_record = "SELECT min(date) as startdate,max(date) as enddate FROM `broiler_medicine_record` WHERE `batch_code` = '$batch' AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                if($sqlc > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        if ($start_date > $row['startdate']){
                            $start_date = $row['startdate'];
                        }

                        if ($row['enddate'] > $end_date){
                            $end_date = $row['enddate'];
                        }
                    }
                }

                $sql_record = "SELECT min(date) as startdate,max(date) as enddate FROM `broiler_sales` WHERE `farm_batch` = '$batch' AND `active` = '1' AND `dflag` = '0' GROUP BY farm_batch ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                if($sqlc > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        if ($start_date > $row['startdate']){
                            $start_date = $row['startdate'];
                        }

                        if ($row['enddate'] > $end_date){
                            $end_date = $row['enddate'];
                        }
                    }
                }

                $sql_record = "SELECT min(date) as startdate,max(date) as enddate FROM `item_stocktransfers` WHERE `from_batch` = '$batch' AND `active` = '1' AND `dflag` = '0' GROUP BY from_batch ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                if($sqlc > 0){
                    while($row = mysqli_fetch_assoc($query)){


                        if ($start_date > $row['startdate']){
                            $start_date = $row['startdate'];
                        }

                        if ($row['enddate'] > $end_date){
                            $end_date = $row['enddate'];
                        }
                    }
                }
                */
                if($start_date == '' || $start_date == "01.01.1970"){
                    $from_date = date("Y-m-d");
                }
                else{
                    $from_date = $start_date;
                }
                if($end_date == '' || $end_date == "01.01.1970"){
                    $till_date = date("Y-m-d");
                }
                else{
                    $till_date = $end_date;
                }
                $from_opening_date = date("Y-m-d",strtotime($from_date."-1 days")); 
                   
                    $total_feeds_open = $total_feeds_in = $total_feed_consumed = $total_feed_stock = $total_feed_cumulate = $total_obirds = $total_mort = $total_culls = $total_lifted = $total_liftedwt = $total_bbirds = $total_medvac_qty = $slno = $display_total_cummort = $display_total_present_obirds = 0;
                    
                    $key_code = "";
                    if($count61 > 0){

                        $sql_record = "SELECT icode,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,farm_batch FROM `broiler_purchases` WHERE farm_batch = '$batch' AND  `icode` =  '$chick_code' AND `active` = '1' AND `dflag` = '0' GROUP BY farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['farm_batch'];

                                $open_pur_chicks_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];

                            }
                        }
                        $sql_record = "SELECT icode,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,farm_batch,min(date) as `date` FROM `broiler_purchases` WHERE farm_batch = '$batch' AND `date` < '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY icode,farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); 
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['icode']."@".$row['farm_batch'];

                                //if($chick_code == $row['icode']){  $open_pur_chicks_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($feed_code[$row['icode']])){ $open_pur_feeds_in_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }
                                if(!empty($medvac_code[$row['icode']])){ $open_pur_medvacs_in_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty']; }

                            }
                        }
                        $sql_record = "SELECT icode,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,farm_batch,`date`,addedemp,addedtime FROM `broiler_purchases` WHERE farm_batch = '$batch' AND `date` BETWEEN '$from_date' ANd '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,icode,farm_batch ORDER BY `date`,`trnum` ASC";
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

                              
                                
                                $datewise_addedemp_pur_array[$key_code] = $row['addedemp']; 
                                $datewise_addedtime_pur_array[$key_code] = $row['addedtime']; 
                            }
                        }
                    }

                    //var_dump($datewise_present_pur_feeds_in__array)."</br>";

                    if($count91 > 0){
                        $sql_record = "SELECT to_batch,code,sum(quantity) as quantity FROM `item_stocktransfers` WHERE  to_batch = '$batch' AND `code` = '$chick_code' AND `active` = '1' AND `dflag` = '0' GROUP BY code,to_batch  ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query);
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['to_batch'];

                                $open_str_chicks_in_array[$key_code] = (float)$row['quantity'];
                                

                            }
                        }
                        $sql_record = "SELECT to_batch,code,sum(quantity) as quantity FROM `item_stocktransfers` WHERE to_batch = '$batch' AND `date` < '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY code,to_batch  ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query);
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['code']."@".$row['to_batch'];

                               // if($chick_code == $row['code']){ $open_str_chicks_in_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($feed_code[$row['code']])){ $open_str_feeds_in_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($medvac_code[$row['code']])){ $open_str_medvacs_in_array[$key_code] = (float)$row['quantity']; }

                            }
                        }

                        $sql_record = "SELECT to_batch,code,sum(quantity) as quantity,date,addedemp,addedtime FROM `item_stocktransfers` WHERE to_batch = '$batch' AND `date` BETWEEN '$from_date' ANd '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,code,to_batch  ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query);
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['code']."@".$row['to_batch'];

                                if(!empty($feed_code[$row['code']])){ $open_str_feeds_in_array[$key_code] = (float)$row['quantity']; }
                                if(!empty($medvac_code[$row['code']])){ $open_str_medvacs_in_array[$key_code] = (float)$row['quantity']; }

                                //if($chick_code == $row['icode']){ $open_str_chicks_in_array[$key_code] = (float)$row['quantity']; }
                                //if($chick_code == $row['icode']){ $datewise_present_str_chicks_in__array[$key_code] = (float)$row['quantity'];  }
                                if(!empty($feed_code[$row['code']])){ $datewise_present_str_feeds_in__array[$key_code] = (float)$row['quantity'] ; }
                                if(!empty($medvac_code[$row['code']])){ $datewise_present_str_medvacs_in__array[$key_code] = (float)$row['quantity'] ; }

                                
                                $datewise_addedemp_trin_array[$key_code] = $row['addedemp']; 
                                $datewise_addedtime_trin_array[$key_code] = $row['addedtime']; 

                            }
                        }
                    }


                    //var_dump($datewise_present_str_feeds_in__array);


                    if($count18 > 0){
                        $sql_record = "SELECT sum(mortality) as mortality ,sum(culls) as culls,sum(kgs1) as kgs1,sum(kgs2) as kgs2,batch_code FROM `broiler_daily_record` WHERE batch_code = '$batch' AND `date` < '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['batch_code'];

                                $open_mort_consume_array[$key_code] = (float)$row['mortality'];
                                $open_culls_consume_array[$key_code] = (float)$row['culls'];
                                $open_feed_consume_array[$key_code] = ((float)$row['kgs1'] + (float)$row['kgs2']);

                            }
                        }
                        $sql_record = "SELECT MAX(date) as last_entry_Date,sum(mortality) as mortality,synch_flag,updatedtime,avg_wt,remarks ,dieases_codes,sum(culls) as culls,sum(kgs1) as kgs1,sum(kgs2) as kgs2,batch_code,`date`,mort_image,feed_photos,cull_photos,addedemp,addedtime,latitude,longitude,brood_age FROM `broiler_daily_record` WHERE batch_code = '$batch' AND `date` BETWEEN '$from_date' ANd '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,batch_code ORDER BY `date`,`trnum` ASC";
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
                                if($row['dieases_codes'] !=  ''){
                                    $datewise_dieases_codes_array[$key_code] = $row['dieases_codes'];
                                }
                             
                                
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
                        $sql_record = "SELECT batch_code,sum(quantity) as quantity FROM `broiler_medicine_record` WHERE batch_code = '$batch' AND `date` < '$from_date'  AND `active` = '1' AND `dflag` = '0' GROUP BY batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $from_opening_date."@".$row['batch_code'];

                                $open_medvacs_consume_array[$key_code] = (float)$row['quantity'];
                                
                                

                            }
                        }
                        $sql_record = "SELECT batch_code,sum(quantity) as quantity,date,addedemp,addedtime FROM `broiler_medicine_record` WHERE batch_code = '$batch' AND `date` BETWEEN '$from_date' ANd '$till_date'  AND `active` = '1' AND `dflag` = '0' GROUP BY date,batch_code ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['batch_code'];

                                $open_medvacs_consume_array[$key_code] = (float)$row['quantity'];

                                $datewise_present_medvacs_consume_array[$key_code] = (float)$row['quantity'];

                                
                                $datewise_addedemp_sale_array[$key_code] = $row['addedemp']; 
                                $datewise_addedtime_sale_array[$key_code] = $row['addedtime']; 

                            }
                        }
                    }

                    if($count65 > 0){
                        $sql_record = "SELECT sum(birds) as birds,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,icode,farm_batch FROM `broiler_sales` WHERE farm_batch = '$batch' AND `date` < '$from_date' AND `active` = '1' AND `dflag` = '0' GROUP BY icode,farm_batch ORDER BY `date`,`trnum` ASC";
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
                
                        $sql_record = "SELECT sum(birds) as birds,sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty,icode,farm_batch,date,addedemp,addedtime FROM `broiler_sales` WHERE farm_batch = '$batch' AND `date` BETWEEN '$from_date' ANd '$till_date' AND `active` = '1' AND `dflag` = '0' GROUP BY date,icode,farm_batch ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $sqlc = mysqli_num_rows($query); $i = 1;
                        if($sqlc > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['date']."@".$row['icode']."@".$row['farm_batch'];

                                if($bird_code == $row['icode']){
                                    $open_birds_sale_array[$key_code] = (float)$row['birds'];
                                    $open_birdwt_sale_array[$key_code] = (float)$row['rcd_qty'] + (float)$row['fre_qty'];
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

                                
                                $datewise_addedemp_trout_array[$key_code] = $row['addedemp']; 
                                $datewise_addedtime_trout_array[$key_code] = $row['addedtime']; 

                                
                            }
                        }
                    }

                    if($count91 > 0){
                        $sql_record = "SELECT from_batch,date,sum(quantity) as quantity,code FROM `item_stocktransfers` WHERE from_batch = '$batch' AND `date` < '$from_date'  AND from_batch IS NOT NULL AND `active` = '1' AND `dflag` = '0' GROUP BY code,from_batch ORDER BY `date`,`trnum` ASC";
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

                        $sql_record = "SELECT from_batch,date,sum(quantity) as quantity,code,date FROM `item_stocktransfers` WHERE from_batch = '$batch' AND `date` BETWEEN '$from_date' ANd '$till_date'  AND from_batch IS NOT NULL  AND `active` = '1' AND `dflag` = '0' GROUP BY date,code,from_batch ORDER BY `date`,`trnum` ASC";
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


                    //var_dump($datewise_present_feeds_trout_array);

                    $opened_date = strtotime($start_date); $closed_date = strtotime($end_date); 


                    //echo date("d.m.Y",$opened_date)."@".date("d.m.Y",$closed_date);

                    

                    
                    for ($currentDate1 = $opened_date; $currentDate1 <= $closed_date; $currentDate1 += (86400)){
                        
                        $today = date("Y-m-d",$currentDate1); $opening_date = date("Y-m-d",strtotime($today. '- 1 days' ));
                        
                        $batches = $batch;
                        
                            $fetch_fcode = $batch_farm[$batches];
                            if($batches != ""){
                               $dend_date = $dstart_date = $mort_image = $feed_image = $addedemp = $addedtime = $latitude = $longitude = "";
                                $pur_qty = $sale_qty = $sold_birds = $trin_qty = $trout_qty = $medvac_qty = array();
                                $pur_chicks = $sale_chicks = $trin_chicks = $trout_chicks = $dentry_chicks = $medvac_chicks = array();
                                
                                $open_chicks_in = $open_feeds_in = $open_medvacs_in = $present_chicks_in = $present_feeds_in = $present_medvacs_in = 0;
                            
                               

                                foreach($item_code as $items){

                                     $key_code = $opening_date."@".$items."@".$batches;
                                      
                                    
                                    //Opening Balances
                                    if($chick_code == $items){  $open_chicks_in = $open_pur_chicks_array[$batches] + $open_str_chicks_in_array[$batches]; }
                                    if(!empty($feed_code[$items])){ 
                                        $open_feeds_in = $prev_open_feeds_in_array[$batches] + $open_pur_feeds_in_array[$key_code] + $open_str_feeds_in_array[$key_code];
                                        $prev_open_feeds_in_array[$batches] = $open_feeds_in;
                                    }
                                    if(!empty($medvac_code[$items])){ 
                                        $open_medvacs_in = $prev_open_medvacs_in_array[$batches] + $open_pur_medvacs_in_array[$key_code] + $open_str_medvacs_in_array[$key_code]; 
                                        $prev_open_medvacs_in_array[$batches] = $open_medvacs_in;
                                    }

                                    $key_code1 = $today."@".$items."@".$batches;
                                    //Today's Balances
                                    //if($chick_code == $item){ $open_chicks_in = $open_pur_chicks_array[$key_code1] + $open_str_chicks_array[$key_code1]; }
                                    //if($chick_code == $items){ $present_chicks_in = $datewise_present_pur_chicks_in__array[$key_code1] + $datewise_present_str_chicks_in__array[$key_code1]; }
                                    if(!empty($feed_code[$items])){ $present_feeds_in += $datewise_present_pur_feeds_in__array[$key_code1] + $datewise_present_str_feeds_in__array[$key_code1]; }
                                    if(!empty($medvac_code[$items])){ $present_medvacs_in += $datewise_present_pur_medvacs_in__array[$key_code1] + $datewise_present_str_medvacs_in__array[$key_code1]; }
                                    

                                }

                                $open_mort_consume  = $open_culls_consume  = $open_feed_consume  = $act_body_weight = 
                                $present_mort_consume = $present_culls_consume = $present_feed_consume = $latest_avg_wt = $consumed_feeds = 0;
                                $remarks = $dieases_codes = "";

                                $key_code_dailyentry = $opening_date."@".$batches;
                                $key_code_dailyentry1 = $today."@".$batches;

                                
                                
                                $open_mort_consume = (float)$prev_open_mort_consume_array[$batches]+(float)$open_mort_consume_array[$key_code_dailyentry];
                                $prev_open_mort_consume_array[$batches] =  $open_mort_consume;

                                $open_culls_consume = (float)$prev_open_culls_consume_array[$batches] + (float)$open_culls_consume_array[$key_code_dailyentry];
                                $prev_open_culls_consume_array[$batches] =  $open_culls_consume;

                                $open_feed_consume = (float)$prev_open_feed_consume_array[$batches] + ((float)$open_feed_consume_array[$key_code_dailyentry]);
                                $prev_open_feed_consume_array[$batches] =  $open_feed_consume;

                               
                                $present_mort_consume = (float)$datewise_present_mort_consume_array[$key_code_dailyentry1];
                                $present_culls_consume = (float)$datewise_present_culls_consume_array[$key_code_dailyentry1];
                                $present_feed_consume = ((float)$datewise_present_feed_consume_array[$key_code_dailyentry1]);
                                $act_body_weight = $datewise_act_body_weight_array[$key_code_dailyentry1];
                                $remarks = $datewise_remarks_array[$key_code_dailyentry1];
                                if( $datewise_dieases_codes_array[$key_code_dailyentry1] != ''){
                                    $dieases_codes = $datewise_dieases_codes_array[$key_code_dailyentry1];
                                }else{
                                    $dieases_codes = "";
                                }
                                

                                if( $act_body_weight != "" &&  $act_body_weight > 0){
                                    $latest_avg_wt = $datewise_latest_avg_wt_array[$key_code_dailyentry1];
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


                                $open_medvacs_consume = $present_medvacs_consume = 0;

                                $open_medvacs_consume = (float)$open_medvacs_consume_array[$key_code_dailyentry];
                                $present_medvacs_consume = (float)$datewise_present_medvacs_consume_array[$key_code_dailyentry1];

                               
                                $open_birds_sale = $open_birdwt_sale = $open_feeds_sale = $open_medvacs_sale = $present_birds_sale = $present_birdwt_sale = 
                                $present_feeds_sale = $present_medvacs_sale = 0;

                                foreach($item_code as $items){

                                    $key_code = $opening_date."@".$items."@".$batches;
                                    $key_code1 = $today."@".$items."@".$batches;
                                    //Opening Balances
                                    if($bird_code == $items){
                                        $open_birds_sale = (float)$prev_open_birds_sale_array[$batches]+(float)$open_birds_sale_array[$key_code];
                                        $prev_open_birds_sale_array[$batches] = $open_birds_sale;
                                        $open_birdwt_sale = (float)$prev_open_birdwt_sale_array[$batches] + (float)$open_birdwt_sale_array[$key_code];
                                        $prev_open_birdwt_sale_array[$batches] = $open_birdwt_sale;
                                    }
                                    if(!empty($feed_code[$items])){ 
                                        $open_feeds_sale = (float)$prev_open_feeds_sale_array[$batches] + (float)$open_feeds_sale_array[$key_code]; 
                                        $prev_open_feeds_sale_array[$batches] = $open_feeds_sale;
                                    }
                                    if(!empty($medvac_code[$items])){ 
                                        $open_medvacs_sale = (float)$prev_open_medvacs_sale_array[$batches] + (float)$open_medvacs_sale_array[$key_code];
                                        $prev_open_medvacs_sale_array[$batches] = $open_medvacs_salee; 
                                    }


                                     //Today's Balances
                                     if($bird_code == $items){
                                        $present_birds_sale = (float)$datewise_present_birds_sale_array[$key_code1];
                                        $present_birdwt_sale = (float) $datewise_present_birdwt_sale_array[$key_code1];
                                    }
                                    if(!empty($feed_code[$items])){ $present_feeds_sale += (float)$datewise_feed_code_array[$key_code1]; }
                                    if(!empty($medvac_code[$items])){ $present_medvacs_sale += (float)$datewise_medvac_code_array[$key_code1]; }

                                }

                               

                                $open_birds_trout = $open_feeds_trout = $open_medvacs_trout = $present_birds_trout = $present_feeds_trout = $present_medvacs_trout = 0;
                                
                                foreach($item_code as $items){

                                    $key_code = $opening_date."@".$items."@".$batches;
                                    $key_code1 = $today."@".$items."@".$batches;

                                    //Opening Balances
                                    if($bird_code == $items){ 
                                        $open_birds_trout += (float)$prev_open_birds_trout_array[$batches]  + (float)$open_birds_trout_array[$key_code]; 
                                        $prev_open_birds_trout_array[$batches] = $open_birds_trout;
                                    
                                    }
                                    if(!empty($feed_code[$items])){ 
                                        $open_feeds_trout = (float)$prev_open_feeds_trout_array[$batches] + (float)$open_feeds_trout_array[$key_code];
                                        $prev_open_feeds_trout_array[$batches] = $open_feeds_trout; 
                                        
                                    }
                                    if(!empty($medvac_code[$items])){ 
                                        $open_medvacs_trout = (float)$prev_open_medvacs_trout_array[$batches] + (float)$open_medvacs_trout_array[$key_code];
                                        $prev_open_medvacs_trout_array[$batches] = $open_medvacs_trout; 
                                    }

                                     //Today's Balances
                                     if($bird_code == $items){ $present_birds_trout += (float)$datewise_present_birds_trout_array[$key_code1]; }
                                     if(!empty($feed_code[$items])){   $present_feeds_trout += (float)$datewise_present_feeds_trout_array[$key_code1]; }
                                     if(!empty($medvac_code[$items])){ $present_medvacs_trout += (float)$datewise_present_medvacs_trout_array[$key_code1]; }
                                    
                                }
                                
                                
                                    $display_farmlatitude = $farm_latitude[$fetch_fcode];
                                    $display_farmlongitude = $farm_longitude[$fetch_fcode];
                                    $display_farmcode = $farm_ccode[$fetch_fcode];
                                    $display_farmname = $farm_name[$fetch_fcode];
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
                                    $display_age = (float)$datewise_dentry_age_array[$key_code_dailyentry1];
                                    $display_entrydate = $datewise_dentry_date_array[$key_code_dailyentry1];
                                    if($display_entrydate == ''){
                                        $display_entrydate =  $today;
                                    }

                                   // echo $key_code_dailyentry1."--".$batches."--".$datewise_dentry_date_array[$key_code_dailyentry1]."<br/>";
                                    //Display Feed Section
                                    $display_feeds_open = (float)$open_feeds_in - (float)$open_feed_consume - (float)$open_feeds_sale - (float)$open_feeds_trout;
                                    //echo $today . "//". (float)$open_feeds_in ."--". (float)$open_feed_consume ."--". (float)$open_feeds_sale ."--". (float)$open_feeds_trout."<br/>";
                                    $display_feeds_in = $present_feeds_in;
                                   // echo $today."---".$display_feeds_in."</br>";
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
                                    $display_present_obirds = (float)$open_chicks_in - ((float)$open_mort_consume + (float)$open_culls_consume + (float)$open_birds_sale + (float)$open_birds_trout);
                                   //echo $today."//".(float)$open_chicks_in ."--". ((float)$open_mort_consume ."-+". (float)$open_culls_consume ."-+". (float)$open_birds_sale ."-+". (float)$open_birds_trout)."<br/>";
                                    $display_mort = $present_mort_consume;
                                    $display_cummort = (float)$open_mort_consume + (float)$present_mort_consume;
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
                                                //$mort_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/mortimages/".$mia."');";
                                                $break_image = explode("/",$mia);
                                                if(sizeof($break_image) > 0 && $break_image[1] == "AndroidApp_API"){
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
                                                $break_image = explode("/",$mia);
                                                if(sizeof($break_image) > 0 && $break_image[1] == "AndroidApp_API"){
                                                
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
                                                if( $addedtime < "2024-04-20 07:21:20" ){
                                                    $feed_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/feedimages/".$fia."');";
                                                    
                                                }else{
                                                    $feed_img_list = "window.open('..".$fia."');";
                                                }
                                                
                                            }
                                            else{
                                                if( $addedtime < "2024-04-20 07:21:20" ){
                                                    $feed_img_list = $feed_img_list."window.open('../AndroidApp_API/clientimages/".$client."/feedimages/".$fia."');";
                                                    
                                                }else{
                                                    $feed_img_list = $feed_img_list."window.open('..".$fia."');";
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
                                                
                                                if( $addedtime < "2024-04-20 07:21:20" ){
                                                    $cull_img_list = "window.open('../AndroidApp_API/clientimages/".$client."/cullimages/".$cia."');";
                                                    
                                                }else{
                                                    $cull_img_list = "window.open('..".$cia."');";
                                                }
                                            }
                                            else{
                                                //$cull_img_list = $cull_img_list."window.open('../AndroidApp_API/clientimages/".$client."/cullimages/".$cia."');";
                                                if( $addedtime < "2024-04-20 07:21:20" ){
                                                    $cull_img_list = $cull_img_list."window.open('../AndroidApp_API/clientimages/".$client."/cullimages/".$cia."');";
                                                    
                                                }else{
                                                    $cull_img_list = $cull_img_list."window.open('..".$cia."');";
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
                                    $display_lifted =  (float)$present_birds_sale;
                                    $display_liftedwt = (float)$present_birdwt_sale;
                                    $display_bbirds = (float)$display_present_obirds - (float)$display_mort - (float)$display_culls - (float)$display_lifted;
                                    $display_medvacname = $medvac_names;
                                    $display_remarks = $remarks;
                                    
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
                                    

                                    if($sales_birds_qty > 0) {
                                        $display_fcr = ((float)$consumed_feeds / (float)$sales_birds_qty);
                                    }
                                    else if($latest_avg_wt > 0 && $display_present_obirds > 0) {
                                        $display_fcr = ((float)$consumed_feeds / ((float)$display_present_obirds * ((float)$latest_avg_wt / 1000)));
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
                                        $display_farm_location = "https://broiler.poulsoft.net/records/ShowLocation.php?lat=".$display_farmlatitude."&lng=".$display_farmlongitude."&farm_name=".$display_farmname."&type=Farm Location";
                                    }
                                    else{
                                        $display_farm_location = "";
                                    }
                                    if(!empty($latitude) && !empty($longitude)){
                                        /*$display_entry_location = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=AIzaSyCQO_zZX9F0UzrOzCYsXRAAbhwjhSSXWaw";*/
                                        $display_entry_location = "https://broiler.poulsoft.net/records/ShowLocation.php?lat=".$latitude."&lng=".$longitude."&farm_name=".$display_farmname."&type=Daily Entry Farm Location";
                                    }
                                    else{
                                        $display_entry_location = "";
                                    }
                                    if(!empty($display_farmlatitude) && $display_farmlatitude != 0.0 && !empty($display_farmlongitude) && $display_farmlongitude != 0.0 && !empty($latitude) && $latitude != 0.0 && !empty($longitude) && $longitude != 0.0 ){
                                        $display_differ_location =  computeDistance($display_farmlatitude,$display_farmlongitude,$latitude,$longitude)."";
                                            $display_differ_location_link = "https://broiler.poulsoft.net/records/ShowDirection.php?lat1=".$display_farmlatitude."&lng1=".$display_farmlongitude."&lat2=".$latitude."&lng2=".$longitude."&farm_name=".$display_farmname."&type=Daily Entry Farm Location";
                                    }else{
                                        $display_differ_location = "";
                                        $display_differ_location_link = "";
                                    }
                                    
                                    //if(date("d.m.Y",((int)$dend_date)) != "01.01.1970" && $dend_date == strtotime($_POST['tdate'])){
                                       // if(date("d.m.Y",strtotime($display_entrydate)) != "01.01.1970"){

                                        $slno++;
                                            if($slno == 1){
                                                $total_feeds_open += (float)$display_feeds_open;
                                            }
                                        
                                            if(empty($prev_total_batch) && $batchwise_last_entry_date[$batches] == $today){
                                               // $total_feeds_open += (float)$display_feeds_open;
                                                //$total_feeds_in += (float)$display_feeds_in;
                                                //$total_feed_consumed += (float)$display_feed_consume;
                                               // $total_feed_stock += (float)$display_feed_stock;
                                                $total_feed_cumulate += (float)$display_feed_cumulate;
                                                $total_obirds += (float)$display_obirds;
                                               // $display_total_present_obirds += (float)$display_present_obirds;
                                                //$total_mort += (float)$display_mort;
                                                //$total_culls += (float)$display_culls;
                                                //$total_lifted += (float)$display_lifted;
                                                //$total_liftedwt += (float)$display_liftedwt;
                                                //$total_bbirds += (float)$display_bbirds;
                                                $total_medvac_qty += (float)$display_medvacqty;
                                                $display_total_cummort += (float)$display_cummort;

                                                $prev_total_batch[] = $batches;

                                                
                                            }else if(!empty($prev_total_batch) && !in_array($batches,$prev_total_batch) && $batchwise_last_entry_date[$batches] == $today ){
                                               // $total_feeds_open += (float)$display_feeds_open;
                                                //$total_feeds_in += (float)$display_feeds_in;
                                               // $total_feed_consumed += (float)$display_feed_consume;
                                               // $total_feed_stock += (float)$display_feed_stock;
                                                $total_feed_cumulate += (float)$display_feed_cumulate;
                                                $total_obirds += (float)$display_obirds;
                                               // $display_total_present_obirds += (float)$display_present_obirds;
                                               // $total_mort += (float)$display_mort;
                                               // $total_culls += (float)$display_culls;
                                               // $total_lifted += (float)$display_lifted;
                                               // $total_liftedwt += (float)$display_liftedwt;
                                               // $total_bbirds += (float)$display_bbirds;
                                                $total_medvac_qty += (float)$display_medvacqty;
                                                $display_total_cummort += (float)$display_cummort;

                                                $prev_total_batch[] = $batches;

                                                
                                            }

                                            $total_mort += (float)$display_mort;
                                            $total_culls += (float)$display_culls;
                                            $total_lifted += (float)$display_lifted;
                                            $total_liftedwt += (float)$display_liftedwt;
                                            $total_feeds_in += (float)$display_feeds_in;
                                            $total_feeds_out += (float)$display_feed_out;
                                            $total_feed_consumed += (float)$display_feed_consume;

                                            
                                            

                                         
                                        
                                            if($dieases_codes != ''){
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

                                           
                                          
                                            if($display_age > 0){
                                                $slno1++;
                                            }
                                            echo "<tr>";
                                            for($i = 1;$i <= $col_count;$i++){
                                                $key_id = "A:1:".$i;
                                                if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No.'>".$slno."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<td title='Farm Code'>".$display_farmcode."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farmer'>".$display_farmname."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<td title='Batch'>".$display_farmbatch."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<td title='Book No'>".$display_batchbook."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor'>".$display_supervisor."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){
                                                    
                                                    if(date("d.m.Y",strtotime($display_entrydate)) == "01.01.1970"){ echo "<td title='Age'></td>"; }
                                                    else{ echo "<td title='Age' style='text-align:center;'>".round($display_age)."</td>"; }
                                                } else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "date"){
                                                    
                                                    if(date("d.m.Y",strtotime($display_entrydate)) == "01.01.1970"){ echo "<td title='Entry Date'></td>"; }
                                                    else{ echo "<td title='Entry Date' style='text-align:center;'>".date("d.m.Y",strtotime($display_entrydate))."</td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<td title='Placed Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_obirds,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno"){ echo "<td title='Opening Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_present_obirds,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<td title='Mort' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_mort,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<td title='Mort%' style='text-align:right;'>".number_format_ind(round($display_mortper,2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_img"){
                                                    if(!empty($display_mortimage)){ ?><td style="text-align:right;" title="Mort Image"><a href="javascript:void(0)" onclick="<?php echo $mort_img_list; ?>" title="<?php echo $mort_img_list; ?>">mortImage-<?php echo $slno1; ?></a></td><?php }
                                                    else{ echo "<td title='Mort Image'></td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum"){ echo "<td title='Cum Mort' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_cummort,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_cum_per"){ echo "<td title='Cum Mort%' style='text-align:right;'>".number_format_ind(round($display_cummortper,2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_count"){ echo "<td title='Culls' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_culls,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "culls_img"){
                                                    if(!empty($display_cullimage)){ ?> <td title="Cull Image"><a href="javascript:void(0)" onclick="<?php echo $cull_img_list; ?>" title="<?php echo $cull_img_list; ?>">cullImage-<?php echo $slno1; ?></a></td><?php }
                                                    else{ echo "<td title='Cull Image'></td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Sold' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_lifted,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Sold Wt' style='text-align:right;'>".number_format_ind(round($display_liftedwt,2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "available_birds"){ echo "<td title='Balance Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind(round($display_bbirds,2)))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_bodywt"){ echo "<td title='Std B.Wt' style='text-align:right;'>".round($display_stdbodyWt,3)."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){
                                                    if(number_format_ind($display_bodyWt) == "0.00"){ echo "<td style='text-align:right;color:black;' title='Body Weight'>".round($display_bodyWt / 1000,3)."</td>"; }
                                                    else if((float)$display_bodyWt >= (float)$display_stdbodyWt){
                                                        echo "<td style='text-align:right;color:red;' title='Body Weight'>".round($display_bodyWt / 1000,3)."</td>";
                                                    }
                                                    else{
                                                        echo "<td style='text-align:right;color:green;' title='Body Weight'>".round($display_bodyWt / 1000,3)."</td>";
                                                    }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<td title='Std FCR' style='text-align:right;'>".round($display_stdfcr,3)."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){
                                                    if($display_stdfcr < $display_fcr){ echo "<td title='FCR' style='text-align:right;color:red;'>".round($display_fcr,3)."</td>"; }
                                                    else if(number_format_ind($display_fcr) == "0.00"){ echo "<td title='FCR' style='text-align:right;color:black;'>".round($display_fcr,3)."</td>"; }
                                                    else { echo "<td title='FCR' style='text-align:right;color:green;'>".round($display_fcr,3)."</td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<td title='CFCR' style='text-align:right;'>".(round($display_cfcr,3))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count"){ echo "<td title='Feed OB' style='text-align:right;'>".number_format_ind(round(($display_feeds_open),2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count"){ echo "<td title='Feed In' style='text-align:right;'>".number_format_ind(round(($display_feeds_in),2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count"){ echo "<td title='Feed Out' style='text-align:right;'>".number_format_ind(round(($display_feed_out),2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<td title='Feed Con' style='text-align:right;'>".number_format_ind(round(($display_feed_consume),2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ echo "<td title='Feed Stock' style='text-align:right;'>".number_format_ind(round(($display_feed_stock),2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count"){ echo "<td title='Cum. Feed' style='text-align:right;'>".number_format_ind(round(($display_feed_cumulate),2))."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img"){
                                                    if(!empty($display_feedimage)){ ?><td title="Feed Images"><a href="javascript:void(0)" onclick="<?php echo $feed_img_list; ?>" title="<?php echo $feed_img_list; ?>">feedImage-<?php echo $slno1; ?></a></td><?php }
                                                    else{ echo "<td title='Feed Images'></td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<td title='Line'>".$display_line."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<td title='Branch'>".$display_place."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1"){ echo "<td title='Farmer Contact'>".$display_contact."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedtime"){
                                                    if(date("d.m.Y",strtotime($display_addedtime)) == "01.01.1970"){ echo "<td title='Entry Time'></td>"; }
                                                    else{ echo "<td title='Entry Time'>".date('d.m.Y H:i:s A',strtotime($display_addedtime))."</td>"; }
                                                     
                                                 }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "addedemp"){ 
                                                    if(date("d.m.Y",strtotime($display_addedtime)) == "01.01.1970"){ echo "<td title='Entry By'></td>"; }
                                                    else{ echo "<td title='Entry By'>".$supervisor_name[$db_emp_code[$display_addedemp]]."</td>"; }
                                                     
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "remakrs"){ echo "<td title='Remarks'>".$display_remarks."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "dieases_name"){ echo "<td title='Dieases Names'>".$display_dieases_codes."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_location"){
                                                    if(!empty($display_farm_location)){ echo "<td title='Farm Location'><a href='".$display_farm_location."' target='_BLANK'>Location-".$slno1."</a></td>"; }
                                                    else{ echo "<td title='Farm Location'></td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "entry_location"){
                                                    if(!empty($display_entry_location)){ echo "<td title='Entry Location'><a href='".$display_entry_location."' target='_BLANK'>Location-".$slno1."</a></td>"; }
                                                    else{ echo "<td title='Entry Location'></td>"; }
                                                }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "difference_kms"){
                                                    if(!empty($display_differ_location)){ echo "<td title='Difference K.M'><a href='".$display_differ_location_link."' target='_BLANK'>".$display_differ_location."</a></td>"; }
                                                    else{ echo "<td title='Difference K.M'></td>"; }
                                                }
                        
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_perbirdno"){ echo "<td title='Std Feed/Bird' style='text-align:right;'>".$display_std_feed_perbird."</td>"; } 
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_perbirdno"){ echo "<td title='Feed Feed/Bird' style='text-align:right;'>".$display_act_feed_perbird."</td>"; }
                                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_cumfeed_perbirdno"){ echo "<td title='Feed Feed/Bird' style='text-align:right;'>".$display_actcum_feed_perbird."</td>"; }
                                            }
                                            echo "</tr>";
                                        //}
                                    //}
                                //}
                            }
                        
                    }
                ?>
            </tbody>
            <tfoot>
                <?php
                $display_total_present_obirds = $total_obirds;
                $total_feed_stock = (float)$total_feeds_in - (float)$total_feed_consumed - (float)$total_feeds_out;
                $total_bbirds = $total_obirds - $total_mort - $total_culls - $total_lifted;
                echo "<tr class='thead4'>";
                if($total_lifted > 0){
                    $tot_avg_wt = $total_liftedwt/$total_lifted;
                }else{
                    $tot_avg_wt = 0;
                }
                if($total_liftedwt > 0){
                    $tot_fcr = $total_feed_consumed/$total_liftedwt;
                }else{
                    $tot_fcr = 0;
                }
                $tot_feed_stock = ($total_feeds_open+$total_feeds_in)-($total_feeds_out +$total_feed_consumed);
                echo ($total_feeds_open+$total_feeds_in)."//".($total_feeds_out +$total_feed_consumed);
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th style='text-align:left; border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<th style='text-align:center; border-left: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "date"){ echo "<th style='text-align:center; border-left: 0px;'>Total</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($total_obirds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "opening_birdsno"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind($display_total_present_obirds))."</th>"; }
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
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th style='text-align:left;'>".round($tot_avg_wt,3)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<th style='text-align:left;'>".round($tot_fcr,3)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedopening_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feeds_open)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedin_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feeds_in)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedout_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feeds_out)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_consumed)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_balance_count"){ echo "<th style='text-align:right;'>".number_format_ind($tot_feed_stock)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedcumconsumed_count"){ echo "<th style='text-align:right;'>".number_format_ind($total_feed_cumulate)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_img"){ echo "<th style='text-align:left;'></th>"; }
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
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }

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

            function fetch_farms_details1(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
               
                //var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        /*removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>*/
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        /*removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>*/
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        /*removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_line[$fcode])){ $l_code = $farm_line[$fcode]; } else{ $l_code = ""; }
                                
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>*/
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        /*removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>*/
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        /*removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){
                                    $f_code = $farm_svr[$fcode];
                                }
                                else{
                                    $f_code = "";
                                }
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>*/
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
                fetch_farms_details();
            }
            fetch_farms_details();
            function fetch_farms_details(){
                var farm_name = document.getElementById("farms").value;
                var batch_1 = '<?php echo $batch; ?>'; var batch_2 = "";
                //Update Bastch Details
                removeAllOptions(document.getElementById("batch"));
                                    myselect1 = document.getElementById("batch");
                                    <?php
                                        foreach($batch_code as $batchcode){
                                            $b_code = $batch_farm[$batchcode];
                                            echo "if(farm_name == '$b_code'){";
                                    ?>
                                        theOption1=document.createElement("OPTION");
                                        theText1=document.createTextNode("<?php echo $batch_name[$batchcode]; ?>");
                                        theOption1.value = "<?php echo $batch_code[$batchcode]; ?>";
                                        batch_2 = '<?php echo $batchcode; ?>';
                                        if(batch_1 == batch_2){
                                            theOption1.selected = "selected";
                                        }
                                        theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                                    <?php
                                        echo "}";
                                        }
                                    ?>
            }

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
        $(document).ready(function(){
            var table =  $('#mine').DataTable({
                paging: false,
            });
            
            $("#hide_show_all").on("change",function(){
                var hide = $(this).is(":checked");
                $(".hide_show").prop("checked", hide);
                if(hide){
                    $('#mine tr th').hide(100);
                    $('#mine tr td').hide(100);
                }else{
                    $('#mine tr th').show(100);
                    $('#mine tr td').show(100);
                }
            });

            $(".hide_show").on("change",function(){
                var hide = $(this).is(":checked");
                
                var all_ch = $(".hide_show:checked").length == $(".hide_show").length;

                $("#hide_show_all").prop("checked", all_ch);
                
                var ti = $(this).index(".hide_show");
                
                $('#mine tr').each(function(){
                    if(hide){
                        $('td:eq(' + ti + ')',this).hide(100);
                        $('th:eq(' + ti + ')',this).hide(100);
                    }else{
                        $('td:eq(' + ti + ')',this).show(100);
                        $('th:eq(' + ti + ')',this).show(100);
                    }
                });

            });
            //$('#mine tfoot th').each( function () {
                //var title = $(this).text();
                //$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //} );

            $('#myInput').keyup( function() {
                    table.draw();
                } );
                $('input.column_filter').on( 'keyup click', function () {
                    filterColumn( $(this).parents('tr').attr('data-column') );
                });
            
            });
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>