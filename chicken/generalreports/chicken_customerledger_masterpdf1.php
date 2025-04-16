<?php
//chicken_customerledger_masterpdf1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_customerledger_masterpdf1.php";
}
else{
    include "APIconfig.php";
    $_SESSION['dbase'] = $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_customerledger_masterpdf1.php?db=".$db."&emp_code=".$users_code;
}
include "number_format_ind.php";
include "decimal_adjustments.php";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

//Check for Column Availability
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sup_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `sup_code` VARCHAR(300) NULL DEFAULT NULL AFTER `customercode`"; mysqli_query($conn,$sql); }
if(in_array("description", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `description` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Manual Name' AFTER `sup_code`"; mysqli_query($conn,$sql); }
if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("font_style_master", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.font_style_master LIKE poulso6_admin_chickenmaster.font_style_master;"; mysqli_query($conn,$sql1); }
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

/*Master Report Format*/
//$field_calign_flag: All Fields except date and transaction type2 are align to center flag
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
        $cus_cdays_flag = $row['cus_cdays_flag'];
        $cus_outbal_flag = $row['cus_outbal_flag'];
        $field_calign_flag = $row['field_calign_flag'];
        $logo_ascom_flag = $row['logo_ascom_flag']; $slogo_path = $row['logo_path'];
        $view_normal_flag = $row['view_normal_flag'];
        $view_excel_flag = $row['view_excel_flag'];
        $view_print_flag = $row['view_print_flag'];
        $view_pdf_flag = $row['view_pdf_flag'];
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
        if((int)$field_calign_flag == 1){
            if($row['tbl_col_name'] == "date" || $row['tbl_col_name'] == "trns_type2"){
                $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;"';
            }
            else{
                if($row['tbl_col_name'] == "cr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:green;"'; }
                else if($row['tbl_col_name'] == "dr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:blue;"'; }
                else if($row['tbl_col_name'] == "cr_amt" || $row['tbl_col_name'] == "odue_days"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;color:red;"'; }
                else{ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:center;"'; }
            }
        }
        else if($row['col_type'] == "order_date" || $row['col_type'] == "order"){
            $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:left;"';
        }
        else if($row['col_type'] == "order_num"){
            if($row['tbl_col_name'] == "cr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:green;"'; }
            else if($row['tbl_col_name'] == "dr_amt"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:blue;"'; }
            else if($row['tbl_col_name'] == "cr_amt" || $row['tbl_col_name'] == "odue_days"){ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;color:red;"'; }
            else{ $rpt_txt_align[$row['mst_col_name']] = 'style="text-align:right;"'; }
        }
        else{ }
    }
}

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Customer Ledger Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0'".$user_sector_filter." ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_obtype = $cus_obamt = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_mobile[$row['code']] = $row['mobileno']; $cus_obtype[$row['code']] = $row['obtype']; $cus_obamt[$row['code']] = $row['obamt']; $credit_days[$row['code']] = $row['creditdays']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0'".$user_sector_filter." ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $csup_alist = $carea_alist = array();
while($row = mysqli_fetch_assoc($query)){ $csup_alist[$row['supr_code']] = $row['supr_code']; $carea_alist[$row['area_code']] = $row['area_code']; }

//Supervisor Details
$supv_list = implode("','",$csup_alist);
$sql = "SELECT * FROM `chicken_employee` WHERE `code` IN ('$supv_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $supv_code = $supv_name = array();
while($row = mysqli_fetch_assoc($query)){ $supv_code[$row['code']] = $row['code']; $supv_name[$row['code']] = $row['name']; }

//Area Details
$area_list = implode("','",$carea_alist);
$sql = "SELECT * FROM `main_areas` WHERE `code` IN ('$area_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $area_code = $area_name = array();
while($row = mysqli_fetch_assoc($query)){ $area_code[$row['code']] = $row['code']; $area_name[$row['code']] = $row['description']; }

$fdate = $tdate = $today = date("Y-m-d"); $supervisors = $areas = "all"; $today_sale = 0;
if(isset($_POST['submit_report']) == true){
    $supervisors = $_POST['supervisors'];
    $areas = $_POST['areas'];
    if($_POST['today_sale'] == true || $_POST['today_sale'] == "on" || $_POST['today_sale'] == 1){ $today_sale = 1; } else{ $today_sale = 0; }
}
$sup_fltr = ""; if($supervisors != "all"){ $sup_fltr = " AND `supr_code` IN ('$supervisors')"; }
$area_fltr = ""; if($areas != "all"){ $area_fltr = " AND `area_code` IN ('$areas')"; }
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
				<table class="table-sm table-hover main-table2">
                    <thead class="thead1">
                        <tr>
                            <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                            <td colspan="2"><?php echo $cdetails; ?></td>
                            <td colspan="15" align="center">
                                <h3><?php echo $file_name; ?></h3>
                                <label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;
                                <label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                            </td>
                        </tr>
                        <form action="<?php echo $form_reload_page; ?>" method="post">
                            <tr>
                                <th colspan="16">
                                    <div class="m-1 p-1 row">
                                        <div class="m-2 form-group">
                                            <label>Supervisor</label>
                                            <select name="supervisors" id="supervisors" class="form-control select2" onchange="fetch_careas();">
                                                <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($supv_code as $gcode){ if($supv_name[$gcode] != ""){ ?>
                                                <option value="<?php echo $gcode; ?>" <?php if($supervisors == $gcode){ echo "selected"; } ?>><?php echo $supv_name[$gcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="m-2 form-group">
                                            <label>Area</label>
                                            <select name="areas" id="areas" class="form-control select2">
                                                <option value="all" <?php if($areas == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($area_code as $gcode){ if($area_name[$gcode] != ""){ ?>
                                                <option value="<?php echo $gcode; ?>" <?php if($areas == $gcode){ echo "selected"; } ?>><?php echo $area_name[$gcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="m-2 form-group">
                                            <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </form>
                    </thead>
                </table>
				<table class="table-sm table-hover main-table2">
                <?php
                echo '';
                echo '<thead class="thead2" id="head_names">';
                echo '<tr>';
                for($i = 1;$i <= $ccount;$i++){
                    $key1 = "A:1:".$i; $key2 = "A:0:".$i;
                    if(empty($acname[$key1]) && $acname[$key1] == "" && empty($icname[$key2]) && $icname[$key2] == ""){ }
                    else{
                        $cname = $checked = ""; if(!empty($acname[$key1])){ $cname = $acname[$key1]; $checked = "checked"; } else if(!empty($icname[$key2])){ $cname = $icname[$key2]; } else{ }
                        if($cname != ""){
                            echo '<input type="checkbox" class="hide_show" id="'.$cname.'" onclick="update_masterreport_status(this.id);"'.$checked.'><span>'.$rpt_col_name[$cname].'</span>&ensp;';
                        }
                    }
                }
                echo '</tr>';
                echo '</thead>';
                ?>
                </table>
				<table class="table-sm table-hover main-table2">
					<thead class="thead1">
                        <form action="..\printformatlibrary\Examples\chicken_customerledger_masterpdf1.php" method="post" onsubmit="return checkval()">
						<tr>
							<td colspan="19" class="p-1">
                                <div class="m-1 p-1 row">
                                    <div class="form-group" style="width:110px;">
                                        <label for="fdate">From Date</label>
                                        <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" onchange="fetch_todate();" readonly />
                                    </div>
                                    <div class="form-group" style="width:110px;">
                                        <label for="tdate">To Date</label>
                                        <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                    </div>
                                    <div class="ml-2 mr-2 form-group">
                                        <label>Select All</label>
                                        <input type="checkbox" name="checkall" id="checkall" class="form-control" style="text-align:center;" onchange="checkedall()" />
                                    </div>
                                    <div class="ml-2 mr-2 form-group" style="width:auto;">
                                        <label for="inc_sac">S&amp;C</label>
                                        <input type="checkbox" name="inc_sac" id="inc_sac" class="form-control" style="text-align:center;" />
                                    </div>
                                    <div class="ml-2 mr-2 form-group" style="width:210px;">
                                        <label>Type</label>
                                        <select name="send_type" id="send_type" class="form-control select2" style="width:200px;">
                                            <option value="download_pdf" selected >-Download PDF-</option>
                                            <option value="view_normal_print">-View Normal Print-</option>
                                            <option value="view_pdf_print">-View PDF Print-</option>
                                        </select>
                                    </div>
                                    <!--<div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                    </div>-->
                                </div>
							</td>
						</tr>
					</thead>
                    <tbody>
                        <tr>
                            <th>Sl.No.</th>
                            <th>Selection</th>
                            <th>Name</th>
                            <th>Mobile No.</th>
                        </tr>
                        <?php
                        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$sup_fltr."".$area_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
                        $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){
                            $c++;
                            $code = $row['code'];
                            $name = $row['name'];
                            $mobe = $row['mobileno'];

                            echo "<tr>";
                            echo "<td style='width:100px;text-align:center;'>".$c."</td>";
                            echo "<td style='width:100px;text-align:center;'><input type='checkbox' name='ccode[$c]' id='ccode[$c]' value='$code' /></td>";
                            echo "<td style='padding-left:10px;text-align:left;'><input type='text' name='cusname[$c]' id='cusname[$c]' class='form-control' value='$name' style='padding:0; padding-left:1px;width:250px;height:16px;border:none;background:inherit;text-decoration:none;font-size:13px;' readonly /></td>";
                            echo "<td style='padding-left:10px;text-align:left;'><input type='text' name='cmobile[$c]' id='cmobile[$c]' class='form-control' value='$mobe' style='padding:0; padding-left:1px;width:450px;height:16px;border:none;background:inherit;text-decoration:none;font-size:13px;' readonly /></td>";
                            echo "</tr>";
                        }
                        ?>
                        <tr class="thead3">
                            <th colspan="4" style="text-align:center;">
                                <button type="submit" name="send_cuspdf" id="send_cuspdf" class="btn btn-sm btn-success">Send</button>
                            </th>
                        </tr>
                    </tbody>
                </table>
				</form>
			</div>
		</section>
        <script>
            function checkval(){
                var incr = '<?php echo $c; ?>';
                var l = true; var c = d = ""; var a = g = 0; var e = [];
                send_type = document.getElementById("send_type").value;
                for(var b = 1;b <= incr;b++){
                    if(l == true){
                        c = document.getElementById("ccode["+b+"]");
                        if(c.checked == true){
                            a++;
                            if(send_type == "send_pdf"){
                                d = document.getElementById("cmobile["+b+"]").value;
                                if(d.match(",")){
                                    e = d.split(",");
                                    g = 0;
                                    for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                                    if(g == 0){
                                        alert("Please enter appropriate Mobile No to send PDF");
                                        document.getElementById("cusname["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").focus();
                                        l = false;
                                    }
                                }
                                else{
                                    d = document.getElementById("cmobile["+b+"]").value;
                                    if(d.length != 10){
                                        alert("Please enter appropriate Mobile No to send PDF");
                                        document.getElementById("cusname["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").focus();
                                        l = false;
                                    }
                                }
                            }
                        }
                    }
				}
                if(l == true){
                    if(a > 0){
                        l = true;
                    }
                    else{
                        alert("select atlest one customer to send/Display Ledger Report");
                        l = false;
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
			function checkedall(){
                var incr = '<?php echo $c; ?>';
				var a = document.getElementById("checkall");
                send_type = document.getElementById("send_type").value;
                var c = d = ""; var e = []; var g = 0;
				if(a.checked == true){
					for(var b = 1;b <= incr;b++){
					    c = document.getElementById("ccode["+b+"]");
						
                        if(send_type == "send_pdf"){
                            d = document.getElementById("cmobile["+b+"]").value;
                            if(d.match(",")){
                                e = d.split(",");
                                g = 0;
                                for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                                if(g == 0){ }
                                else{
                                    c.checked = true;
                                }
                            }
                            else{
                                d = document.getElementById("cmobile["+b+"]").value;
                                if(d.length != 10){ }
                                else{
                                    c.checked = true;
                                }
                            }
                        }
                        else{
                            c.checked = true;
                        }
					}
				}
				else{
					for(var b = 1;b <= incr;b++){
					    c = document.getElementById("ccode["+b+"]");
						c.checked = false;
					}
				}
			}

            function update_masterreport_status(a) {
                var file_url = '<?php echo $href; ?>';
                var user_code = '<?php echo $usr_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
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
            function fetch_careas(){
                var supervisors = document.getElementById("supervisors").value;
                removeAllOptions(document.getElementById("areas"));
                //if(supervisors == "" || supervisors == "select"){ } else{}
                var fetch_areas = new XMLHttpRequest();
                var method = "GET";
                var url = "chicken_fetch_customer_areas.php?supervisors="+supervisors+"&type=from_emp";
                //window.open(url);
                var asynchronous = true;
                fetch_areas.open(method, url, asynchronous);
                fetch_areas.send();
                fetch_areas.onreadystatechange = function(){
                    if (this.readyState == 4 && this.status == 200) {
                        var area_list = this.responseText;
                        $('#areas').append(area_list);
                    }
                }
            }
            function fetch_todate(){
                var fdate = document.getElementById("fdate").value;
                var fetch_tdate = new XMLHttpRequest();
                var method = "GET";
                var url = "chicken_fetch_dates.php?fdate="+fdate+"&type=days_7";
                //window.open(url);
                var asynchronous = true;
                fetch_tdate.open(method, url, asynchronous);
                fetch_tdate.send();
                fetch_tdate.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var tdate = this.responseText;
                        document.getElementById("tdate").value = tdate;
                    }
                }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
