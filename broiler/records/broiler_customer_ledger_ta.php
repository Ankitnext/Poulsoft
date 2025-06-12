<?php
//broiler_customer_ledger_ta.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
$user_code = $_SESSION['userid'];
include "header_head.php";

$sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
// removed due to the ticket no 2023-0226 on 23-06-2023
//$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank')";
$sql = "SELECT * FROM `acc_coa` "; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_modes`"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){  $group_access_code = $row['cgroup_access']; }
if($group_access_code == "all" || $group_access_code == ""){ $group_access_filter1 = ""; } else{ $group_access_list = implode("','", explode(",",$group_access_code)); $group_access_filter1 = " AND `code` IN ('$group_access_list')"; $group_access_filter2 = " AND `groupcode` IN ('$group_access_list')"; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ".$group_access_filter2." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $obdate[$row['code']] = $row['obdate']; $obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt']; }

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){  $coa_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $vendors = "all"; $excel_type = "display";
if(isset($_GET['fdate']) && isset($_GET['tdate']) && isset($_GET['vendors'])){
    $fdate = $_GET['fdate'];
    $tdate = $_GET['tdate'];
    $vendors = $_GET['vendors'];
}
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/CustomerHistoyReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors;
}

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
                    <th colspan="15" align="center"><?php echo $row['cdetails']; ?><h5>Customer History Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_customer_ledger_ta.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="17">
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
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $vcode){ if($vendor_name[$vcode] != ""){ ?>
                                        <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $vendor_name[$vcode]; ?></option>
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
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:93px;"'; } ?>>Date</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Trnum</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:70px;"'; } ?>>Doc. No.</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Type</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Item</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Birds</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Quantity</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Avg.Weight</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Rate</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Amount</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Debit</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Credit</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Balance</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Sector</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Vehicle</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Remarks</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Over Due By Days</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true || (isset($_GET['fdate']) && isset($_GET['tdate']) && isset($_GET['vendors']))){
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = ""; $opening_sales = $opening_receipts = $opening_ccn = $opening_cdn = $opening_cntcr = $opening_cntdr = $opening_returns = $rb_amt = $odcr_amt = $oddr_amt = 0;
                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $opening_sales += (double)$row['finl_amt']; $oddr_amt += (double)$row['finl_amt']; $old_inv = $row['trnum']; } } }

                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` < '$fdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_receipts += (double)$row['amount']; $odcr_amt += (double)$row['amount']; } }

                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_returns += (double)$row['amount']; $odcr_amt += (double)$row['amount']; } }
                    
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $opening_ccn += (double)$row['amount']; $odcr_amt += (double)$row['amount']; } else{ $opening_cdn += (double)$row['amount']; $oddr_amt += (double)$row['amount']; } } }

                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntcr += (double)$row['amount']; $odcr_amt += (double)$row['amount']; } }

                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntdr += (double)$row['amount']; $oddr_amt += (double)$row['amount']; } }
                    
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` < '$fdate' AND `vcode` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_vcr += (double)$row['cr_amt']; $odcr_amt += (double)$row['cr_amt']; $opening_vdr += (double)$row['dr_amt']; $oddr_amt += (double)$row['dr_amt']; } }

                    $ob_cramt = $ob_cramt = 0;
                    if($obtype[$vendors] == "Cr"){ $ob_cramt = $obamt[$vendors]; $ob_dramt = 0; } else{ $ob_dramt = $obamt[$vendors]; $ob_cramt = 0; }

                    $ob_rcv = $opening_sales + $opening_cdn + $opening_cntdr + $opening_vdr + $ob_dramt;
					$ob_pid = $opening_receipts + $opening_returns + $opening_ccn + $opening_cntcr + $opening_vcr + $ob_cramt;
                    
                    if($_SERVER['REMOTE_ADDR'] == "49.205.134.69"){
                        echo "<br/>Sale: ".$opening_sales;
                        echo "<br/>Debit Note: ".$opening_cdn;
                        echo "<br/>Contra Dr: ".$opening_cntdr;
                        echo "<br/>Voucher Dr: ".$opening_vdr;
                        echo "<br/>OB Dr: ".$ob_dramt;
                        echo "<br/>Receipt: ".$opening_receipts;
                        echo "<br/>Sale Return: ".$opening_returns;
                        echo "<br/>Credit Note: ".$opening_ccn;
                        echo "<br/>Contra Cr: ".$opening_cntcr;
                        echo "<br/>Voucher Cr: ".$opening_vcr;
                        echo "<br/>OB Cr: ".$ob_cramt;
                        echo "<br/>$ob_rcv = $opening_sales + $opening_cdn + $opening_cntdr + $opening_vdr + $ob_dramt;<br/>";
                        echo "<br/>$ob_pid = $opening_receipts + $opening_returns + $opening_ccn + $opening_cntcr + $opening_vcr + $ob_cramt;<br/>";
                    }

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='9' style='font-weight:bold;'>Previous Balance</td>";
                    if($ob_rcv >= $ob_pid){
                        echo "<td></td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_rcv - $ob_pid)."</td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_rcv - $ob_pid)."</td>";
                        $rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
						$ob_rev_amt = $ob_rcv - $ob_pid;
						$ob_pid_amt = 0;
                    }
                    else{
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_pid - $ob_rcv)."</td>";
                        echo "<td></td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_rcv - $ob_pid)."</td>";
                        $rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
						$ob_pid_amt = $ob_pid - $ob_rcv;
						$ob_rev_amt = 0;
                    }
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    $key_code = "";
                    $sale_info = $receipt_info = $return_info = $ccn_info = $cdn_info = $inv_count = $contra_cr = $contra_dr = $between_vcr = $between_vdr = array();

                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $sale_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['billno']."@".$row['icode']."@".$row['birds']."@".$row['snt_qty']."@".$row['rcd_qty']."@".$row['fre_qty']."@".$row['rate']."@".$row['dis_per']."@".$row['dis_amt']."@".$row['gst_per']."@".$row['gst_amt']."@".$row['tcds_per']."@".$row['tcds_amt']."@".$row['item_tamt']."@".$row['freight_type']."@".$row['freight_amt']."@".$row['freight_pay_type']."@".$row['freight_pay_acc']."@".$row['freight_acc']."@".$row['round_off']."@".$row['finl_amt']."@".$row['bal_qty']."@".$row['bal_amt']."@".$row['remarks']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['supervisor_code']."@".$row['bag_code']."@".$row['bag_count']."@".$row['batch_no']."@".$row['exp_date']."@".$row['vehicle_code']."@".$row['driver_code']."@".$row['sale_type']."@".$row['active']."@".$row['flag']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['mob_flag'];
                            if(!empty($inv_count[$row['trnum']])){ $inv_count[$row['trnum']] = $inv_count[$row['trnum']] + 1; } else{ $inv_count[$row['trnum']] = 1; }
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $receipt_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['sms_sent']."@".$row['whapp_sent']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['c10']."@".$row['c20']."@".$row['c50']."@".$row['c100']."@".$row['c500']."@".$row['c2000']."@".$row['ccoins']."@".$row['c200'];
                            $odcr_amt += (double)$row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $return_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['inv_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['rtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                            $odcr_amt += (double)$row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){
                                $i++; $key_code = $row['date']."@".$i;
                                $ccn_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['crdr']."@".$row['date']."@".$row['vcode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['amtinwords']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                                $odcr_amt += (double)$row['amount'];
                            }
                            else{
                                $j++; $key_code = $row['date']."@".$j;
                                $cdn_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['crdr']."@".$row['date']."@".$row['vcode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['amtinwords']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                            }
                        }
                    }

                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $contra_cr[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['dcno']."@".$row['fcoa']."@".$row['tcoa']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                            $odcr_amt += (double)$row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $contra_dr[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['dcno']."@".$row['fcoa']."@".$row['tcoa']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                        }
                    }

                    $between_vcr = $between_vdr = $to_trnums = $from_trnums = $to_coa_name = $from_coa_name = array();
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Cr"){
                                $i++; $key_code = $row['date']."@".$i;
                                $between_vcr[$key_code] = $row['trnum']."@".$row['date']."@".$row['dcno']."@".$row['group_code']."@".$row['vcode']."@".$row['amount']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['remarks'];
                                $to_trnums[$row['trnum']] = $row['trnum'];
                                $odcr_amt += (double)$row['amount'];
                            }
                            else if($row['crdr'] == "Dr"){
                                $j++; $key_code = $row['date']."@".$j;
                                $between_vdr[$key_code] = $row['trnum']."@".$row['date']."@".$row['dcno']."@".$row['group_code']."@".$row['vcode']."@".$row['amount']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['remarks'];
                                $from_trnums[$row['trnum']] = $row['trnum'];
                            }
                        }
                    }
                    $trno_list = implode("','",$to_trnums);
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trnum` IN ('$trno_list') AND `crdr` = 'Dr' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $to_coa_name[$row['trnum']] = $coa_name[$row['vcode']]; } }

                    $trno_list = implode("','",$from_trnums);
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trnum` IN ('$trno_list') AND `crdr` = 'Cr' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $from_coa_name[$row['trnum']] = $coa_name[$row['vcode']]; } }

                    //Over Due Calculations for Opening Balances
                    $odcr_amt = round(((double)$odcr_amt - (double)$oddr_amt),5);

                    $sale_ccount = sizeof($sale_info);
                    $receipt_ccount = sizeof($receipt_info);
                    $return_ccount = sizeof($return_info);
                    $ccn_ccount = sizeof($ccn_info);
                    $cdn_ccount = sizeof($cdn_info);
                    $cdr_ccount = sizeof($contra_dr);
                    $ccr_ccount = sizeof($contra_cr);
                    $vcr_ccount = sizeof($between_vcr);
                    $vdr_ccount = sizeof($between_vdr);

                    $exist_inv = $old_inv1 = ""; $bt_sale_amt = $bt_rct_amt = 0; $today = date("Y-m-d");
                    for ($currentDate = strtotime($fdate); $currentDate <= strtotime($tdate); $currentDate += (86400)) {
                        $date_asc = date('Y-m-d', $currentDate);

                        // Sale Entries
                        for($i = 0;$i <=$sale_ccount;$i++){
                            if(!empty($sale_info[$date_asc."@".$i])){
                                $sales_details = explode("@",$sale_info[$date_asc."@".$i]);
                                
                                if($old_inv1 != $sales_details[2]){
                                    $old_inv1 = $sales_details[2];
                                    $odue_days = 0;
                                    //Over Due Calculations for between days calculations
                                    $odcr_amt = round(((double)$odcr_amt - (double)$sales_details[25]),5);
                                    if((double)$odcr_amt < 0){
                                        $odue_days = ((strtotime($today) - strtotime($sales_details[3])) / 60 / 60 / 24);
                                    }
                                }

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($sales_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$sales_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$sales_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Sales Invoice</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$sales_details[4]]."</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$sales_details[6]]."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[7])."</td>";
                                $tot_birds = $tot_birds + (double)$sales_details[7];
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[9])."</td>";
                                $tot_qty = $tot_qty + (double)$sales_details[9];
                                if($sales_details[7] > 0){
                                    echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[9]/$sales_details[7])."</td>";
                                }
                                else{
                                    echo "<td style='width:110px;text-align:right;'>".number_format_ind(0)."</td>";
                                }
                                
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[11])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[18])."</td>";
                                if($exist_inv != $sales_details[2]){
                                    $exist_inv = $sales_details[2];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$sales_details[2]].">".number_format_ind(0.00)."</td>";
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$sales_details[2]].">".number_format_ind($sales_details[25])."</td>";
                                    $bt_sale_amt = $bt_sale_amt + $sales_details[25];
                                    $ob_rev_amt = $ob_rev_amt + $sales_details[25];
                                    $rb_amt = $rb_amt + $sales_details[25];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$sales_details[2]].">".number_format_ind($rb_amt)."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$sales_details[2]].">".$sector_name[$sales_details[29]]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$sales_details[2]].">".$sales_details[36]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$sales_details[2]].">".$sales_details[28]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$sales_details[2]].">".str_replace(".00","",number_format_ind($odue_days))."</td>";
                                }
                                echo "</tr>";
                            }
                        }

                        // Receipt Entries
                        for($i = 0;$i <=$receipt_ccount;$i++){
                            if(!empty($receipt_info[$date_asc."@".$i])){
                                $receipt_details = explode("@",$receipt_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($receipt_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$receipt_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$receipt_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Receipts</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$receipt_details[4]]."</td>";
								echo "<td style='width:130px;text-align:left;'>".$mode_name[$receipt_details[6]]."-".$coa_name[$receipt_details[7]]."</td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($receipt_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($receipt_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_rct_amt = $bt_rct_amt + $receipt_details[8];
                                $ob_pid_amt = $ob_pid_amt + $receipt_details[8];
                                $rb_amt = $rb_amt - $receipt_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$receipt_details[11]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$receipt_details[12]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // Return Entries
                        for($i = 0;$i <=$return_ccount;$i++){
                            if(!empty($return_info[$date_asc."@".$i])){
                                $return_details = explode("@",$return_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($return_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$return_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$return_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Sales Return</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$return_details[6]]."</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$return_details[7]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'>".$return_details[9]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[10]."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                $bt_rct_amt = $bt_rct_amt + $return_details[11];
                                $ob_pid_amt = $ob_pid_amt + $return_details[11];
                                $rb_amt = $rb_amt - $return_details[11];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$return_details[13]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$return_details[14]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // CCN Entries
                        for($i = 0;$i <=$ccn_ccount;$i++){
                            if(!empty($ccn_info[$date_asc."@".$i])){
                                $ccn_details = explode("@",$ccn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($ccn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$ccn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$ccn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Customer Credit Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$ccn_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$ccn_details[7]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($ccn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($ccn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_rct_amt = $bt_rct_amt + $ccn_details[9];
                                $ob_pid_amt = $ob_pid_amt + $ccn_details[9];
                                $rb_amt = $rb_amt - $ccn_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$ccn_details[11]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$ccn_details[12]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // CDN Entries
                        for($i = 0;$i <=$cdn_ccount;$i++){
                            if(!empty($cdn_info[$date_asc."@".$i])){
                                $odue_days = 0;
                                //Over Due Calculations for between days calculations
                                $odcr_amt = round(((double)$odcr_amt - (double)$cdn_details[9]),5);
                                if((double)$odcr_amt <= 0){ $odue_days = ((strtotime($today) - strtotime($cdn_details[4])) / 60 / 60 / 24); }

                                $cdn_details = explode("@",$cdn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($cdn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$cdn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$cdn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Customer Debit Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$cdn_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$cdn_details[7]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($cdn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($cdn_details[9])."</td>";
                                $bt_sale_amt = $bt_sale_amt + $cdn_details[9];
                                $ob_rev_amt = $ob_rev_amt + $cdn_details[9];
                                $rb_amt = $rb_amt + $cdn_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$cdn_details[11]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$cdn_details[12]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".str_replace(".00","",number_format_ind($odue_days))."</td>";
                                echo "</tr>";
                            }
                        }
                        
                        // Contra CR Note Entries
                        for($i = 0;$i <=$ccr_ccount;$i++){
                            if(!empty($contra_cr[$date_asc."@".$i])){
                                $ccr_details = explode("@",$contra_cr[$date_asc."@".$i]);

                                if($coa_name[$ccr_details[7]] == ''){
                                    $coa_name = $coa_name[$ccr_details[7]];
                                }else{
                                    $coa_name = $coa_name[$ccr_details[7]];
                                }
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($ccr_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$ccr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$ccr_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Contra Cr Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$ccr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($ccr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($ccr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_rct_amt = $bt_rct_amt + $ccr_details[8];
                                $ob_pid_amt = $ob_pid_amt + $ccr_details[8];
                                $rb_amt = $rb_amt - $ccr_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$ccr_details[9]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$ccr_details[10]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // Contra DR Note Entries
                        for($i = 0;$i <=$cdr_ccount;$i++){
                            if(!empty($contra_dr[$date_asc."@".$i])){
                                $odue_days = 0;
                                //Over Due Calculations for between days calculations
                                $odcr_amt = round(((double)$odcr_amt - (double)$cdr_details[8]),5);
                                if((double)$odcr_amt <= 0){ $odue_days = ((strtotime($today) - strtotime($cdr_details[4])) / 60 / 60 / 24); }

                                $cdr_details = explode("@",$contra_dr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($cdr_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$cdr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$cdr_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Contra Dr Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$cdr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$cdr_details[6]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($cdr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($cdr_details[8])."</td>";
                                $bt_sale_amt = $bt_sale_amt + $cdr_details[8];
                                $ob_rev_amt = $ob_rev_amt + $cdr_details[8];
                                $rb_amt = $rb_amt + $cdr_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$cdr_details[9]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$cdr_details[10]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".str_replace(".00","",number_format_ind($odue_days))."</td>";
                                echo "</tr>";
                            }
                        }
                        //Voucher CR Note Entries
                        for($i = 0;$i <=$vcr_ccount;$i++){
                            if(!empty($between_vcr[$date_asc."@".$i])){
                                $vcr_details = explode("@",$between_vcr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($vcr_details[1]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$vcr_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$vcr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>Receipt Voucher</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$vcr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$to_coa_name[$vcr_details[0]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($vcr_details[5])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($vcr_details[5])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_rct_amt = $bt_rct_amt + $vcr_details[5];
                                $ob_pid_amt = $ob_pid_amt + $vcr_details[5];
                                $rb_amt = $rb_amt - $vcr_details[5];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$vcr_details[6]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$vcr_details[8]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        //Voucher DR Note Entries
                        for($i = 0;$i <=$vdr_ccount;$i++){
                            if(!empty($between_vdr[$date_asc."@".$i])){
                                $odue_days = 0;
                                //Over Due Calculations for between days calculations
                                $odcr_amt = round(((double)$odcr_amt - (double)$vdr_details[5]),5);
                                if((double)$odcr_amt <= 0){ $odue_days = ((strtotime($today) - strtotime($vdr_details[1])) / 60 / 60 / 24); }

                                $vdr_details = explode("@",$between_vdr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($vdr_details[1]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$vdr_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$vdr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>Payment Voucher</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$vdr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$from_coa_name[$vdr_details[0]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($vdr_details[5])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($vdr_details[5])."</td>";
                                $bt_sale_amt = $bt_sale_amt + $vdr_details[5];
                                $ob_rev_amt = $ob_rev_amt + $vdr_details[5];
                                $rb_amt = $rb_amt + $vdr_details[5];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$vdr_details[6]]."</td>";
                                echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$vdr_details[8]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".str_replace(".00","",number_format_ind($odue_days))."</td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='5'>Between Dates Total</td>";
					echo "<td style='width:130px;text-align:right;font-weight:bold;'>".number_format_ind($tot_birds)."</td>";
					echo "<td style='width:110px;text-align:right;font-weight:bold;'>".number_format_ind($tot_qty)."</td>";
					echo "<td style='width:110px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_rct_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_sale_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='5'>Closing Total</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_pid_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_rev_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='5'>Outstanding</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:110px;text-align:right;'></td>";
                    if($ob_rev_amt >= $ob_pid_amt){
                        echo "<td style='width:100px;text-align:right;'></td>";
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_rev_amt - $ob_pid_amt)."</td>";
                    }
                    else{
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_pid_amt - $ob_rev_amt)."</td>";
                        echo "<td style='width:100px;text-align:right;'></td>";
                    }
                    
                    echo "<td style='width:100px;text-align:right;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
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