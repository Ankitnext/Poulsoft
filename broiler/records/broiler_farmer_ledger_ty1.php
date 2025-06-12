<?php
//broiler_supplier_ledger.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }


/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("account_contranotes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.account_contranotes LIKE poulso6_admin_broiler_broilermaster.account_contranotes;"; mysqli_query($conn,$sql1); }
if(in_array("master_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_payments LIKE poulso6_admin_broiler_broilermaster.master_payments;"; mysqli_query($conn,$sql1); }
if(in_array("master_receipts", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_receipts LIKE poulso6_admin_broiler_broilermaster.master_receipts;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `broiler_farmer`  WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['code']; $farmer_panno[$row['code']] = $row['panno']; }
$farmer_pann = implode("','",$farmer_panno);

$sql = "SELECT * FROM `broiler_farm`  WHERE 1 ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." order by description ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $farmer_code[$row['code']] = $row['farmer_code']; $farmer_pan[$row['code']] = $farmer_panno[$row['farmer_code']]; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank')"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_modes`"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $obdate[$row['code']] = $row['obdate']; $obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }


$export_fdate = $_POST['fdate'];
$export_tdate = $_POST['tdate'];
$export_vendors = $sector_name[$_POST['vendors']]; if ( $export_vendors == "") {  $export_vendors = "All"; }

$fdate = $tdate = date("Y-m-d"); $vendors = "select"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $farmer_pann = $farmer_pan[$vendors]; 

	//$excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/SupplierHistoyReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors;
    if ($export_fdate == $export_tdate)
    {$filename = "Farmer History_".$export_tdate; }
    else {
    $filename = "Farmer History_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];

}
else{
$url = "";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="5" align="center">
                        <?php echo $row['cdetails']; ?>
                        <h5>Farmer History Report</h5>
                        <?php
                        if($export_vendors == "" || $export_vendors == "All" || $export_vendors == "all"){ }
                        else{
                            if($farmer_pann == ""){
                                echo "<h5>Farmer Name: ".$export_vendors."</h5>";
                            } else {
                                echo "<h5>Farmer Name: ".$export_vendors." ( Pan No : ".$farmer_pann." )</h5>";
                            }
                        }
                        ?>
                    </th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_farmer_ledger_ty1.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="7">
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
                                    <label>Farmer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="select" <?php if($vendors == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($sector_code as $vcode){ if($sector_name[$vcode] != ""){ ?>
                                        <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $sector_name[$vcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('main_body', 'Farmer History','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                <table>
            </form>
            <table id="main_body" class="tbl" align="center"  style="width:1300px;">
            
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                    <th colspan="7" align="center"><?php echo $row['cdetails']; ?><h5>Farmer History Report</h5></th>
                </tr>
            <?php } ?>

            <tr>
                       
                       <th colspan="7">
                                   <div class="row">
                                       <div class="m-2 form-group">
                                           <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Farmer: <?php echo $export_vendors; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label><br/></label>
                   
                                       </div>
                                       
                               </th>
                           
                       </tr>
       
                       </thead>
            
            <br/>
            <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            
            </div>
            
            <thead class="thead3" align="center">
                <tr align="center">
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:93px;"'; } ?>>Date</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Type</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Trnum</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Remarks</th>
                   
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Dr Amount</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Cr Amount</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Balance</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = ""; $opening_purchases = $opening_tds = $opening_oded = $opening_receipts = $opening_payments = $opening_scn = $opening_sdn = $opening_returns = $rb_amt = 0;
                    $sql_record = "SELECT * FROM `broiler_rearingcharge` WHERE `date` < '$fdate' AND `farm_code` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                $opening_purchases = $opening_purchases + $row['amount_payable'];
                                $opening_tds = $opening_tds + $row['tds_amt'];
                                $opening_oded = $opening_oded + $row['other_deduction'];
                                
                            }
                            $old_inv = $row['trnum'];
                        }
                    }
                    else{
                        $opening_purchases = 0;
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_payments` WHERE `date` < '$fdate' AND `ccode` = '$farmer_code[$vendors]' AND `warehouse` = '$vendors' AND `vtype` IN ('FarmerPay') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['pay_type'] == 'gc_pay' || $row['pay_type'] == '' ){
                                $opening_payments = $opening_payments + $row['amount'];
                            }else{
                                $opening_advance_payments = $opening_advance_payments + $row['amount'];
                            }
                            
                            
                        }
                    }
                    else{
                        $opening_payments = 0;
                    }

                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` < '$fdate' AND `ccode` = '$farmer_code[$vendors]' AND `vtype` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_receipts = $opening_receipts + $row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` < '$fdate' AND `to_account` = '$vendors' AND `t_type` IN ('Farmer Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_receipts = $opening_receipts + $row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` < '$fdate' AND `to_account` = '$vendors' AND `t_type` IN ('Farmer Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_payments = $opening_payments + $row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_payments = $opening_payments + $row['amount'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` < '$fdate' AND (`warehouse` = '$vendors' OR `vcode` = '$vendors') AND (`sale_type` IN ('FarmerSale') OR `sale_type` IN ('FormMBSale') OR `sale_type` IN ('FeedSingleSale')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; //echo $sql_record;
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                $opening_salesamount = $opening_salesamount + $row['finl_amt'];
                                
                            }
                            $old_inv = $row['trnum'];
                            
                        }
                    }
                    else{
                        $opening_salesamount = 0;
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `warehouse` = '$vendors' AND `type` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_returns = $opening_returns + $row['amount'];
                        }
                    }
                    else{
                        $opening_returns = 0;
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` = '$farmer_code[$vendors]' AND `warehouse` = '$vendors' AND `type` IN ('Farmer') AND `crdr` IN ('Debit','Credit') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){ $opening_scn = $opening_scn + $row['amount']; } else{ $opening_sdn = $opening_sdn + $row['amount']; }
                        }
                    }
                    else{
                        $opening_scn = $opening_sdn = 0;
                    }

                    $ob_cramt = $ob_cramt = 0;
                    if($obtype[$vendors] == "Cr"){ $ob_cramt = $obamt[$vendors]; $ob_dramt = 0; } else{ $ob_dramt = $obamt[$vendors]; $ob_cramt = 0; }


                    $ob_rcv = $opening_purchases + $opening_scn + $ob_dramt + $opening_advance_payments +  $opening_receipts ;
					$ob_pid = $opening_payments + $opening_returns + $opening_sdn + $ob_cramt + $opening_tds + $opening_oded +  $opening_salesamount;

                    //echo $opening_purchases ."//". $opening_scn ."//". $ob_dramt ."----". $opening_payments ."//". $opening_returns ."//". $opening_sdn ."//". $ob_cramt;

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='3' style='font-weight:bold;'>Previous Balance</td>";
                    if($ob_rcv >= $ob_pid){
                      
                   
                        echo "<td style='font-weight:bold;text-align:right;'>".""."</td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_rcv - $ob_pid)."</td>";
                        $rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
						$ob_rev_amt = $ob_rcv - $ob_pid;
						$ob_pid_amt = 0;
                        $bal  = $ob_rcv - $ob_pid;
                        $prev_bal_cr  = $ob_rcv - $ob_pid;
                        $type = "Cr";
                    }
                    else{
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_pid - $ob_rcv)."</td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".""."</td>";
                        
                        $rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
						$ob_pid_amt = $ob_pid - $ob_rcv;
						$ob_rev_amt = 0;
                        $bal  = $ob_pid - $ob_rcv;
                        $prev_bal_dr  = $ob_pid - $ob_rcv;
                        $type = "Dr";
                    }
                    echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($bal)."(".$type.")"."</td>";
                    echo "</tr>";

                    $key_code = "";
                    $purchase_info = $payment_info = $return_info = $scn_info = $sdn_info = $inv_count = $tds_info = $other_ded = array();
                    $sql_record = "SELECT * FROM `broiler_rearingcharge` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `farm_code` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            $purchase_info[$key_code] = $row['date']."[@$&]"."GC"."[@$&]".$row['trnum']."[@$&]".$row['amount_payable']."[@$&]"."0"."[@$&]".$row['amount_payable'];
                            $tds_info[$key_code] = $row['date']."[@$&]"."TDS"."[@$&]".$row['trnum']."[@$&]"."0"."[@$&]".$row['tds_amt']."[@$&]".$row['tds_amt'];
                            $other_ded[$key_code] = $row['date']."[@$&]"."Other Deduction"."[@$&]".$row['trnum']."[@$&]"."0"."[@$&]".$row['other_deduction']."[@$&]".$row['other_deduction'];
                            if(!empty($inv_count[$row['trnum']])){
                                $inv_count[$row['trnum']] = $inv_count[$row['trnum']] + 1;
                            }
                            else{
                                $inv_count[$row['trnum']] = 1;
                            }
                        }
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$farmer_code[$vendors]' AND `warehouse` = '$vendors' AND `vtype` IN ('FarmerPay') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); } $payment_info = array();
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            
                            /*if($row['pay_type'] == 'gc_pay' || $row['pay_type'] == '' ){
                                $payment_info[$key_code] = $row['date']."[@$&]"."Payments"."[@$&]".$row['trnum']."[@$&]"."0"."[@$&]".$row['amount']."[@$&]".$row['remarks']."[@$&]".$row['pay_type'];
                            }else{
                                $payment_info[$key_code] = $row['date']."[@$&]"."Advance Payments"."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks']."[@$&]".$row['pay_type'];
                            }*/
                            
                            if($row['pay_type'] == 'gc_pay' || $row['pay_type'] == '' ){
                                $payment_info[$key_code] = $row['date']."[@$&]"."Payments"."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks']."[@$&]".$row['pay_type'];
                            }else{
                                $payment_info[$key_code] = $row['date']."[@$&]"."Advance Payments"."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks']."[@$&]".$row['pay_type'];
                            }
                            
                            
                          //  $payment_info[$key_code] = $row['incr']."[@$&]".$row['prefix']."[@$&]".$row['trnum']."[@$&]".$row['date']."[@$&]".$row['ccode']."[@$&]".$row['docno']."[@$&]".$row['mode']."[@$&]".$row['method']."[@$&]".$row['amount']."[@$&]".$row['amtinwords']."[@$&]".$row['vtype']."[@$&]".$row['warehouse']."[@$&]".$row['remarks']."[@$&]".$row['sms_sent']."[@$&]".$row['whapp_sent']."[@$&]".$row['flag']."[@$&]".$row['active']."[@$&]".$row['dflag']."[@$&]".$row['addedemp']."[@$&]".$row['addedtime']."[@$&]".$row['updatedemp']."[@$&]".$row['updatedtime']."[@$&]".$row['c10']."[@$&]".$row['c20']."[@$&]".$row['c50']."[@$&]".$row['c100']."[@$&]".$row['c500']."[@$&]".$row['c2000']."[@$&]".$row['ccoins']."[@$&]".$row['c200'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` = '$vendors' AND `t_type` IN ('Farmer Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            $payment_info[$key_code] = $row['date']."[@$&]".$row['t_type']."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks'];
                        }
                    }

                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; //echo $sql_record;
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            $payment_info[$key_code] = $row['date']."[@$&]Journal[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks'];
                        }
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$farmer_code[$vendors]' AND `vtype` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            $receipt_info[$key_code] = $row['date']."[@$&]"."Receipt"."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks'];
                          //  $payment_info[$key_code] = $row['incr']."[@$&]".$row['prefix']."[@$&]".$row['trnum']."[@$&]".$row['date']."[@$&]".$row['ccode']."[@$&]".$row['docno']."[@$&]".$row['mode']."[@$&]".$row['method']."[@$&]".$row['amount']."[@$&]".$row['amtinwords']."[@$&]".$row['vtype']."[@$&]".$row['warehouse']."[@$&]".$row['remarks']."[@$&]".$row['sms_sent']."[@$&]".$row['whapp_sent']."[@$&]".$row['flag']."[@$&]".$row['active']."[@$&]".$row['dflag']."[@$&]".$row['addedemp']."[@$&]".$row['addedtime']."[@$&]".$row['updatedemp']."[@$&]".$row['updatedtime']."[@$&]".$row['c10']."[@$&]".$row['c20']."[@$&]".$row['c50']."[@$&]".$row['c100']."[@$&]".$row['c500']."[@$&]".$row['c2000']."[@$&]".$row['ccoins']."[@$&]".$row['c200'];
                        }
                    }
                    else{
                        $receipt_info = array();
                    }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` = '$vendors' AND `t_type` IN ('Farmer Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            $receipt_info[$key_code] = $row['date']."[@$&]"."Receipt"."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks'];
                        }
                    }

                     $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Birds%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = ""; $broiler_acodes = array();
                    while($row = mysqli_fetch_assoc($query)){  $broiler_acodes[$row['code']] = $row['code']; }
                    
                    $broiler_codes = implode("','",$broiler_acodes);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$broiler_codes') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_name1 = $item_code1 = $broiler_bds = array(); 
                    while($row = mysqli_fetch_assoc($query)){ $item_code1[$row['code']] = $row['code']; $item_name1[$row['code']] = $row['description']; $broiler_bds[] = $row['code']; }
		            $broiler_bds_str = "'" . implode("','", $broiler_bds) . "'";

                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND (`vcode` = '$vendors' OR `warehouse` = '$vendors') AND (`sale_type` IN ('FarmerSale') OR `sale_type` IN ('FormMBSale') OR `sale_type` IN ('FeedSingleSale')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; 
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                $i++; $key_code = $row['date']."[@$&]".$i;
                                $sales_type = in_array($row['icode'], $broiler_bds) ? "Birds Sales" : "Other Sales";
                                $sales_info[$key_code] = $row['date']."[@$&]".$sales_type."[@$&]".$row['trnum']."[@$&]"."0"."[@$&]".$row['finl_amt']."[@$&]".$row['remarks'];
                                
                            }
                            $old_inv = $row['trnum'];
                           
                          //  $payment_info[$key_code] = $row['incr']."[@$&]".$row['prefix']."[@$&]".$row['trnum']."[@$&]".$row['date']."[@$&]".$row['ccode']."[@$&]".$row['docno']."[@$&]".$row['mode']."[@$&]".$row['method']."[@$&]".$row['amount']."[@$&]".$row['amtinwords']."[@$&]".$row['vtype']."[@$&]".$row['warehouse']."[@$&]".$row['remarks']."[@$&]".$row['sms_sent']."[@$&]".$row['whapp_sent']."[@$&]".$row['flag']."[@$&]".$row['active']."[@$&]".$row['dflag']."[@$&]".$row['addedemp']."[@$&]".$row['addedtime']."[@$&]".$row['updatedemp']."[@$&]".$row['updatedtime']."[@$&]".$row['c10']."[@$&]".$row['c20']."[@$&]".$row['c50']."[@$&]".$row['c100']."[@$&]".$row['c500']."[@$&]".$row['c2000']."[@$&]".$row['ccoins']."[@$&]".$row['c200'];
                        }
                    }
                    else{
                        $sales_info = array();
                    }
                   // print_r($sales_info);
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `warehouse` = '$vendors' AND `type` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."[@$&]".$i;
                            $return_info[$key_code] = $row['incr']."[@$&]".$row['prefix']."[@$&]".$row['trnum']."[@$&]".$row['type']."[@$&]".$row['date']."[@$&]".$row['inv_trnum']."[@$&]".$row['vcode']."[@$&]".$row['itemcode']."[@$&]".$row['birds']."[@$&]".$row['quantity']."[@$&]".$row['price']."[@$&]".$row['amount']."[@$&]".$row['rtype']."[@$&]".$row['warehouse']."[@$&]".$row['remarks']."[@$&]".$row['flag']."[@$&]".$row['active']."[@$&]".$row['dflag']."[@$&]".$row['addedemp']."[@$&]".$row['addedtime']."[@$&]".$row['updatedemp']."[@$&]".$row['updatedtime'];
                        }
                    }
                    else{
                        $return_info = array();
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$farmer_code[$vendors]' AND `warehouse` = '$vendors' AND `type` IN ('Farmer') AND `crdr` IN ('Debit','Credit') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){
                                $i++; $key_code = $row['date']."[@$&]".$i;
                                $scn_info[$key_code] = $row['date']."[@$&]"."Farmer Credit Note"."[@$&]".$row['trnum']."[@$&]".$row['amount']."[@$&]"."0"."[@$&]".$row['remarks'];
                               // $scn_info[$key_code] = $row['incr']."[@$&]".$row['prefix']."[@$&]".$row['trnum']."[@$&]".$row['type']."[@$&]".$row['date']."[@$&]".$row['vcode']."[@$&]".$row['docno']."[@$&]".$row['coa']."[@$&]".$row['crdr']."[@$&]".$row['amount']."[@$&]".$row['amtinwords']."[@$&]".$row['warehouse']."[@$&]".$row['remarks']."[@$&]".$row['flag']."[@$&]".$row['active']."[@$&]".$row['dflag']."[@$&]".$row['addedemp']."[@$&]".$row['addedtime']."[@$&]".$row['updatedemp']."[@$&]".$row['updatedtime'];
                            }
                            else{
                                $j++; $key_code = $row['date']."[@$&]".$j;
                                $sdn_info[$key_code] = $row['date']."[@$&]"."Farmer Debit Note"."[@$&]".$row['trnum']."[@$&]"."0"."[@$&]".$row['amount']."[@$&]".$row['remarks'];
                               // $sdn_info[$key_code] = $row['incr']."[@$&]".$row['prefix']."[@$&]".$row['trnum']."[@$&]".$row['type']."[@$&]".$row['date']."[@$&]".$row['vcode']."[@$&]".$row['docno']."[@$&]".$row['coa']."[@$&]".$row['crdr']."[@$&]".$row['amount']."[@$&]".$row['amtinwords']."[@$&]".$row['warehouse']."[@$&]".$row['remarks']."[@$&]".$row['flag']."[@$&]".$row['active']."[@$&]".$row['dflag']."[@$&]".$row['addedemp']."[@$&]".$row['addedtime']."[@$&]".$row['updatedemp']."[@$&]".$row['updatedtime'];
                            }
                        }
                    }

                    $purchase_ccount = sizeof($purchase_info);
                    $oded_ccount = sizeof($other_ded);
                    $tds_ccount = sizeof($tds_info);
                    $payment_ccount = sizeof($payment_info);
                    $sales_ccount = sizeof($sales_info);
                    $receipt_ccount = sizeof($receipt_info);
                    $return_ccount = sizeof($return_info);
                    $scn_ccount = sizeof($scn_info);
                    $sdn_ccount = sizeof($sdn_info);
                    $exist_inv = ""; $tot_pur_qty = $tot_pur_amt = $bt_pur_amt = $bt_pay_amt = 0;
                    for ($currentDate = strtotime($fdate); $currentDate <= strtotime($tdate); $currentDate += (86400)) {
                        $date_asc = date('Y-m-d', $currentDate);

                        // purchase Entries
                        for($i = 0;$i <=$purchase_ccount;$i++){
                            if(!empty($purchase_info[$date_asc."[@$&]".$i])){
                                $purchases_details = explode("[@$&]",$purchase_info[$date_asc."[@$&]".$i]);


                                $bal =  $bal +   $purchases_details[3];

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($purchases_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$purchases_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$purchases_details[2]."</td>";
                                echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                              
								echo "<td style='width:70px;text-align:right;'>".number_format_ind($purchases_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>" . number_format_ind(max(0, $purchases_details[3])) . "</td>";
                                echo "<td style='width:70px;text-align:right;'>".number_format_ind($bal)."</td>";
								
                                
                                $bt_pur_amt = $bt_pur_amt + $purchases_details[3];
                                echo "</tr>";
                            }
                        }

                        //Other Deduction Entries
                        for($i = 0;$i <=$oded_ccount;$i++){
                            if(!empty($other_ded[$date_asc."[@$&]".$i])){
                                $tds_details = explode("[@$&]",$other_ded[$date_asc."[@$&]".$i]);


                                $bal =  $bal -   $tds_details[4];

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($tds_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$tds_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$tds_details[2]."</td>";
                                echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                                
								echo "<td style='width:70px;text-align:right;'>".number_format_ind($tds_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($tds_details[3])."</td>";
                                echo "<td style='width:70px;text-align:right;'>".number_format_ind($bal)."</td>";
								
                                
                                $bt_pay_amt = $bt_pay_amt + $tds_details[4];
                                echo "</tr>";
                            }
                        }

                        // TDS Entries
                        for($i = 0;$i <=$tds_ccount;$i++){
                            if(!empty($tds_info[$date_asc."[@$&]".$i])){
                                $tds_details = explode("[@$&]",$tds_info[$date_asc."[@$&]".$i]);


                                $bal =  $bal -   $tds_details[4];

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($tds_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$tds_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$tds_details[2]."</td>";
                                echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                                
								echo "<td style='width:70px;text-align:right;'>".number_format_ind($tds_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($tds_details[3])."</td>";
                                echo "<td style='width:70px;text-align:right;'>".number_format_ind($bal)."</td>";
								
                                
                                $bt_pay_amt = $bt_pay_amt + $tds_details[4];
                                echo "</tr>";
                            }
                        }

                        // payment Entries
                        for($i = 0;$i <=$payment_ccount;$i++){
                            if(!empty($payment_info[$date_asc."[@$&]".$i])){
                                $payment_details = explode("[@$&]",$payment_info[$date_asc."[@$&]".$i]);
                                if($row['pay_type'] == 'gc_pay' || $row['pay_type'] == ''){
                                    $bal =  $bal - $payment_details[3];
                                }else{
                                    $bal =  $bal - $payment_details[3];
                                }

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($payment_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$payment_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$payment_details[2]."</td>";
                                echo "<td style='width:70px;text-align:left;'>".$payment_details[5]."</td>";
								
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($payment_details[3])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($payment_details[4])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($bal)."</td>";
                               
                                echo "</tr>";
                                if($row['pay_type'] == 'gc_pay' || $row['pay_type'] == ''){
                                    $bt_pay_amt = $bt_pay_amt + $payment_details[3];
                                }else{
                                    $bt_pur_amt = $bt_pur_amt + $payment_details[3];
                                }
                             
                            }
                        }

                         // Sales Entries
                         for($i = 0;$i <=$sales_ccount;$i++){
                            if(!empty($sales_info[$date_asc."[@$&]".$i])){
                                $sales_details = explode("[@$&]",$sales_info[$date_asc."[@$&]".$i]);

                                $bal =  $bal -   $sales_details[4];

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($sales_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$sales_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$sales_details[2]."</td>";
                                echo "<td style='width:70px;text-align:left;'>".$sales_details[5]."</td>";
								
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($sales_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[3])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($bal)."</td>";
                               
                                echo "</tr>";

                                $bt_pay_amt = $bt_pay_amt + $sales_details[4];
                            }
                        }

                        // Receipt Entries
                        for($i = 0;$i <=$receipt_ccount;$i++){
                            if(!empty($receipt_info[$date_asc."[@$&]".$i])){
                                $receipt_details = explode("[@$&]",$receipt_info[$date_asc."[@$&]".$i]);


                                $bal =  $bal +   $receipt_details[3];

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($receipt_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$receipt_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$receipt_details[2]."</td>";
                                echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                               
								echo "<td style='width:70px;text-align:right;'>".number_format_ind($receipt_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($receipt_details[3])."</td>";
                                echo "<td style='width:70px;text-align:right;'>".number_format_ind($bal)."</td>";
								
                                
                                $bt_pur_amt = $bt_pur_amt + $receipt_details[3];
                                echo "</tr>";
                            }
                        }


                        // Return Entries
                        for($i = 0;$i <=$return_ccount;$i++){
                            if(!empty($return_info[$date_asc."[@$&]".$i])){
                                $return_details = explode("[@$&]",$return_info[$date_asc."[@$&]".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($return_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$return_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$return_details[5]."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                        
                                echo "</tr>";
                            }
                        }

                        // SCN Entries
                        for($i = 0;$i <=$scn_ccount;$i++){
                            if(!empty($scn_info[$date_asc."[@$&]".$i])){
                                $scn_details = explode("[@$&]",$scn_info[$date_asc."[@$&]".$i]);

                                $bal =  $bal +   $scn_details[3];

                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($scn_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$scn_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$scn_details[2]."</td>";
                                echo "<td style='width:70px;text-align:left;'>".$scn_details[5]."</td>";
								
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($scn_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($scn_details[3])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($bal)."</td>";
                                
                                echo "</tr>";

                                $bt_pur_amt = $bt_pur_amt + $scn_details[3];
                            }
                        }

                        // SDN Entries
                        for($i = 0;$i <=$sdn_ccount;$i++){
                            if(!empty($sdn_info[$date_asc."[@$&]".$i])){
                                $sdn_details = explode("[@$&]",$sdn_info[$date_asc."[@$&]".$i]);

                                $bal =  $bal -   $sdn_details[4];
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($sdn_details[0]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$sdn_details[1]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$sdn_details[2]."</td>";
                                echo "<td style='width:70px;text-align:left;'>".$sdn_details[5]."</td>";
								
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($sdn_details[4])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($sdn_details[3])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($bal)."</td>";
                                echo "</tr>";

                                $bt_pay_amt = $bt_pay_amt + $sdn_details[4];
                            }
                        }
                    }
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='4'>Between Dates Total</td>";
					
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_pay_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_pur_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='4'>Closing Total</td>";
                   
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($prev_bal_dr+$bt_pay_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($prev_bal_cr+$bt_pur_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='4'>Outstanding</td>";
                    if(($prev_bal_cr+$bt_pur_amt) > ($prev_bal_dr+$bt_pay_amt)){
                       
                        echo "<td style='width:100px;text-align:right;'></td>";
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind(($prev_bal_cr+$bt_pur_amt) - ($prev_bal_dr+$bt_pay_amt))."</td>";
                    }else{
                        echo "<td style='width:100px;text-align:right;'>".number_format_ind(($prev_bal_dr+$bt_pay_amt) - ($prev_bal_cr+$bt_pur_amt) )."</td>";
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                      
                    }
					
					echo "<td style='width:110px;text-align:right;'></td>";
                   
                    echo "</tr>";
                ?>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script type="text/javascript">

        var tableToExcel = (function() {
    
    var uri = 'data:application/vnd.ms-excel;base64,'
      , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
     // if (selectedValue === 'excel') {  
    return function(table, name, filename, chosen) {
      if (chosen === 'excel') { 
      if (!table.nodeType) table = document.getElementById(table)
      var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
      //window.location.href = uri + base64(format(template, ctx))
      var link = document.createElement("a");
                      link.download = filename+".xls";
                      link.href = uri + base64(format(template, ctx));
                      link.click();
      }
    }
  
  })()
</script>  
    </body>
</html>
<?php
include "header_foot.php";
?>