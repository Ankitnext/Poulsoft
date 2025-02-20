<?php
//chicken_retail_cashhover1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_retail_cashhover1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_retail_cashhover1.php?db=".$db;
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
$file_name = "Cash hand Over Report";

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

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cbcoa_code = $cbcoa_name = array();
while($row = mysqli_fetch_assoc($query)){ $cbcoa_code[$row['code']] = $row['code']; $cbcoa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $coa_name = array();
while($row = mysqli_fetch_assoc($query)){ $coa_name[$row['code']] = $row['description']; }

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

$date = date("Y-m-d"); $sectors = $users = "select"; $fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    $date = date("Y-m-d",strtotime($_POST['date']));
    $sectors = $_POST['sectors'];
    $users = $_POST['users'];
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
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">Date</label>
                                            <input type="text" name="date" id="date" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="users">user</label>
                                            <select name="users" id="users" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($users == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($usr_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($users == $scode){ echo "selected"; } ?>><?php echo $usr_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Shop/Outlet</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($sectors == "select"){ echo "selected"; } ?>>-select-</option>
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
                            $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users' AND `active` = '1'";
                            $query = mysqli_query($conn,$sql); $cash_coa = $bank_coa = "";
                            while($row = mysqli_fetch_assoc($query)){ $cash_coa = $row['cash_coa']; $bank_coa = $row['bank_coa']; }
                            
                            $html = '';
                            //Retails Sales
                            $sql = "SELECT * FROM `retail_sales` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `addedemp` = '$users' AND `active` = '1' AND `dflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $csale_amt = $bsale_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if($row['cash_code'] == $cash_coa){ $csale_amt += (float)$row['cash_amt']; }
                                if($row['bank_code'] == $bank_coa){ $bsale_amt += (float)$row['bank_amt']; }
                            }
                            $html .= '<tbody class="tbody1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="4" style="text-align:right;">Cash Sale</th>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($csale_amt,2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="4" style="text-align:right;">UPI Sale</th>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($bsale_amt,2)).'</td>';
                            $html .= '</tr>';
                            //Sales
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="5" style="text-align:center;">Credit Sales</th>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:center;">Sl.No.</th>';
                            $html .= '<th style="text-align:center;">Inv.No.</th>';
                            $html .= '<th style="text-align:center;">Customer</th>';
                            $html .= '<th style="text-align:center;">Remarks</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '</tr>';

                            $sql = "SELECT * FROM `customer_sales` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `addedemp` = '$users' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `invoice` ORDER BY `invoice` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $tsale_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $slno++;
                                $trnum = $row['invoice'];
                                $cname = $ven_name[$row['customercode']];
                                $remarks = $row['remarks'];
                                $inv_amt = (float)$row['finaltotal'];

                                $html .= '<tr>';
                                $html .= '<td>'.$slno.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$cname.'</td>';
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($inv_amt,2)).'</td>';
                                $html .= '</tr>';

                                $tsale_amt += (float)$inv_amt;
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="4">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tsale_amt,2)).'</th>';
                            $html .= '</tr>';

                            //Purchase
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="5" style="text-align:center;">Credit Purchases</th>';
                            $html .= '</tr>';
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">Sl.No.</th>';
                            $html .= '<th style="text-align:center;">Inv.No.</th>';
                            $html .= '<th style="text-align:center;">Supplier</th>';
                            $html .= '<th style="text-align:center;">Remarks</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '</tr>';

                            $sql = "SELECT * FROM `pur_purchase` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `addedemp` = '$users' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `invoice` ORDER BY `invoice` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $tpur_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $slno++;
                                $trnum = $row['invoice'];
                                $cname = $ven_name[$row['vendorcode']];
                                $remarks = $row['remarks'];
                                $inv_amt = (float)$row['finaltotal'];

                                $html .= '<tr>';
                                $html .= '<td>'.$slno.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$cname.'</td>';
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($inv_amt,2)).'</td>';
                                $html .= '</tr>';

                                $tpur_amt += (float)$inv_amt;
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="4">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($tpur_amt,2)).'</th>';
                            $html .= '</tr>';

                            //Receipt
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="5" style="text-align:center;">Cash Collection</th>';
                            $html .= '</tr>';
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">Sl.No.</th>';
                            $html .= '<th style="text-align:center;">Bill No.</th>';
                            $html .= '<th style="text-align:center;">Customer</th>';
                            $html .= '<th style="text-align:center;">Remarks</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '</tr>';

                            $sql = "SELECT * FROM `customer_receipts` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `method` = '$cash_coa' AND `addedemp` = '$users' AND `vtype` LIKE '%C%' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $trct_camt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $slno++;
                                $trnum = $row['trnum'];
                                $cname = $ven_name[$row['ccode']];
                                $remarks = $row['remarks'];
                                $inv_amt = (float)$row['amount'];

                                $html .= '<tr>';
                                $html .= '<td>'.$slno.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$cname.'</td>';
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($inv_amt,2)).'</td>';
                                $html .= '</tr>';

                                $trct_camt += (float)$inv_amt;
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="4">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($trct_camt,2)).'</th>';
                            $html .= '</tr>';

                            //Transferred to bank
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="5" style="text-align:center;">Transferred to bank</th>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:center;">Sl.No.</th>';
                            $html .= '<th style="text-align:center;">Bill No.</th>';
                            $html .= '<th style="text-align:center;">Customer</th>';
                            $html .= '<th style="text-align:center;">Remarks</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '</tr>';

                            $sql = "SELECT * FROM `customer_receipts` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `method` = '$bank_coa' AND `addedemp` = '$users' AND `vtype` LIKE '%C%' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $trct_bamt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $slno++;
                                $trnum = $row['trnum'];
                                $cname = $ven_name[$row['ccode']];
                                $remarks = $row['remarks'];
                                $inv_amt = (float)$row['amount'];

                                $html .= '<tr>';
                                $html .= '<td>'.$slno.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$cname.'</td>';
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($inv_amt,2)).'</td>';
                                $html .= '</tr>';

                                $trct_bamt += (float)$inv_amt;
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="4">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($trct_bamt,2)).'</th>';
                            $html .= '</tr>';

                            //Payment
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="5" style="text-align:center;">Cash Payments / Expenses</th>';
                            $html .= '</tr>';
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">Sl.No.</th>';
                            $html .= '<th style="text-align:center;">Voucher No.</th>';
                            $html .= '<th style="text-align:center;">Paid To</th>';
                            $html .= '<th style="text-align:center;">Remarks</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '</tr>';

                            $sql = "SELECT * FROM `pur_payments` WHERE `date` = '$date' AND `vtype` LIKE '%S%' AND `warehouse` = '$sectors' AND `method` = '$cash_coa' AND `addedemp` = '$users' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $tpay_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $slno++;
                                $trnum = $row['trnum'];
                                $cname = $ven_name[$row['ccode']];
                                $remarks = $row['remarks'];
                                $inv_amt = (float)$row['amount'];

                                $html .= '<tr>';
                                $html .= '<td>'.$slno.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$cname.'</td>';
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($inv_amt,2)).'</td>';
                                $html .= '</tr>';

                                $tpay_amt += (float)$inv_amt;
                            }

                            $coa_list = implode("','",$cbcoa_code);
                            $sql = "SELECT * FROM `acc_vouchers` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `fcoa` = '$cash_coa' AND `tcoa` NOT IN ('$coa_list') AND `addedemp` = '$users' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $texp_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $slno++;
                                $trnum = $row['trnum'];
                                $cname = $coa_name[$row['tcoa']];
                                $remarks = $row['remarks'];
                                $inv_amt = (float)$row['amount'];

                                $html .= '<tr>';
                                $html .= '<td>'.$slno.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$cname.'</td>';
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($inv_amt,2)).'</td>';
                                $html .= '</tr>';

                                $texp_amt += (float)$inv_amt;
                            }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="4">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($texp_amt,2)).'</th>';
                            $html .= '</tr>';

                            $tcrcvd_amt = (float)$csale_amt + (float)$trct_camt;
                            $html .= '<tr>';
                            $html .= '<th colspan="4" style="text-align:right;">Total Cash Collected</th>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($tcrcvd_amt,2)).'</td>';
                            $html .= '</tr>';

                            $coa_list = implode("','",$cbcoa_code);
                            $sql = "SELECT * FROM `acc_vouchers` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `fcoa` = '$cash_coa' AND `tcoa` IN ('$coa_list') AND `addedemp` = '$users' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $tctb_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $inv_amt = (float)$row['amount'];
                                $tctb_amt += (float)$inv_amt;
                            }

                            $html .= '<tr>';
                            $html .= '<th colspan="4" style="text-align:right;">Total Transffered to Bank</th>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($tctb_amt,2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="5" style="text-align:center;">Cash Handed Over</th>';
                            $html .= '</tr>';
                            
                            $sql = "SELECT * FROM `retail_cash_hover` WHERE `date` = '$date' AND `warehouse` = '$sectors' AND `addedemp` = '$users' AND `active` = '1' AND `dflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                            $query = mysqli_query($conn, $sql); $slno = $tcash_hamt = $d2000_amt = $d500_amt = $d200_amt = $d100_amt = $d50_amt = $d20_amt = $d10_amt = $d5_amt = $d2_amt = $d1_amt = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $tcash_hamt += (float)$row['cash_hamt'];
                                $d2000_amt += (float)$row['d2000'];
                                $d500_amt += (float)$row['d500'];
                                $d200_amt += (float)$row['d200'];
                                $d100_amt += (float)$row['d100'];
                                $d50_amt += (float)$row['d50'];
                                $d20_amt += (float)$row['d20'];
                                $d10_amt += (float)$row['d10'];
                                $d5_amt += (float)$row['d5'];
                                $d2_amt += (float)$row['d2'];
                                $d1_amt += (float)$row['d1'];
                            }
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">2000</th>';
                            $html .= '<td style="text-align:right;">x'.$d2000_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d2000_amt * 2000),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">500</th>';
                            $html .= '<td style="text-align:right;">x'.$d500_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d500_amt * 500),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">200</th>';
                            $html .= '<td style="text-align:right;">x'.$d200_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d200_amt * 200),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">100</th>';
                            $html .= '<td style="text-align:right;">x'.$d100_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d100_amt * 100),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">50</th>';
                            $html .= '<td style="text-align:right;">x'.$d50_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d50_amt * 50),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">20</th>';
                            $html .= '<td style="text-align:right;">x'.$d20_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d20_amt * 20),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">10</th>';
                            $html .= '<td style="text-align:right;">x'.$d10_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d10_amt * 10),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">5</th>';
                            $html .= '<td style="text-align:right;">x'.$d5_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d5_amt * 5),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">2</th>';
                            $html .= '<td style="text-align:right;">x'.$d2_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d2_amt * 2),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:right;">1</th>';
                            $html .= '<td style="text-align:right;">x'.$d1_amt.'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($d1_amt * 1),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '</tbody>';

                            $diff_amt = (float)$tcash_hamt - (float)$tctb_amt;
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="4" style="text-align:right;">Total Amount</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($tcash_hamt),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<th colspan="4" style="text-align:right;">Difference Amount</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind(round(($diff_amt),2)).'</td>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';

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
