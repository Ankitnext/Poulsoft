<?php
//broiler_customer_gstr3B_summary1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_customer_gstr3B_summary1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_customer_gstr3B_summary1.php?db=$db&userid=".$user_code;
}
$file_name = "GSTR-3B Report";
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_ebill_states", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_ebill_states LIKE poulso6_admin_broiler_broilermaster.broiler_ebill_states;"; mysqli_query($conn,$sql1); }
if(in_array("country_states", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.country_states LIKE poulso6_admin_broiler_broilermaster.country_states;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_companyprofile`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("gstr_state_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_companyprofile` ADD `gstr_state_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'GSTR Reporting State Code'"; mysqli_query($conn,$sql); }
if(in_array("com_gstinno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_companyprofile` ADD `com_gstinno` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `com_gstinno`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gstr_state_code = $row['gstr_state_code']; $clnt_gstin = $row['com_gstinno']; $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `country_states` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $state_code = $state_name = $state_sname = $state_mcode = array();
while($row = mysqli_fetch_assoc($query)){ $state_code[$row['code']] = $row['code']; $state_name[$row['code']] = $row['name']; $state_sname[$row['code']] = $row['short_name']; $state_mcode[$row['code']] = $row['state_code']; }
$gstr_state_code = $state_mcode[$gstr_state_code];

$sql = "SELECT * FROM `tax_details` WHERE `dflag` = '0' ORDER BY `gst_type` ASC";
$query = mysqli_query($conn,$sql); $gst_code = $gst_iflag = array();
while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_iflag[$row['code']] = $row['isflag']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $ven_gcode = $ven_gname = array();
while($row = mysqli_fetch_assoc($query)){ $ven_gcode[$row['code']] = $row['code']; $ven_gname[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = $ven_gstin = $ven_state = $ven_saddr = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; $ven_gstin[$row['code']] = $row['gstinno']; $ven_state[$row['code']] = $row['state_code']; $ven_saddr[$row['code']] = $row['saddress']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $sup_code = $sup_name = $sup_gstin = $sup_state = $sup_saddr = array();
while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; $sup_gstin[$row['code']] = $row['gstinno']; $sup_state[$row['code']] = $row['state_code']; $sup_saddr[$row['code']] = $row['saddress']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_taxtype = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_hsn[$row['code']] = $row['hsn_code']; $item_cunit[$row['code']] = $row['cunits']; $item_taxtype[$row['code']] = $row['gst_type']; }

$fdate = $tdate = date("Y-m-d"); $groups = $vendors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    //$groups = $_POST['groups'];
    //$vendors = $_POST['vendors'];
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
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="23" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="25">
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
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel2('main_table','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                        <option value="json" <?php if($excel_type == "json"){ echo "selected"; } ?>>-Json-</option>
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
            <?php
            if(isset($_POST['submit_report']) == true){
                $gstr3b = [
                    '3_1_taxable' => 0, '3_1_igst' => 0, '3_1_cgst' => 0, '3_1_sgst' => 0,
                    '3_1_other_outward' => 0,
                    '3_2_taxable' => 0, '3_2_igst' => 0,
                    '4A_taxable' => 0, '4A_igst' => 0, '4A_cgst' => 0, '4A_sgst' => 0,
                    '4B_advance_inward' => 0,
                    '4_other_nongst' => 0,
                    '5_advance_outward' => 0,
                    '5_exempt_taxable' => 0,
                    '6_interest_latefee' => 0
                ];

                $voucher_counts = [
                    'sales' => 0,
                    'purchases' => 0,
                    'creditnotes' => 0,
                    'debitnotes' => 0,
                    'advancesuppliers' => 0,
                    'advancecustomers' => 0
                ];

                //SALES
                $sql = "SELECT * FROM `broiler_sales` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn, $sql); $old_inv = "";
                while($row = mysqli_fetch_assoc($query)){
                    if($old_inv != $row['trnum']){ $voucher_counts['sales']++; $old_inv = $row['trnum']; }

                    $state = $ven_state[$row['vcode']];
                    $icode = $row['icode'];
                    $gst_type = isset($item_taxtype[$icode]) ? strtolower(trim($item_taxtype[$icode])) : 'taxable';
                    $taxable = (((float)$row['rcd_qty'] * (float)$row['rate']) - (float)$row['dis_amt']);
                    $gst_amt = (float)$row['gst_amt'];

                    if($gst_type == 'taxable' || $gst_amt > 0){
                        if($gst_amt > 0){
                            if($state == "" || $gstr_state_code == "" || $state == $gstr_state_code){
                                $gstr3b['3_1_taxable'] += $taxable;
                                $gstr3b['3_1_cgst'] += ($gst_amt / 2);
                                $gstr3b['3_1_sgst'] += ($gst_amt / 2);
                            }
                            else{
                                $gstr3b['3_2_taxable'] += $taxable;
                                $gstr3b['3_2_igst'] += $gst_amt;
                            }
                        }
                        else{
                            $gstr3b['3_1_other_outward'] += $taxable;
                        }
                    }
                    else if(in_array($gst_type, ['exempt', 'nil rated', 'non-gst'])){
                        $gstr3b['5_exempt_taxable'] += $taxable;
                    }
                    else{
                        $gstr3b['3_1_other_outward'] += $taxable;
                    }
                }

                //PURCHASES
                $sql = "SELECT * FROM `broiler_purchases` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn, $sql); $old_inv = "";
                while($row = mysqli_fetch_assoc($query)){
                    if($old_inv != $row['trnum']){ $voucher_counts['purchases']++; $old_inv = $row['trnum']; }

                    $state = $sup_state[$row['vcode']];
                    $icode = $row['icode'];
                    $gst_type = isset($item_taxtype[$icode]) ? strtolower(trim($item_taxtype[$icode])) : 'taxable';
                    $taxable = ((float)$row['rcd_qty'] * (float)$row['rate']) - (float)$row['dis_amt'];
                    $gst_amt = (float)$row['gst_amt'];

                    if($gst_type == 'taxable' || $gst_amt > 0){
                        $gstr3b['4A_taxable'] += $taxable;
                        if($state == "" || $gstr_state_code == "" || $state == $gstr_state_code){
                            $gstr3b['4A_cgst'] += ($gst_amt / 2);
                            $gstr3b['4A_sgst'] += ($gst_amt / 2);
                        }
                        else{
                            $gstr3b['4A_igst'] += $gst_amt;
                        }
                    }
                    else if(in_array($gst_type, ['exempt', 'nil rated', 'non-gst'])){
                        $gstr3b['4_other_nongst'] += $taxable;
                    }
                    else{
                        $gstr3b['4_other_nongst'] += $taxable;
                    }
                }

                //CREDIT / DEBIT NOTES
                $sql = "SELECT * FROM `broiler_crdrnote` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `type` IN ('Customer','Supplier') AND `crdr` IN ('Credit','Debit') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $state = (strtolower($row['type']) == 'customer') ? $ven_state[$row['vcode']] : $sup_state[$row['vcode']];
                    $sign = (strtolower($row['crdr']) == 'credit') ? -1 : 1;

                    if(strtolower($row['type']) == 'customer'){
                        $voucher_counts['creditnotes']++;
                        if($state == "" || $gstr_state_code == "" || $state == $gstr_state_code){
                            $gstr3b['3_1_taxable'] += $sign * (float)$row['amount'];
                            if(!empty($row['gst_amt']) && (float)$row['gst_amt'] > 0){
                                $gstr3b['3_1_cgst'] += $sign * ((float)$row['gst_amt'] / 2);
                                $gstr3b['3_1_sgst'] += $sign * ((float)$row['gst_amt'] / 2);
                            }
                        }
                        else{
                            echo "<br/>".$gstr_state_code."@".$state."@".$row['trnum'];
                            $gstr3b['3_2_taxable'] += $sign * (float)$row['amount'];
                            if(!empty($row['gst_amt']) && (float)$row['gst_amt'] > 0){
                                $gstr3b['3_2_igst'] += $sign * (float)$row['gst_amt'];
                            }
                        }
                    }
                    else if(strtolower($row['type']) == 'supplier'){
                        $voucher_counts['debitnotes']++;
                        $gstr3b['4A_taxable'] += $sign * (float)$row['amount'];
                        if($state == "" || $gstr_state_code == "" || $state == $gstr_state_code){
                            if(!empty($row['gst_amt']) && (float)$row['gst_amt'] > 0){
                                $gstr3b['4A_cgst'] += $sign * ((float)$row['gst_amt'] / 2);
                                $gstr3b['4A_sgst'] += $sign * ((float)$row['gst_amt'] / 2);
                            }
                        }
                        else{
                            if(!empty($row['gst_amt']) && (float)$row['gst_amt'] > 0){
                                $gstr3b['4A_igst'] += $sign * (float)$row['gst_amt'];
                            }
                        }
                    }
                }
                //ADVANCE SUPPLIER PAYMENTS
                $sql = "SELECT * FROM `broiler_payments` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `vtype` = 'Supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    if(strtolower($row['type']) == 'advance'){
                        $voucher_counts['advancesuppliers']++;
                        $gstr3b['4B_advance_inward'] += (float)$row['amount'];

                        $gst_amt = (float)$row['gst_amt'];
                        $state = $sup_state[$row['ccode']];
                        if($gst_amt > 0){
                            if($state == "" || $gstr_state_code == "" || $state == $gstr_state_code){
                                $gstr3b['4A_cgst'] += round(($gst_amt / 2), 2);;
                                $gstr3b['4A_sgst'] += round(($gst_amt / 2), 2);;
                            }
                            else{
                                $gstr3b['4A_igst'] += round($gst_amt, 2);;
                            }
                        }
                    }
                }
                //ADVANCE CUSTOMER RECEIPTS
                $sql = "SELECT * FROM `broiler_receipts` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `vtype` = 'Customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    if(strtolower($row['ptype']) == 'advance'){
                        $voucher_counts['advancecustomers']++;
                        $gstr3b['5_advance_outward'] += (float)$row['amount'];

                        $gst_amt = (float)$row['gst_amt'];
                        $state = $ven_state[$row['ccode']];
                        if($gst_amt > 0){
                            if($state == "" || $gstr_state_code == "" || $state == $gstr_state_code){
                                $gstr3b['3_1_cgst'] += round(($gst_amt / 2), 2);;
                                $gstr3b['3_1_sgst'] += round(($gst_amt / 2), 2);;
                            }
                            else{
                                $gstr3b['3_2_igst'] += round($gst_amt, 2);;
                            }
                        }
                    }
                }
                $html = '';
                /*$html .= '<thead style="background:#f2f2f2;">';
                $html .= '<tr><th colspan="8" align="left">GSTR-3B : RAINBOW INDIA (from 1-Apr-2021)</th></tr>';
                $html .= '<tr><th colspan="8" align="left">GST Registration : 10BYWPS0057G2ZA &nbsp;&nbsp;&nbsp; Status: Not Signed</th></tr>';
                $html .= '<tr><th colspan="8" align="left">Period : ' . date("j-M-y", strtotime($fdate)) . ' to ' . date("j-M-y", strtotime($tdate)) . '</th></tr>';
                $html .= '</thead>';

                $html .= '<tr style="background-color:#ffefcf;font-weight:bold;"><td colspan="8">Total Vouchers</td></tr>';
                $html .= '<tr><td colspan="6">Included in Return</td><td colspan="2" align="right">'.array_sum($voucher_counts).'</td></tr>';
                $html .= '<tr><td colspan="6">Not Relevant for This Return</td><td colspan="2" align="right">0</td></tr>';
                $html .= '<tr><td colspan="6">Uncertain Transactions (Corrections needed)</td><td colspan="2" align="right">0</td></tr>';
                */
                $html .= '<thead style="background-color:#eee; font-weight:bold; text-align:right;">';
                $html .= '<tr>
                    <th style="text-align:left;">Particulars</th>
                    <th>Taxable Amount</th>
                    <th>IGST</th>
                    <th>CGST</th>
                    <th>SGST/UTGST</th>
                    <th>Cess</th>
                    <th>Tax Amount</th>
                </tr>';
                $html .= '</thead>';

                $html .= '<tbody style="text-align:right;">';
                $html .= '<tr><td colspan="7" style="text-align:left;"><strong>Return View</strong></td></tr>';

                $html .= '<tr><td style="text-align:left;"><strong>3.1 Tax on Outward and Reverse Charge Inward Supplies</strong></td>
                    <td>' . number_format_ind($gstr3b['3_1_taxable'] + $gstr3b['3_1_other_outward'], 2) . '</td>
                    <td>0.00</td>
                    <td>' . number_format_ind($gstr3b['3_1_cgst'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['3_1_sgst'], 2) . '</td>
                    <td>0.00</td>
                    <td>' . number_format_ind($gstr3b['3_1_cgst'] + $gstr3b['3_1_sgst'], 2) . '</td>
                </tr>';

                $html .= '<tr><td style="text-align:left;"><strong>3.2 Interstate Supplies</strong></td>
                    <td>' . number_format_ind($gstr3b['3_2_taxable'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['3_2_igst'], 2) . '</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>' . number_format_ind($gstr3b['3_2_igst'], 2) . '</td>
                </tr>';

                $html .= '<tr><td colspan="7" style="text-align:left;"><strong>4. Eligible for Input Tax Credit</strong></td></tr>';

                $html .= '<tr><td style="text-align:left;">&ensp;&ensp;A. Input Tax Credit Available (either in part or in full)</td>
                    <td>' . number_format_ind($gstr3b['4A_taxable'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['4A_igst'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['4A_cgst'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['4A_sgst'], 2) . '</td>
                    <td>0.00</td>
                    <td>' . number_format_ind($gstr3b['4A_igst'] + $gstr3b['4A_cgst'] + $gstr3b['4A_sgst'], 2) . '</td>
                </tr>';

                $html .= '<tr><td style="text-align:left;">&ensp;&ensp;B. Input Tax Credit Reversed</td>
                    <td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td>
                </tr>';

                $html .= '<tr><td style="text-align:left;">&ensp;&ensp;C. Net Input Tax Credit Available (A) - (B)</td>
                    <td>' . number_format_ind($gstr3b['4A_taxable'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['4A_igst'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['4A_cgst'], 2) . '</td>
                    <td>' . number_format_ind($gstr3b['4A_sgst'], 2) . '</td>
                    <td>0.00</td>
                    <td>' . number_format_ind($gstr3b['4A_igst'] + $gstr3b['4A_cgst'] + $gstr3b['4A_sgst'], 2) . '</td>
                </tr>';

                $html .= '<tr><td style="text-align:left;">&ensp;&ensp;D. Other Details</td><td colspan="6"></td></tr>';
                $html .= '<tr><td style="text-align:left;">&ensp;&ensp;&ensp;&ensp;1. ITC reclaimed which was reversed under Table 4(B)(2) in earlier tax period</td><td>0.00</td><td colspan="5"></td></tr>';
                $html .= '<tr><td style="text-align:left;">&ensp;&ensp;&ensp;&ensp;2. Ineligible ITC under section 16(4) and ITC restricted due to PoS rules</td><td>0.00</td><td colspan="5"></td></tr>';

                //$html .= '<tr><td style="text-align:left;">&ensp;&ensp;E. Advance Paid to Suppliers</td><td>' . number_format_ind($gstr3b['4B_advance_inward'], 2) . '</td><td colspan="5"></td></tr>';

                $html .= '<tr><td style="text-align:left;"><strong>5. Exempt, Nil Rated, and Non-GST Inward Supplies</strong></td>
                    <td>' . number_format_ind($gstr3b['4_other_nongst'], 2) . '</td><td colspan="5"></td>
                </tr>';

                $html .= '<tr><td style="text-align:left;"><strong>5.1 Exempt/Nil/Non-GST Outward Supplies</strong></td>
                    <td>' . number_format_ind($gstr3b['5_exempt_taxable'], 2) . '</td><td colspan="5"></td>
                </tr>';

                //$html .= '<tr><td style="text-align:left;"><strong>5.1 Advance received from customers</strong></td><td>' . number_format_ind($gstr3b['5_advance_outward'], 2) . '</td><td colspan="5"></td></tr>';

                $html .= '<tr><td style="text-align:left;"><strong>6. Interest, Late Fee, Penalty and Others</strong></td>
                    <td>' . number_format_ind($gstr3b['6_interest_latefee'], 2) . '</td><td colspan="5"></td>
                </tr>';

                $html .= '</tbody>';

                echo $html;

                //Build the GSTR-3B JSON structure
                function fix_gstr3b_floats($value) {
                    return number_format((float)$value, 2, '.', '');
                }

                $gstr3b_json = array(
                    "gstin" => $clnt_gstin,
                    "ret_period" => date("mY", strtotime($fdate)),
                    "sup_details" => array(
                        "osup_det" => array(
                            "txval" => fix_gstr3b_floats($gstr3b['3_1_taxable'] + $gstr3b['3_1_other_outward']),
                            "iamt" => 0,
                            "camt" => fix_gstr3b_floats($gstr3b['3_1_cgst']),
                            "samt" => fix_gstr3b_floats($gstr3b['3_1_sgst']),
                            "csamt" => 0
                        ),
                        "osup_zero" => array(
                            "txval" => 0,
                            "iamt" => 0,
                            "csamt" => 0
                        ),
                        "osup_nil_exmp" => array(
                            "txval" => fix_gstr3b_floats($gstr3b['5_exempt_taxable'])
                        ),
                        "isup_rev" => array(
                            "txval" => 0,
                            "iamt" => 0,
                            "camt" => 0,
                            "samt" => 0,
                            "csamt" => 0
                        ),
                        "osup_nongst" => array(
                            "txval" => 0
                        )
                    ),
                    "inter_sup" => array(
                        "unreg_details" => array(),
                        "comp_details" => array(),
                        "uin_details" => array()
                    ),
                    "itc_elg" => array(
                        "itc_avl" => array(
                            array("ty" => "IMPG", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0),
                            array("ty" => "IMPS", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0),
                            array("ty" => "ISRC", "iamt" => 0, "camt" => fix_gstr3b_floats($gstr3b['4A_cgst']), "samt" => fix_gstr3b_floats($gstr3b['4A_sgst']), "csamt" => 0),
                            array("ty" => "ISD", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0),
                            array("ty" => "OTH", "iamt" => fix_gstr3b_floats($gstr3b['4A_igst']), "camt" => 0, "samt" => 0, "csamt" => 0)
                        ),
                        "itc_rev" => array(
                            array("ty" => "RUL", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0),
                            array("ty" => "OTH", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0)
                        ),
                        "itc_net" => array(
                            "iamt" => fix_gstr3b_floats($gstr3b['4A_igst']),
                            "camt" => fix_gstr3b_floats($gstr3b['4A_cgst']),
                            "samt" => fix_gstr3b_floats($gstr3b['4A_sgst']),
                            "csamt" => 0
                        ),
                        "itc_inelg" => array(
                            array("ty" => "RUL", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0),
                            array("ty" => "OTH", "iamt" => 0, "camt" => 0, "samt" => 0, "csamt" => 0)
                        )
                    ),
                    "inward_sup" => array(
                        "isup_details" => array(
                            array("ty" => "NONGST", "inter" => 0, "intra" => fix_gstr3b_floats($gstr3b['4_other_nongst'])),
                            array("ty" => "GST", "inter" => fix_gstr3b_floats($gstr3b['4A_igst']), "intra" => fix_gstr3b_floats($gstr3b['4A_cgst'] + $gstr3b['4A_sgst']))
                        )
                    ),
                    "intr_ltfee" => array(
                        "intr_details" => array(
                            "iamt" => 0,
                            "camt" => 0,
                            "samt" => 0,
                            "csamt" => 0
                        )
                    )
                );

                //JSON
                $gstr3b_json_dt1 = json_encode($gstr3b_json, JSON_PRETTY_PRINT);

            }
        ?>
        </table><br/><br/><br/>
        <script type="text/javascript" src="table_sorting_wauto_slno.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <!--<script type="text/javascript" src="table_download_excel.js"></script>-->
        <script>
            function download_to_excel2(tbl_name, filename, chosen){
                if(chosen === 'excel'){
                    table_heading_to_normal1();
                    cdate_format1();

                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");

                    cdate_format2();
                    table_heading_to_standard_filters();
                }
                else if(chosen === 'json'){
                    var gstr3b_json_dt1 = <?php echo $gstr3b_json_dt1; ?>;
                    var m1 = <?php echo date('m', strtotime($fdate)); ?>;
                    var m = m1 < 10 ? '0' + m1 : m1.toString();
                    var Y = <?php echo date('Y', strtotime($fdate)); ?>;
                    const jsonStr = JSON.stringify(gstr3b_json_dt1, null, 2);
                    const blob = new Blob([jsonStr], { type: "application/json" });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement("a");
                    link.href = url;
                    link.download = "GSTR3B_"+m+""+Y+"_Report.json";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                }
            }
        </script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $fhtml; ?>';
                document.getElementById("head_names").innerHTML = html;
                    
                $('#export').select2();
                document.getElementById("export").value = "display";
                $('#export').select2();
                table_sort();
                table_sort2();
                table_sort3();
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