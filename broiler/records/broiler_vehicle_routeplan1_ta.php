<?php
//broiler_vehicle_routeplan1_ta.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_vehicle_routeplan1_ta.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_vehicle_routeplan1_ta.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_sc_saleorder", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sc_saleorder LIKE poulso6_admin_broiler_broilermaster.broiler_sc_saleorder;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_routeplan", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_routeplan LIKE poulso6_admin_broiler_broilermaster.broiler_routeplan;"; mysqli_query($conn,$sql1); }

$file_name = "Vehicle Wise Route Plan Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `broiler_routeplan` WHERE `dflag` = '0'";
$query = mysqli_query($conn,$sql); $vehicle_no = $driver_name = array();
while($row = mysqli_fetch_assoc($query)){ $vehicle_no[$row['vehicle']] = $row['vehicle']; $driver_name[$row['driver']] = $row['driver']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }


$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $customers = $vehicles = $sect = $drivers = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $customers = $_POST['customers'];
    $vehicles = $_POST['vehicles'];
    $drivers = $_POST['drivers'];
    $excel_type = $_POST['export'];
    $sect  = $_POST['sector'];

    if($sect == "all"){ $sect_filter = ""; } else{ $sect_filter = " AND `warehouse` = '$sect'"; }
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="customers">Customer</label>
                                    <select name="customers" id="customers" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($customers == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($cus_code as $bcode){ if($cus_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($customers == $bcode){ echo "selected"; } ?>><?php echo $cus_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="vehicles">Vehicle</label>
                                    <select name="vehicles" id="vehicles" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($vehicles == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vehicle_no as $bcode){ if($vehicle_no[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($vehicles == $bcode){ echo "selected"; } ?>><?php echo $vehicle_no[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="drivers">Driver</label>
                                    <select name="drivers" id="drivers" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($drivers == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($driver_name as $bcode){ if($driver_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($drivers == $bcode){ echo "selected"; } ?>><?php echo $driver_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="m-2 form-group">
                                    <label>Sector</label>
                                    <select name="sector" id="sector" class="form-control select2" >
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $icats){ if($sector_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($sect == $icats){ echo "selected"; } ?>><?php echo $sector_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
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
            $html = $nhtml = $fhtml = ''; $cflag = $i_cnt = 0;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';
            $nhtml .= '<th>Sl.no</th>'; $fhtml .= '<th>Sl.no</th>';
            $nhtml .= '<th>Order Date</th>'; $fhtml .= '<th id="order_date">Order Date</th>';
            $nhtml .= '<th>Vehicle No.</th>'; $fhtml .= '<th id="order">Vehicle No.</th>';
            $nhtml .= '<th>Route / Line</th>'; $fhtml .= '<th id="order">Route / Line</th>';
            $nhtml .= '<th>Supply Order</th>'; $fhtml .= '<th id="order_num">Supply Order</th>';
            $nhtml .= '<th>Order No.</th>'; $fhtml .= '<th id="order">Order No.</th>';
            $nhtml .= '<th>Customer</th>'; $fhtml .= '<th id="order">Customer</th>';
            $nhtml .= '<th>Item</th>'; $fhtml .= '<th id="order">Item</th>';
            $nhtml .= '<th>Box / Crates</th>'; $fhtml .= '<th id="order_num">Box / Crates</th>';
            $nhtml .= '<th>Order Qty</th>'; $fhtml .= '<th id="order_num">Order Qty</th>';
            $nhtml .= '<th>Delivery Date</th>'; $fhtml .= '<th id="order_date">Delivery Date</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order_date">Remarks</th>';
            
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                $cus_fltr = ""; if($customers != "all"){ $cus_fltr = " AND `vcode` = '$customers'"; }
                $veh_fltr = ""; if($vehicles != "all"){ $veh_fltr = " AND `vehicle` = '$vehicles'"; }
                $drv_fltr = ""; if($drivers != "all"){ $drv_fltr = " AND `driver` = '$drivers'"; }
                $sln = 1;


            $sql = "SELECT * FROM `broiler_routeplan` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cus_fltr.$veh_fltr.$drv_fltr.$sect_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`, `trnum` ASC";
            $query = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($query)){
                $so_date = date("d.m.Y", strtotime($row['so_date']));
                $vehicle = $row['vehicle'];
                $route_no = $row['route_no'];
                $sorder_no = $row['sorder_no'];
                $so_trnum = $row['so_trnum'];
                $cust_name = $cus_name[$row['vcode']];
                $it_name = $item_name[$row['item_code']];
                $boxes = $row['boxes'];
                $order_qty = $row['order_qty'];
                $delivery_date = date("d.m.Y", strtotime($row['delivery_date']));

                // Fetch remarks from broiler_sc_saleorder
                $remark_sql = "SELECT remarks FROM `broiler_sc_saleorder` WHERE `trnum` = '$so_trnum' AND `active` = '1' AND `dflag` = '0' ORDER BY `trnum`,`id` ASC";
                $remark_query = mysqli_query($conn, $remark_sql);
                $remarks_arr = array();

                while($r_row = mysqli_fetch_assoc($remark_query)){
                    if (!empty($r_row['remarks'])) {
                        $remarks_arr[] = $r_row['remarks'];
                    }
                }

                $all_remarks = implode(", ", $remarks_arr); // Join all remarks with comma

                // Output HTML row
                $html .= '<tr>';
                $html .= '<td>'.$sln.'</td>';
                $html .= '<td style="text-align:right;" class="dates">'.$so_date.'</td>';
                $html .= '<td>'.$vehicle.'</td>';
                $html .= '<td>'.$route_no.'</td>';
                $html .= '<td>'.$sorder_no.'</td>';
                $html .= '<td>'.$so_trnum.'</td>';
                $html .= '<td>'.$cust_name.'</td>';
                $html .= '<td>'.$it_name.'</td>';
                $html .= '<td style="text-align:right;">'.str_replace(".00", "", number_format_ind(round($boxes, 5))).'</td>';
                $html .= '<td style="text-align:right;">'.number_format_ind(round($order_qty, 5)).'</td>';
                $html .= '<td style="text-align:right;" class="dates">'.$delivery_date.'</td>';
                $html .= '<td>'.$all_remarks.'</td>';
                $html .= '</tr>';

                $tot_boxes += (float)$boxes;
                $tot_oqty += (float)$order_qty;
                $sln++;
            }

                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="8">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_boxes,5))).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_oqty,5)).'</th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '</tr>';
                $html .= '</tfoot>';
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function table_sort() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {

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
                    cdate_format1();
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
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
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>