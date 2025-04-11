<?php
//broiler_daily_record2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else { $db = ''; }

if($db == ''){

    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
}

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

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code']; $farm_latitude[$row['code']] = $row['latitude']; $farm_longitude[$row['code']] = $row['longitude'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; }

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
        $farm_query2 = " AND farm_code IN ('$farms')";
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
        $farm_query2 = " AND farm_code IN ('$farm_list')";
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
        $farm_query2 = " AND farm_code IN ('$farm_list')";
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
        $farm_query2 = " AND farm_code IN ('$farm_list')";
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
        $farm_query2 = " AND farm_code IN ('$farm_list')";
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
            .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead2_empty_row { display:none; }
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
    <body>
        <table class="tbl" align="center" style="width:1212px;">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Day Record Report</h5></th>
                    <th colspan="27" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_daily_record2.php" method="post">
                <?php } else { ?>
                <form action="broiler_daily_record2.php?db=<?php echo $db; ?>&client=<?php echo $_GET['client']; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="39">
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
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
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
        <td>
        <div id='control_sh'>
        <input type="checkbox" class="hide_show"><span>Sl.No.</span> 
        <input type="checkbox" class="hide_show"><span>Farm Code</span> 
        <input type="checkbox" class="hide_show"><span>Farmer</span> 
        <input type="checkbox" class="hide_show"><span>Batch</span> 
        <input type="checkbox" class="hide_show"><span>Book No</span> 
        <input type="checkbox" class="hide_show"><span>Supervisor</span> 
        <!--<input type="checkbox" class="hide_show"><span>Entry Date</a>-->
        <input type="checkbox" class="hide_show"><span>Age</span> 
        
        <input type="checkbox" class="hide_show"><span>Placed Birds</span> 
        <input type="checkbox" class="hide_show"><span>Opening Birds</span> 
        <input type="checkbox" class="hide_show"><span>Mort</span> 
        <input type="checkbox" class="hide_show"><span>Mort%</span>  
        <input type="checkbox" class="hide_show"><span>Mort Image</span> 
        <input type="checkbox" class="hide_show"><span>Cum Mort</span> 
        <input type="checkbox" class="hide_show"><span>Cum Mort%</span> 
        <input type="checkbox" class="hide_show"><span>Culls</span> 
        <input type="checkbox" class="hide_show"><span>Cull Image</span> 
        <input type="checkbox" class="hide_show"><span>Sold</span> 
        <input type="checkbox" class="hide_show"><span>Sold Wt</span> 
        <input type="checkbox" class="hide_show"><span>Balance Birds</span> 

        <input type="checkbox" class="hide_show"><span>Std B.Wt</span> 
        <input type="checkbox" class="hide_show"><span>Avg B.Wt</span> 
        <input type="checkbox" class="hide_show"><span>Std FCR</span> 
        <input type="checkbox" class="hide_show"><span>FCR</span> 
        <input type="checkbox" class="hide_show"><span>CFCR</span> 

        <input type="checkbox" class="hide_show"><span>Feed OB</span> 
        <input type="checkbox" class="hide_show"><span>Feed In</span> 
        <input type="checkbox" class="hide_show"><span>Feed Out</span> 
        <input type="checkbox" class="hide_show"><span>Feed Con</span> 
        <input type="checkbox" class="hide_show"><span>Feed Stock</span> 
        <input type="checkbox" class="hide_show"><span>Cum. Feed</span> 
        <input type="checkbox" class="hide_show"><span>Feed Images</span> 
        <input type="checkbox" class="hide_show"><span>Line</span> 
        <input type="checkbox" class="hide_show"><span>Branch</span> 
        <input type="checkbox" class="hide_show"><span>Farmer Contact</span> 
        <input type="checkbox" class="hide_show"><span>Entry Time</span> 
        <input type="checkbox" class="hide_show"><span>Entry By</span>
        <input type="checkbox" class="hide_show"><span>Remarks</span> 
        <input type="checkbox" class="hide_show"><span>Dieases Names</span>
        <input type="checkbox" class="hide_show"><span>Farm Location</span> 
        <input type="checkbox" class="hide_show"><span>Entry Location</span> 
       <!--- <input type="checkbox" class="hide_show"><span>Diff KM</span>  --->
                
        </div>
        </td>
    </tr>    
    <tr><td><br></td></tr>                                
    </table>  
            <table id="mine" class="tbl" align="center"  style="width:1300px;">
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                    <th>Sl.No.</th>
                    <th>Farm Code</th>
                    <th>Farmer</th>
                    <th>Batch</th>
                    <th>Book No</th>
                    <th>Supervisor</th>
                    <!--<th>Entry Date</th>-->
                    <th>Age</th>
                    
                    <th>Placed Birds</th>
                    <th>Opening Birds</th>
                    <th>Mort</th>
                    <th>Mort%</th>
                    <th>Mort Image</th>
                    <th>Cum Mort</th>
                    <th>Cum Mort%</th>
                    <th>Culls</th>
                    <th>Cull Image</th>
                    <th>Sold</th>
                    <th>Sold Wt</th>
                    <th>Balance Birds</th>

                    <th>Std B.Wt</th>
                    <th>Avg B.Wt</th>
                    <th>Std FCR</th>
                    <th>FCR</th>
                    <th>CFCR</th>

                    <th>Feed OB</th>
                    <th>Feed In</th>
                    <th>Feed Out</th>
                    <th>Feed Con</th>
                    <th>Feed Stock</th>
                    <th>Cum. Feed</th>
                    <th>Feed Images</th>
                    <th>Line</th>
                    <th>Branch</th>
                    <th>Farmer Contact</th>
                    <th>Entry Time</th>
                    <th>Entry By</th>
                    <th>Remarks</th>
                    <th>Dieases Names</th>
                    <th>Farm Location</th>
                    <th>Entry Location</th>
                  <!---  <th>Diff KM(mts)</th> --->
                  
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
                ?>
                <tbody class="tbody1">
                <?php
                $batch_sql = "SELECT * FROM `broiler_batch` WHERE gc_flag = '0' AND active = '1' AND dflag = '0'"; $batch_query = mysqli_query($conn,$batch_sql);
                $batch_all = "";
                while($row = mysqli_fetch_assoc($batch_query)){
                    if($batch_all == ""){
                        $batch_all = $row['code'];
                    }
                    else{
                        $batch_all = $batch_all."','".$row['code'];
                    }
                }
                $batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code AND a.gc_flag = '0' AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY age DESC"; $batch_query = mysqli_query($conn,$batch_sql);
                $i = 0; while($batch_row = mysqli_fetch_assoc($batch_query)){
                    $i++;
                    $batch_list[$i] = $batch_row['batch_code'];
                    $batch_age[$batch_row['batch_code']] = $batch_row['age'];
                    $batch_farm[$batch_row['batch_code']] = $batch_row['farm_code'];
                    if($batch1 == ""){
                        $batch1 = $batch_row['batch_code'];
                    }
                    else{
                        $batch1 = $batch1."','".$batch_row['batch_code'];
                    }
                }
                $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0'".$farm_query2." AND `code` NOT IN ('$batch1')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $i++;
                    $batch_list[$i] = $row['code'];
                    $batch_age[$row['code']] = 0;
                    $batch_farm[$row['code']] = $row['farm_code'];
                }
                $total_feeds_open = $total_feeds_in = $total_feed_consumed = $total_feed_stock = $total_feed_cumulate = $total_obirds = $total_mort = $total_culls = $total_lifted = $total_liftedwt = $total_bbirds = $total_medvac_qty = $slno = $display_total_cummort = $display_total_present_obirds = 0;
                $bag_size = 50; $till_date = date("Y-m-d",strtotime($_POST['tdate']));
                //while($batch_row = mysqli_fetch_assoc($batch_query)){
                    foreach($batch_list as $batches){
                        //$batches = $batch_row['batch_code'];
                        //$brood_age = $batch_row['age'];
                        $brood_age = $batch_age[$batches];
                        $fetch_fcode = $batch_farm[$batches];
                    if($batches != ""){
                        $start_date = $end_date = $dend_date = $dstart_date = $mort_image = $feed_image = $addedemp = $addedtime = $latitude = $longitude = "";
                        $pur_qty = $sale_qty = $sold_birds = $trin_qty = $trout_qty = $medvac_qty = array();
                        $pur_chicks = $sale_chicks = $trin_chicks = $trout_chicks = $dentry_chicks = $medvac_chicks = array();

                        $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$till_date' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['icode']."@".$i;
                            $pur_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` <= '$till_date' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['icode']."@".$i;
                            $sold_birds[$key_code] = $row['birds'];
                            $sale_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$till_date' AND `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['code']."@".$i;
                            $trin_qty[$key_code] = $row['quantity'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$till_date' AND `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['code']."@".$i;
                            $trout_qty[$key_code] = $row['quantity'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$till_date' AND `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                  
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$i;
                            
                            $dentry_chicks[$key_code] = $row['trnum']."@".$row['supervisor_code']."@".$row['date']."@".$row['farm_code']."@".$row['batch_code']."@".$row['brood_age']."@".$row['mortality']."@".$row['culls']."@".$row['item_code1']."@".$row['kgs1']."@".$row['item_code2']."@".$row['kgs2']."@".$row['avg_wt']."@".$row['addedemp']."@".$row['addedtime']."@".$row['remarks']."@".$row['dieases_codes'];
                                
                          $i++;
                            if(strtotime($till_date) == strtotime($row['date'])){ $mort_image = $row['mort_image'];$feed_image = $row['feed_photos'];$cull_image = $row['cull_photos']; $addedemp = $row['addedemp']; $addedtime = $row['addedtime']; $latitude = $row['latitude']; $longitude = $row['longitude']; }
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                            if($dstart_date == ""){ $dstart_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $dstart_date){ $dstart_date = strtotime($row['date']); } }
                            if($dend_date == ""){ $dend_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $dend_date){ $dend_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_medicine_record` WHERE `date` <= '$till_date' AND `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['item_code']."@".$i;
                            $medvac_qty[$key_code] = $row['quantity'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                   
                            $pur_count = sizeof($pur_qty); $trin_count = sizeof($trin_qty);
                            $dentry_count = sizeof($dentry_chicks); $medvac_count = sizeof($medvac_qty);
                            $sale_count = sizeof($sale_qty); $trout_count = sizeof($trout_qty);

                            $today = date("Y-m-d",strtotime($_POST['tdate']));
                            $opening_date = strtotime($today."-1 days"); $close_date = strtotime($today);
                            
                            $medvac_names = "";
                            $currentDate = $age = $open_chicks_in = $open_feeds_in = $open_culls_consume = $present_culls_consume = $open_mort_consume = $display_fcr = $display_cfcr = $present_mort_consume = $open_feed_consume = $open_medvacs_in = $present_chicks_in = $present_feeds_in = $present_feed_consume = $present_medvacs_in = $present_birds_trout = $present_feeds_trout = $present_medvacs_trout = $open_birds_trout = $open_feeds_trout = $open_medvacs_trout = $open_birds_sale = $open_birdwt_sale = $present_birdwt_sale = $open_feeds_sale = $open_medvacs_sale = $present_medvacs_sale = $present_feeds_sale = $present_birds_sale = $present_medvacs_consume = $open_medvacs_consume = 0;
                            for ($currentDate = ((int)$start_date); $currentDate <= ((int)$end_date); $currentDate += (86400)) { $age++;
                                $prev_date = date("Y-m-d",((int)$currentDate));
                                //Purchased Quantity
                                for($i = 1;$i <= $pur_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Balances
                                        if(!empty($pur_qty[$prev_date."@".$chick_code."@".$i])){
                                            $open_chicks_in = $open_chicks_in + $pur_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($pur_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_in = $open_feeds_in + $pur_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($pur_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $open_medvacs_in = $open_medvacs_in + $pur_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's Balances
                                        if(!empty($pur_qty[$prev_date."@".$chick_code."@".$i])){
                                            $open_chicks_in = $open_chicks_in + $pur_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        if(!empty($pur_qty[$prev_date."@".$chick_code."@".$i])){
                                            $present_chicks_in = $present_chicks_in + $pur_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($pur_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_in = $present_feeds_in + $pur_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($pur_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $present_medvacs_in = $present_medvacs_in + $pur_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else{ }
                                }
                                //Transferred In Quantity
                                for($i = 1;$i <= $trin_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Balances
                                        if(!empty($trin_qty[$prev_date."@".$chick_code."@".$i])){
                                            $open_chicks_in = $open_chicks_in + $trin_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trin_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_in = $open_feeds_in + $trin_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($trin_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $open_medvacs_in = $open_medvacs_in + $trin_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's  Balances
                                        if(!empty($trin_qty[$prev_date."@".$chick_code."@".$i])){
                                            $open_chicks_in = $open_chicks_in + $trin_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        if(!empty($trin_qty[$prev_date."@".$chick_code."@".$i])){
                                            $present_chicks_in = $present_chicks_in + $trin_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trin_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_in = $present_feeds_in + $trin_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($trin_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $present_medvacs_in = $present_medvacs_in + $trin_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else{ }
                                }
                                //Consume Day Record Quantity
                                for($i = 1;$i <= $dentry_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Consumption
                                        if(!empty($dentry_chicks[$prev_date."@".$i])){
                                            $day_dtails = explode("@",$dentry_chicks[$prev_date."@".$i]);

                                       

                                            $open_mort_consume = $open_mort_consume + $day_dtails[6];
                                            $open_culls_consume = $open_culls_consume + $day_dtails[7];
                                            $open_feed_consume = $open_feed_consume + ($day_dtails[9] + $day_dtails[11]);
                                            $act_body_weight = $day_dtails[12];
                                            $remarks = $day_dtails[15];
                                            $dieases_codes = $day_dtails[16];
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's  Balances
                                        if(!empty($dentry_chicks[$prev_date."@".$i])){
                                            $day_dtails = explode("@",$dentry_chicks[$prev_date."@".$i]);
                                           
                                            $dentry_age = $day_dtails[5];
                                            $present_mort_consume = $present_mort_consume + $day_dtails[6];
                                            $present_culls_consume = $present_culls_consume + $day_dtails[7];
                                            $present_feed_consume = $present_feed_consume + ($day_dtails[9] + $day_dtails[11]);
                                            $act_body_weight = $day_dtails[12];
                                            $remarks = $day_dtails[15];
                                            $dieases_codes = $day_dtails[16];
                                        }
                                    }
                                    else{ }
                                }
                                //Consume MedVac Record Quantity
                                for($i = 1;$i <= $medvac_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening MedVac Consume
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($medvac_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $open_medvacs_consume = $open_medvacs_consume + $medvac_qty[$prev_date."@".$mvcodes."@".$i];
                                                /*if($medvac_names == ""){
                                                    if(!empty($item_name[$mvcodes])){
                                                        $medvac_names = $item_name[$mvcodes];
                                                    }
                                                }
                                                else{
                                                    if(!empty($item_name[$mvcodes])){
                                                        $medvac_names = $medvac_names.",".$item_name[$mvcodes];
                                                    }
                                                }*/
                                            }
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's  Balances
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($medvac_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $present_medvacs_consume = $present_medvacs_consume + $medvac_qty[$prev_date."@".$mvcodes."@".$i];
                                                if($medvac_names == ""){
                                                    if(!empty($item_name[$mvcodes])){
                                                        $medvac_names = $item_name[$mvcodes];
                                                    }
                                                }
                                                else{
                                                    if(!empty($item_name[$mvcodes])){
                                                        $medvac_names = $medvac_names.",".$item_name[$mvcodes];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else{ }
                                }
                                //Sale Quantity
                                for($i = 1;$i <= $sale_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Sale
                                        if(!empty($sold_birds[$prev_date."@".$bird_code."@".$i])){
                                            $open_birds_sale = $open_birds_sale + $sold_birds[$prev_date."@".$bird_code."@".$i];
                                        }
                                        if(!empty($sale_qty[$prev_date."@".$bird_code."@".$i])){
                                            $open_birdwt_sale = $open_birdwt_sale + $sale_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($sale_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_sale = $open_feeds_sale + $sale_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($sale_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $open_medvacs_sale = $open_medvacs_sale + $sale_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's Balances
                                        if(!empty($sold_birds[$prev_date."@".$bird_code."@".$i])){
                                            $present_birds_sale = $present_birds_sale + $sold_birds[$prev_date."@".$bird_code."@".$i];
                                        }
                                        if(!empty($sale_qty[$prev_date."@".$bird_code."@".$i])){
                                            $present_birdwt_sale = $present_birdwt_sale + $sale_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($sale_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_sale = $present_feeds_sale + $sale_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($sale_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $present_medvacs_sale = $present_medvacs_sale + $sale_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else{ }
                                }
                                //Trout Quantity
                                for($i = 1;$i <= $trout_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Sale
                                        if(!empty($trout_qty[$prev_date."@".$bird_code."@".$i])){
                                            $open_birds_trout = $open_birds_trout + $trout_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trout_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_trout = $open_feeds_trout + $trout_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($trout_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $open_medvacs_trout = $open_medvacs_trout + $trout_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's Balances
                                        if(!empty($trout_qty[$prev_date."@".$bird_code."@".$i])){
                                            $present_birds_trout = $present_birds_trout + $trout_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trout_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_trout = $present_feeds_trout + $trout_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        foreach($medvac_code as $mvcodes){
                                            if(!empty($trout_qty[$prev_date."@".$mvcodes."@".$i])){
                                                $present_medvacs_trout = $present_medvacs_trout + $sale_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else{ }
                                }
                            }
                            $display_farmlatitude = $farm_latitude[$fetch_fcode];
                            $display_farmlongitude = $farm_longitude[$fetch_fcode];
                            $display_farmcode = $farm_ccode[$fetch_fcode];
                            $display_farmname = $farm_name[$fetch_fcode];
                            $display_farmbatch = $batch_name[$batches];
                            $display_batchbook = $batch_book[$batches];
                            if(!empty($supervisor_name[$farm_supervisor[$fetch_fcode]])){
                                $display_supervisor = $supervisor_name[$farm_supervisor[$fetch_fcode]];
                            }
                            else{
                                $display_supervisor = "";
                            }
                            $display_farmer = $farmer_name[$farm_farmer[$fetch_fcode]];
                            $display_age = $display_age1 = 0;
                            //$display_age1 = ((int)$dend_date) - ((int)$dstart_date);
                            //$display_age = ($display_age1 / (60 * 60 * 24));
                            $display_age = $dentry_age;
                            //Display Feed Section
                            $display_feeds_open = $open_feeds_in - $open_feed_consume - $open_feeds_sale - $open_feeds_trout;
                            $display_feeds_in = $present_feeds_in;
                            $display_feed_consume = $present_feed_consume;
                            $display_feed_out = $present_feeds_sale + $present_feeds_trout;
                            $display_feed_stock = (($display_feeds_open + $display_feeds_in) - ($display_feed_consume + $display_feed_out));
                            $display_feed_cumulate = $open_feed_consume + $present_feed_consume;

                            if(!empty($bstd_body_weight[$display_age])){
                                $display_stdbodyWt = ($bstd_body_weight[$display_age] / 1000);
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
                            $display_present_obirds = $open_chicks_in - ($open_mort_consume + $open_culls_consume + $open_birds_sale + $open_birds_trout);
                            $display_mort = $present_mort_consume;
                            $display_cummort = $open_mort_consume + $present_mort_consume;
                            if($display_present_obirds > 0 && $display_mort > 0){
                                
                                $display_mortper = (((float)$display_mort / (float)$display_present_obirds) * 100);
                            }
                            else{
                                $display_mortper = 0;
                            }
                            if($display_obirds > 0 && $display_cummort > 0){
                                $display_cummortper = (((float)$display_cummort / (float)$display_obirds) * 100);
                            }
                            else{
                                $display_mortper = $display_cummortper = 0;
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
                            $display_culls = $open_culls_consume + $present_culls_consume;
                            $display_lifted = $open_birds_sale + $present_birds_sale;
                            $display_liftedwt = $open_birdwt_sale + $present_birdwt_sale;
                            $display_bbirds = $display_obirds - $display_cummort - $display_culls - $display_lifted;
                            $display_medvacname = $medvac_names;
                            $display_remarks = $remarks;
                            
                            $display_medvacqty = $open_medvacs_consume + $present_medvacs_consume;

                            $consumed_feeds = $open_feed_consume + $present_feed_consume;
                            $sales_birds_qty = $open_birdwt_sale + $present_birdwt_sale;
                            $sales_birds_nos = $open_birds_sale + $present_birds_sale;
                            if($sales_birds_qty > 0 && $sales_birds_nos > 0){
                                $display_availableavg_body_wt = ($sales_birds_qty / $sales_birds_nos);
                            }
                            else{
                                $display_availableavg_body_wt = 0;
                            }

                            if($sales_birds_qty > 0 && $consumed_feeds > 0) {
                                $display_fcr = ($consumed_feeds / $sales_birds_qty);
                            }
                            else{
                                $display_fcr = 0;
                            }
                            if($display_availableavg_body_wt > 0){
                                $display_cfcr = (((2 - ($display_availableavg_body_wt)) / 4) + $display_fcr);
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
                                $display_farm_location = "https://broiler.poulsoft.com/records/ShowLocation.php?lat=".$display_farmlatitude."&lng=".$display_farmlongitude."&farm_name=".$display_farmname."&type=Farm Location";
                              }
                              else{
                                  $display_farm_location = "";
                              }
                              if(!empty($latitude) && !empty($longitude)){
                                  /*$display_entry_location = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=AIzaSyCQO_zZX9F0UzrOzCYsXRAAbhwjhSSXWaw";*/
                                   $display_entry_location = "https://broiler.poulsoft.com/records/ShowLocation.php?lat=".$latitude."&lng=".$longitude."&farm_name=".$display_farmname."&type=Daily Entry Farm Location";
                              }
                              else{
                                  $display_entry_location = "";
                              }
                              if(!empty($display_farmlatitude) && $display_farmlatitude != 0.0 && !empty($display_farmlongitude) && $display_farmlongitude != 0.0 && !empty($latitude) && $latitude != 0.0 && !empty($longitude) && $longitude != 0.0 ){
                                   $display_differ_location =  computeDistance($display_farmlatitude,$display_farmlongitude,$latitude,$longitude)."";
                                    $display_differ_location_link = "https://broiler.poulsoft.com/records/ShowDirection.php?lat1=".$display_farmlatitude."&lng1=".$display_farmlongitude."&lat2=".$latitude."&lng2=".$longitude."&farm_name=".$display_farmname."&type=Daily Entry Farm Location";
                               }else{
                                  $display_differ_location = "";
                                  $display_differ_location_link = "";
                               }
                            
                            if(date("d.m.Y",((int)$dend_date)) != "01.01.1970" && $dend_date == strtotime($_POST['tdate'])){
                                if($display_obirds > 0 || $present_chicks_in > 0 || $display_feeds_open > 0 || $display_feeds_in > 0 || $display_feed_stock > 0){ $slno++;
                                    $total_feeds_open = $total_feeds_open + $display_feeds_open;
                                    $total_feeds_in = $total_feeds_in + $display_feeds_in;
                                    $total_feed_consumed += $display_feed_consume;
                                    $total_feed_stock = $total_feed_stock + $display_feed_stock;
                                    $total_feed_cumulate = $total_feed_cumulate + $display_feed_cumulate;
                                    $total_obirds =  $total_obirds + $display_obirds;
                                    $display_total_present_obirds = $display_total_present_obirds + $display_present_obirds;
                                    $total_mort = $total_mort + $display_mort;
                                    $total_culls = $total_culls + $display_culls;
                                    $total_lifted = $total_lifted + $display_lifted;
                                    $total_liftedwt = $total_liftedwt + $display_liftedwt;
                                    $total_bbirds = $total_bbirds + $display_bbirds;
                                    $total_medvac_qty = $total_medvac_qty + $display_medvacqty;
                                    $display_total_cummort = $display_total_cummort + $display_cummort;

                                    $str_arr = explode (",", $dieases_codes); 
                                
                                    for($a = 0;$a<count($str_arr);$a++){
                                        if($a == 0){
                                            $display_dieases_codes = $dieases_name[$str_arr[$a]];
                                        }else{
                                            $display_dieases_codes .= ",".$dieases_name[$str_arr[$a]];
                                        }
                                    
                                    }
                                ?>
                                <tr>
                                    <td title="Sl.No."><?php echo $slno; ?></td>
                                    <td title="Farm Code"><?php echo $display_farmcode; ?></td>
                                    <td title="Farm Name"><?php echo $display_farmname; ?></td>
                                    <td title="Farm Batch"><?php echo $display_farmbatch; ?></td>
                                    <td title="Farm Batch"><?php echo $display_batchbook; ?></td>
                                    <td title="Supervisor"><?php echo $display_supervisor; ?></td>
                                    <!--<td title="Latest Entry Date"><?php //if(date("d.m.Y",((int)$dend_date)) == "01.01.1970"){ echo "<b style='color:red'>Not Started</b>"; } else{ echo date("d.m.Y",((int)$dend_date)); } ?></td>-->
                                    <td style="text-align:center;" title="Age"><?php if(date("d.m.Y",((int)$dend_date)) == "01.01.1970"){ echo "0"; } else{ echo round($display_age); } ?></td>

                                    <td style="text-align:right;" title="Opening Birds"><?php echo str_replace(".00","",number_format_ind(round($display_obirds,2))); ?></td>
                                    <td style="text-align:right;" title="Opening Birds"><?php echo str_replace(".00","",number_format_ind(round($display_present_obirds,2))); ?></td>
                                    <td style="text-align:right;" title="Mortality"><?php echo str_replace(".00","",number_format_ind(round($display_mort,2))); ?></td>
                                    <td style="text-align:right;" title="Mortality %"><?php echo number_format_ind(round($display_mortper,2)); ?></td>
                                    <?php
                                    if(!empty($display_mortimage)){
                                    ?>
                                    <td style="text-align:right;" title="Mort Image"><a href="javascript:void(0)" onclick="<?php echo $mort_img_list; ?>" title="<?php echo $mort_img_list; ?>"><?php echo "mortImage-".$slno; ?></a></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td title="Mort Image"></td>
                                    <?php
                                    }
                                    ?>
                                    
                                    <td style="text-align:right;" title="Cumulative Mortality"><?php echo str_replace(".00","",number_format_ind(round($display_cummort,2))); ?></td>
                                    <td style="text-align:right;" title="Cumulative Mortality %"><?php echo number_format_ind(round($display_cummortper,2)); ?></td>
                                    <td style="text-align:right;" title="Culls"><?php echo str_replace(".00","",number_format_ind(round($display_culls,2))); ?></td>
                                    <?php
                                    if(!empty($display_cullimage)){
                                    ?>
                                    <td style="text-align:right;" title="Cull Image"><a href="javascript:void(0)" onclick="<?php echo $cull_img_list; ?>" title="<?php echo $cull_img_list; ?>"><?php echo "cullImage-".$slno; ?></a></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td title="Cull Image"></td>
                                    <?php
                                    }
                                    ?>
                                    <td style="text-align:right;" title="Lifted"><?php echo str_replace(".00","",number_format_ind(round($display_lifted,2))); ?></td>
                                    <td style="text-align:right;" title="Lifted Weight"><?php echo number_format_ind(round($display_liftedwt,2)); ?></td>
                                    <td style="text-align:right;" title="Balance Birds"><?php echo str_replace(".00","",number_format_ind(round($display_bbirds,2))); ?></td>
                                     
                                    <td style="text-align:right;" title="Std Body Weight"><?php echo number_format_ind(round($display_stdbodyWt,2)); ?></td>
                                    <?php
                                    if(number_format_ind($display_bodyWt) == "0.00"){
                                    ?>
                                    <td style="text-align:right;color:black;" title="Body Weight"><?php echo number_format_ind(round($display_bodyWt / 1000,2)); ?></td>
                                    <?php
                                    }
                                    else if((float)$display_bodyWt >= (float)$display_stdbodyWt){
                                    ?>
                                    <td style="text-align:right;color:red;" title="Body Weight"><?php echo number_format_ind(round($display_bodyWt / 1000,3)); ?></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td style="text-align:right;color:green;" title="Body Weight"><?php echo number_format_ind(round($display_bodyWt / 1000,3)); ?></td>
                                    <?php
                                    }
                                    ?>
                                    <td style="text-align:right;" title="Std F.C.R"><?php echo number_format_ind(round($display_stdfcr,2)); ?></td>
                                    
                                    <?php
                                    if($display_stdfcr < $display_fcr){
                                    ?>
                                    <td style="text-align:right;color:red;" title="F.C.R"><?php echo number_format_ind(round($display_fcr,2)); ?></td>
                                    <?php
                                    }
                                    else if(number_format_ind($display_fcr) == "0.00"){
                                    ?>
                                    <td style="text-align:right;color:black;" title="F.C.R"><?php echo number_format_ind(round($display_fcr,2)); ?></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td style="text-align:right;color:green;" title="F.C.R"><?php echo number_format_ind(round($display_fcr,2)); ?></td>
                                    <?php
                                    }
                                    ?>
                                    
                                    <td style="text-align:right;" title="C.F.C.R"><?php echo number_format_ind(round($display_cfcr,2)); ?></td>

                                    <td style="text-align:right;" title="Feed OB"><?php echo number_format_ind(round(($display_feeds_open),2)); ?></td>
                                    <td style="text-align:right;" title="Feed In"><?php echo number_format_ind(round(($display_feeds_in),2)); ?></td>
                                    <td style="text-align:right;" title="Feed Out"><?php echo number_format_ind(round(($display_feed_out),2)); ?></td>
                                    <td style="text-align:right;" title="Feed Consumed"><?php echo number_format_ind(round(($display_feed_consume),2)); ?></td>
                                    <td style="text-align:right;" title="Feed Stock"><?php echo number_format_ind(round(($display_feed_stock),2)); ?></td>
                                    <td style="text-align:right;" title="Cumulative Feed"><?php echo number_format_ind(round(($display_feed_cumulate),2)); ?></td>

                                    <?php
                                    if(!empty($display_feedimage)){
                                    ?>
                                    <td style="text-align:right;" title="Feed Image"><a href="javascript:void(0)" onclick="<?php echo $feed_img_list; ?>" title="<?php echo $feed_img_list; ?>"><?php echo "feedImage-".$slno; ?></a></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td title="Feed Image"></td>
                                    <?php
                                    }
                                    ?>

                                  
                                    <!--<td style="white-space: normal;" title="Medicine Name"><?php //echo $display_medvacname; ?></td>
                                    <td style="text-align:right;" title="Medicine Quantity"><?php //echo $display_medvacqty; ?></td>-->

                                    <td title="Line"><?php echo $display_line; ?></td>
                                    <td title="Place"><?php echo $display_place; ?></td>
                                    <td title="Contact"><?php echo $display_contact; ?></td>
                                    <td title="Entry Time"><?php echo date("d.m.Y H:i:s A",strtotime($display_addedtime)); ?></td>
                                    <td title="Entry By"><?php echo $supervisor_name[$db_emp_code[$display_addedemp]]; ?></td>
                                    <td title="remaks"><?php echo $display_remarks; ?></td>
                                    <td title="dieases_codes"><?php echo $display_dieases_codes; ?></td>
                                    <?php
                                    if(!empty($display_farm_location)){
                                    ?>
                                    <td style="text-align:right;" title="Farm Location"><a href="<?php echo $display_farm_location; ?>" target="_BLANK"><?php echo "Location-".$slno; ?></a></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td title="Farm Location"></td>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if(!empty($display_entry_location)){
                                    ?>
                                    <td style="text-align:right;" title="Entry Location"><a href="<?php echo $display_entry_location; ?>" target="_BLANK"><?php echo "Location-".$slno; ?></a></td>
                                    <?php
                                    }
                                    else{
                                    ?>
                                    <td title="Entry Location"></td>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if(!empty($display_differ_location)){
                                    ?>
                                   <!--- <td style="text-align:right;" title="Difference K.M"><a href="<?php echo $display_differ_location_link; ?>" target="_BLANK"><?php echo $display_differ_location; ?></a></td> --->
                                    <?php
                                    }
                                    else{
                                    ?>
                                   <!--- <td title="Difference K.M"></td> --->
                                    <?php
                                    }
                                    ?>
                                 </tr> 
                                <?php
                                }
                            }
                        } 
                    
                    }
                ?>
               
               
            </tbody>
            
            <tfoot>
            <tr class="thead4">
                <th style="text-align:left; border-right: 0px;"></th>
				<th style="text-align:left; border-left: 0px;border-right: 0px;"></th>
				<th style="text-align:left; border-left: 0px;border-right: 0px;"></th>
				<th style="text-align:left; border-left: 0px;border-right: 0px;"></th>
				<th style="text-align:left; border-left: 0px;border-right: 0px;"></th>
				<th style="text-align:left; border-left: 0px;border-right: 0px;"></th>
                <th style="text-align:center; border-left: 0px;">Total</th>
                
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_obirds)); ?></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($display_total_present_obirds)); ?></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",$total_mort); ?></th>
				<th style="text-align:right;">
                <?php
                    if($total_mort > 0 && $display_total_present_obirds > 0){
                        echo number_format_ind(($total_mort / $display_total_present_obirds) * 100);
                    }
                    else{
                        echo number_format_ind(0);
                    }
                    
                ?>
                </th>
				<th style="text-align:left;"></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($display_total_cummort)); ?></th>
				<th style="text-align:right;">
                <?php
                if($display_total_cummort > 0 && $total_obirds > 0){
                    echo number_format_ind((($display_total_cummort / $total_obirds ) * 100));
                }
                else{
                    echo number_format_ind(0);
                }
                ?>
                </th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_culls)); ?></th>
                <th style="text-align:left;"></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_lifted)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_liftedwt); ?></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_bbirds)); ?></th>

				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>

				<th style="text-align:right;"><?php echo number_format_ind($total_feeds_open); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feeds_in); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($display_feed_out); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feed_consumed); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feed_stock); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feed_cumulate); ?></th>
				<th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
                <th style="text-align:left;"></th>
               <!--- <th style="text-align:left;"></th> --->
            </tr>
                </tfoot>
            <?php
            }?>
        </table>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>

        <script>
        $(document).ready(function(){
            var table =  $('#mine').DataTable({
                //scrollY: '600px',
            
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
            /*$('#mine tfoot tr').each(function(){
                alert("hide flag"+hide);
            if(hide){
                alert("hide flag is set and index is "+ti);

                $('th:eq(' + ti + ')',this).hide(100);
            }else{
                alert("hide flag is not checked and index is "+ti);
              
                $('th:eq(' + ti + ')',this).show(100);
            }    
        });*/

        });
       /* $('#mine tfoot th').each( function () {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            } );

        $('#myInput').keyup( function() {
                table.draw();
            } );
            $('input.column_filter').on( 'keyup click', function () {
                filterColumn( $(this).parents('tr').attr('data-column') );
            });*/
            
            });
        </script>
        
    </body>
</html>
<?php
include "header_foot.php";
?>