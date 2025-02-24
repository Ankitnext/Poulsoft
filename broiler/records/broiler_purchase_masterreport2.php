<?php
//broiler_purchase_masterreport2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_purchase_masterreport2.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_purchase_masterreport2.php?db=$db&userid=".$user_code;
}

$file_name = "Supplier Purchase Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Master Report Format*/
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$href%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = ""; $slno_flag = 0;
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
                $all_col_numbs[$key_id] = $cna;
                if($act_col_numbs[$key_id] == "sl_no"){ $slno_flag = 1; }
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
                $all_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $emp_code = $emp_name = array();
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_vehicle` WHERE `dflag` = '0' ORDER BY `registration_number` ASC";
$query = mysqli_query($conn,$sql); $vehicle_code = $vehicle_name = array();
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; $vendor_mobl[$row['code']] = $row['mobile1']; $vendor_addr[$row['code']] = $row['baddress']; $vendor_gstno[$row['code']] = $row['gstinno']; }

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_category = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $vendors = $item_cat = $items = $sectors = "all"; $gst_amt_flag = "on";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $item_cat = $_POST['item_cat'];
    $items = $_POST['items'];
    $sectors = $_POST['sectors'];
    $excel_type = $_POST['export'];
    if(!empty($_POST['gst_amt_flag'])){ $gst_amt_flag = $_POST['gst_amt_flag']; } else { $gst_amt_flag = "off"; }
}

if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `warehouse` = '$sectors'"; }
if($vendors == "all"){ $vendor_filter = ""; } else{ $vendor_filter = " AND `vcode` = '$vendors'"; }
if($gst_amt_flag != "on"){ $gstamt_filter = ""; } else{ $gstamt_filter = " AND `gst_amt` > '0'"; }

if($items != "all"){ $item_filter = " AND `icode` IN ('$items')"; }
else if($item_cat == "all"){ $item_filter = ""; }
else{
    $icat_list = $item_filter = "";
    foreach($item_code as $icode){
        $item_category[$icode];
        if($item_category[$icode] == $item_cat){
            if($icat_list == ""){
                $icat_list = $icode;
            }
            else{
                $icat_list = $icat_list."','".$icode;
            }
        }
    }
    $item_filter = " AND `icode` IN ('$icat_list')";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
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
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supplier</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sup_code as $cust){ if($sup_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $sup_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Category</label>
                                    <select name="item_cat" id="item_cat" class="form-control select2" onchange="fetch_item_list();">
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Items</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php if($item_cat == "all"){ ?>
                                        <?php foreach($item_code as $icodes){ if($item_name[$icodes] != ""){ ?>
                                        <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                        <?php } } }
                                        else{
                                            foreach($item_code as $icodes){
                                                if($item_cat == $item_category[$icodes]){
                                                ?>
                                                <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                                <?php
                                                }
                                            }
                                        }
                                            ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Warehouse</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $whcode){ if($farm_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $farm_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>With GST Amt</label>
                                    <input type="checkbox" name="gst_amt_flag" id="gst_amt_flag" class="form-control"  style='transform: scale(.7);' <?php if($gst_amt_flag == "on"){ echo "checked"; } ?>>
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
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td>
                <div id='control_sh'>
                    <?php
                        //for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; if(!empty($act_col_numbs[$key_id])){ echo "<br/>".$act_col_numbs[$key_id]."@".$key_id; } else if(!empty($nac_col_numbs[$key_id1])){ echo "<br/>".$nac_col_numbs[$key_id1]."@".$key_id1; } else{ } }
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($all_col_numbs[$key_id]) || !empty($nac_col_numbs[$key_id1])){
                                if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl. No.</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_date" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Date</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_trnum" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_trnum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_trnum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transaction No.</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "transaction_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="transaction_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transaction Type</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_name" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier Name</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mobile_no1" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mobile_no1"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mobile_no1" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mobile No.</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_address" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_address"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_address" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier Address</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_gst_no" || !empty($nac_col_numbs[$key_id1]) && !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_gst_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_gst_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier GST No</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_billno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_billno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_billno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Doc No</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_itemname" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_itemname"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_itemname" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Name</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_snt_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_snt_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_snt_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sent Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_dcrcd_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_dcrcd_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_dcrcd_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Dc Rcv Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_nof_bags" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_nof_bags"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_nof_bags" onclick="update_masterreport_status(this.id);" '.$checked.'><span>No. of Bags</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_bag_weight" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_bag_weight"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_bag_weight" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bag Weight</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_mortality" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_mortality"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_mortality" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mortality</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_shortage" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_shortage"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_shortage" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Shortage</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_weaks" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_weaks"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_weaks" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Weaks</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_excess_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_excess_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_excess_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Excess Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_chicks_pur" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_chicks_pur"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_chicks_pur" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier Chick Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_rcd_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_rcd_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_rcd_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Received Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_short_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_short_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_short_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Shortage Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_fre_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_fre_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_fre_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Free Qty</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_rate" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_rate"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_rate" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Rate</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_dis_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_dis_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_dis_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Discount %</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_dis_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_dis_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_dis_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Discount Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_gst_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_gst_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_gst_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST %</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_gst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_gst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_gst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_cgst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_cgst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_cgst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>CGST Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_sgst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_sgst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_sgst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>SGST Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_igst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_igst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_igst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>IGST Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_tgst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_tgst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_tgst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total GST</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_tcds_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_tcds_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_tcds_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TDS %</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_tcds_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_tcds_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_tcds_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TDS Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_item_tamt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_item_tamt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_item_tamt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Total</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_freight_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_freight_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_freight_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Type</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_freight_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_freight_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_freight_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_freight_damt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_freight_damt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_freight_damt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Disc. Amt</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_freight_pay_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_freight_pay_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_freight_pay_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Pay Type</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_freight_pay_acc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_freight_pay_acc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_freight_pay_acc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Pay Acc</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_freight_acc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_freight_acc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_freight_acc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Acc</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_round_off" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_round_off"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_round_off" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Round Off</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_finl_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_finl_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_finl_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Final Total</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_remarks" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_remarks"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_remarks" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Remarks</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_warehouse" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_warehouse"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_warehouse" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sector/Farm</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_farm_batch" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_farm_batch"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_farm_batch" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Batch</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_bag_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_bag_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_bag_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bag Code</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_bag_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_bag_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_bag_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bag Count</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_batch_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_batch_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_batch_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch No.</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_exp_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_exp_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_exp_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Expiry Date</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_vehicle_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_vehicle_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_vehicle_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Vehicle No.</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_driver_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_driver_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_driver_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Driver Name</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_amt_basedon" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_amt_basedon"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_amt_basedon" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Amt Based On</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_ttype" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_ttype"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_ttype" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Invoice Type</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_gc_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_gc_flag"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_gc_flag" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gc Flag</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_direct_sale_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_direct_sale_flag"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_direct_sale_flag" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Direct Sale</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_lqt_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_lqt_flag"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_lqt_flag" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lag Test</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_grade_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_grade_flag"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_grade_flag" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Grade</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_receipt_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_receipt_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_receipt_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Receipt Date</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_receive_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_receive_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_receive_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Received Date</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_addedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_addedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_addedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Added By</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_addedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_addedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_addedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Added Time</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_updatedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_updatedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_updatedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Edited By</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_updatedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_updatedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_updatedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Edited Time</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supplier_trlink" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supplier_trlink"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supplier_trlink" onclick="update_masterreport_status(this.id);" '.$checked.'><span>File Name</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gross_pur_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gross_pur_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gross_pur_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gross value</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "per_bag_rate" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "per_bag_rate"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="per_bag_rate" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Per Bag Rate</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sup_icat_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sup_icat_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sup_icat_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item category</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "frt_crg_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "frt_crg_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="frt_crg_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Charge %</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sup_net_rate" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sup_net_rate"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sup_net_rate" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Net Price</span>'; }
                                else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sup_iwise_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sup_iwise_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sup_iwise_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Wise Amount</span>'; }
                                else{ }
                            }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table id="main_table" class="tbl" align="center">
            <?php
            $fhead_html = $nhead_html = '';
            $html .= '<thead class="thead3" id="head_names">';
            $fhead_html .= '<tr align="center">';
            $nhead_html .= '<tr align="center">';
            for($i = 1;$i <= $col_count;$i++){
                $key_id = "A:1:".$i;
                if(!empty($act_col_numbs[$key_id])){
                    if($act_col_numbs[$key_id] == "sl_no"){ $nhead_html .= '<th>Sl. No.</th>'; $fhead_html .= '<th id="order_num">Sl. No.</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_date"){ $nhead_html .= '<th>Date</th>'; $fhead_html .= '<th id="order_date">Date</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_trnum"){ $nhead_html .= '<th>Transaction No.</th>'; $fhead_html .= '<th id="order">Transaction No.</th>'; }
                    else if($act_col_numbs[$key_id] == "transaction_type"){ $nhead_html .= '<th>Transaction Type</th>'; $fhead_html .= '<th id="order">Transaction Type</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_name"){ $nhead_html .= '<th>Supplier Name</th>'; $fhead_html .= '<th id="order">Supplier Name</th>'; }
                    else if($act_col_numbs[$key_id] == "mobile_no1"){ $nhead_html .= '<th>Mobile No.</th>'; $fhead_html .= '<th id="order">Mobile No.</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_address"){ $nhead_html .= '<th>Supplier Address</th>'; $fhead_html .= '<th id="order">Supplier Address</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_gst_no"){ $nhead_html .= '<th>Supplier GST No</th>'; $fhead_html .= '<th id="order">Supplier GST No</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_billno"){ $nhead_html .= '<th>Doc No</th>'; $fhead_html .= '<th id="order">Doc No</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_itemname"){ $nhead_html .= '<th>Item Name</th>'; $fhead_html .= '<th id="order">Item Name</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_snt_qty"){ $nhead_html .= '<th>Sent Qty</th>'; $fhead_html .= '<th id="order_num">Sent Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_dcrcd_qty"){ $nhead_html .= '<th>Dc Rcv Qty</th>'; $fhead_html .= '<th id="order_num">Dc Rcv Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_nof_bags"){ $nhead_html .= '<th>No. of Bags</th>'; $fhead_html .= '<th id="order_num">No. of Bags</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_bag_weight"){ $nhead_html .= '<th>Bag Weight</th>'; $fhead_html .= '<th id="order_num">Bag Weight</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_mortality"){ $nhead_html .= '<th>Mortality</th>'; $fhead_html .= '<th id="order_num">Mortality</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_shortage"){ $nhead_html .= '<th>Shortage</th>'; $fhead_html .= '<th id="order_num">Shortage</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_weaks"){ $nhead_html .= '<th>Weaks</th>'; $fhead_html .= '<th id="order_num">Weaks</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_excess_qty"){ $nhead_html .= '<th>Excess Qty</th>'; $fhead_html .= '<th id="order_num">Excess Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_chicks_pur"){ $nhead_html .= '<th>Supplier Chick Qty</th>'; $fhead_html .= '<th id="order_num">Supplier Chick Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_rcd_qty"){ $nhead_html .= '<th>Received Qty</th>'; $fhead_html .= '<th id="order_num">Received Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_short_qty"){ $nhead_html .= '<th>Shortage Qty</th>'; $fhead_html .= '<th id="order_num">Shortage Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_fre_qty"){ $nhead_html .= '<th>Free Qty</th>'; $fhead_html .= '<th id="order_num">Free Qty</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_rate"){ $nhead_html .= '<th>Rate</th>'; $fhead_html .= '<th id="order_num">Rate</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_dis_per"){ $nhead_html .= '<th>Discount %</th>'; $fhead_html .= '<th id="order_num">Discount %</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_dis_amt"){ $nhead_html .= '<th>Discount Amt</th>'; $fhead_html .= '<th id="order_num">Discount Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_gst_per"){ $nhead_html .= '<th>GST %</th>'; $fhead_html .= '<th id="order_num">GST %</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_gst_amt"){ $nhead_html .= '<th>GST Amt</th>'; $fhead_html .= '<th id="order_num">GST Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_cgst_amt"){ $nhead_html .= '<th>CGST Amt</th>'; $fhead_html .= '<th id="order_num">CGST Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_sgst_amt"){ $nhead_html .= '<th>SGST Amt</th>'; $fhead_html .= '<th id="order_num">SGST Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_igst_amt"){ $nhead_html .= '<th>IGST Amt</th>'; $fhead_html .= '<th id="order_num">IGST Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_tgst_amt"){ $nhead_html .= '<th>Total GST</th>'; $fhead_html .= '<th id="order_num">Total GST</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_tcds_per"){ $nhead_html .= '<th>TDS %</th>'; $fhead_html .= '<th id="order_num">TDS %</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_tcds_amt"){ $nhead_html .= '<th>TDS Amt</th>'; $fhead_html .= '<th id="order_num">TDS Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_item_tamt"){ $nhead_html .= '<th>Item Total</th>'; $fhead_html .= '<th id="order_num">Item Total</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_freight_type"){ $nhead_html .= '<th>Freight Type</th>'; $fhead_html .= '<th id="order">Freight Type</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_freight_amt"){ $nhead_html .= '<th>Freight Amt</th>'; $fhead_html .= '<th id="order_num">Freight Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_freight_damt"){ $nhead_html .= '<th>Freight Amt</th>'; $fhead_html .= '<th id="order_num">Freight Disc. Amt</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_freight_pay_type"){ $nhead_html .= '<th>Freight Pay Type</th>'; $fhead_html .= '<th id="order">Freight Pay Type</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_freight_pay_acc"){ $nhead_html .= '<th>Freight Pay Acc</th>'; $fhead_html .= '<th id="order">Freight Pay Acc</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_freight_acc"){ $nhead_html .= '<th>Freight Acc</th>'; $fhead_html .= '<th id="order">Freight Acc</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_round_off"){ $nhead_html .= '<th>Round Off</th>'; $fhead_html .= '<th id="order_num">Round Off</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_finl_amt"){ $nhead_html .= '<th>Final Total</th>'; $fhead_html .= '<th id="order_num">Final Total</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_remarks"){ $nhead_html .= '<th>Remarks</th>'; $fhead_html .= '<th id="order">Remarks</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_warehouse"){ $nhead_html .= '<th>Sector/Farm</th>'; $fhead_html .= '<th id="order">Sector/Farm</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_farm_batch"){ $nhead_html .= '<th>Farm Batch</th>'; $fhead_html .= '<th id="order">Farm Batch</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_bag_code"){ $nhead_html .= '<th>Bag Code</th>'; $fhead_html .= '<th id="order">Bag Code</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_bag_count"){ $nhead_html .= '<th>Bag Count</th>'; $fhead_html .= '<th id="order_num">Bag Count</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_batch_no"){ $nhead_html .= '<th>Batch No.</th>'; $fhead_html .= '<th id="order">Batch No.</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_exp_date"){ $nhead_html .= '<th>Expiry Date</th>'; $fhead_html .= '<th id="order_date">Expiry Date</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_vehicle_code"){ $nhead_html .= '<th>Vehicle No.</th>'; $fhead_html .= '<th id="order">Vehicle No.</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_driver_code"){ $nhead_html .= '<th>Driver Name</th>'; $fhead_html .= '<th id="order">Driver Name</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_amt_basedon"){ $nhead_html .= '<th>Amt Based On</th>'; $fhead_html .= '<th id="order">Amt Based On</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_ttype"){ $nhead_html .= '<th>Invoice Type</th>'; $fhead_html .= '<th id="order">Invoice Type</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_gc_flag"){ $nhead_html .= '<th>Gc Flag</th>'; $fhead_html .= '<th id="order_num">Gc Flag</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_direct_sale_flag"){ $nhead_html .= '<th>Direct Sale</th>'; $fhead_html .= '<th id="order_num">Direct Sale</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_lqt_flag"){ $nhead_html .= '<th>Lab Test</th>'; $fhead_html .= '<th id="order_num">Lab Test</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_grade_flag"){ $nhead_html .= '<th>Grade</th>'; $fhead_html .= '<th id="order_num">Grade</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_receipt_date"){ $nhead_html .= '<th>Receipt Date</th>'; $fhead_html .= '<th id="order_date">Receipt Date</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_receive_date"){ $nhead_html .= '<th>Received Date</th>'; $fhead_html .= '<th id="order_date">Received Date</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_addedemp"){ $nhead_html .= '<th>Added By</th>'; $fhead_html .= '<th id="order">Added By</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_addedtime"){ $nhead_html .= '<th>Added Time</th>'; $fhead_html .= '<th id="order_date">Added Time</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_updatedemp"){ $nhead_html .= '<th>Edited By</th>'; $fhead_html .= '<th id="order">Edited By</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_updatedtime"){ $nhead_html .= '<th>Edited Time</th>'; $fhead_html .= '<th id="order_date">Edited Time</th>'; }
                    else if($act_col_numbs[$key_id] == "supplier_trlink"){ $nhead_html .= '<th>File Name</th>'; $fhead_html .= '<th id="order">File Name</th>'; }

                    else if($act_col_numbs[$key_id] == "gross_pur_amt"){ $nhead_html .= '<th>Gross value</th>'; $fhead_html .= '<th id="order_num">Gross value</th>'; }
                    else if($act_col_numbs[$key_id] == "per_bag_rate"){ $nhead_html .= '<th>Per Bag Rate</th>'; $fhead_html .= '<th id="order_num">Per Bag Rate</th>'; }
                    else if($act_col_numbs[$key_id] == "sup_icat_name"){ $nhead_html .= '<th>Item category</th>'; $fhead_html .= '<th id="order">Item category</th>'; }
                    else if($act_col_numbs[$key_id] == "frt_crg_per"){ $nhead_html .= '<th>Freight Charge %</th>'; $fhead_html .= '<th id="order_num">Freight Charge %</th>'; }
                    else if($act_col_numbs[$key_id] == "sup_net_rate"){ $nhead_html .= '<th>Net Price</th>'; $fhead_html .= '<th id="order_num">Net Price</th>'; }
                    else if($act_col_numbs[$key_id] == "sup_iwise_amt"){ $nhead_html .= '<th>Item Wise Amount</th>'; $fhead_html .= '<th id="order_num">Item Wise Amount</th>'; }
                    else{ }
                }
            }
            $fhead_html .= '</tr>';
            $nhead_html .= '</tr>';
            $html .= $fhead_html;
            $html .= '</thead>';
            if(isset($_POST['submit_report']) == true){
                $html .= '<tbody class="tbody1" id="tbody1">';
                $sql_record = "SELECT DISTINCT(trnum) as trnum,COUNT(trnum) as tcount,SUM(gst_amt) as gst_amt FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$item_filter."".$gstamt_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `date`,`trnum` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $inv_count = $inv_tgst_amt = array();
                while($row = mysqli_fetch_assoc($query)){ $inv_count[$row['trnum']] = $row['tcount']; $inv_tgst_amt[$row['trnum']] += $row['gst_amt']; }

                $tot_supplier_snt_qty = $tot_supplier_dcrcd_qty = $tot_supplier_nof_bags = $tot_supplier_bag_weight = $tot_supplier_mortality = $tot_supplier_shortage = 
                $tot_supplier_weaks = $tot_supplier_excess_qty = $tot_supplier_chicks_pur = $tot_supplier_rcd_qty = $tot_supplier_short_qty = $tot_supplier_fre_qty = 
                $tot_supplier_rate = $tot_grs_pur_amt = $tot_supplier_dis_per = $tot_supplier_dis_amt = $tot_supplier_gst_per = $tot_supplier_gst_amt = $tot_supplier_cgst_amt = 
                $tot_supplier_sgst_amt = $tot_supplier_igst_amt = $tot_supplier_tgst_amt = $tot_supplier_tcds_per = $tot_supplier_tcds_amt = $tot_supplier_item_tamt = $tot_supplier_freight_amt = $tot_supplier_freight_damt = 
                $tot_supplier_round_off = $tot_supplier_finl_amt = $tot_inet_amt = $tot_supplier_bag_count = 0;

                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$item_filter."".$gstamt_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $sl_no = $tot_bds = $tot_qty = $tot_amt = 0; $old_inv = "";
                while($row = mysqli_fetch_assoc($query)){
                    $supplier_date = date('d.m.Y',strtotime($row['date']));
                    $supplier_trnum = $row['trnum'];
                    $transaction_type = "Purchase Invoice";
                    $supplier_name = $sup_name[$row['vcode']];
                    $mobile_no1 = $vendor_mobl[$row['vcode']];
                    $supplier_address = $vendor_addr[$row['vcode']];
                    $supplier_gst_no = $vendor_gstno[$row['vcode']];
                    $supplier_billno = $row['billno'];
                    if(!empty($item_name[$row['icode']])){ $supplier_itemname = $item_name[$row['icode']]; } else { $supplier_itemname = ""; }
                    $supplier_snt_qty = $row['snt_qty'];
                    $supplier_dcrcd_qty = $row['dcrcd_qty'];
                    $supplier_nof_bags = $row['nof_bags'];
                    $supplier_bag_weight = $row['bag_weight'];
                    $supplier_mortality = $row['mort'];
                    $supplier_shortage = $row['shortage'];
                    $supplier_weaks = $row['weeks'];
                    $supplier_excess_qty = $row['excess_qty'];
                    $supplier_chicks_pur = $row['chicks_pur'];
                    $supplier_rcd_qty = $row['rcd_qty'];
                    $supplier_short_qty = $row['short_qty'];
                    $supplier_fre_qty = $row['fre_qty'];
                    $supplier_rate = $row['rate'];
                    $supplier_dis_per = $row['dis_per'];
                    $supplier_dis_amt = $row['dis_amt'];
                    $supplier_gst_per = $row['gst_per'];
                    $supplier_gst_amt = $row['gst_amt'];
                    
                    $supplier_cgst_amt = $supplier_sgst_amt = $supplier_igst_amt = 0;
                    if(!empty($gst_isflag[$row['gst_code']]) && $gst_isflag[$row['gst_code']] == '1'){
                        $supplier_igst_amt = $supplier_gst_amt;
                    }
                    else{
                        $gamt = 0; $gamt = round(($supplier_gst_amt / 2),2);
                        $supplier_cgst_amt = $supplier_sgst_amt = $gamt;
                    }
                    $supplier_tcds_per = $row['tcds_per'];
                    $supplier_tcds_amt = $row['tcds_amt'];
                    //$supplier_item_tamt = $row['item_tamt'];
                    $supplier_item_tamt = (float)$row['item_tamt'] - (float)$row['gst_amt'] - (float)$row['dis_amt'];
                    $supplier_freight_type = $row['freight_type']; if($supplier_freight_type == "0.00"){ $supplier_freight_type = ""; }
                    $supplier_freight_amt = $row['freight_amt'];
                    $supplier_freight_damt = $row['freight_disc_amt'];
                    $supplier_freight_pay_type = $row['freight_pay_type'];
                    if(!empty($coa_name[$row['freight_pay_acc']])){ $supplier_freight_pay_acc = $coa_name[$row['freight_pay_acc']]; } else{ $supplier_freight_pay_acc = ""; } if($supplier_freight_pay_acc == "select"){ $supplier_freight_pay_acc = ""; }
                    if(!empty($coa_name[$row['freight_acc']])){ $supplier_freight_acc = $coa_name[$row['freight_acc']]; } else{ $supplier_freight_acc = ""; }
                    $supplier_round_off = $row['round_off'];
                    $supplier_finl_amt = $row['finl_amt'];
                    $supplier_remarks = $row['remarks'];
                    if(!empty($farm_name[$row['warehouse']])){ $supplier_warehouse = $farm_name[$row['warehouse']]; } else{ $supplier_warehouse = ""; }
                    if(!empty($batch_name[$row['farm_batch']])){ $supplier_farm_batch = $batch_name[$row['farm_batch']]; } else{ $supplier_farm_batch = ""; }
                    if(!empty($item_name[$row['bag_code']])){ $supplier_bag_code = $item_name[$row['bag_code']]; } else{ $supplier_bag_code = ""; }
                    $supplier_bag_count = $row['bag_count'];
                    $supplier_batch_no = $row['batch_no'];
                    if($row['exp_date'] == ""){ $supplier_exp_date = ""; } else{ $supplier_exp_date = date('d.m.Y',strtotime($row['exp_date'])); } if($supplier_exp_date == "01.01.1970"){ $supplier_exp_date = ""; }
                    if(!empty($vehicle_name[$row['vehicle_code']])){ $supplier_vehicle_code = $vehicle_name[$row['vehicle_code']]; } else{ $supplier_vehicle_code = $row['vehicle_code']; }
                    
                    if(!empty($emp_name[$row['driver_code']])){ $supplier_driver_code = $emp_name[$row['driver_code']]; }
                    else if(!empty($emp_name[$db_emp_code[$row['driver_code']]])){ $supplier_driver_code = $emp_name[$db_emp_code[$row['driver_code']]]; }
                    else if(!empty($emp_name[$sp_emp_code[$row['driver_code']]])){ $supplier_driver_code = $emp_name[$sp_emp_code[$row['driver_code']]]; }
                    else{ $supplier_driver_code = $row['driver_code']; }

                    $supplier_amt_basedon = $row['amt_cal_basedon'];
                    $supplier_ttype = $row['ttype'];
                    $supplier_gc_flag = $row['gc_flag'];
                    $supplier_direct_sale_flag = $row['direct_sale_flag'];
                    $supplier_lqt_flag = $row['lqt_flag'];
                    $supplier_grade_flag = $row['grade_flag'];
                    if(empty($row['receipt_date']) || $row['receipt_date'] == ""){ $supplier_receipt_date = ""; } else{ $supplier_receipt_date = date('d.m.Y',strtotime($row['receipt_date'])); } if($supplier_receipt_date == "01.01.1970"){ $supplier_receipt_date = ""; }
                    if(empty($row['receive_date']) || $row['receive_date'] == ""){ $supplier_receive_date = ""; } else{ $supplier_receive_date = date('d.m.Y',strtotime($row['receive_date'])); } if($supplier_receive_date == "01.01.1970"){ $supplier_receive_date = ""; }
                    
                    if(!empty($emp_name[$row['addedemp']])){ $supplier_addedemp = $emp_name[$row['addedemp']]; }
                    else if(!empty($emp_name[$db_emp_code[$row['addedemp']]])){ $supplier_addedemp = $emp_name[$db_emp_code[$row['addedemp']]]; }
                    else if(!empty($emp_name[$sp_emp_code[$row['addedemp']]])){ $supplier_addedemp = $emp_name[$sp_emp_code[$row['addedemp']]]; }
                    else{ $supplier_addedemp = ""; }

                    $supplier_addedtime = date('d.m.Y h:i:s:A',strtotime($row['addedtime']));

                    if(!empty($emp_name[$row['updatedemp']])){ $supplier_updatedemp = $emp_name[$row['updatedemp']]; }
                    else if(!empty($emp_name[$db_emp_code[$row['updatedemp']]])){ $supplier_updatedemp = $emp_name[$db_emp_code[$row['updatedemp']]]; }
                    else if(!empty($emp_name[$sp_emp_code[$row['updatedemp']]])){ $supplier_updatedemp = $emp_name[$sp_emp_code[$row['updatedemp']]]; }
                    else{ $supplier_updatedemp = ""; }

                    $supplier_updatedtime = date('d.m.Y h:i:s:A',strtotime($row['updatedtime']));
                    $supplier_trlink = $row['trlink'];

                    $grs_pur_amt = 0; $grs_pur_amt = (float)$supplier_snt_qty * (float)$supplier_rate;
                    $per_bag_rate = 0; if((float)$supplier_bag_count != 0){ $per_bag_rate = (float)$grs_pur_amt / (float)$supplier_bag_count; }
                    $sup_item_cats = ""; $sup_item_cats = $icat_name[$item_category[$row['icode']]];
                    $frt_per = 0; if((float)$supplier_snt_qty != 0){ $frt_per = round((((float)$supplier_freight_damt / (float)$supplier_snt_qty) * 1000),2); }
                    $net_rate = 0; if((float)$supplier_snt_qty != 0){ $net_rate = round(((((float)$grs_pur_amt - (float)$supplier_dis_amt) / (float)$supplier_snt_qty)),2); }
                    $inet_amt = 0; $inet_amt = round(((float)$grs_pur_amt - (float)$supplier_dis_amt - (float)$supplier_freight_damt),2);

                    $tot_supplier_snt_qty += (float)$supplier_snt_qty;
                    $tot_supplier_dcrcd_qty += (float)$supplier_dcrcd_qty;
                    $tot_supplier_nof_bags += (float)$supplier_nof_bags;
                    $tot_supplier_bag_weight += (float)$supplier_bag_weight;
                    $tot_supplier_mortality += (float)$supplier_mortality;
                    $tot_supplier_shortage += (float)$supplier_shortage;
                    $tot_supplier_weaks += (float)$supplier_weaks;
                    $tot_supplier_excess_qty += (float)$supplier_excess_qty;
                    $tot_supplier_chicks_pur += (float)$supplier_chicks_pur;
                    $tot_supplier_rcd_qty += (float)$supplier_rcd_qty;
                    $tot_supplier_short_qty += (float)$supplier_short_qty;
                    $tot_supplier_fre_qty += (float)$supplier_fre_qty;
                    $tot_supplier_rate += (float)$supplier_rate;
                    $tot_supplier_dis_per += (float)$supplier_dis_per;
                    $tot_supplier_dis_amt += (float)$supplier_dis_amt;
                    $tot_supplier_gst_per += (float)$supplier_gst_per;
                    $tot_supplier_gst_amt += (float)$supplier_gst_amt;
                    $tot_supplier_cgst_amt += (float)$supplier_cgst_amt;
                    $tot_supplier_sgst_amt += (float)$supplier_sgst_amt;
                    $tot_supplier_igst_amt += (float)$supplier_igst_amt;
                    $tot_supplier_tcds_per += (float)$supplier_tcds_per;
                    $tot_supplier_item_tamt += (float)$supplier_item_tamt;

                    $tot_grs_pur_amt += (float)$grs_pur_amt;
                    $tot_supplier_bag_count += (float)$supplier_bag_count;
                    $tot_inet_amt += (float)$inet_amt;

                    //$tot_supplier_freight_damt += (float)$supplier_freight_damt;
                    $supplier_tgst_amt = 0;
                    if($old_inv != $supplier_trnum){
                        $old_inv = $supplier_trnum;
                        $tot_supplier_tcds_amt += (float)$supplier_tcds_amt;
                        $tot_supplier_freight_damt += (float)$supplier_freight_damt;
                        $tot_supplier_round_off += (float)$supplier_round_off;
                        $tot_supplier_finl_amt += (float)$supplier_finl_amt;
                        $sl_no++;

                        $supplier_tgst_amt = (float)$inv_tgst_amt[$supplier_trnum];
                        $tot_supplier_tgst_amt += (float)$supplier_tgst_amt;
                        
                    }
                    else{ }
                    $html .= '<tr>';
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                        if(!empty($act_col_numbs[$key_id])){
                            if($act_col_numbs[$key_id] == "sl_no"){ $html .= '<td title="Sl. No." style="text-align:center;">'.$sl_no.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_date"){ $html .= '<td title="Date" style="text-align:left;">'.$supplier_date.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_trnum"){ $html .= '<td title="Transaction No." style="text-align:left;">'.$supplier_trnum.'</td>'; }
                            else if($act_col_numbs[$key_id] == "transaction_type"){ $html .= '<td title="Transaction Type" style="text-align:left;">'.$transaction_type.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_name"){ $html .= '<td title="Supplier Name" style="text-align:left;">'.$supplier_name.'</td>'; }
                            else if($act_col_numbs[$key_id] == "mobile_no1"){ $html .= '<td title="Mobile No." style="text-align:left;">'.$mobile_no1.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_address"){ $html .= '<td title="Supplier Address" style="text-align:left;">'.$supplier_address.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_gst_no"){ $html .= '<td title="Supplier GST No" style="text-align:left;">'.$supplier_gst_no.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_billno"){ $html .= '<td title="Doc No" style="text-align:left;">'.$supplier_billno.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_itemname"){ $html .= '<td title="Item Name" style="text-align:left;">'.$supplier_itemname.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_snt_qty"){ $html .= '<td title="Sent Qty" style="text-align:right;">'.number_format_ind($supplier_snt_qty).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_dcrcd_qty"){ $html .= '<td title="Dc Rcv Qty" style="text-align:right;">'.number_format_ind($supplier_dcrcd_qty).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_nof_bags"){ $html .= '<td title="No. of Bags" style="text-align:right;">'.number_format_ind($supplier_nof_bags).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_bag_weight"){ $html .= '<td title="Bag Weight" style="text-align:right;">'.number_format_ind($supplier_bag_weight).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_mortality"){ $html .= '<td title="Mortality" style="text-align:right;">'.number_format_ind($supplier_mortality).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_shortage"){ $html .= '<td title="Shortage" style="text-align:right;">'.number_format_ind($supplier_shortage).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_weaks"){ $html .= '<td title="Weaks" style="text-align:right;">'.number_format_ind($supplier_weaks).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_excess_qty"){ $html .= '<td title="Excess Qty" style="text-align:right;">'.number_format_ind($supplier_excess_qty).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_chicks_pur"){ $html .= '<td title="Supplier Chick Qty" style="text-align:right;">'.number_format_ind($supplier_chicks_pur).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_rcd_qty"){ $html .= '<td title="Received Qty" style="text-align:right;">'.number_format_ind($supplier_rcd_qty).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_short_qty"){ $html .= '<td title="Shortage Qty" style="text-align:right;">'.number_format_ind($supplier_short_qty).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_fre_qty"){ $html .= '<td title="Free Qty" style="text-align:right;">'.number_format_ind($supplier_fre_qty).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_rate"){ $html .= '<td title="Rate" style="text-align:right;">'.number_format_ind($supplier_rate).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_dis_per"){ $html .= '<td title="Discount %" style="text-align:right;">'.number_format_ind($supplier_dis_per).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_dis_amt"){ $html .= '<td title="Discount Amt" style="text-align:right;">'.number_format_ind($supplier_dis_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_gst_per"){ $html .= '<td title="GST %" style="text-align:right;">'.number_format_ind($supplier_gst_per).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_gst_amt"){ $html .= '<td title="GST Amt" style="text-align:right;">'.number_format_ind($supplier_gst_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_cgst_amt"){ $html .= '<td title="CGST Amt" style="text-align:right;">'.number_format_ind($supplier_cgst_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_sgst_amt"){ $html .= '<td title="SGST Amt" style="text-align:right;">'.number_format_ind($supplier_sgst_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_igst_amt"){ $html .= '<td title="IGST Amt" style="text-align:right;">'.number_format_ind($supplier_igst_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_tgst_amt"){ $html .= '<td title="Total GST" style="text-align:right;">'.number_format_ind($supplier_tgst_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_tcds_per"){ $html .= '<td title="TDS %" style="text-align:right;">'.number_format_ind($supplier_tcds_per).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_tcds_amt"){ $html .= '<td title="TDS Amt" style="text-align:right;">'.number_format_ind($supplier_tcds_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_item_tamt"){ $html .= '<td title="Item Total" style="text-align:right;">'.number_format_ind($supplier_item_tamt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_freight_type"){ $html .= '<td title="Freight Type" style="text-align:right;">'.$supplier_freight_type.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_freight_amt"){ $html .= '<td title="Freight Amt" style="text-align:right;">'.number_format_ind($supplier_freight_damt).'</td>'; }
                            //else if($act_col_numbs[$key_id] == "supplier_freight_damt"){ $html .= '<td title="Freight Amt" style="text-align:right;">'.number_format_ind($supplier_freight_damt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_freight_pay_type"){ $html .= '<td title="Freight Pay Type" style="text-align:left;">'.$supplier_freight_pay_type.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_freight_pay_acc"){ $html .= '<td title="Freight Pay Acc" style="text-align:left;">'.$supplier_freight_pay_acc.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_freight_acc"){ $html .= '<td title="Freight Acc" style="text-align:left;">'.$supplier_freight_acc.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_round_off"){ $html .= '<td title="Round Off" style="text-align:right;">'.number_format_ind($supplier_round_off).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_finl_amt"){ $html .= '<td title="Final Total" style="text-align:right;">'.number_format_ind($supplier_finl_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_remarks"){ $html .= '<td title="Remarks" style="text-align:left;">'.$supplier_remarks.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_warehouse"){ $html .= '<td title="Sector/Farm" style="text-align:left;">'.$supplier_warehouse.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_farm_batch"){ $html .= '<td title="Farm Batch" style="text-align:left;">'.$supplier_farm_batch.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_bag_code"){ $html .= '<td title="Bag Code" style="text-align:left;">'.$supplier_bag_code.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_bag_count"){ $html .= '<td title="Bag Count" style="text-align:right;">'.str_replace(".00","",number_format_ind($supplier_bag_count)).'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_batch_no"){ $html .= '<td title="Batch No." style="text-align:left;">'.$supplier_batch_no.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_exp_date"){ $html .= '<td title="Expiry Date" style="text-align:left;">'.$supplier_exp_date.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_vehicle_code"){ $html .= '<td title="Vehicle No." style="text-align:left;">'.$supplier_vehicle_code.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_driver_code"){ $html .= '<td title="Driver Name" style="text-align:left;">'.$supplier_driver_code.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_amt_basedon"){ $html .= '<td title="Amt Based On" style="text-align:left;">'.$supplier_amt_basedon.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_ttype"){ $html .= '<td title="Invoice Type" style="text-align:left;">'.$supplier_ttype.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_gc_flag"){ $html .= '<td title="Gc Flag" style="text-align:left;">'.$supplier_gc_flag.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_direct_sale_flag"){ $html .= '<td title="Direct Sale" style="text-align:left;">'.$supplier_direct_sale_flag.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_lqt_flag"){ $html .= '<td title="Lab Test" style="text-align:left;">'.$supplier_lqt_flag.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_grade_flag"){ $html .= '<td title="Grade" style="text-align:left;">'.$supplier_grade_flag.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_receipt_date"){ $html .= '<td title="Receipt Date" style="text-align:left;">'.$supplier_receipt_date.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_receive_date"){ $html .= '<td title="Received Date" style="text-align:left;">'.$supplier_receive_date.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_addedemp"){ $html .= '<td title="Added By" style="text-align:left;">'.$supplier_addedemp.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_addedtime"){ $html .= '<td title="Added Time" style="text-align:left;">'.$supplier_addedtime.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_updatedemp"){ $html .= '<td title="Edited By" style="text-align:left;">'.$supplier_updatedemp.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_updatedtime"){ $html .= '<td title="Edited Time" style="text-align:left;">'.$supplier_updatedtime.'</td>'; }
                            else if($act_col_numbs[$key_id] == "supplier_trlink"){ $html .= '<td title="File Name" style="text-align:left;">'.$supplier_trlink.'</td>'; }
                            
                            else if($act_col_numbs[$key_id] == "gross_pur_amt"){ $html .= '<td title="Gross value" style="text-align:right;">'.number_format_ind($grs_pur_amt).'</td>'; }
                            else if($act_col_numbs[$key_id] == "per_bag_rate"){ $html .= '<td title="Per Bag Rate" style="text-align:right;">'.number_format_ind($per_bag_rate).'</td>'; }
                            else if($act_col_numbs[$key_id] == "sup_icat_name"){ $html .= '<td title="Item category" style="text-align:left;">'.$sup_item_cats.'</td>'; }
                            else if($act_col_numbs[$key_id] == "frt_crg_per"){ $html .= '<td title="Freight Charge %" style="text-align:right;">'.number_format_ind($frt_per).'</td>'; }
                            else if($act_col_numbs[$key_id] == "sup_net_rate"){ $html .= '<td title="Net Price" style="text-align:right;">'.number_format_ind($net_rate).'</td>'; }
                            else if($act_col_numbs[$key_id] == "sup_iwise_amt"){ $html .= '<td title="Item Wise Amount" style="text-align:right;">'.number_format_ind($inet_amt).'</td>'; }
                            else{ }
                        }
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                
                $avg_per_bag_rate = $avg_frt_per = $avg_net_rate = $avg_frt_dper = 0;
                if((float)$tot_supplier_bag_count != 0){ $avg_per_bag_rate = (float)$tot_grs_pur_amt / (float)$tot_supplier_bag_count; }
                //if((float)$tot_supplier_snt_qty != 0){ $avg_frt_per = round((((float)$tot_supplier_freight_amt / (float)$tot_supplier_snt_qty) * 1000),2); }
                if((float)$tot_supplier_snt_qty != 0){ $avg_frt_dper = round((((float)$tot_supplier_freight_damt / (float)$tot_supplier_snt_qty) * 1000),2); }
                if((float)$tot_supplier_snt_qty != 0){ $avg_net_rate = round(((((float)$tot_grs_pur_amt - (float)$tot_supplier_dis_amt) / (float)$tot_supplier_snt_qty)),2); }

                $html .= '<tfoot class="thead3">';
                $html .= '<tr class="thead4">';
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id])){
                        if($act_col_numbs[$key_id] == "sl_no"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_date"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_trnum"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "transaction_type"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_name"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "mobile_no1"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_address"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_gst_no"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_billno"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_itemname"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_snt_qty"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_snt_qty,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_dcrcd_qty"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_dcrcd_qty,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_nof_bags"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_nof_bags,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_bag_weight"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_bag_weight,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_mortality"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_mortality,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_shortage"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_shortage,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_weaks"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_weaks,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_excess_qty"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_excess_qty,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_chicks_pur"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_chicks_pur,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_rcd_qty"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_rcd_qty,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_short_qty"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_short_qty,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_fre_qty"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_fre_qty,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_rate"){ $html .= '<th style="text-align:right;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_dis_per"){ $html .= '<th style="text-align:right;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_dis_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_dis_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_gst_per"){ $html .= '<th style="text-align:right;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_gst_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_gst_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_cgst_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_cgst_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_sgst_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_sgst_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_igst_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_igst_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_tgst_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_tgst_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_tcds_per"){ $html .= '<th style="text-align:right;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_tcds_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_tcds_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_item_tamt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_item_tamt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_freight_type"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_freight_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_freight_damt,2)).'</th>'; }
                        //else if($act_col_numbs[$key_id] == "supplier_freight_damt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_freight_damt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_freight_pay_type"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_freight_pay_acc"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_freight_acc"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_round_off"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_round_off,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_finl_amt"){ $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_supplier_finl_amt,2)).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_remarks"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_warehouse"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_farm_batch"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_bag_code"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_bag_count"){ $html .= '<th style="text-align:right;">'.number_format_ind($tot_supplier_bag_count).'</th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_batch_no"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_exp_date"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_vehicle_code"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_driver_code"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_amt_basedon"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_ttype"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_gc_flag"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_direct_sale_flag"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_lqt_flag"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_grade_flag"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_receipt_date"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_receive_date"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_addedemp"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_addedtime"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_updatedemp"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_updatedtime"){ $html .= '<th style="text-align:left;"></th>'; }
                        else if($act_col_numbs[$key_id] == "supplier_trlink"){ $html .= '<th style="text-align:left;"></th>'; }
                            
                        else if($act_col_numbs[$key_id] == "gross_pur_amt"){ $html .= '<td title="Gross value" style="text-align:right;">'.number_format_ind($tot_grs_pur_amt).'</td>'; }
                        else if($act_col_numbs[$key_id] == "per_bag_rate"){ $html .= '<td title="Per Bag Rate" style="text-align:right;">'.number_format_ind($avg_per_bag_rate).'</td>'; }
                        else if($act_col_numbs[$key_id] == "sup_icat_name"){ $html .= '<td title="Item category" style="text-align:left;"></td>'; }
                        else if($act_col_numbs[$key_id] == "frt_crg_per"){ $html .= '<td title="Freight Charge %" style="text-align:right;">'.number_format_ind($avg_frt_dper).'</td>'; }
                        else if($act_col_numbs[$key_id] == "sup_net_rate"){ $html .= '<td title="Net Price" style="text-align:right;">'.number_format_ind($avg_net_rate).'</td>'; }
                        else if($act_col_numbs[$key_id] == "sup_iwise_amt"){ $html .= '<td title="Item Wise Amount" style="text-align:right;">'.number_format_ind($tot_inet_amt).'</td>'; }
                        else{ }
                    }
                }
                $html .= '</tr>';
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

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
                console.log("test");
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
                    console.log("test1");

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
                console.log("test");
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
                    console.log("test1");

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
                var slno_flag = '<?php echo $slno_flag; ?>';
                if(parseInt(slno_flag) == 1){
                    var rcount = document.getElementById("tbody1").rows.length;
                    var myTable = document.getElementById('tbody1');
                    var j = 0;
                    for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
                }
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
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html +='<?php echo $nhead_html; ?>';
                    $('#head_names').append(html);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    var link = document.createElement("a");
                    link.download = filename+".xlsx";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html +='<?php echo $nhead_html; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
            
        </script>
        <script>
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icodes){
                        $icats = $item_category[$icodes];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icodes){
                            $icats = $item_category[$icodes];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            function update_masterreport_status(a){
                var file_url = '<?php echo $href; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>