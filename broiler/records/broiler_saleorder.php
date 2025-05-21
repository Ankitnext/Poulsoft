<?php
//broiler_item_summary.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

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

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $vendor_mobl[$row['code']] = $row['mobile1']; $vendor_addr[$row['code']] = $row['baddress']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }


$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }


$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; $icat_iac[$row['code']] = $row['iac']; }

$sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cline_code[$row['code']] = $row['code']; $cline_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql); $db_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name']; $vendor_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql); $emp_name = array();
while($row = mysqli_fetch_assoc($query)){ $emp_name[$row['code']] = $row['name']; }

$fdate = $tdate = date("Y-m-d"); $item_cat = $items = $sectors = $sect = $groups = $clines = $vendors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
     $tdate = date("Y-m-d",strtotime($_POST['tdate'])); 
     $item_cat = $_POST['item_cat']; 
     $items = $_POST['items']; 
     $vendors = $_POST['vendors'];
     $sect    = $_POST['sector'];
     $clines = $_POST['cline'];

     if($vendors == "all"){ $vendor_filter = ""; } else{ $vendor_filter = " AND `vcode` = '$vendors'"; }
     if($sect == "all"){ $sect_filter = ""; } else{ $sect_filter = " AND `warehouse` = '$sect'"; }
    
     
    if($items != "all"){
        $item_filter = " AND `item_code` IN ('$items')";
    }
    else if($item_cat == "all") { $item_filter = $item_filter2 = ""; }
    else if($item_cat != "all"){
        $icat_list = $item_filter = "";
        foreach($item_code as $icodes){
            if($item_category[$icodes] == $item_cat){
                if($icat_list == ""){
                    $icat_list = $icodes;
                }
                else{
                    $icat_list = $icat_list."','".$icodes;
                }
            }
        }
        $item_filter = " AND `item_code` IN ('$icat_list')";
        
    }
   
    $cline_fltr = "";
    if($clines != "all"){ $cline_fltr = " AND `cline_code` IN ('$clines')"; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$vendor_filter."".$cline_fltr." ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $bcodes = ""; $cus_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name'];$cus_alist[$row['code']] = $row['code']; }
    $cus_list = implode("','",$cus_alist);
    $customer_filter = " AND `vcode` IN ('$cus_list')";
    
    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_item_cat =$icat_name[$_POST['item_cat']]; if ($export_item_cat == "") { $export_item_cat = "All"; }
    $export_items = $item_name[$_POST['items']]; if ($export_items == "") { $export_items = "All"; }

    $export_vendors = $sector_name[$_POST['vendors']]; if ( $export_vendors == "") {  $export_vendors = "All"; }
    
    
     
     if ($export_fdate == $export_tdate)
     {$filename = "Sales Order_".$export_tdate; }
      else {
     $filename = "Sales Order_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/ItemRerateMaster-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors;
}
else{
    $url = "";
}
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
        <table class="tbl" style="width:auto;"  align="center" style="width:1212px;" >
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Sale Order Report</h5></th>
                    <th colspan="17" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_saleorder.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="26">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date:</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date: </label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer</label>
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
                                    <label>Sector</label>
                                    <select name="sector" id="sector" class="form-control select2" >
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $icats){ if($sector_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($sect == $icats){ echo "selected"; } ?>><?php echo $sector_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                               
                                <div class="m-2 form-group">
                                    <label>Customer Line</label>
                                    <select name="cline" id="cline" class="form-control select2">
                                        <option value="all" <?php if($clines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($cline_code as $bcode){ if($cline_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($clines == $bcode){ echo "selected"; } ?>><?php echo $cline_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Item Summary','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
           <table id="main_body" class="tbl" align="center"  style="width:1300px;">
          
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                   
                    <th colspan="9" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Item Summary Report</h5></th>
                    
                </tr>
            <?php } ?>
            
  
                <tr>
                       
                <th colspan="9">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer: <?php echo $export_vendors; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Category: <?php echo $export_item_cat; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Items: <?php echo $export_items; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label><br/></label>
            
                                </div>
                                
                        </th>
                    
                </tr>

                   
       
               

           
               
            </thead>
            
            <br/>
            <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            
            </div>
            
            <thead class="thead3" align="center" style="width:1212px;">            
       
            
                <tr align="center" id="header_sorting">
                    <th id='order_date'>Date</th>
                    <th id='order'>SO</th>
                    <th id='order'>Customer</th>
                    <th id='order'>Item Name</th>
                    <th id='order_num'>Box/Crate</th>
                    <th id='order_num'>Quantity</th>
                    <th id='order_date'>Delivery Date</th>
                    <th id='order'>Remarks</th>
                    <th id='order'>Username</th>
                </tr>
            </thead>
                <thead class="thead1" align="center" style="width:1212px;  display:none; ">
                <tr align="center" style="width:1212px;  ">
                    <th>Date</th>
                    <th>SO</th>
                    <th>Customer</th>
                    <th >Item Name</th>
                    <th>Box/Crate</th>
                    <th >Quantity</th>
                    <th >Delivery Date</th>
                    <th>Remarks</th>
                    <th>Username</th>
                </tr>
            
               

            </thead>
            
            <?php
            if(isset($_POST['submit_report']) == true){
                
                $total_box_qty = $total_qty = 0;
                $sql = "SELECT * FROM `broiler_sc_saleorder` WHERE  `date` >= '$fdate' and `date` <= '$tdate'".$vendor_filter."".$item_filter."".$sect_filter."".$customer_filter." AND `active` = 1 AND `dflag` = 0 ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $ename = "";
                    if(empty($emp_name[$row['addedemp']]) || $emp_name[$row['addedemp']] == ""){
                        $ename = $emp_name[$db_emp_code[$row['addedemp']]];
                    }
                    else{
                        $ename = $emp_name[$row['addedemp']];
                    }
                ?>
            <tbody class="tbody1">
            <?php

                        echo "<tr>";
                            echo "<td>".date('d.m.Y',strtotime($row['date']))."</td>";
							echo "<td>". $row['trnum']."</td>";
							echo "<td>". $vendor_name[$row['vcode']]."</td>";
							echo "<td >". $item_name[$row['item_code']]."</td>";
                            echo "<td style='text-align:right;'>". number_format_ind($row['box_crate_qty']) ."</td>";
							echo "<td style='text-align:right;'>". number_format_ind($row['rcvd_qty'])."</td>";
                            echo "<td>". date('d.m.Y',strtotime($row['delivery_date']))."</td>";
                            echo "<td>". $row['remarks']."</td>";
                            echo "<td>". $ename."</td>";
                            $total_box_qty = $total_box_qty + $row['box_crate_qty'];
                            $total_qty = $total_qty + $row['rcvd_qty'];
                                        
                        echo "</tr>";
                      }
                }
                ?>
                <tfoot>
                <tr class='thead4'>
                <th colspan="4" style="text-align:center;">Grand Total</th>
				<th style="text-align:right;"><?php echo number_format_ind($total_box_qty); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_qty); ?></th>
                <th colspan="3" style="text-align:center;"></th>

                <tr>
                </tfoot>
           
                
            </tbody>
           
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
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        
    <script src="../table_search_filter/Search_Script.js"></script>
    <script type="text/javascript">
var tableToExcel = (function() {
    
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
   // if (selectedValue === 'excel') {  
  return function(table, name, filename, chosen) {
    if (chosen === 'excel') { 
        //document.getElementById("header_sorting").remove();
        
        //document.getElementById("header_sorting").Value = "";
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
    html += '<th id="order">SO</th>';
    html += '<th id="order">Customer</th>';
    html += '<th id="order">Item Name</th>';
    html += '<th id="order_num">Box/Crate</th>';
    html += '<th id="order_num">Quantity</th>';
    html += '<th id="order_date">Delivery Date</th>';
    html += '<th id="order">Remarks</th>';
    html += '<th id="order">Username</th>';
    $('#header_sorting').append(html);
    table_sort();
            table_sort2();
            table_sort3();
  }

}
})()
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
                    //slnos();
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
                    //slnos();
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
            /*function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }*/

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        </body>
</html>
<?php
include "header_foot.php";
?>