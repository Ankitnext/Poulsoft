<?php
//broiler_receipt.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

   /*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_cus_lines", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.breeder_cus_lines LIKE poulso6_admin_broiler_broilermaster.breeder_cus_lines;"; mysqli_query($conn,$sql1); }



$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cline_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cline_code` VARCHAR(100) NULL DEFAULT NULL AFTER `aadhar_no`"; mysqli_query($conn,$sql); }
      


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

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_cus_lines", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.breeder_cus_lines LIKE poulso6_admin_broiler_broilermaster.breeder_cus_lines;"; mysqli_query($conn,$sql1); }
// if(in_array("broiler_item_brands", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_item_brands LIKE poulso6_admin_broiler_broilermaster.broiler_item_brands;"; mysqli_query($conn,$sql1); }
// if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }
// if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE IF NOT EXISTS $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }


$sql = "SELECT * FROM `main_access` "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_db_code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $user_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}
$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mode_name[$row['code']] = $row['description']; $mode_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $method_name[$row['code']] = $row['description']; $method_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name']; $vendor_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cline_code[$row['code']] = $row['code']; $cline_name[$row['code']] = $row['description']; }

//$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
//while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
$fdate = $tdate = date("Y-m-d"); $groups = $vendors = $sectors = $methods = $clines = "all"; $excel_type = "display"; $url = "";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $groups = $_POST['groups'];
    $vendors = $_POST['vendors'];
    $sectors = $_POST['sectors'];
    $methods = $_POST['methods'];
    $clines = $_POST['cline'];

    if($vendors != "all"){ $vcodes = " AND `code` = '$vendors'"; }
    else if($groups != "all"){
        $cus_list = "";
        foreach($vendor_code as $vcode){
            if(!empty($vendor_group[$vcode]) && $vendor_group[$vcode] == $groups){ if($cus_list == ""){ $cus_list = $vcode; } else{ $cus_list = $cus_list."','".$vcode; } }
        }
        $vcodes = " AND `code` IN ('$cus_list')";
    }
    else{ $vcodes = ""; }

    $cline_fltr = "";
    if($clines != "all"){ $cline_fltr = " AND `cline_code` IN ('$clines')"; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$vcodes."".$cline_fltr." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
    while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name'];$cus_alist[$row['code']] = $row['code']; }
    $cus_list = implode("','",$cus_alist);
    $customer_filter = " AND `ccode` IN ('$cus_list')";
    // echo $customer_filter;
    //   die();



    if($sectors != "all"){ $wcodes = " AND `warehouse` = '$sectors'"; } else{ $wcodes = ""; }
    if($methods != "all"){ $method_fltr = " AND `method` = '$methods'"; } else{ $method_fltr = ""; }
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_receipt-Excel.php?fdate=".$fdate."&tdate=".$tdate."&vendors=".$vendors."&sectors=".$sectors."&methods=".$methods;
}
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
                    <th colspan="13" align="center"><?php echo $row['cdetails']; ?><h5>Receipt Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_receipt.php" method="post">
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
                                    <label>Group</label>
                                    <select name="groups" id="groups" class="form-control select2" onchange="fetch_group_list();">
                                        <option value="all" <?php if($groups == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($grp_code as $gcode){ if($grp_name[$gcode] != ""){ ?>
                                        <option value="<?php echo $gcode; ?>" <?php if($groups == $gcode){ echo "selected"; } ?>><?php echo $grp_name[$gcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php
                                        foreach($vendor_code as $cust){
                                            if($vendor_name[$cust] != ""){
                                                if($groups == "all" || $vendor_group[$cust] == $groups){
                                        ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php
                                                }
                                            }
                                        }
                                    ?>
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
                                    <label>Method</label>
                                    <select name="methods" id="methods" class="form-control select2">
                                        <option value="all" <?php if($methods == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($method_code as $whcode){ if($method_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($methods == $whcode){ echo "selected"; } ?>><?php echo $method_name[$whcode]; ?></option>
                                        <?php } } ?>
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
                    <th id='order_date'>Date</th>
                    <th id='cus_ccode'>Code</th>
                    <th id='order'>Customer</th>
                    <th id='order'>Invoice</th>
                    <th id='order'>Dc No.</th>
                    <th id='order'>Mode</th>
                    <th id='order'>Method</th>
                    <th id='order_num'>Amount</th>
                    <th id='order'>Farm/Warehouse</th>
                    <th id='order'>Remarks</th>
                    <th id='order'>Added Emp.</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$customer_filter."".$wcodes."".$method_fltr." AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Code"><?php echo $vendor_ccode[$row['ccode']]; ?></td>
                    <td title="Customer"><?php echo $vendor_name[$row['ccode']]; ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['docno']; ?></td>
                    <td title="Mode"><?php echo $mode_name[$row['mode']]; ?></td>
                    <td title="Method"><?php echo $method_name[$row['method']]; ?></td>
                    <td title="Total Amount" style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
                    <td title="Farm/Warehouse"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td>
                    <td title="Remarks"><?php echo $user_name[$emp_db_code[$row['addedemp']]]; ?></td>
                </tr>
                <?php
                    $tot_amt = $tot_amt + $row['amount'];
                }
                ?>
            </tbody>
            <tfoot style='background-image: linear-gradient(#F5EEF8,#F5EEF8);'>
            <tr class="thead4">
                <th colspan="7" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
                <th colspan="3" style="text-align:right;"></th>
            </tr>
            </tfoot>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_group_list(){
                var gcode = document.getElementById("groups").value;
                removeAllOptions(document.getElementById("vendors"));
                myselect = document.getElementById("vendors"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(gcode != "all"){
                <?php
                    foreach($vendor_code as $vcode){
                        $icats = $vendor_group[$vcode];
                        echo "if(gcode == '$icats'){";
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
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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
                   // slnos();
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
                   // slnos();
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
           /* function slnos(){
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
    </body>
</html>
<?php
include "header_foot.php";
?>