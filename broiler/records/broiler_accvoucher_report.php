<?php
//broiler_accvoucher_report.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_accvoucher_report.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_accvoucher_report.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

/*Check for Table Availability*/
// $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
// $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
// if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_breeder_breedermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
// if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_breeder_breedermaster.breeder_units;"; mysqli_query($conn,$sql1); }
// if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_breeder_breedermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }
// if(in_array("breeder_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_shed_allocation LIKE poulso6_admin_breeder_breedermaster.breeder_shed_allocation;"; mysqli_query($conn,$sql1); }

$file_name = "Account Vouchers Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT DISTINCT(type) FROM `account_vouchers` WHERE `dflag` = '0' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $acc_code = $acc_name = array();
while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['type']] = $row['type']; $acc_name[$row['type']] = $row['type']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = $unit_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description'];  }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = $shed_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description'];  }

$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $voc = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $voc = $_POST['voc'];
    $farms = $_POST['farms'];
    $sheds = $_POST['sheds'];
    $excel_type = $_POST['export'];
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
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div> <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                 <div class="m-2 form-group" style="width:230px;">
                                    <label for="voc">Voucher Type</label>
                                    <select name="voc" id="voc" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($voc == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($acc_code as $bcode){ if($acc_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($voc == $bcode){ echo "selected"; } ?>><?php echo $acc_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> 
                                <!--<div class="m-2 form-group" style="width:230px;">
                                    <label for="unit_code">Unit</label>
                                    <select name="units" id="units" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($units == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($unit_code as $ucode){ if($unit_name[$ucode] != ""){ ?>
                                        <option value="<?php echo $ucode; ?>" <?php if($units == $ucode){ echo "selected"; } ?>><?php echo $unit_name[$ucode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> <div class="m-2 form-group" style="width:230px;">
                                    <label for="unit_code">Shed</label>
                                    <select name="sheds" id="sheds" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($sheds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($shed_code as $scode){ if($shed_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($sheds == $scode){ echo "selected"; } ?>><?php echo $shed_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> -->
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

           
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Date</th>';
            $nhtml .= '<th>Voucher type</th>'; $fhtml .= '<th id="order_num">Voucher type</th>';
            $nhtml .= '<th>Voucher No.</th>'; $fhtml .= '<th id="order_num">Voucher No.</th>';
            $nhtml .= '<th>fcoa</th>'; $fhtml .= '<th id="order_num">fcoa</th>';
            $nhtml .= '<th>tcoa</th>'; $fhtml .= '<th id="order_num">tcoa</th>';
            $nhtml .= '<th>Debit/Credit Amount</th>'; $fhtml .= '<th id="order_num">Debit/Credit Amount</th>';
            // $nhtml .= '<th>Credit Amount</th>'; $fhtml .= '<th id="order_num">Credit Amount</th>';
            $nhtml .= '<th>Cheque No.</th>'; $fhtml .= '<th id="order_num">Cheque No.</th>';
            $nhtml .= '<th>Narration</th>'; $fhtml .= '<th id="order_num">Narration</th>';
          
            
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
              
                // $farm_fltr = ""; if($farms != "all"){ $farm_fltr = " AND `farm_code` = '$farms'"; }
                // $unit_fltr = ""; if($units != "all"){ $unit_fltr = " AND `unit_code` = '$units'"; }
                // $shed_fltr = ""; if($sheds != "all"){ $shed_fltr = " AND `shed_code` = '$sheds'"; }
                 $acc_fltr = ""; if($voc != "all"){ $acc_fltr = " AND `type` = '$voc'"; }
                 $acc2_fltr = ""; if($voc != "all"){ $acc2_fltr = " AND `etype` = '$voc'"; }

                $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

                $sql = "SELECT * FROM `account_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$acc_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql); $tot_sqft = $tot_cpty = $tot_emps = 0;
                while($row = mysqli_fetch_assoc($query)){
                 
                    $trnum = $row['trnum'];
                    $cdate[$row['trnum']] = date("d-m-Y",strtotime($row['date']));
                    $trnums[$row['trnum']] = $row['trnum'];

                    $vtype[$trnum] = $row['type'];
                    $fcoas[$trnum] = $coa_name[$row['fcoa']];
                    $tcoas[$trnum] = $coa_name[$row['tcoa']];
                    // $amount = $row['amount'];
                    $cheque_no[$trnum] = $row['cheque_no'];
                    $remarks[$trnum] = $row['remarks'];
                    // $diff = $row['bll_dr'] - $row['bll_max_cut'];
                    // $bhl_dr = $row['bhl_dr'];
                    // $bhl_max_cut = $row['bhl_max_cut'];
                    // $diff2 = $row['bhl_dr'] - $row['bhl_max_cut'];
                }  

                if (!empty($trnums) && is_array($trnums)) { 
                    $tr_num = implode("','", $trnums); 
                    $tr_fltr = "AND `trnum` IN ('$tr_num')"; 
                } else { 
                    $tr_fltr = ""; 
                }

                $sql = "SELECT * FROM `account_summary` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$acc2_fltr." AND `active` = '1' AND `dflag` = '0' AND `crdr` = 'DR' ".$tr_fltr." ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql); $tot_sqft = $tot_cpty = $tot_emps = 0;
                while($row = mysqli_fetch_assoc($query)){
                 
                    // $cdate = date("d-m-Y",strtotime($row['date']));
                    $trnum = $row['trnum'];
                    $deb_amt[$trnum] = $row['amount'];
                }
                $sql = "SELECT * FROM `account_summary` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$acc2_fltr." AND `active` = '1' AND `dflag` = '0' AND `crdr` = 'CR' ".$tr_fltr." ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql); $tot_sqft = $tot_cpty = $tot_emps = 0;
                while($row = mysqli_fetch_assoc($query)){
                 
                    // $cdate = date("d-m-Y",strtotime($row['date']));
                    $trnum = $row['trnum'];
                    $crd_amt[$trnum] = $row['amount'];
                }
// print_r($trnums);
                foreach($trnums as $tr){

                    $html .= '<tr>';
                    $html .= '<td>'.$cdate[$tr].'</td>';
                    $html .= '<td>'.$vtype[$tr].'</td>';
                    $html .= '<td>'.$trnum.'</td>';
                    $html .= '<td>'.$fcoas[$tr].'</td>';
                    $html .= '<td>'.$tcoas[$tr].'</td>';
                    $html .= '<td style="text-align:right;">'.rtrim(rtrim(number_format_ind(round($deb_amt[$tr], 5)), '0'), '.').'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($crd_amt[$tr],5))).'</td>';
                    $html .= '<td>'.$cheque_no[$tr].'</td>';
                    $html .= '<td>'.$remarks[$tr].'</td>';
                    $html .= '</tr>';

                    $tdebt += (float)$deb_amt[$tr];
                }
                
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="5">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tdebt,5))).'</th>';
                $html .= '<th style="text-align:right;" colspan="3"></th>';
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