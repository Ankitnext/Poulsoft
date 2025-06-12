<?php
	session_start(); include "newConfig.php";
	include "header_head.php";

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'CrDr-Note Transaction' AND `field_function` LIKE 'Display: Reason selection' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $rsncrdr_flag = mysqli_num_rows($query);

	if((int)$rsncrdr_flag == 1){
		$sql = "SELECT * FROM `crdr_note_reasons` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
		$query = mysqli_query($conn,$sql); $reason_code = $reason_name = array();
		while($row = mysqli_fetch_assoc($query)){ $reason_code[$row['code']] = $row['code']; $reason_name[$row['code']] = $row['description']; }
	}

	//check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";
?>
<html>
	<head>
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
				text-align: center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Edit Credit / Debit</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Credit / Debit Display</li>
				<li class="active">Edit</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$invoiceid = $_GET['id'];
				$ctypes = $invoiceid[0];
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%$ctypes%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$fpcode[$row['code']] = $row['code'];
					$fpname[$row['code']] = $row['name'];
				} $fpsize = sizeof($fpcode);
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
				}
				$visibles = 'style="visibility:visible;"'; $nvisibles = 'style="visibility:hidden;"';
				$sql = "SELECT * FROM `dataentry_daterange` WHERE `active` = '1'";
				$query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$from_date = date("d.m.Y",strtotime($row['fromdate']));
					$to_date = date("d.m.Y",strtotime($row['todate']));
				}
			?>
			
				<div class="box-body" style="min-height:400px;"><br/><br/><br/>
					<div class="row">
						<div class="col-md-18">
							<div class="col-md-18" align="center">
								<form action="main_updatecreditdebitnote.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<table style="width:100%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Trnum<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>S/C Type<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>C/D Type<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Supplier/Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Doc No</label></th>
												<th><label>Account<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<?php if((int)$rsncrdr_flag == 1){ echo '<th><label>Reason</label></th>'; } ?>
												
												<th><label>Remarks</label></th>
                                                <th style="visibility:hidden"><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
											</tr>
										</thead>
										<?php
											$cedit = 0;
											$sqledit = "SELECT * FROM `main_crdrnote` WHERE `trnum` LIKE '$invoiceid' AND `flag` = '0'"; $queryedit = mysqli_query($conn,$sqledit);
											while($rowedit = mysqli_fetch_assoc($queryedit)){
												if($ctypes == "S"){ $csoption = '<option selected value="S">Supplier</option>'; }
												else if($ctypes == "C"){ $csoption = '<option selected value="C">Customer</option>'; }
												if($rowedit['mode'] == "SCN" || $rowedit['mode'] == "CCN"){ $coption = '<option selected value="CN">Credit Note</option>'; }
												else if($rowedit['mode'] == "SDN" || $rowedit['mode'] == "CDN"){ $coption = '<option selected value="DN">Debit Note</option>'; }
												$rsn_code = $rowedit['reason_code'];
										?>
											<tr style="line-height:30px;">
												<td><input type="text" name="trnum" id="trnum" class="form-control" style="width:100%" value="<?php echo $rowedit['trnum']; ?>" readonly /></td>
												<td><select name="vtype" id="vtype" class="form-control select2" style="width:100%"><option value="select">select</option><?php echo $csoption; ?></select></td>
												<td><select name="cdtype" id="cdtype" class="form-control select2" style="width:100%"> <option value="select">select</option> <?php echo $coption; ?> </select></td>
												<td><select name="pname" id="pname" class="form-control select2" style="width:100%"> <option value="select">select</option> <?php foreach($fpcode as $fpcodes){ ?> <option <?php if($rowedit['ccode'] == $fpcode[$fpcodes]){ echo "selected"; } ?> value="<?php echo $fpcode[$fpcodes]; ?>"><?php echo $fpname[$fpcodes]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="pdate" id="pdate"class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($rowedit['date'])); ?>" readonly></td>
												<td><input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $rowedit['docno']; ?>"></td>
												<td><select name="mode" id="mode" class="form-control select2" style="width:100%"><option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option <?php if($rowedit['coa'] == $acode[$fcode]){ echo "selected"; } ?> value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount" id="amount" class="form-control" value="<?php echo $rowedit['amount']; ?>" onchange="validateamount(this.id);getamountinwords()"></td>
												<?php if((int)$rsncrdr_flag == 1){ ?><td><select name="reason_code" id="reason_code" class="form-control select2" style="width:100%"> <?php foreach($reason_code as $rcode){ ?> <option value="<?php echo $rcode; ?>" <?php if($rsn_code == $rcode){ echo "selected"; } ?>><?php echo $reason_name[$rcode]; ?></option> <?php } ?> </select></td><?php } ?>
												
												<td><textarea name="remark" id="remark" class="form-control" style="height: 23px;"><?php echo $rowedit['remarks']; ?></textarea></td>
                                                <td style="visibility:hidden"><select name="sector" id="sector" class="form-control select2" style="width:100%"> <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option <?php if($rowedit['warehouse'] == $wcode[$fcode]){ echo "selected"; } ?> value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td style="visibility:hidden;"><input type="text" name="gtamtinwords" id="gtamtinwords" class="form-control" value="<?php echo $row['amtinwords']; ?>" readonly /></td>
											</tr>
										<?php 
											}
										?>
									</table><br/><br/><br/>
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-flat btn-social btn-linkedin">
											<i class="fa fa-save"></i> Update
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
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			//Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
			function getamountinwords() {
				var a = document.getElementById("amount").value;
				var b = convertNumberToWords(a);
				document.getElementById("gtamtinwords").value = b;
			}
			function redirection_page(){ window.location.href = "main_displaycreditdebitnote.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; getamountinwords(); }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var b = document.getElementById("vtype").value;
				var c = document.getElementById("cdtype").value;
				var d = document.getElementById("pname").value;
				//var e = document.getElementById("dcno").value;
				var f = document.getElementById("mode").value;
				var g = document.getElementById("amount").value;
				var h = document.getElementById("sector").value;
				var k = false;
				if(b.match("select")){
					alert("Please select supplier / Customer type");
					k = false;
				}
				else if(c.match("select")){
					alert("Please select Credit / Debit type");
					k = false;
				}
				else if(d.match("select")){
					alert("Please select supplier / Customer name");
					k = false;
				}
				/*else if(e == 0 || e == "" || e.lenght == 0){
					alert("Please enter Document No.");
					k = false;
				}*/
				else if(f.match("select")){
					alert("Please select CoA");
					k = false;
				}
				else if(g == 0 || g.lenght == 0 || g == ""){
					alert("Please enter Amount");
					k = false;
				}
				else if(h.match("select")){
					alert("Please select sector");
					k = false;
				}
				else {
					k = true;
				}
				
				if(k === true){
					//alert(k);
					return true;
				}
				else if(k == false){
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
				else {
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			
			document.getElementById("form_id").onkeypress = function(e) {
				var key = e.charCode || e.keyCode || 0;     
				if (key == 13) {
				//alert("No Enter!");
				e.preventDefault();
				}
			} 
		</script>
		<script src="main_numbertoamount.js"></script>
		<script src="handle_ebtn_as_tbtn.js"></script>
		  <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>