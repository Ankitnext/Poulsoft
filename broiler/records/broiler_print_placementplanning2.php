<?php
//broiler_print_placementplanning2.php.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_print_placementplanning2.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_print_placementplanning2.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
// if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_breeder_breedermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
// if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_breeder_breedermaster.breeder_units;"; mysqli_query($conn,$sql1); }
// if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_breeder_breedermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }

$file_name = "Placement Planning Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code'];$branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $farm_ccode[$row['code']] = $row['farm_code']; }

// $sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
// $query = mysqli_query($conn,$sql); $unit_code = $unit_name = $unit_ccode = array();
// while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; $unit_ccode[$row['code']] = $row['unit_code']; }

$fdate = $tdate = date("Y-m-d"); $branch = $units = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branch = $_POST['branch'];
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
                                <div class="m-2 form-group" style="width:130px;">
                                    <label for="fdate">From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="farms">Branch</label>
                                    <select name="branch" id="branch" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($branch == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branch == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
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

            // $nhtml .= '<th rowspan="3">Transaction No.</th>'; $fhtml .= '<th id="order" rowspan="3">Transaction No.</th>';
            $nhtml .= '<th rowspan="3">Placement Date</th>'; $fhtml .= '<th id="order" rowspan="3">Placement Date</th>';
            $nhtml .= '<th rowspan="3">Branch</th>'; $fhtml .= '<th id="order" rowspan="3">Branch</th>';
            $nhtml .= '<th rowspan="3">Farm</th>'; $fhtml .= '<th id="order" rowspan="3">Farm</th>';
            // $nhtml .= '<th rowspan="3">Village</th>'; $fhtml .= '<th id="order" rowspan="3">Village</th>';
            $nhtml .= '<th rowspan="3">Sq. Feet</th>'; $fhtml .= '<th id="order" rowspan="3">Sq. Feet</th>';
            // $nhtml .= '<th rowspan="3">Line Name</th>'; $fhtml .= '<th id="order" rowspan="3">Line Name</th>';
            // $nhtml .= '<th rowspan="3">Supervisor Name</th>'; $fhtml .= '<th id="order" rowspan="3">Supervisor Name</th>';
            $nhtml .= '<th rowspan="3">Chicks Placement</th>'; $fhtml .= '<th id="order" rowspan="3">Chicks Placement</th>';
            // $nhtml .= '<th colspan="18">Previous Performance</th>'; $fhtml .= '<th id="order" colspan="18">Previous Performance</th>';
            $nhtml .= '<th rowspan="3">Remarks</th>'; $fhtml .= '<th id="order_num" rowspan="3">Remarks</th>';
            // $nhtml .= '<th rowspan="3"></th>'; $fhtml .= '<th id="order_num" rowspan="3"></th>';
            
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
              
                $farm_fltr = ""; if($farms != "all"){ $farm_fltr = " AND `farm_code` = '$farms'"; }
                $branch_fltr = ""; if($branch != "all"){ $branch_fltr = " AND `branch_code` = '$branch'"; }
                $farm_fltr1 = ""; if($farms != "all"){ $farm_fltr1 = " AND `code` = '$farms'"; }
                // $unit_fltr = ""; if($units != "all"){ $unit_fltr = " AND `unit_code` = '$units'"; }

                $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'".$branch_fltr." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $branch_code = $row['branch_code'];
                    $line_code = $row['line_code'];
                    $supervisor_code = $row['supervisor_code'];
                    $farm_capacity = $row['farm_capacity'];
                    $area_name = $row['area_name'];
                }

                $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $branch_name = $line_name = $emp_name = array();
                while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; }

                $sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $line_name[$row['code']] = $row['description']; }

                $sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $emp_name[$row['code']] = $row['name']; }

                $sql = "SELECT * FROM `broiler_placementplan` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'".$branch_fltr." AND `dflag` = '0' ORDER BY `addedtime` ASC";
                $query = mysqli_query($conn,$sql); $tot_sqft = $tot_cpty = $tot_emps = 0; 
                while($row = mysqli_fetch_assoc($query)){
                 
                    $f_code = $farm_ccode[$row['farm_code']];
                    $fname = $farm_name[$row['farm_code']];
                    $branch_names = $branch_name[$row['branch_code']];
                    $village_codes = $row['village_code'];
                    $chicks_places = $row['chicks_place']; if(empty($chicks_places) || $chicks_places == ""){$chicks_places = 0;}
                    $remarks = $row['remarks'];
                    $trnum = $row['trnum'];
                    $dates = $row['date'];
                    $sq_feets = $row['sq_feet']; if(empty($sq_feets) || $sq_feets == ""){ $sq_feets = 0; }
                    $line_names = $line_name[$row['line_code']];
                    $emp_names = $emp_name[$row['supervisor_code']];
                    
                    $lb_batch_code = $row['lb_batch_code'];
                    $lb_fcr = $row['lb_fcr']; if(empty($lb_fcr) || $lb_fcr == ""){ $lb_fcr = 0; }
                    $lb_cfcr = $row['lb_cfcr']; if(empty($lb_cfcr) || $lb_cfcr == ""){ $lb_cfcr = 0; }
                    $lb_mort = $row['lb_mort']; if(empty($lb_mort) || $lb_mort == ""){ $lb_mort = 0; }
                    $lb_avg_bodywt = $row['lb_avg_bodywt']; if(empty($lb_avg_bodywt) || $lb_avg_bodywt == ""){ $lb_avg_bodywt = 0; }
                    $lb_mean_age = $row['lb_mean_age']; if(empty($lb_mean_age) || $lb_mean_age == ""){ $lb_mean_age = 0; }
                    $lb_gc_date = $row['lb_gc_date'];
                    
                    $blb_batch_code = $row['blb_batch_code'];
                    $blb_fcr = $row['blb_fcr']; if(empty($blb_fcr) || $blb_fcr == ""){ $blb_fcr = 0; }
                    $blb_cfcr = $row['blb_cfcr']; if(empty($blb_cfcr) || $blb_cfcr == ""){ $blb_cfcr = 0; }
                    $blb_mort = $row['blb_mort']; if(empty($blb_mort) || $blb_mort == ""){ $blb_mort = 0; }
                    $blb_avg_bodywt = $row['blb_avg_bodywt']; if(empty($blb_avg_bodywt) || $blb_avg_bodywt == ""){ $blb_avg_bodywt = 0; }
                    $blb_mean_age = $row['blb_mean_age']; if(empty($blb_mean_age) || $blb_mean_age == ""){ $blb_mean_age = 0; }
                    $blb_gc_date = $row['blb_gc_date']; 

                    $olb_batch_code = $row['olb_batch_code'];
                    $olb_fcr = $row['olb_fcr']; if(empty($olb_fcr) || $olb_fcr == ""){ $olb_fcr = 0; }
                    $olb_cfcr = $row['olb_cfcr']; if(empty($olb_cfcr) || $olb_cfcr == ""){ $olb_cfcr = 0; }
                    $olb_mort = $row['olb_mort']; if(empty($olb_mort) || $olb_mort == ""){ $olb_mort = 0; } 
                    $olb_avg_bodywt = $row['olb_avg_bodywt']; if(empty($olb_avg_bodywt) || $olb_avg_bodywt == ""){ $olb_avg_bodywt = 0; }
                    $olb_mean_age = $row['olb_mean_age']; if(empty($olb_mean_age) || $olb_mean_age == ""){ $olb_mean_age = 0; }
                    $olb_gc_date = $row['olb_gc_date'];
                    

                    $html .= '<tr>';
                    // $html .= '<td>'.$trnum.'</td>';
                    $html .= '<td>'.date("d.m.Y",strtotime($dates)).'</td>';
                    $html .= '<td>'.$branch_names.'</td>';
                    $html .= '<td>'.$fname.'</td>';
                    // $html .= '<td>'.$village_codes.'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($sq_feets,5))).'</td>';
                    // $html .= '<td>'.$line_names.'</td>';
                    // $html .= '<td>'.$emp_names.'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($chicks_places,2))).'</td>';
                    $html .= '<td>'.$remarks.'</td>';

                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($lb_cfcr,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($lb_mort,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($lb_avg_bodywt,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($lb_mean_age,5))).'</td>';
                    // $html .= '<td>'.date("d.m.Y",strtotime($lb_gc_date)).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($blb_fcr,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($blb_cfcr,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($blb_mort,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($blb_avg_bodywt,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($blb_mean_age,5))).'</td>';
                    // $html .= '<td>'.date("d.m.Y",strtotime($blb_gc_date)).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($olb_fcr,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($olb_cfcr,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($olb_mort,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($olb_avg_bodywt,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($olb_mean_age,5))).'</td>';
                    // $html .= '<td>'.date("d.m.Y",strtotime($olb_gc_date)).'</td>';
                    
                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($bird_capacity,5)).'</td>';
                    
                    $html .= '</tr>';

                    $tsq_feets += (float)$sq_feets;
                    $tchicks_places += (float)$chicks_places;

                    // $tlb_cfcr += (float)$lb_cfcr;
                    // $tlb_mort += (float)$lb_mort;
                    // $tlb_avg_bodywt += (float)$lb_avg_bodywt;
                    // $tlb_mean_age += (float)$lb_mean_age;

                    // $tblb_fcr += (float)$blb_fcr;
                    // $tblb_cfcr += (float)$blb_cfcr;
                    // $tblb_mort += (float)$blb_mort;
                    // $tblb_avg_bodywt += (float)$blb_avg_bodywt;
                    // $tblb_mean_age += (float)$blb_mean_age;
                    
                    // $tolb_fcr += (float)$olb_fcr;
                    // $tolb_cfcr += (float)$olb_cfcr;
                    // $tolb_mort += (float)$olb_mort;
                    // $tolb_avg_bodywt += (float)$olb_avg_bodywt;
                    // $tolb_mean_age += (float)$olb_mean_age;
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="3">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tsq_feets,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tchicks_places,5))).'</th>';
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