<?php
//chicken_dailytransaction_report.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_dailytransaction_report.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_dailytransaction_report.php?db=".$db;
}
include "number_format_ind.php";

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
$file_name = "Daywise Transaction Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

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

// $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
// $query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
// while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

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

$fdate = $tdate = date("Y-m-d"); $sectors = $users = "all"; $fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sectors = $_POST['sectors'];
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
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="20" class="p-1">
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
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
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
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Submit</button>
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
                            $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users' AND `active` = '1'";
                            $query = mysqli_query($conn,$sql); $cash_coa = $bank_coa = "";
                            while($row = mysqli_fetch_assoc($query)){ $cash_coa = $row['cash_coa']; $bank_coa = $row['bank_coa']; }
                            
                            $html = '';
                            //Sales
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="20" style="text-align:center;">Credit Sales</th>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:center;">Date</th>';
                            $html .= '<th style="text-align:center;" colspan = "3">Purchase</th>';
                            $html .= '<th style="text-align:center;" colspan = "3">Purchase Return</th>';
                            $html .= '<th style="text-align:center;">Payment</th>';
                            $html .= '<th style="text-align:center;" colspan = "3">Sales</th>';
                            $html .= '<th style="text-align:center;" colspan = "3">Sales Return</th>';
                            $html .= '<th style="text-align:center;" colspan = "3">Mortality</th>';
                            $html .= '<th style="text-align:center;">Receipt</th>';
                            $html .= '<th style="text-align:center;">Vouchers</th>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:center;"></th>';
                            $html .= '<th style="text-align:center;">Qty</th>';
                            $html .= '<th style="text-align:center;">Rate</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Qty</th>';
                            $html .= '<th style="text-align:center;">Rate</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Qty</th>';
                            $html .= '<th style="text-align:center;">Rate</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Qty</th>';
                            $html .= '<th style="text-align:center;">Rate</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Qty</th>';
                            $html .= '<th style="text-align:center;">Rate</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '</tr>';

                            $usr_fltr = ""; if($users != "all"){ $usr_fltr = " AND `addedemp` = '$users'"; }
                            $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }

                            // $sql = "SELECT * FROM `retail_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$usr_fltr."".$sec_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `trnum` ASC";
                            // $query = mysqli_query($conn, $sql); $isale_qty = $isale_amt = array();
                            // while($row = mysqli_fetch_assoc($query)){
                            //     $key = $row['icode'];
                            //     $isale_qty[$key] += (float)$row['quantity'];
                            //     $isale_amt[$key] += (float)$row['amount'];
                            // }

                            // $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
                            // $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
                            // while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
                            
                            // $sql = "SELECT * FROM `inv_sectors` WHERE `addedtime` >= '$fdate' AND `addedtime` <= '$tdate'".$sec_fltr." AND `active` = '1' ORDER BY `trnum` ASC";
                            // $query = mysqli_query($conn, $sql); $iivs_desc = array();
                            // while($row = mysqli_fetch_assoc($query)){
                            //     $key = $row['addedtime'];
                            //     $iivs_desc[$key] = (float)$row['description'];
                            // }

                            $sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sec_fltr." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $ivoc_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $ivoc_amt[$key] += (float)$row['amount'];
                            }

                            $sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sec_fltr." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $icus_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $icus_amt[$key] += (float)$row['amount'];
                            }

                            $sql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sec_fltr." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $ipur_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $ipur_amt[$key] += (float)$row['amount'];
                            }

                            $sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn, $sql); $imm_qty = $imm_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $imm_qty[$key] += (float)$row['quantity'];
                                $imm_amt[$key] += (float)$row['amount'];
                            }

                            $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn, $sql); $ics_qty = $ics_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $ics_qty[$key] += (float)$row['netweight'];
                                $ics_amt[$key] += (float)$row['totalamt'];
                            }

                            $sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn, $sql); $ipp_qty = $ipp_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $ipp_qty[$key] += (float)$row['netweight'];
                                $ipp_amt[$key] += (float)$row['totalamt'];
                            }

                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` = 'customer' And `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn, $sql); $isr_qty = $isr_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $isr_qty[$key] += (float)$row['quantity'];
                                $isr_amt[$key] += (float)$row['amount'];
                            }

                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn, $sql); $ipr_qty = $ipr_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['date'];
                                $ipr_qty[$key] += (float)$row['quantity'];
                                $ipr_amt[$key] += (float)$row['amount'];
                            }

                            $tsale_amt = 0;
                            for($cdate = strtotime($fdate);$cdate <= strtotime($tdate);$cdate += (86400)){
                                $adate = date('Y-m-d', $cdate);
                                $key = $adate;
                                // if(empty($isale_qty[$key]) || $isale_qty[$key] == "" || (float)$isale_qty[$key] == 0){ }
                                //  if(empty($ipur_amt[$key]) || $ipur_amt[$key] == "" || (float)$ipur_amt[$key] == 0){ }
                                //  if(empty($icus_amt[$key]) || $icus_amt[$key] == "" || (float)$icus_amt[$key] == 0){ }
                                //  if(empty($ivoc_amt[$key]) || $ivoc_amt[$key] == "" || (float)$ivoc_amt[$key] == 0){ }
                                // else{
                                    $slno++;
                                    // $price = 0; if((float)$isale_qty[$key] != 0){ $price = (float)$isale_amt[$key] / (float)$isale_qty[$key]; }
                                    $imm_price = 0; if((float)$imm_qty[$key] != 0){ $imm_price = (float)$imm_amt[$key] / (float)$imm_qty[$key]; }
                                    $ipr_price = 0; if((float)$ipr_qty[$key] != 0){ $ipr_price = (float)$ipr_amt[$key] / (float)$ipr_qty[$key]; }
                                    $isr_price = 0; if((float)$isr_qty[$key] != 0){ $isr_price = (float)$isr_amt[$key] / (float)$isr_qty[$key]; }
                                    $ipp_price = 0; if((float)$ipp_qty[$key] != 0){ $ipp_price = (float)$ipp_amt[$key] / (float)$ipp_amt[$key]; }
                                    $ics_price = 0; if((float)$ics_qty[$key] != 0){ $ics_price = (float)$ics_amt[$key] / (float)$ics_qty[$key]; }
                                    // $imm_price = 0; if((float)$imm_qty[$key] != 0){ $imm_price = (float)$imm_amt[$key] / (float)$imm_qty[$key]; }

                                    $html .= '<tr>';
                                    // $html .= '<td>'.$slno.'</td>';
                                    $html .= '<td>'.date("d.m.Y",strtotime($adate)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ipp_qty[$key],2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ipp_price,2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ipp_amt[$key],2)).'</td>';
                                    
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ipr_qty[$key],2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ipr_price,2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ipr_amt[$key],2)).'</td>';
                                    $html .= '<td>'.$ipur_amt[$key].'</td>';
                                    
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ics_qty[$key],2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ics_price,2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($ics_amt[$key],2)).'</td>';
                                    
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($isr_qty[$key],2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($isr_price,2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($isr_amt[$key],2)).'</td>';
                                    
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($imm_qty[$key],2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($imm_price,2)).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind(round($imm_amt[$key],2)).'</td>';
                                    $html .= '<td>'.$icus_amt[$key].'</td>';
                                    $html .= '<td>'.$ivoc_amt[$key].'</td>';
                                   
                                    $html .= '</tr>';
                                    // $tsale_amt += (float)$isale_amt[$key];
                                     $tivoc_amt += (float)$ivoc_amt[$key];
                                     $ticus_amt += (float)$icus_amt[$key];
                                     
                                     $timm_amt += (float)$imm_amt[$key];
                                     $tisr_amt += (float)$isr_amt[$key];
                                     $tics_amt += (float)$ics_amt[$key];

                                     $tipur_amt += (float)$ipur_amt[$key];

                                     $tipr_amt += (float)$ipr_amt[$key];
                                     $tipp_amt += (float)$ipp_amt[$key];
                                // }
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="1">Total</th>';
                            $html .= '<th style="text-align:right;" colspan="3">'.number_format_ind(round($tipp_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="3">'.number_format_ind(round($tipr_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="1">'.number_format_ind(round($tipur_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="3">'.number_format_ind(round($tics_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="3">'.number_format_ind(round($tisr_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="3">'.number_format_ind(round($timm_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="1">'.number_format_ind(round($ticus_amt,2)).'</th>';
                            $html .= '<th style="text-align:right;" colspan="1">'.number_format_ind(round($tivoc_amt,2)).'</th>';
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
                var users = document.getElementById("users").value;
                var sectors = document.getElementById("sectors").value;
                var l = true;
                if(users == "select"){
                    alert("Kindly select User");
                    l = false;
                }
                else if(sectors == "select"){
                    alert("Kindly select Shop/Outlet");
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
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
