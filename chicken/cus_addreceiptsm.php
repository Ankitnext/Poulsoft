<?php
	session_start(); include "newConfig.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "header_head.php";
	$emp_code = $_SESSION['userid'];
	$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access']; }
	if($loc_access == "all" || $loc_access == "" || $loc_access == NULL){
		$warehouse_codes = "";
	}
	else{
		$whs_code = "";
		$crp_codes = explode(",",$loc_access);
		foreach($crp_codes as $whs){
			if($whs_code == ""){
				$whs_code = $whs;
			}
			else{
				$whs_code = $whs_code."','".$whs;
			}
		}
		if($whs_code != ""){
			$warehouse_codes = " AND `code` IN ('$whs_code')";
		}
		else{
			$warehouse_codes = "";
		}
	}
	if($cgroup_access == "all" || $cgroup_access == "" || $cgroup_access == NULL){
		$cgroup_codes = "";
	}
	else{
		$crp_code = "";
		$crp_codes = explode(",",$cgroup_access);
		foreach($crp_codes as $cgrps){
			if($crp_code == ""){
				$crp_code = $cgrps;
			}
			else{
				$crp_code = $crp_code."','".$cgrps;
			}
		}
		if($crp_code != ""){
			$cgroup_codes = " AND `groupcode` IN ('$crp_code')";
		}
		else{
			$cgroup_codes = "";
		}
	}
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1' AND (`receipt_sms` = '1' || `receipt_wapp` = '1')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ 
        $sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'WAPP-MSG' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
    }
	else{
		$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'SAVE-DATA' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
	}

	$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Receipt Transaction' AND `field_function` LIKE 'Discount: Pass Credit Note' AND `flag` = '1'"; $query = mysqli_query($conn,$sql);
	$dccni_flag = mysqli_num_rows($query); if($dccni_flag > 0){ } else { $dccni_flag = 0; }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Receipt Transaction' AND `field_function` LIKE 'Display Amount in words'";
	$query = mysqli_query($conn,$sql); $cnt = mysqli_num_rows($query);
	if((int)$cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $daiw_flag = $row['flag']; } } else{ $sql = "INSERT INTO `extra_access` (`id`, `field_name`, `field_function`, `field_value`, `user_access`, `flag`) VALUES (NULL, 'Receipt Transaction', 'Display Amount in words', NULL, 'all', '1');"; mysqli_query($conn,$sql); $daiw_flag = 1; }
	if($daiw_flag == ""){ $daiw_flag = 1; }
?>
<html>
	<head>
		<!--<link rel="stylesheet" type="text/css" href="loading_screen.css">-->
		<style>
			.select2-container .select2-selection--single{ box-sizing:border-box; cursor:pointer; display:block; height:23px; user-select:none; -webkit-user-select:none; }
			.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
			.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
			.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
			.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
			.select2-container--default .select2-selection--single .select2-selection__arrow{height:23px;position:absolute;top:1px;right:1px;width:20px}
			.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
			.form-control { width: 100%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
			#tab3 tbody td {
				padding: 0px 5px;
			}
			th {
				width: 10%;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Receipts</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Receipt Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				/*Check for Table Availability*/
				$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
				$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
				if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
				
				/*Check for Sector Display Flag*/
				$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Customer Receipt' AND `field_function` LIKE 'Sector Selection Display' AND `user_access` LIKE 'all'";
				$query = mysqli_query($conn,$sql); $fcount = mysqli_num_rows($query); $secs_dflag = 1;
				if($fcount > 0){ while($row = mysqli_fetch_assoc($query)){ $secs_dflag = $row['flag']; } }
				else{ $sql = "INSERT INTO `extra_access` (`id`, `field_name`, `field_function`, `field_value`, `user_access`, `flag`) VALUES (NULL, 'Customer Receipt', 'Sector Selection Display', NULL, 'all', '1');"; mysqli_query($conn,$sql); }
				if($secs_dflag == ""){ $secs_dflag = 1; }

				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$cus_code = $cus_name = $wcode = $wdesc = $acode = $adesc = array();

				$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'".$user_cusgrp_filter." ORDER BY `description` ASC";
				$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
				while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }
			
				$grp_list = implode("','",$grp_code);
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cgroup_codes." AND `groupcode` IN ('$grp_list') AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$cus_code[$row['code']] = $row['code'];
					$cus_name[$row['code']] = $row['name'];
					$cus_group[$row['code']] = $row['groupcode'];
				}
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_codes." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
				}
				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="cus_updatereceiptsm.php" method="post" role="form" onsubmit="return checkval()">
									<div class="row">
										<div class="form-group col-md-1">
											<label>Date<b style="color:red;">&nbsp;*</b></label>
											<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo $fdate; ?>" id="rct_datepickers" readonly>
										</div>
										<div class="form-group col-md-2">
											<label>Mode<b style="color:red;">&nbsp;*</b></label>
											<select name="mode" id="mode" class="form-control select2" onchange="updatecode()"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>" <?php if($adesc[$fcode] == "cash" || $adesc[$fcode] == "Cash"){ echo "selected"; } ?>><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select>
										</div>
										<div class="form-group col-md-2">
											<label>Cash/Bank Code<b style="color:red;">&nbsp;*</b></label>
											<select name="code" id="code" class="form-control select2" > <option value="select">select</option></select>
										</div>
                                        <div class="form-group col-md-2">
                                            <label for="groups">Group</label>
                                            <select name="groups" id="groups" class="form-control select2" onchange="filter_group_customers(this.id);">
                                                <option value="all" <?php if($groups == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($grp_code as $gcode){ ?><option value="<?php echo $gcode; ?>" <?php if($groups == $gcode){ echo "selected"; } ?>><?php echo $grp_name[$gcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
										<div class="form-group col-md-1" style="visibility:hidden;">
											<label>incr<b style="color:red;">&nbsp;*</b></label>
											<input type="text" class="form-control" name="incr" id="incr" value="0">
										</div>
										<div class="form-group col-md-1" style="visibility:hidden;">
											<label>incrs<b style="color:red;">&nbsp;*</b></label>
											<input type="text" class="form-control" name="incrs" id="incrs" value="0">
										</div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
									</div>
									<table style="width:125%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<?php if((int)$daiw_flag == 1){ echo '<th><label>Amount in Words<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<?php if((int)$dccni_flag == 1){ ?><th><label>Discount</label></th><?php } ?>
												<th style="width:80px;"><label>Doc No</label></th>
												<?php if((int)$secs_dflag == 1){ ?><th><label>Sector<b style="color:red;">&nbsp;*</b></label></th><?php } ?>
												<th><label>Remarks</label></th>
												<th>Action</th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><select name="pname[]" id="pname[0]" class="form-control select2" style="width:200px;"> <option value="select">select</option> <?php foreach($cus_code as $fcode){ ?> <option value="<?php echo $fcode; ?>"><?php echo $cus_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" value="" onkeyup="validatenum(this.id);granttotalamount();getamountinwords(this.id);"></td>
												<?php if((int)$daiw_flag == 1){ ?><td style="visibility:visible;"><textarea name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" style="width:180px;" readonly ></textarea></td> <?php } ?>
												<?php if((int)$dccni_flag == 1){ ?><td><input type="text" name="discount_amt[]" id="discount_amt[0]" class="form-control" value="" onkeyup="validatenum(this.id);granttotalamount();getamountinwords(this.id);"></td><?php } ?>
												<td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:80px;" onkeyup="validate_docno(this.id);"></td>
												<?php if((int)$secs_dflag == 1){ ?><td><select name="sector[]" id="sector[0]" class="form-control select2"  style="width:170px;">  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td><?php } ?>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
											</tr>
									</table><br/><br/><br/>
									<div class="col-md-12" align="left">
										<div class="col-md-2">
											<label>Total Receipts</label>
											<input type="text" name="tno" id="tno" class="form-control"style="width:auto;" >
										</div>
										<div class="col-md-2">
											<label>Receipt Amount</label>
											<input type="text" name="gtamt" id="gtamt" class="form-control" style="width:auto;" readonly>
										</div>
									</div><br/><br/><br/>
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
											<i class="fa fa-save"></i> Save
										</button>&ensp;&ensp;&ensp;&ensp;
										<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
											<i class="fa fa-trash"></i> Cancel
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--<div class="ring"><?php //echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>-->
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			function getamountinwords(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var amount = parseFloat(document.getElementById("amount["+d+"]").value);
				daiw_flag = '<?php echo $daiw_flag; ?>';
				if(parseFloat(daiw_flag) == 1 && amount != ""){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "number_convert_to_amt.php?number="+amount;
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var cbal = this.responseText;
							if(cbal == "" || cbal == " rupees only") {
								document.getElementById("gtamtinwords["+d+"]").value = "";
							}
							else {
								document.getElementById("gtamtinwords["+d+"]").value = cbal;
							}
						}
					}
				}
				else {
					document.getElementById("gtamtinwords["+d+"]").value = "";
				}
			}
			function rowgen(){
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				c++;
				var daiw_flag = '<?php echo $daiw_flag; ?>';
				var dccni_flag = '<?php echo $dccni_flag; ?>';
				var secs_dflag = '<?php echo $secs_dflag; ?>';
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><select name="pname[]" id="pname['+c+']" class="form-control select2" style="width:200px;"> <option value="select">select</option> <?php foreach($cus_code as $fcode){ ?> <option value="<?php echo $fcode; ?>"><?php echo $cus_name[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><input type="text" name="amount[]" id="amount['+c+']" class="form-control" value="" onkeyup="validatenum(this.id);granttotalamount();getamountinwords(this.id);"></td>';
					if(parseFloat(daiw_flag) == 1){
						html+= '<td style="visibility:visible;"><textarea name="gtamtinwords[]" id="gtamtinwords['+c+']" class="form-control" style="width:180px;" readonly ></textarea></td>';
					}
					if(parseFloat(dccni_flag) == 1){
						html+= '<td><input type="text" name="discount_amt[]" id="discount_amt['+c+']" class="form-control" value="" onkeyup="validatenum(this.id);"></td>';
					}
					html+= '<td><input type="text" name="dcno[]" id="dcno['+c+']" class="form-control" style="width:80px;" onkeyup="validate_docno(this.id);"></td>';
					if(parseInt(secs_dflag) == 1){ html+= '<td><select name="sector[]" id="sector['+c+']" class="form-control select2" style="width:170px;">  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>'; }
					html+= '<td><textarea name="remark[]" id="remark['+c+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html); var row = $('#row_cnt').val(); $('#row_cnt').val(parseInt(row) + parseInt(1)); var newtrlen = c; if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); } document.getElementById("incr").value = c;
				$('.select2').select2(); granttotalamount();
				var x = "fltr_cus["+c+"]";
				filter_group_customers(x);
			}
			$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
			$(document).on('click','.delete',function(){ var index = $('.delete').index(this); var newIndex = parseInt(index) + parseInt(1); $('#tab3 > tbody > tr:eq('+newIndex+')').remove(); var row = $('#row_cnt').val(); var trlen = $('#tab3 > tbody > tr').length; var minusIndex = parseInt(trlen) - parseInt(1); if(trlen > 1){ $('.add:eq('+minusIndex+')').removeClass('disabledbutton'); $('#row_cnt').val(trlen); }else{ $('.add:eq(0)').removeClass('disabledbutton'); $('#row_cnt').val(1); } var a = document.getElementById("incr").value; a--; document.getElementById("incr").value = a; if(a > 0){ document.getElementById("rmval["+a+"]").style.visibility = "visible"; } else { document.getElementById("rmval["+a+"]").style.visibility = "hidden"; } document.getElementById("addval["+a+"]").style.visibility = "visible"; granttotalamount(); });
			function granttotalamount(){
				var s = document.getElementById("incr").value;
				var k = l = 0;
				for(var j=0;j<=s;j++){
					k = document.getElementById("amount["+j+"]").value;
					l = parseFloat(l) + parseFloat(k);
				}
				s++;
				document.getElementById("tno").value = s;
				document.getElementById("gtamt").value = l;
			}
			function redirection_page(){ window.location.href = "cus_displayreceiptsm.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				var b = document.getElementById("mode").value;
				var c = document.getElementById("code").value;
				var secs_dflag = '<?php echo $secs_dflag; ?>';
				var d = true; var sector = "";
				if(b.match("select")){
					alert("Please select Mode : ");
					d = false;
				}
				else if(c.match("select")){
					alert("Please select Cash/Bank Code");
					d = false;
				}
				for (var j=0;j<=a;j++){
					if(d == true){
						var e = document.getElementById("pname["+j+"]").value;
						var f = document.getElementById("amount["+j+"]").value;
						if(parseInt(secs_dflag) == 1){ sector = document.getElementById("sector["+j+"]").value; } else{ sector = ""; }
						var l = j; l++;
						if(e.match("select")){
							alert("Please select Customer name in row : "+l);
							d = false;
						}
						else if(f == 0 || f == "" || f.lenght == 0 || f.lenght == 0.00){
							alert("Please enter amount in row : "+l);
							d = false;
						}
						else if(parseInt(secs_dflag) == 1 && sector == "select"){
							alert("Please select sector in row : "+l);
							d = false;
						}
						else {
							d = true;
						}
					}
				}
				if(d === true){
					/*document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php //echo $loading_stitle; ?>';*/
					return true;
				}
				else {
					/*document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
					document.getElementById("disp_val").innerHTML = "";*/

					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
            function filter_group_customers(a){
                if(a == "groups"){
					var incr = document.getElementById("incr").value;
					var groups = document.getElementById('groups').value;
					if(groups == "all"){
						for(var d = 0;d <= incr;d++){
							removeAllOptions(document.getElementById("pname["+d+"]"));
							myselect = document.getElementById("pname["+d+"]");
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("-select-");
							theOption1.value = "select";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);

							<?php
							foreach($cus_code as $vcode){
							?>
								theOption1=document.createElement("OPTION");
								theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
								theOption1.value = "<?php echo $vcode; ?>";
								theOption1.appendChild(theText1);
								myselect.appendChild(theOption1);
							<?php
							}
							?>
						}
					}
					else{
						for(var d = 0;d <= incr;d++){
							removeAllOptions(document.getElementById("pname["+d+"]"));
							myselect = document.getElementById("pname["+d+"]");
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("-select-");
							theOption1.value = "select";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);

							<?php
							foreach($cus_code as $vcode){
								$gcode = $cus_group[$vcode];
								echo "if(groups == '$gcode'){";
								?>
								theOption1=document.createElement("OPTION");
								theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
								theOption1.value = "<?php echo $vcode; ?>";
								theOption1.appendChild(theText1);
								myselect.appendChild(theOption1);
								<?php
								echo "}";
							}
							?>
						}
					}
				}
				else{
					var b = a.split("["); var c = b[1].split("]"); var d = c[0];
					var groups = document.getElementById('groups').value;

					removeAllOptions(document.getElementById("pname["+d+"]"));
					myselect = document.getElementById("pname["+d+"]");
					theOption1=document.createElement("OPTION");
					theText1=document.createTextNode("-select-");
					theOption1.value = "select";
					theOption1.appendChild(theText1);
					myselect.appendChild(theOption1);

					if(groups == "all"){
						<?php
						foreach($cus_code as $vcode){
						?>
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
							theOption1.value = "<?php echo $vcode; ?>";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);
						<?php
						}
						?>
					}
					else{
						<?php
						foreach($cus_code as $vcode){
							$gcode = $cus_group[$vcode];
							echo "if(groups == '$gcode'){";
							?>
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
							theOption1.value = "<?php echo $vcode; ?>";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);
							<?php
							echo "}";
						}
						?>
					}
				}
            }
			function updatecode(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("mode").value;
				removeAllOptions(document.getElementById("code"));
				
				if(b.match("MOD-001")){
					myselect = document.getElementById("code");
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Cash%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else {
				myselect = document.getElementById("code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Bank%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
				<?php } ?>
				}
			}
			updatecode();
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submittrans').click(); }); } } else{ } });
		</script>
		<!--<script src="main_numbertoamount.js"></script>-->
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>