<?php
//chicken_region_prate1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_region_prate1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_region_prate1.php?db=".$db;
}
include "number_format_ind.php";
$file_name = "Region Wise Paper Rate Report";
function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; }
    $a = round($a,$b);
    $c = explode(".",$a);
    $ed = "";
    $iv = 0;
    if($c[1] == ""){ $iv = 1; }
    else{ $iv = strlen($c[1]); }
    if($iv == 0){ $iv = 1; }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    return $a."".$ed;
}

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("main_regions", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_regions LIKE poulso6_admin_chickenmaster.main_regions;"; mysqli_query($conn,$sql1); }
if(in_array("main_dailypaperrate", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_dailypaperrate LIKE poulso6_admin_chickenmaster.main_dailypaperrate;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Customer Ledger Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Region Details
$sql = "SELECT * FROM `main_regions` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $region_code = $region_name = array();
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

//Item Details
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $regions = $items = "all";
$exports = "display";
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $items = $_POST['items'];
    $exports = $_POST['exports'];
}
?>
<html>
	<head>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            .main-table { white-space: nowrap; }
            .tbody1{
                color: black;
            }
        </style>
	</head>
	<body>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table <?php if($exports == "print") { echo ' class="main-table"'; } else{ echo ' class="table-sm table-hover main-table2"'; } ?>>
                        <thead class="thead1">
                            <tr>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                    <label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;
                                    <label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="regions">Region</label>
                                            <select name="regions" id="regions" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($region_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($regions == $scode){ echo "selected"; } ?>><?php echo $region_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($items == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    <!--</div>
                                    <div class="m-1 p-1 row">-->
                                        <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
                    <?php if($exports == "display" || $exports == "exportpdf"){ ?>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
                    <?php } ?>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            if($regions == "all"){ $rgn_fltr = ""; } else{ $rgn_fltr = " AND `cgroup` = '$regions'"; }
                            if($items == "all"){ $item_fltr = ""; } else{ $item_fltr = " AND `code` = '$items'"; }
                            $html = $nhtml = $fhtml = '';

                            $html .= '<thead class="thead2" id="head_names">';

                            $nhtml .= '<tr>'; $fhtml .= '<tr>';
                            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Date</th>';
                            $nhtml .= '<th>Region</th>'; $fhtml .= '<th id="order">Region</th>';
                            $nhtml .= '<th>Item</th>'; $fhtml .= '<th id="order">Item</th>';
                            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
                            $nhtml .= '</tr>'; $fhtml .= '</tr>';

                            $html .= $fhtml;
                            $html .= '</thead>';

                            $html .= '<tbody class="tbody1">';
                            
                            $sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$rgn_fltr."".$item_fltr." AND `active` = '1' AND `dflag` = '0' AND `trtype` LIKE '%region_paperrate%' ORDER BY `date`,`id` ASC";
                            $query = mysqli_query($conn, $sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $date = date("d.m.Y",strtotime($row['date']));
                                $rname = $region_name[$row['cgroup']];
                                $iname = $item_name[$row['code']];
                                $prate = $row['new_price'];

                                $html .= '<tr>';
                                $html .= '<td class="dates">'.$date.'</td>';
                                $html .= '<td>'.$rname.'</td>';
                                $html .= '<td>'.$iname.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($prate).'</td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>';

                            $html .= '<thead class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="4"></th>';
                            $html .= '</tr>';
                            $html .= '</thead>';

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                var vendors = document.getElementById("vendors").value;
                var l = true;
                if(vendors == "select"){
                    alert("Kindly select customer to fetch Ledger");
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
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
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();

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
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
