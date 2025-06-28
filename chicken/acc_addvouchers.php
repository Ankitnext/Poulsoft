<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;

	$sql='SHOW COLUMNS FROM `master_itemfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $j = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$j] = $row['Field']; $j++; }
	if(in_array("salary_voucher_wapp", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `salary_voucher_wapp` INT(100) NOT NULL DEFAULT '0'"; mysqli_query($conn,$sql); }
	  
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1' AND `salary_voucher_wapp` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ 
        $sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'WAPP-MSG' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
    }
	else{
		$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'SAVE-DATA' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
	}

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'acc_displayvouchers.php' AND `field_function` = 'E-Button'"; $query = mysqli_query($conn,$sql);
	$eb_flag = mysqli_num_rows($query);
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
				width: 12%;
				text-align: center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Vouchers</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Vouchers Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
			$emp_code = $_SESSION['userid'];
			$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access']; $slae_rate_edit_flag = $row['slae_rate_edit_flag']; }
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
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$wcode = $wdesc = $acode = $adesc = $icode = $idesc = array();
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' $warehouse_codes ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
					if($row['description'] == "Cash In Hand"){ $cash_code = $row['code']; }
				}
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$icode[$row['code']] = $row['code'];
					$idesc[$row['code']] = $row['description'];
				}
				$idisplay = ''; $ndisplay = 'style="display:none;';
				$sql = "SELECT * FROM `dataentry_daterange` WHERE `active` = '1'";
				$query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$from_date = date("d.m.Y",strtotime($row['fromdate']));
					$to_date = date("d.m.Y",strtotime($row['todate']));
				}
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="acc_updatevouchers.php" method="post" role="form" onsubmit="return checkval()">
									<div class="form-group col-md-2">&ensp;&ensp;
										<label>Voucher Type<b style="color:red;">&nbsp;*</b></label>
										<select name="pname" id="pname" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<option value="PV" selected>Payment Voucher</option>
											<option value="RV">Receipt Voucher</option>
											<option value="JV">Journal Voucher</option>
										</select>
									</div>
									<div class="form-group col-md-1">&ensp;&ensp;
										<label>Date</label>
										<input type="text"  name="adate" id="adate"class="form-control vou_datepickers" value="<?php echo $fdate; ?>" style="padding:0;padding-left:2px;" onchange="update_rowwise_dates();" readonly />
									</div>
									<div class="form-group col-md-18" style="visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									<table style="width:95%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Dc No.</label></th>
												<th><label>From CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><input type="text"  name="pdate[]" id="pdate[0]"class="form-control vou_datepickers" value="<?php echo $fdate; ?>" readonly></td>
												<td><input type="text" name="dcno[]" id="dcno[0]" class="form-control"></td>
												<td><select name="fcoa[]" id="fcoa[0]" class="form-control select2" > <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>" <?php if($cash_code == $fcode){ echo "selected"; } ?>><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="tcoa[]" id="tcoa[0]" class="form-control select2" > <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" value="0" onkeyup="granttotalamount()" onchange="getamountinwords()"></td>
												<td><select name="sector[]" id="sector[0]" class="form-control select2" >  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" readonly /></td>
											</tr>
									</table><br/><br/><br/>
									<div class="col-md-12" align="left">
										<div class="col-md-4">
											<label>Total Vouchers</label>
											<input type="text" name="tno" id="tno" class="form-control"style="width:auto;" >
										</div>
										<div class="col-md-4">
											<label>Voucher Amount</label>
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
			function getamountinwords() {
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("amount["+a+"]").value;
				var c = convertNumberToWords(b);
				document.getElementById("gtamtinwords["+a+"]").value = c;
			}
			function rowgen(){
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				var pdate = document.getElementById("pdate["+c+"]").value;
				c++;
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+c+']" class="form-control vou_datepickers" value="'+pdate+'" onmouseover="vou_displaycalendor(this.id)" readonly></td>';
					html+= '<td><input type="text" name="dcno[]" id="dcno['+c+']" class="form-control"></td>';
					html+= '<td><select name="fcoa[]" id="fcoa['+c+']" class="form-control select2"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>" <?php if($cash_code == $fcode){ echo "selected"; } ?>><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><select name="tcoa[]" id="tcoa['+c+']" class="form-control select2"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><input type="text" name="amount[]" id="amount['+c+']" class="form-control" value="0" onkeyup="granttotalamount()" onchange="getamountinwords()"></td>';
					html+= '<td><select name="sector[]" id="sector['+c+']" class="form-control select2">  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><textarea name="remark[]" id="remark['+c+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
					html+= '<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords['+c+']" class="form-control" readonly /></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html); 
				var row = $('#row_cnt').val(); $('#row_cnt').val(parseInt(row) + parseInt(1)); var newtrlen = c; if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); } 
				document.getElementById("incr").value = c;
				$('.select2').select2(); granttotalamount();
			}
			$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
			$(document).on('click','.delete',function(){ var index = $('.delete').index(this); var newIndex = parseInt(index) + parseInt(1); $('#tab3 > tbody > tr:eq('+newIndex+')').remove(); var row = $('#row_cnt').val(); var trlen = $('#tab3 > tbody > tr').length; var minusIndex = parseInt(trlen) - parseInt(1); if(trlen > 1){ $('.add:eq('+minusIndex+')').removeClass('disabledbutton'); $('#row_cnt').val(trlen); }else{ $('.add:eq(0)').removeClass('disabledbutton'); $('#row_cnt').val(1); } var a = document.getElementById("incr").value; a--; document.getElementById("incr").value = a; if(a > 0){ document.getElementById("rmval["+a+"]").style.visibility = "visible"; } else { document.getElementById("rmval["+a+"]").style.visibility = "hidden"; } document.getElementById("addval["+a+"]").style.visibility = "visible"; granttotalamount(); });
			function redirection_page(){ window.location.href = "acc_displayvouchers.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				for (var j=0;j<=a;j++){
					var f = document.getElementById("pname").value;
					var b = document.getElementById("fcoa["+j+"]").value;
					var c = document.getElementById("tcoa["+j+"]").value;
					var d = document.getElementById("amount["+j+"]").value;
					var e = document.getElementById("sector["+j+"]").value;
					var k = false; var l = j; l++;
					if(f.match("select")){
						alert("Please select Voucher Type");
						k = false;
					}
					if(b.match("select")){
						alert("Please select From CoA in row : "+l);
						k = false;
					}
					else if(c.match("select")){
						alert("Please select To CoA in row : "+l);
						k = false;
					}
					else if(d == 0 || d == "" || d.lenght == 0){
						alert("Please Enter Amount in row : "+l);
						k = false;
					}
					else if(e.match("select") || e == "" || e.lenght == 0){
						alert("Please select Sector in row : "+l);
						k = false;
					}
					else {
						k = true;
					}
				}
				if(k === true){
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
			function update_rowwise_dates(){
				var incr = document.getElementById("incr").value;
				var adate = document.getElementById("adate").value;
				for(var d = 0;d <= incr;d++){
					document.getElementById("pdate["+d+"]").value = adate;
				}
			}

			document.addEventListener("keydown", function(e) {
				var ebtn = "<?php echo $eb_flag; ?>";
				if(ebtn > 0){
					if (e.key === "Enter") {
						const ebtnEl = document.getElementById("ebtncount");
						const count = ebtnEl && ebtnEl.value.trim() !== "" ? parseInt(ebtnEl.value) : 0;

						if (count > 0) {
							e.preventDefault();  // Proper use of the event object
						} else {
							const active = document.activeElement;
							if (active && /^remark\[\d+\]$/.test(active.id)) {
								e.preventDefault();  // Prevents unintended form submission
								rowgen();  // Generate new row
							}
						}
					}
			    } else {}
			});
  			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submittrans').click(); }); } } else{ } });
		</script>
		<script src="main_numbertoamount.js"></script>
		<script src="handle_ebtn_as_tbtn.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>