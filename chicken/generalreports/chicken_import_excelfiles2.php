<?php
	//chicken_import_excelfiles2.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	session_start();
	include "../config.php";
    //include "header_head.php";
    include "number_format_ind.php"; $users_code = $_SESSION['userid'];
			
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '%chicken_import_excelfiles2.php%' AND `activate` = '1'";
    $query = mysqli_query($conn,$sql); $fcount = mysqli_num_rows($query);
    while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `displayaccess` LIKE '%$cid%' AND `active` = '1'";
	$query = mysqli_query($conn,$sql); $acount = mysqli_num_rows($query);

    if($fcount > 0 && $acount > 0){
        $sector_code = $sector_name = $item_code = $item_name = $cus_code = $cus_name = array();
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

        // Logo Flag
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
        if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
?>
<html>
	<head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Chicken Trading Software</title><link rel="icon" href="../images/poulsoftlogo_2.png" type="image/ico" />
		<!-- Tell the browser to be responsive to screen width --> <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- daterange picker --> <link rel="stylesheet" href="../bower_components/bootstrap-daterangepicker/daterangepicker.css">
		<!-- bootstrap datepicker --> <link rel="stylesheet" href="../bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
        <!-- Select2 --> <link rel="stylesheet" href="../bower_components/select2/dist/css/select2.min.css">
        <script src="../loading_page_out.js"></script>
		<link rel="stylesheet" href="../loading_style.css"><div class="page-loader-wrapper"><div class="loader"><div class="preloader"><div class="spinner-layer pl-orange"><div class="circle-clipper left"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div><p>Please wait...</p></div></div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <script src="https://kit.fontawesome.com/ab89810909.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css"href="reportstyle.css">
        <!-- Datepicker --><link href="datepicker/jquery-ui.css" rel="stylesheet">
        <style>
			.thead2 th {
                top: 0;
                position: sticky;
                background-color: #98fb98;
			}
			body{
				font-size: 12px;
				color: black;
			}
			.thead2,.tbody1 {
				font-size: 12px;
				padding: 0;
				color: black;
			}
			.formcontrol{
				font-size: 12px;
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
			}
			.form-control{
                padding: 2px;
				font-size: 12px;
				color: black;
				height:24px;
				border: 0.1vh solid gray;
                text-decoration: none;
			}
            form input:focus {
                border: 0.1vh solid gray;
                outline: none;
            }
			.formcontrol:focus {
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 12px;
				color: black;
				padding: 0;
				text-align: left;
			}
			.reportselectionlabel{
				font-size: 12px;
			}
			.table1 table, .table1 thead, .table1 tbody, .table1 tr, .table1 th, .table1 td{
				border: 0.1vh solid black;
				border-collapse: collapse;
			}
            label{
                font-weight: bold;
            }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini" align="center">
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
                    if($dlogo_flag > 0) { ?>
                        <td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                    <?php }
                    else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $qr_img_path = $row['qr_img_path']; ?>
					<td><img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['imagename']); ?>" height="100px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
				</tr>
                <tr>
                    <td colspan="2" style="text-align:center;"><h3>Import Transactional Files</h3></td>
                </tr>
			</table>
		</header>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<table class="table1" style="width:auto;">
                    <form action="chicken_import_excelfiles2.php" method="post" onsubmit="return checkval()" enctype="multipart/form-data">
						<thead class="thead1" style="background-color: #98fb98;">
							<tr>
								<td colspan="25">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="mr-5 form-group">
                                                <label>Import Type</label><br/>
                                                <select name="transaction_type" id="transaction_type" class="form-control select2" onChange="chicken_update_files()">
                                                    <option value="SalesWithFreightonJals">Sales With Freight on Jals</option>
                                                </select>
                                            </div>
                                            <div class="mr-5 form-group">
                                                <br/>
                                                <a href="ChickenModule-SalesImport2.xlsx" id="download_file" download title="download"><img src="../images/Excel-Icon_1.png" height="40px"/>Download Format&ensp;</a>
                                            </div>
                                            <div class="form-group">
                                                <label id="lbl_headname">Upload Sales-Excel</label><br/>
                                                <input type="file" name="file_uploads" id="file_uploads" class="form-control-file" />
                                            </div>
                                            <div class="form-group">
                                                <br/><button type="submit" class="btn btn-success btn-sm" name="submit" id="submit">Import</button>
                                            </div>
                                        </div>
                                    </div>
									
								</td>
							</tr>
						</thead>
                    </form>
						<?php
						if(isset($_POST['submit']) == true){
                            $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $act_item_cat[$row['code']] = $row['category']; }

                            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE 'Chicken%' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $req_item_cat = $row['code']; }

                        ?>
						<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
                        <?php
                            require_once('Classes/PHPExcel.php');
                            $file_name = $_FILES['file_uploads']['name'];
                            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                            $allowed_ext = ['xls','csv','xlsx'];
                            if(in_array($file_ext, $allowed_ext)){
                                $file_path = $_FILES['file_uploads']['tmp_name'];
                                $read_excel = PHPExcel_IOFactory::createReaderForFile($file_path);
                                $excel_obj = $read_excel->load($file_path);

                                $excel_info = $excel_obj->getSheet('0');
                                
                                $act_rows = $excel_info->getHighestRow();
                                $act_cols = $excel_info->getHighestDataColumn();
                                $col_cno = PHPExcel_Cell::columnIndexFromString($act_cols);

                                $heading_name = array();
                                $html = ''; $row = 1; $incr = 0;
                                $html .= '<tr class="thead2">';
                                for($col = 0;$col < $col_cno;$col++){
                                    $hname = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                                    if($hname == "Date"){ $heading_name[$col] = "date"; $html .= '<th style="text-align:center;">Date</th>'; $incr++; }
                                    else if($hname == "Dc No"){ $heading_name[$col] = "bookinvoice"; $html .= '<th style="text-align:center;">Dc No</th>'; $incr++; }
                                    else if($hname == "Customer"){ $heading_name[$col] = "customercode"; $html .= '<th style="text-align:center;">Customer</th>'; $incr++; }
                                    else if($hname == "Item"){ $heading_name[$col] = "itemcode"; $html .= '<th style="text-align:center;">Item</th>'; $incr++; }
                                    else if($hname == "Jals"){ $heading_name[$col] = "jals"; $html .= '<th style="text-align:center;">Jals</th>'; $incr++; }
                                    else if($hname == "Birds"){ $heading_name[$col] = "birds"; $html .= '<th style="text-align:center;">Birds</th>'; $incr++; }
                                    else if($hname == "Total Weight"){ $heading_name[$col] = "totalweight"; $html .= '<th style="text-align:center;">Total Weight</th>'; $incr++; }
                                    else if($hname == "Empty Weight"){ $heading_name[$col] = "emptyweight"; $html .= '<th style="text-align:center;">Empty Weight</th>'; $incr++; }
                                    else if($hname == "Net Weight"){ $heading_name[$col] = "netweight"; $html .= '<th style="text-align:center;">Net Weight</th>'; $incr++; }
                                    else if($hname == "Rate"){ $heading_name[$col] = "itemprice"; $html .= '<th style="text-align:center;">Rate</th>'; $incr++; }
                                    else if($hname == "Freight Price Per Jals"){ $heading_name[$col] = "freight_price_perjal"; $html .= '<th style="text-align:center;">Freight</th>'; $incr++; }
                                    else if($hname == "Freight Amount"){ $heading_name[$col] = "freight_amount_jal"; $html .= '<th style="text-align:center;">Freight</th>'; $incr++; }
                                    else if($hname == "Warehouse"){ $heading_name[$col] = "warehouse"; $html .= '<th style="text-align:center;">Warehouse</th>'; $incr++; }
                                    else{ }
                                }
                                $html .= '</tr>';
                                for($row = 2;$row <= $act_rows;$row++){
                                    $html .= '<tr>';
                                    for($col = 0;$col < $col_cno;$col++){
                                        $hvalue = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                                        if($heading_name[$col] == "date"){
                                            $html .= '<td><input type="text" name="date[]" id="date['.$row.']" class="form-control datepickers" value="'.$hvalue.'" style="width:80px;" readonly /></td>';
                                        }
                                        else if($heading_name[$col] == "bookinvoice"){
                                            $html .= '<td><input type="text" name="bookinvoice[]" id="bookinvoice['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;" /></td>';
                                        }
                                        else if($heading_name[$col] == "customercode"){
                                            $html .= '<td><select name="customercode[]" id="customercode['.$row.']" class="form-control select2" style="width:160px;" onchange="fetch_customer_prices();">';
                                            $html .= '<option value="select" selected>-select-</option>';
                                            foreach($cus_code as $ccode){
                                                if($cus_name[$ccode] == $hvalue){
                                                    $html .= '<option value="'.$ccode.'" selected>'.$cus_name[$ccode].'</option>';
                                                }
                                                else{
                                                    $html .= '<option value="'.$ccode.'">'.$cus_name[$ccode].'</option>';
                                                }
                                            }
                                            $html .= '</select></td>';
                                        }
                                        else if($heading_name[$col] == "itemcode"){
                                            $html .= '<td><select name="itemcode[]" id="itemcode['.$row.']" class="form-control select2" style="width:160px;">';
                                            $html .= '<option value="select" selected>-select-</option>';
                                            foreach($item_code as $icode){
                                                if($item_name[$icode] == $hvalue){
                                                    $html .= '<option value="'.$icode.'" selected>'.$item_name[$icode].'</option>';
                                                }
                                                else{
                                                    $html .= '<option value="'.$icode.'">'.$item_name[$icode].'</option>';
                                                }
                                            }
                                            $html .= '</select></td>';
                                        }
                                        else if($heading_name[$col] == "jals"){
                                            $html .= '<td><input type="text" name="jals[]" id="jals['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatebirds(this.id);calculate_net_qty(this.id);" onchange="validatebirds(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "birds"){
                                            $html .= '<td><input type="text" name="birds[]" id="birds['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatebirds(this.id);calculate_net_qty(this.id);" onchange="validatebirds(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "totalweight"){
                                            $html .= '<td><input type="text" name="totalweight[]" id="totalweight['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatenum(this.id);calculate_net_qty(this.id);" onchange="validateamount(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "emptyweight"){
                                            $html .= '<td><input type="text" name="emptyweight[]" id="emptyweight['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatenum(this.id);calculate_net_qty(this.id);" onchange="validateamount(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "netweight"){
                                            $html .= '<td><input type="text" name="netweight[]" id="netweight['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatenum(this.id);calculate_net_qty(this.id);" onchange="validateamount(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "itemprice"){
                                            $html .= '<td><input type="text" name="itemprice[]" id="itemprice['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "freight_price_perjal"){
                                            $html .= '<td><input type="text" name="freight_price_perjal[]" id="freight_price_perjal['.$row.']" class="form-control" value="'.$hvalue.'" style="width:80px;text-align:right;" onkeyup="validatenum(this.id);calculate_net_qty(this.id);" onchange="validateamount(this.id);" /></td>';
                                        }
                                        else if($heading_name[$col] == "freight_amount_jal"){
                                            $html .= '<td><input type="text" name="freight_amount_jal[]" id="freight_amount_jal['.$row.']" class="form-control" value="'.$hvalue.'" style="width:100px;text-align:right;" readonly /></td>';
                                        }
                                        else if($heading_name[$col] == "warehouse"){
                                            $html .= '<td><select name="warehouse[]" id="warehouse['.$row.']" class="form-control select2" style="width:160px;" >';
                                            $html .= '<option value="select" selected>-select-</option>';
                                            foreach($sector_code as $scode){
                                                if($sector_name[$scode] == $hvalue){
                                                    $html .= '<option value="'.$scode.'" selected>'.$sector_name[$scode].'</option>';
                                                }
                                                else{
                                                    $html .= '<option value="'.$scode.'">'.$sector_name[$scode].'</option>';
                                                }
                                            }
                                            $html .= '</select></td>';
                                        }
                                        else{ }
                                    }
                                    $html .= '</tr>';
                                }
                                
                                ?>
                                <form action="chicken_save_excelfiles2.php" method="post" onsubmit="return checkval2();">
                                <?php echo $html; ?>
                                <tr style="visibility:hidden;">
                                    <th colspan="<?php echo $incr / 2; ?>" style="text-align:center;">
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:80px;" />
                                    </th>
                                    <th colspan="<?php echo $incr / 2; ?>" style="text-align:center;">
                                        <input type="text" name="tr_type" id="tr_type" class="form-control" value="<?php echo $_POST['transaction_type']; ?>" style="width:80px;" />
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="<?php echo $incr; ?>" id="save_file" style="text-align:center;visibility:visible;">
                                        <button type="submit" class="btn btn-success btn-md" name="save_import_submit" id="save_import_submit">Save</button>
                                    </th>
                                </tr>
                                </form>
                                <?php
                            }
                        ?>
                        </tbody>
					    <?php
						}
						?>
				</table>
			</div>
		</section>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
		<script>
            function checkval(){
                var file_uploads = document.getElementById("file_uploads").value; var l = true;
                if(file_uploads == ""){ alert("Please select file to upload"); l = false; } else{ }
                if(l == true){ return true; } else{ return false; }
            }
            function checkval2(){
                document.getElementById("ebtncount").value = "1"; document.getElementById("save_import_submit").style.visibility = "hidden";
                var l = true; var customercode = itemcode = warehouse = ""; var c = netweight = itemprice = 0;
                var act_rows = '<?php echo $act_rows; ?>';
                for(var d = 2;d <= act_rows;d++){
                    if(l == true){
                        c = d - 1;
                        customercode = document.getElementById("customercode["+d+"]").value;
                        itemcode = document.getElementById("itemcode["+d+"]").value;
                        warehouse = document.getElementById("warehouse["+d+"]").value;
                        netweight = document.getElementById("netweight["+d+"]").value;
                        itemprice = document.getElementById("itemprice["+d+"]").value;
                        if(customercode == "select"){
                            alert("Please select customer in row: "+c);
                            document.getElementById("customercode["+d+"]").focus();
                            l = false;
                        }
                        else if(itemcode == "select"){
                            alert("Please select Item in row: "+c);
                            document.getElementById("itemcode["+d+"]").focus();
                            l = false;
                        }
                        else if(warehouse == "select"){
                            alert("Please select Warehouse in row: "+c);
                            document.getElementById("warehouse["+d+"]").focus();
                            l = false;
                        }
                        else if(netweight == "" || parseFloat(netweight) == 0){
                            alert("Please Enter Net Weight in row: "+c);
                            document.getElementById("netweight["+d+"]").focus();
                            l = false;
                        }
                        else if(itemprice == "" || parseFloat(itemprice) == 0){
                            alert("Please Enter Rate in row: "+c);
                            document.getElementById("itemprice["+d+"]").focus();
                            l = false;
                        }
                        else{ }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("save_import_submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
			function fetch_customer_prices(){
                var jals = freight_amount_jal = 0;
                var incr = '<?php echo $act_rows; ?>';
                for(var d = 2;d <= incr;d++){
                    var pdate = document.getElementById("date["+d+"]").value;
                    var customercode = document.getElementById("customercode["+d+"]").value;
                    var itemcode = document.getElementById("itemcode["+d+"]").value;
                    var msale_prate_flag = 1;
                    if(msale_prate_flag == 1){
                        if(customercode != "select" && itemcode != "select"){
                            var prices = new XMLHttpRequest();
                            var method = "GET";
                            var url = "fetch_customer_prices3.php?pname="+customercode+"&mdate="+pdate+"&iname="+itemcode+"&row_count="+d;
                            //window.open(url);
                            var asynchronous = true;
                            prices.open(method, url, asynchronous);
                            prices.send();
                            prices.onreadystatechange = function(){
                                if(this.readyState == 4 && this.status == 200){
                                    var prc_list = this.responseText;
                                    var prc_details = prc_list.split("@");
                                    if(prc_details[1] == ""){ prc_details[1] = 0; }
                                    document.getElementById("itemprice["+prc_details[2]+"]").value = prc_details[0];
                                    document.getElementById("freight_price_perjal["+prc_details[2]+"]").value = prc_details[1];

                                    jals = document.getElementById("jals["+prc_details[2]+"]").value;
                                    freight_amount_jal = parseFloat(jals) * parseFloat(prc_details[1]);
                                    document.getElementById("freight_amount_jal["+prc_details[2]+"]").value = parseFloat(freight_amount_jal).toFixed(2);
                                    calculate_net_qty("itemprice["+prc_details[2]+"]");
                                }
                            }
                        }
                        else { }
                    }
                }
			}
            function chicken_update_files(){
                var transaction_type = document.getElementById("transaction_type").value;
                var link = document.getElementById("download_file");
                if(transaction_type == "SalesWithFreightonJals"){
                    link.setAttribute("href", "ChickenModule-SalesImport2.xlsx");
                    document.getElementById("lbl_headname").innerHTML = "Upload Sales-Excel";
                }
                else{
                    link.setAttribute("href", "ChickenModule-PurchaseImport.xlsx");
                    document.getElementById("lbl_headname").innerHTML = "Upload Purchase-Excel";
                }
                
            }
            function calculate_net_qty(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var totalweight = document.getElementById("totalweight["+d+"]").value; if(totalweight == ""){ totalweight = 0; }
                var emptyweight = document.getElementById("emptyweight["+d+"]").value; if(emptyweight == ""){ emptyweight = 0; }
                var netweight = parseFloat(totalweight) - parseFloat(emptyweight);
                document.getElementById("netweight["+d+"]").value = parseFloat(netweight).toFixed(2);

                
                var jals = document.getElementById("jals["+d+"]").value; if(jals == ""){ jals = 0; }
                var freight_price_perjal = document.getElementById("freight_price_perjal["+d+"]").value; if(freight_price_perjal == ""){ freight_price_perjal = 0; }

                var freight_amount_jal = parseFloat(jals) * parseFloat(freight_price_perjal);
                document.getElementById("freight_amount_jal["+d+"]").value = parseFloat(freight_amount_jal).toFixed(2);
            }
            <?php if(isset($_POST['submit']) == true){ $aflag = 1; } else{ $aflag = 0; } ?> var aflag = '<?php echo $aflag; ?>'; if(aflag == 1){ fetch_customer_prices(); }
            function validatebirds(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
            function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#save_import_submit').click(); }); } } else{ } });
        </script>
        <!-- Select2 --><script src="../bower_components/select2/dist/js/select2.full.min.js"></script>
        <script>
            $(function () {
                $('.select2').select2();
                $('.datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true});
            })
            function selectionvalue(){ $('.select').select2() }
        </script>
        <link rel="stylesheet" href="../css/datepickers/datepickers.css">
        <script src="../js/datepicker/datepickerjquery.js"></script>
        <script src="../js/datepicker/datepickerjqueryui.js"></script>
        <!-- Select2 --><script src="../bower_components/select2/dist/js/select2.full.min.js"></script>
        <script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php //include "header_foot.php"; ?>
<?php
    }
    else{
    ?>
    <script>
        var x =confirm("You don't have access to this file, kindly contact admin for support");
        if(x == true){
            window.opener = self; window.close();
        }
        else{
            window.opener = self; window.close();
        }
    </script>
    <?php
    }
?>
