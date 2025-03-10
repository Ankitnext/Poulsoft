<?php
//chicken_weekly_cussales1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
// $cuss = $_GET['cuss'];
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_weekly_cussales1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_weekly_cussales1.php?db=".$db;
}
include "number_format_ind.php";
include "decimal_adjustments.php";
$file_name = "Customer Weekly Balance Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $loc_access = ""; $adm_aflag = 0;
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; if((int)$row['supadmin_access'] == 1 || (int)$row['admin_access'] == 1){ $adm_aflag = 1; } }

//Sector Access Filter
if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
else{
    $loc1 = explode(",",$loc_access); $loc_list = "";
    foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
    $sec_fltr = " AND `code` IN ('$loc_list')";
}
//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

//Fetch User Details
if((int)$adm_aflag == 1){
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `dflag` = '0' ORDER BY `username` ASC";
}
else{
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `empcode` LIKE '$emp_code' AND `dflag` = '0' ORDER BY `username` ASC";
}
$query = mysqli_query($conns,$sql); $usr_code = $usr_name = array();
while($row = mysqli_fetch_assoc($query)){ $usr_code[$row['empcode']] = $row['empcode']; $usr_name[$row['empcode']] = $row['username']; }

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$fdate = date("Y-m-d"); $sectors = $cuss = "select"; $fstyles = $fsizes = "default"; $exports = "display"; $no_weeks = 1;
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $cuss = $_POST['cus_code'];
    $no_weeks = $_POST['no_weeks'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
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
                                <td colspan="18" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div> 
                                        <div class="form-group" style="width:110px;">
                                            <label for="no_weeks">No. of Weeks</label>
                                            <input type="text" name="no_weeks" id="no_weeks" class="form-control" value="<?php echo $no_weeks; ?>" onkeyup="validatenum(this.id);" style="padding:0;padding-left:2px;width:100px;" />
                                        </div>
                                        <div class="form-group" style="width:290px;">
                                            <label for="cus_code">Customer</label>
                                            <select name="cus_code" id="cus_code" class="form-control select2" style="width:280px;">
                                                <option value="select" <?php if($cuss == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cuss == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php if((int)$font_fflag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="fstyles">Font-Family</label>
                                            <select name="fstyles" id="fstyles" class="form-control select2" style="width:180px;">
                                                <option value="default" <?php if($fstyles == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_id as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fstyles == $scode){ echo "selected"; } ?>><?php echo $font_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:70px;">
                                            <label for="fsizes">Font-Size</label>
                                            <select name="fsizes" id="fsizes" class="form-control select2" style="width:60px;">
                                                <option value="default" <?php if($fsizes == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_sizes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fsizes == $scode){ echo "selected"; } ?>><?php echo $font_sizes[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
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
                            $html = '';
                            //Sales
                            // $html .= '<tr class="thead2">';
                            // $html .= '<th colspan="8" style="text-align:center;">Customer Weekly Balance Sales</th>';
                            // $html .= '</tr>';
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">Opening Balance</th>';
                            $html .= '<th style="text-align:center;">From Date</th>';
                            $html .= '<th style="text-align:center;">To Date</th>';
                            $html .= '<th style="text-align:center;">Sale Qty</th>';
                            $html .= '<th style="text-align:center;">Sale Amount</th>';
                            $html .= '<th style="text-align:center;">Receipt</th>';
                            $html .= '<th style="text-align:center;">Weekly Closing Balance</th>';
                            $html .= '<th style="text-align:center;">Closing Balance</th>';
                            $html .= '</tr>';

                            $sdate = $edate = $min_sdate = $max_sdate = ""; $week_no = array(); $fdate1 = $fdate;
                            for($i = 1; $i<= $no_weeks; $i++){
                                $sdate = $fdate1;
                                $edate = date("Y-m-d", strtotime($sdate . ' + 6 days'));
                                $fdate1 = date("Y-m-d", strtotime($edate . ' + 1 days'));

                                $week_no[$i] = $sdate."@".$edate;

                                if($min_sdate == "" || strtotime($min_sdate) >= strtotime($sdate)){ $min_sdate = $sdate; }
                                if($max_sdate == "" || strtotime($max_sdate) <= strtotime($edate)){ $max_sdate = $edate; }
                            }

                            $min_rdate = $max_rdate = ""; $week_no2 = array(); $fdate1 = $fdate;
                            for($i = 1; $i<= $no_weeks + 1; $i++){
                                $sdate = $fdate1;
                                $edate = date("Y-m-d", strtotime($sdate . ' + 6 days'));
                                $fdate1 = date("Y-m-d", strtotime($edate . ' + 1 days'));

                                if($i == 1){ }
                                else{
                                    $week_no2[$i] = $sdate."@".$edate;

                                    if($min_rdate == "" || strtotime($min_rdate) >= strtotime($sdate)){ $min_rdate = $sdate; }
                                    if($max_rdate == "" || strtotime($max_rdate) <= strtotime($edate)){ $max_rdate = $edate; }
                                }
                            }
                            
                            $sql1 = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$cuss'";
                            $query = mysqli_query($conn,$sql1); $obcramt = $obdramt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if($row['obtype'] == "Cr"){ $obcramt = $row['obamt']; }
                                else if($row['obtype'] == "Dr"){ $obdramt = $row['obamt']; }
                                else{ }
                            }
                            
                            //Date Wise 1st week opening Balance
                            $old_inv = ""; $oinv = $orct = $ocdn = $occn = $omortality = $oreturns = 0;
                            $sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` < '$min_sdate' AND `customercode` LIKE '$cuss' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
                            $sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` < '$min_sdate' AND `ccode` LIKE '$cuss' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $orct += (float)$row['tamt']; } }
                            $sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` < '$min_sdate' AND `ccode` LIKE '$cuss' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn += (float)$row['tamt']; } else { $occn += (float)$row['tamt']; } } }
                            $obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$min_sdate' AND `ccode` = '$cuss' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $obquery = mysqli_query($conn,$obsql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($obrow = mysqli_fetch_assoc($obquery)){ $omortality += (float)$obrow['amount']; } }
                            $obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$min_sdate' AND `vcode` = '$cuss' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $obquery = mysqli_query($conn,$obsql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($obrow = mysqli_fetch_assoc($obquery)){ $oreturns += (float)$obrow['amount']; } }
                            
                            $sales = $oinv + $ocdn + $obdramt;
                            $receipts = $orct + $omortality + $oreturns + $occn + $obcramt;
                            $balance = $sales - $receipts;

                            //Week Wise Sales
                            $sql = "SELECT * FROm `customer_sales` WHERE `date` >= '$min_sdate' AND `date` <= '$max_sdate' AND `customercode` LIKE '$cuss' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $sql_qty = $sql_amt = array(); $old_inv = "";
                            while($row = mysqli_fetch_assoc($query)){
                                foreach($week_no as $key => $value){
                                    $data1 = array(); $data1 = explode("@",$value);
                                    if(strtotime($row['date']) >= strtotime($data1[0]) && strtotime($row['date']) <= strtotime($data1[1])){
                                        $sql_qty[$key] += (float)$row['netweight'];
                                        if($old_inv != $row['invoice']){ $sql_amt[$key] += (float)$row['finaltotal']; $old_inv = $row['invoice']; }
                                    }
                                }
                            }

                            //Week Wise Receipt
                            $sql = "SELECT * FROm `customer_receipts` WHERE `date` >= '$min_rdate' AND `date` <= '$max_rdate' AND `ccode` LIKE '$cuss' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $rct_amt = array(); $old_inv = ""; $key2 = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                foreach($week_no2 as $key => $value){
                                    $data1 = array(); $data1 = explode("@",$value);
                                    if(strtotime($row['date']) >= strtotime($data1[0]) && strtotime($row['date']) <= strtotime($data1[1])){
                                        $key2 = (int)$key - 1;
                                        $rct_amt[$key2] += (float)$row['amount'];
                                    }
                                }
                            }

                            $tsale_amt = 0;
                            foreach($week_no as $key => $value){
                                if((int)$key == 1){ $ob_amt = $balance; } else{ $ob_amt = $cls_bal; }
                                $data1 = array(); $data1 = explode("@",$value);

                                $week_amt = $cls_bal = 0;
                                $week_amt = ((float)$sql_amt[$key] - (float)$rct_amt[$key]);
                                $cls_bal = (((float)$ob_amt + (float)$sql_amt[$key]) - (float)$rct_amt[$key]);

                                $slno++;
                                $price = 0; if((float)$isale_qty[$key] != 0){ $price = (float)$isale_amt[$key] / (float)$isale_qty[$key]; }
                                $html .= '<tr>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($ob_amt,2)).'</td>';
                                $html .= '<td>'.date("d.m.Y",strtotime($data1[0])).'</td>';
                                $html .= '<td>'.date("d.m.Y",strtotime($data1[1])).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($sql_qty[$key],2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($sql_amt[$key],2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($rct_amt[$key],2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($week_amt,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($cls_bal,2)).'</td>';
                                $html .= '</tr>';
                                
                                $tsale_qty += (float)$sql_qty[$key];
                                $tsale_amt += (float)$sql_amt[$key];
                                $tsale_ramt += (float)$rct_amt[$key];
                                $tsale_wamt += (float)$week_amt;
                                $tsale_clb = (float)$cls_bal;
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="3">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tsale_qty,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tsale_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tsale_ramt,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tsale_wamt,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tsale_clb,2)).'</th>';
                            $html .= '</tr>';

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                var users = document.getElementById("cus_code").value;
                var no_weeks = document.getElementById("no_weeks").value;
                var l = true;
                if(users == "select"){
                    alert("Kindly select Customer");
                    l = false;
                }
                else if(no_weeks == ""){
                    alert("Kindly select Number of Weeks");
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
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();
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
            function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
