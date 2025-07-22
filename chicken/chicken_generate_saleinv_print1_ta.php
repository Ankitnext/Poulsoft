<?php
//chicken_generate_saleinv_print1.php
session_start(); include "newConfig.php";
include "number_format_ind.php";

$ids = $_GET['trnum'];
if($ids != ""){
    //$sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `flag` = '0' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` = 'chicken_display_generalsales6.php'";
    // changed by Harish on 07-07-2025 removed trlink condtion
    $sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `flag` = '0' AND `tdflag` = '0' AND `pdflag` = '0' ";
    $query = mysqli_query($conn,$sql); $c = 0;
    while($row = mysqli_fetch_assoc($query)){
        $date = $row['date'];
        $trnum = $row['invoice'];
        $vcode = $row['customercode'];
        $jali_no = $row['jali_no'];
        $bookinvoice = $row['bookinvoice'];
        $itemcode[$c] = $row['itemcode'];
        $jals[$c] = round($row['jals'],5);
        $birds[$c] = round($row['birds'],5);
        $totalweight[$c] = round($row['totalweight'],5);
        $emptyweight[$c] = round($row['emptyweight'],5);
        $netweight[$c] = round($row['netweight'],5);
        $warehouse = $row['warehouse'];
        $price[$c] = round($row['itemprice'],5);
        $amount[$c] = round($row['totalamt'],5);

        $tcdsper = round($row['tcdsper'],2);
        $tcds_type1 = $row['tcds_type1'];
        $tcds_type2 = $row['tcds_type2'];
        $tcdsamt = round($row['tcdsamt'],2);
        $tports_code = $row['transporter_code'];
        $freight_amount = round($row['freight_amount'],2);
        $dressing_charge = round($row['dressing_charge'],2);
        $roundoff_type1 = $row['roundoff_type1'];
        $roundoff_type2 = $row['roundoff_type2'];
        $roundoff = round($row['roundoff'],2);
        $finaltotal = round($row['finaltotal'],2);
        $driver = $row['drivercode'];
        $vehicle = $row['vehiclecode'];
        $remarks = $row['remarks'];
        $c++;
    } $c = $c - 1;
    if((int)$c > 12){ $incr = $c; } else{ $incr = 12; }

    //Fetch Account Modes
    $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
    while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }
    
    $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$cash_mode' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
    $query = mysqli_query($conn,$sql); $cash_ramt = 0; $cash_trno = $cash_rcode = "";
    while($row = mysqli_fetch_assoc($query)){ $cash_trno = $row['trnum']; $cash_rcode = $row['method']; $cash_ramt = round($row['amount'],5); }

    $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$bank_mode' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
    $query = mysqli_query($conn,$sql); $bank_ramt = 0; $bank_trno = $bank_rcode = "";
    while($row = mysqli_fetch_assoc($query)){ $bank_trno = $row['trnum']; $bank_rcode = $row['method']; $bank_ramt = round($row['amount'],5); }

    /*Company Profile*/
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }
    
    // Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
    if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }
    
    // Print Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'chicken_generate_saleinv_print1.php' AND `field_function` LIKE 'Print Invoice' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $prt_flag = mysqli_num_rows($query); //$avou_flag = 1;
    
    // Warehouse
    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
    
    //Customer Details
    $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $ven_name = $ven_phone = array();
    while($row = mysqli_fetch_assoc($query)){ $ven_name[$row['code']] = $row['name']; $ven_phone[$row['code']] = $row['phoneno']; }
    
    //Item Details
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

    //Date Wise 1st week opening Balance
    $old_inv = ""; $oinv = $orct = $arct = $ocdn = $occn = $omortality = $oreturns = $obcramt = $obdramt = 0;
    $sql1 = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$vcode'"; $query = mysqli_query($conn,$sql1); 
    while($row = mysqli_fetch_assoc($query)){ if($row['obtype'] == "Cr"){ $obcramt = $row['obamt']; } else if($row['obtype'] == "Dr"){ $obdramt = $row['obamt']; } else{ } }
    $sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` <= '$date' AND `customercode` LIKE '$vcode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
    $sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` <= '$date' AND `ccode` LIKE '$vcode' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $orct += (float)$row['tamt']; } }
    $sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` = '$date' AND `ccode` LIKE '$vcode' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $arct += (float)$row['tamt']; } }
    $sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` <= '$date' AND `ccode` LIKE '$vcode' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn += (float)$row['tamt']; } else { $occn += (float)$row['tamt']; } } }
    $sql = "SELECT * FROM `main_mortality` WHERE `date` <= '$date' AND `ccode` = '$vcode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $omortality += (float)$row['amount']; } }
    $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$date' AND `vcode` = '$vcode' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $oreturns += (float)$row['amount']; } }
    
    $sales = (((float)$oinv + (float)$ocdn + (float)$obdramt) - (float)$finaltotal);
    $receipts = (((float)$orct + (float)$omortality + (float)$oreturns + (float)$occn + (float)$obcramt) - (float)$arct);
    $balance = ((float)$sales - (float)$receipts);

    $sales = ((float)$oinv + (float)$ocdn + (float)$obdramt);
    $receipts = ((float)$orct + (float)$omortality + (float)$oreturns + (float)$occn + (float)$obcramt);
    $p_bal = ((float)$sales - (float)$receipts);

    //fetch Latest Receipt
    $sql = "SELECT * FROM `customer_receipts` WHERE `date` < '$date' AND `ccode` LIKE '$vcode' AND `active` = '1' AND `id` IN (SELECT MAX(id) as id FROM `customer_receipts` WHERE  `date` < '$date' AND `ccode` LIKE '$vcode' AND `active` = '1')";
    $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query); $lrct_date = ""; $lrct_amt = 0;
    if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $lrct_date = date("d.m.Y",strtotime($row['date'])); $lrct_amt = (float)$row['amount']; } }
?>
<html>
    <head>
        <?php include "header_head1.php"; ?>
        <style>
           @page {
    size: A4 portrait;
    margin: 10mm;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f7f7f7;
}

.print-container {
    width: 235mm;         /* Slightly less than full A4 width to avoid overflow */
    min-height: 277mm;     /* Adjust height to fit all content neatly */
    margin: 0 auto;
    background: #fff;
    padding: 10mm 10mm;
    box-sizing: border-box;
    overflow: hidden;
}

.main-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.main-table, 
.main-table th, 
.main-table td {
    /* border: 1px solid #000; */
    padding: 5px;
}

.main-table tr {
    line-height: 26px;
}

.signature-cell {
    height: 60px;
    vertical-align: bottom;
    text-align: right;
    padding-right: 20px;
}

@media print {
    body {
        background: none;
    }

    .print-container {
        box-shadow: none;
        width: 100%;
        min-height: 100%;
        padding: 0;
        margin: 0;
    }

    button {
        display: none !important;
    }
}

        </style>
    </head>
    <?php if($prt_flag > 0){  ?>
    <body>
   <?php } else { ?>
    <body onload="printAndClose();">
        <?php } ?>
        <div class="print-container">
        <div class="a4-container">
            <table class="main-table" style="width:99%;border: 4px solid;">
                <thead>
                    <tr>
                        <td colspan="6"><?php echo $cdetails; ?></td>
                        <td colspan="1"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                    </tr>
                    <tr>
    <td colspan="7" style="padding:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <th colspan="4" style="border-top:1px solid black;border-right:1px solid black;width:600px;">Billed To: 
                    <span style="font-size:26px;"><?php echo $ven_name[$vcode]; ?></span><br/>
                    <span style="font-size:26px;"><?php echo $ven_phone[$vcode]; ?></span>
                </th>
                <th colspan="1" style="border-top:1px solid black;text-align:right;">Invoice No</th>
                <th style="border-top:1px solid black;width:5px;">:</th>
                <th style="border-top:1px solid black;text-align:left;width: 190px;"><?php echo $trnum; ?></th>
            </tr>
            <tr>
                <th colspan="4" style="border-right:1px solid black;"></th>
                <th colspan="1" style="text-align:right;">Date of Invoice</th>
                <th style="width:5px;">:</th>
                <th style="text-align:left;"><?php echo date("d.m.Y",strtotime($date)); ?></th>
            </tr>
            <tr>
                <th colspan="4" style="border-bottom:1px solid black;border-right:1px solid black;"><br/></th>
                <th colspan="1" style="border-bottom:1px solid black;text-align:right;"></th>
                <th style="border-bottom:1px solid black;width:5px;"></th>
                <th style="border-bottom:1px solid black;text-align:left;"></th>
            </tr>
           
        </table>
    </td>
</tr>
                    <tr>
                        <th colspan="7" style="border-bottom:1px solid black;">पेमेंट देते समय लिख के ले और अपना पुराना बिल पे ध्यान दे।.</th>
                    </tr>
                    <tr>
                        <th style="text-align:center;border:1px solid black;width:100px;">Quantity</th>
                        <th style="text-align:center;border:1px solid black;width:200px;">Kgs</th>
                        <th style="text-align:center;border:1px solid black;" colspan="3">Description of Goods</th>
                        <th style="text-align:center;border:1px solid black;width:100px;">Price</th>
                        <th style="text-align:center;border:1px solid black;">Amount</th>
                    </tr>
                    <tr>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;" colspan="4"></th>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;" colspan="2">MAGIL JAMA (Dated: <?php echo $lrct_date; ?>)</th>
                        <th style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;text-align:right;"><?php echo number_format_ind($lrct_amt); ?>&nbsp;Dr.</th>
                    </tr>
                    <tr>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;" colspan="4"></th>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;" colspan="2">Old udhari</th>
                        <th style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;text-align:right;"><?php echo number_format_ind($balance); ?>&nbsp;Dr.</th>
                    </tr>
                    <?php
                    $html = '';
                    for($c = 0;$c <= $incr;$c++){
                        if(!empty($item_name[$itemcode[$c]]) && (float)$netweight[$c] > 0){
                            $html .= '<tr>';
                            $html .= '<td style="text-align:right;border-right:1px solid black;width:100px;text-align:center;"><h4><b>'.str_replace(".00","",number_format_ind($birds[$c])).'</b></h4></td>';
                            $html .= '<td style="text-align:right;border-right:1px solid black;width:200px;text-align:center;"><h4><b>'.number_format_ind($netweight[$c]).'</b></h4></td>';
                            $html .= '<td style="text-align:left;border-right:1px solid black;" colspan="3"><h4><b>'.$item_name[$itemcode[$c]].'</b></h4></td>';
                            $html .= '<td style="text-align:right;border-right:1px solid black;width:100px;"><h4><b>'.number_format_ind($price[$c]).'</b></h4></td>';
                            $html .= '<td style="text-align:right;"><h4><b>'.number_format_ind($amount[$c]).'</b></h4></td>';
                            $html .= '</tr>';
                        }
                        else{
                            $html .= '<tr>';
                            $html .= '<td style="text-align:right;border-right:1px solid black;visibility:hidden;">0</td>';
                            $html .= '<td style="text-align:right;border-right:1px solid black;"></td>';
                            $html .= '<td style="text-align:left;border-right:1px solid black;" colspan="3"></td>';
                            $html .= '<td style="text-align:right;border-right:1px solid black;"></td>';
                            $html .= '<td style="text-align:right;"></td>';
                            $html .= '</tr>';
                        }
                    }
                    echo $html;
                    ?>
                    <tr>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;"></th>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;" colspan="5">Less : JAMA</th>
                        <th style="border-top:1px solid black;border-bottom:1px solid black;text-align:right;"><?php echo number_format_ind($arct); ?></th>
                    </tr>
                    <tr>
                        <th colspan="4" style="border-top:1px solid black;width:650px;">Remarks:<b><?php echo $remarks; ?></b></th>
                        <th colspan="2" style="border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;">Grand Total: </th>
                        <th style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;"><?php echo number_format_ind($finaltotal); ?></th>
                    </tr>
                    <tr>
                        <th colspan="4" style="border:none;"></th>
                        <th colspan="2" style="border-left:1px solid black;">Total udhari</th>
                        <th style="text-align:right;"><?php echo number_format_ind($p_bal); ?>&nbsp;Dr.</th>
                    </tr>

                    <tr style="line-height: 30px;">
                        <th style="border-top:1px solid black;border-bottom:1px solid black;">JALI NO</th>
                        <th style="border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;" colspan="3"><?php echo $jali_no; ?></th>
                        <th rowspan="2" colspan="4" class="signature-cell" style="border-top:1px solid black;">Authorized Signatory</th>
                    </tr>

                    <tr style="line-height: 30px;">
                    <th style="border-top:1px solid black;border-bottom:1px solid black;">Driver Name</th>
                    <th style="border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;" colspan="2" ><?php echo $driver; ?></th>
                    <!-- <th rowspan="2" colspan="4"  style="border-right:1px solid black;visibility: hidden;">Hello</th> -->
                    </tr>
                    <!-- <tr style="line-height: 30px;">
                        <th style="border-top:1px solid black;border-bottom:1px solid black;">Driver Name</th>
                        <th colspan="2" style="border-top:1px solid black;border-bottom:1px solid black;"><?php echo $driver; ?></th>
                        <th colspan="4" class="signature-cell" style="border-top:1px solid black;visibility: hidden;">Hello</th>
                    </tr> -->
                </thead>
                <tbody>
                    <tr>

                    </tr>
                </tbody>
            </table>
            <?php if($prt_flag > 0){  ?>
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 30px;">
                <button onclick="printAndClose()" style="padding: 10px 25px; font-size: 18px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Print Invoice
                </button>
                <button onclick="printClose()" style="padding: 10px 25px; font-size: 18px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Save
                </button>
                <button onclick="printSave()" style="padding: 10px 25px; font-size: 18px; background-color: #3665f4ff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Cancel
                </button>
            </div>
            <?php } ?>
        </div>
        </div>
        <?php include "header_foot1.php"; ?>
        <script>
            function printAndClose() {
                window.print();
                setTimeout(() => {
                   // window.close();
                }, 1000);
            }
            function printAndClose1() {
                window.print(); // Only prints when button is clicked
            }

            function printClose() {
                window.location.href = "chicken_display_generalsales6.php";
            }
            function printSave() {
                window.location.href = "chicken_edit_generalsales6.php?utype=edit&trnum=<?php echo $trnum ?>";
            }
        </script>
    </body>
</html>
<?php
}
?>

