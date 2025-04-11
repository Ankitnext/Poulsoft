<?php
//broiler_tripsheet.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_tripsheet.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_tripsheet.php?db=$db&userid=".$user_code;
}

$file_name = "Employee Visit Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("trip_sheet", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.trip_sheet LIKE poulso6_admin_broiler_broilermaster.trip_sheet;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_designation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_designation LIKE poulso6_admin_broiler_broilermaster.broiler_designation;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_emp_allowance_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_emp_allowance_master LIKE poulso6_admin_broiler_broilermaster.broiler_emp_allowance_master;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_secbrch_mapping", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_secbrch_mapping LIKE poulso6_admin_broiler_broilermaster.broiler_secbrch_mapping;"; mysqli_query($conn,$sql1); }

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $line_code = $line_name = $line_branch = array();
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$farm_code = $farm_name = $farm_branch = $farm_line = $farm_svr = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
}

$sql = "SELECT * FROM `broiler_vehicle` WHERE `dflag` = '0' ORDER BY `registration_number` ASC";
$query = mysqli_query($conn,$sql); $vehicle_code = $vehicle_name = array();
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $emp_code = $emp_name = $emp_desig = array();
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; $emp_desig[$row['code']] = $row['desig_code']; $emp_sector[$row['code']] = $row['warehouse']; }

$sql = "SELECT * FROM `main_access`";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; }

$sql = "SELECT * FROM `broiler_secbrch_mapping` WHERE `dflag` = '0' Order BY `branch_code` ASC";
$query = mysqli_query($conn,$sql); $sector_branch = $branch_sector = array();
while($row = mysqli_fetch_assoc($query)){ $sector_branch[$row['sector_code']] = $row['branch_code']; $branch_sector[$row['branch_code']] = $row['sector_code']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = "all"; $excel_type = "display"; $tnc = 0;
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $tnc = $_POST['tnc'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="<?php echo (int)$days + 1; ?>" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="23">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:190px;">
                                    <label for="branches">Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:190px;">
                                    <label for="lines">Line</label>
                                    <select name="lines" id="lines" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:190px;">
                                    <label for="supervisors">Employee</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($emp_code as $scode){ if($emp_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $emp_name[$scode]; ?></option>
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
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group" style="visibility:hidden;">
                                    <label>Trip Not Completed</label>
                                    <input type="checkbox" name="tnc" id="tnc" class="form-control" value="1" style="width:auto;" <?php if((int)$tnc == 1){ echo "checked"; } ?> checked />
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
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center">
                <tr align="center">
                    <th id='order_date'>Date</th>
                    <th id='order'>Branch</th>
                    <th id='order'>Trip No.</th>
                    <th id='order'>Vehicle No.</th>
                    <th id='order'>Start Place</th>
                    <th id='order_num'>Start Km</th>
                    <th id='order'>Start Km Photo</th>
                    <th id='order'>End place</th>
                    <th id='order_num'>End Km</th>
                    <th id='order'>End Km Photo</th>
                    <th id='order_num'>Total Km</th>
                    <th id='order_num'>No. of visit Farm</th>
                    <th id='order_num'>Per KM Rate</th>
                    <th id='order_num'>KM Amount</th>
                    <th id='order_num'>D.A.</th>
                    <th id='order_num'>T.A.</th>
                    <th id='order_num'>Amount</th>
                    <th id='order'>Supervisor</th>
                    <th id='order'>Start Time</th>
                    <th id='order'>End Time</th>
                    <th id='order'>Total Time</th>
                    <th id='order'>Start Entry Location</th>
                    <th id='order'>End Entry Location</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1" id="tbody1">
            <?php
                function alter_vehicleno($vstn){
                    $va_str = $vc_str = $vt_str = array(); $vo_str = ""; $d = 0;
                    $va_str = str_split(preg_replace('/[^A-Za-z0-9]/', '', $vstn));
                    $i = 0;
                    foreach($va_str as $va){
                        $i++;
                        if(is_numeric($va) == true){ $vt_str[$i] = "N"; } else{ $vt_str[$i] = "S"; }

                        if($i == 1){ $vo_str .= $va; }
                        else{ $d = $i - 1; if($vt_str[$i] != $vt_str[$d]){ $vo_str .= "-".$va; } else{ $vo_str .= $va; } }
                    }
                    return strtoupper($vo_str);
                }

                $re_fdate = $fdate." 00:00:00";
                $re_tdate = $tdate." 23:59:59";

                $sql = "SELECT COUNT(*) as noofVisits,addedemp,DATE(addedtime) as added_date,farm_code FROM `broiler_daily_record` WHERE addedtime BETWEEN '$re_fdate' AND '$re_tdate' AND active = 1 AND dflag = 0 GROUP BY addedemp,added_date,farm_code ORDER BY `added_date` ASC;";
               $query = mysqli_query($conn,$sql);
                if(mysqli_num_rows($query) > 0){
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['added_date']."@".$row['addedemp'];
                        $noOfVisits[$key] = $noOfVisits[$key] + 1;
                    }
                }
                

                $sbm_flag = 1; $brh_filter = "";
                if($branches != "all"){ $farm_filter .= " AND `branch_code` = '$branches'"; } if($lines != "all"){ $farm_filter .= " AND `line_code` = '$lines'"; $sbm_flag = 1;}
                if($supervisors != "all"){ $farm_filter .= " AND `supervisor_code` = '$supervisors'"; $sbm_flag = 0; }

                $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'".$farm_filter." ORDER BY `farm_code` ASC";
                $query = mysqli_query($conn,$sql); $sup_list = ""; $sup_alist = array();
                while($row = mysqli_fetch_assoc($query)){
                    $sup_alist[$sp_emp_code[$row['supervisor_code']]] = $sp_emp_code[$row['supervisor_code']];
                }
                if($sbm_flag == 1){
                    if($branches != "all"){ $brh_filter = " AND `branch_code` = '$branches'"; }
                    $sql = "SELECT * FROM `broiler_secbrch_mapping` WHERE `dflag` = '0'".$brh_filter."Order BY `branch_code` ASC";
                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                    if($count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $scode = $row['sector_code'];

                            foreach($emp_code as $ecode){
                                if(!empty($emp_sector[$ecode]) && $emp_sector[$ecode] == $scode){
                                    $sup_alist[$sp_emp_code[$ecode]] = $sp_emp_code[$ecode];
                                }
                            }
                        }
                    }
                }
                $sup_list = implode("','",$sup_alist);
                if($sup_list == ""){ $sup_list = $sp_emp_code[$supervisors]; }
                if($branches == "all" && $lines == "all" && $supervisors == "all"){ $emp_filter = ""; } else{ $emp_filter = " AND `added_empcode` IN ('$sup_list')"; }

                /*Fetch Employee Allowances*/
                $sql = "SELECT * FROM `broiler_emp_allowance_master` WHERE `active` = '1' AND `dflag` = '0' AND (`fdate` <= '$fdate' || `tdate` >= '$tdate') ORDER BY `fdate` ASC";
                $query = mysqli_query($conn,$sql); $ea_cnt = mysqli_num_rows($query); $per_km_rate = $daily_allowance = $travel_allowance = $ea_key = array();
                if($ea_cnt > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['desig_code']."@".$row['branch_code']."@".$row['fdate']."@".$row['tdate'];
                        $per_km_rate[$key] = $row['per_km_rate'];
                        $daily_allowance[$key] = $row['daily_allowance'];
                        $travel_allowance[$key] = $row['travel_allowance'];
                        $ea_key[$key] = $key;
                    }
                }
                
                $tot_amt = 0; $noff_visited = $trip_allow_type = $trip_pkm_cost = $trip_da_amt = $trip_ta_amt = array();

                $sql_record = "SELECT * FROM `trip_sheet` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$emp_filter." AND `dflag` = '0' AND `active` = '1' ORDER BY `date`,`trnum`,`id` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $tkey = $row['date']."@".$row['trnum'];
                    $trip_code[$tkey] = $tkey;
                    $trip_date[$tkey] = $row['date'];
                    $trip_trnum[$tkey] = $row['trnum'];
                    //$trip_vehicle[$tkey] = $row['vch_number'];
                    $trip_vehicle[$tkey] = alter_vehicleno($row['vch_number']);
                    $trip_totalkm[$tkey] = $row['total_km'];

                    $dbe_code = $db_emp_code[$row['added_empcode']];
                    $trip_empname[$tkey] = $emp_name[$dbe_code];
                    $trip_addedemp[$tkey] = $row['added_empcode'];

                    $trip_sbcode = "";
                    //echo "<br/>".$farm_svr[$dbe_code]."@".$dbe_code;
                    if(empty($farm_branch[$farm_svr[$dbe_code]]) || $farm_branch[$farm_svr[$dbe_code]] == ""){
                        $trip_empbranch[$tkey] = $sector_name[$emp_sector[$dbe_code]];

                        if(empty($sector_branch[$emp_sector[$dbe_code]]) || $sector_branch[$emp_sector[$dbe_code]] == ""){
                            $trip_sbcode = $emp_sector[$dbe_code];
                        }
                        else{ $trip_sbcode = $sector_branch[$emp_sector[$dbe_code]]; }
                    }
                    else{
                        $trip_empbranch[$tkey] = $branch_name[$farm_branch[$farm_svr[$dbe_code]]];
                        $trip_sbcode = $farm_branch[$farm_svr[$dbe_code]];
                    }
                    
                    
                    //No. Of Farm Visited Calculations
                    if(empty($farm_name[$row['farm_code']]) || $farm_name[$row['farm_code']] == ""){ }
                    else{ $noff_visited[$tkey] += 1; }

                    //Employee Daily Allowance Calculations
                    $ldcode = $lbcode = $lfdate = $ltdate = "";
                    if($ea_cnt > 0){
                        foreach($ea_key as $key1){
                            $key2 = explode("@",$key1);
                            $adcode = $key2[0]; $abcode = $key2[1]; $afdate = $key2[2]; $atdate = $key2[3];
                            //echo "<br/>$adcode == $emp_desig[$dbe_code] <br/>$abcode == $trip_sbcode <br/>".$row['date']."@".$afdate;
                            if($adcode == $emp_desig[$dbe_code] && $abcode == $trip_sbcode && strtotime($row['date']) >= strtotime($afdate) && strtotime($row['date']) <= strtotime($atdate)){
                                if($lfdate == ""){
                                    $ldcode = $adcode; $lbcode = $abcode; $lfdate = $afdate; $ltdate = $atdate;
                                }
                                else if(strtotime($lfdate) < strtotime($afdate)){
                                    $ldcode = $adcode; $lbcode = $abcode; $lfdate = $afdate; $ltdate = $atdate;
                                }
                            }
                        }
                        if($ldcode == "" && $lbcode == "" && $lfdate == "" && $ltdate == ""){ }
                        else{
                            $hkey = $ldcode."@".$lbcode."@".$lfdate."@".$ltdate;
                            if(empty($per_km_rate[$hkey]) || $per_km_rate[$hkey] == ""){ $per_km_rate[$hkey] = 0; }
                            if(empty($daily_allowance[$hkey]) || $daily_allowance[$hkey] == ""){ $daily_allowance[$hkey] = 0; }
                            if(empty($travel_allowance[$hkey]) || $travel_allowance[$hkey] == ""){ $travel_allowance[$hkey] = 0; }

                            $trip_pkm_cost[$tkey] = $per_km_rate[$hkey];
                            $trip_ta_amt[$tkey] = $travel_allowance[$hkey];
                            $trip_da_amt[$tkey] = $daily_allowance[$hkey];
                        }
                    }

                    if($row['trip_type'] == "Start"){
                        if(!empty($farm_name[$row['farm_code']])){
                            $trip_start_farm[$tkey] = $farm_name[$row['farm_code']];
                            $trip_start_time[$tkey] = $row['addedtime'];
                        }
                        else{
                            $trip_start_farm[$tkey] = $row['farm_code'];
                            $trip_start_time[$tkey] = $row['addedtime'];
                        }
                        $trip_start_reading[$tkey] = $row['meter_reading'];
                        if(!empty($row['meter_image'])){
                            //$trip_start_image[$tkey] = "../AndroidApp_API/clientimages/".$client."/tripimages/".$row['meter_image'];
                            if( $row['addedtime'] < "2024-04-19 09:45:29" ){
                                if($row['addedtime'] < "2024-07-04 00:00:00"){
                                    $trip_start_image[$tkey] = "https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/tripimages/".$row['meter_image'];
                                    
                                }else{
                                    $trip_start_image[$tkey] = "../AndroidApp_API/clientimages/".$client."/tripimages/".$row['meter_image']; 
                                }
                               
                                
                            }else{
                                if($row['addedtime'] < "2024-07-04 00:00:00"){
                                    $trip_start_image[$tkey] = "https://broiler.poulsoft.net".$row['meter_image'];
                                  
                                }else{
                                    $trip_start_image[$tkey] = "..".$row['meter_image']; 
                                }
                            }
                        }
                        else{
                            $trip_start_image[$tkey] = "javascript:void(0)";
                        }

                        $latitude = $row['latitude'];
                        $longitude = $row['longitude'];

                        if(!empty($latitude) && !empty($longitude)){
                            /*$display_entry_location = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=AIzaSyCQO_zZX9F0UzrOzCYsXRAAbhwjhSSXWaw";*/
                            $display_entry_location_start[$tkey] = "https://broiler.poulsoft.org/records/ShowLocation.php?lat=".$latitude."&lng=".$longitude."&farm_name=".$trip_start_farm[$tkey]."&type=Daily Entry Farm Location";
                        }
                        else{
                            $display_entry_location_start[$tkey] = "";
                        }
                    }
                    else if($row['trip_type'] == "End"){
                        if(!empty($farm_name[$row['farm_code']])){
                            $trip_end_farm[$tkey] = $farm_name[$row['farm_code']];
                            $trip_end_time[$tkey] = $row['addedtime'];
                        }
                        else{
                            $trip_end_farm[$tkey] = $row['farm_code'];
                            $trip_end_time[$tkey] = $row['addedtime'];
                        }
                        $trip_end_reading[$tkey] = $row['meter_reading'];
                        if(!empty($row['meter_image'])){
                           // $trip_end_image[$tkey] = "../AndroidApp_API/clientimages/".$client."/tripimages/".$row['meter_image'];
                           if( $row['addedtime'] < "2024-04-20 06:33:52" ){
                                if($row['addedtime'] < "2024-07-04 00:00:00"){
                                    $trip_end_image[$tkey] = "https://broiler.poulsoft.net/AndroidApp_API/clientimages/".$client."/tripimages/".$row['meter_image'];
                                }else{
                                    $trip_end_image[$tkey] = "../AndroidApp_API/clientimages/".$client."/tripimages/".$row['meter_image'];
                                }
                            }else{
                                if($row['addedtime'] < "2024-07-04 00:00:00"){
                                    $trip_end_image[$tkey] = "https://broiler.poulsoft.net".$row['meter_image'];
                                }else{
                                    $trip_end_image[$tkey] = "..".$row['meter_image'];
                                }
                            
                            }
                        }
                        else{
                            $trip_end_image[$tkey] = "javascript:void(0)";
                        }

                        $latitude = $row['latitude'];
                        $longitude = $row['longitude'];

                        if(!empty($latitude) && !empty($longitude)){
                            /*$display_entry_location = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=AIzaSyCQO_zZX9F0UzrOzCYsXRAAbhwjhSSXWaw";*/
                            $display_entry_location_end[$tkey] = "https://broiler.poulsoft.org/records/ShowLocation.php?lat=".$latitude."&lng=".$longitude."&farm_name=".$trip_end_farm[$tkey]."&type=Daily Entry Farm Location";
                        }
                        else{
                            $display_entry_location_end[$tkey] = "";
                        }
                    }
                    else{ }
                }
                $slno = $total_start_reading = $total_end_reading = $total_final_reading = $t_hrs = $t_min = $t_sec = 0; $i = 0;
                $tnoff_visit = $ftpkm_amt = $ftda_amt = $ftta_amt = $ftea_amt = 0;
                foreach($trip_code as $trips){ $slno++; $i++;
                    if(isset($_POST['tnc']) == true){
                        //Employee Allowance Calculations-2
                        if(empty($trip_pkm_cost[$trips]) || $trip_pkm_cost[$trips] == ""){ $trip_pkm_cost[$trips] = 0; }
                        if(empty($trip_da_amt[$trips]) || $trip_da_amt[$trips] == ""){ $trip_da_amt[$trips] = 0; }
                        if(empty($trip_ta_amt[$trips]) || $trip_ta_amt[$trips] == ""){ $trip_ta_amt[$trips] = 0; }
                        if(empty($trip_totalkm[$trips]) || $trip_totalkm[$trips] == ""){ $trip_totalkm[$trips] = 0; }
                        $tpkm_prc = $tpkm_amt = $tta_cost = $tda_cost = $ea_tamt = 0;
                        
                        if((float)$trip_pkm_cost[$trips] > 0){ $tpkm_prc = $trip_pkm_cost[$trips];  $tpkm_amt = ((float)$trip_totalkm[$trips] * (float)$trip_pkm_cost[$trips]); }
                        if((float)$trip_ta_amt[$trips] > 0){ $tta_cost = $trip_ta_amt[$trips]; }
                        if((float)$trip_da_amt[$trips] > 0){ $tda_cost = $trip_da_amt[$trips]; }
                        $ea_tamt = (float)$tda_cost + (float)$tta_cost + (float)$tpkm_amt;
                        $ftpkm_amt += (float)$tpkm_amt; $ftda_amt += (float)$tda_cost; $ftta_amt += (float)$tta_cost; $ftea_amt += (float)$ea_tamt;

                        $key = $trip_date[$trips]."@".$trip_addedemp[$trips];
                        $tnoff_visit += (float)$noOfVisits[$key];

                        ?>
                        <tr>
                            <td style="text-align:left;"><?php echo date("d.m.Y",strtotime($trip_date[$trips])); ?></td>
                            <td style="text-align:left;"><?php echo $trip_empbranch[$trips]; ?></td>
                            <td style="text-align:left;"><?php echo $trip_trnum[$trips]; ?></td>
                            <td style="text-align:left;"><?php echo $trip_vehicle[$trips]; ?></td>
                            <td style="text-align:left;"><?php echo $trip_start_farm[$trips]; ?></td>
                            <td style="text-align:right;"><?php echo $trip_start_reading[$trips]; ?></td>
                            <td style="text-align:left;"><a href="<?php echo $trip_start_image[$trips]; ?>" target="_BLANK"><i class="fa-solid fa-image"></i><?php //echo " Start_".$slno; ?></a></td>
                            <td style="text-align:left;"><?php echo $trip_end_farm[$trips]; ?></td>
                            <td style="text-align:right;"><?php echo $trip_end_reading[$trips]; ?></td>
                            <td style="text-align:left;" title="<?php echo $trip_end_image[$trips]; ?>"><?php if($trip_start_image[$trips] != "javascript:void(0)"){ ?><a href="<?php echo $trip_end_image[$trips]; ?>" target="_BLANK"><i class="fa-solid fa-image"></i><?php //echo " End_".$slno; ?></a><?php } ?></td>
                            <!--<td style="text-align:left;"><?php //echo $trip_end_reading[$trips] - $trip_start_reading[$trips]; ?></td>-->
                            <td style="text-align:right;"><?php echo $trip_totalkm[$trips]; ?></td>

                            <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($noOfVisits[$key])); ?></td>
                            <td style="text-align:right;"><?php echo number_format_ind($tpkm_prc); ?></td>
                            <td style="text-align:right;"><?php echo number_format_ind($tpkm_amt); ?></td>
                            <td style="text-align:right;"><?php echo number_format_ind($tda_cost); ?></td>
                            <td style="text-align:right;"><?php echo number_format_ind($tta_cost); ?></td>
                            <td style="text-align:right;"><?php echo number_format_ind($ea_tamt); ?></td>

                            <td style="text-align:left;"><?php echo $trip_empname[$trips]; ?></td>
                            <td style="text-align:left;"><?php echo date("d.m.Y h:i:A",strtotime($trip_start_time[$trips])); ?></td>
                            <td style="text-align:left;"><?php echo date("d.m.Y h:i:A",strtotime($trip_end_time[$trips])); ?></td>
                            <?php
                                if(date("d.m.Y",strtotime($trip_start_time[$trips])) == "01.01.1970" || date("d.m.Y",strtotime($trip_end_time[$trips])) == "01.01.1970"){
                                    ?>
                                    <td style="text-align:left;color:red;">Trip Not Completed</td>
                                    <?php
                                }
                                else{
                                    //$trip_time = strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips]);
                                    //$time[$i] = date("H:i:s",($trip_time));

                                    // Calculating the difference between DateTime objects 
                                    /*$dateTimeObject1 = date_create($trip_start_time[$trips]);
                                    $dateTimeObject2 = date_create($trip_end_time[$trips]);
                                    $interval = date_diff($dateTimeObject1, $dateTimeObject2);
                                    $hours = $interval->h;
                                    $minutes = $interval->days * 24 * 60;
                                    $mns = $minutes += $interval->i;
                                    $secs =  $interval->s;
                                    $time[$i] = $trip_time = date("H:i:s",strtotime($hours.":".$mns.":".$secs));
                                    */
                                    $hours = (INT)((strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips])) / 60 / 60);
                                    $mns = (((INT)((strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips])) / 60)) - ($hours * 60));
                                    $secs = (((INT)((strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips])))) - ($hours * 60 * 60) - ($mns *60));

                                    if($hours < 10){ $hours = "0".$hours; }
                                    if($mns < 10){ $mns = "0".$mns; }
                                    if($secs < 10){ $secs = "0".$secs; }
                                    $trip_time = $hours.":".$mns.":".$secs;

                                    $t_hrs += (float)$hours;
                                    $t_min += (float)$mns;
                                    $t_sec += (float)$secs;
                                    ?>
                                    <td style="text-align:left;"><?php echo $trip_time; ?></td>
                                    <?php
                                }
                            ?>

                            <?php
                             if(!empty($display_entry_location_start[$trips])){ 
                                ?>
                                <td title='Start Entry Location'><a href="<?php echo $display_entry_location_start[$trips]; ?>" target='_BLANK'><?php echo "Start Location-".$slno; ?></a></td>
                            <?php }else{ ?>
                                <td title='Start Entry Location'></td>
                            <?php } ?>
                            <?php
                             if(!empty($display_entry_location_end[$trips])){ 
                                ?>
                                <td title='End Entry Location'><a href="<?php echo $display_entry_location_end[$trips]; ?>" target='_BLANK'><?php echo "End Location-".$slno; ?></a></td>
                            <?php }else{ ?>
                                <td title='End Entry Location'></td>
                            <?php } ?>
                            
                        </tr>
                    <?php
                        $total_start_reading = $total_start_reading + $trip_start_reading[$trips];
                        $total_end_reading = $total_end_reading + $trip_end_reading[$trips];
                        $total_final_reading = $total_final_reading + $trip_totalkm[$trips];
                    }
                    else{
                        if(date("d.m.Y",strtotime($trip_end_time[$trips])) != "01.01.1970"){
                          
                            
                            //Employee Allowance Calculations-2
                            if(empty($trip_pkm_cost[$trips]) || $trip_pkm_cost[$trips] == ""){ $trip_pkm_cost[$trips] = 0; }
                            if(empty($trip_da_amt[$trips]) || $trip_da_amt[$trips] == ""){ $trip_da_amt[$trips] = 0; }
                            if(empty($trip_ta_amt[$trips]) || $trip_ta_amt[$trips] == ""){ $trip_ta_amt[$trips] = 0; }
                            if(empty($trip_totalkm[$trips]) || $trip_totalkm[$trips] == ""){ $trip_totalkm[$trips] = 0; }
                            $tpkm_prc = $tpkm_amt = $tta_cost = $tda_cost = $ea_tamt = 0;
                            
                            if((float)$trip_pkm_cost[$trips] > 0){ $tpkm_prc = $trip_pkm_cost[$trips];  $tpkm_amt = ((float)$trip_totalkm[$trips] * (float)$trip_pkm_cost[$trips]); }
                            if((float)$trip_ta_amt[$trips] > 0){ $tta_cost = $trip_ta_amt[$trips]; }
                            if((float)$trip_da_amt[$trips] > 0){ $tda_cost = $trip_da_amt[$trips]; }
                            $ea_tamt = (float)$tda_cost + (float)$tta_cost + (float)$tpkm_amt;
                            $ftpkm_amt += (float)$tpkm_amt; $ftda_amt += (float)$tda_cost; $ftta_amt += (float)$tta_cost; $ftea_amt += (float)$ea_tamt;

                            $key = $trip_date[$trips]."@".$trip_addedemp[$trips];
                            $tnoff_visit += (float)$noOfVisits[$key];

                            ?>
                            <tr>
                                <td style="text-align:left;"><?php echo date("d.m.Y",strtotime($trip_date[$trips])); ?></td>
                                <td style="text-align:left;"><?php echo $trip_empbranch[$trips]; ?></td>
                                <td style="text-align:left;"><?php echo $trip_trnum[$trips]; ?></td>
                                <td style="text-align:left;"><?php echo $trip_vehicle[$trips]; ?></td>
                                <td style="text-align:left;"><?php echo $trip_start_farm[$trips]; ?></td>
                                <td style="text-align:right;"><?php echo $trip_start_reading[$trips]; ?></td>
                                <td style="text-align:left;"><a href="<?php echo $trip_start_image[$trips]; ?>" target="_BLANK"><i class="fa-solid fa-image"></i><?php //echo " Start_".$slno; ?></a></td>
                                <td style="text-align:left;"><?php echo $trip_end_farm[$trips]; ?></td>
                                <td style="text-align:right;"><?php echo $trip_end_reading[$trips]; ?></td>
                                <td style="text-align:left;" title="<?php echo $trip_end_image[$trips]; ?>"><?php if($trip_start_image[$trips] != "javascript:void(0)"){ ?><a href="<?php echo $trip_end_image[$trips]; ?>" target="_BLANK"><i class="fa-solid fa-image"></i><?php //echo " End_".$slno; ?></a><?php } ?></td>
                                <!--<td style="text-align:left;"><?php //echo $trip_end_reading[$trips] - $trip_start_reading[$trips]; ?></td>-->
                                <td style="text-align:right;"><?php echo $trip_totalkm[$trips]; ?></td>

                                <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($noOfVisits[$key])); ?></td>
                                <td style="text-align:right;"><?php echo number_format_ind($tpkm_prc); ?></td>
                                <td style="text-align:right;"><?php echo number_format_ind($tpkm_amt); ?></td>
                                <td style="text-align:right;"><?php echo number_format_ind($tda_cost); ?></td>
                                <td style="text-align:right;"><?php echo number_format_ind($tta_cost); ?></td>
                                <td style="text-align:right;"><?php echo number_format_ind($ea_tamt); ?></td>

                                <td style="text-align:left;"><?php echo $trip_empname[$trips]; ?></td>
                                <td style="text-align:left;"><?php echo date("d.m.Y h:i:A",strtotime($trip_start_time[$trips])); ?></td>
                                <td style="text-align:left;"><?php echo date("d.m.Y h:i:A",strtotime($trip_end_time[$trips])); ?></td>
                                <?php
                                    if(date("d.m.Y",strtotime($trip_start_time[$trips])) == "01.01.1970" || date("d.m.Y",strtotime($trip_end_time[$trips])) == "01.01.1970"){
                                        ?>
                                        <td style="text-align:left;color:red;">Trip Not Completed</td>
                                        <?php
                                    }
                                    else{
                                        //$trip_time = strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips]);
                                        //$time[$i] = date("H:i:s",($trip_time));
                                        
                                        // Calculating the difference between DateTime objects 
                                        /*$dateTimeObject1 = date_create($trip_start_time[$trips]);
                                        $dateTimeObject2 = date_create($trip_end_time[$trips]);
                                        $interval = date_diff($dateTimeObject1, $dateTimeObject2);
                                        $hours = $interval->h;
                                        $minutes = $interval->days * 24 * 60;
                                        $mns = $minutes += $interval->i;
                                        $secs =  $interval->s;
                                        $time[$i] = $trip_time = date("H:i:s",strtotime($hours.":".$mns.":".$secs));
                                        */
                                        $hours = (INT)((strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips])) / 60 / 60);
                                        $mns = (((INT)((strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips])) / 60)) - ($hours * 60));
                                        $secs = (((INT)((strtotime($trip_end_time[$trips]) - strtotime($trip_start_time[$trips])))) - ($hours * 60 * 60) - ($mns *60));

                                        if($hours < 10){ $hours = "0".$hours; }
                                        if($mns < 10){ $mns = "0".$mns; }
                                        if($secs < 10){ $secs = "0".$secs; }
                                        $time[$i] = $trip_time = $hours.":".$mns.":".$secs;

                                        $t_hrs += (float)$hours;
                                        $t_min += (float)$mns;
                                        $t_sec += (float)$secs;
                                        ?>
                                        <td style="text-align:left;"><?php echo $trip_time; ?></td>
                                        <?php
                                    }
                                ?>

                            <?php
                             if(!empty($display_entry_location_start[$trips])){ 
                                ?>
                                <td title='Start Entry Location'><a href="<?php echo $display_entry_location_start[$trips]; ?>" target='_BLANK'><?php echo "Start Location-".$slno; ?></a></td>
                            <?php }else{ ?>
                                <td title='Start Entry Location'></td>
                            <?php } ?>
                            <?php
                             if(!empty($display_entry_location_end[$trips])){ 
                                ?>
                                <td title='End Entry Location'><a href="<?php echo $display_entry_location_end[$trips]; ?>" target='_BLANK'><?php echo "End Location-".$slno; ?></a></td>
                            <?php }else{ ?>
                                <td title='End Entry Location'></td>
                            <?php } ?>
                                
                            </tr>
                        <?php
                            $total_start_reading = $total_start_reading + $trip_start_reading[$trips];
                            $total_end_reading = $total_end_reading + $trip_end_reading[$trips];
                            $total_final_reading = $total_final_reading + $trip_totalkm[$trips];
                        }
                    }
                }
            ?>
            </tbody>
            <thead class="thead3">
                <tr>
                    <th colspan="5" style="text-align:center;">Total</th>
                    <th style="text-align:right;"><?php //echo str_replace(".00","",number_format_ind(round($total_start_reading))); ?></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"><?php //echo str_replace(".00","",number_format_ind(round($total_end_reading,2))); ?></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($total_final_reading,2))); ?></th>

                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($tnoff_visit)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(0); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind($ftpkm_amt); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind($ftda_amt); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind($ftta_amt); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind($ftea_amt); ?></th>

                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"></th>
                    <?php
                        /*$sum = strtotime('00:00:00');
                        foreach($time as $element) {
                            // Converting the time into seconds
                            $timeinsec = strtotime($element) - $sum;
                            
                            // Sum the time with previous value
                            $totaltime = $totaltime + $timeinsec;
                        }

                        $h = intval($totaltime / 3600);
                        $totaltime = $totaltime - ($h * 3600);
                        $m = intval($totaltime / 60);
                        $s = $totaltime - ($m * 60);
                        */
                        $n_min = (int)($t_sec / 60); $a_sec = (int)(($t_sec) - ($n_min * 60));

                        $t_min = $t_min + $n_min;
                        $n_hrs = (int)($t_min / 60); $a_min = (int)(($t_min) - ($n_hrs * 60));

                        $a_hrs = $t_hrs + $n_hrs;
                        
                        if($a_hrs < 10){ $a_hrs = "0".$a_hrs; }
                        if($a_min < 10){ $a_min = "0".$a_min; }
                        if($a_sec < 10){ $a_sec = "0".$a_sec; }
                    ?>
                    <th style="text-align:left;"><?php echo ("$a_hrs:$a_min:$a_sec"); ?></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_row_height(){
                var table_elements = document.querySelector("table>tbody");
                var i; var max_height = 0;
                for(i = 1; i <= table_elements.rows.length; i++){
                    var row_selector = "table>tbody>tr:nth-child(" + [i] + ")";
                    var table_row = document.querySelector(row_selector);
                    var vertical_spacing = window.getComputedStyle(table_row).getPropertyValue("-webkit-border-vertical-spacing");
                    var margin_top = window.getComputedStyle(table_row).getPropertyValue("margin-top");
                    var margin_bottom = window.getComputedStyle(table_row).getPropertyValue("margin-bottom");
                    var row_height= parseInt(vertical_spacing, 10)+parseInt(margin_top, 10)+parseInt(margin_bottom, 10)+table_row.offsetHeight;
                    if(max_height <= row_height){
                        max_height = row_height;
                    }
                }
                //alert("The height is: "+max_height+"px");
                document.getElementById("thead2_empty_row").style.height = max_height+"px";
            }
            fetch_row_height();
        </script>
        <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

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
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($emp_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $emp_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
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
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($emp_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                //echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $emp_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $emp_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            //echo "}";
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($emp_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $l_code = $farm_line[$f_code];
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $emp_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $emp_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($emp_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $emp_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $emp_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($emp_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                //echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $emp_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $emp_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            //echo "}";
                            }
                        ?>
                    }
                }
                else{ }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

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
                console.log("test");
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
                    console.log("test1");

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
                console.log("test");
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
                    console.log("test1");

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
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search_table');
                const table = document.getElementById('main_table');
                const tableBody = table.querySelector('tbody');

                searchInput.addEventListener('input', () => {
                    const filter = searchInput.value.toLowerCase();
                    const rows = tableBody.querySelectorAll('tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){ 
                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                }
                else{ }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>