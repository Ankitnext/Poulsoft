<?php
//breeder_breed_standards.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Breed Standards Report";
    include "header_head.php";
    $form_path = "breeder_breed_standards.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Breed Standards Report";
    include "header_head.php";
    $form_path = "breeder_breed_standards.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_breed_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_breed_details LIKE poulso6_admin_breeder_breedermaster.breeder_breed_details;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_breed_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_breed_standards LIKE poulso6_admin_breeder_breedermaster.breeder_breed_standards;"; mysqli_query($conn,$sql1); }

$file_name = "Breed Standards Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `breeder_breed_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $br_code = $br_name = array();
while($row = mysqli_fetch_assoc($query)){ $br_code[$row['code']] = $row['code']; $br_name[$row['code']] = $row['description']; }

$breeds = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $breeds = $_POST['breeds'];
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
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="breeds">Breed</label>
                                    <select name="breeds" id="breeds" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($breeds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($br_code as $bcode){ if($br_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($breeds == $bcode){ echo "selected"; } ?>><?php echo $br_name[$bcode]; ?></option>
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

            $nhtml .= '<th>Age (In Weeks)</th>'; $fhtml .= '<th id="order_num">Age (In Weeks)</th>';
            $nhtml .= '<th>Livability</th>'; $fhtml .= '<th id="order_num">Livability</th>';
            $nhtml .= '<th>F.Feed/Bird (gms)</th>'; $fhtml .= '<th id="order_num">F.Feed/Bird (gms)</th>';
            $nhtml .= '<th>M.Feed/Bird (gms)</th>'; $fhtml .= '<th id="order_num">M.Feed/Bird (gms)</th>';
            $nhtml .= '<th>HD%</th>'; $fhtml .= '<th id="order_num">HD%</th>';
            $nhtml .= '<th>HE%</th>'; $fhtml .= '<th id="order_num">HE%</th>';
            $nhtml .= '<th>HHP/Week</th>'; $fhtml .= '<th id="order_num">HHP/Week</th>';
            $nhtml .= '<th>C.HHP/Week</th>'; $fhtml .= '<th id="order_num">C.HHP/Week</th>';
            $nhtml .= '<th>HHE/Week</th>'; $fhtml .= '<th id="order_num">HHE/Week</th>';
            $nhtml .= '<th>C.HHE/Week</th>'; $fhtml .= '<th id="order_num">C.HHE/Week</th>';
            $nhtml .= '<th>Hatch %</th>'; $fhtml .= '<th id="order_num">Hatch %</th>';
            $nhtml .= '<th>Chicks/week</th>'; $fhtml .= '<th id="order_num">Chicks/week</th>';
            $nhtml .= '<th>C.Chicks/ week</th>'; $fhtml .= '<th id="order_num">C.Chicks/ week</th>';
            $nhtml .= '<th>Egg Weight</th>'; $fhtml .= '<th id="order_num">Egg Weight</th>';
            $nhtml .= '<th>F.B.Wt(gms)</th>'; $fhtml .= '<th id="order_num">F.B.Wt(gms)</th>';
            $nhtml .= '<th>M.B.Wt(gms)</th>'; $fhtml .= '<th id="order_num">M.B.Wt(gms)</th>';
            
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                $brd_fltr = ""; if($breeds != "all"){ $brd_fltr = " AND `breed_code` = '$breeds'"; }

                $sql = "SELECT * FROM `breeder_breed_standards` WHERE `active` = '1'".$brd_fltr." AND `dflag` = '0' ORDER BY `breed_age` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                 
                    $breed_age = $row['breed_age'];
                    $livability = $row['livability'];
                    $ffeed_pbird = $row['ffeed_pbird'];
                    $mfeed_pbird = $row['mfeed_pbird'];
                    $hd_per = $row['hd_per'];
                    $he_per = $row['he_per'];
                    $hhp_pweek = $row['hhp_pweek'];
                    $chhp_pweek = $row['chhp_pweek'];
                    $hhe_pweek = $row['hhe_pweek'];
                    $chhe_pweek = $row['chhe_pweek'];
                    $hatch_per = $row['hatch_per'];
                    $chicks_pweek = $row['chicks_pweek'];
                    $cchicks_pweek = $row['cchicks_pweek'];
                    $egg_weight = $row['egg_weight'];
                    $fbird_bweight = $row['fbird_bweight'];
                    $mbird_bweight = $row['mbird_bweight'];
                   
                    $html .= '<tr>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($breed_age,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($livability,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ffeed_pbird,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($mfeed_pbird,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($hd_per,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($he_per,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($hhp_pweek,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($chhp_pweek,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($hhe_pweek,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($chhe_pweek,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($hatch_per,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($chicks_pweek,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($cchicks_pweek,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($egg_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($fbird_bweight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($mbird_bweight,5)).'</td>';
                   
                    $html .= '</tr>';

                  
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="16"></th>'; 
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