<?php
//poulsoft_ledger_report1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Ledger Report ";
    include "header_head.php";
    $form_path = "poulsoft_ledger_report1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Ledger Report";
    include "header_head.php";
    $form_path = "poulsoft_ledger_report1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Ledger Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("account_summary", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.account_summary LIKE poulso6_admin_broiler_broilermaster.account_summary;"; mysqli_query($conn,$sql1); }

$coa_code = $acc_name = $sector_name = $item_name = array();
$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_coa` WHERE `visible_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_grp = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['name']; $cus_grp[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $sup_code = $sup_grp = array();
while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['name']; $sup_grp[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $frm_code = $farm_frmr = array();
while($row = mysqli_fetch_assoc($query)){ $frm_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; $sector_name[$row['code']] = $row['description']; $farm_frmr[$row['code']] = $row['farmer_code']; }
asort($acc_name);
asort($sector_name);

$fdate = $tdate = date("Y-m-d"); $vendors = "select"; $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $vendors = $_POST['vendors'];
    $sectors = $_POST['sectors'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="4" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="20" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="24">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Account</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="select" <?php if($vendors == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($acc_name as $scode => $sname){ if($sname != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($vendors == $scode){ echo "selected"; } ?>><?php echo $sname." (".$scode.")"; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Sector</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_name as $scode => $sname){ if($sname != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sname; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel('main_table','<?php echo $file_name; ?>');">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
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
            
            $html = $nhtml = $fhtml = $rhtml = ''; $tot_iqty = $cr_amt = $dr_amt = $rb_amt = $slno = 0;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Date</th>';
            $nhtml .= '<th>Transaction No.</th>'; $fhtml .= '<th id="order">Transaction No.</th>';
            $nhtml .= '<th>Transaction Type</th>'; $fhtml .= '<th id="order">Transaction Type</th>';
            $nhtml .= '<th>Doc. No.</th>'; $fhtml .= '<th id="order">Doc. No.</th>';
            $nhtml .= '<th>From Warehouse</th>'; $fhtml .= '<th id="order">From Warehouse</th>';
            $nhtml .= '<th>Vehicle number</th>'; $fhtml .= '<th id="order">Vehicle number</th>';
            $nhtml .= '<th>Item</th>'; $fhtml .= '<th id="order">Item</th>';
            $nhtml .= '<th>Quantity</th>'; $fhtml .= '<th id="order_num">Quantity</th>';
            $nhtml .= '<th>Paid/Received</th>'; $fhtml .= '<th id="order">Paid/Received</th>';
            $nhtml .= '<th>Cheque No.</th>'; $fhtml .= '<th id="order">Cheque No.</th>';
            $nhtml .= '<th>Cheque Date</th>'; $fhtml .= '<th id="order_date">Cheque Date</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>';
            $nhtml .= '<th>Debit</th>'; $fhtml .= '<th id="order_num">Debit</th>';
            $nhtml .= '<th>Credit</th>'; $fhtml .= '<th id="order_num">Credit</th>';
            $nhtml .= '<th>Running Balance</th>'; $fhtml .= '<th id="order_num">Running Balance</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $coa_fltr = "";
                if(!empty($coa_code[$vendors]) && $coa_code[$vendors] != ""){
                    $coa_fltr = " AND `coa_code` IN ('$vendors')";
                }
                else if(!empty($cus_code[$vendors]) && $cus_code[$vendors] != ""){
                    $gcode = $cus_grp[$vendors]; $coa_list = "";
                    $sql = "SELECT * FROM `main_groups` WHERE `code` = '$gcode' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $coa_list = $row['cus_controller_code']; }

                    $coa_fltr = " AND `coa_code` IN ('$coa_list') AND `vendor` IN ('$vendors')";
                }
                else if(!empty($sup_code[$vendors]) && $sup_code[$vendors] != ""){
                    $gcode = $sup_grp[$vendors]; $coa_list = "";
                    $sql = "SELECT * FROM `main_groups` WHERE `code` = '$gcode' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $coa_list = $row['sup_controller_code']; }

                    $coa_fltr = " AND `coa_code` IN ('$coa_list') AND `vendor` IN ('$vendors')";
                }
                else if(!empty($frm_code[$vendors]) && $frm_code[$vendors] != ""){
                    $fcode = $farm_frmr[$vendors]; $gcode = $coa_list = "";
                    $sql = "SELECT * FROM `broiler_farmer` WHERE `code` LIKE '$fcode' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $gcode = $row['farmer_group']; }

                    $sql = "SELECT * FROM `broiler_farmergroup` WHERE `code` = '$gcode' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $coa_list = $row['pay_acc_code']; }

                    $coa_fltr = " AND `coa_code` IN ('$coa_list') AND `vendor` IN ('$fcode')";
                }
                else{ }
                $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `location` IN ('$sectors')"; }

                $sql = "SELECT * FROM `account_summary` WHERE `crdr` IN ('CR','DR') AND `date` <= '$tdate'".$coa_fltr."".$sec_fltr." AND `etype` NOT IN ('DayEntryMortality','DayEntryFeed','MedVacEntry','DayEntryFeed2') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_array($query)){
                    if(strtotime($row['date']) < strtotime($fdate)){
                        if($row['crdr'] == "DR"){ $rb_amt += (float)$row['amount']; }
                        else if($row['crdr'] == "CR"){ $rb_amt -= (float)$row['amount']; }
                        else{ }
                    }
                    else{
                        $slno++;

                        //Opening Balance
                        if($slno == 1){
                            $html .= '<tr>';
                            $html .= '<th colspan="13" style="text-align:left;">Opening Balance</th>';
                            if((float)$rb_amt >= 0){
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($rb_amt,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round(0,5)).'</td>';
                            }
                            else{
                                $html .= '<td style="text-align:right;">'.number_format_ind(round(0,5)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace("-","",number_format_ind(round($rb_amt,5))).'</td>';
                            }
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($rb_amt,5)).'</td>';
                            $html .= '</tr>';
                        }
                        //Between days Balance
                        $date = date("d.m.Y",strtotime($row['date']));
                        $trnum = $row['trnum'];
                        $ttype = $row['etype'];
                        $dc_no = $row['dc_no'];
                        $f_sec = $sector_name[$row['location']];
                        $vehicle = $row['vehicle_code'];
                        $iname = $item_name[$row['item_code']];
                        $i_qty = number_format_ind(round($row['quantity'],5));
                        $por_name = ""; if(!empty($acc_name[$row['vendor']]) && $acc_name[$row['vendor']] != ""){ $por_name = $acc_name[$row['vendor']]; }
                        $chq_no = $row['cheque_no'];
                        $chq_date = $chq_cls = ""; if($row['cheque_date'] != ""){ $chq_cls = 'class="dates"'; $chq_date = date("d.m.Y",strtotime($row['cheque_date'])); }
                        $remarks = $row['remarks'];

                        $html .= '<tr>';
                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                        $html .= '<td style="text-align:left;">'.$date.'</td>'; // class="dates"
                        $html .= '<td style="text-align:left;">'.$trnum.'</td>';
                        $html .= '<td style="text-align:left;">'.$ttype.'</td>';
                        $html .= '<td style="text-align:left;">'.$dc_no.'</td>';
                        $html .= '<td style="text-align:left;">'.$f_sec.'</td>';
                        $html .= '<td style="text-align:left;">'.$vehicle.'</td>';
                        $html .= '<td style="text-align:left;">'.$iname.'</td>';
                        $html .= '<td style="text-align:right;">'.$i_qty.'</td>';
                        $html .= '<td style="text-align:left;">'.$por_name.'</td>';
                        $html .= '<td style="text-align:left;">'.$chq_no.'</td>';
                        $html .= '<td style="text-align:left;" '.$chq_cls.'>'.$chq_date.'</td>';
                        $html .= '<td style="text-align:left;">'.$remarks.'</td>';

                        if($row['crdr'] == "DR"){
                            $dr_amt += (float)$row['amount'];
                            $rb_amt += (float)$row['amount'];
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($row['amount'],5)).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(0,5)).'</td>';
                        }
                        else if($row['crdr'] == "CR"){
                            $cr_amt += (float)$row['amount'];
                            $rb_amt -= (float)$row['amount'];
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(0,5)).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($row['amount'],5)).'</td>';
                        }
                        else{
                            $html .= '<td style="text-align:right;"></td>';
                            $html .= '<td style="text-align:right;"></td>';
                        }
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($rb_amt,5)).'</td>';

                        //Total Calculations
                        $tot_iqty += (float)$row['quantity'];
                    }
                }
            }
            $html .= '</tbody>';
            $html .= '<tr class="thead2">';
            $html .= '<th colspan="8">Total</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_iqty,5))).'</th>';
            $html .= '<th style="text-align:left;"></th>';
            $html .= '<th style="text-align:left;"></th>';
            $html .= '<th style="text-align:left;"></th>';
            $html .= '<th style="text-align:left;"></th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($dr_amt,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($cr_amt,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($rb_amt,5))).'</th>';
            $html .= '</tr>';

            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function checkval(){
                var fdate = document.getElementById("fdate").value;
                var tdate = document.getElementById("tdate").value;
                var vendors = document.getElementById("vendors").value;
                var sectors = document.getElementById("sectors").value;
                var l = true;
                if(fdate == ""){
                    alert("Please enter/select appropriate from date");
                    document.getElementById("fdate").focus();
                    l = false;
                }
                else if(tdate == ""){
                    alert("Please enter/select appropriate to date");
                    document.getElementById("tdate").focus();
                    l = false;
                }
                else if(vendors == "" || vendors == "select"){
                    alert("Please select appropriate account");
                    document.getElementById("vendors").focus();
                    l = false;
                }
                else if(sectors == "" || sectors == "select"){
                    alert("Please select appropriate sector");
                    document.getElementById("sectors").focus();
                    l = false;
                }
                else{ }

                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <!--<script type="text/javascript" src="table_sorting_wauto_slno.js"></script>-->
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_download_excel.js"></script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $fhtml; ?>';
                document.getElementById("head_names").innerHTML = html;
                    
                $('#export').select2();
                document.getElementById("export").value = "display";
                $('#export').select2();
                //table_sort();
                //table_sort2();
                //table_sort3();
            }
        </script>
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>