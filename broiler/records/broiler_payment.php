<?php
//broiler_payment.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Payment Report";
include "header_head.php";
$user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
    $method_name[$row['code']] = $row['description'];
    $vendor_name[$row['code']] = $row['description'];
}
$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mode_name[$row['code']] = $row['description']; $mode_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $method_name[$row['code']] = $row['description']; $vendor_name[$row['code']] = $row['description']; $method_code[$row['code']] = $row['code']; $coa_mode[$row['code']] = $row['ctype']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $method_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; $method_name[$row['code']] = $row['name']; }

$fdate = $tdate = date("Y-m-d"); $vendors = $sectors = "all"; $excel_type = "display"; $url = "";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $sectors = $_POST['sectors'];
    if($vendors == "all"){ $vcodes = $vcodes1 = $vcodes2 = $vcodes3 = ""; } else{ $vcodes = " AND `ccode` = '$vendors'"; $vcodes1 = " AND `vcode` = '$vendors'"; $vcodes2 = " AND `tcoa` = '$vendors'"; $vcodes3 = " AND `to_account` = '$vendors'"; }
    if($sectors != "all"){ $wcodes = " AND `warehouse` = '$sectors'"; $wcodes2 = " AND `sector` = '$sectors'"; } else{ $wcodes = $wcodes2 = ""; }
	
    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_supplier =$vendor_name[$_POST['vendors']]; if ($export_supplier == "") { $export_supplier = "All"; }
    $export_sectors = $sector_name[$_POST['sectors']]; if ( $export_sectors == "") {  $export_sectors = "All"; }
    
    if ($export_fdate == $export_tdate)
    {$filename = "Payment Summary_".$export_tdate; }
     else {
    $filename = "Payment Summary_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/PaymentReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors."&sectors=".$sectors;
}

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_payments LIKE poulso6_admin_broiler_broilermaster.broiler_payments;"; mysqli_query($conn,$sql1); }
if(in_array("master_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_payments LIKE poulso6_admin_broiler_broilermaster.master_payments;"; mysqli_query($conn,$sql1); }
if(in_array("account_contranotes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.account_contranotes LIKE poulso6_admin_broiler_broilermaster.account_contranotes;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_voucher_notes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_voucher_notes LIKE poulso6_admin_broiler_broilermaster.broiler_voucher_notes;"; mysqli_query($conn,$sql1); }

?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
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
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }
            table thead,
            table tfoot {
                position: sticky;
            }
            table thead {
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
            </style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
        <form action="broiler_payment.php" method="post">
            <thead class="thead1" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Payment Report</h5></th>
                </tr>
            <?php } ?>
                    <tr>
                        <th colspan="14">
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
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Location</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Payment Summary','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                    </form>
                        
                </table>
                <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            
            </div>
                            
           <table id="main_body" class="tbl" align="center"  style="width:1300px;">
          
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
             $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
             while($row = mysqli_fetch_assoc($query)){
             ?>
                 <tr align="center">
                     <th colspan="9" align="center"><?php echo $row['cdetails']; ?><h5>Payment Report</h5></th>
                 </tr>
             <?php } ?>
             <tr>
                       
                       <th colspan="9">
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
                                           <label>Farm/Location: <?php echo $export_sectors; ?></label>
                   
                                       </div>
                                       <div class="m-2 form-group">
                                           <label><br/></label>

                                       </div>

                                       
                               </th>
                           
                       </tr>
                      
                   </thead>
                   <thead class="thead3" align="center">
                    <tr align="center" id="header_sorting">
                    <th id='order_date'>Date</th>
                    <th id='order'>Supplier</th>
                    <th id='order'>Invoice</th>
                    <th id='order'>Dc No.</th>
                    <th id='order'>Mode</th>
                    <th id='order'>Method</th>
                    <th id='order_num'>Amount</th>
                    <th id='order'>Farm/Warehouse</th>
                    <th id='order'>Remarks</th>
                </tr>
                </thead>

                <thead class="thead3" align="center" style="width:1212px; display:none;">            
       
                <tr align="center">
                   <th>Date</th>
                    <th>Supplier</th>
                    <th>Invoice</th>
                    <th>Dc No.</th>
                    <th>Mode</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Farm/Warehouse</th>
                    <th>Remarks</th>    
                </tr>
       </thead>
  
            
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1" id="tbody1">
                <?php
                $sql_record = "SELECT * FROM `broiler_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vcodes."".$wcodes." AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Supplier"><?php echo $vendor_name[$row['ccode']]; ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['docno']; ?></td>
                    <td title="Mode"><?php echo $mode_name[$row['mode']]; ?></td>
                    <td title="Method"><?php echo $method_name[$row['method']]; ?></td>
                    <td title="Total Amount" style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
                    <td title="Farm/Warehouse"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                    $tot_amt = $tot_amt + $row['amount'];
                }

                $sql_record = "SELECT * FROM `broiler_voucher_notes` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vcodes1."".$wcodes." AND `crdr` IN ('Dr') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Supplier"><?php echo $vendor_name[$row['vcode']]; ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['dcno']; ?></td>
                    <td title="Mode"><?php echo $mode_name[$row['group_code']]; ?></td>
                    <td title="Method"><?php echo $method_name[$row['vcode']]; ?></td>
                    <td title="Total Amount" style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
                    <td title="Farm/Warehouse"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                    $tot_amt = $tot_amt + $row['amount'];
                }
                $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vcodes2."".$wcodes." AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Supplier"><?php echo $vendor_name[$row['tcoa']]; ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['dcno']; ?></td>
                    <td title="Mode"><?php echo $coa_mode[$row['fcoa']]; ?></td>
                    <td title="Method"><?php echo $method_name[$row['fcoa']]; ?></td>
                    <td title="Total Amount" style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
                    <td title="Farm/Warehouse"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                    $tot_amt = $tot_amt + $row['amount'];
                }
                $sql_record = "SELECT * FROM `master_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vcodes3."".$wcodes2." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Supplier"><?php echo $vendor_name[$row['to_account']]; ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['billno']; ?></td>
                    <td title="Mode"><?php echo $coa_mode[$row['from_account']]; ?></td>
                    <td title="Method"><?php echo $method_name[$row['from_account']]; ?></td>
                    <td title="Total Amount" style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
                    <td title="Farm/Warehouse"><?php echo $sector_name[$row['sector']]; ?></td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                    $tot_amt = $tot_amt + $row['amount'];
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="thead4">
                    <th colspan="6" style="text-align:center;">Total</th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
                    <th colspan="2" style="text-align:right;"></th>
                </tr>
            </tfoot>
        <?php
            }
        ?>
        </table><br/><br/><br/>
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
                    //slnos();
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
                    //slnos();
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
                    //slnos();
                    asc = !asc;
                    })
                });
                
            }
            /*function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }*/

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script type="text/javascript">
var tableToExcel = (function() {
    
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
   // if (selectedValue === 'excel') {  
  return function(table, name, filename, chosen, bag_column) {
    if (chosen === 'excel') { 
        
        $('#header_sorting').empty();
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    //window.location.href = uri + base64(format(template, ctx))
    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                var html = '';
                html += '<th id="order_date">Date</th>';
                html += '<th id="order">Supplier</th>';
                html += '<th id="order">Invoice</th>';
                html += '<th id="order">Dc. No.</th>';
                html += '<th id="order">Mode</th>';
                html += '<th id="order">Method</th>';
                html += '<th id="order_num">Amount</th>';
                html += '<th id="order">Farm/Warehouse</th>';
                html += '<th id="order">Remarks</th>';
                $('#header_sorting').append(html);
                table_sort();
                table_sort2();
                table_sort3();
  
    }
  }

//}
})()
</script>
       
       
       <script src="../table_search_filter/Search_Script.js"></script>
    
    </body>
</html>
<?php
include "header_foot.php";
?>