<?php
//broiler_customerbalanceall_report.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
}
$user_code = $_SESSION['userid'];
include "../broiler_check_tableavailability.php";

    /*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_cus_lines", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.breeder_cus_lines LIKE poulso6_admin_broiler_broilermaster.breeder_cus_lines;"; mysqli_query($conn,$sql1); }


$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cline_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cline_code` VARCHAR(100) NULL DEFAULT NULL AFTER `aadhar_no`"; mysqli_query($conn,$sql); }
   
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){  $group_access_code = $row['cgroup_access']; }
if($group_access_code == "all" || $group_access_code == ""){ $group_access_filter1 = ""; } else{ $group_access_list = implode("','", explode(",",$group_access_code)); $group_access_filter1 = " AND `code` IN ('$group_access_list')"; $group_access_filter2 = " AND `groupcode` IN ('$group_access_list')"; }

$vengrp_code = $vengrp_name = $vendor_code = $vendor_name = $obdate = $obtype = $obamt = array();
$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `dflag` = '0'".$group_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vengrp_code[$row['code']] = $row['code']; $vengrp_name[$row['code']] = $row['description']; $vengrp_ccode[$row['code']] = $row['controlaccount']; $vengrp_controlaccount[$row['controlaccount']] = $row['code']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` ='1'".$group_access_filter2." AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $vendor_grps[$row['code']] = $row['groupcode']; $obdate[$row['code']] = $row['obdate']; $obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt']; }

$sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cline_code[$row['code']] = $row['code']; $cline_name[$row['code']] = $row['description']; }

$tdate = date("Y-m-d"); $vendor_groups = $vendors = $clines = "all"; $excel_type = "display";
if(isset($_REQUEST['submit_report']) == true){
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
	$vendor_groups = $_REQUEST['vendor_groups'];
	$vendors = $_REQUEST['vendors'];
	$clines = $_REQUEST['clines'];

    if($_GET['vendor_groups'] != ''){
        $vendor_groups = $vengrp_controlaccount[$vendor_groups];
    }

	$excel_type = $_REQUEST['export'];
}

$ven_fltr = $cgrp_fltr = $cline_fltr = $customer_filter = "";
if($vendors != "all"){ $ven_fltr = " AND `code` IN ('$vendors')"; }
if($vendor_groups != "all"){ $cgrp_fltr = " AND `groupcode` IN ('$vendor_groups')"; }
if($clines != "all"){ $cline_fltr = " AND `cline_code` IN ('$clines')"; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `active` ='1'".$ven_fltr."".$cgrp_fltr."".$cline_fltr." AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_alist = array();
while($row = mysqli_fetch_assoc($query)){ $cus_alist[$row['code']] = $row['code']; }
$cus_list = implode("','",$cus_alist);
$customer_filter = " AND `vendor` IN ('$cus_list')";

$coa_filter = $cus_glist = "";
if($vendors != "all"){
    $cus_glist = $vengrp_ccode[$vendor_grps[$vendors]];
}
else if($vendor_groups != "all"){
    foreach($vendor_code as $vcode){
        if($vendor_grps[$vcode] == $vendor_groups){
            if($cus_glist == ""){ $cus_glist = $vengrp_ccode[$vendor_grps[$vcode]]; } else{ $cus_glist = $cus_glist."','".$vengrp_ccode[$vendor_grps[$vcode]]; }
        }
    }
}
else{
    foreach($vendor_code as $vcode){
        if($cus_glist == ""){ $cus_glist = $vengrp_ccode[$vendor_grps[$vcode]]; } else{ $cus_glist = $cus_glist."','".$vengrp_ccode[$vendor_grps[$vcode]]; }
    }
}
$coa_filter = " AND `coa_code` IN ('$cus_glist')";
$url = "../PHPExcel/Examples/BroilerCustomerBalanceallReport-Excel.php?tdate=".$tdate."&vendor_groups=".$vendor_groups."&vendors=".$vendors;
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
            echo '<style>body { left:0;width:auto;overflow:auto;text-align:left; } table { white-space: nowrap; }
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:auto;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="11" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Customer Balance Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_customerbalanceall_report.php" method="post"  onsubmit="return checkval()">
                 <?php } else { ?>
                <form action="broiler_customerbalanceall_report.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr style="padding:10px;">
                        <th colspan="13">
                            <div class="row">&ensp;&ensp;
                                <div class="form-group" style="width:120px;">
                                    <label>Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" onchange="fetch_nisan_sales_dcnos();" />
                                </div>
                                <div class="form-group" style="width:220px;">
                                    <label>Customer Group</label>
                                    <select name="vendor_groups" id="vendor_groups" class="form-control select2" style="width:210px;" onchange="broiler_set_vendors();">
                                        <option value="all" <?php if($vendor_groups == "all"){ echo "selected"; } ?> >-All-</option>
                                        <?php
                                        foreach($vengrp_code as $vgcode){
                                        ?>
                                        <option value="<?php echo $vgcode; ?>" <?php if($vendor_groups == $vgcode){ echo "selected"; } ?> ><?php echo $vengrp_name[$vgcode]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                               
                                <div class="form-group" style="width:220px;">
                                    <label>Customer Name</label>
                                    <select name="vendors" id="vendors" class="form-control select2" style="width:210px;">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?> >-All-</option>
                                        <?php
                                        if($vendor_groups == "all"){
                                            foreach($vendor_code as $vcode){
                                            ?>
                                            <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?> ><?php echo $vendor_name[$vcode]; ?></option>
                                            <?php
                                            }
                                        }
                                        else{
                                            foreach($vendor_code as $vcode){
                                                if($vendor_grps[$vcode] == $vendor_groups){
                                                ?>
                                                <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?> ><?php echo $vendor_name[$vcode]; ?></option>
                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                 <div class="form-group" style="width:220px;">
                                        <label>Customer Line</label>
                                        <select name="clines" id="clines" class="form-control select2" style="width: 100%;">
                                        <option value="all">all</option>
                                        <?php
                                            foreach($cline_code as $code ){
                                                echo "<option value=".$code.">".$cline_name[$code]."</option>";
                                            }
                                         ?>
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                &ensp;
                                <div class="form-group" style="width:100px;">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php if(isset($_REQUEST['submit_report']) == true){ ?>
                <thead>
                    <tr class="thead3" align="center">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="2">Last Lifting</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr class="thead3" align="center">
                        <th id="order_num">Sl.No.</th>
                        <th id="order">Customer Name</th>
                        <th id="order_num">Total Outstanding</th>
                        <th id="order_num" title="<?php echo 'Till Date: '.date('Y-m-d',strtotime($tdate.'-10 days')); ?>">More Than 10 Days Balance</th>
                        <th id="order_num" title="<?php echo 'Till Date: '.date('Y-m-d',strtotime($tdate.'-15 days')); ?>">More Than 15 Days Balance</th>
                        <th id="order_date">Date</th>
                        <th id="order_num">Days</th>
                        <th id="order_num">Yesterday Received Amount</th>
                        <th id="order_num">Remaining Balance</th>
                    </tr>
                </thead>
                <tbody class="tbody1">
                <?php
                    //Active Date Outstanding Balance
                    $datea = date("Y-m-d",strtotime($tdate));
                    $datea_arr_codes = $datea_cr_amt = $datea_dr_amt = $datea_out_bals = array();
                    $sql = "SELECT coa_code,crdr,vendor,SUM(amount) as amount FROM `account_summary` WHERE `date` <= '$datea' AND `active` = '1'".$coa_filter."".$customer_filter." AND `dflag` = '0' GROUP BY crdr,coa_code,vendor ORDER BY `vendor` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['coa_code']."@".$row['vendor']; $datea_arr_codes[$key] = $key;
                        if($row['crdr'] == "CR"){ $datea_cr_amt[$key] = $row['amount']; }
                        else if($row['crdr'] == "DR"){ $datea_dr_amt[$key] = $row['amount']; }
                        else{ }
                    }
                    foreach($datea_arr_codes as $key){
                        if(empty($datea_cr_amt[$key])){ $datea_cr_amt[$key] = 0; }
                        if(empty($datea_dr_amt[$key])){ $datea_dr_amt[$key] = 0; }

                        $key1 = explode("@",$key);
                        if($obtype[$key1[1]] == "Cr"){ $cr_amt = $obamt[$row['code']]; $dr_amt = 0; }
                        else if($obtype[$key1[1]] == "Dr"){ $dr_amt = $obamt[$row['code']]; $cr_amt = 0; }

                        $datea_out_bals[$key] += (((float)$datea_dr_amt[$key] + (float)$dr_amt) - ((float)$datea_cr_amt[$key] + (float)$cr_amt));
                    }

                    //10 Days Before Active Date Outstanding Balance
                    $date10 = date("Y-m-d",strtotime($tdate."-10 days"));
                    $date10_arr_codes = $date10_cr_amt = $date10_dr_amt = $date10_out_bals = array();
                    $sql = "SELECT coa_code,crdr,vendor,SUM(amount) as amount FROM `account_summary` WHERE `date` <= '$date10' AND `active` = '1'".$coa_filter."".$customer_filter." AND `dflag` = '0' GROUP BY crdr,coa_code,vendor ORDER BY `vendor` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['coa_code']."@".$row['vendor']; $date10_arr_codes[$key] = $key;
                        if($row['crdr'] == "CR"){ $date10_cr_amt[$key] = $row['amount']; }
                        else if($row['crdr'] == "DR"){ $date10_dr_amt[$key] = $row['amount']; }
                        else{ }
                    }
                    foreach($date10_arr_codes as $key){
                        if(empty($date10_cr_amt[$key])){ $date10_cr_amt[$key] = 0; }
                        if(empty($date10_dr_amt[$key])){ $date10_dr_amt[$key] = 0; }

                        $key1 = explode("@",$key);
                        if($obtype[$key1[1]] == "Cr"){ $cr_amt = $obamt[$row['code']]; $dr_amt = 0; }
                        else if($obtype[$key1[1]] == "Dr"){ $dr_amt = $obamt[$row['code']]; $cr_amt = 0; }
                        
                        $date10_out_bals[$key] += (((float)$date10_dr_amt[$key] + (float)$dr_amt) - ((float)$date10_cr_amt[$key] + (float)$cr_amt));
                    }

                    //15 Days Before Active Date Outstanding Balance
                    $date15 = date("Y-m-d",strtotime($tdate."-15 days"));
                    $date15_arr_codes = $date15_cr_amt = $date15_dr_amt = $date15_out_bals = array();
                    $sql = "SELECT coa_code,crdr,vendor,SUM(amount) as amount FROM `account_summary` WHERE `date` <= '$date15' AND `active` = '1'".$coa_filter."".$customer_filter." AND `dflag` = '0' GROUP BY crdr,coa_code,vendor ORDER BY `vendor` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['coa_code']."@".$row['vendor']; $date15_arr_codes[$key] = $key;
                        if($row['crdr'] == "CR"){ $date15_cr_amt[$key] = $row['amount']; }
                        else if($row['crdr'] == "DR"){ $date15_dr_amt[$key] = $row['amount']; }
                        else{ }
                    }
                    foreach($date15_arr_codes as $key){
                        if(empty($date15_cr_amt[$key])){ $date15_cr_amt[$key] = 0; }
                        if(empty($date15_dr_amt[$key])){ $date15_dr_amt[$key] = 0; }

                        $key1 = explode("@",$key);
                        if($obtype[$key1[1]] == "Cr"){ $cr_amt = $obamt[$row['code']]; $dr_amt = 0; }
                        else if($obtype[$key1[1]] == "Dr"){ $dr_amt = $obamt[$row['code']]; $cr_amt = 0; }
                        
                        $date15_out_bals[$key] += (((float)$date15_dr_amt[$key] + (float)$dr_amt) - ((float)$date15_cr_amt[$key] + (float)$cr_amt));
                    }

                    //Present Date Outstanding Balance
                    $datep = date("Y-m-d");
                    $datep_arr_codes = $datep_cr_amt = $datep_dr_amt = $datep_out_bals = array();
                    $sql = "SELECT coa_code,crdr,vendor,SUM(amount) as amount FROM `account_summary` WHERE `date` <= '$datep' AND `active` = '1'".$coa_filter."".$customer_filter." AND `dflag` = '0' GROUP BY crdr,coa_code,vendor ORDER BY `vendor` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['coa_code']."@".$row['vendor']; $datep_arr_codes[$key] = $key;
                        if($row['crdr'] == "CR"){ $datep_cr_amt[$key] = $row['amount']; }
                        else if($row['crdr'] == "DR"){ $datep_dr_amt[$key] = $row['amount']; }
                        else{ }
                    }
                    foreach($datep_arr_codes as $key){
                        if(empty($datep_cr_amt[$key])){ $datep_cr_amt[$key] = 0; }
                        if(empty($datep_dr_amt[$key])){ $datep_dr_amt[$key] = 0; }

                        $key1 = explode("@",$key);
                        if($obtype[$key1[1]] == "Cr"){ $cr_amt = $obamt[$row['code']]; $dr_amt = 0; }
                        else if($obtype[$key1[1]] == "Dr"){ $dr_amt = $obamt[$row['code']]; $cr_amt = 0; }
                        
                        $datep_out_bals[$key] += (((float)$datep_dr_amt[$key] + (float)$dr_amt) - ((float)$datep_cr_amt[$key] + (float)$cr_amt));
                    }

                    //Yesterday Date Receipt Amount
                    $rct_date = date("Y-m-d",strtotime($tdate."-1 days"));
                    $rct_amt = array();
                    $sql = "SELECT ccode,SUM(amount) as amount FROM `broiler_receipts` WHERE `date` = '$rct_date' AND `active` = '1' AND `dflag` = '0' GROUP BY ccode ORDER BY `ccode` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $key = $row['ccode']; $rct_amt[$key] = $row['amount']; }

                    //Latest Sale Date
                    $sale_date = date("Y-m-d",strtotime($tdate));
                    $latest_sale_date = array();
                    $sql = "SELECT vcode,MAX(date) as date FROM `broiler_sales` WHERE `date` <= '$sale_date' AND `active` = '1' AND `dflag` = '0' GROUP BY vcode ORDER BY `vcode` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $key = $row['vcode']; $latest_sale_date[$key] = $row['date']; }

                    $slno = 0; $html = '';
                    foreach($vendor_code as $vcode){
                        $vgcode = $vengrp_ccode[$vendor_grps[$vcode]];
                        $key = $vgcode."@".$vcode;

                        if(!empty($datep_out_bals[$key]) && number_format_ind($datep_out_bals[$key]) != "0.00"){ //!empty($datea_out_bals[$key]) && number_format_ind($datea_out_bals[$key]) != "0.00" || !empty($date10_out_bals[$key]) && number_format_ind($date10_out_bals[$key]) != "0.00" || !empty($date15_out_bals[$key]) && number_format_ind($date15_out_bals[$key]) != "0.00" || 
                            $slno++;
                            $html .= '<tr>
                            <td style="text-align:center;">'.$slno.'</td>
                            <td style="text-align:left;">'.$vendor_name[$vcode].'</td>
                            <td style="text-align:right;">'.number_format_ind(round($datea_out_bals[$key],2)).'</td>
                            <td style="text-align:right;">'.number_format_ind(round($date10_out_bals[$key],2)).'</td>
                            <td style="text-align:right;">'.number_format_ind(round($date15_out_bals[$key],2)).'</td>
                            ';
                            if(date("d.m.Y",strtotime($latest_sale_date[$vcode])) == "01.01.1970"){
                                $html .= '<td style="text-align:right;"></td><td style="text-align:right;">0</td>';
                            }
                            else{
                                $html .= '<td style="text-align:right;">'.date("d.m.Y",strtotime($latest_sale_date[$vcode])).'</td><td style="text-align:right;">'.(((strtotime($tdate) - strtotime($latest_sale_date[$vcode]))  / 60 / 60 / 24)).'</td>';
                            }
                            $html .= '
                            <td style="text-align:right;">'.number_format_ind(round($rct_amt[$vcode],2)).'</td>
                            <td style="text-align:right;">'.number_format_ind(round($datep_out_bals[$key],2)).'</td>
                            </tr>';

                            $datea_tamt += (float)$datea_out_bals[$key];
                            $date10_tamt += (float)$date10_out_bals[$key];
                            $date15_tamt += (float)$date15_out_bals[$key];
                            $daterct_tamt += (float)$rct_amt[$vcode];
                            $datep_tamt += (float)$datep_out_bals[$key];
                        }
                    }
                ?>
                </tbody>
            <?php
                $html .= '<thead><tr class="thead3">
                <th style="text-align:right;" colspan="2">Total</th>
                <th style="text-align:right;">'.number_format_ind(round($datea_tamt,2)).'</th>
                <th style="text-align:right;">'.number_format_ind(round($date10_tamt,2)).'</th>
                <th style="text-align:right;">'.number_format_ind(round($date15_tamt,2)).'</th>
                <th style="text-align:right;"></th><th style="text-align:right;"></th>
                <th style="text-align:right;">'.number_format_ind(round($daterct_tamt,2)).'</th>
                <th style="text-align:right;">'.number_format_ind(round($datep_tamt,2)).'</th>
                </tr></thead>';

                echo $html;
            }
            ?>
        </table>
        <script>
            function checkval(){
                var usr_code = document.getElementById("usr_code").value;
                if(usr_code == "select"){
                    alert('Please User');
                    document.getElementById("usr_code").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
            function broiler_set_vendors(){
                var vendor_groups = document.getElementById("vendor_groups").value;
                    
                removeAllOptions(document.getElementById("vendors"));
                myselect = document.getElementById("vendors"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("All"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                
                if(vendor_groups != "all"){
				<?php
                    foreach($vendor_code as $vcode){
                        $v2 = $vendor_grps[$vcode];
                        echo "if(vendor_groups == '$v2'){";
                        ?>
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $vendor_name[$vcode]; ?>"); theOption1.value = "<?php echo $vcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                        <?php
                        echo "}";
                    }
                ?>
                }
                else{
                <?php
                    foreach($vendor_code as $vcode){
                    ?>
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $vendor_name[$vcode]; ?>"); theOption1.value = "<?php echo $vcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                    }
                ?>
                }
            }
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
                    asc = !asc;
                    })
                });
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script>
            function checkval(){
                var tdate = document.getElementById("tdate").value;
                var dc_nos = document.getElementById("dc_nos").value;
                var l = true;
                if(tdate == ""){
                    alert("Please select appropriate date");
                    document.getElementById("tdate").focus();
                    l = false;
                }
                else if(dc_nos == "select"){
                    alert("Please select appropriate DC No.");
                    document.getElementById("dc_nos").focus();
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
            function fetch_nisan_sales_dcnos(){
                var tdate = document.getElementById("tdate").value;
                removeAllOptions(document.getElementById("dc_nos"));
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_nisan_saledcnos_details.php?tdate="+tdate;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length > 0){
                            $('#dc_nos').append(item_list);
                        }
                        else{
                            alert("Active Sales Details are not available \n Kindly check and try again ...!");
                        }
                    }
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