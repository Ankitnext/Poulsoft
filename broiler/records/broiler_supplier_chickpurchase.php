<?php
//broiler_supplier_chickpurchase.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_supplier_chickpurchase.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_supplier_chickpurchase.php?db=$db&userid=".$user_code;
}

$file_name = "Supplier Chick Purchase";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

//Check Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Purchase Report' AND `field_function` = 'Download Multiple Files' AND (`user_access` = 'all' OR `user_access` = '$user_code')";
$query = mysqli_query($conn, $sql); $ccount4 = mysqli_num_rows($query); $dlf_flag = 0;
if($ccount4 > 0){ while($row3 = mysqli_fetch_assoc($query)){ $dlf_flag = $row3['flag']; } } if($dlf_flag == ""){ $dlf_flag =  0; }

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'  ".$branch_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors`  WHERE `dflag` = '0'".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $vendor_code = $vendor_name = array();
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_vehicle` WHERE `dflag` = '0'";
$query = mysqli_query($conn,$sql);  $vehicle_code = $vehicle_name = array();
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$fdate = $tdate = date("Y-m-d"); $vendors = $branches = $sectors = $upload_status = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $branches = $_POST['branches'];
    $sectors = $_POST['sectors'];
    $upload_status = $_POST['upload_status'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="15" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
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
                                    <label>Supplier</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Sectors</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($sectors == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Upload Status</label>
                                    <select name="upload_status" id="upload_status" class="form-control select2">
                                        <option value="all" <?php if($upload_status == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="1" <?php if($upload_status == "1"){ echo "selected"; } ?>>-Uploaded-</option>
                                        <option value="0" <?php if($upload_status == "0"){ echo "selected"; } ?>>-Not Uploaded-</option>
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
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
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
            if(isset($_POST['submit_report']) == true){
                $sector_filter = $vendor_filter = $upload_filter = "";
                if($sectors != "all"){ $sector_filter = " AND `warehouse` IN ('$sectors')"; }
                else if($branches != "all"){
                    $farm_list = ""; foreach($farm_code as $fcode){ if(!empty($farm_branch[$fcode]) && $farm_branch[$fcode] == $branches){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
                    $sector_filter = " AND `warehouse` IN ('$farm_list')";
                } else{ }
                
                if($vendors != "all"){ $vendor_filter = " AND `vcode` = '$vendors'"; }
                if($upload_status == "1"){ $upload_filter = " AND `file_url1` IS NOT NULL AND `file_url1` != '' "; } else if($upload_status == "0"){ $upload_filter = " AND ( `file_url1` IS  NULL OR `file_url1` = '' )"; }
                
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $chick_code = $chick_name = array();
                while($row = mysqli_fetch_assoc($query)){ $chick_code[$row['code']] = $row['code']; $chick_name[$row['code']] = $row['description']; }

                //$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC";
                //$query = mysqli_query($conn,$sql);
                //while($row = mysqli_fetch_assoc($query)){ $chick_code[$row['code']] = $row['code']; $chick_name[$row['code']] = $row['description']; }
                
                $html = '';
                $html .= '<thead class="thead3" id="head_names">';
                $html .= '<tr style="text-align:center;" align="center">';
                $html .= '<th style="text-align:center;" id="order_date">Date</th>';
                $html .= '<th style="text-align:center;" id="order_date">Received Date</th>';
                $html .= '<th style="text-align:center;" id="order">Supplier</th>';
                $html .= '<th style="text-align:center;" id="order">Dc. No.</th>';
                $html .= '<th style="text-align:center;" id="order">Invoice</th>';
                $html .= '<th style="text-align:center;" id="order">Item Code</th>';
                $html .= '<th style="text-align:center;" id="order">Item</th>';
                $html .= '<th style="text-align:center;" id="order_num">Sent Qty</th>';
                $html .= '<th style="text-align:center;" id="order_num">Chargeable Qty</th>';
                $html .= '<th style="text-align:center;" id="order_num">Free qty</th>';
                $html .= '<th style="text-align:center;" id="order_num">Net Quantity</th>';
                $html .= '<th style="text-align:center;" id="order_num">Rate</th>';
                $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                $html .= '<th style="text-align:center;" id="order_num">Vehicle No</th>';
                $html .= '<th style="text-align:center;" id="order_num">Farm/Warehouse</th>';
                $html .= '<th style="text-align:center;" id="order_num">Batch Code</th>';
                $html .= '<th style="text-align:center;" id="order_num">Batch No.</th>';
                $html .= '<th style="text-align:center;" id="order_num">Remarks</th>';
                $html .= '<th style="text-align:center;" id="order_num">Upload Status</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody class="tbody1" id="tbody1">';

                /*Fetch Item Details*/
                $item_list = ""; $item_list = implode("','", $chick_code);

                // $sql = "SELECT * FROM `inv_sectors`  WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                // while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
                 //Purchase
                $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `icode` IN ('$item_list')".$vendor_filter."".$sector_filter."".$upload_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $slno = 0; $rdate = ""; $file_list = array();
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    $date = date("d.m.Y",strtotime($row['date']));
                    //$rdate = date("d.m.Y",strtotime($row['date']));
                    $vname = $vendor_name[$row['vcode']];
                    $dcno = $row['billno'];
                    $trnum = $row['trnum'];
                    $icode = $row['icode'];
                    $iname = $chick_name[$row['icode']];
                    $snt_qty = $row['snt_qty'];
                    $rcd_qty = $row['rcd_qty'];
                    $fre_qty = $row['fre_qty'];
                    $net_qty = ((float)$rcd_qty + (float)$fre_qty);
                    $rate = $row['rate'];
                    $item_tamt = $row['item_tamt'];
                    if(!empty($vehicle_name[$row['vehicle_code']])){ $tname = $vehicle_name[$row['vehicle_code']]; } else{ $tname = $row['vehicle_code']; }
                    $sname = $farm_name[$row['warehouse']];
                    $batch_c = $row['farm_batch'];
                    $batch_n = $batch_name[$row['farm_batch']];
                    $remarks = $row['remarks'];

                    $file_count = 0;
                    if((int)$dlf_flag == 1){
                        if($row['file_url1'] != ""){ $file_count++; $link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/".$row['file_url1']; $file_list[$link] = $link; }
                        if($row['file_url2'] != ""){ $file_count++; $link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/".$row['file_url2']; $file_list[$link] = $link; }
                        if($row['file_url3'] != ""){ $file_count++; $link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/".$row['file_url3']; $file_list[$link] = $link; }
                    }

                    $html .= '<tr>';
                    $html .= '<td style="text-align:left;">'.$date.'</td>';
                    $html .= '<td style="text-align:left;">'.$rdate.'</td>';
                    $html .= '<td style="text-align:left;">'.$vname.'</td>';
                    $html .= '<td style="text-align:left;">'.$dcno.'</td>';
                    $html .= '<td style="text-align:left;">'.$trnum.'</td>';
                    $html .= '<td style="text-align:left;">'.$icode.'</td>';
                    $html .= '<td style="text-align:left;">'.$iname.'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($snt_qty)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($rcd_qty)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($fre_qty)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($net_qty)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind($rate).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind($item_tamt).'</td>';
                    $html .= '<td style="text-align:left;">'.$tname.'</td>';
                    $html .= '<td style="text-align:left;">'.$sname.'</td>';
                    $html .= '<td style="text-align:left;">'.$batch_c.'</td>';
                    $html .= '<td style="text-align:left;">'.$batch_n.'</td>';
                    $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                    if((int)$file_count > 0){ $html .= '<td style="text-align:left;">Uploaded</td>'; } else{ $html .= '<td style="text-align:left;">Not Uploaded</td>'; }
                    $html .= '</tr>';

                    $t_sqty += (float)$snt_qty;
                    $t_rqty += (float)$rcd_qty;
                    $t_fqty += (float)$fre_qty;
                    $t_nqty += (float)$net_qty;
                    $t_iamt += (float)$item_tamt;
                }

                if((float)$t_rqty > 0){ $avg_prc = round((((float)$t_iamt / (float)$t_rqty)),2); }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="7">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($t_sqty)).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($t_rqty)).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($t_fqty)).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($t_nqty)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($avg_prc).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($t_iamt).'</th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '</tr>';
                $html .= '</tfoot>';

                echo $html;
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
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
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
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search_table');
                const table = document.getElementById('main_table');
                const tableBody = table.querySelector('tbody');

                searchInput.addEventListener('input', () => {
                    const filter = searchInput.value.toLowerCase();
                    const rows = tableBody.querySelectorAll('tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    //document.getElementById("head_names").innerHTML = "";
                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}

                    //
                    /*html = '';
                    html += '<tr style="text-align:center;" align="center">';
                    html += '<th style="text-align:center;" id="order_date">Date</th>';
                    html += '<th style="text-align:center;" id="order_date">Received Date</th>';
                    html += '<th style="text-align:center;" id="order">Supplier</th>';
                    html += '<th style="text-align:center;" id="order">Dc. No.</th>';
                    html += '<th style="text-align:center;" id="order">Invoice</th>';
                    html += '<th style="text-align:center;" id="order">Item Code</th>';
                    html += '<th style="text-align:center;" id="order">Item</th>';
                    html += '<th style="text-align:center;" id="order_num">Sent Qty</th>';
                    html += '<th style="text-align:center;" id="order_num">Chargeable Qty</th>';
                    html += '<th style="text-align:center;" id="order_num">Free qty</th>';
                    html += '<th style="text-align:center;" id="order_num">Net Quantity</th>';
                    html += '<th style="text-align:center;" id="order_num">Rate</th>';
                    html += '<th style="text-align:center;" id="order_num">Amount</th>';
                    html += '<th style="text-align:center;" id="order_num">Vehicle No</th>';
                    html += '<th style="text-align:center;" id="order_num">Farm/Warehouse</th>';
                    html += '<th style="text-align:center;" id="order_num">Remarks</th>';
                    html += '<th style="text-align:center;" id="order_num">Upload Status</th>';
                    html += '</tr>';
                    $('#head_names').append(html);*/
                }
                else{ }
            }
        </script>
        <script>
            function fetch_row_height(){
                var table_elements = document.querySelector("table>tbody");
                var i; var max_height = 0;
                for(i = 1; i <= table_elements.rows.length; i++){
                    var row_selector = "table>tbody>tr:nth-child(" + [i] + ")";
                    var table_row = document.querySelector(row_selector);
                    var vertical_spacing = window.getComputedStyle(table_row).getPropertyValue("-webkit-border-vertical-spacing");
                    var margin_top = window.getComputedStyle(table_row).getPropertyValue("margin-top");
                    var margin_bottom = window.getComputedStyle(table_row).getPropertyValue("margin-bottom");
                    var row_height= parseInt(vertical_spacing, 10)+parseInt(margin_top, 10)+parseInt(margin_bottom, 10)+table_row.offsetHeight;
                    if(max_height <= row_height){
                        max_height = row_height;
                    }
                }
                //alert("The height is: "+max_height+"px");
                document.getElementById("thead2_empty_row").style.height = max_height+"px";
            }
            fetch_row_height();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>