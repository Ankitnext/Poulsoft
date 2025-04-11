<?php
//broiler_hr_attendance.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

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


function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; } if($b == ""){ $b = 0; } $a = round($a,$b); $c = explode(".",$a);
    $ed = ""; $iv = 0; if($c[1] == ""){ $iv = 0; } else{ $iv = strlen($c[1]); }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    if(str_contains($a, '.')){ return $a."".$ed; } else if($b > 0){ return $a.".".$ed; } else{ return $a; }
}

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $emp_code = $emp_ccode = $emp_name = $emp_desig = array();
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_ccode[$row['code']] = $row['emp_id']; $emp_name[$row['code']] = $row['name']; $emp_desig[$row['code']] = $row['desig_code']; $emp_bth[$row['code']] = $row['birth_date']; $emp_jnd[$row['code']] = $row['join_date']; $emp_desig[$row['code']] = $row['desig_code']; }

$sql = "SELECT * FROM `broiler_designation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $desig_code = $desig_name = array();
while($row = mysqli_fetch_assoc($query)){ $desig_code[$row['code']] = $row['code']; $desig_name[$row['code']] = $row['description']; }

$months = date("m"); $years = date("Y"); $employees = $designations = $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $months = $_POST['months'];
    $years = $_POST['years'];
    $employees = $_POST['employees'];
    $designations = $_POST['designations'];
    $sectors = $_POST['sectors'];

    $excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_hr_attendance-Excel.php?months=".$months."&years=".$years."&employees=".$employees."&sectors=".$sectors;
}
else{
    $url = "";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
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
    <body align="center">
        <table class="tbl" style="width:auto;" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Employee Attendance Report</h5></th>
                    <th colspan="17" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_hr_attendance.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="26">
                            <div class="row">
                                <div class="m-2 form-group" style="width:190px;">
                                    <label for="months">Month</label>
                                    <select name="months" id="months" class="form-control select2" style="width:180px;">
                                        <?php for($mts = 1; $mts <= 12; $mts++){ $mname = date("F", mktime(0, 0, 0, $mts, 1)); ?>
                                            <option value='<?php echo $mts; ?>' <?php if($months == $mts){ echo "selected"; } ?>><?php echo $mname; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:190px;">
                                    <label for="tdate">Year</label>
                                    <select name="years" id="years" class="form-control select2" style="width:180px;">
                                        <?php $syear = 2020; $eyear = date("Y"); for ($i = $syear; $i <= $eyear; $i++){ ?>
                                            <option value='<?php echo $i; ?>' <?php if($years == $i){ echo "selected"; } ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Employee</label>
                                    <select name="employees" id="employees" class="form-control select2">
                                        <option value="all" <?php if($employees == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($emp_code as $ecode){ if($emp_name[$ecode] != ""){ ?>
                                        <option value="<?php echo $ecode; ?>" <?php if($employees == $ecode){ echo "selected"; } ?>><?php echo $emp_name[$ecode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Designation</label>
                                    <select name="designations" id="designations" class="form-control select2">
                                        <option value="all" <?php if($designations == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($desig_code as $dcode){ if($desig_name[$dcode] != ""){ ?>
                                        <option value="<?php echo $dcode; ?>" <?php if($designations == $dcode){ echo "selected"; } ?>><?php echo $desig_name[$dcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Sector</label>
                                    <select name="sectors" id="sectors" class="form-control select2" style="width:250px;">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($sectors == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option>
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
            <?php
            if(isset($_POST['submit_report']) == true){
                $fdate = date("Y-m-d",strtotime("{$years}-{$months}-01")); $tdate = date("Y-m-t",strtotime($fdate));
                if($employees != "all"){ $emp_filter = " AND `emp_code` = '$employees'"; } else{ $emp_filter = ""; }
                if($designations != "all"){ $desig_filter = " AND `desig_code` = '$designations'"; } else{ $desig_filter = ""; }
                if($sectors != "all"){ $sector_filter = " AND `warehouse` = '$sectors'"; } else{ $sector_filter = ""; }
                
                $sql = "SELECT * FROM `employee_attendance` WHERE `date` >='$fdate' AND `date` <= '$tdate'".$emp_filter."".$desig_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $emp_status = $emp_alist = array(); $start_date = $end_date = "";
                if($count > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['date']."@".$row['emp_code'];
                        $emp_status[$key] = $row['status'];
                        $emp_alist[$row['emp_code']] = $row['emp_code'];
                        $emp_sec[$row['emp_code']] = $row['warehouse'];

                        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
                        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
                    }

                    $html_head = $html_body = $html_foot = '';
                    $html_head .= '<thead class="thead3" align="center">';
                    $html_head .= '<tr>';
                    $html_head .= '<th>Sl No.</th>';
                    $html_head .= '<th>Code</th>';
                    $html_head .= '<th>Sector</th>';
                    $html_head .= '<th>Name</th>';
                    $html_head .= '<th>Designation</th>';
                    $html_head .= '<th>Birth Date</th>';
                    $html_head .= '<th>Join Date</th>';
                    $html_head .= '<th>Service Year</th>';
                    $sdate = strtotime($start_date); $edate = strtotime($end_date); $tcols = 0;
                    for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                        $adate = date("Y-m-d",$cdate); $day = date("d",strtotime($adate)); $tcols++;
                        if(strlen($day) == 1){ $html_head .= '<th>0'.$day.'</th>'; } else{ $html_head .= '<th>'.$day.'</th>'; }
                    }
                    $html_head .= '<th>Full Days</th>';
                    $html_head .= '<th>Half Days</th>';
                    $html_head .= '<th>Leaves</th>';
                    $html_head .= '<th>Absents</th>';
                    $html_head .= '<th>N/A</th>';
                    $html_head .= '</tr>';
                    $html_head .= '</thead>';

                    $sdate = strtotime($start_date); $edate = strtotime($end_date);
                    $tfdays = $thdays = $tldays = $tadays = $tbdays = $sl_no = 1; 
                    $full_days = $half_days = $leave_days = $absent_days = $blank_days = array();
                    foreach($emp_code as $ecode){
                        if(!empty($emp_alist[$ecode])){
                            $fdays = $hdays = $ldays = $adays = $bdays = 0;
                            $html_body .= '<tr>';
                            $html_body .= '<td>'.$sl_no++.'</td>';
                            $html_body .= '<td>'.$emp_ccode[$ecode].'</td>';
                            $html_body .= '<td>'.$sector_name[$emp_sec[$ecode]].'</td>';
                            $html_body .= '<td>'.$emp_name[$ecode].'</td>';
                            $html_body .= '<td>'.$desig_name[$emp_desig[$ecode]].'</td>';
                            $html_body .= '<td>'.date("d.m.Y",strtotime($emp_bth[$ecode])).'</td>';
                            $html_body .= '<td>'.date("d.m.Y",strtotime($emp_jnd[$ecode])).'</td>';

                            $old_date = date("d.m.Y",strtotime($emp_jnd[$ecode]));
                            
                            // Explode the old date to extract day, month, and year
                            list($old_day, $old_month, $old_year) = explode(".", $old_date);

                            // Get the current date
                            $current_date = date("d.m.Y");
                            list($current_day, $current_month, $current_year) = explode(".", $current_date);

                            // Calculate differences
                            $years_diff = $current_year - $old_year;
                            $months_diff = $current_month - $old_month;
                            $days_diff = $current_day - $old_day;

                            // Adjust for negative values
                            if ($days_diff < 0) {
                                $months_diff -= 1;
                                $days_diff += 30; // Approximate number of days in a month
                            }
                            if ($months_diff < 0) {
                                $years_diff -= 1;
                                $months_diff += 12;
                            }
                            // Format the result as "X years Y months"
                            $duration = "{$years_diff} years {$months_diff} months";
                            
                            $html_body .= '<td>'.$duration.'</td>';
                            for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                                $key = date("Y-m-d",$cdate)."@".$ecode; $key2 = date("d",$cdate);
                                if(!empty($emp_status[$key])){
                                    if($emp_status[$key] == "F"){
                                        $html_body .= '<td style="color:green;font-weight:bold;text-align:center;">'.$emp_status[$key].'</td>';
                                        $fdays++;
                                        $tfdays++;
                                        $full_days[$key2] += 1;
                                    }
                                    else if($emp_status[$key] == "H"){
                                        $html_body .= '<td style="color:orange;font-weight:bold;text-align:center;">'.$emp_status[$key].'</td>';
                                        $hdays++;
                                        $thdays++;
                                        $half_days[$key2] += 1;
                                    }
                                    else if($emp_status[$key] == "L"){
                                        $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">'.$emp_status[$key].'</td>';
                                        $ldays++;
                                        $tldays++;
                                        $leave_days[$key2] += 1;
                                    }
                                    else if($emp_status[$key] == "A"){
                                        $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">'.$emp_status[$key].'</td>';
                                        $adays++;
                                        $tadays++;
                                        $absent_days[$key2] += 1;
                                    }
                                    else{
                                        $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">N/A</td>';
                                        $bdays++;
                                        $tbdays++;
                                        $blank_days[$key2] += 1;
                                    }
                                }
                                else{ 
                                    $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">N/A</td>';
                                    $bdays++;
                                    $tbdays++;
                                    $blank_days[$key2] += 1;
                                }
                            }
                            if($fdays == 0){ $fdays = ""; }
                            if($hdays == 0){ $hdays = ""; }
                            if($ldays == 0){ $ldays = ""; }
                            if($adays == 0){ $adays = ""; }
                            if($bdays == 0){ $bdays = ""; }

                            $html_body .= '<td style="color:green;font-weight:bold;text-align:center;">'.$fdays.'</td>';
                            $html_body .= '<td style="color:orange;font-weight:bold;text-align:center;">'.$hdays.'</td>';
                            $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">'.$ldays.'</td>';
                            $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">'.$adays.'</td>';
                            $html_body .= '<td style="color:red;font-weight:bold;text-align:center;">'.$bdays.'</td>';
                            $html_body .= '</tr>';
                        }
                    }

                    $tcols += 3;
                    $html_foot .= '<tfoot class="thead3" align="center">';
                    $html_foot .= '<tr>';
                    $html_foot .= '<th colspan="'.$tcols.'" style="text-align:center;">Total</th>';
                    $html_foot .= '<th style="color:green;font-weight:bold;text-align:center;">'.$tfdays.'</th>';
                    $html_foot .= '<th style="color:orange;font-weight:bold;text-align:center;">'.$thdays.'</th>';
                    $html_foot .= '<th style="color:red;font-weight:bold;text-align:center;">'.$tldays.'</th>';
                    $html_foot .= '<th style="color:red;font-weight:bold;text-align:center;">'.$tadays.'</th>';
                    $html_foot .= '<th style="color:red;font-weight:bold;text-align:center;">'.$tbdays.'</th>';
                    $html_foot .= '</tr>';
                    $html_foot .= '</tfoot>';

                    $html = '';
                    $html .= $html_head."".$html_body."".$html_foot;

                    echo $html;
                }
            }
        ?>
        </table>
        <?php
        if(isset($_POST['submit_report']) == true){
            if($count > 0){
                $html = '';
                $html .= '<br/><br/><br/>';
                $html .= '<table class="tbl" style="width:auto;" align="center">';

                $html .= '<thead class="thead3" align="center">';
                $html .= '<tr>';
                $html .= '<th></th>';
                $sdate = strtotime($start_date); $edate = strtotime($end_date);
                for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                    $adate = date("Y-m-d",$cdate); $day = date("d",strtotime($adate));
                    if(strlen($day) == 1){ $html .= '<th>0'.$day.'</th>'; } else{ $html .= '<th>'.$day.'</th>'; }
                }
                $html .= '</tr>';
                $html .= '</thead>';
                
                $html .= '<tbody>';
                $html .= '<tr>';
                $html .= '<th>Full Days</th>';
                $sdate = strtotime($start_date); $edate = strtotime($end_date);
                for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                    $adate = date("Y-m-d",$cdate); $key2 = date("d",strtotime($adate));
                    if(!empty($full_days[$key2])){ $html .= '<th>'.$full_days[$key2].'</th>'; } else{ $html .= '<th></th>'; }
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Half Days</th>';
                $sdate = strtotime($start_date); $edate = strtotime($end_date);
                for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                    $adate = date("Y-m-d",$cdate); $key2 = date("d",strtotime($adate));
                    if(!empty($half_days[$key2])){ $html .= '<th>'.$half_days[$key2].'</th>'; } else{ $html .= '<th></th>'; }
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Leaves</th>';
                $sdate = strtotime($start_date); $edate = strtotime($end_date);
                for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                    $adate = date("Y-m-d",$cdate); $key2 = date("d",strtotime($adate));
                    if(!empty($leave_days[$key2])){ $html .= '<th>'.$leave_days[$key2].'</th>'; } else{ $html .= '<th></th>'; }
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Absents</th>';
                $sdate = strtotime($start_date); $edate = strtotime($end_date);
                for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                    $adate = date("Y-m-d",$cdate); $key2 = date("d",strtotime($adate));
                    if(!empty($absent_days[$key2])){ $html .= '<th>'.$absent_days[$key2].'</th>'; } else{ $html .= '<th></th>'; }
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>N/A</th>';
                $sdate = strtotime($start_date); $edate = strtotime($end_date);
                for ($cdate = $sdate; $cdate <= $edate; $cdate += (86400)){
                    $adate = date("Y-m-d",$cdate); $key2 = date("d",strtotime($adate));
                    if(!empty($blank_days[$key2])){ $html .= '<th>'.$blank_days[$key2].'</th>'; } else{ $html .= '<th></th>'; }
                }
                $html .= '</tr>';
                $html .= '</tbody>';
                
                $html .= '</table>';

                echo $html;
            }
        }
        ?>
        <script>
            function checkval(){
                var items = document.getElementById("items").value;
                var sectors = document.getElementById("sectors").value;
                if(items.match("select")){
                    alert("Please select Item");
                    document.getElementById("items").focus();
                    return true;
                }
                else if(sectors.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("sectors").focus();
                    return true;
                }
                else{
                    return true;
                }
            }
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icodes){
                        $icats = $item_category[$icodes];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icodes){
                            $icats = $item_category[$icodes];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>