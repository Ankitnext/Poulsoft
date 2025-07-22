<?php
//broiler_expense_ledger.php
include "../newConfig.php";
$user_code = $_SESSION['userid'];

$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial, sans-serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica, Arial, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Verdana, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Tahoma, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Trebuchet MS";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Trebuchet MS', sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman'";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman', serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Georgia, serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Garamond, serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Courier New', monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Courier, monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Optima";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Segoe";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Calibri";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Candara";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Grande";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Sans Unicode";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Gill Sans";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Source Sans Pro', 'Arial', sans-serif";

for($i = 0;$i <= 30;$i++){ $fsizes[$i."px"] = $i."px"; }

$i = 0;
$sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array('he_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `he_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Hatchery Expense Flag'  AFTER `dflag`;"; mysqli_query($conn,$sql); }
if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `he_flag`"; mysqli_query($conn,$sql); }
if(in_array('fe_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `fe_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Feed Expense Flag'  AFTER `he_flag`;"; mysqli_query($conn,$sql); }
if(in_array('noninv_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `noninv_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Non-Inventory Coas Flag'  AFTER `fe_flag`;"; mysqli_query($conn,$sql); }
if(in_array('freight_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `freight_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Retail Flag'  AFTER `active`;"; mysqli_query($conn,$sql); }
if(in_array('transporter_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `transporter_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Transporter Flag'  AFTER `freight_flag`;"; mysqli_query($conn,$sql); }
if(in_array('expledger_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `expledger_flag` INT(100) NOT NULL DEFAULT '0' COMMENT ''  AFTER `transporter_flag`;"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }

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

include $num_format_file;
global $page_title; $page_title = "Expense Ledger Report";
include "header_head.php";
$sector_code = $sector_name = array();
$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ".$sector_access_list." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_schedules` WHERE `expledger_flag` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $schedule_code = array();
while($row = mysqli_fetch_assoc($query)){ $schedule_code[$row['code']] = $row['code']; }

$schedule_list = implode("','",$schedule_code);
$coa_code = $coa_name = array();
$sql = "SELECT * FROM `acc_coa` WHERE `expledger_flag` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

$item_code = $item_name = array();
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$vehicle_code = $vehicle_name = array();
$sql = "SELECT * FROM `broiler_vehicle` WHERE `dflag` = '0' ORDER BY `registration_number` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$ven_code = $ven_name = array();
$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

$fdate = $tdate = date("Y-m-d"); $coas = $sectors = "all"; $excel_type = "display";
$font_stype = ""; $font_size = "11px";
if(isset($_REQUEST['fdate']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $coas = $_REQUEST['coas'];
    $sectors = $_REQUEST['sectors'];
    $font_stype = $_REQUEST['font_stype'];
    $font_size = $_REQUEST['font_size'];
	$excel_type = $_REQUEST['export'];
	$url = "../PHPExcel/Examples/broiler_account_ledger-Excel.php?fdate=".$fdate."&tdate=".$tdate."&coas=".$coas."&sectors=".$sectors;
}
$sector_filter = ""; if($sectors != "all"){ $sector_filter = " AND `location` LIKE '$sectors'"; } else{ $sector_filter = ""; }
$coa_filter = $coa_list = ""; if($coas != "all"){ $coa_list = $coas; } else{ $coa_list = implode("','",$coa_code); } $coa_filter = " AND `coa_code` IN ('$coa_list')";
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
            include "headerstyle_wprint_font.php";  
        }
        else{
            include "headerstyle_woprint_font.php"; 
            
        }
       ?>
    </head>
    <body align="center">
        <table class="tbl" align="center" <?php if($excel_type == "print"){ echo ' id="mine"'; } else{ echo 'width="1300px"'; } ?> >
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="15" align="center"><?php echo $row['cdetails']; ?><h5>Expense Ledger Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_expense_ledger.php" method="post" onSubmit="return checkval()">
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
                                    <label>CoA</label>
                                    <select name="coas" id="coas" class="form-control select2">
                                        <option value="all" <?php if($coas == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($coa_code as $ccode){ if($coa_name[$ccode] != ""){ ?>
                                        <option value="<?php echo $ccode; ?>" <?php if($coas == $ccode){ echo "selected"; } ?>><?php echo $coa_name[$ccode]; ?></option>
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
                                    <label>Font Style</label>
                                    <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                        <option value="" <?php if($font_stype == ""){ echo "selected"; } ?>>-Defalut-</option>
                                        <?php
                                        foreach($font_family_code as $i){
                                        ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if($font_stype == $font_family_name[$i]){ echo "selected"; } ?>><?php echo $font_family_name[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Size</label>
                                    <select name="font_size" id="font_size" class="form-control select2">
                                        <?php
                                        foreach($fsizes as $i){
                                        ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if($font_size == $fsizes[$i]){ echo "selected"; } ?>><?php echo $fsizes[$i]; ?></option>
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
            <?php if($excel_type == "print"){ } else{ ?>
                </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
        </table>
        <table id="mine" class="tbl" align="center"  style="width:1300px;">
        <?php } ?>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th id='order_date'>Date</th>
                    <th id='order'>CoA</th>
                    <th id='order'>Transaction No.</th>
                    <th id='order'>Transaction Type</th>
                    <th id='order'>Doc. No.</th>
                    <th id='order'>From Warehouse</th>
                    <th id='order'>Narrations</th>
                    <th id='order_num'>Debit</th>
                    <th id='order_num'>Credit</th>
                </tr>
            </thead>
            <?php
            if(isset($_REQUEST['submit_report']) == true || $_GET['fdate'] != ""){
            ?>
            <tbody class="tbody1" id = "tbody1">
                <?php
                $sql = "SELECT * FROM `account_summary` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$coa_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $tot_bds = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $vendors = $row['vendor'];
                    $locations = $row['location'];
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Transaction No"><?php echo $coa_name[$row['coa_code']]; ?></td>
                    <td title="Transaction No"><?php echo $row['trnum']; ?></td>
                    <td title="Transaction Type"><?php echo $row['etype']; ?></td>
                    <td title="Doc. No."><?php echo $row['dc_no']; ?></td>
                    <td title="From Warehouse"><?php if(!empty($sector_name[$locations])){ echo $sector_name[$locations]; } else if(!empty($ven_name[$vendors])){ echo $ven_name[$vendors]; } else{ } ?></td>
                    <td title="Remarks" style="text-align:left;"><?php echo $row['remarks']; ?></td>
                    <td title="Debit" style="text-align:right;"><?php if($row['crdr'] == "DR"){ echo number_format_ind($row['amount']); } else{ } ?></td>
                    <td title="Credit" style="text-align:right;"><?php if($row['crdr'] == "CR"){ echo number_format_ind($row['amount']); } else{ } ?></td>
                </tr>
                <?php
                    if($row['crdr'] == "CR"){ $coa_crbal_amount += (float)$row['amount']; } else{ $coa_drbal_amount += (float)$row['amount']; }
                }
                ?>
            </tbody>
            <tfoot>
            <tr class="thead4">
                <th colspan="7" style="text-align:center;">Between Days Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($coa_drbal_amount,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($coa_crbal_amount,2)); ?></th>
            </tr>
            <tr class="thead4">
                <th colspan="7" style="text-align:center;">Closing Balance</th>
                <th style="text-align:right;"><?php if($coa_crbal_amount <= $coa_drbal_amount) { echo number_format_ind(round(($coa_drbal_amount - $coa_crbal_amount),2)); } ?></th>
                <th style="text-align:right;"><?php if($coa_crbal_amount > $coa_drbal_amount) { echo number_format_ind(round(($coa_crbal_amount - $coa_drbal_amount),2)); } ?></th>
            </tr>
            </tfoot>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function checkval(){
                var coas = document.getElementById("coas").value;
                if(coas.match("select")){
                    alert("Kindly select CoA to fetch details");
                    document.getElementById("coas").focus();
                    return false;
                }
                else{
                    return true;
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
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('.tbody1 tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                    slnos();
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
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('.tbody1 tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                    slnos();
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
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('.tbody1 tr'));
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
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>