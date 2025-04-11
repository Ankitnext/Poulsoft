<?php
//broiler_pandl_report1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_pandl_report1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_pandl_report1.php?db=$db&userid=".$user_code;
}

$file_name = "Batchwise Stocktransfer Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("item_stocktransfers", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stocktransfers LIKE poulso6_admin_broiler_broilermaster.item_stocktransfers;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_sales", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sales LIKE poulso6_admin_broiler_broilermaster.broiler_sales;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_daily_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_daily_record LIKE poulso6_admin_broiler_broilermaster.broiler_daily_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_medicine_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_medicine_record LIKE poulso6_admin_broiler_broilermaster.broiler_medicine_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_itemreturns", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_itemreturns LIKE poulso6_admin_broiler_broilermaster.broiler_itemreturns;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_inv_intermediate_issued", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_inv_intermediate_issued LIKE poulso6_admin_broiler_broilermaster.broiler_inv_intermediate_issued;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_inv_intermediate_received", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_inv_intermediate_received LIKE poulso6_admin_broiler_broilermaster.broiler_inv_intermediate_received;"; mysqli_query($conn,$sql1); }

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'  ".$branch_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $line_code = $line_name = $line_branch = array();
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $catname[$row['code']] = $row['description']; $catcode[$row['code']] = $row['code']; }
$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; $item_dcode[$row['code']] = $row['category'];$itemdetail[$row['code']] = $row['category']."@".$row['code']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_employee` ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $emp_code = $emp_name = $driver_code = $driver_name = array();
while($row = mysqli_fetch_assoc($query)){
    $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name'];
    if(empty($desig_name[$row['desig_code']]) || $desig_name[$row['desig_code']] == ""){ }
    else{
        $driver_code[$row['code']] = $row['code'];
        $driver_name[$row['code']] = $row['name'];
    }
}

$sql = "SELECT DISTINCT driver_code FROM `item_stocktransfers`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ 
    if($row['driver_code'] == 'select' || $row['driver_code'] == ''){ }
    else{
        if(empty($driver_name[$row['driver_code']])){
            $driver_code[$row['driver_code']] = $row['driver_code'];
            $driver_name[$row['driver_code']] = $row['driver_code'];
        }
    }
}

$fdate = $tdate = date("Y-m-d"); $farms = "all"; $fetch_type = "farm_wise"; /*$batch_type = "Live";*/ $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];

    $i = 0; $sec_codes = array();
		foreach($_POST['farms'] as $scodes){
			$i++;
			$sec_codes[$i] = $scodes;
			if($scodes != "all" || $scodes != ""){
				if($all_sec_codes == ""){
					$all_sec_codes = $scodes;
				}
				else{
					$all_sec_codes = $all_sec_codes."','".$scodes;
				}
			}
		}
		if($all_sec_codes != ""){
			$sector_from_search = " AND `fromwarehouse` IN ('$all_sec_codes')";
			$sector_to_search = " AND `towarehouse` IN ('$all_sec_codes')";
		}
		else{
			$sector_from_search = $sector_to_search = "";
		}
	
	
    //$fetch_type = $_POST['fetch_type'];
    $excel_type = $_POST['export'];
}
else{
    $sec_codes = array();
    $sec_codes[1] = "all"; $sector_from_search = $sector_to_search = "";
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
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <!-- <div class="m-2 form-group">
                                    <label>Fetch Type</label>
                                    <select name="fetch_type" id="fetch_type" class="form-control select2">
                                        <option value="branch_wise" <?php if($fetch_type == "branch_wise"){ echo "selected"; } ?>>Branch</option>
                                        <option value="line_wise" <?php if($fetch_type == "line_wise"){ echo "selected"; } ?>>Line</option>
                                        <option value="supvr_wise" <?php if($fetch_type == "supvr_wise"){ echo "selected"; } ?>>Supervisor</option>
                                        <option value="farm_wise" <?php if($fetch_type == "farm_wise"){ echo "selected"; } ?>>Farm</option>
                                    </select>
                                </div> -->
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
        <table id="main_table" class="tbl" align="center">
            <?php
                $html = ''; $cflag = $i_cnt = 0;
                $html .= '<thead class="thead3" id="head_names">';

                $html .= '<tr style="text-align:center;" align="center">';
                $html .= '<th style="text-align:center;" id="order" colspan="2">Expense</th>'; 
                $html .= '<th style="text-align:center;" id="order" colspan="2">Revenue</th>';
          
                $html .= '</tr>';
                $html .= '<tr style="text-align:center;" align="center">';
                $html .= '<th style="text-align:center;" id="order">Description</th>'; 
                $html .= '<th style="text-align:center;" id="order">Amount</th>';
                $html .= '<th style="text-align:center;" id="order">Description</th>'; 
                $html .= '<th style="text-align:center;" id="order">Amount</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                
                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = "";
                // // if($regions != "all"){ $rgn_fltr = " AND `region_code` = '$regions'"; }
                // if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                // if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                // if($supervisors != "all"){ $sup_fltr = " AND `supervisor_code` = '$supervisors'"; }
                if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }

                $farm_list = ""; $farm_list = implode("','", $farm_code);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }
              
                $farm_list = "";
                 $farm_list = implode("','", $farm_alist);
                //Fetch Live Batch and Farm codes
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')  AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; 
                $query = mysqli_query($conn,$sql); $batch_alist = $farm_alist = $batch_name = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $farm_alist[$row['code']] = $row['farm_code']; $batch_farm[$row['farm_code']] = $row['code']; }

                $batch_list = implode("','",$batch_alist); $farm_list = implode("','",$farm_alist);
                     
                $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_region = $farm_branch = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $farm_region[$row['code']] = $row['region_code']; $farm_branch[$row['code']] = $row['branch_code']; }

                //Broiler Chick code
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%' AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $chick_code = "";
                while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
                 
                //Broiler Bird code
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $bird_code = "";
                while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
                 
                //Broiler Feed Details
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_alist[$row['code']] = $row['code']; }
                $feed_list = implode("','", $feed_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_code = $feed_name = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }
  
                //Broiler MedVac Details
                $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $medvac_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_alist[$row['code']] = $row['code']; }
                $medvac_list = implode("','", $medvac_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$medvac_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $medvac_code = $medvac_name = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }
  
                //Calculations
                //Purchase
               // $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `warehouse` IN ('$farm_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
               // Broiler
                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `warehouse` IN ('$farm_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $item_dcode[$row['icode']];
                    $idate = $row['date'];
                 // echo  $hg =  ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                    if(strtotime($idate) < strtotime($fdate)){
                       $op_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                       $op_amt[$key_code] = ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                    } else if((strtotime($idate) > strtotime($fdate)) && (strtotime($idate) < strtotime($tdate))){
                        $pur_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                         $pur_amt[$key_code] = ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                    } else {
                        $cls_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $cls_amt[$key_code] = ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                    }
                    $i++;
                    if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                    if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                }
                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `warehouse` IN ('$farm_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $item_dcode[$row['icode']];
                     
                    if(strtotime($idate) < strtotime($fdate)){
                        $op_sqty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $op_samt[$key_code] = ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                     
                     } else if((strtotime($idate) > strtotime($fdate)) && (strtotime($idate) < strtotime($tdate))){
                        $sale_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        $sale_amt[$key_code] = ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                         
                     } else {
                         $cls_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                         $cls_samt[$key_code] = ($row['rcd_qty'] + $row['fre_qty']) * $row['rate'];
                         
                     }
                    // $sold_birds[$key_code] = $row['birds'];
                    // $sale_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];

                    $i++;
                    if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                    if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                }
                
               
                // Chicken -------------------------------------------------------------------------------------
                $tcr_amt = $tdr_amt = $trec_amt = $texp_amt = $c = 0; $whcodes = array();
                foreach($_POST['farms'] as $whcode){ if($whcode == "all"){ $c = $w_all = 1; } else{ $whcodes[] = $whcode; } }
                $csize = sizeof($whcodes); $whcode = "";
                for($i = 0;$i <= $csize;$i++){ if($whcodes[$i] == ""){ } else if($whcode == ""){ $whcode = $whcodes[$i]; } else{ $whcode = $whcode."','".$whcodes[$i]; } }
                if($c == 1){ $whname = ""; } else{ $whname = " AND `warehouse` IN ('$whcode')"; }
                $fdate = date("Y-m-d",strtotime($_POST['fromdate'])); $tdate = date("Y-m-d",strtotime($_POST['todate']));
                $pdate = date('Y-m-d', strtotime($fdate.'-1 days'));
                
                
                if($sec_codes[1] != "all"){
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `active` = '1' AND `fromwarehouse` IN ('$farm_list') AND `from_batch` IN ('$batch_list') ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $tout_qty[$itemdetail[$row['code']]] = $tout_qty[$row['code']] + $row['quantity'];
                        $tout_amt[$itemdetail[$row['code']]] = $tout_amt[$row['code']] + ($row['quantity'] * $row['price']);
                    }
                     $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `active` = '1' AND `towarehouse` IN ('$farm_list') AND `to_batch` IN ('$batch_list') ORDER BY `date`,`trnum` ASC";$query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                          $tin_qty[$itemdetail[$row['code']]] = $tin_qty[$row['code']] + $row['quantity'];
                          $tin_amt[$itemdetail[$row['code']]] = $tin_amt[$row['code']] + ($row['quantity'] * $row['price']);
                    }
                }
                
                $sql = "SELECT * FROM `acc_coa` WHERE `type` = 'COA-0003' AND `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ if($coa_code == ""){ $coa_code = $row['code']; $coa[] = $row['code']; $coaname[$row['code']] = $row['description']; } else { $coa_code = $coa_code."','".$row['code']; $coa[] = $row['code']; $coaname[$row['code']] = $row['description']; } }
                
                $seq = "SELECT * FROM `account_vouchers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `fcoa` IN ('$coa_code') AND `active` = '1'";
                $grp = " ORDER BY `fcoa` ASC";
                $sql = $seq."".$whname."".$grp;
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $exp_amt[$row['fcoa']] = $exp_amt[$row['fcoa']] + $row['amount'];
                    $texp_amt = $texp_amt + $row['amount'];
                }
                $seq = "SELECT * FROM `account_vouchers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `tcoa` IN ('$coa_code') AND `active` = '1'";
                $grp = " ORDER BY `tcoa` ASC";
                $sql = $seq."".$whname."".$grp;
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $rec_amt[$row['tcoa']] = $rec_amt[$row['tcoa']] + $row['amount'];
                    $trec_amt = $trec_amt + $row['amount'];
                }
                $ob_cat_amt = $cb_cat_amt = $sal_cat_amt = $tin_cat_amt = $tout_cat_amt = $pur_cat_amt = array();
                foreach($catcode as $cat_code){
                    // foreach($itemdetail as $itm_code){
                        // $spl_code = explode("@",$itm_code);
                        // if($cat_code == $spl_code[0]){
                            $ob_cat_amt[$cat_code] = $ob_cat_amt[$cat_code] + $op_amt[$cat_code] + $op_samt[$cat_code];
                            $cb_cat_amt[$cat_code] = $cb_cat_amt[$cat_code] + $cls_amt[$cat_code] + $cls_samt[$cat_code];
                            $pur_cat_amt[$cat_code] = $pur_cat_amt[$cat_code] + $pur_amt[$cat_code];
                            $sal_cat_amt[$cat_code] = $sal_cat_amt[$cat_code] + $sale_amt[$cat_code];
                            $tin_cat_amt[$cat_code] = $tin_cat_amt[$cat_code] + $tin_amt[$cat_code];
                            $tout_cat_amt[$cat_code] = $tout_cat_amt[$cat_code] + $tout_amt[$cat_code];
                           // $mort_cat_amt[$cat_code] = $mort_cat_amt[$cat_code] + $mort_amt[$spl_code[1]];
                        // } else { }
                    // }
                }
                echo "<tr>";
                echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Opening</td>";
                echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Sales</td>";
                echo "</tr>";
                foreach($catcode as $cat_code){
                    if(number_format_ind($ob_cat_amt[$cat_code]) == "0.00" && number_format_ind($sal_cat_amt[$cat_code]) == "0.00"){
                        $tdr_amt = $tdr_amt + $ob_cat_amt[$cat_code] + $pur_cat_amt[$cat_code];
                    }
                    else{
                        echo "<tr>";
                        //echo "<td></td>";
                        echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                        echo "<td>".number_format_ind($ob_cat_amt[$cat_code])."</td>";
                        //echo "<td></td>";
                        echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                        echo "<td>".number_format_ind($sal_cat_amt[$cat_code])."</td>";
                        echo "</tr>";
                        $tdr_amt = $tdr_amt + $ob_cat_amt[$cat_code] + $pur_cat_amt[$cat_code];
                        $t_ob_amt = $t_ob_amt + $ob_cat_amt[$cat_code];
                        
                        $t_sl_amt = $t_sl_amt + $sal_cat_amt[$cat_code];
                    }
                }
                echo "<tr style='background-color:#77c4e2;' class='text-primary;'>";
                echo "<td style='text-align:center;font-weight:bold;'>Total Opening Balance Amount</td>";
                echo "<td>".number_format_ind($t_ob_amt)."</td>";
                echo "<td style='text-align:center;font-weight:bold;'>Total Sales Amount</td>";
                echo "<td>".number_format_ind($t_sl_amt)."</td>";
                echo "</tr>";
                if($sec_codes[1] != "all"){
                    echo "<tr>";
                    echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Transfer In</td>";
                    echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Transfer Out</td>";
                    echo "</tr>";
                    foreach($catcode as $cat_code){
                        //echo "<br/>".number_format_ind($tout_cat_amt[$cat_code]);
                        if(number_format_ind($tout_cat_amt[$cat_code]) == "0.00" && number_format_ind($tin_cat_amt[$cat_code]) == "0.00"){
                            
                        }
                        else{
                            echo "<tr>";
                            //echo "<td></td>";
                            echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                            
                            echo "<td>".number_format_ind($tin_cat_amt[$cat_code])."</td>";
                            //echo "<td></td>";
                            echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                            echo "<td>".number_format_ind($tout_cat_amt[$cat_code])."</td>";
                            echo "</tr>";
                            $t_tin_amt = $t_tin_amt + $tin_cat_amt[$cat_code];
                            $t_tout_amt = $t_tout_amt + $tout_cat_amt[$cat_code];
                            
                            $tcr_amt = $tcr_amt + $tout_cat_amt[$cat_code];
                            $tdr_amt = $tdr_amt + $tin_cat_amt[$cat_code];
                        }
                    }
                    echo "<tr style='background-color: #77c4e2;' class='text-primary;'>";
                    echo "<td style='text-align:center;font-weight:bold;'>Total Transfer In Amount</td>";
                    echo "<td>".number_format_ind($t_tin_amt)."</td>";
                    echo "<td style='text-align:center;font-weight:bold;'>Total Transfer Out Amount</td>";
                    echo "<td>".number_format_ind($t_tout_amt)."</td>";
                    echo "</tr>";
                }
                echo "<tr>";
                echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Purchases</td>";
                echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Mortality</td>";
                echo "</tr>";
                foreach($catcode as $cat_code){
                    //if($pur_cat_amt[$cat_code] == "" && $mort_cat_amt[$cat_code] == ""){
                    if($pur_cat_amt[$cat_code] == ""){
                        //$tcr_amt = $tcr_amt + $sal_cat_amt[$cat_code] + $mort_cat_amt[$cat_code];
                    }
                    else{
                        echo "<tr>";
                        //echo "<td></td>";
                        echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                        echo "<td>".number_format_ind($pur_cat_amt[$cat_code])."</td>";
                        //echo "<td></td>";
                        echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                        //echo "<td>".number_format_ind($mort_cat_amt[$cat_code])."</td>";
                        echo "<td>".number_format_ind(0)."</td>";
                        echo "</tr>";
                       // $tdr_amt = $tdr_amt + $mort_cat_amt[$cat_code];
                        $t_pur_amt = $t_pur_amt + $pur_cat_amt[$cat_code];
                       // $t_mt_amt = $t_mt_amt + $mort_cat_amt[$cat_code];
                    }
                }
                echo "<tr style='background-color: #77c4e2;' class='text-primary;'>";
                echo "<td style='text-align:center;font-weight:bold;'>Total Purchase Amount</td>";
                echo "<td>".number_format_ind($t_pur_amt)."</td>";
                echo "<td style='text-align:center;font-weight:bold;'>Total Mortality Amount</td>";
               // echo "<td>".number_format_ind($t_mt_amt)."</td>";
                echo "<td>".number_format_ind(0)."</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'></td>";
                echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Closing</td>";
                echo "</tr>";
                foreach($catcode as $cat_code){
                    if($cb_cat_amt[$cat_code] == ""){
                        $tcr_amt = $tcr_amt + $sal_cat_amt[$cat_code] + $cb_cat_amt[$cat_code];
                    }
                    else{
                        echo "<tr>";
                        //echo "<td></td>";
                        echo "<td style='text-align:left;'></td>";
                        echo "<td></td>";
                        //echo "<td></td>";
                        echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
                        echo "<td>".number_format_ind($cb_cat_amt[$cat_code])."</td>";
                        echo "</tr>";
                        $tcr_amt = $tcr_amt + $sal_cat_amt[$cat_code] + $cb_cat_amt[$cat_code];
                        //$t_pur_amt = $t_pur_amt + $pur_cat_amt[$cat_code];
                        $t_cl_amt = $t_cl_amt + $cb_cat_amt[$cat_code];
                    }
                }
                echo "<tr style='background-color:#77c4e2;' class='text-primary;'>";
                echo "<td style='text-align:center;font-weight:bold;'></td>";
                echo "<td></td>";
                echo "<td style='text-align:center;font-weight:bold;'>Total Closing Amount</td>";
                echo "<td>".number_format_ind($t_cl_amt)."</td>";
                echo "</tr>";
                
                if($tcr_amt > $tdr_amt){
                    echo "<tr style='background-color:#77c4e2;' class='text-primary;'>";
                        //echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        //echo "<td></td>";
                        echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Gross Profit</td>";
                        echo "<td>".number_format_ind($tcr_amt - $tdr_amt)."</td>";
                    echo "</tr>";
                    $gp_amt = $tcr_amt - $tdr_amt;
                    $gl_amt = 0;
                }
                else{
                    echo "<tr style='background-color: #77c4e2;' class='text-primary;'>";
                        //echo "<td></td>";
                        echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Gross Loss</td>";
                        echo "<td>".number_format_ind($tdr_amt - $tcr_amt)."</td>";
                        //echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                    $gp_amt = 0;
                    $gl_amt = $tdr_amt - $tcr_amt;
                }
                echo "<tr>";
                //echo "<td></td>";
                echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Expenses</td>";
                echo "<td></td>";
                //echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
                echo "<tr style='background-color:#77c4e2;' class='text-primary;'>";
                echo "<td colspan='1' style='text-align:center;font-size:15px;font-weight:bold;'>Total</td>";
                echo "<td>".number_format_ind($trec_amt)."</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
                foreach($coa as $coa_codes){
                    if($rec_amt[$coa_codes] == "" || $rec_amt[$coa_codes] == "0.00" || $rec_amt[$coa_codes] == ".00"){
                        
                    }
                    else{
                        echo "<tr>";
                            //echo "<td></td>";
                            echo "<td style='text-align:left;'>".$coaname[$coa_codes]."</td>";
                            echo "<td>".number_format_ind($rec_amt[$coa_codes])."</td>";
                            //echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                        echo "</tr>";
                    }
                }
                /*$gp_amt = 0;
                    $gl_amt = $tdr_amt - $tcr_amt;
                    if($gp_amt == 0 && $gl_amt != 0){}
                    else if($gp_amt != 0 && $gl_amt == 0){}*/
                if($tcr_amt > $tdr_amt){
                    echo "<tr style='background-color: #77c4e2;' class='text-primary;'>";
                        if((($tcr_amt - $tdr_amt) - $trec_amt) > 0){
                            echo "<td style='text-align:center;font-size:15px;font-weight:bold;' >Net Profit</td>";
                            echo "<td style='font-weight:bold;'>".number_format_ind(($tcr_amt - $tdr_amt) - $trec_amt)."</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            $net_pf_amt = ($tcr_amt - $tdr_amt) - $trec_amt;
                            $net_ls_amt = 0;
                        }
                        else{
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td style='text-align:center;font-size:15px;font-weight:bold;' >Net Loss</td>";
                            echo "<td style='font-weight:bold;'>".number_format_ind(($tdr_amt - $tcr_amt) + $trec_amt)."</td>";
                            $net_ls_amt = ($tdr_amt - $tcr_amt) + $trec_amt;
                            $net_pf_amt = 0;
                        }
                        
                    echo "</tr>";
                }
                else{
                    echo "<tr style='background-color: #77c4e2;' class='text-primary;'>";
                        if((($tcr_amt - $tdr_amt) - $trec_amt) > 0){
                            echo "<td style='text-align:center;font-size:15px;font-weight:bold;' >Net Profit</td>";
                            echo "<td style='font-weight:bold;'>".number_format_ind(($tcr_amt - $tdr_amt) - $trec_amt)."</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            $net_pf_amt = ($tcr_amt - $tdr_amt) - $trec_amt;
                            $net_ls_amt = 0;
                        }
                        else{
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td style='text-align:center;font-size:15px;font-weight:bold;' >Net Loss</td>";
                            echo "<td style='font-weight:bold;'>".number_format_ind(($tdr_amt - $tcr_amt) + $trec_amt)."</td>";
                            $net_ls_amt = ($tdr_amt - $tcr_amt) + $trec_amt;
                            $net_pf_amt = 0;
                        }
                        
                    echo "</tr>";
                }
                echo "<tr style='background-color: #77c4e2;' class='text-primary;'>";
                    echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Total</td>";
                    echo "<td style='font-weight:bold;'>".number_format_ind($trec_amt + $t_pur_amt + $t_tin_amt + $t_ob_amt + $net_pf_amt)."</td>";
                    echo "<td></td>";
                    echo "<td style='font-weight:bold;'>".number_format_ind($t_sl_amt + $t_tout_amt + $t_cl_amt + $net_ls_amt - $t_mt_amt)."</td>";
                echo "</tr>";
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_line[$fcode])){ $l_code = $farm_line[$fcode]; } else{ $l_code = ""; }
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
            }
        
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
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
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
                    html += '<?php echo $nhead_html; ?>';
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
                    html += '<?php echo $fhead_html; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
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