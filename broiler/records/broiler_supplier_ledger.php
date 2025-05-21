<?php
//broiler_supplier_ledger.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $cdetails = $row['cdetails']; $logo_path = "../".$row['logopath']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

$file_name = "Supplier History Report";

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

$sql = "SELECT * FROM `inv_sectors`  WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank')"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_modes`"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $obdate[$row['code']] = $row['obdate']; $obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt']; }

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){  $coa_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $vendors = "select"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_supplier_ledger-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors;
}


/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_payments LIKE poulso6_admin_broiler_broilermaster.broiler_payments;"; mysqli_query($conn,$sql1); }
if(in_array("master_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_payments LIKE poulso6_admin_broiler_broilermaster.master_payments;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_itemreturns", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_itemreturns LIKE poulso6_admin_broiler_broilermaster.broiler_itemreturns;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_crdrnote", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_crdrnote LIKE poulso6_admin_broiler_broilermaster.broiler_crdrnote;"; mysqli_query($conn,$sql1); }
if(in_array("account_contranotes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.account_contranotes LIKE poulso6_admin_broiler_broilermaster.account_contranotes;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_voucher_notes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_voucher_notes LIKE poulso6_admin_broiler_broilermaster.broiler_voucher_notes;"; mysqli_query($conn,$sql1); }
if(in_array("master_receipts", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_receipts LIKE poulso6_admin_broiler_broilermaster.master_receipts;"; mysqli_query($conn,$sql1); }


?>
<style>
 @page {
            size: landscape; /* Try portrait or landscape */
        }
    </style>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo $logo_path; ?>" height="110px"/></td>
                    <th colspan="18" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5> <?php if($vendors != "select"){ echo '<h5>'.$vendor_name[$vendors].'</h5>'; } ?></th>
                </tr>
            </thead>
            <form action="broiler_supplier_ledger.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="20">
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
                                    <label>Supplier</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="select" <?php if($vendors == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($vendor_code as $vcode){ if($vendor_name[$vcode] != ""){ ?>
                                        <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $vendor_name[$vcode]; ?></option>
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
            <?php
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr align="center">';
            $nhtml .= '<td colspan="4" align="center"><img src="'.$logo_path.'" height="110px"/></td>';
            $nhtml .= '<th colspan="8" align="center">'.$cdetails.'<h5>'.$file_name.'</h5></th>';
            $nhtml .= '</tr>';

            $nhtml .= '<th>Date</th>'; $fhtml .= '<th>Date</th>';
            $nhtml .= '<th>Trnum</th>'; $fhtml .= '<th>Trnum</th>';
            $nhtml .= '<th>Doc. No.</th>'; $fhtml .= '<th>Doc. No.</th>';
            $nhtml .= '<th>Type</th>'; $fhtml .= '<th>Type</th>';
            //$nhtml .= '<th>Supplier</th>'; $fhtml .= '<th>Supplier</th>';
            $nhtml .= '<th>Item</th>'; $fhtml .= '<th>Item</th>';
            $nhtml .= '<th>Boxes</th>'; $fhtml .= '<th>Boxes</th>';
            $nhtml .= '<th>Sent Quantity</th>'; $fhtml .= '<th>Sent Quantity</th>';
            $nhtml .= '<th>Received Quantity</th>'; $fhtml .= '<th>Received Quantity</th>';
            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th>Rate</th>';
            $nhtml .= '<th>Amount</th>'; $fhtml .= '<th>Amount</th>';
            $nhtml .= '<th>Freight</th>'; $fhtml .= '<th>Freight</th>';
            $nhtml .= '<th>GST</th>'; $fhtml .= '<th>GST</th>';
            $nhtml .= '<th>TDS</th>'; $fhtml .= '<th>TDS</th>';
            $nhtml .= '<th>Credit</th>'; $fhtml .= '<th>Credit</th>';
            $nhtml .= '<th>Debit</th>'; $fhtml .= '<th>Debit</th>';
            $nhtml .= '<th>Balance</th>'; $fhtml .= '<th>Balance</th>';
            $nhtml .= '<th>Sector</th>'; $fhtml .= '<th>Sector</th>';
            $nhtml .= '<th>Farm Code</th>'; $fhtml .= '<th>Farm Code</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th>Remarks</th>';
            $nhtml .= '<th>Vehicle</th>'; $fhtml .= '<th>Vehicle</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';

            echo $html;
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = ""; $opening_purchases = $osup_frtamt = $opening_payments = $opening_scn = $opening_sdn = $opening_returns = $rb_amt = $opening_cntcr = $opening_cntdr = $opening_vcr = $opening_vdr = 0;

                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $opening_purchases += (float)$row['finl_amt']; $old_inv = $row['trnum']; } } }
                    //Fetch Supplier Freight
                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` < '$fdate' AND `freight_pay_acc` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; $old_inv = ""; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $osup_frtamt += (float)$row['freight_amt']; $old_inv = $row['trnum']; } } }
                    
                    $sql_record = "SELECT * FROM `broiler_payments` WHERE `date` < '$fdate' AND `ccode` = '$vendors' AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_payments += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` < '$fdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_payments += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_returns += (float)$row['amount']; } }

                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `crdr` IN ('Debit','Credit') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $opening_scn += (float)$row['amount']; } else{ $opening_sdn += (float)$row['amount']; } } }

                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntcr += (float)$row['amount']; } }

                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_cntdr += (float)$row['amount']; } }

                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` < '$fdate' AND `vcode` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_vcr += (float)$row['cr_amt']; $opening_vdr += (float)$row['dr_amt']; } }

                    $ob_cramt = $ob_cramt = 0;
                    if($obtype[$vendors] == "Cr"){ $ob_cramt = $obamt[$vendors]; $ob_dramt = 0; } else{ $ob_dramt = $obamt[$vendors]; $ob_cramt = 0; }

                    $ob_rcv = $opening_purchases + $osup_frtamt + $opening_scn + $opening_cntcr + $opening_vcr + $ob_dramt;
					$ob_pid = $opening_payments + $opening_returns + $opening_sdn + $opening_cntdr + $opening_vdr + $ob_cramt;

                    if($_SERVER['REMOTE_ADDR'] == "49.205.134.69"){
                        echo "<br/>Sale: ".$opening_purchases;
                        echo "<br/>Credit Note: ".$opening_scn;
                        echo "<br/>Contra Cr: ".$opening_cntcr;
                        echo "<br/>Voucher Cr: ".$opening_vcr;
                        echo "<br/>OB Dr: ".$ob_dramt;
                        echo "<br/>Receipt: ".$opening_payments;
                        echo "<br/>Sale Return: ".$opening_returns;
                        echo "<br/>Debit Note: ".$opening_sdn;
                        echo "<br/>Contra Dr: ".$opening_cntdr;
                        echo "<br/>Voucher Dr: ".$opening_vdr;
                        echo "<br/>OB Cr: ".$ob_cramt;
                        echo "<br/>$ob_rcv = $opening_purchases + $opening_scn + $opening_cntcr + $opening_vcr + $ob_dramt;<br/>";
                        echo "<br/>$ob_pid = $opening_payments + $opening_returns + $opening_sdn + $opening_cntdr + $opening_vdr + $ob_cramt;<br/>";
                    }

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='12' style='font-weight:bold;'>Previous Balance</td>";
                    if($ob_rcv >= $ob_pid){
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_rcv - $ob_pid)."</td>";
                        echo "<td></td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_rcv - $ob_pid)."</td>";
                        $rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
						$ob_rev_amt = $ob_rcv - $ob_pid;
						$ob_pid_amt = 0;
                    }
                    else{
                        echo "<td></td>";
                        echo "<td style='font-weight:bold;text-align:right;'>".number_format_ind($ob_pid - $ob_rcv)."</td>";
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
                    $purchase_info = $pur_freight_info = $payment_info = $return_info = $contra_cr = $contra_dr = $scn_info = $sdn_info = $inv_count = $inv_fcount = $between_vcr = $between_vdr = array();

                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $purchase_info[$key_code] = $row['incr']."@$&".$row['prefix']."@$&".$row['trnum']."@$&".$row['date']."@$&".$row['vcode']."@$&".$row['billno']."@$&".$row['icode']."@$&0@$&".$row['snt_qty']."@$&".$row['rcd_qty']."@$&".$row['fre_qty']."@$&".$row['rate']."@$&".$row['dis_per']."@$&".$row['dis_amt']."@$&".$row['gst_per']."@$&".$row['gst_amt']."@$&".$row['tcds_per']."@$&".$row['tcds_amt']."@$&".$row['item_tamt']."@$&".$row['freight_type']."@$&".$row['freight_amt']."@$&".$row['freight_pay_type']."@$&".$row['freight_pay_acc']."@$&".$row['freight_acc']."@$&".$row['round_off']."@$&".$row['finl_amt']."@$&".$row['bal_qty']."@$&".$row['bal_amt']."@$&".$row['remarks']."@$&".$row['warehouse']."@$&".$row['farm_batch']."@$&".$row['supervisor_code']."@$&".$row['bag_code']."@$&".$row['bag_count']."@$&".$row['batch_no']."@$&".$row['exp_date']."@$&".$row['vehicle_code']."@$&".$row['driver_code']."@$&".$row['sale_type']."@$&".$row['active']."@$&".$row['flag']."@$&".$row['dflag']."@$&".$row['addedemp']."@$&".$row['addedtime']."@$&".$row['updatedemp']."@$&".$row['updatedtime']."@$&".$row['mob_flag']."@$&".$row['nof_boxes'];
                            if(!empty($inv_count[$row['trnum']])){
                                $inv_count[$row['trnum']] = $inv_count[$row['trnum']] + 1;
                            }
                            else{
                                $inv_count[$row['trnum']] = 1;
                            }
                        }
                    }
                    //Fetch Supplier Freight
                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `freight_pay_acc` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; $old_inv = ""; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv == "" || $old_inv != $row['trnum']){
                                $i++; $key_code = $row['date']."@".$i;
                                $old_inv = $row['trnum'];
                                $pur_freight_info[$key_code] = $row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['freight_type']."@".$row['freight_amt']."@".$row['freight_pay_type']."@".$row['freight_pay_acc']."@".$row['freight_acc']."@".$row['freight_tons']."@".$row['freight_price']."@".$row['btds_freight_amt']."@".$row['freight_tds_amt']."@".$row['nof_boxes']."@".$row['warehouse']."@".$row['billno']."@".$row['vehicle_code'];
                            }
                        }
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$vendors' AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $payment_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['sms_sent']."@".$row['whapp_sent']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['c10']."@".$row['c20']."@".$row['c50']."@".$row['c100']."@".$row['c500']."@".$row['c2000']."@".$row['ccoins']."@".$row['c200'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $payment_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['to_account']."@".$row['billno']."@".$row['mode']."@".$row['from_account']."@".$row['amount']."@".$row['amtinwords']."@".$row['t_type']."@".$row['sector']."@".$row['remarks']."@".$row['sms_sent']."@".$row['whapp_sent']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['c10']."@".$row['c20']."@".$row['c50']."@".$row['c100']."@".$row['c500']."@".$row['c2000']."@".$row['ccoins']."@".$row['c200'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; $i = 0; $rct_info = array(); if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $rct_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['to_account']."@".$row['billno']."@".$row['mode']."@".$row['from_account']."@".$row['amount']."@".$row['amtinwords']."@".$row['t_type']."@".$row['sector']."@".$row['remarks']."@".$row['sms_sent']."@".$row['whapp_sent']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['c10']."@".$row['c20']."@".$row['c50']."@".$row['c100']."@".$row['c500']."@".$row['c2000']."@".$row['ccoins']."@".$row['c200'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $return_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['inv_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['rtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `crdr` IN ('Debit','Credit') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){
                                $i++; $key_code = $row['date']."@".$i;
                                $scn_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['vcode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['amtinwords']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                            }
                            else{
                                $j++; $key_code = $row['date']."@".$j;
                                $sdn_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['vcode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['amtinwords']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                            }
                        }
                    }

                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $contra_cr[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['dcno']."@".$row['fcoa']."@".$row['tcoa']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
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

                    $purchase_ccount = sizeof($purchase_info);
                    $purfrt_ccount = sizeof($pur_freight_info);
                    $payment_ccount = sizeof($payment_info);
                    $return_ccount = sizeof($return_info);
                    $scn_ccount = sizeof($scn_info);
                    $sdn_ccount = sizeof($sdn_info);
                    $cdr_ccount = sizeof($contra_dr);
                    $ccr_ccount = sizeof($contra_cr);
                    $vcr_ccount = sizeof($between_vcr);
                    $vdr_ccount = sizeof($between_vdr);
                    $rct_ccount = sizeof($rct_info);

                    $exist_inv = $exist_finv = ""; $tot_pur_qty = $tot_pamt = $tot_gst = $tot_tds = $tot_pur_amt = $bt_pur_amt = $bt_pay_amt = 0;
                    for ($currentDate = strtotime($fdate); $currentDate <= strtotime($tdate); $currentDate += (86400)) {
                        $date_asc = date('Y-m-d', $currentDate);

                        //Purchase Entries
                        for($i = 0;$i <=$purchase_ccount;$i++){
                            if(!empty($purchase_info[$date_asc."@".$i])){
                                $purchases_details = array(); $purchases_details = explode("@$&",$purchase_info[$date_asc."@".$i]);
                                $url1 = "https://".$_SERVER['SERVER_NAME']."/print/Examples/broiler_purchaseinvoice1.php?id=".$purchases_details[2];
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($purchases_details[3]))."</td>";
                                ?>
                                <td title="trnum" style='width:110px;text-align:left;'><a href="<?php echo $url1; ?>" target="_blank"><?php echo $purchases_details[2]; ?></a></td>
                                <?php
								
								echo "<td style='width:70px;text-align:left;'>".$purchases_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Purchase Invoice</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$purchases_details[4]]."</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$purchases_details[6]]."</td>";
                                echo "<td style='width:110px;text-align:right;' title='".$purchases_details[47]."'>".number_format_ind($purchases_details[47])."</td>";
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[8])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[9])."</td>";
								//echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[11])."</td>";
                                if(!empty($purchases_details[18]) && $purchases_details[18] > 0 && !empty($purchases_details[9]) && $purchases_details[9] > 0){
                                    echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[18] / $purchases_details[9])."</td>";
                                }
                                else{
                                    echo "<td style='width:110px;text-align:right;'>".number_format_ind(0)."</td>";
                                }
								
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[18])."</td>";
								/*Freight*/
                                echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[20])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[15])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[17])."</td>";
                                if($exist_inv != $purchases_details[2]){
                                    $exist_inv = $purchases_details[2];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$purchases_details[2]].">".number_format_ind($purchases_details[25])."</td>";
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$purchases_details[2]].">".number_format_ind(0.00)."</td>";
                                    $bt_pur_amt = $bt_pur_amt + $purchases_details[25];
                                    $ob_rev_amt = $ob_rev_amt + $purchases_details[25];
                                    $rb_amt = $rb_amt + $purchases_details[25];
                                    if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$purchases_details[2]].">".number_format_ind($rb_amt)."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$purchases_details[2]]." title='".$purchases_details[29]."'>".$sector_name[$purchases_details[29]]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$purchases_details[2]].">".$batch_name[$purchases_details[30]]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$purchases_details[2]].">".$purchases_details[28]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$purchases_details[2]].">".$purchases_details[36]."</td>";
                                }
                                $tot_boxes += (float)$purchases_details[47];
                                $tot_sent_qty += (float)$purchases_details[8];
                                $tot_pur_qty += (float)$purchases_details[9];
                                $tot_pamt += (float)$purchases_details[18];
                                $tot_freight += (float)$purchases_details[20];
                                $tot_gst += (float)$purchases_details[15];
                                $tot_tds += (float)$purchases_details[17];
                                $tot_pur_amt += (float)$purchases_details[18];
                                echo "</tr>";
                            }
                        }
                        //Purchase Freight Entries
                        for($i = 0;$i <=$purfrt_ccount;$i++){
                            if(!empty($pur_freight_info[$date_asc."@".$i])){
                                $pur_freight_info[$date_asc."@".$i];
                                $purchases_details = array(); $purchases_details = explode("@",$pur_freight_info[$date_asc."@".$i]);
                                
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($purchases_details[1]))."</td>";
								echo "<td title='trnum' style='width:110px;text-align:left;'>".$purchases_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$purchases_details[14]."</td>";
								echo "<td style='width:70px;text-align:left;'>Purchase Freight</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[12])."</td>";
                                echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[8])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[9])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[10])."</td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[11])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($purchases_details[4])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_pur_amt = $bt_pur_amt + $purchases_details[4];
                                $ob_rev_amt = $ob_rev_amt + $purchases_details[4];
                                $rb_amt = $rb_amt + $purchases_details[4];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$purchases_details[13]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:70px;text-align:left;'>".$purchases_details[15]."</td>";
                                echo "</tr>";
                                $tot_boxes += (float)$purchases_details[12];
                                $tot_pur_qty += (float)$purchases_details[8];
                                $tot_pamt += (float)$purchases_details[10];
                                $tot_tds += (float)$purchases_details[11];
                                $tot_pur_amt += (float)$purchases_details[4];
                            }
                        }

                        // payment Entries
                        for($i = 0;$i <=$payment_ccount;$i++){
                            if(!empty($payment_info[$date_asc."@".$i])){
                                $payment_details = array(); $payment_details = explode("@",$payment_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($payment_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$payment_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$payment_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Payments</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$payment_details[4]]."</td>";
								//echo "<td style='width:130px;text-align:left;'>".$mode_name[$payment_details[6]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$payment_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($payment_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($payment_details[8])."</td>";
                                $bt_pay_amt = $bt_pay_amt + $payment_details[8];
                                $ob_pid_amt = $ob_pid_amt + $payment_details[8];
                                $rb_amt = $rb_amt - $payment_details[8];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$payment_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$payment_details[12]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                         // receipt Entries
                        for($i = 0;$i <=$rct_ccount;$i++){
                            if(!empty($rct_info[$date_asc."@".$i])){
                                $rct_details = array(); $rct_details = explode("@",$rct_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($rct_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$rct_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$rct_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Receipts</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$payment_details[4]]."</td>";
								//echo "<td style='width:130px;text-align:left;'>".$mode_name[$payment_details[6]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$rct_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($rct_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rct_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_pur_amt = $bt_pur_amt + $rct_details[8];
                               // $bt_pay_amt = $bt_pay_amt + $rct_details[8];
                               // $ob_pid_amt = $ob_pid_amt + $rct_details[8];
                                $rb_amt = $rb_amt + $rct_details[8];
                                $ob_rev_amt = $ob_rev_amt + $rct_details[8];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$rct_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$rct_details[12]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // Return Entries
                        for($i = 0;$i <=$return_ccount;$i++){
                            if(!empty($return_info[$date_asc."@".$i])){
                                $return_details = array(); $return_details = explode("@",$return_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($return_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$return_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$return_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Item Returns</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$return_details[6]]."</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$return_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[9]."</td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[10]."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                $bt_pay_amt = $bt_pay_amt + $return_details[11];
                                $ob_pid_amt = $ob_pid_amt + $return_details[11];
                                $rb_amt = $rb_amt - $return_details[11];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$return_details[13]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$return_details[14]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // SCN Entries
                        for($i = 0;$i <=$scn_ccount;$i++){
                            if(!empty($scn_info[$date_asc."@".$i])){
                                $scn_details = array(); $scn_details = explode("@",$scn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($scn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$scn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$scn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Supplier Credit Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$scn_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$scn_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($scn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($scn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_pur_amt = $bt_pur_amt + $scn_details[9];
                                $ob_rev_amt = $ob_rev_amt + $scn_details[9];
                                $rb_amt = $rb_amt + $scn_details[9];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$scn_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$scn_details[12]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // SDN Entries
                        for($i = 0;$i <=$sdn_ccount;$i++){
                            if(!empty($sdn_info[$date_asc."@".$i])){
                                $sdn_details = array(); $sdn_details = explode("@",$sdn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($sdn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$sdn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$sdn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Supplier Debit Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$sdn_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$sdn_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sdn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($sdn_details[9])."</td>";
                                $bt_pay_amt = $bt_pay_amt + $sdn_details[9];
                                $ob_pid_amt = $ob_pid_amt + $sdn_details[9];
                                $rb_amt = $rb_amt - $sdn_details[9];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$sdn_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$sdn_details[12]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // Contra CR Note Entries
                        for($i = 0;$i <=$ccr_ccount;$i++){
                            if(!empty($contra_cr[$date_asc."@".$i])){
                                $ccr_details = array(); $ccr_details = explode("@",$contra_cr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($ccr_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$ccr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$ccr_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Contra Cr Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$ccr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$ccr_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($ccr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($ccr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_pur_amt = $bt_pur_amt + $ccr_details[8];
                                $ob_rev_amt = $ob_rev_amt + $ccr_details[8];
                                $rb_amt = $rb_amt + $ccr_details[8];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$ccr_details[9]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$ccr_details[10]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // Contra DR Note Entries
                        for($i = 0;$i <=$cdr_ccount;$i++){
                            if(!empty($contra_dr[$date_asc."@".$i])){
                                $cdr_details = array(); $cdr_details = explode("@",$contra_dr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($cdr_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$cdr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$cdr_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Contra Dr Note</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$cdr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$cdr_details[6]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($cdr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($cdr_details[8])."</td>";
                                $bt_pay_amt = $bt_pay_amt + $cdr_details[8];
                                $ob_pid_amt = $ob_pid_amt + $cdr_details[8];
                                $rb_amt = $rb_amt - $cdr_details[8];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$cdr_details[9]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$cdr_details[10]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }
                        // Voucher CR Note Entries
                        for($i = 0;$i <=$vcr_ccount;$i++){
                            if(!empty($between_vcr[$date_asc."@".$i])){
                                $vcr_details = array(); $vcr_details = explode("@",$between_vcr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($vcr_details[1]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$vcr_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$vcr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>Receipt Voucher</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$vcr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$to_coa_name[$vcr_details[0]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($vcr_details[5])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($vcr_details[5])."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                $bt_pur_amt = $bt_pur_amt + $vcr_details[5];
                                $ob_rev_amt = $ob_rev_amt + $vcr_details[5];
                                $rb_amt = $rb_amt + $vcr_details[5];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$vcr_details[6]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$vcr_details[8]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }

                        // Voucher DR Note Entries
                        for($i = 0;$i <=$vdr_ccount;$i++){
                            if(!empty($between_vdr[$date_asc."@".$i])){
                                $vdr_details = array(); $vdr_details = explode("@",$between_vdr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;' class='dates'>".date("d.m.Y",strtotime($vdr_details[1]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$vdr_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$vdr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>Payment Voucher</td>";
								//echo "<td style='width:130px;text-align:left;'>".$vendor_name[$vdr_details[5]]."</td>";
								echo "<td style='width:110px;text-align:left;'>".$from_coa_name[$vdr_details[0]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($vdr_details[5])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($vdr_details[5])."</td>";
                                $bt_pay_amt = $bt_pay_amt + $vdr_details[5];
                                $ob_pid_amt = $ob_pid_amt + $vdr_details[5];
                                $rb_amt = $rb_amt - $vdr_details[5];
                                if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){ $rb_amt = 0; }
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$vdr_details[6]]."</td>";
                                echo "<td style='width:130px;text-align:left;'></td>";
                                echo "<td style='width:130px;text-align:left;'>".$vdr_details[8]."</td>";
								echo "<td style='width:70px;text-align:left;'></td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='3'>Between Dates Total</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_boxes)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_sent_qty)."</td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_pur_qty)."</td>";
                    if($tot_pur_amt > 0 && $tot_pur_qty > 0){
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_pur_amt / $tot_pur_qty)."</td>";
                    }
                    else{
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind(0)."</td>";
                    }
					
                    echo "<td style='width:130px;text-align:right;font-weight:bold;'>".number_format_ind($tot_pamt)."</td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_freight)."</td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_gst)."</td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_tds)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_pur_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_pay_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='3'>Closing Total</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_rev_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_pid_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='3'>Outstanding</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";

                    if(number_format_ind(round($ob_rev_amt,5)) == number_format_ind(round($ob_pid_amt,5))){
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind(0)."</td>";
                        echo "<td style='width:100px;text-align:right;'></td>";
                    }
                    else if($ob_rev_amt >= $ob_pid_amt){
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_rev_amt - $ob_pid_amt)."</td>";
                        echo "<td style='width:100px;text-align:right;'></td>";
                    }
                    else{
                        echo "<td style='width:100px;text-align:right;'></td>";
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_pid_amt - $ob_rev_amt)."</td>";
                    }
                    
					echo "<td style='width:110px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "</tr>";
                ?>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    cdate_format1();
                    /*document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php //echo $nhtml; ?>';
                    $('#head_names').append(html);
                    */
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    /*
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php //echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    */
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                    cdate_format2();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>