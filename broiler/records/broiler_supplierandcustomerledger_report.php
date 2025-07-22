<?php
//broiler_supplierandcustomerledger_report.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Supplier & Customer History Report";
include "header_head.php";

$sector_code = $sector_name = $vehicle_code = $vehicle_name = $emp_code = $emp_name = $coa_code = $coa_name = $mode_code = $mode_name = $vendor_code = $vendor_name = $obdate = 
$obtype = $obamt = $item_code = $item_name = $item_category = array();

$sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank')"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_modes`"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S&C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){
    $vendor_code[$row['code']] = $row['code'];
    $vendor_name[$row['code']] = $row['name'];
    $coa_name[$row['code']] = $row['name'];
    $obdate[$row['code']] = $row['obdate'];
    $obtype[$row['code']] = $row['obtype'];
    $obamt[$row['code']] = $row['obamt'];
}

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '$href' AND `field_function` LIKE 'From Date Auto Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $fdate = date("Y-m-d");
while($row = mysqli_fetch_assoc($query)){ if($row['field_value'] != ""){ $fdate = date("Y-m-d",strtotime($row['field_value'])); } }

$tdate = date("Y-m-d"); $vendors = "select"; $excel_type = "display";
if(isset($_REQUEST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $vendors = $_REQUEST['vendors'];

    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_supplier =$vendor_name[$_POST['vendors']]; if ($export_supplier == "") { $export_supplier = "All"; }
    
    if ($export_fdate == $export_tdate)
    {$filename = "Supplier & Customer History Report_".$export_tdate; }
     else {
    $filename = "Supplier & Customer History Report_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];
	
	//$url = "../PHPExcel/Examples/SupplierHistoyReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors;
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
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:15px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:15px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
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
                    <th colspan="14" align="center"><?php echo $row['cdetails']; ?><h5>Supplier & Customer History Report</h5></br>
                    <h6><span style="color:red;"> From Date: </span><span style="color:green;"><b><?php echo date("d.m.Y", strtotime($fdate)); ?></b></span> &nbsp;&nbsp;&nbsp;<span style="color:red;">To Date: </span> <span style="color:green;"><b><?php echo date("d.m.Y", strtotime($tdate)); ?></b></span> &nbsp;&nbsp;</br><span style="color:red;">Name: </span><span style="color:green;"><b><?php echo $vendor_name[$vendors]; ?></b></span> </h6>
                    </th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_supplierandcustomerledger_report.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="16">
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
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Supplier & Customer History','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                </table>
            </form>
            <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            
            </div>
                            
           <table id="main_body" class="tbl" align="center"  style="width:1300px;">
          
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
             $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
             while($row = mysqli_fetch_assoc($query)){
             ?>
                 <tr align="center">
                     <th colspan="15" align="center"><?php echo $row['cdetails']; ?><h5>Supplier & Customer History Report</h5></th>
                 </tr>
             <?php } ?>
            
             <tr>
                       
                       <th colspan="15">
                                   <div class="row">
                                       <div class="m-2 form-group">
                                           <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                         
                                                                   
                                       <div class="m-2 form-group">
                                           <label>Supplier: <?php echo $export_supplier; ?></label>
                   
                                       </div>
                                        <div class="m-2 form-group">
                                           <label><br/></label>

                                       </div>
                               </th>
                           
                       </tr>
                      
            </thead>
             

            <thead class="thead3" align="center">
                <tr align="center">
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:93px;"'; } ?>>Date</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Trnum</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:70px;"'; } ?>>Doc. No.</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Type</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Item</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Quantity</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Rate</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>Amount</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>GST</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:110px;"'; } ?>>TDS</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Debit</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Credit</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:100px;"'; } ?>>Balance</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Sector</th>
                    <th <?php if($excel_type == "display" || $excel_type == "excel"){ echo 'style="width:130px;"'; } ?>>Remarks</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = ""; $opening_purchases = $opening_payments = $opening_scn = $opening_sdn = $opening_pur_returns = $rb_amt = 0;
                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                $opening_purchases += (float)$row['finl_amt'];
                                $old_inv = $row['trnum'];
                            }
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_payments` WHERE `date` < '$fdate' AND `ccode` = '$vendors' AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_payments += (float)$row['amount'];
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_pur_returns += (float)$row['amount'];
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `crdr` IN ('Debit','Credit') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){ $opening_scn += (float)$row['amount']; } else{ $opening_sdn += (float)$row['amount']; }
                        }
                    }
                    
                    $old_inv = ""; $opening_sales = $opening_receipts = $opening_ccn = $opening_cntcr = $opening_cntdr = $opening_cdn = $opening_sale_returns = $rb_amt = 0;

                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                $opening_sales += (float)$row['finl_amt'];
                                $old_inv = $row['trnum'];
                            }
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` < '$fdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_receipts += (float)$row['amount'];
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_sale_returns += (float)$row['amount'];
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){ $opening_ccn += (float)$row['amount']; } else{ $opening_cdn += (float)$row['amount']; }
                        }
                    }
                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_cntcr += (float)$row['amount'];
                        }
                    }
                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opening_cntdr += (float)$row['amount'];
                        }
                    }

                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` < '$fdate' AND `vcode` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $opening_vcr += (float)$row['cr_amt']; $opening_vdr += (float)$row['dr_amt']; } }

                    $ob_cramt = $ob_cramt = 0;  
                    if($obtype[$vendors] == "Cr"){ $ob_cramt = $obamt[$vendors]; $ob_dramt = 0; } else{ $ob_dramt = $obamt[$vendors]; $ob_cramt = 0; }

                    $sql = "SELECT * FROM `master_receipts` WHERE `date` < '$fdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $mrct = 0; $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $mrct += (float)$row['amount']; }                } 

                    $sql = "SELECT * FROM `master_payments` WHERE `date` < '$fdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $mpay = 0; $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $mpay += (float)$row['amount'];}}

					$ob_rcv = $opening_sales + $opening_cdn + $opening_cntdr + $ob_dramt + $opening_payments + $opening_pur_returns + $opening_vdr + $opening_sdn + $mpay ;
                    $ob_pid = $opening_purchases + $opening_receipts + $opening_sale_returns + $opening_ccn + $opening_scn + $opening_cntcr + $opening_vcr + $ob_cramt + $mrct ;

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='9' style='font-weight:bold;'>Previous Balance</td>";
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
                    echo "</tr>";

                    $key_code = "";
                    $purchase_info = $payment_info = $pur_return_info = $contra_cr = $contra_dr = $scn_info = $sdn_info = $pinv_count = $between_vcr = $between_vdr = array();

                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $purchase_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['billno']."@".$row['icode']."@0@".$row['snt_qty']."@".$row['rcd_qty']."@".$row['fre_qty']."@".$row['rate']."@".$row['dis_per']."@".$row['dis_amt']."@".$row['gst_per']."@".$row['gst_amt']."@".$row['tcds_per']."@".$row['tcds_amt']."@".$row['item_tamt']."@".$row['freight_type']."@".$row['freight_amt']."@".$row['freight_pay_type']."@".$row['freight_pay_acc']."@".$row['freight_acc']."@".$row['round_off']."@".$row['finl_amt']."@".$row['bal_qty']."@".$row['bal_amt']."@".$row['remarks']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['supervisor_code']."@".$row['bag_code']."@".$row['bag_count']."@".$row['batch_no']."@".$row['exp_date']."@".$row['vehicle_code']."@".$row['driver_code']."@".$row['sale_type']."@".$row['active']."@".$row['flag']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['mob_flag'];
                            if(!empty($pinv_count[$row['trnum']])){ $pinv_count[$row['trnum']] += 1; } else{ $pinv_count[$row['trnum']] = 1; }
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
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $pur_return_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['inv_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['rtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
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

                    
                    $key_code = "";
                    $sale_info = $receipt_info = $sale_return_info = $ccn_info = $cdn_info = $inv_count = array();
                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $sale_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['billno']."@".$row['icode']."@".$row['birds']."@".$row['snt_qty']."@".$row['rcd_qty']."@".$row['fre_qty']."@".$row['rate']."@".$row['dis_per']."@".$row['dis_amt']."@".$row['gst_per']."@".$row['gst_amt']."@".$row['tcds_per']."@".$row['tcds_amt']."@".$row['item_tamt']."@".$row['freight_type']."@".$row['freight_amt']."@".$row['freight_pay_type']."@".$row['freight_pay_acc']."@".$row['freight_acc']."@".$row['round_off']."@".$row['finl_amt']."@".$row['bal_qty']."@".$row['bal_amt']."@".$row['remarks']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['supervisor_code']."@".$row['bag_code']."@".$row['bag_count']."@".$row['batch_no']."@".$row['exp_date']."@".$row['vehicle_code']."@".$row['driver_code']."@".$row['sale_type']."@".$row['active']."@".$row['flag']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['mob_flag'];
                            if(!empty($inv_count[$row['trnum']])){
                                $inv_count[$row['trnum']] = $inv_count[$row['trnum']] + 1;
                            }
                            else{
                                $inv_count[$row['trnum']] = 1;
                            }
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $receipt_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['sms_sent']."@".$row['whapp_sent']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime']."@".$row['c10']."@".$row['c20']."@".$row['c50']."@".$row['c100']."@".$row['c500']."@".$row['c2000']."@".$row['ccoins']."@".$row['c200'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Receipt') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $suprct_info = array();
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0;  if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $suprct_info[$key_code] = $row['t_type']."@".$row['v_type']."@".$row['trnum']."@".$row['date']."@".$row['billno']."@".$row['from_account']."@".$row['to_group']."@".$row['to_account']."@".$row['farm_batch']."@".$row['amount']."@".$row['pay_type']."@".$row['gc_amount']."@".$row['cheque_date']."@".$row['ref_no']."@".$row['sector']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime'];
                        }
                    }

                    $sql_record = "SELECT * FROM `master_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_account` = '$vendors' AND `t_type` IN ('Supplier Payment') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $suppay_info = array();
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0;  if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $suppay_info[$key_code] = $row['t_type']."@".$row['v_type']."@".$row['trnum']."@".$row['date']."@".$row['billno']."@".$row['from_account']."@".$row['to_group']."@".$row['to_account']."@".$row['farm_batch']."@".$row['amount']."@".$row['pay_type']."@".$row['gc_amount']."@".$row['cheque_date']."@".$row['ref_no']."@".$row['sector']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $i++; $key_code = $row['date']."@".$i;
                            $sale_return_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['type']."@".$row['date']."@".$row['inv_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['rtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
                        }
                    }
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Credit"){
                                $i++; $key_code = $row['date']."@".$i;
                                $ccn_info[$key_code] = $row['incr']."@".$row['prefix']."@".$row['trnum']."@".$row['crdr']."@".$row['date']."@".$row['vcode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['amtinwords']."@".$row['warehouse']."@".$row['remarks']."@".$row['flag']."@".$row['active']."@".$row['dflag']."@".$row['addedemp']."@".$row['addedtime']."@".$row['updatedemp']."@".$row['updatedtime'];
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
                    $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` IN ('$vendors') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $i = $j = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['crdr'] == "Cr"){
                                $i++; $key_code = $row['date']."@".$i;
                                $between_vcr[$key_code] = $row['trnum']."@".$row['date']."@".$row['dcno']."@".$row['group_code']."@".$row['vcode']."@".$row['amount']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['remarks'];
                            }
                            else if($row['crdr'] == "Dr"){
                                $j++; $key_code = $row['date']."@".$j;
                                $between_vdr[$key_code] = $row['trnum']."@".$row['date']."@".$row['dcno']."@".$row['group_code']."@".$row['vcode']."@".$row['amount']."@".$row['warehouse']."@".$row['farm_batch']."@".$row['remarks'];
                            }
                        }
                    }

                    $purchase_ccount = sizeof($purchase_info);
                    $payment_ccount = sizeof($payment_info);
                    $pur_return_ccount = sizeof($pur_return_info);
                    $scn_ccount = sizeof($scn_info);
                    $sdn_ccount = sizeof($sdn_info);
                    $cdr_ccount = sizeof($contra_dr);
                    $ccr_ccount = sizeof($contra_cr);
                    $mrct_ccount = sizeof($suprct_info);
                    $suppay_ccount = sizeof($suppay_info);
                    $sale_ccount = sizeof($sale_info);
                    $receipt_ccount = sizeof($receipt_info);
                    $sale_return_ccount = sizeof($sale_return_info);
                    $ccn_ccount = sizeof($ccn_info);
                    $cdn_ccount = sizeof($cdn_info);
                    $cdr_ccount = sizeof($contra_dr);
                    $ccr_ccount = sizeof($contra_cr);

                    $vcr_ccount = sizeof($between_vcr);
                    $vdr_ccount = sizeof($between_vdr);

                    $exist_inv = ""; $tot_pur_qty = $tot_gst = $tot_tds = $tot_pur_amt = $bt_credit_amt = $bt_debit_amt = 0;
                    for ($currentDate = strtotime($fdate); $currentDate <= strtotime($tdate); $currentDate += (86400)) {
                        $date_asc = date('Y-m-d', $currentDate);

                        // purchase Entries
                        for($i = 0;$i <=$purchase_ccount;$i++){
                            if(!empty($purchase_info[$date_asc."@".$i])){
                                $purchases_details = explode("@",$purchase_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($purchases_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$purchases_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$purchases_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Purchase Invoice</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$purchases_details[6]]."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[9])."</td>";
                                if(!empty($purchases_details[9]) && $purchases_details[9] != 0){
                                    echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[18] / $purchases_details[9])."</td>";
                                }
                                else{
                                    echo "<td style='width:110px;text-align:right;'>".number_format_ind(0)."</td>";
                                }
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[18])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[15])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($purchases_details[17])."</td>";
                                if($exist_inv != $purchases_details[2]){
                                    $exist_inv = $purchases_details[2];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$pinv_count[$purchases_details[2]]."></td>";
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$pinv_count[$purchases_details[2]].">".number_format_ind($purchases_details[25])."</td>";
                                    $bt_debit_amt = $bt_debit_amt + $purchases_details[25];
                                    $ob_pid_amt = $ob_pid_amt + $purchases_details[25];
                                    $rb_amt = $rb_amt - $purchases_details[25];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$pinv_count[$purchases_details[2]].">".number_format_ind($rb_amt)."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$pinv_count[$purchases_details[2]].">".$sector_name[$purchases_details[29]]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$pinv_count[$purchases_details[2]].">".$purchases_details[28]."</td>";
                                }
                                $tot_pur_qty = $tot_pur_qty + $purchases_details[9];
                                $tot_gst = $tot_gst + $purchases_details[15];
                                $tot_tds = $tot_tds + $purchases_details[17];
                                $tot_pur_amt = $tot_pur_amt + $purchases_details[18];
                                echo "</tr>";
                            }
                        }

                        // payment Entries
                        for($i = 0;$i <=$payment_ccount;$i++){
                            if(!empty($payment_info[$date_asc."@".$i])){
                                $payment_details = explode("@",$payment_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($payment_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$payment_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$payment_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Payments</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$payment_details[7]]."</td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($payment_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($payment_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_credit_amt = $bt_credit_amt + $payment_details[8];
                                $ob_rev_amt = $ob_rev_amt + $payment_details[8];
                                $rb_amt = $rb_amt + $payment_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$payment_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$payment_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                         // master payment Entries
                         for($i = 0;$i <=$suppay_ccount;$i++){
                            if(!empty($suppay_info[$date_asc."@".$i])){
                                $payment_details = explode("@",$suppay_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($payment_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$payment_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$payment_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Payments</td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($payment_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_credit_amt = $bt_credit_amt + $payment_details[9];
                                $ob_rev_amt = $ob_rev_amt + $payment_details[9];
                                $rb_amt = $rb_amt + $payment_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$payment_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$payment_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                        // Return Entries
                        for($i = 0;$i <=$pur_return_ccount;$i++){
                            if(!empty($pur_return_info[$date_asc."@".$i])){
                                $return_details = explode("@",$pur_return_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($return_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$return_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$return_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Item Returns</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$return_details[7]]."</td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[9]."</td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[10]."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_credit_amt = $bt_credit_amt + $return_details[11];
                                $ob_rev_amt = $ob_rev_amt + $return_details[11];
                                $rb_amt = $rb_amt + $return_details[11];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$return_details[13]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$return_details[14]."</td>";
                                echo "</tr>";
                            }
                        }

                        // SCN Entries
                        for($i = 0;$i <=$scn_ccount;$i++){
                            if(!empty($scn_info[$date_asc."@".$i])){
                                $scn_details = explode("@",$scn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($scn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$scn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$scn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Supplier Credit Note</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$scn_details[7]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($scn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($scn_details[9])."</td>";
                                $bt_debit_amt = $bt_debit_amt + $scn_details[9];
                                $ob_pid_amt = $ob_pid_amt + $scn_details[9];
                                $rb_amt = $rb_amt - $scn_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$scn_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$scn_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                        // SDN Entries
                        for($i = 0;$i <=$sdn_ccount;$i++){
                            if(!empty($sdn_info[$date_asc."@".$i])){
                                $sdn_details = explode("@",$sdn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($sdn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$sdn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$sdn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Supplier Debit Note</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$sdn_details[7]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sdn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($sdn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_credit_amt = $bt_credit_amt + $sdn_details[9];
                                $ob_rev_amt = $ob_rev_amt + $sdn_details[9];
                                $rb_amt = $rb_amt + $sdn_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$sdn_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sdn_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                        
                        // Sale Entries
                        for($i = 0;$i <=$sale_ccount;$i++){
                            if(!empty($sale_info[$date_asc."@".$i])){
                                $sales_details = explode("@",$sale_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($sales_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$sales_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$sales_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Sales Invoice</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$sales_details[6]]."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[9])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[11])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[18])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[15])."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($sales_details[17])."</td>";
                                if($exist_inv != $sales_details[2]){
                                    $exist_inv = $sales_details[2];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$sales_details[2]].">".number_format_ind($sales_details[25])."</td>";
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$sales_details[2]]."></td>";
                                    $bt_sale_amt = $bt_sale_amt + $sales_details[25];
                                    $ob_rev_amt = $ob_rev_amt + $sales_details[25];
                                    $rb_amt = $rb_amt + $sales_details[25];
                                    echo "<td style='width:100px;text-align:right;' rowspan=".$inv_count[$sales_details[2]].">".number_format_ind($rb_amt)."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$sales_details[2]].">".$sector_name[$sales_details[29]]."</td>";
                                    echo "<td style='width:130px;text-align:left;' rowspan=".$inv_count[$sales_details[2]].">".$sales_details[28]."</td>";
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
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($receipt_details[8])."</td>";
								echo "<td style='width:110px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($receipt_details[8])."</td>";
                                $bt_rct_amt = $bt_rct_amt + $receipt_details[8];
                                $ob_pid_amt = $ob_pid_amt + $receipt_details[8];
                                $rb_amt = $rb_amt - $receipt_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$receipt_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$receipt_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                         // Receipt Entries
                         for($i = 0;$i <=$mrct_ccount;$i++){
                            if(!empty($suprct_info[$date_asc."@".$i])){
                                $receipt_details = explode("@",$suprct_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($receipt_details[3]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$receipt_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$receipt_details[4]."</td>";
								echo "<td style='width:70px;text-align:left;'>Receipts</td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
								echo "<td style='width:110px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind(0.00)."</td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($receipt_details[9])."</td>";
                                $bt_rct_amt = $bt_rct_amt + $receipt_details[9];
                                $ob_pid_amt = $ob_pid_amt + $receipt_details[9];
                                $rb_amt = $rb_amt - $receipt_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$receipt_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$receipt_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                        // Return Entries
                        for($i = 0;$i <=$sale_return_ccount;$i++){
                            if(!empty($sale_return_info[$date_asc."@".$i])){
                                $return_details = explode("@",$sale_return_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($return_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$return_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$return_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Sales Return</td>";
								echo "<td style='width:130px;text-align:left;'>".$item_name[$return_details[7]]."</td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[9]."</td>";
								echo "<td style='width:110px;text-align:right;'>".$return_details[10]."</td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($return_details[11])."</td>";
                                $bt_rct_amt = $bt_rct_amt + $return_details[11];
                                $ob_pid_amt = $ob_pid_amt + $return_details[11];
                                $rb_amt = $rb_amt - $return_details[11];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$return_details[13]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$return_details[14]."</td>";
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
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($ccn_details[9])."</td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($ccn_details[9])."</td>";
                                $bt_rct_amt = $bt_rct_amt + $ccn_details[9];
                                $ob_pid_amt = $ob_pid_amt + $ccn_details[9];
                                $rb_amt = $rb_amt - $ccn_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$ccn_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$ccn_details[12]."</td>";
                                echo "</tr>";
                            }
                        }

                        // CDN Entries
                        for($i = 0;$i <=$cdn_ccount;$i++){
                            if(!empty($cdn_info[$date_asc."@".$i])){
                                $cdn_details = explode("@",$cdn_info[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($cdn_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$cdn_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$cdn_details[6]."</td>";
								echo "<td style='width:70px;text-align:left;'>Customer Debit Note</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($cdn_details[9])."</td>";
								echo "<td style='width:110px;text-align:left;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($cdn_details[9])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_sale_amt = $bt_sale_amt + $cdn_details[9];
                                $ob_rev_amt = $ob_rev_amt + $cdn_details[9];
                                $rb_amt = $rb_amt + $cdn_details[9];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$cdn_details[11]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$cdn_details[12]."</td>";
                                echo "</tr>";
                            }
                        }
                        
                        // Contra CR Note Entries
                        for($i = 0;$i <=$ccr_ccount;$i++){
                            if(!empty($contra_cr[$date_asc."@".$i])){
                                $ccr_details = explode("@",$contra_cr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($ccr_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$ccr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$ccr_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Contra Cr Note</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$ccr_details[7]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($ccr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($ccr_details[8])."</td>";
                                $bt_debit_amt = $bt_debit_amt + $ccr_details[8];
                                $ob_pid_amt = $ob_pid_amt + $ccr_details[8];
                                $rb_amt = $rb_amt - $ccr_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$ccr_details[9]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$ccr_details[10]."</td>";
                                echo "</tr>";
                            }
                        }

                        // Contra DR Note Entries
                        for($i = 0;$i <=$cdr_ccount;$i++){
                            if(!empty($contra_dr[$date_asc."@".$i])){
                                $cdr_details = explode("@",$contra_dr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($cdr_details[4]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$cdr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$cdr_details[5]."</td>";
								echo "<td style='width:70px;text-align:left;'>Contra Dr Note</td>";
								echo "<td style='width:110px;text-align:left;'>".$coa_name[$cdr_details[6]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($cdr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($cdr_details[8])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_credit_amt = $bt_credit_amt + $cdr_details[8];
                                $ob_rev_amt = $ob_rev_amt + $cdr_details[8];
                                $rb_amt = $rb_amt + $cdr_details[8];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$cdr_details[9]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$cdr_details[10]."</td>";
                                echo "</tr>";
                            }
                        }
                        
                        // Voucher CR Note Entries
                        for($i = 0;$i <=$vcr_ccount;$i++){
                            if(!empty($between_vcr[$date_asc."@".$i])){
                                $vcr_details = explode("@",$between_vcr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($vcr_details[1]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$vcr_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$vcr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>Receipt Voucher</td>";
								echo "<td style='width:110px;text-align:left;'>".$to_coa_name[$vcr_details[0]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($vcr_details[6])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($vcr_details[6])."</td>";
                                $bt_debit_amt = $bt_debit_amt + $vcr_details[6];
                                $ob_pid_amt = $ob_pid_amt + $vcr_details[6];
                                $rb_amt = $rb_amt - $vcr_details[6];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$vcr_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$vcr_details[9]."</td>";
                                echo "</tr>";
                            }
                        }

                        // Voucher DR Note Entries
                        for($i = 0;$i <=$vdr_ccount;$i++){
                            if(!empty($between_vdr[$date_asc."@".$i])){
                                $vdr_details = explode("@",$between_vdr[$date_asc."@".$i]);
                                echo "<tr>";
								echo "<td style='width:93px;'>".date("d.m.Y",strtotime($vdr_details[1]))."</td>";
								echo "<td style='width:110px;text-align:left;'>".$vdr_details[0]."</td>";
								echo "<td style='width:70px;text-align:left;'>".$vdr_details[2]."</td>";
								echo "<td style='width:70px;text-align:left;'>Payment Voucher</td>";
								echo "<td style='width:110px;text-align:left;'>".$from_coa_name[$vdr_details[0]]."</td>";
								echo "<td style='width:130px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:left;'></td>";
								echo "<td style='width:110px;text-align:right;'>".number_format_ind($vdr_details[6])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($vdr_details[6])."</td>";
                                echo "<td style='width:100px;text-align:right;'></td>";
                                $bt_credit_amt = $bt_credit_amt + $vdr_details[6];
                                $ob_rev_amt = $ob_rev_amt + $vdr_details[6];
                                $rb_amt = $rb_amt + $vdr_details[6];
                                echo "<td style='width:100px;text-align:right;'>".number_format_ind($rb_amt)."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$sector_name[$vdr_details[7]]."</td>";
                                echo "<td style='width:130px;text-align:left;'>".$vdr_details[9]."</td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='3'>Between Dates Total</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_pur_qty)."</td>";
                    if($tot_pur_amt > 0 && $tot_pur_qty > 0){
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_pur_amt / $tot_pur_qty)."</td>";
                    }
                    else{
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind(0)."</td>";
                    }
					
                    echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_gst)."</td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($tot_tds)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_credit_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($bt_debit_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='3'>Closing Total</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
					echo "<td style='width:100px;text-align:right;font-weight:bold;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_rev_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_pid_amt)."</td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "</tr>";
                    echo "<tr>";
					echo "<td style='width:403px;text-align:center;font-weight:bold;' colspan='3'>Outstanding</td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:130px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:right;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:130px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    if($ob_rev_amt >= $ob_pid_amt){
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_rev_amt - $ob_pid_amt)."</td>";
                        echo "<td style='width:100px;text-align:right;'></td>";
                    }
                    else{
                        echo "<td style='width:100px;text-align:right;'></td>";
                        echo "<td style='width:100px;text-align:right;font-weight:bold;'>".number_format_ind($ob_pid_amt - $ob_rev_amt)."</td>";
                    }
                    
					echo "<td style='width:110px;text-align:left;'></td>";
					echo "<td style='width:110px;text-align:left;'></td>";
                    echo "<td style='width:100px;text-align:right;'></td>";
                    echo "</tr>";
                ?>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script src="../table_search_filter/Search_Script.js"></script>
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

//}
})()
</script>  

    </body>
</html>
<?php
include "header_foot.php";
?>