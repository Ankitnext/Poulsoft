<?php
//chicken_pursale_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];

if($db == ''){
    $users_code = $_SESSION['userid'];
    include "../newConfig.php";
    $dbname = $_SESSION['dbase'];
    $form_path = "chicken_pursale_masterreport.php";
}
else{
    $users_code = $_GET['emp_code'];
    include "APIconfig.php";
    $dbname = $db;
    $form_path = "chicken_pursale_masterreport.php?db=$db&emp_code=".$users_code;
}

$file_name = "Purchase-Sale";

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$users_code'";
$query = mysqli_query($conn,$sql); $loc_access = $cgroup_access = "";
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; $cgroup_access = $row['cgroup_access']; }

//Usr access Based Sector Filter
$lacs = array(); $loc_aflag = 0; $loc_list = $user_sector_filter = ""; $lacs = explode(",", $loc_access);
foreach($lacs as $la){ if($la == "all" || $la == "All"){ $loc_aflag = 1; } if($loc_list == ""){ $loc_list = $la; } else{ $loc_list = $loc_list."','".$la; } }
if($loc_aflag == 0){ $user_sector_filter = " AND `code` IN ('$loc_list')"; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Usr access Based Customer Group Filter
$cgacs = array(); $cus_gaflag = 0; $cus_glist = $user_cusgrp_filter = ""; $cgacs = explode(",", $cgroup_access);
foreach($cgacs as $cg){ if($cg == "all" || $cg == "All"){ $cus_gaflag = 1; } if($cus_glist == ""){ $cus_glist = $cg; } else{ $cus_glist = $cus_glist."','".$cg; } }
if($cus_gaflag == 0){ $user_cusgrp_filter = " AND `code` IN ('$cus_glist')"; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'".$user_cusgrp_filter." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cgrp_code = $cgrp_name = array();
while($row = mysqli_fetch_assoc($query)){ $cgrp_code[$row['code']] = $row['code']; $cgrp_name[$row['code']] = $row['description']; }

$grp_list = implode("','",$cgrp_code);
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `groupcode` IN ('$grp_list') ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_group = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $sup_code = $sup_name = $sup_group = array();
while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; $sup_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'";
$query = mysqli_query($conns,$sql); $user_code = $user_name = array();
while($row = mysqli_fetch_assoc($query)){ $user_code[$row['empcode']] = $row['empcode']; $user_name[$row['empcode']] = $row['username']; }

$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
$query = mysqli_query($conn,$sql); $ifwt = $ifbw = $ifjbw = $ifjbwen = $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; }
if((float)$ifjbwen == 1 || (float)$ifjbw == 1){ $jals_flag = 1; }
if((float)$ifjbwen == 1 || (float)$ifjbw == 1 || (float)$ifbw == 1){ $birds_flag = 1; }
if((float)$ifjbwen == 1){ $tweight_flag = $eweight_flag = 1; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Purchase-Sale' AND `field_function` = 'Display Expense Add Column' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dexp_flag = mysqli_num_rows($query);

$fdate = $tdate = date("Y-m-d"); $customers = $suppliers = $item_cat = $items = $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $customers = $_POST['customers'];
    $suppliers = $_POST['suppliers'];
    $item_cat = $_POST['item_cat'];
    $items = $_POST['items'];
    $sectors = $_POST['sectors'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <?php include "header_head2.php"; ?>
        <style>
            body .main-table{
                font-size: 12px;
            }
        </style>
    </head>
    <body align="center">
		<table align="center">
			<tr>
            <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
				<td><?php echo $cdetails; ?></td>
				<td align="center">
                    <h5><?php echo $file_name; ?></h5>
					<label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
					<label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
				</td>
			</tr>
		</table>
		<section class="content" align="center">
			<div class="col-md-12" align="center"></div>
                <table class="main-table table-sm table-hover" align="center">
                    <form action="<?php echo $form_path; ?>" method="post">
                        <thead class="thead1" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                            <tr>
                                <th colspan="21">
                                    <div class="m-1 p-1 row">
                                        <div class="m-1 form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="m-1 form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="m-1 form-group" style="width:170px;">
                                            <label>Customer</label>
                                            <select name="customers" id="customers" class="form-control select2" style="width:160px;" onchange="fetch_item_list();">
                                                <option value="all" <?php if($customers == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($cus_code as $ccode){ if($cus_name[$ccode] != ""){ ?>
                                                <option value="<?php echo $ccode; ?>" <?php if($customers == $ccode){ echo "selected"; } ?>><?php echo $cus_name[$ccode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="m-1 form-group" style="width:170px;">
                                            <label>Supplier</label>
                                            <select name="suppliers" id="suppliers" class="form-control select2" style="width:160px;" onchange="fetch_item_list();">
                                                <option value="all" <?php if($suppliers == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($sup_code as $scode){ if($sup_name[$scode] != ""){ ?>
                                                <option value="<?php echo $scode; ?>" <?php if($suppliers == $scode){ echo "selected"; } ?>><?php echo $sup_name[$scode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="m-1 form-group" style="width:170px;">
                                            <label>Category</label>
                                            <select name="item_cat" id="item_cat" class="form-control select2" style="width:160px;" onchange="fetch_item_list();">
                                                <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                                <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="m-1 form-group" style="width:150px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php
                                                foreach($item_code as $icode){
                                                    if($item_cat == $item_category[$icode] || $item_cat == "all"){
                                                    ?>
                                                    <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="m-1 form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>All</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="m-1 form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="export" id="export" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="m-1 p-1 row">
                                        <div class="m-1 form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="m-1 form-group">
                                            <br/>
                                            <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </form>
                </table>
                <table class="main-table table-sm table-hover" id="main_table" align="center">
                    <?php
                    if(isset($_POST['submit_report']) == true){
                        $cus_fltr = $sup_fltr = $item_fltr = $sector_fltr = $cinv_fltr = $sinv_fltr = "";
                        if($sectors != "all"){ $sector_fltr = " AND `warehouse` = '$sectors'"; }
                        
                        if($items != "all"){ $item_fltr = " AND `itemcode` IN ('$items')"; }
                        else if($item_cat != "all"){
                            $icat_list = $item_fltr = "";
                            foreach($item_code as $icode){
                                $item_category[$icode];
                                if(!empty($item_category[$icode]) && $item_category[$icode] == $item_cat){
                                    if($icat_list == ""){ $icat_list = $icode; } else{ $icat_list = $icat_list."','".$icode; }
                                }
                            }
                            $item_fltr = " AND `itemcode` IN ('$icat_list')";
                        }
                        if($customers != "all"){
                            $cus_fltr = " AND `customercode` = '$customers'";

                            $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cus_fltr."".$item_fltr."".$sector_fltr." AND `link_trnum` != '' AND  `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $sup_alinks = array();
                            while($row = mysqli_fetch_assoc($query)){ $sup_alinks[$row['link_trnum']] = $row['link_trnum']; }
                            $sinv_list = implode("','",$sup_alinks);
                            $sinv_fltr = " AND `invoice` IN ('$sinv_list')";                            
                        }
                        if($suppliers != "all"){
                            $sup_fltr = " AND `vendorcode` = '$suppliers'";

                            $sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sup_fltr."".$item_fltr."".$sector_fltr." AND `link_trnum` != '' AND  `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $cus_alinks = array();
                            while($row = mysqli_fetch_assoc($query)){ $cus_alinks[$row['link_trnum']] = $row['link_trnum']; }
                            $cinv_list = implode("','",$cus_alinks);
                            $cinv_fltr = " AND `invoice` IN ('$cinv_list')";
                        }

                        $act_date = $act_tnum = $act_item = $act_key = $cus_sdate = $cus_stnum = $cus_sbill = $cus_cname = $cus_citem = $cus_cjals = $cus_birds = $cus_tweight = $cus_eweight = 
                        $cus_nweight = $cus_cprice = $cus_amount = $cus_tcsamt = $cus_fnlamt = $cus_sector = $cus_vehicle = $cus_driver = $cus_addemp = $cus_addtime = $sup_sdate = 
                        $sup_stnum = $sup_sbill = $sup_cname = $sup_citem = $sup_cjals = $sup_birds = $sup_tweight = $sup_eweight = $sup_nweight = $sup_cprice = $sup_amount = $sup_tcsamt = 
                        $sup_fnlamt = $sup_sector = $sup_vehicle = $sup_driver = $sup_remarks = $sup_addemp = $sup_addtime = array();

                        $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cinv_fltr."".$cus_fltr."".$item_fltr."".$sector_fltr." AND `link_trnum` != '' AND  `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['date']."@".$row['link_trnum']."@".$row['itemcode'];
                            $act_date[$row['date']] = $row['date'];
                            $act_tnum[$row['link_trnum']] = $row['link_trnum'];
                            $act_item[$row['itemcode']] = $row['itemcode'];
                            $act_key[$key] = $key;

                            $cus_sdate[$key] = $row['date'];
                            $cus_stnum[$key] = $row['invoice'];
                            $cus_sbill[$key] = $row['bookinvoice'];
                            $cus_cname[$key] = $row['customercode'];
                            $cus_citem[$key] = $row['itemcode'];
                            $cus_cjals[$key] = $row['jals'];
                            $cus_birds[$key] = $row['birds'];
                            $cus_tweight[$key] = $row['totalweight'];
                            $cus_eweight[$key] = $row['emptyweight'];
                            $cus_nweight[$key] = $row['netweight'];
                            $cus_cprice[$key] = $row['itemprice'];
                            $cus_amount[$key] = $row['totalamt'];
                            $cus_tcsamt[$key] = $row['tcdsamt'];
                            $cus_fnlamt[$key] = $row['finaltotal'];
                            $cus_sector[$key] = $row['warehouse'];
                            $cus_vehicle[$key] = $row['vehiclecode'];
                            $cus_driver[$key] = $row['drivercode'];
                            $cus_addemp[$key] = $row['addedemp'];
                            $cus_addtime[$key] = $row['addedtime'];
                            $expense_amt1[$key] = round($row['expense_amt1'],5);
                        }

                        $sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sinv_fltr."".$sup_fltr."".$item_fltr."".$sector_fltr." AND `link_trnum` != '' AND  `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['date']."@".$row['invoice']."@".$row['itemcode'];
                            $act_date[$row['date']] = $row['date'];
                            $act_tnum[$row['invoice']] = $row['invoice'];
                            $act_item[$row['itemcode']] = $row['itemcode'];
                            $act_key[$key] = $key;

                            $sup_sdate[$key] = $row['date'];
                            $sup_stnum[$key] = $row['invoice'];
                            $sup_sbill[$key] = $row['bookinvoice'];
                            $sup_cname[$key] = $row['vendorcode'];
                            $sup_citem[$key] = $row['itemcode'];
                            $sup_cjals[$key] = $row['jals'];
                            $sup_birds[$key] = $row['birds'];
                            $sup_tweight[$key] = $row['totalweight'];
                            $sup_eweight[$key] = $row['emptyweight'];
                            $sup_nweight[$key] = $row['netweight'];
                            $sup_cprice[$key] = $row['itemprice'];
                            $sup_amount[$key] = $row['totalamt'];
                            $sup_tcsamt[$key] = $row['tcdsamt'];
                            $sup_fnlamt[$key] = $row['finaltotal'];
                            $sup_sector[$key] = $row['warehouse'];
                            $sup_vehicle[$key] = $row['vehiclecode'];
                            $sup_driver[$key] = $row['drivercode'];
                            $sup_remarks[$key] = $row['remarks'];
                            $sup_addemp[$key] = $row['addedemp'];
                            $sup_addtime[$key] = $row['addedtime'];
                        }

                        $akey = sizeof($act_key);
                        if($akey > 0){
                            $html = '';$jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
                            $html .= '<thead class="thead2" id="head_names">';
                            $html .= '<tr style="text-align:center;" align="center">';
                            $html .= '<th id="order_num">S.No</th>';
                            $html .= '<th id="order_date">Date</th>';
                            $html .= '<th id="order">Dc. No.</th>';
                            $html .= '<th id="order">Supplier</th>';
                            $html .= '<th id="order">Trnum</th>';
                            $html .= '<th id="order">Item</th>';
                            if((float)$jals_flag == 1){ $html .= '<th id="order_num">Jals</th>'; }
                            if((float)$birds_flag == 1){ $html .= '<th id="order_num">Birds</th>'; }
                            if((float)$tweight_flag == 1){ $html .= '<th id="order_num">T. Weight</th>'; }
                            if((float)$eweight_flag == 1){ $html .= '<th id="order_num">E. Weight</th>'; }
                            $html .= '<th style="text-align:center;" id="order_num">Net Weight</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                            $html .= '<th style="text-align:center;" id="order_num">TCS/TDS Amt</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Final Amt</th>';
                            
                            $html .= '<th id="order">Customer</th>';
                            $html .= '<th id="order">Trnum</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                            $html .= '<th style="text-align:center;" id="order_num">TCS/TDS Amt</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Final Amt</th>';
                            if((int)$dexp_flag == 1){ $html .= '<th style="text-align:center;" id="order_num">Expense Amt</th>'; $html .= '<th style="text-align:center;" id="order_num">Profit / Loss</th>'; }
                            $html .= '<th style="text-align:center;" id="order_num">Warehouse</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Vehicle</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Driver</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Remarks</th>';
                            $html .= '</tr>';
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1" id="tbody1">';
                            
                            $tot_sjals = $tot_sbirds = $tot_stweight = $tot_seweight = $tot_snweight = $tot_samount = $tot_stcsamt = $tot_sfnlamt = $tot_camount = $tot_ctcsamt = $tot_cfnlamt = $tot_expamt = 0;
                            $sno = 1;
                            foreach($act_key as $key){
                                $sdate = date("d.m.Y",strtotime($sup_sdate[$key]));
                                $sdcno = $sup_sbill[$key];
                                $sname = $sup_name[$sup_cname[$key]];
                                $siname = $item_name[$sup_citem[$key]];
                                if(empty($sup_cjals[$key]) || $sup_cjals[$key] == ""){ $sup_cjals[$key] = 0; }
                                if(empty($sup_birds[$key]) || $sup_birds[$key] == ""){ $sup_birds[$key] = 0; }
                                if(empty($sup_tweight[$key]) || $sup_tweight[$key] == ""){ $sup_tweight[$key] = 0; }
                                if(empty($sup_eweight[$key]) || $sup_eweight[$key] == ""){ $sup_eweight[$key] = 0; }
                                if(empty($sup_nweight[$key]) || $sup_nweight[$key] == ""){ $sup_nweight[$key] = 0; }
                                if(empty($sup_cprice[$key]) || $sup_cprice[$key] == ""){ $sup_cprice[$key] = 0; }
                                if(empty($sup_amount[$key]) || $sup_amount[$key] == ""){ $sup_amount[$key] = 0; }
                                if(empty($sup_tcsamt[$key]) || $sup_tcsamt[$key] == ""){ $sup_tcsamt[$key] = 0; }
                                if(empty($sup_fnlamt[$key]) || $sup_fnlamt[$key] == ""){ $sup_fnlamt[$key] = 0; }

                                $cdate = date("d.m.Y",strtotime($cus_sdate[$key]));
                                $cdcno = $cus_sbill[$key];
                                $cname = $cus_name[$cus_cname[$key]];
                                $ciname = $item_name[$cus_citem[$key]];
                                if(empty($cus_cjals[$key]) || $cus_cjals[$key] == ""){ $cus_cjals[$key] = 0; }
                                if(empty($cus_birds[$key]) || $cus_birds[$key] == ""){ $cus_birds[$key] = 0; }
                                if(empty($cus_tweight[$key]) || $cus_tweight[$key] == ""){ $cus_tweight[$key] = 0; }
                                if(empty($cus_eweight[$key]) || $cus_eweight[$key] == ""){ $cus_eweight[$key] = 0; }
                                if(empty($cus_nweight[$key]) || $cus_nweight[$key] == ""){ $cus_nweight[$key] = 0; }
                                if(empty($cus_cprice[$key]) || $cus_cprice[$key] == ""){ $cus_cprice[$key] = 0; }
                                if(empty($cus_amount[$key]) || $cus_amount[$key] == ""){ $cus_amount[$key] = 0; }
                                if(empty($cus_tcsamt[$key]) || $cus_tcsamt[$key] == ""){ $cus_tcsamt[$key] = 0; }
                                if(empty($cus_fnlamt[$key]) || $cus_fnlamt[$key] == ""){ $cus_fnlamt[$key] = 0; }
                                if(empty($expense_amt1[$key]) || $expense_amt1[$key] == ""){ $expense_amt1[$key] = 0; }

                                $wname = $sector_name[$sup_sector[$key]];
                                $strnum = $sup_stnum[$key];
                                $vname = $sup_vehicle[$key];
                                $dname = $sup_driver[$key];
                                $remarks = $sup_remarks[$key];
                                $ctrnum = $cus_stnum[$key];

                                

                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$sno++.'</td>';
                                $html .= '<td style="text-align:left;">'.$sdate.'</td>';
                                $html .= '<td style="text-align:left;">'.$sdcno.'</td>';
                                $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                $html .= '<td style="text-align:left;">'.$strnum.'</td>';
                                $html .= '<td style="text-align:left;">'.$siname.'</td>';
                                if((float)$jals_flag == 1){ $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($sup_cjals[$key])).'</td>'; }
                                if((float)$birds_flag == 1){ $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($sup_birds[$key])).'</td>'; }
                                if((float)$tweight_flag == 1){ $html .= '<td style="text-align:right;">'.number_format_ind($sup_tweight[$key]).'</td>'; }
                                if((float)$eweight_flag == 1){ $html .= '<td style="text-align:right;">'.number_format_ind($sup_eweight[$key]).'</td>'; }
                                $html .= '<td style="text-align:right;">'.number_format_ind($sup_nweight[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($sup_cprice[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($sup_amount[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($sup_tcsamt[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($sup_fnlamt[$key]).'</td>';

                                $html .= '<td style="text-align:left;">'.$cname.'</td>';
                                $html .= '<td style="text-align:left;">'.$ctrnum.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($cus_cprice[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($cus_amount[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($cus_tcsamt[$key]).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($cus_fnlamt[$key]).'</td>';
                                $pl_amt = 0;
                                if((int)$dexp_flag == 1){
                                    $pl_amt = (float)$cus_fnlamt[$key] - (float)$sup_fnlamt[$key] - (float)$expense_amt1[$key];
                                    $html .= '<td style="text-align:right;">'.number_format_ind($expense_amt1[$key]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($pl_amt).'</td>';
                                }
                                $html .= '<td style="text-align:left;">'.$wname.'</td>';
                                $html .= '<td style="text-align:left;">'.$vname.'</td>';
                                $html .= '<td style="text-align:left;">'.$dname.'</td>';
                                $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                                $html .= '</tr>';

                                $tot_sjals += $sup_cjals[$key];
                                $tot_sbirds += $sup_birds[$key];
                                $tot_stweight += $sup_tweight[$key];
                                $tot_seweight += $sup_eweight[$key];
                                $tot_snweight += $sup_nweight[$key];
                                $tot_samount += $sup_amount[$key];
                                $tot_stcsamt += $sup_tcsamt[$key];
                                $tot_sfnlamt += $sup_fnlamt[$key];
                                $tot_camount += $cus_amount[$key];
                                $tot_ctcsamt += $cus_tcsamt[$key];
                                $tot_cfnlamt += $cus_fnlamt[$key];
                                $tot_expamt += (float)$expense_amt1[$key];
                                $tot_plamt += (float)$pl_amt;
                            }
                            $avg_prc1 = 0; if((float)$tot_snweight != 0){ $avg_prc1 = round(((float)$tot_samount / (float)$tot_snweight),2); }
                            $avg_prc2 = 0; if((float)$tot_snweight != 0){ $avg_prc2 = round(((float)$tot_camount / (float)$tot_snweight),2); }
                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:left;" colspan="6">Total</th>';
                            if((float)$jals_flag == 1){ $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_sjals)).'</th>'; }
                            if((float)$birds_flag == 1){ $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_sbirds)).'</th>'; }
                            if((float)$tweight_flag == 1){ $html .= '<th style="text-align:right;">'.number_format_ind($tot_stweight).'</th>'; }
                            if((float)$eweight_flag == 1){ $html .= '<th style="text-align:right;">'.number_format_ind($tot_seweight).'</th>'; }
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_snweight).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($avg_prc1).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_samount).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_stcsamt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_sfnlamt).'</th>';
                            $html .= '<th style="text-align:left;"></th>';
                            $html .= '<th style="text-align:left;"></th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($avg_prc2).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_camount).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_ctcsamt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_cfnlamt).'</th>';
                            if((int)$dexp_flag == 1){
                                $html .= '<th style="text-align:right;">'.number_format_ind($tot_expamt).'</th>';
                                $html .= '<th style="text-align:right;">'.number_format_ind($tot_plamt).'</th>';
                            }
                            $html .= '<th style="text-align:left;"></th>';
                            $html .= '<th style="text-align:left;"></th>';
                            $html .= '<th style="text-align:left;"></th>';
                            $html .= '<th style="text-align:left;"></th>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';
                            
                            echo $html;
                        }
                    }
                ?>
                </table>
            </div>
        </section>
        <br/><br/><br/>
        <script>
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icode){
                        $icats = $item_category[$icode];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icode){
                            $icats = $item_category[$icode];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
            
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    document.getElementById("head_names").innerHTML = "";
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';
                    var dexp_flag = '<?php echo $dexp_flag; ?>';
                    
                    var html = '';
                    html +='<tr style="text-align:center;" align="center">';
                    html +='<th>Date</th>';
                    html +='<th>Dc. No.</th>';
                    html +='<th>Supplier</th>';
                    html +='<th>Trnum</th>';
                    html +='<th>Item</th>';
                    if(jals_flag == 1){ html +='<th>Jals</th>'; }
                    if(birds_flag == 1){ html +='<th>Birds</th>'; }
                    if(tweight_flag == 1){ html +='<th>T. Weight</th>'; }
                    if(eweight_flag == 1){ html +='<th>E. Weight</th>'; }
                    html +='<th style="text-align:center;">Net Weight</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">TCS/TDS Amt</th>';
                    html +='<th style="text-align:center;">Final Amt</th>';
                            
                    html +='<th>Customer</th>';
                    html +='<th>Trnum</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">TCS/TDS Amt</th>';
                    html +='<th style="text-align:center;">Final Amt</th>';
                    if(parseInt(dexp_flag) == 1){
                        html +='<th style="text-align:center;">Expense Amt</th>';
                        html +='<th style="text-align:center;">Profit / Loss</th>';
                    }
                    html +='<th style="text-align:center;">Warehouse</th>';
                    html +='<th style="text-align:center;">Vehicle</th>';
                    html +='<th style="text-align:center;">Driver</th>';
                    html +='<th style="text-align:center;">Remarks</th>';
                    html +='</tr>';
                    $('#head_names').append(html);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html +='<tr style="text-align:center;" align="center">';
                    html +='<th id="order_date">Date</th>';
                    html +='<th id="order">Dc. No.</th>';
                    html +='<th id="order">Supplier</th>';
                    html +='<th id="order">Trnum</th>';
                    html +='<th id="order">Item</th>';
                    if(jals_flag == 1){ html +='<th id="order_num">Jals</th>'; }
                    if(birds_flag == 1){ html +='<th id="order_num">Birds</th>'; }
                    if(tweight_flag == 1){ html +='<th id="order_num">T. Weight</th>'; }
                    if(eweight_flag == 1){ html +='<th id="order_num">E. Weight</th>'; }
                    html +='<th style="text-align:center;" id="order_num">Net Weight</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">TCS/TDS Amt</th>';
                    html +='<th style="text-align:center;" id="order_num">Final Amt</th>';
                            
                    html +='<th id="order">Customer</th>';
                    html +='<th id="order">Trnum</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">TCS/TDS Amt</th>';
                    html +='<th style="text-align:center;" id="order_num">Final Amt</th>';
                    if(parseInt(dexp_flag) == 1){
                        html +='<th style="text-align:center;" id="order_num">Expense Amt</th>';
                        html +='<th style="text-align:center;" id="order_num">Profit / Loss</th>';
                    }
                    html +='<th style="text-align:center;" id="order_num">Warehouse</th>';
                    html +='<th style="text-align:center;" id="order_num">Vehicle</th>';
                    html +='<th style="text-align:center;" id="order_num">Driver</th>';
                    html +='<th style="text-align:center;" id="order_num">Remarks</th>';
                    html +='</tr>';
                    //$('#head_names').append(html);
                    document.getElementById("head_names").innerHTML = html;
                    
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
        </script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
		<?php if($exports == "displaypage" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
    </body>
</html>