<?php
//chicken_customerdetails_masterreport1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_customerdetails_masterreport1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_customerdetails_masterreport1.php?db=".$db;
}
include "number_format_ind.php";
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("font_style_master", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.font_style_master LIKE poulso6_admin_chickenmaster.font_style_master;"; mysqli_query($conn,$sql1); }
if(in_array("main_contactdetails", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_contactdetails LIKE poulso6_admin_chickenmaster.main_contactdetails;"; mysqli_query($conn,$sql1); }
if(in_array("customer_sales", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.customer_sales LIKE poulso6_admin_chickenmaster.customer_sales;"; mysqli_query($conn,$sql1); }
if(in_array("master_cbr_main_details", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_cbr_main_details LIKE poulso6_admin_chickenmaster.master_cbr_main_details;"; mysqli_query($conn,$sql1); }
if(in_array("master_cbr_header_names", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_cbr_header_names LIKE poulso6_admin_chickenmaster.master_cbr_header_names;"; mysqli_query($conn,$sql1); }

//Check for Column Availability
$sql='SHOW COLUMNS FROM `master_cbr_main_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cus_cdays_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `cus_cdays_flag` INT(100) NOT NULL DEFAULT '0' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("cus_outbal_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `cus_outbal_flag` INT(100) NOT NULL DEFAULT '1' AFTER `cus_cdays_flag`"; mysqli_query($conn,$sql); }
if(in_array("field_calign_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `field_calign_flag` INT(100) NOT NULL DEFAULT '0' AFTER `cus_outbal_flag`"; mysqli_query($conn,$sql); }
if(in_array("logo_path", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `logo_path` VARCHAR(500) NULL DEFAULT NULL AFTER `field_calign_flag`"; mysqli_query($conn,$sql); }
if(in_array("logo_ascom_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_cbr_main_details` ADD `logo_ascom_flag` INT(100) NOT NULL DEFAULT '0' AFTER `logo_path`"; mysqli_query($conn,$sql); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("sman_code", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `sman_code` VARCHAR(300) NULL DEFAULT NULL AFTER `groupcode`"; mysqli_query($conn,$sql); }
if(in_array("supr_code", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `supr_code` VARCHAR(300) NULL DEFAULT NULL AFTER `sman_code`"; mysqli_query($conn,$sql); }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Master Report Format*/
$acname = $icname = array(); $ac_cnt = $cus_cdays_flag = $cus_outbal_flag = $field_calign_flag = $logo_ascom_flag = 0; $slogo_path = "";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `master_cbr_main_details` WHERE `project` LIKE 'CTS' AND `file_url` LIKE '$href' AND `user_code` LIKE '$users_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $count1 = mysqli_num_rows($query);
if($count1 == 0){
    $sql = "SELECT * FROM `master_cbr_main_details` WHERE `project` LIKE 'CTS' AND `file_url` LIKE '$href' AND `user_code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $count2 = mysqli_num_rows($query);
}
if($count1 > 0 || $count2 > 0){
    while($row = mysqli_fetch_assoc($query)){
        $file_code = $row['code'];
        $file_name = $row['file_name'];
        $usr_code = $row['user_code'];
        $ccount = $row['column_count'];
        $field_calign_flag = $row['field_calign_flag'];
        $logo_ascom_flag = $row['logo_ascom_flag']; $slogo_path = $row['logo_path'];
        $view_normal_flag = $row['view_normal_flag'];
        $view_excel_flag = $row['view_excel_flag'];
        $view_print_flag = $row['view_print_flag'];
        $view_pdf_flag = $row['view_pdf_flag'];
        $view_imgdwl_flag = $row['view_imgdwl_flag'];
        $view_pdfdwl_flag = $row['view_pdfdwl_flag'];
        $view_imgwapp_flag = $row['view_imgwapp_flag'];
        $view_pdfwapp_flag = $row['view_pdfwapp_flag'];
        $send_wapp_flag = $row['send_wapp_flag'];

        for($i = 1;$i <= $ccount;$i++){
            $cname = "c".$i; $cval1 = $row[$cname]; $cval2 = explode(":",$cval1);
            if($cval2[0] == "A" && $cval2[1] == "1" && (float)$cval2[2] > 0){
                $acname[$cval1] = $cname; $ac_cnt++;
            }
            else if($cval2[0] == "A" && $cval2[1] == "0" && (float)$cval2[2] > 0){
                $icname[$cval1] = $cname;
            }
            else{ }
        }
    }

    $sql = "SELECT * FROM `master_cbr_header_names` WHERE `link_code` LIKE '$file_code' AND `user_code` LIKE '$usr_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `mst_col_name` ASC";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $tbl_col_name[$row['mst_col_name']] = $row['tbl_col_name'];
        $rpt_col_name[$row['mst_col_name']] = $row['rpt_col_name'];
        $rpt_col_type[$row['mst_col_name']] = $row['col_type'];
        if($row['col_type'] == "order_date" || $row['col_type'] == "order"){
            $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;"';
        }
        else if($row['col_type'] == "order_num"){
            $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;"';
        }
        else{ }
    }
}

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Customer Ledger Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Group Details
$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cgrp_code = $cgrp_name = array();
while($row = mysqli_fetch_assoc($query)){ $cgrp_code[$row['code']] = $row['code']; $cgrp_name[$row['code']] = $row['description']; }

//Supervisor
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Master' AND `field_function` = 'Display Supervisor selection' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $dsprm_flag = 0;
if((int)$d_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dsprm_flag = $row['flag']; } }
else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Customer Master','Display Supervisor selection','all','0');"; mysqli_query($conn,$sql); }
if((int)$dsprm_flag == 1){
    //Supervisor Details
    $sql = "SELECT * FROM `chicken_designation` WHERE `description` LIKE '%supervisor%' AND `dflag`= '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $desig_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $desig_alist[$row['code']] = $row['code']; }

    $desig_list = implode("','", $desig_alist);
    $sql = "SELECT * FROM `chicken_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag`= '0' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $csupr_code = $csupr_name = array();
    while($row = mysqli_fetch_assoc($query)){ $csupr_code[$row['code']] = $row['code']; $csupr_name[$row['code']] = $row['name']; }
}

//Salesman
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Master' AND `field_function` = 'Display Salesman selection' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $dsm_flag = 0;
if((int)$d_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dsm_flag = $row['flag']; } }
else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Customer Master','Display Salesman selection','all','0');"; mysqli_query($conn,$sql); }
if((int)$dsm_flag == 1){
    //Salesman Details
    $sql = "SELECT * FROM `chicken_designation` WHERE `description` LIKE '%sales%' AND `dflag`= '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $desig_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $desig_alist[$row['code']] = $row['code']; }

    $desig_list = implode("','", $desig_alist);
    $sql = "SELECT * FROM `chicken_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag`= '0' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sman_code = $sman_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sman_code[$row['code']] = $row['code']; $sman_name[$row['code']] = $row['name']; }
}

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$cus_type = $cus_status = $supervisors = $salesmans = "all"; $areas = ""; $fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    $cus_type = $_POST['cus_type'];
    $cus_status = $_POST['cus_status'];
    if((int)$dsprm_flag == 1){ $supervisors = $_POST['supervisors']; }
    if((int)$dsm_flag == 1){ $salesmans = $_POST['salesmans']; }
    $areas = $_POST['areas'];
}
?>
<html>
	<head>
        <title><?php echo $file_name; ?></title>
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
                                <?php if((int)$logo_ascom_flag == 1){ ?>
                                <td colspan="4"><img src="<?php echo "../".$slogo_path; ?>" height="150px"/></td>
                                <?php } else{ ?>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <?php } ?>
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
                                        <div class="form-group" style="width:190px;">
                                            <label for="cus_type">Customer Type</label>
                                            <select name="cus_type" id="cus_type" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($cus_type == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($cgrp_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cus_type == $scode){ echo "selected"; } ?>><?php echo $cgrp_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="cus_status">Status</label>
                                            <select name="cus_status" id="cus_status" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($cus_status == "all"){ echo "selected"; } ?>>-All-</option>
                                                <option value="1" <?php if($cus_status == "1"){ echo "selected"; } ?>>-Active-</option>
                                                <option value="0" <?php if($cus_status == "0"){ echo "selected"; } ?>>-In-Active-</option>
											</select>
                                        </div>
                                        <?php if((int)$dsprm_flag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="supervisors">Supervisor</label>
                                            <select name="supervisors" id="supervisors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($csupr_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $csupr_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <?php if((int)$dsm_flag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="salesmans">Salesman</label>
                                            <select name="salesmans" id="salesmans" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($salesmans == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($sman_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($salesmans == $scode){ echo "selected"; } ?>><?php echo $sman_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="width:110px;">
                                            <label for="areas">Area</label>
                                            <input type="text" name="areas" id="areas" class="form-control" value="<?php echo $areas; ?>" style="padding:0;padding-left:2px;width:100px;" />
                                        </div>
                                    </div>
                                    <div class="m-1 p-1 row">
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
                                                <?php if((int)$view_excel_flag == 1){ ?><option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option><?php } ?>
                                                <?php if((int)$view_print_flag == 1){ ?><option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option><?php } ?>
                                                <?php if((int)$view_imgdwl_flag == 1){ ?><option value="img_download" <?php if($exports == "img_download"){ echo "selected"; } ?>>-Image Download-</option><?php } ?>
                                                <?php if((int)$view_pdfdwl_flag == 1){ ?><option value="pdf_download" <?php if($exports == "pdf_download"){ echo "selected"; } ?>>-PDF Download-</option><?php } ?>
                                                <?php if((int)$view_imgwapp_flag == 1){ ?><option value="img_wapp" <?php if($exports == "img_wapp"){ echo "selected"; } ?>>-Image WhatsApp-</option><?php } ?>
                                                <?php if((int)$view_pdfwapp_flag == 1){ ?><option value="pdf_wapp" <?php if($exports == "pdf_wapp"){ echo "selected"; } ?>>-PDF WhatsApp-</option><?php } ?>
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
                            $html = $hhtml = $nhtml = $fhtml = ''; $ifix_cnt = $ino_sval = $acol_cnt = 0;
                            $lpath1 = "../".$logopath; $cd_cnt = $io_cnt = 0;
                            if($ac_cnt > 4){ $cd_cnt = $ac_cnt - 4; $io_cnt = 2; } else if($ac_cnt == 4){ $cd_cnt = 2; $io_cnt = 1; } else{ $cd_cnt = $io_cnt = 1; }

                            
                            $hhtml .= '<tr>';
                            $hhtml .= '<td colspan="'.$io_cnt.'"><img src="'.$lpath1.'" height="150px"/></td>';
                            $hhtml .= '<td colspan="'.$io_cnt.'">'.$cmpy_fname.'</td>';
                            $hhtml .= '<td colspan="'.$cd_cnt.'" align="center">';
                            $hhtml .= '<h3>'.$file_name.'</h3>';
                            $hhtml .= '</td>';
                            $hhtml .= '</tr>';
                            

                            $html .= '<thead class="thead2" id="head_names">';
                            $nhtml .= '<tr>'; $fhtml .= '<tr>';
                            for($i = 1;$i <= $ccount;$i++){
                                $key1 = "A:1:".$i; $key2 = "A:0:".$i;
                                if(empty($acname[$key1]) && $acname[$key1] == "" && empty($icname[$key2]) && $icname[$key2] == ""){ }
                                else{
                                    $cname = $checked = ""; if(!empty($acname[$key1])){ $cname = $acname[$key1]; $checked = "checked"; } else if(!empty($icname[$key2])){ $cname = $icname[$key2]; } else{ }
                                    if($cname != ""){
                                        if($exports == "display" || $exports == "exportpdf") {
                                            echo '<input type="checkbox" class="hide_show" id="'.$cname.'" onclick="update_masterreport_status(this.id);"'.$checked.'><span>'.$rpt_col_name[$cname].'</span>&ensp;';
                                        }
                                    }
                                    if(empty($acname[$key1]) && $acname[$key1] == ""){ }
                                    else{
                                        $nhtml .= '<th>'.$rpt_col_name[$cname].'</th>';
                                        $fhtml .= '<th id="'.$rpt_col_type[$cname].'">'.$rpt_col_name[$cname].'</th>';

                                        //check initial values for total Columns
                                        if($rpt_col_type[$cname] != "order_num" && $ino_sval == 0){ $ifix_cnt++; $ini_val1 = $i; } else{ $ino_sval++; }
                                        $acol_cnt++;
                                    }
                                }
                            }
                            $nhtml .= '</tr>'; $fhtml .= '</tr>';
                            $html .= $fhtml;
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';
                            
                            //Display
                            $supr_fltr = $sman_fltr = $cst_fltr = "";
                            if($supervisors != "all"){ $supr_fltr = " AND `supr_code` LIKE '$supervisors'"; }
                            if($salesmans != "all"){ $sman_fltr = " AND `sman_code` LIKE '$salesmans'"; }
                            if($cus_status != "all"){ $cst_fltr = " AND `active` LIKE '$cus_status'"; }

                            $sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'".$supr_fltr."".$sman_fltr."".$cst_fltr." ORDER BY `name` ASC";
                            $query = mysqli_query($conn, $sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $d_code = $row['code'];
                                $d_name = $row['name'];
                                $d_mobileno = $row['mobileno'];
                                $d_address = $row['address'];
                                $d_contacttype = $row['contacttype'];
                                $d_groupcode = $cgrp_name[$row['groupcode']];
                                $d_gstinno = $row['gstinno'];
                                $d_aadhar_no = $row['aadhar_no'];
                                $d_pan_no = $row['pan_no'];
                                $d_creditdays = $row['creditdays'];
                                $d_creditamt = $row['creditamt'];
                                $d_obdate = date("d.m.Y",strtotime($row['obdate'])); if($d_obdate == "01.01.1997"){ $d_obdate = ""; }
                                $d_obtype = $row['obtype']; if($d_obtype == "select" || $d_obtype == "-select-"){ $d_obtype = ""; }
                                $d_obamt = $row['obamt'];
                                $d_obremarks = $row['obremarks'];
                                $d_supr_code = $csupr_name[$row['supr_code']]; if($d_supr_code == "select" || $d_supr_code == "-select-"){ $d_supr_code = ""; }
                                $d_sman_code = $sman_name[$row['sman_code']]; if($d_sman_code == "select" || $d_sman_code == "-select-"){ $d_sman_code = ""; }
                                $d_bank = $row['bank'];
                                $d_branch = $row['branch'];
                                $d_accno = $row['accno'];
                                $d_ifsc = $row['ifsc'];
                                $d_micr = $row['micr'];

                                $html .= '<tr>';
                                for($j = 1;$j <= $ccount;$j++){
                                    $key1 = "A:1:".$j;
                                    if(empty($acname[$key1]) || $acname[$key1] == ""){ }
                                    else{
                                        $cname = $tcname = ""; $cname = $acname[$key1];
                                        if(empty($tbl_col_name[$cname]) || $tbl_col_name[$cname] == ""){ } else{
                                            $tcname = $tbl_col_name[$cname];
                                            if($tcname == "code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_code.'</td>'; }
                                            else if($tcname == "name"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_name.'</td>'; }
                                            else if($tcname == "mobileno"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_mobileno.'</td>'; }
                                            else if($tcname == "address"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_address.'</td>'; }
                                            else if($tcname == "contacttype"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_contacttype.'</td>'; }
                                            else if($tcname == "groupcode"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_groupcode.'</td>'; }
                                            else if($tcname == "gstinno"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_gstinno.'</td>'; }
                                            else if($tcname == "aadhar_no"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_aadhar_no.'</td>'; }
                                            else if($tcname == "pan_no"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_pan_no.'</td>'; }
                                            else if($tcname == "creditdays"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_creditdays.'</td>'; }
                                            else if($tcname == "creditamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_creditamt.'</td>'; }
                                            else if($tcname == "obdate"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_obdate.'</td>'; }
                                            else if($tcname == "obtype"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_obtype.'</td>'; }
                                            else if($tcname == "obamt"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_obamt.'</td>'; }
                                            else if($tcname == "obremarks"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_obremarks.'</td>'; }
                                            else if($tcname == "supr_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_supr_code.'</td>'; }
                                            else if($tcname == "sman_code"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_sman_code.'</td>'; }
                                            else if($tcname == "bank"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_bank.'</td>'; }
                                            else if($tcname == "branch"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_branch.'</td>'; }
                                            else if($tcname == "accno"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_accno.'</td>'; }
                                            else if($tcname == "ifsc"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_ifsc.'</td>'; }
                                            else if($tcname == "micr"){ $html .= '<td '.$rpt_txt_align[$cname].' title="'.$rpt_col_name[$cname].'">'.$d_micr.'</td>'; }
                                            else{ }
                                        }
                                    }
                                }
                                $html .= '</tr>';
                            }

                            $html .= '</tbody>';

                            $html .= '<thead class="tfoot1">';
                            
                            $html .= '<tr>';
                            $html .= '<th colspan="'.$acol_cnt.'"></th>';
                            $html .= '</tr>';

                            $html .= '</thead>';

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                var vendors = document.getElementById("vendors").value;
                var l = true;
                if(vendors == "select"){
                    alert("Kindly select customer to fetch Ledger");
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }

            function update_masterreport_status(a) {
                var file_url = '<?php echo $href; ?>';
                var user_code = '<?php echo $usr_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url=" + file_url + "&user_code=" + user_code + "&field_name=" + field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
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
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    cdate_format1();
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();

                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
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
