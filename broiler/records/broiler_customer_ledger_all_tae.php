<?php
//broiler_customer_ledger_all_tae.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
$user_code = $_SESSION['userid'];
include "header_head.php";

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){  $group_access_code = $row['cgroup_access']; }
if($group_access_code == "all" || $group_access_code == ""){ $group_access_filter1 = ""; } else{ $group_access_list = implode("','", explode(",",$group_access_code)); $group_access_filter1 = " AND `code` IN ('$group_access_list')"; $group_access_filter2 = " AND `groupcode` IN ('$group_access_list')"; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `dflag` = '0'".$group_access_filter1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
  echo  $vengrp_code[$row['code']] = $row['code'];
    $vengrp_name[$row['code']] = $row['description'];
    $vengrp_gtype[$row['code']] = $row['gtype'];
    $grp_ccac[$row['code']] = $row['cus_controller_code'];
    $grp_scac[$row['code']] = $row['sup_controller_code'];
}
echo "<br>";
$fdate = $tdate = date("Y-m-d"); $vendor_group = "all"; $excel_type = "display"; $cas_flag = 0;
if(isset($_REQUEST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
	$vendor_group = $_REQUEST['vendor_group'];
    $clines = $_POST['cline'];
    $cas_flag = 0; if($_REQUEST['cas_flag'] == TRUE || $_REQUEST['cas_flag'] == "on" || (int)$_REQUEST['cas_flag'] == 1){ $cas_flag = 1; }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/CustomerBalanceReportAll-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendor_group=".$vendor_group;
}

$cline_fltr = "";
    if($clines != "all"){ $cline_fltr = " AND `cline_code` IN ('$clines')"; }


if($vendor_group != "all"){
    $vengrp_filter = " AND `groupcode` LIKE '$vendor_group'";
}
else{
    if($cas_flag == 1){
        $vengrp_filter = "";
    }
    else{
        $glist = "";
        foreach($vengrp_code as $gcode){ if($vengrp_gtype[$gcode] == "C"){ if($glist == ""){ $glist = $gcode; } else{ $glist = $glist."','".$gcode; } } }
       echo $vengrp_filter = " AND `groupcode` IN ('$glist')";
    }
}
echo "<br>";
echo $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$vengrp_filter."".$cline_fltr." AND `active` ='1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $vendor_code = array();
while($row = mysqli_fetch_assoc($query)){
    $vendor_code[$row['code']] = $row['code'];
    $vendor_ccode[$row['code']] = $row['cus_ccode'];
    $vendor_name[$row['code']] = $row['name'];
    $vendor_ctype[$row['code']] = $row['contacttype'];
    $vendor_gcode[$row['code']] = $row['groupcode'];
    $obdate[$row['code']] = $row['obdate'];
    $obtype[$row['code']] = $row['obtype'];
    $obtrnum[$row['code']] = $row['opn_trnum'];
    $obamt[$row['code']] = $row['obamt'];
    $olimitamt[$row['code']] = $row['creditamt'];
}
$customer_filter = "";
$customer_filter = implode("','",$vendor_code);
echo "<br>";
/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_payments LIKE poulso6_admin_broiler_broilermaster.broiler_payments;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_itemreturns", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_itemreturns LIKE poulso6_admin_broiler_broilermaster.broiler_itemreturns;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_crdrnote", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_crdrnote LIKE poulso6_admin_broiler_broilermaster.broiler_crdrnote;"; mysqli_query($conn,$sql1); }
if(in_array("account_contranotes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.account_contranotes LIKE poulso6_admin_broiler_broilermaster.account_contranotes;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_voucher_notes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_voucher_notes LIKE poulso6_admin_broiler_broilermaster.broiler_voucher_notes;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_transitloss", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_transitloss LIKE poulso6_admin_broiler_broilermaster.broiler_transitloss;"; mysqli_query($conn,$sql1); }


$sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cline_code[$row['code']] = $row['code']; $cline_name[$row['code']] = $row['description']; }
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="17" align="center"><?php echo $row['cdetails']; ?><h5>Customer Balance Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_customer_ledger_all_tae.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="19">
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
                                    <label>C&S</label>
                                    <input type="checkbox" name="cas_flag" id="cas_flag" class="form-control" <?php if($cas_flag == 1){ echo "checked"; } ?> />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer Group</label>
                                    <select name="vendor_group" id="vendor_group" class="form-control select2">
                                        <option value="all" <?php if($vendor_group == "all"){ echo "selected"; } ?> >-All-</option>
                                        <?php
                                        foreach($vengrp_code as $vgcode){
                                        ?>
                                        <option value="<?php echo $vgcode; ?>" <?php if($vendor_group == $vgcode){ echo "selected"; } ?> ><?php echo $vengrp_name[$vgcode]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="m-2 form-group">
                                    <label>Customer Line</label>
                                    <select name="cline" id="cline" class="form-control select2">
                                        <option value="all" <?php if($clines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($cline_code as $bcode){ if($cline_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($clines == $bcode){ echo "selected"; } ?>><?php echo $cline_name[$bcode]; ?></option>
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
            <thead class="thead3" align="center">
                <tr align="center">
                    <th rowspan="2">Group</th>
                    <th rowspan="2">Code</th>
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Opening Balance</th>
                    <th colspan="5">Selected Period</th>
                    <th colspan="2" >Balance</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr align="center">
                    <th>Birds</th>
                    <th>Weight</th>
                    <th>Amount</th>
                    <th>Receipt</th>
                    <th>B/w days balance</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    <th>Credit Limit</th>
                    <th>Limit Exceded</th>
                    <th>Available Limit</th>
                    <th>Last Receipt Gap Days</th>
                </tr>
            </thead>
            <?php
            if(isset($_REQUEST['submit_report']) == true){
                
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = "";
                    $opening_sales = $opening_receipts = $opening_ccn = $opening_cdn = $opening_returns = $opening_cntcr = $opening_cntdr = $opening_vcr = $opening_tloss = 
                    $opening_vdr = $last_rdate = array();

                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` < '$fdate' AND `vcode` IN ('$customer_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $opening_sales[$row['vcode']]  += (float)$row['finl_amt']; $old_inv = $row['trnum']; } $opening_sales[$row['vcode']]  += (float)$row['customer_freight_amt']; } }

                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` < '$fdate' AND `ccode` IN ('$customer_filter') AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_receipts[$row['ccode']] += (float)$row['amount']; if(empty($last_rdate[$row['ccode']]) || $last_rdate[$row['ccode']] == "" || strtotime($last_rdate[$row['ccode']]) <= strtotime($row['date'])){ $last_rdate[$row['ccode']] = $row['date']; } } }

                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` IN ('$customer_filter') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_returns[$row['vcode']] += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` IN ('$customer_filter') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $opening_ccn[$row['vcode']] += (float)$row['amount']; } else{ $opening_cdn[$row['vcode']] += (float)$row['amount']; } } }
                    
                    $sql_record = "SELECT SUM(amount) as amount,fcoa FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` IN ('$customer_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `fcoa` ORDER BY `fcoa` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntcr[$row['fcoa']] += (float)$row['amount']; } }

                    $sql_record = "SELECT SUM(amount) as amount,tcoa FROM `account_contranotes` WHERE `date` < '$fdate' AND `tcoa` IN ('$customer_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `tcoa` ORDER BY `tcoa` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntdr[$row['tcoa']] += (float)$row['amount']; } }

                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` < '$fdate' AND `vcode` IN ('$customer_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_vcr[$row['vcode']] += (float)$row['cr_amt']; $opening_vdr[$row['vcode']] += (float)$row['dr_amt']; } }

                    $sql_record = "SELECT * FROM `broiler_transitloss` WHERE `date` < '$fdate' AND `vcode` IN ('$customer_filter') AND `type` IN ('Customer') AND `rtype` IN ('TransitLoss') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record); $i = 0; $transaction_count = 0; if (!empty($query)) { $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while ($row = mysqli_fetch_assoc($query)) { $opening_tloss[$row['vcode']] += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` < '$fdate' AND `to_account` IN ('$customer_filter') AND `t_type` IN ('Customer Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record); $transaction_count = 0; 
                    while($row = mysqli_fetch_assoc($query)){
                        $opening_payments[$row['to_account']] += (float)$row['amount'];
                    }

                  echo  $sql_record = "SELECT * FROM `master_receipts` WHERE `date` < '$fdate' AND `to_account` IN ('$customer_filter') AND `t_type` IN ('Customer Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                           echo $opening_receipts[$row['to_account']] += (float)$row['amount'];
                        }
                    }

                    $old_inv = "";
                    $between_sales_birds = $between_sales_weight = $between_sales_amt = array();
                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$customer_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                $between_sales_birds[$row['vcode']] += (float)$row['birds'];
                                $between_sales_weight[$row['vcode']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $between_sales_amt[$row['vcode']] += (float)$row['finl_amt'];
                                $old_inv = $row['trnum'];
                            }
                            $between_sales_amt[$row['vcode']] += (float)$row['customer_freight_amt'];
                        }
                    }

                    $between_rct_amt = array();
                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` IN ('$customer_filter') AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $between_rct_amt[$row['ccode']] += (float)$row['amount'];
                            if(empty($last_rdate[$row['ccode']]) || $last_rdate[$row['ccode']] == "" || strtotime($last_rdate[$row['ccode']]) <= strtotime($row['date'])){ $last_rdate[$row['ccode']] = $row['date']; }
                        }
                    }
                    
                    $between_cus_ireturn_birds = $between_cus_ireturn_weight = $between_cus_ireturn_amt = array();
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$customer_filter') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                        $between_cus_ireturn_birds[$row['vcode']] += (float)$row['amount'];
                        $between_cus_ireturn_weight[$row['vcode']] += (float)$row['amount'];
                        $between_cus_ireturn_amt[$row['vcode']] += (float)$row['amount'];
                        }
                    }
                    
                    $between_ccn = $between_cntdr = array();
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$customer_filter') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){ $between_ccn[$row['vcode']] += (float)$row['amount']; } else{ $between_cdn[$row['vcode']] += (float)$row['amount']; }
                        }
                    }
                    $between_cntcr = $between_cntdr = array();
                    $sql_record = "SELECT SUM(amount) as amount,fcoa FROM `account_contranotes` WHERE`date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` IN ('$customer_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `fcoa` ORDER BY `fcoa` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_cntcr[$row['fcoa']] += (float)$row['amount']; } }

                    $sql_record = "SELECT SUM(amount) as amount,tcoa FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tcoa` IN ('$customer_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `tcoa` ORDER BY `tcoa` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_cntdr[$row['tcoa']] += (float)$row['amount']; } }
                    
                    $between_vcr = $between_vdr = array();
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$customer_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_vcr[$row['vcode']] += (float)$row['cr_amt']; $between_vdr[$row['vcode']] += (float)$row['dr_amt']; } }

                    $between_tloss = array();
                    $sql_record = "SELECT * FROM `broiler_transitloss` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$customer_filter') AND `type` IN ('Customer') AND `rtype` IN ('TransitLoss') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record); $i = 0; $transaction_count = 0; if (!empty($query)) { $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while ($row = mysqli_fetch_assoc($query)) { $between_tloss[$row['vcode']] += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` IN ('$customer_filter') AND `t_type` IN ('Customer Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $between_rct_amt[$row['to_account']] += (float)$row['amount'];
                        }
                    }
                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` < '$fdate' AND `to_account` = '$vendors' AND `t_type` IN ('Customer Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $between_payments[$row['to_account']] += (float)$row['amount'];
                        }
                    }


                    $final_opening_amt = $final_between_sale_birds = $final_between_sale_weight = $final_between_sale_amt = $final_between_rct_amt = $final_between_balance_amt = 
                    $final_all_customer_balance_amt = $tot_cr_amt = $tot_dr_amt = 0; $today = date("Y-m-d");
                    foreach($vendor_code as $vcode){
                        /*Customer Inital Opening Balance */
                        $ob_cramt = $ob_dramt = 0;
                        //check for C&S
                        if(strtolower($vendor_ctype[$vcode]) == "s&c"){
                            $otrnum = $obtrnum[$vcode]; $gcode = $vendor_gcode[$vcode]; $c_coa = $grp_ccac[$gcode]; $s_coa = $grp_scac[$gcode];
                            $sql1 = "SELECT * FROM `account_summary` WHERE `trnum` = '$otrnum' AND `coa_code` IN ('$c_coa') AND `active` = '1' AND `dflag` = '0'";
                            $query1 = mysqli_query($conn,$sql1); $s_cnt = mysqli_num_rows($query1);
                            if($s_cnt > 0){
                                if($obtype[$vcode] == "Cr"){ $ob_cramt = (float)$obamt[$vcode]; $ob_dramt = 0; } else{ $ob_dramt = (float)$obamt[$vcode]; $ob_cramt = 0; }
                            }
                        }
                        else{
                            if($obtype[$vcode] == "Cr"){ $ob_cramt = (float)$obamt[$vcode]; $ob_dramt = 0; } else{ $ob_dramt = (float)$obamt[$vcode]; $ob_cramt = 0; }
                        }
                        //if($obtype[$vcode] == "Cr"){ $ob_cramt = (float)round($obamt[$vcode],5); $ob_dramt = 0; } else{ $ob_dramt = (float)round($obamt[$vcode],5); $ob_cramt = 0; }

                        /*Opening Balance Calculations */
                        $current_opening_amt = 0;
                        $current_opening_amt = (((float)round($opening_sales[$vcode],5) + (float)round($opening_cdn[$vcode],5) + (float)round($ob_dramt,5) + (float)round($opening_cntdr[$vcode],5) + (float)round($opening_vdr[$vcode],5) + (float)round($opening_payments[$vcode],2)) - ((float)round($opening_receipts[$vcode],5) + (float)round($opening_vcr[$vcode],5) + (float)round($opening_ccn[$vcode],5) + (float)round($opening_returns[$vcode],5) + (float)round($ob_cramt,5) + (float)round($opening_cntcr[$vcode],5) + (float)round($opening_tloss[$vcode],5)));
                          $opns += (float)round($opening_sales[$vcode]);
                        // echo "<br>broiler_crdrnote";
                         $opnn += (float)round($opening_cdn[$vcode]);
                        // echo "<br>account_summary";
                         $opnd += (float)round($ob_dramt);
                        // echo "<br>account_contranotes";
                         $opnt += (float)round($opening_cntdr[$vcode]);
                        // echo "<br>broiler_voucher_notes";
                         $opnv += (float)round($opening_vdr[$vcode]);
                        // echo "<br>master_payments";
                         $opnp += (float)round($opening_payments[$vcode]);
                        // echo "<br> Minus ";
                        // echo "<br>master_receipts";
                         $opnr += (float)round($opening_receipts[$vcode]);
                        // echo "<br>broiler_voucher_notes";
                         $opnvc += (float)round($opening_vcr[$vcode]);
                        // echo "<br>broiler_crdrnote";
                         $opncc += (float)round($opening_ccn[$vcode]);
                        // echo "<br>broiler_itemreturns";
                         $opnrt += (float)round($opening_returns[$vcode]);
                        // echo "<br>account_summary";
                         $opncrt += (float)round($ob_cramt);
                        // echo "<br>account_contranotes";
                         $opncnt += (float)round($opening_cntcr[$vcode]);
                        // echo "<br>broiler_transitloss";
                         $opntl += (float)round($opening_tloss[$vcode]);
                        // echo "<br> total Opening Amount";
                         $opna += (float)round($current_opening_amt);
                        //if($vendor_name[$vcode] == "SS Reddy Hatchery"){
                        //echo "<br/>$current_opening_amt = (((float)round($opening_sales[$vcode],5) + (float)round($opening_cdn[$vcode],5) + (float)round($ob_dramt,5) + (float)round($opening_cntdr[$vcode],5) + (float)round($opening_vdr[$vcode],5)) - ((float)round($opening_receipts[$vcode],5) + (float)round($opening_vcr[$vcode],5) + (float)round($opening_ccn[$vcode],5) + (float)round($opening_returns[$vcode],5) + (float)round($ob_cramt,5) + (float)round($opening_cntcr[$vcode],5) + (float)round($opening_tloss[$vcode],5)));";
                        //}
                        /*if($vendor_name[$vcode] == "KARTICK MONDAL"){
                            if($_SERVER['REMOTE_ADDR'] == "49.207.226.103"){
                                echo "<br/>Sale: ".$opening_sales[$vcode];
                                echo "<br/>Debit Note: ".$opening_cdn[$vcode];
                                echo "<br/>Contra Dr: ".$opening_cntdr[$vcode];
                                echo "<br/>Voucher Dr: ".$opening_vdr[$vcode];
                                echo "<br/>OB Dr: ".$ob_dramt;
                                echo "<br/>Receipt: ".$opening_receipts[$vcode];
                                echo "<br/>Sale Return: ".$opening_returns[$vcode];
                                echo "<br/>Credit Note: ".$opening_ccn[$vcode];
                                echo "<br/>Contra Cr: ".$opening_cntcr[$vcode];
                                echo "<br/>Voucher Cr: ".$opening_vcr[$vcode];
                                echo "<br/>Transit Loss Cr: ".$opening_tloss[$vcode];
                                echo "<br/>OB Cr: ".$ob_cramt;
                            }
                        }*/
                        /*Between days Total Birds */
                        $current_sale_birds = 0;
                        $current_sale_birds = (float)round($between_sales_birds[$vcode],5) - (float)round($between_cus_ireturn_birds[$vcode],5);

                        /*Between days Total Weight */
                        $current_sale_weight = 0;
                        $current_sale_weight = (float)round($between_sales_weight[$vcode],5) - (float)round($between_cus_ireturn_weight[$vcode],5);

                        /*Between days Total Amount */
                        $current_sale_amt = 0;
                        $current_sale_amt = (float)round($between_sales_amt[$vcode],5) + (float)round($between_cdn[$vcode],5) + (float)round($between_cntdr[$vcode],5) + (float)round($between_payments[$vcode],5);

                        /*Between days Total Receipt */
                        $current_rct_amt = 0;
                        $current_rct_amt = (((float)round($between_rct_amt[$vcode],5) + (float)round($between_vcr[$vcode],5) + (float)round($between_tloss[$vcode],5) + (float)round($between_ccn[$vcode],5) + (float)round($between_cus_ireturn_amt[$vcode],5) + (float)round($between_cntcr[$vcode],5)) - (float)round($between_vdr[$vcode],5));

                        /*Between Days Balance Amount */
                        $current_Balance_amt = 0;
                        $current_Balance_amt = (float)round($current_sale_amt,5) - (float)round($current_rct_amt,5);
                         // echo "<br> Customer Sale";
                         $ctm += $current_sale_amt;
                        // echo "<br> Customer Receipt";
                         $cmt += $current_rct_amt;

                        /*Final Customer Balance */
                        $final_customer_balance_amt = 0;
                        $final_customer_balance_amt = (float)round($current_opening_amt,5) + (float)round($current_Balance_amt,5);
                        // echo "<br> Current Opening";
                         $cts += $current_opening_amt;
                        // echo "<br> Current Balance";
                         $ctss += $current_Balance_amt;
                         
                        if(number_format_ind($final_customer_balance_amt) != "0.00"){
                            $url_link = "broiler_customer_ledger.php?fdate=".$fdate."&tdate=".$tdate."&vendors=".$vcode;
                            echo "<tr>";
                            echo "<td>".$vengrp_name[$vendor_gcode[$vcode]]."</td>";
                            //echo "<td>".$vcode."</td>";
                            echo "<td>".$vendor_ccode[$vcode]."</td>";
                            echo "<td><a href='$url_link' target='_BLANK'>".$vendor_name[$vcode]."</a></td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_opening_amt)."</td>";
                            echo "<td style='text-align:right;'>".str_replace(".00","",number_format_ind($current_sale_birds))."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_sale_weight)."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_sale_amt)."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_rct_amt)."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_Balance_amt)."</td>";
                            if((float)$final_customer_balance_amt < 0){
                                echo "<td style='text-align:right;'>".str_replace("-","",number_format_ind($final_customer_balance_amt))."</td>";
                                echo "<td style='text-align:right;'></td>";
                                $tot_cr_amt += (float)$final_customer_balance_amt;
                            }
                            else{
                                echo "<td style='text-align:right;'></td>";
                                echo "<td style='text-align:right;'>".number_format_ind($final_customer_balance_amt)."</td>";
                                $tot_dr_amt += (float)$final_customer_balance_amt;
                            }
                            //echo "<td style='text-align:right;'>".number_format_ind($final_customer_balance_amt)."</td>";
                            $limit_amt = $olimitamt[$vcode]; $exd_limit = 0;
                            $rct_gapdays = 0; if(!empty($last_rdate[$vcode]) && $last_rdate[$vcode] != ""){ $rct_gapdays = (INT)((strtotime($today) - strtotime($last_rdate[$vcode])) / 60 / 60 / 24); }
                            echo "<td style='text-align:right;'>".round($limit_amt)."</td>";
                            if((float)$final_customer_balance_amt > (float)$limit_amt){ $exd_limit = (float)$final_customer_balance_amt - (float)$limit_amt; $red = "color:red;";
                                echo "<td style='text-align:right;color:red;'>".number_format_ind($exd_limit)."</td>"; } else {
                                    echo "<td style='text-align:right;'>".number_format_ind($exd_limit)."</td>"; 
                                }
                                if((float)$final_customer_balance_amt < (float)$limit_amt){ $avl_limit = (float)$limit_amt - (float)$final_customer_balance_amt; } else { $avl_limit = 0; }
                                echo "<td style='text-align:right;'>".number_format_ind($avl_limit)."</td>";
                                echo "<td style='text-align:right;'>".round($rct_gapdays)."</td>";

                            echo "</tr>";

                            /*Final Total */
                            $final_opening_amt += (float)round($current_opening_amt,5);
                            $final_between_sale_birds += (float)round($current_sale_birds,5);
                            $final_between_sale_weight += (float)round($current_sale_weight,5);
                            $final_between_sale_amt += (float)round($current_sale_amt,5);
                            $final_between_rct_amt += (float)round($current_rct_amt,5);
                            $final_between_balance_amt += (float)round($current_Balance_amt,5);
                            $final_all_customer_balance_amt += (float)round($final_customer_balance_amt,5);
                            $final_credit_limit_amt += (float)round($limit_amt,5);
                            $final_credit_limit_exceed_amt += (float)round($exd_limit,5);
                        }
                    }
                    echo " <br>";
                        echo "<br>Customes Sales";
                        echo $opns;
                        echo "<br>broiler_crdrnote";
                        echo $opnn ;
                        echo "<br>account_summary";
                        echo $opnd ;
                        echo "<br>account_contranotes";
                        echo $opnt ;
                        echo "<br>broiler_voucher_notes";
                        echo $opnv ;
                        echo "<br>master_payments";
                        echo $opnp ;
                        echo "<br> Minus ";
                        echo "<br>master_receipts";
                        echo $opnr ;
                        echo "<br>broiler_voucher_notes";
                        echo $opnvc ;
                        echo "<br>broiler_crdrnote";
                        echo $opncc ;
                        echo "<br>broiler_itemreturns";
                        echo $opnrt ;
                        echo "<br>account_summary";
                        echo $opncrt ;
                        echo "<br>account_contranotes";
                        echo $opncnt ;
                        echo "<br>broiler_transitloss";
                        echo $opntl ;
                        echo "<br> total Opening Amount";
                        echo $opna ;
                        echo "<br>";
                        echo "<br> Customer Sale";
                        echo $ctm;
                        echo "<br> Customer Receipt";
                        echo $cmt;
                         echo "<br> Current Opening";
                        echo $cts;
                         echo "<br>Old Current Opening";
                        echo $final_opening_amt;
                        echo "<br> Current Balance";
                        echo $ctss;
                        echo "<br>Total CR Current Balance";
                        echo $tot_cr_amt;
                        echo "<br>Total DR Current Balance";
                        echo $tot_dr_amt;
                    echo "<tr class='thead3'>";
					echo "<td style='text-align:center;font-weight:bold;' colspan='3'>Total</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_opening_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".str_replace(".00","",number_format_ind($final_between_sale_birds))."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_sale_weight)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_sale_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_rct_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_balance_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".str_replace("-","",number_format_ind($tot_cr_amt))."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($tot_dr_amt)."</td>";
                    echo "<td style='font-weight:bold;'></td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_credit_limit_exceed_amt)."</td>";
                    echo "<td style='font-weight:bold;'></td>";
                    echo "<td style='font-weight:bold;'></td>";
                    echo "</tr>";
                ?>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>