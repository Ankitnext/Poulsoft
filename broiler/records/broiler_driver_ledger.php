<?php
//broiler_driver_ledger.php
include "../newConfig.php";
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $desig_acode = array();
while($row = mysqli_fetch_assoc($query)){ $desig_acode[$row['code']] = $row['code']; }

$desig_list = implode("','",$desig_acode);
$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $driver_code = $driver_name = array();
while($row = mysqli_fetch_assoc($query)){ $driver_code[$row['code']] = $row['code']; $driver_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_vehicle` WHERE `dflag` = '0' ORDER BY `registration_number` ASC";
$query = mysqli_query($conn,$sql); $vehicle_code = $vehicle_name = array();
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_category = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Driver-Transport Charges'";
$query = mysqli_query($conn,$sql); $driver_coa = "";
while($row = mysqli_fetch_assoc($query)){ $driver_coa = $row['code']; }

$fdate = $tdate = date("Y-m-d"); $drivers = $vehicles = $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $drivers = $_POST['drivers'];
    $vehicles = $_POST['vehicles'];
    $sectors = $_POST['sectors'];
	$url = "../PHPExcel/Examples/broiler_driver_ledger-Excel.php?fromdate=".$fdate."&todate=".$tdate."&drivers=".$drivers."&vehicles=".$vehicles."&sectors=".$sectors;
}
else{
    $url = "";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
         
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                    .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead2_empty_row { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" style="width:auto;" align="center" id="main_table">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="8" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Driver Transportation Cost Report</h5></th>
                    <th colspan="2" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_driver_ledger.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="12">
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
                                    <label>Driver</label>
                                    <select name="drivers" id="drivers" class="form-control select2">
                                        <option value="all" <?php if($drivers == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($driver_code as $dcode){ if($driver_name[$dcode] != ""){ ?>
                                        <option value="<?php echo $dcode; ?>" <?php if($drivers == $dcode){ echo "selected"; } ?>><?php echo $driver_name[$dcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Vehicle</label>
                                    <select name="vehicles" id="vehicles" class="form-control select2">
                                        <option value="all" <?php if($vehicles == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vehicle_code as $dcode){ if($vehicle_name[$dcode] != ""){ ?>
                                        <option value="<?php echo $dcode; ?>" <?php if($vehicles == $dcode){ echo "selected"; } ?>><?php echo $vehicle_name[$dcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Sector</label>
                                    <select name="sectors" id="sectors" class="form-control select2" style="width:250px;">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($sectors == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <!--<div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php //if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php //if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php //if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>-->
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
                if($drivers == "all"){ $driver_filter = ""; } else{ $driver_filter = " AND `driver_code` = '$drivers'"; }
                if($vehicles == "all"){ $vehicle_filter = ""; } else{ $vehicle_filter = " AND `vehicle_code` = '$vehicles'"; }
                if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `location` = '$sectors'"; }

                $html = '';
                $html = '<thead class="thead3" align="center">
                <tr align="center">
                    <th>Sl. No.</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Trnum</th>
                    <th>Driver</th>
                    <th>Vehicle</th>
                    <th>From Location</th>
                    <th>Location</th>
                    <th>Remarks</th>
                    <th>DC Number</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Cr</th>
                    <th>Dr</th>
                </tr>
            </thead>
            <tbody>';
                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` LIKE '$driver_coa' AND `date` >= '$fdate' AND `date` <= '$tdate'".$vehicle_filter."".$driver_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`crdr` ASC";
                $query = mysqli_query($conn,$sql); $c = 0; $tot_qty = $tot_camt = $tot_damt = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $c++;
                    $date = date("d.m.Y",strtotime($row['date']));
                    $type = $row['etype'];
                    $trnum = $row['trnum'];
                    $dname = $driver_name[$row['driver_code']];
                    $vname = $vehicle_name[$row['vehicle_code']];
                    $sname = $sector_name[$row['location']];
                    $remarks = $row['remarks'];
                    $dc_no = $row['dc_no'];
                    $quantity = $row['quantity']; if($quantity == ""){ $quantity = 0; }
                    $price = $row['price']; if($price == ""){ $price = 0; }
                    $amount = $row['amount']; if($amount == ""){ $amount = 0; }
                    $tot_qty += (float)$quantity;
                    $html .= '<tr>';
                    $html .= '<td style="text-align:center;">'.$c.'</td>';
                    $html .= '<td style="text-align:left;">'.$date.'</td>';
                    $html .= '<td style="text-align:left;">'.$type.'</td>';
                    $html .= '<td style="text-align:left;">'.$trnum.'</td>';
                    $html .= '<td style="text-align:left;">'.$dname.'</td>';
                    $html .= '<td style="text-align:left;">'.$vname.'</td>';
                    $html .= '<td style="text-align:left;"></td>';
                    $html .= '<td style="text-align:left;">'.$sname.'</td>';
                    $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                    $html .= '<td style="text-align:left;">'.$dc_no.'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind($quantity).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                    if($row['crdr'] == "CR"){
                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                        $html .= '<td style="text-align:right;"></td>';
                        $tot_camt += (float)$amount;
                    }
                    else if($row['crdr'] == "DR"){
                        $html .= '<td style="text-align:right;"></td>';
                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                        $tot_damt += (float)$amount;
                    }
                    else{
                        $html .= '<td style="text-align:left;color:red;" colspan="2">Not Added Properly, Please edit and update the transaction.</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th colspan="10">Total</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tot_qty).'</th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tot_camt).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tot_damt).'</th>';
                $html .= '</tr>';
                $html .= '</tfoot>';

                echo $html;
            }
        ?>
        </table>
        <script>
            function checkval(){
                var items = document.getElementById("items").value;
                var sectors = document.getElementById("sectors").value;
                if(items.match("select")){
                    alert("Please select Item");
                    document.getElementById("items").focus();
                    return true;
                }
                else if(sectors.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("sectors").focus();
                    return true;
                }
                else{
                    return true;
                }
            }
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
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>