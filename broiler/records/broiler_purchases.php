<?php
//broiler_purchases.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    //broiler_day_records.php
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}

/* bag_column flag check*/
$sql3 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Purchase Report' AND `field_function` LIKE 'bag_column' AND (`user_access` LIKE '%$addedemp%' OR `user_access` = 'all')";
$query3 = mysqli_query($conn, $sql3);
$ccount3 = mysqli_num_rows($query3);
if ($ccount3 > 0) {
	while ($row3 = mysqli_fetch_assoc($query3)) {
		$bag_column = $row3['flag'];
	}
} else {
	mysqli_query($conn, "INSERT INTO `extra_access` ( `field_name`, `field_function`, `user_access`, `flag`) VALUES ( 'Purchase Report', 'bag_column', 'all', '0')");
	$bag_column =  0;
}
if ($bag_column == '') {
	$bag_column =  0;
}


 $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Purchase Report' AND `field_function` = 'Download Multiple Files' AND (`user_access` = 'all' OR `user_access` = '$user_code')";
$query = mysqli_query($conn, $sql); $ccount4 = mysqli_num_rows($query);
if ($ccount4 > 0) {
	while ($row3 = mysqli_fetch_assoc($query)) {
		$downloadmfiles_flag = $row3['flag'];
	}
} else {
	mysqli_query($conn, "INSERT INTO `extra_access` ( `field_name`, `field_function`, `user_access`, `flag`) VALUES ( 'Purchase Report', 'Download Multiple Files', 'all', '0')");
	$downloadmfiles_flag =  0;
}
if ($downloadmfiles_flag == '') {
	$downloadmfiles_flag =  0;
}



/* bag_column flag check*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line`  WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors`  WHERE 1 ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE 1 ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code'];
    $farm_ccode[$row['code']] = $row['farm_code'];
    $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code'];
    $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code'];
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `feed_bagcapacity` WHERE active = 1 AND dflag = 0 "; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $all_feed_bag_capacity[$row['code']] = $row['bag_size']; }

$fdate = $tdate = date("Y-m-d"); $item_cat = $items = $vendors = $branches = $sectors = $upload_status = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $item_cat = $_POST['item_cat'];
    $items = $_POST['items'];
    $vendors = $_POST['vendors'];
    $branches = $_POST['branches'];
    $sectors = $_POST['sectors'];
    $upload_status = $_POST['upload_status'];

    if($branches == "all"){ $branch_filter = ""; }
    else{
        $farm_list = "";
        foreach($farm_code as $fcode){
            if(!empty($farm_branch[$fcode]) && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $branch_filter = " AND `warehouse` IN ('$farm_list')";
    }
    if($sectors == "all"){ $wcodes = ""; } else{ $wcodes = " AND `warehouse` = '$sectors'"; }
    if($vendors == "all"){ $vcodes = ""; } else{ $vcodes = " AND `vcode` = '$vendors'"; }
    if($upload_status == "all"){ $upload_filter = ""; } else if($upload_status == "1"){ $upload_filter = " AND `file_url1` IS NOT NULL AND `file_url1` != '' "; }else if($upload_status == "0"){ $upload_filter = " AND ( `file_url1` IS  NULL OR `file_url1` = '' )"; }
    //if($item_cat == "all"){ $icodes = ""; } else{ $icodes = " AND `icode` = '$vendors'"; }

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

    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_supplier =$vendor_name[$_POST['vendors']]; if ($export_supplier == "") { $export_supplier = "All"; }
    $export_item_cat =$icat_name[$_POST['item_cat']]; if ($export_item_cat == "") { $export_item_cat = "All"; }
    $export_items = $item_name[$_POST['items']]; if ($export_items == "") { $export_items = "All"; }
    $export_branches = $branch_name[$_POST['branches']]; if ($export_branches == "") { $export_branches = "All"; }
    $export_sectors = $sector_name[$_POST['sectors']]; if ( $export_sectors == "") {  $export_sectors = "All"; }
    $export_upload_status = $_POST['upload_status']; 
    if ($export_upload_status == "" || $export_upload_status = "all") { $export_upload_status  = "All"; }
    else if  ($export_upload_status == "1" ) {  $export_upload_status  = "Uploaded";  } 
    else if  ($export_upload_status == "0" ) {  $export_upload_status  = "Not Uploaded";  } 
    
     
     if ($export_fdate == $export_tdate)
     {$filename = "Purchase Summary_".$export_tdate; }
      else {
     $filename = "Purchase Summary_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/PurchaseReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&item_cat=".$item_cat."&items=".$items."&vendors=".$vendors."&sectors=".$sectors;
}

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_pc_goodsreceipt", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_pc_goodsreceipt LIKE poulso6_admin_broiler_broilermaster.broiler_pc_goodsreceipt;"; mysqli_query($conn,$sql1); }

?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
       
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="15" align="center"><?php echo $row['cdetails']; ?><h5>Purchase Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            
            <?php if($db == ''){?>
            <form action="broiler_purchases.php" method="post">
                <?php } else { ?>
                <form action="broiler_purchases.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="17">
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
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
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
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Warehouse</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Upload Status</label>
                                    <select name="upload_status" id="upload_status" class="form-control select2">
                                        <option value="all" <?php if($upload_status == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="1" <?php if($upload_status == "1"){ echo "selected"; } ?>>-Uploaded-</option>
                                        <option value="0" <?php if($upload_status == "0"){ echo "selected"; } ?>>-Not Uploaded-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('main_body', 'Purchase Summary','<?php echo $filename;?>', this.options[this.selectedIndex].value,'<?php echo $bag_column;?>')">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </table>
            </form>
            <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            
            </div>
                            
           <table id="main_body" class="tbl" align="center"  style="width:1300px;">
          
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
           
                <tr align="center">
                    <th colspan="15" align="center"><?php echo $row['cdetails']; ?><h5>Purchase Report</h5></th>
                </tr>
            
            <?php } ?>
            <tr>
                       
                       <th colspan="17">
                                   <div class="row">
                                       <div class="m-2 form-group">
                                           <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Supplier: <?php echo $export_supplier; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Category: <?php echo $export_item_cat; ?></label>
                                       </div>
                                       
                                       <div class="m-2 form-group">
                                           <label>Items: <?php echo $export_items; ?></label>
                                       </div>
                                       
                                       <div class="m-2 form-group">
                                           <label>Branch: <?php echo $export_branches; ?></label>
                                       </div>
                                       
                                       <div class="m-2 form-group">
                                           <label>Farm/Warehouse: <?php echo $export_sectors; ?></label>
                   
                                       </div>
                                       
                                       <div class="m-2 form-group">
                                           <label>Upload Status: <?php echo $export_upload_status; ?></label>
                   
                                       </div>
                                       <div class="m-2 form-group">
                                           <label><br/></label>

                                       </div>

                                       
                               </th>
                           
                       </tr>
                      
                   </thead>
                   
            <thead class="thead3" align="center">
                <tr align="center"  id="header_sorting">
                    <th id='order_date'>Date</th>
                    <th id='order_date'>Received Date</th>
                    <th id='order'>Supplier</th>
                    <th id='order'>Dc. No.</th>
                    <th id='order'>Invoice</th>
                    <th id='order'>Item Code</th>
                    <th id='order'>Item</th>
                    <th id='order_num'>Sent Qty</th>
                    <th id='order_num'>Received Qty</th>
                    <?php  if($bag_column == 1){ ?>
                        <th id='order_num'>Received Bags</th>
                    <?php } ?>
                    <th id='order_num'>Rate</th>
                    <th id='order_num'>Amount</th>
                    <th id='order'>Vehicle No</th>
                    <th id='order'>Farm/Warehouse</th>
                    <th id='order'>Remarks</th>
                    <th id='order'>Upload Status</th>
                    <th id='order'>Added By</th>
                    <th id='order_date'>Added Time</th>
                </tr>
            </thead>
            <thead class="thead3" align="center" style="width:1212px; display:none;">            
       
            
            <tr align="center">
                    <th>Date</th>
                    <th>Received Date</th>
                    <th>Supplier</th>
                    <th>Dc. No.</th>
                    <th>Invoice</th>
                    <th>Item Code</th>
                    <th>Item</th>
                    <th>Sent Qty</th>
                    <th>Received Qty</th>
                    <?php  if($bag_column == 1){ ?>
                        <th>Received Bags</th>
                    <?php } ?>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Vehicle No</th>
                    <th>Farm/Warehouse</th>
                    <th>Remarks</th>
                    <th>Upload Status</th>
                    <th>Added By</th>
                    <th>Added Time</th>
                </tr>
            </thead>
           
           <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vcodes."".$item_filter."".$wcodes."".$branch_filter."".$upload_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $gr_trnums = $gr_date = array();
                while($row = mysqli_fetch_assoc($query)){ $gr_trnums[$row['gr_trnum']] = $row['gr_trnum']; }

                if(sizeof($gr_trnums) > 0){
                    $gr_list = implode("','",$gr_trnums);
                    $sql = "SELECT * FROM `broiler_pc_goodsreceipt` WHERE `trnum` IN ('$gr_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $gr_date[$row['trnum']] = $row['date'];
                    }
                }
                

                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vcodes."".$item_filter."".$wcodes."".$branch_filter."".$upload_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_rqty = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){

                    $file_count = 0;

                    if($all_feed_bag_capacity[$row['icode']] > 0){

                        $bags_qty = ($row['rcd_qty'] + $row['fre_qty'])/$all_feed_bag_capacity[$row['icode']];

                    }else  if($all_feed_bag_capacity['all'] > 0){
                        $bags_qty = ($row['rcd_qty'] + $row['fre_qty'])/$all_feed_bag_capacity['all'];
                    }else{
                        $bags_qty = 0;
                    }

                    $tot_bag_qty += $bags_qty ;


                    if((int)$downloadmfiles_flag == 1){
                        if($row['file_url1'] != ""){
                            $file_count++;
                            $link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/".$row['file_url1']; $file_list[$link] = $link;
                        }
                        if($row['file_url2'] != ""){
                            $file_count++;
                            $link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/".$row['file_url2']; $file_list[$link] = $link;
                        }
                        if($row['file_url3'] != ""){
                            $file_count++;
                            $link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/".$row['file_url3']; $file_list[$link] = $link;
                        }
                    }
                    $received_date = "";
                    if(!empty($gr_date[$row['gr_trnum']]) && $gr_date[$row['gr_trnum']] != "" && date("d.m.Y",strtotime($gr_date[$row['gr_trnum']])) != "01.01.1970"){ $received_date = date("d.m.Y",strtotime($gr_date[$row['gr_trnum']])); }


                    //Added Employee Name
                    if(!empty($emp_name[$row['addedemp']])){ $supplier_addedemp = $emp_name[$row['addedemp']]; }
                    else if(!empty($emp_name[$db_emp_code[$row['addedemp']]])){ $supplier_addedemp = $emp_name[$db_emp_code[$row['addedemp']]]; }
                    else if(!empty($emp_name[$sp_emp_code[$row['addedemp']]])){ $supplier_addedemp = $emp_name[$sp_emp_code[$row['addedemp']]]; }
                    else{ $supplier_addedemp = ""; }

                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Received Date"><?php echo $received_date; ?></td>
                    <td title="Supplier"><?php echo $vendor_name[$row['vcode']]; ?></td>
                    <td title="Dc. No."><?php echo $row['billno']; ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Item Code"><?php echo $row['icode']; ?></td>
                    <td title="Item"><?php echo $item_name[$row['icode']]; ?></td>
                    <td title="Sent Qty" style="text-align:right;"><?php echo number_format_ind($row['snt_qty']); ?></td>
                    <td title="Received Qty" style="text-align:right;"><?php echo number_format_ind($row['rcd_qty'] + $row['fre_qty']); ?></td>
                    <?php  if($bag_column == 1){ ?>
                        <td title="Received Bags" style="text-align:right;"><?php echo round($bags_qty); ?></td>
                    <?php } ?>
                    <?php
                        if( $row['rcd_qty'] > 0){
                           $result = $row['item_tamt'] / $row['rcd_qty'];
                        }else{
                            $result = 0;
                        }
                    ?>
                    <td title="Rate" style="text-align:right;"><?php echo number_format_ind(round(($result),2)); ?></td>
                    <td title="Amount" style="text-align:right;"><?php echo number_format_ind($row['item_tamt']); ?></td>
                    <td title="Farm/Warehouse" style="text-align:left;"><?php if(!empty($vehicle_name[$row['vehicle_code']])){ echo $vehicle_name[$row['vehicle_code']]; } else{ echo $row['vehicle_code']; } ?></td>
                    <td title="Farm/Warehouse" style="text-align:left;"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Remarks" style="width:180px;white-space: normal;text-align:left;"><?php echo $row['remarks']; ?></td>
                    <td title="Upload Status" style="text-align:left;" rowspan="<?php echo $inv_rowc[$row['trnum']]; ?>"><?php if((int)$file_count > 0){ echo "Uploaded"; } else{ echo "Not Uploaded"; } ?></td>
                    <td title="Remarks" style="text-align:left;"><?php echo $supplier_addedemp; ?></td>
                    <td title="Remarks" style="text-align:left;"><?php echo date("d.m.Y h:i:s A",strtotime($row['addedtime'])); ?></td>
                </tr>
                <?php
                    
                    $tot_bds = $tot_bds + $row['snt_qty'];
                    $tot_rqty = $tot_rqty + $row['rcd_qty'];
                    $tot_qty = $tot_qty + $row['rcd_qty'] + $row['fre_qty'];
                    $tot_amt = $tot_amt + $row['item_tamt'];
                }
                if((float)$tot_rqty != 0){
                    $avg_price = round(($tot_amt / $tot_rqty),2);
                }
                else{
                    $avg_price = 0;
                }
                
                ?>
            </tbody>
            <thead class="thead2">
            <tr >
                <th colspan="7" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_bds)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_qty,2)); ?></th>
                <?php  if($bag_column == 1){ ?>
                    <th style="text-align:right;"><?php echo round($tot_bag_qty); ?></th>
                <?php } ?>
                <th style="text-align:right;"><?php echo number_format_ind(round($avg_price,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"></th>
            </tr>
            </thead>
            <?php if((int)$downloadmfiles_flag == 1 && $file_list != null){
            
            ?>
            <tr>
                <td colspan="23">
                    <div class="row">
                        <div class="form-group">
                            <input type="text" name="download_dt" id="download_dt" value="<?php echo implode("@$&",$file_list); ?>" style="visibility:hidden;" readonly />
                            <button type="button" class="btn btn-sm btn-success" onclick="download_files();">Download All</button>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function download_files(){
                var download_dt = document.getElementById("download_dt").value;
                var download_list = download_dt.split("@$&");

                //alert(download_list.length);

                var string_val = ""; var file_dt = []; var file_id = 0;
                for(var i = 0;i < download_list.length;i++){

                    /*console.log( i + download_list[i]);
                    
                    var temporaryDownloadLink = document.createElement("a");
                    document.body.appendChild( temporaryDownloadLink );
                    temporaryDownloadLink.setAttribute( 'href', download_list[i] );
                    file_dt = []; file_dt = download_list[i].split("/");
                    file_id = 0; file_id = file_dt.length - 1;
                    temporaryDownloadLink.setAttribute( 'download', file_dt[file_id]);
                    temporaryDownloadLink.click();
                    document.body.removeChild( temporaryDownloadLink );*/

                    file_dt = []; file_dt = download_list[i].split("/");
                    file_id = 0; file_id = file_dt.length - 1;

                    downFn(download_list[i],file_dt[file_id]);
                    
                }
            }
            function downFn(url,filename) {
                const pattern = /^(ftp|http|https):\/\/[^ "]+$/;
                if (!pattern.test(url)) {
                   // errMsg.textContent = "Wrong URL Entered";
                    //dBtn.innerText = "Download File";
                    alert("Wrong URL Entered");
                    return;
                }
                //errMsg.textContent = "";
                fetch(url,{mode: 'no-cors'})
                    .then((res) => {
                    if (!res.ok) {
                        throw new Error("Network Problem");
                    }
                    return res.blob();
                    })
                    .then((file) => {
                    const ex = extFn(url);
                    let tUrl = URL.createObjectURL(file);
                    const tmp1 = document.createElement("a");
                    tmp1.href = tUrl;
                    tmp1.download = filename;
                    document.body.appendChild(tmp1);
                    tmp1.click();
                    //dBtn.innerText = "Download File";
                    URL.revokeObjectURL(tUrl);
                    tmp1.remove();
                    })
                    .catch(() => {
                   /* errMsg.textContent = 
                        "Cannot Download Restricted Content!";
                    dBtn.innerText = "Download File";*/
                    console.log("Cannot Download Restricted Content!");
                    });
            }
            function extFn(url) {
            const match = url.match(/\.[0-9a-z]+$/i);
            return match ? match[0].slice(1) : "";
            }
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
                   // slnos();
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
                   // slnos();
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
                    //slnos();
                    asc = !asc;
                    })
                });
                
            }
           /* function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }*/

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        
        <script type="text/javascript">
var tableToExcel = (function() {
    
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
   // if (selectedValue === 'excel') {  
  return function(table, name, filename, chosen, bag_column) {
    if (chosen === 'excel') { 
        
        $('#header_sorting').empty();
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    //window.location.href = uri + base64(format(template, ctx))
    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                var html = '';
                html += '<th id="order_date">Date</th>';
                html += '<th id="order_date">Received Date</th>';
                html += '<th id="order">Supplier</th>';
                html += '<th id="order">Dc. No.</th>';
                html += '<th id="order">Invoice</th>';
                html += '<th id="order">Item Code</th>';
                html += '<th id="order">Item</th>';
                html += '<th id="order_num">Sent Qty</th>';
                html += '<th id="order_num">Received Qty</th>';
                if(bag_column === '1'){ 
                html += '<th id="order_num">Received Bags</th>';
                }
                html += '<th id="order_num">Rate</th>';
                html += '<th id="order_num">Amount</th>';
                html += '<th id="order">Vehicle No</th>';
                html += '<th id="order">Farm/Warehouse</th>';
                html += '<th id="order">Remarks</th>';
                html += '<th id="order">Upload Status</th>';

                $('#header_sorting').append(html);
                table_sort();
                table_sort2();
                table_sort3();
  
    }
  }

//}
})()
</script>
       
       
       <script src="../table_search_filter/Search_Script.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>