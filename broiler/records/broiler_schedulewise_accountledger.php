<?php
//broiler_schedulewise_accountledger.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Schedule Wise Account Ledger";
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

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
 
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_list." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_category = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `acc_schedules` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $schedule_code = $schedule_name = array();
while($row = mysqli_fetch_assoc($query)){ $schedule_code[$row['code']] = $row['code']; $schedule_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $schedules = "select"; $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $schedules = $_POST['schedules'];
    $sectors = $_POST['sectors'];

    if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `location` = '$sectors'"; }
    $excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_schedulewise_accountledger-Excel.php?fdate=".$fdate."&tdate=".$tdate."&schedules=".$schedules."&sectors=".$sectors;
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
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead2_empty_row { display:none; }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
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
                    <th colspan="4" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Schedule Wise Account Ledger</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_schedulewise_accountledger.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="6">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Schedules</label>
                                    <select name="schedules" id="schedules" class="form-control select2" style="width:250px;">
                                        <option value="select" <?php if($schedules == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($schedule_code as $scode){ if($schedule_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($schedules == $scode){ echo "selected"; } ?>><?php echo $schedule_name[$scode]; ?></option>
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
                                <div class="m-2 form-group"><br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Sl.No.</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Cr</th>
                    <th>Dr</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_POST['submit_report']) == true){
                if($schedules == "select" || $schedules == "all"){ $sch_filter = ""; } else{ $sch_filter = " AND `schedules` LIKE '$schedules'"; }
                if($sectors == "select" || $sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `location` LIKE '$sectors'"; }

                $sql = "SELECT * FROM `acc_coa` WHERE `dflag` = '0'".$sch_filter." ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $coa_code = $coa_name = array();
                while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

                $coa_list = implode("','",$coa_code);
                $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$tdate' AND `coa_code` IN ('$coa_list')".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql); $coa_cr_amt = $coa_dr_amt = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['coa_code'];
                    if($row['crdr'] == "CR"){ $coa_cr_amt[$key] += (float)$row['amount']; }
                    else if($row['crdr'] == "DR"){ $coa_dr_amt[$key] += (float)$row['amount']; }
                }
                $html = ''; $c = $tcr_amt = $tdr_amt = 0;
                foreach($coa_code as $key){
                    if(empty($coa_cr_amt[$key]) || $coa_cr_amt[$key] == ""){ $coa_cr_amt[$key] = 0; }
                    if(empty($coa_dr_amt[$key]) || $coa_dr_amt[$key] == ""){ $coa_dr_amt[$key] = 0; }

                    $c++;
                    $url = "broiler_account_ledger.php?fdate=".$fdate."&tdate=".$tdate."&coas=".$key."&sectors=".$sectors;
                    $html .= '<tr>';
                    $html .= '<td style="width:50px;text-align:center;">'.$c.'</td>';
                    $html .= '<td style="width:80px;"><a href="'.$url.'" target="_BLANK">'.$key.'</a></td>';
                    $html .= '<td><a href="'.$url.'" target="_BLANK">'.$coa_name[$key].'</a></td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($coa_cr_amt[$key],2)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($coa_dr_amt[$key],2)).'</td>';
                    $html .= '</tr>';

                    $tcr_amt += (float)$coa_cr_amt[$key];
                    $tdr_amt += (float)$coa_dr_amt[$key];
                }
                echo $html;
                ?>
            </tbody>
            
            <tr class="thead4">
                <th colspan="3" style="text-align:center;">Total</th>
                <th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tcr_amt),2)); ?></th>
                <th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tdr_amt),2)); ?></th>
            </tr>
        <?php
            }
        ?>
        </table>
        <script>
            function checkval(){
                var fdate = document.getElementById("fdate").value;
                var tdate = document.getElementById("tdate").value;
                var schedules = document.getElementById("schedules").value;
                var sectors = document.getElementById("sectors").value;
                if(fdate == ""){
                    alert("Please select/enter Appropriate From Date");
                    document.getElementById("fdate").focus();
                    return false;
                }
                else if(tdate == ""){
                    alert("Please select/enter Appropriate To Date");
                    document.getElementById("tdate").focus();
                    return false;
                }
                else if(schedules == "select"){
                    alert("Please select Schedules");
                    document.getElementById("schedules").focus();
                    return false;
                }
                else if(sectors == "select"){
                    alert("Please select Farm/Sector");
                    document.getElementById("sectors").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>