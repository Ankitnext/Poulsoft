<?php
//broiler_supplier_ledger_all.php
include "../newConfig.php";
include "number_format_ind.php";
global $page_title; $page_title = "Supplier Balance Report";
include "header_head.php";

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%S%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $vengrp_code[$row['code']] = $row['code'];
    $vengrp_name[$row['code']] = $row['description'];
    $vengrp_gtype[$row['code']] = $row['gtype'];
    $grp_ccac[$row['code']] = $row['cus_controller_code'];
    $grp_scac[$row['code']] = $row['sup_controller_code'];
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '$href' AND `field_function` LIKE 'From Date Auto Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $fdate = date("Y-m-d");
while($row = mysqli_fetch_assoc($query)){ if($row['field_value'] != ""){ $fdate = date("Y-m-d",strtotime($row['field_value'])); } }

$tdate = date("Y-m-d"); $vendor_group = "all"; $excel_type = "display"; $cas_flag = 0;
if(isset($_REQUEST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
	$vendor_group = $_REQUEST['vendor_group'];
    $cas_flag = 0; if($_REQUEST['cas_flag'] == TRUE || $_REQUEST['cas_flag'] == "on" || (int)$_REQUEST['cas_flag'] == 1){ $cas_flag = 1; }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/SupplierBalanceReportAll-Excel.php?fromdate=".$fdate."&todate=".$tdate;
}
if($vendor_group != "all"){
    $vengrp_filter = " AND `groupcode` LIKE '$vendor_group'";
}
else{
    if($cas_flag == 1){
        $vengrp_filter = "";
    }
    else{
        $glist = "";
        foreach($vengrp_code as $gcode){ if($vengrp_gtype[$gcode] == "S"){ if($glist == ""){ $glist = $gcode; } else{ $glist = $glist."','".$gcode; } } }
        $vengrp_filter = " AND `groupcode` IN ('$glist')";
    }
}

$vendor_code = array();
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%'".$vengrp_filter." AND `active` ='1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $vendor_code[$row['code']] = $row['code'];
    $vendor_name[$row['code']] = $row['name'];
    $vendor_ctype[$row['code']] = $row['contacttype'];
    $vendor_gcode[$row['code']] = $row['groupcode'];
    $obdate[$row['code']] = $row['obdate'];
    $obtype[$row['code']] = $row['obtype'];
    $obtrnum[$row['code']] = $row['opn_trnum'];
    $obamt[$row['code']] = $row['obamt'];
    //echo "<br/>".$row['code']."@".$row['name'];
}
$supplier_filter = "";
$supplier_filter = implode("','",$vendor_code);

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_payments LIKE poulso6_admin_broiler_broilermaster.broiler_payments;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_itemreturns", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_itemreturns LIKE poulso6_admin_broiler_broilermaster.broiler_itemreturns;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_crdrnote", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_crdrnote LIKE poulso6_admin_broiler_broilermaster.broiler_crdrnote;"; mysqli_query($conn,$sql1); }
if(in_array("account_contranotes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.account_contranotes LIKE poulso6_admin_broiler_broilermaster.account_contranotes;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_voucher_notes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_voucher_notes LIKE poulso6_admin_broiler_broilermaster.broiler_voucher_notes;"; mysqli_query($conn,$sql1); }

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
                    <th colspan="13" align="center"><?php echo $row['cdetails']; ?><h5>Supplier Balance Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_supplier_ledger_all.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="15">
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
                                    <label>Supplier</label>
                                    <select name="vendor_group" id="vendor_group" class="form-control select2">
                                        <option value="all" selected>-All-</option>
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
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Opening Balance</th>
                    <th colspan="3">Selected Period</th>
                    <th colspan="2">Balance</th>
                    <th rowspan="2">Last Payment gap Days</th>
                </tr>
                <tr align="center">
                    <th>Amount</th>
                    <th>Receipt</th>
                    <th>B/w days balance</th>
                    <th>Credit</th>
                    <th>Debit</th>
                </tr>
            </thead>
            <?php
            if(isset($_REQUEST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = "";
                    $opening_sales = $opening_receipts = $opening_returns = $opening_ccn = $opening_cdn = $opening_cntcr = $opening_cntdr = array();

                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` < '$fdate' AND `vcode` IN ('$supplier_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $opening_sales[$row['vcode']] += (float)$row['finl_amt']; $old_inv = $row['trnum']; } } }

                    $sql = "SELECT * FROM `broiler_payments` WHERE `date` < '$fdate' AND `ccode` IN ('$supplier_filter') AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_receipts[$row['ccode']] += (float)$row['amount']; } }

                    $sql = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` IN ('$supplier_filter') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_returns[$row['vcode']] += (float)$row['amount']; } }

                    $sql = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` IN ('$supplier_filter') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $opening_ccn[$row['vcode']] += (float)$row['amount']; } else{ $opening_cdn[$row['vcode']] += (float)$row['amount']; } } }

                    $sql = "SELECT SUM(amount) as amount,fcoa FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` IN ('$supplier_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `fcoa` ORDER BY `fcoa` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntcr[$row['fcoa']] += (float)$row['amount']; } }

                    $sql = "SELECT SUM(amount) as amount,tcoa FROM `account_contranotes` WHERE `date` < '$fdate' AND `tcoa` IN ('$supplier_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `tcoa` ORDER BY `tcoa` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntdr[$row['tcoa']] += (float)$row['amount']; } }

                    $opening_vcr = $opening_vdr = array();
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` < '$fdate' AND `vcode` IN ('$supplier_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_vcr[$row['vcode']] += (float)$row['cr_amt']; $opening_vdr[$row['vcode']] += (float)$row['dr_amt']; } }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` < '$fdate' AND `to_account` IN ('$supplier_filter') AND `t_type` IN ('Supplier Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $opening_master_payments = 0;
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_receipts[$row['to_account']] += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` < '$fdate' AND `to_account` IN ('$supplier_filter') AND `t_type` IN ('Supplier Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $opening_master_rcts = array();
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_master_rcts[$row['to_account']] += (float)$row['amount']; } }

                    $old_inv = "";
                    $between_sales_birds = $between_sales_weight = $between_sales_amt = array();
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$supplier_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $between_sales_birds[$row['vcode']] += (float)$row['birds'];
                            $between_sales_weight[$row['vcode']] += ((float)($row['rcd_qty'] + (float)$row['fre_qty']));
                            if($old_inv != $row['trnum']){ $between_sales_amt[$row['vcode']] += (float)$row['finl_amt']; $old_inv = $row['trnum']; }
                        }
                    }

                    $between_rct_amt = array();
                    $sql = "SELECT * FROM `broiler_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` IN ('$supplier_filter') AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_rct_amt[$row['ccode']] += (float)$row['amount']; } }

                    

                    $between_cus_ireturn_birds = $between_cus_ireturn_weight = $between_cus_ireturn_amt = array();
                    $sql = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$supplier_filter') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                        $between_cus_ireturn_birds[$row['vcode']] += (float)$row['amount'];
                        $between_cus_ireturn_weight[$row['vcode']] += (float)$row['amount'];
                        $between_cus_ireturn_amt[$row['vcode']] += (float)$row['amount'];
                        }
                    }
                    
                    $between_ccn = $between_cdn = array();
                    $sql = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$supplier_filter') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){ $between_ccn[$row['vcode']] += (float)$row['amount']; } else{ $between_cdn[$row['vcode']] += (float)$row['amount']; }
                        }
                    }

                    $between_cntcr = $between_cntdr = array();
                    $sql = "SELECT SUM(amount) as amount,fcoa FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` IN ('$supplier_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `fcoa` ORDER BY `fcoa` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_cntcr[$row['fcoa']] += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` IN ('$supplier_filter') AND `t_type` IN ('Supplier Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $between_mast_pay = array();
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0;  if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $between_rct_amt[$row['to_account']] += (float)$row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` IN ('$supplier_filter') AND `t_type` IN ('Supplier Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $between_mast_rect = array();
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0;  if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $between_mast_rect[$row['to_account']] = $row['amount'];
                        }
                    }

                    $sql = "SELECT SUM(amount) as amount,tcoa FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tcoa` IN ('$supplier_filter') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' GROUP BY `tcoa` ORDER BY `tcoa` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_cntdr[$row['tcoa']] += (float)$row['amount']; } }

                    $between_vcr = $between_vdr = array();
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$supplier_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $between_vcr[$row['vcode']] += (float)$row['cr_amt']; $between_vdr[$row['vcode']] += (float)$row['dr_amt']; } }

                    $latest_payment = $latest_paydate = array();
                    $sql = "SELECT * FROM `broiler_payments` WHERE `date` <= '$tdate' AND `ccode` IN ('$supplier_filter') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if(empty($latest_paydate[$row['ccode']]) || $latest_paydate[$row['ccode']] == ""){
                                $latest_paydate[$row['ccode']] = $row['date'];
                                $latest_payment[$row['ccode']] = $row['amount'];
                            }
                            else if(strtotime($row['date']) > strtotime($latest_paydate[$row['ccode']])){
                                $latest_paydate[$row['ccode']] = $row['date'];
                                $latest_payment[$row['ccode']] = $row['amount'];
                            }
                        }
                    }




                    $today = date("Y-m-d");
                    $final_opening_amt = $final_between_sale_birds = $final_between_sale_weight = $final_between_sale_amt = $final_between_rct_amt = $final_between_balance_amt = 
                    $final_all_supplier_balance_amt = $tot_cr_amt = $tot_dr_amt = 0;
                    foreach($vendor_code as $vcode){
                        /*Supplier Inital Opening Balance */
                        $ob_cramt = $ob_dramt = 0;
                        //check for C&S
                        if(strtolower($vendor_ctype[$vcode]) == "s&c"){
                            $otrnum = $obtrnum[$vcode]; $gcode = $vendor_gcode[$vcode]; $c_coa = $grp_ccac[$gcode]; $s_coa = $grp_scac[$gcode];
                            $sql1 = "SELECT * FROM `account_summary` WHERE `trnum` = '$otrnum' AND `coa_code` IN ('$s_coa') AND `active` = '1' AND `dflag` = '0'";
                            $query1 = mysqli_query($conn,$sql1); $s_cnt = mysqli_num_rows($query1);
                            if($s_cnt > 0){
                                if($obtype[$vcode] == "Cr"){ $ob_cramt = (float)$obamt[$vcode]; $ob_dramt = 0; } else{ $ob_dramt = (float)$obamt[$vcode]; $ob_cramt = 0; }
                            }
                        }
                        else{
                            if($obtype[$vcode] == "Cr"){ $ob_cramt = (float)$obamt[$vcode]; $ob_dramt = 0; } else{ $ob_dramt = (float)$obamt[$vcode]; $ob_cramt = 0; }
                        }

                        //Initial Values
                        if(empty($opening_vcr[$vcode]) || $opening_vcr[$vcode] == ""){ $opening_vcr[$vcode] = 0; }
                        if(empty($opening_vdr[$vcode]) || $opening_vdr[$vcode] == ""){ $opening_vdr[$vcode] = 0; }
                        if(empty($between_vcr[$vcode]) || $between_vcr[$vcode] == ""){ $between_vcr[$vcode] = 0; }
                        if(empty($between_vdr[$vcode]) || $between_vdr[$vcode] == ""){ $between_vdr[$vcode] = 0; }
                        if(empty($latest_paydate[$vcode]) || $latest_paydate[$vcode] == ""){
                            $pay_gapdays = 0;
                        }
                        else{
                            $pay_gapdays = (INT)((strtotime($today) - strtotime($latest_paydate[$vcode])) / 60 / 60 / 24);
                        }

                        /*Opening Balance Calculations */
                        $current_opening_amt = $opn_sale = $opn_rct = 0;
                        $opn_sale = ((float)$opening_sales[$vcode] + (float)$opening_ccn[$vcode] + (float)$opening_cntcr[$vcode] + (float)$ob_dramt + (float)$opening_master_rcts[$vcode]);

                        $opn_rct = (((float)$opening_receipts[$vcode] + (float)$opening_cdn[$vcode] + (float)$opening_returns[$vcode] + (float)$opening_cntdr[$vcode] + (float)$opening_vdr[$vcode] + (float)$ob_cramt) - (float)$opening_vcr[$vcode]);

                        if(number_format_ind(round($opn_sale,5)) == number_format_ind(round($opn_rct,5))){ $current_opening_amt = 0; }
                        else{ $current_opening_amt = (((float)$opn_sale) - ((float)$opn_rct)); }
                        
                        /*Between days Total Birds */
                        $current_sale_birds = 0;
                        $current_sale_birds = (float)$between_sales_birds[$vcode] - (float)$between_cus_ireturn_birds[$vcode];

                        /*Between days Total Weight */
                        $current_sale_weight = 0;
                        $current_sale_weight = (float)$between_sales_weight[$vcode] - (float)$between_cus_ireturn_weight[$vcode];

                        /*Between days Total Amount */
                        $current_sale_amt = 0; //  + (float)$btw_pay[$vcode] - (float)$btw_rec[$vcode]
                        $current_sale_amt = (float)$between_sales_amt[$vcode] + (float)$between_ccn[$vcode] + (float)$between_cntcr[$vcode]  ;

                        /*Between days Total Receipt */
                        $current_rct_amt = 0;
                        $current_rct_amt = (((float)$between_rct_amt[$vcode] + (float)$between_cdn[$vcode] + (float)$between_cus_ireturn_amt[$vcode] + (float)$between_cntdr[$vcode] + (float)$between_vdr[$vcode] ) - (float)$between_vcr[$vcode] );

                        /*Between Days Balance Amount */
                        $current_Balance_amt = 0;
                        if(number_format_ind(round($current_sale_amt,5)) == number_format_ind(round($current_rct_amt,5))){ $current_Balance_amt = 0; }
                        else{ $current_Balance_amt = (float)$current_sale_amt - (float)$current_rct_amt; }

                        /*Final Supplier Balance */
                        $final_supplier_balance_amt = 0;
                        $final_supplier_balance_amt = (float)$current_opening_amt + (float)$current_Balance_amt; 

                        /*if($vendor_name[$vcode] == "VETKING"){
                            if($_SERVER['REMOTE_ADDR'] == "49.205.134.69"){
                                echo "<br/>Sale: ".$opening_sales[$vcode];
                                echo "<br/>Credit Note: ".$opening_ccn[$vcode];
                                echo "<br/>Contra Cr: ".$opening_cntcr[$vcode];
                                echo "<br/>Voucher Cr: ".$opening_vcr[$vcode];
                                echo "<br/>OB Dr: ".$ob_dramt;
                                echo "<br/>Receipt: ".$opening_receipts[$vcode];
                                echo "<br/>Sale Return: ".$opening_returns[$vcode];
                                echo "<br/>Debit Note: ".$opening_cdn[$vcode];
                                echo "<br/>Contra Dr: ".$opening_cntdr[$vcode];
                                echo "<br/>Voucher Dr: ".$opening_vdr[$vcode];
                                echo "<br/>OB Cr: ".$ob_cramt;
                            }        
                        }*/
                        if(number_format_ind($final_supplier_balance_amt) != "0.00"){
                            echo "<tr>";
                            echo "<td>".$vendor_name[$vcode]."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_opening_amt)."</td>";
                            //echo "<td style='text-align:right;'>".str_replace(".00","",number_format_ind($current_sale_birds))."</td>";
                            //echo "<td style='text-align:right;'>".number_format_ind($current_sale_weight)."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_sale_amt)."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_rct_amt)."</td>";
                            echo "<td style='text-align:right;'>".number_format_ind($current_Balance_amt)."</td>";
                            if((float)$final_supplier_balance_amt < 0){
                                echo "<td style='text-align:right;'></td>";
                                echo "<td style='text-align:right;'>".str_replace("-","",number_format_ind($final_supplier_balance_amt))."</td>";
                                $tot_dr_amt += (float)$final_supplier_balance_amt;
                            }
                            else{
                                echo "<td style='text-align:right;'>".number_format_ind($final_supplier_balance_amt)."</td>";
                                echo "<td style='text-align:right;'></td>";
                                $tot_cr_amt += (float)$final_supplier_balance_amt;
                            }
                            
                            //echo "<td style='text-align:right;'>".number_format_ind($final_supplier_balance_amt)."</td>";
                            echo "<td style='text-align:right;'>".round($pay_gapdays)."</td>";
                            echo "</tr>";

                            /*Final Total */
                            $final_opening_amt += (float)$current_opening_amt;
                            $final_between_sale_birds += (float)$current_sale_birds;
                            $final_between_sale_weight += (float)$current_sale_weight;
                            $final_between_sale_amt += (float)$current_sale_amt;
                            $final_between_rct_amt += (float)$current_rct_amt;
                            $final_between_balance_amt += (float)$current_Balance_amt;
                            $final_all_supplier_balance_amt += (float)$final_supplier_balance_amt;
                        }
                    }
                    echo "<tr class='thead3'>";
					echo "<td style='text-align:center;font-weight:bold;' colspan='1'>Total</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_opening_amt)."</td>";
                    //echo "<td style='text-align:right;font-weight:bold;'>".str_replace(".00","",number_format_ind($final_between_sale_birds))."</td>";
                    //echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_sale_weight)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_sale_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_rct_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_balance_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".str_replace("-","",number_format_ind($tot_cr_amt))."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".str_replace("-","",number_format_ind($tot_dr_amt))."</td>";
                    //echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_all_supplier_balance_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'></td>";
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